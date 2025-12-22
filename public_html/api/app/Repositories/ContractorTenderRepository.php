<?php
namespace App\Repositories;

use App\Http\Resources\ContractorTenderResource;
use App\Http\Resources\TenderResource;
use App\Models\Contractor;
use App\Models\ContractorTender;
use App\Models\ContractorTenderMaterialWork;
use App\Models\ContractorTenderRevision;
use App\Models\ContractorTenderWorkRevision;
use App\Models\MaterialWorkType;
use App\Models\Organization;
use App\Models\Tender;
use App\Models\TenderMaterialWork;
use App\Models\User;
use App\Repositories\Interfaces\ContractorTenderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractorTenderRepository implements ContractorTenderRepositoryInterface
{
    public function all($request)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        if ($request->item) {
            return ContractorTenderResource::collection(
                ContractorTender::when(request('search'), function ($query) {
                    //$query->where('contractor_name', '] LIKE % ' . request('item'). '%');
                })->where('tender_id', $request->item)->orderBy($field, $order)->paginate($perPage)
            );
        }
        return ContractorTenderResource::collection(
            ContractorTender::when(request('search'), function ($query) {
                //$query->where('contractor_name', '] LIKE % ' . request('item'). '%');
            })->orderBy($field, $order)->paginate($perPage)
        );
    }

    public function create($data)
    {
        $contractorTender = '';
        if (!empty($data)) {
            $contractorTender = $this->_checkIfContractorHasThisTender(Auth::user()->id, $data['tender_id'], $data);
            $revision = $this->_getCurrentRevision($contractorTender);
            $revision++;

            $temp = [];
            foreach (json_decode($data->items) as $tenderWork) {
                foreach($tenderWork as $work) {
                    if ($work->entered_rate < 1) {
                        throw new \Exception("Rate cannot be less than 1 for any work item.");
                    }
                    $contractorTender->contractor_tender_material_works()->create([
                        'tender_material_work_id' => $work->id,
                        'rate' => $work->entered_rate,
                        'quantity' => $work->quantity,
                        'total' => $work->entered_rate * $work->quantity,
                        'revision' => $revision,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    $temp['category_id'] = $work->category_id;
                    $temp[$work->category_id][] = $work->entered_rate * $work->quantity;
                }

                $contractorTender->contractor_tender_work_revisions()->create([
                    'material_work_type_id' => $temp['category_id'],
                    'total_amount' => array_sum($temp[$temp['category_id']]),
                    'revision' => $revision,
                ]);
            }
            $contractorTender->contractor_tender_revisions()->create([
                'revision' => $revision
            ]);

            $contractorTender = $this->_calculatePercentageDiffRevision($contractorTender, $revision);
        }
        activity('bid')
            ->performedOn($contractorTender)
            ->causedBy(Auth::user())
            ->withProperties(['tender' => $contractorTender->tender->tender_reference_number])
            ->log($contractorTender->contractor->name.' Submitted Bid for '.$contractorTender->tender->project->name);

        return $contractorTender;
    }

    private function _calculatePercentageDiffRevision($contractorTender, $revision)
    {
        // Material work rates total
        config()->set('database.connections.mysql.strict', false);
        \DB::reconnect(); //important as the existing connection if any would be in strict mode
        $materialWorks = DB::table('tender_material_works')
                                ->select('tender_id', 'material_work_type_id', DB::raw('ROUND(SUM(`rate`), 2) AS rate_total'))
                                ->whereRaw('tender_id = '.$contractorTender->tender_id)
                                ->groupBy('material_work_type_id')
                                ->orderBy('material_work_type_id', 'asc')
                                ->get();
        //now changing back the strict ON
        config()->set('database.connections.mysql.strict', true);
        \DB::reconnect();


        $contractorRateAdd = $materialRateAdd = 0;
        foreach ($materialWorks as $materialWork) {
            // Contractor rate added.
            $contractorRateTotal = $contractorTender->contractor_tender_material_works()
                                //->where('tender_material_work_id', $materialWork->material_work_type_id)
                                ->where('revision', $revision)
                                ->sum('rate');

            // Now calculate percentage difference.
            // Formula to calculate percentage difference is as below:
            /**
             * (Contractor Rate total - Tender rate total) / ( ((Contractor Rate total + Tender rate total) / 2) ) * 100
             */

            if ((int)$contractorRateTotal > (int)$materialWork->rate_total * 2) {
                $percentageDifference = 100;
            } else if ((int)$contractorRateTotal > (int)$materialWork->rate_total) {
                $percentageDifference = (((int)$contractorRateTotal - (int)$materialWork->rate_total) / (((int)$contractorRateTotal + (int)$materialWork->rate_total) / 2)) * 100;
            } else {
                $percentageDifference = 0;
            }
            $contractorTender->contractor_tender_work_revisions()
                            ->where('material_work_type_id', $materialWork->material_work_type_id)
                            ->where('revision', $revision)
                            ->update(['percentage_difference' => round($percentageDifference, 2)]);

            $materialRateAdd += $materialWork->rate_total;
            $contractorRateAdd += $contractorRateTotal;
        }

        if ((int)$contractorRateAdd > (int)$materialRateAdd * 2) {
            $versionPercentageDifference = 100;
        } else if ((int)$contractorRateAdd > (int)$materialRateAdd) {
            $versionPercentageDifference = ((int)$contractorRateAdd - (int)$materialRateAdd) / (((int)$contractorRateAdd + (int)$materialRateAdd) / 2) * 100;
        } else {
            $versionPercentageDifference = 0;
        }

        $contractorTender->contractor_tender_revisions()->where('revision', $revision)->update(
            [
                'percentage_difference' => round($versionPercentageDifference, 2)
            ]);
        return $contractorTender;
    }


    private function _getCurrentRevision($contractorTender): mixed
    {
        $contractorRevisions = ContractorTenderRevision::where('contractor_tender_id',  $contractorTender->id)
                                                        ->latest()
                                                        ->first();
        return !($contractorRevisions) ? 0 : $contractorRevisions->revision;
    }

    private function _checkIfContractorHasThisTender($userId, $tenderId, $data)
    {
        $contractor = Contractor::where('user_id', $userId)->first();
        $contractorTender = ContractorTender::where('contractor_id', $contractor->id)->where('tender_id', $tenderId)->first();

        if (!$contractorTender) {
            $contractorTender = ContractorTender::create([
                'contractor_id' => $contractor->id,
                'tender_id' => $tenderId,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);
        }
        return $contractorTender;
    }

    public function show($data)
    {

    }

    public function update($data, $contractorTender)
    {

    }


    public function delete($data)
    {

    }

    public function tender_wise($request, $userId = null)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        //dd(request('search'));

        if ($userId) {
            $user = User::findOrFail($userId);
            $userRole = $user->roles->pluck('name', 'id')->toArray();
            if (in_array('Contractor', $userRole)) {
                $contractor = Contractor::where('user_id', $userId)->first();
                $contractorId = $contractor->id;
            }
            if (in_array('Organization', $userRole)) {
                $organization = Organization::where('user_id', $userId)->first();
                $organizationId = $organization->id;

                $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());

                $tenderIds = array_keys(TenderResource::collection(Tender::whereHas('contractor_tenders', function($query) {
                    $query->where('is_awarded', 0);
                })->whereIn('project_id', $projectIds)->get())->pluck('tender_reference_number', 'id')->toArray());


            }
        }
        config()->set('database.connections.mysql.strict', false);
        \DB::reconnect(); //important as the existing connection if any would be in strict mode

        $tenderMaterialWorkTypeid = $tenderTenderReferenceNumber = $tenderCategoryId = $tenderStartDate = '';

        if (request('search')) {
            $searchObj = json_decode(request('search'));
            if (isset($searchObj->tender_uid) && $searchObj->tender_uid) {
                $tenderTenderReferenceNumber = $searchObj->tender_uid;
            }
            if (isset($searchObj->work_type) && $searchObj->work_type) {
                $tenderMaterialWorkTypeid = $searchObj->work_type;
            }
            if (isset($searchObj->tender_category_id) && $searchObj->tender_category_id && $searchObj->tender_category_id != 'Select Tender Category') {
                $tenderCategoryId = $searchObj->tender_category_id;
            }
            if (isset($searchObj->start_date) && $searchObj->start_date) {
                $tenderStartDate = $searchObj->start_date;
            }
        }

        if (isset($contractorId)) {
            $contractorTenders =  ContractorTenderResource::collection(
                ContractorTender::when(request('search'), function ($query) {
                    $searchObj = json_decode(request('search'));

                    //$query->where('contractor_name', '] LIKE % ' . request('item'). '%');
                })
                ->whereHas('tender.material_work_type', function ($query) use($tenderMaterialWorkTypeid) {
                    $query->where('name', 'like', '%' . $tenderMaterialWorkTypeid . '%');
                })
                ->whereHas('tender', function ($query) use($tenderTenderReferenceNumber) {
                    $query->where('tender_reference_number', 'like', '%' . $tenderTenderReferenceNumber . '%');
                })
                ->whereHas('tender', function ($query) use($tenderCategoryId) {
                    $query->where('tender_category_id', 'like', '%' . $tenderCategoryId . '%');
                })
                ->whereHas('tender', function ($query) use($tenderStartDate) {
                    $query->where('created_at', 'like', '%' . $tenderStartDate . '%');
                })
                ->where('contractor_id', $contractorId)
                ->where('is_awarded', 0)
                ->groupBy('tender_id')
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        } else if (isset($tenderIds)) {
            $contractorTenders =  ContractorTenderResource::collection(
                ContractorTender::when(request('search'), function ($query) {
                    //$query->where('tender_uid', ' LIKE % ' . request('item'). '%');
                    $searchObj = json_decode(request('search'));
                })
                ->whereHas('tender.material_work_type', function ($query) use($tenderMaterialWorkTypeid) {
                    $query->where('name', 'like', '%' . $tenderMaterialWorkTypeid . '%');
                })
                ->whereHas('tender', function ($query) use($tenderTenderReferenceNumber) {
                    $query->where('tender_reference_number', 'like', '%' . $tenderTenderReferenceNumber . '%');
                })
                ->whereHas('tender', function ($query) use($tenderCategoryId) {
                    $query->where('tender_category_id', 'like', '%' . $tenderCategoryId . '%');
                })
                ->whereHas('tender', function ($query) use($tenderStartDate) {
                    $query->where('created_at', 'like', '%' . $tenderStartDate . '%');
                })
                ->whereIn('tender_id', $tenderIds)
                ->where('is_awarded', 0)
                ->groupBy('tender_id')
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        } else {
            $contractorTenders =  ContractorTenderResource::collection(
                ContractorTender::when(request('search'), function ($query) {
                    $searchObj = json_decode(request('search'));
                })
                ->whereHas('tender.material_work_type', function ($query) use($tenderMaterialWorkTypeid) {
                    $query->where('name', 'like', '%' . $tenderMaterialWorkTypeid . '%');
                })
                ->whereHas('tender', function ($query) use($tenderTenderReferenceNumber) {
                    $query->where('tender_reference_number', 'like', '%' . $tenderTenderReferenceNumber . '%');
                })
                ->whereHas('tender', function ($query) use($tenderCategoryId) {
                    $query->where('tender_category_id', 'like', '%' . $tenderCategoryId . '%');
                })
                ->whereHas('tender', function ($query) use($tenderStartDate) {
                    $query->where('created_at', 'like', '%' . $tenderStartDate . '%');
                })
                ->groupBy('tender_id')
                ->where('is_awarded', 0)
                ->orderBy($field, $order)
                ->paginate($perPage)
            );

        }

        //now changing back the strict ON
        config()->set('database.connections.mysql.strict', true);
        \DB::reconnect();
        return $contractorTenders;

    }

    public function get_overall_detail($tenderId)
    {
        if (!$tenderId) {
            return false;
        }
        $response = [];
        // Get Basic details of tender
        $tender = Tender::findOrFail($tenderId);

        $response['basic_info'] = [
            'builder_name' => $tender->project->organization->name,
            'project_name' => $tender->project->name,
            'area' => $tender->project->total_project_area,
            'tender_reference_number' => $tender->tender_reference_number,
            'building_name' => $tender->project->project_buildings ? implode(',', $tender->project->project_buildings->pluck('name', 'id')->toArray()): 'NA',
            'project_building_name' => $tender->project_buildings,
        ];

        // We need to get list of the works of selected tender
        $tenderMaterialWorks = TenderMaterialWork::where('tender_id', $tenderId)->get();
        if ($tenderMaterialWorks) {
            $tmp = [];
            $workArray = [];
            $i = 1;
            foreach ($tenderMaterialWorks as $tenderMaterialWork) {
                if (empty($workArray) || !in_array($tenderMaterialWork->material_work_type->name, $workArray)) {
                    array_push($workArray, $tenderMaterialWork->material_work_type->name);
                    $i = 1;
                }
                $tmp[$tenderMaterialWork->material_work_type_id. ':' .$tenderMaterialWork->material_work_type->name][$i] = [
                    'work' => $tenderMaterialWork->work,
                    'unit' => $tenderMaterialWork->unit->code,
                    'quantity' => $tenderMaterialWork->quantity,
                ];
                $i++;
            }
            $response['master_material_works'] = $tmp;
        }
        // Now get the list of all contractors
        $contractorTenders = ContractorTender::where('tender_id', $tenderId)->get();
        $tmpRevision = $tmpContractors = $tmpTotal = [];
        if (!empty($contractorTenders)) {

            foreach ($contractorTenders as $contractorTender) {
                if ($contractorTender) {
                    // Now for each tenders we need to find how many revisions are there

                    $workRevisions = $contractorTender->contractor_tender_work_revisions;
                    foreach ($workRevisions as $workRevision) {
                        // Get the list of all contractor works.
                        $tmpContractors = [];
                        $workArray = [];
                        $tmpForTotal = [];
                        $contractorTenderWorks = $contractorTender->contractor_tender_material_works->where('revision', $workRevision->revision);
                        if (!empty($contractorTenderWorks)) {
                            $j = 1;
                            foreach ($contractorTenderWorks as $contractorTenderWork) {


                                $materialWorkType = TenderMaterialWork::findOrFail($contractorTenderWork->tender_material_work_id);
                                $typeId = $materialWorkType->material_work_type_id;
                                $typeName = $materialWorkType->material_work_type->name;

                                if (empty($workArray) || !in_array($materialWorkType->material_work_type->name, $workArray)) {
                                    array_push($workArray, $materialWorkType->material_work_type->name);
                                    array_push($tmpForTotal, (int)$contractorTenderWork->rate * (int)$materialWorkType->quantity);
                                    $j = 1;
                                }
                                $tmpContractors[$typeId . ':' . $typeName][$j] = [
                                    'work' => $materialWorkType->work,
                                    'rate' => $contractorTenderWork->rate,
                                    'total_amount' => (int)$contractorTenderWork->rate * (int)$materialWorkType->quantity,
                                ];
                                $j++;
                            }
                        }

                        $percentage = ContractorTenderRevision::where('contractor_tender_id', $contractorTender->id)
                                                    ->where('revision', $workRevision->revision)->first();
                        $tmpRevision[$contractorTender->contractor->name]['R'.$workRevision->revision - 1 . ':'.$percentage->percentage_difference][$workRevision->material_work_type_id . ':'.$workRevision->material_work_type->name] = [
                            'total_amount' => $workRevision->total_amount,
                            'percentage_difference' => $workRevision->percentage_difference,
                            'works' => $tmpContractors,
                        ];
                        $tmpTotal[$workRevision->revision - 1] = $workRevision->total_amount;
                    }
                }
            }


            $response['contractors'] = [
                'revisions' => $tmpRevision,
                'revisionTotals' => $tmpTotal,
            ];
        }
        return $response;
    }

    /**
     * Get details of a specific contractor's tender
     *
     * @param int $id The ID of the contractor tender
     * @return array|bool Returns the contractor tender details or false if ID is invalid
     */
    public function get_contractor_wise_detail($id)
    {
        // If ID is not provided, return false
        if (!$id) {
            return false;
        }

        // Find the contractor tender by ID
        $contractorTenders = ContractorTender::findOrFail($id);

        // Get the tender ID from the contractor tender
        $tenderId = $contractorTenders->tender_id;

        $response = [];
        // Get Basic details of tender
        $tender = TenderResource::make(Tender::findOrFail($tenderId));

        // Set the basic information of the tender
        $response['basic_info'] = [
            'tender_id' => $tenderId,
            'builder_name' => $tender->project->organization->name,
            'project_name' => $tender->project->name,
            'project_building_name' => $tender->project_building_name,
            'area' => $tender->project->total_project_area,
            'tender_reference_number' => $tender->tender_reference_number,
            'tender' => $tender,
        ];

        // We need to get list of the works of selected tender
        $tenderMaterialWorks = TenderMaterialWork::where('tender_id', $tenderId)->get();
        if ($tenderMaterialWorks) {
            $tmp = [];
            $workArray = [];
            $i = 1;
            foreach ($tenderMaterialWorks as $tenderMaterialWork) {
                if (empty($workArray) || !in_array($tenderMaterialWork->material_work_type->name, $workArray)) {
                    array_push($workArray, $tenderMaterialWork->material_work_type->name);
                    $i = 1;
                }
                $tmp[$tenderMaterialWork->material_work_type_id. ':' .$tenderMaterialWork->material_work_type->name][$i] = [
                    'work' => $tenderMaterialWork->work,
                    'unit' => $tenderMaterialWork->unit->code,
                    'quantity' => $tenderMaterialWork->quantity,
                ];
                $i++;
            }
            $response['master_material_works'] = $tmp;
        }
        // Now get the list of all contractors
        //$contractorTenders = ContractorTender::where('tender_id', $tenderId)->get();
        $tmpRevision = $tmpContractors = [];
        if (!empty($contractorTenders)) {

            //foreach ($contractorTenders as $contractorTender) {
                if ($contractorTenders) {
                    // Now for each tenders we need to find how many revisions are there

                    $workRevisions = $contractorTenders->contractor_tender_work_revisions;
                    foreach ($workRevisions as $workRevision) {
                        // Get the list of all contractor works.
                        $tmpContractors = [];
                        $workArray = [];
                        $contractorTenderWorks = $contractorTenders->contractor_tender_material_works->where('revision', $workRevision->revision);
                        if (!empty($contractorTenderWorks)) {
                            $j = 1;
                            foreach ($contractorTenderWorks as $contractorTenderWork) {

                                $materialWorkType = TenderMaterialWork::findOrFail($contractorTenderWork->tender_material_work_id);
                                $typeId = $materialWorkType->material_work_type_id;
                                $typeName = $materialWorkType->material_work_type->name;

                                if (empty($workArray) || !in_array($materialWorkType->material_work_type->name, $workArray)) {
                                    array_push($workArray, $materialWorkType->material_work_type->name);
                                    $j = 1;
                                }
                                $tmpContractors[$typeId . ':' . $typeName][$j] = [
                                    'work' => $materialWorkType->work,
                                    'rate' => $contractorTenderWork->rate,
                                    'total_amount' => (int)$contractorTenderWork->rate * (int)$materialWorkType->quantity,
                                ];


                                $j++;
                            }
                        }

                        $percentage = ContractorTenderRevision::where('contractor_tender_id', $contractorTenders->id)
                                                    ->where('revision', $workRevision->revision)->first();
                        $tmpRevision[$contractorTenders->contractor->name]['R'.$workRevision->revision - 1 . ':'.$percentage->percentage_difference][$workRevision->material_work_type_id . ':'.$workRevision->material_work_type->name] = [
                            'total_amount' => $workRevision->total_amount,
                            'percentage_difference' => $workRevision->percentage_difference,
                            'works' => $tmpContractors,
                        ];
                        $tmpTotal[$workRevision->revision - 1] = $workRevision->total_amount;
                    }
                //}
            }
            if (isset($tmpTotal) && !empty($tmpTotal)) {
                $response['contractors'] = [
                    'revisions' => $tmpRevision,
                    'revisionTotals' => $tmpTotal,
                ];
            }
        }
        return $response;
    }

    public function my_tenders($request, $userId)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        $projectStartDate = $projectCompletionDate = $tenderBidSubmissionEndDate = $tenderMaterialworktypeid = $tenderTenderReferenceNumber = '';
        if (request('search')) {
            $searchObj = json_decode(request('search'));
            if (isset($searchObj->project_start_date) && $searchObj->project_start_date) {
                $projectStartDate = $searchObj->project_start_date;
            }
            if (isset($searchObj->project_completion_date) && $searchObj->project_completion_date) {
                $projectCompletionDate = $searchObj->project_completion_date;
            }
            if (isset($searchObj->tender_bid_submission_end_date) && $searchObj->tender_bid_submission_end_date) {
                $tenderBidSubmissionEndDate = $searchObj->tender_bid_submission_end_date;
            }
        }

        $contractor = Contractor::where('user_id', $userId)->first();
        if ($request->item) {
            return ContractorTenderResource::collection(
                ContractorTender::when(request('search'), function ($query) {
                    $searchObj = json_decode(request('search'));
                })
                ->whereHas('tender', function ($query) use($tenderMaterialworktypeid) {
                    $query->where('material_work_type_id', 'like', '%' . $tenderMaterialworktypeid . '%');
                })
                ->whereHas('tender', function ($query) use($tenderTenderReferenceNumber) {
                    $query->where('tender_reference_number', 'like', '%' . $tenderTenderReferenceNumber . '%');
                })
                ->whereHas('tender', function ($query) use($tenderBidSubmissionEndDate) {
                    $query->where('bid_submission_end_date', 'like', '%' . $tenderBidSubmissionEndDate . '%');
                })
                ->whereHas('tender.project', function ($query) use($projectCompletionDate) {
                    $query->where('completion_date', 'like', '%' . $projectCompletionDate . '%');
                })
                ->whereHas('tender.project', function ($query) use($projectStartDate) {
                    $query->where('start_date', 'like', '%' . $projectStartDate . '%');
                })
                ->where('tender_id', $request->item)->orderBy($field, $order)->paginate($perPage)
            );
        }
        return ContractorTenderResource::collection(
            ContractorTender::when(request('search'), function ($query) {
            })->where('contractor_id', $contractor->id)->orderBy($field, $order)->paginate($perPage)
        );
    }

    public function no_of_tenders()
    {
        $contractor = Contractor::where('id', Auth::user()->id)->first();
    }

    public function automatic_comparison($tenderId)
    {
        if (!$tenderId) {
            return false;
        }
        $response = [];
        // Get Basic details of tender
        $tender = Tender::findOrFail($tenderId);

        $response['basic_info'] = [
            'builder_name' => $tender->project->organization->name,
            'project_name' => $tender->project->name,
            'building_name' => $tender->project->project_buildings ? implode(',', $tender->project->project_buildings->pluck('name', 'id')->toArray()): 'NA',
            'area' => $tender->project->total_project_area,
            'tender_reference_number' => $tender->tender_reference_number,
        ];

        // We need to get list of the works of selected tender
        $tenderMaterialWorks = TenderMaterialWork::where('tender_id', $tenderId)->get();
        if ($tenderMaterialWorks) {
            $tmp = [];
            $workArray = [];
            $i = 1;
            foreach ($tenderMaterialWorks as $tenderMaterialWork) {
                if (empty($workArray) || !in_array($tenderMaterialWork->material_work_type->name, $workArray)) {
                    array_push($workArray, $tenderMaterialWork->material_work_type->name);
                    $i = 1;
                }
                $tmp[$tenderMaterialWork->material_work_type_id. ':' .$tenderMaterialWork->material_work_type->name][$i] = [
                    'work' => $tenderMaterialWork->work,
                    'unit' => $tenderMaterialWork->unit->code,
                    'quantity' => $tenderMaterialWork->quantity,
                ];
                $i++;
            }
            $response['master_material_works'] = $tmp;
        }
        // Now get the list of all contractors
        $contractorTenders = ContractorTender::where('tender_id', $tenderId)->get();
        $tmpRevision = $tmpContractors = [];
        if (!empty($contractorTenders)) {

            foreach ($contractorTenders as $contractorTender) {
                if ($contractorTender) {
                    // Now for each tenders we need to find how many revisions are there

                    $workRevisions = $contractorTender->contractor_tender_work_revisions;
                    foreach ($workRevisions as $workRevision) {
                        // Get the list of all contractor works.
                        $tmpContractors = [];
                        $workArray = [];
                        $contractorTenderWorks = $contractorTender->contractor_tender_material_works->where('revision', $workRevision->revision);
                        if (!empty($contractorTenderWorks)) {
                            $j = 1;
                            foreach ($contractorTenderWorks as $contractorTenderWork) {

                                $materialWorkType = TenderMaterialWork::findOrFail($contractorTenderWork->tender_material_work_id);
                                $typeId = $materialWorkType->material_work_type_id;
                                $typeName = $materialWorkType->material_work_type->name;

                                if (empty($workArray) || !in_array($materialWorkType->material_work_type->name, $workArray)) {
                                    array_push($workArray, $materialWorkType->material_work_type->name);
                                    $j = 1;
                                }
                                $tmpContractors[$typeId . ':' . $typeName][$j] = [
                                    'work' => $materialWorkType->work,
                                    'rate' => $contractorTenderWork->rate,
                                    //'total_amount' => $contractorTenderWork->rate * $materialWorkType->quantity,
                                    'total_amount' => $contractorTenderWork->total,
                                ];
                                $j++;
                            }
                        }

                        $percentage = ContractorTenderRevision::where('contractor_tender_id', $contractorTender->id)
                                                    ->where('revision', $workRevision->revision)->first();
                        $tmpRevision[$contractorTender->contractor->name]['R'.$workRevision->revision - 1 . ':'.$percentage->percentage_difference][$workRevision->material_work_type_id . ':'.$workRevision->material_work_type->name] = [
                            'total_amount' => $workRevision->total_amount,
                            'percentage_difference' => $workRevision->percentage_difference,
                            'works' => $tmpContractors,
                        ];
                        $tmpTotal[$workRevision->revision - 1] = $workRevision->total_amount;
                    }
                }
            }
            $response['contractors'] = [
                'revisions' => $tmpRevision,
                'revisionTotals' => $tmpTotal,
            ];
        }
        return $response;
    }

    public function check_tender_password($request)
    {
        $response = [];
        if ($request->id) {
            $tender = Tender::findOrFail($request->id);
            if ($tender->tender_is_opened) {
                $response['success'] = true;
            } else if ($tender->tender_password == $request->tender_password) {
                $user = User::findOrFail(Auth::user()->id);
                $roles = $user->roles->pluck('name')->toArray();
                if (in_array('Organization', $roles) || in_array('Admin', $roles)) {
                    $tender->tender_is_opened = 1;
                    $tender->save();
                }

                $response['success'] = true;
            }
        }
        return $response;
    }

    public function check_tender_is_opened($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        if ($tender->tender_is_opened) {
            $response['success'] = true;
        } else{
            $response['success'] = false;
        }
        return $response;
    }

    public function check_contractor_bid($id, $userId)
    {
        $contractor = Contractor::where('user_id', $userId)->first();
        $contractorTender = ContractorTender::where('contractor_id', $contractor->id)->where('id', $id)->first();

        if (count($contractorTender->toArray()) > 0) {
            return [
                'success' => true,
                'contractorTender' => $contractorTender,
            ];
        }
        return [
            'success' => false,
        ];
    }

    public function get_tender_id($id)
    {
        $contractorTender = ContractorTender::findOrFail($id);
        if (count($contractorTender->toArray()) > 0) {
            return [
                'success' => true,
                'tender_id' => $contractorTender->tender_id,
            ];
        }
        return [
            'success' => false,
        ];
    }

    public function get_contractor_revisions($id)
    {
        config()->set('database.connections.mysql.strict', false);
        \DB::reconnect(); //important as the existing connection if any would be in strict mode

        $revisions = DB::table('contractor_tender_work_revisions')
                        ->select('id', 'revision', 'contractor_tender_id', DB::raw('ROUND(SUM(`total_amount`), 2) AS revision_total'))
                        ->whereRaw('contractor_tender_id = '.$id)
                        ->groupBy('revision')
                        ->orderBy('revision_total', 'asc')
                        ->get();
        //now changing back the strict ON
        config()->set('database.connections.mysql.strict', true);
        \DB::reconnect();

        return $revisions;
    }


    public function get_tender_info($id)
    {
        return Tender::findOrFail($id);
    }
}
