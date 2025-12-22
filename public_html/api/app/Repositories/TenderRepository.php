<?php
namespace App\Repositories;

use App\Http\Requests\StoreTenderRequest;
use App\Http\Resources\ProjectScheduleResource;
use App\Http\Resources\TenderResource;
use App\Imports\TenderWorkListImport;
use App\Models\Contractor;
use App\Models\MaterialWorkType;
use App\Models\Organization;
use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\Tender;
use App\Models\TenderDocument;
use App\Models\TenderMaterialWork;
use App\Models\User;
use App\Repositories\Interfaces\TenderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\TenderCreated;

class TenderRepository implements TenderRepositoryInterface
{

    /**
     * @param mixed $request
     */
    public function all($request, $userId = null, $status = null)
    {
        // Now we need to check if logged in user is contractor.
        // If yes, then contractor must be able to see list of the tenders of material type for which he is registered.
        if ($userId) {
            $user = User::findOrFail($userId);
            $userRole = $user->roles->pluck('name', 'id')->toArray();

            //dd($userRole);
            if (in_array('Contractor', $userRole)) {
                $materialType = $this->__contractorMaterialType();

                if ($materialType) {
                    request()->merge(['material_work_type_id' => $materialType]);
                }
            }
            if (in_array('Organization', $userRole)) {
                $organization = Organization::where('user_id', $userId)->first();
                $organizationId = $organization->id;
            }
        }
        if (!$status) {
            $status = "active";
        }

        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        $projectName = $organizationName = '';
        if (request('search')) {
            $searchObj = json_decode(request('search'));
            if ($searchObj->project_name) {
                $projectName = $searchObj->project_name;
            }
            if ($searchObj->organization_name) {
                $organizationName = $searchObj->organization_name;
            }
        }

        if (isset($materialType) && $materialType) {
            $collection = TenderResource::collection(
                Tender::when(request('search'), function ($query) {
                    if (request('material_work_type_id')) {
                        $query->where('material_work_type_id', request('material_work_type_id '));
                    }
                    $searchObj = json_decode(request('search'));

                    if ($searchObj->bid_submission_start_date && $searchObj->bid_submission_start_date != '') {
                        $query->where('bid_submission_start_date', $searchObj->bid_submission_start_date);
                    }
                    if ($searchObj->bid_submission_end_date && $searchObj->bid_submission_end_date != '') {
                        $query->where('bid_submission_end_date', $searchObj->bid_submission_end_date);
                    }
                })
                ->whereHas('project', function ($query) use($projectName) {
                    $query->where('name', 'like', '%' . $projectName . '%');
                })
                ->whereHas('project.organization', function ($query) use($organizationName) {
                    $query->where('name', 'like', '%' . $organizationName . '%');
                })
                ->where('material_work_type_id', $materialType)
                ->where('status', $status)
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        } else if (isset($organizationId)) {
            // Now, get list of all the projects of selected organization
            $projects = array_keys($organization->projects->pluck('name', 'id')->toArray());

            $collection = TenderResource::collection(
                Tender::when(request('search'), function ($query) {
                    if (request('material_work_type_id')) {
                        $query->where('material_work_type_id', request('material_work_type_id '));
                    }
                    $searchObj = json_decode(request('search'));

                    if ($searchObj->bid_submission_start_date && $searchObj->bid_submission_start_date != '') {
                        $query->where('bid_submission_start_date', $searchObj->bid_submission_start_date);
                    }
                    if ($searchObj->bid_submission_end_date && $searchObj->bid_submission_end_date != '') {
                        $query->where('bid_submission_end_date', $searchObj->bid_submission_end_date);
                    }
                })
                ->whereHas('project', function ($query) use($projectName) {
                    $query->where('name', 'like', '%' . $projectName . '%');
                })
                ->whereHas('project.organization', function ($query) use($organizationName) {
                    $query->where('name', 'like', '%' . $organizationName . '%');
                })
                ->whereIn('project_id', array_values($projects))
                ->where('status', $status)
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        } else if (isset($userRole) && in_array('Site Project Manager', $userRole)) {

            $projects = Project::where('site_project_manager_id', $user->id)->get();
            $projectIds = array_keys($projects->pluck('name', 'id')->toArray());

            $collection = TenderResource::collection(
                Tender::when(request('search'), function ($query) {
                    if (request('material_work_type_id')) {
                        $query->where('material_work_type_id', request('material_work_type_id '));
                    }
                    $searchObj = json_decode(request('search'));

                    if ($searchObj->bid_submission_start_date && $searchObj->bid_submission_start_date != '') {
                        $query->where('bid_submission_start_date', $searchObj->bid_submission_start_date);
                    }
                    if ($searchObj->bid_submission_end_date && $searchObj->bid_submission_end_date != '') {
                        $query->where('bid_submission_end_date', $searchObj->bid_submission_end_date);
                    }
                })
                ->whereHas('project', function ($query) use($projectName) {
                    $query->where('name', 'like', '%' . $projectName . '%');
                })
                ->whereHas('project.organization', function ($query) use($organizationName) {
                    $query->where('name', 'like', '%' . $organizationName . '%');
                })
                ->whereIn('project_id', $projectIds)
                ->where('status', $status)
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        } else if (isset($userRole) && in_array('General Manager', $userRole)) {
            $projects = Project::where('general_manager_id', $user->id)->get();
            $projectIds = array_keys($projects->pluck('name', 'id')->toArray());

            $collection = TenderResource::collection(
                Tender::when(request('search'), function ($query) {
                    if (request('material_work_type_id')) {
                        $query->where('material_work_type_id', request('material_work_type_id '));
                    }
                    $searchObj = json_decode(request('search'));

                    if ($searchObj->bid_submission_start_date && $searchObj->bid_submission_start_date != '') {
                        $query->where('bid_submission_start_date', $searchObj->bid_submission_start_date);
                    }
                    if ($searchObj->bid_submission_end_date && $searchObj->bid_submission_end_date != '') {
                        $query->where('bid_submission_end_date', $searchObj->bid_submission_end_date);
                    }
                })
                ->whereHas('project', function ($query) use($projectName) {
                    $query->where('name', 'like', '%' . $projectName . '%');
                })
                ->whereHas('project.organization', function ($query) use($organizationName) {
                    $query->where('name', 'like', '%' . $organizationName . '%');
                })
                ->whereIn('project_id', $projectIds)
                ->where('status', $status)
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        } else {
            $collection = $this->_getTenderCollection($request, $status);
        }
        return $collection;
    }

    private function _getTenderCollection($request, $status)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        $projectName = $organizationName = '';
        if (request('search')) {
            $searchObj = json_decode(request('search'));
            if ($searchObj->project_name) {
                $projectName = $searchObj->project_name;
            }
            if ($searchObj->organization_name) {
                $organizationName = $searchObj->organization_name;
            }
        }

        if (!$status) {
            $filterStatus = 'active';
        } else {
            $filterStatus = $status;
        }
        $collection = TenderResource::collection(
            Tender::when(request('search'), function ($query) {
                $searchObj = json_decode(request('search'));

                if ($searchObj->bid_submission_start_date && $searchObj->bid_submission_start_date != '') {
                    $query->where('bid_submission_start_date', $searchObj->bid_submission_start_date);
                }
                if ($searchObj->bid_submission_end_date && $searchObj->bid_submission_end_date != '') {
                    $query->where('bid_submission_end_date', $searchObj->bid_submission_end_date);
                }
            })
            ->whereHas('project', function ($query) use($projectName) {
                $query->where('name', 'like', '%' . $projectName . '%');
            })
            ->whereHas('project.organization', function ($query) use($organizationName) {
                $query->where('name', 'like', '%' . $organizationName . '%');
            })
            ->where('status', $filterStatus)
            ->orderBy($field, $order)->paginate($perPage)
        );

        return $collection;
    }

    public function create($data, $user = null)
    {
        $userInfo = User::findOrFail($user->id);
        //dd($data);
        $tenderUID = $this->__getTenderUniqueId($data);

        // Get project unique id
        $project = Project::findOrFail($data->project_id);
//dd($data);
        $inputs = [
            'project_id' => $data->project_id,
            'tender_reference_number' => $this->__getTenderReferenceNumber($data, $project),
            'tender_uid' => $tenderUID,
            'tender_type_id' => $data->tender_type_id,
            'form_contract_id' => $data->form_contract_id,
            'tender_category_id' => $data->tender_category_id,
            'general_technical_evaluation' => (bool)$data->general_technical_evaluation,
            'item_wise_evaluation_allowed' => (bool)$data->item_wise_evaluation_allowed,
            'allow_two_stage_bidding' => (bool)$data->allow_two_stage_bidding,
            'tender_fee' => (int)$data->tender_fee,
            'is_paid' => (bool)$data->is_paid,
            'emd_amount' => (int)$data->emd_amount,
            'emd_exemption_allowed' => (bool)$data->emd_exemption_allowed,
            'emd_fee_type_id' => $data->emd_fee_type_id,
            'emd_percentage' => $data->emd_percentage,
            'material_work_type_id' => $data->material_work_type_id,
            // 'work_title' => $data->work_title,
            // 'work_description' => $data->work_description,
            'pre_qualification' => $data->pre_qualification,
            'remarks' => $data->remarks,
            'tender_value' => (int)$data->tender_value,
            'location' => $data->location,
            'pin_code' => $data->pin_code,
            'bid_validity_days' => $data->bid_validity_days,
            'period_of_works' => $data->period_of_works,
            'pre_bid_meeting_place_id' => $data->pre_bid_meeting_place_id,
            'pre_bid_meeting_date' => $data->pre_bid_meeting_date,
            'pre_bid_opening_date' => $data->pre_bid_opening_date,
            'pre_bid_opening_place' => $data->pre_bid_opening_place,
            'published_date' => $data->published_date,
            'bid_opening_date' => $data->bid_opening_date,
            'document_download_sale_start_date' => $data->document_download_sale_start_date,
            'document_download_sale_end_date' => $data->document_download_sale_end_date,
            'bid_submission_start_date' => $data->bid_submission_start_date,
            'bid_submission_end_date' => $data->bid_submission_end_date,
            'clarification_start_date' => $data->clarification_start_date,
            'clarification_end_date' => $data->clarification_end_date,
            'authorized_name' => $data->authorized_name,
            'authorized_address' => $data->authorized_address,
            'is_verified' => (bool)$data->is_verified,
            'status' => $data->status ? $data->status : 'active',
            'created_by' => $userInfo->id,
            'updated_by' => $userInfo->id,
            'tender_password' => generate_unique_string(),
        ];
        $tender = Tender::create($inputs);

        $tender->project_building()->sync((array) json_decode($data->project_building_ids));

        // Now save the documents
        if (!empty($data->documents)) {
            $i = 0;
            foreach ((array) json_decode($data->documents, true, 10) as $document) {
                $tender->tender_documents()->create([
                    'tender_document_type_id' => $document['tender_document_type_id'],
                    'document_format_id' => $document['document_format_id'],
                    'description' => $document['description'],
                    'storage' => save_documents($data->file('document_'.$i), 'tenders', $tenderUID),
                ]);
                $i++;
             }
        }

        // Now save the material works of tender.
        $this->__importTenderMaterialWorks($data, $tender->id);
        //$tender->tender_material_works()->createMany($data->tender_material_works);
        // Once tender is generated, we need to send these tender to appropriate contractors/vendors.

        activity('tender')
            ->performedOn($tender)
            ->causedBy($userInfo)
            ->withProperties(['tender' => $tender->tender_reference_number])
            ->log('Tender Floated For  '.$project->name);

        // Send notifications to all contractors with select material work type
        // Send email notification to organization about tender creation and tender password.
        event(new TenderCreated($tender));

        return $tender;
    }

    public function update($data, $tender)
    {
        if (!empty($data)) {
            if ($data->form && $data->form === 'basic') {
                $tender = $this->_saveBasicInfo(json_decode($data->basic), $tender);
            }
            if ($data->form && $data->form === 'documents') {
                $tender = $this->_saveDocuments($data, $tender);
            }
            if ($data->form && $data->form === 'works') {
                $tender = $this->_saveWorks($data, $tender);
            }
        }
        activity()
            ->performedOn($tender)
            // ->name('tender')
            ->withProperties(['tender' => $tender->tender_reference_number])
            ->log('Tender information is updated  '.$tender->project->name);
        return $tender;
    }

    private function _saveBasicInfo($data, $tender)
    {
        $input = [
            'tender_type_id' => $data->tender_type_id,
            //'organization_id' => $data->organization_id,
            //'project_id' => $data->project_id,
            'project_building_ids' => $data->project_building_ids,
            'form_contract_id' => $data->form_contract_id,
            'tender_category_id' => $data->tender_category_id,
            'general_technical_evaluation' => $data->general_technical_evaluation === 'Yes' ? 1: 0,
            'item_wise_evaluation_allowed' => $data->item_wise_evaluation_allowed === 'Yes' ? 1: 0,
            'allow_two_stage_bidding' => $data->allow_two_stage_bidding === 'Yes' ? 1: 0,
            'tender_fee' => $data->tender_fee,
            'is_paid' => $data->is_paid === 'Yes' ? 1: 0,
            'emd_amount' => $data->emd_amount,
            'emd_exemption_allowed' => (bool)$data->emd_exemption_allowed,
            'emd_fee_type_id' => $data->emd_fee_type_id,
            'emd_percentage' => $data->emd_percentage,
            'material_work_type_id' => $data->material_work_type_id,
            'pre_qualification' => $data->pre_qualification,
            'remarks' => $data->remarks,
            'tender_value' => $data->tender_value,
            'location' => $data->location,
            'pin_code' => $data->pin_code,
            'bid_validity_days' => $data->bid_validity_days,
            'period_of_works' => $data->period_of_works,
            'pre_bid_meeting_place_id' => $data->pre_bid_meeting_place_id,
            'pre_bid_meeting_date' => $data->pre_bid_meeting_date,
            'pre_bid_opening_date' => $data->pre_bid_opening_date,
            'pre_bid_opening_place' => $data->pre_bid_opening_place,
            'published_date' => $data->published_date,
            'bid_opening_date' => $data->bid_opening_date,
            'document_download_sale_start_date' => $data->document_download_sale_start_date,
            'document_download_sale_end_date' => $data->document_download_sale_end_date,
            'bid_submission_start_date' => $data->bid_submission_start_date,
            'bid_submission_end_date' => $data->bid_submission_end_date,
            'clarification_start_date' => $data->clarification_start_date,
            'clarification_end_date' => $data->clarification_end_date,
            'authorized_name' => $data->authorized_name,
            'authorized_address' => $data->authorized_address,
            'status' => $data->status,
            'updated_by' => Auth::user()->id,
        ];


        // if ($data->project_building_ids) {
        //     $tender->project_building()->sync((array) json_decode($data->project_building_ids));
        // }

        $tender->update($input);
        return $tender;
    }

    private function _saveDocuments($data, $tender)
    {
        if (!empty($data->documents)) {
            $i = 0;
            foreach ((array) json_decode($data->documents, true, 10) as $document) {
                $storage = null;
                if ($data->file('document_'.$i)) {
                    $storage = save_documents($data->file('document_'.$i), 'tenders', $tender->tender_uid);
                }
                if (isset($document['id'])) {
                    $docObj = TenderDocument::find($document['id']);
                    $docObj->tender_document_type_id = $document['tender_document_type_id'];
                    $docObj->document_format_id = $document['document_format_id'];
                    $docObj->description = $document['description'];

                    if ($storage) {
                        $docObj->storage = $storage;
                    }
                    $docObj->save();
                } else {
                    $tender->tender_documents()->create([
                        'tender_document_type_id' => $document['tender_document_type_id'],
                        'document_format_id' => $document['document_format_id'],
                        'description' => $document['description'],
                        'storage' => $storage,
                    ]);
                }
                $i++;
             }
        }
        return $tender;
    }

    private function _saveWorks($data, $tender)
    {
        if (!empty($data->works)) {
            $i = 0;
            foreach ((array) json_decode($data->works, true, 10) as $materialWork) {

                foreach ($materialWork as $work) {
                    if (isset($work['id'])) {

                        $docObj = TenderMaterialWork::find($work['id']);

                        $docObj->work = $work['work'];
                        $docObj->material_work_type_id = $work['material_work_type_id'];
                        $docObj->unit_id = $work['unit_id'];
                        $docObj->rate = $work['rate'];
                        $docObj->quantity = $work['quantity'];

                        $docObj->save();
                    } else {
                        $input = [
                            'material_work_type_id' => $work['material_work_type_id'],
                            'work' => $work['work'],
                            'unit_id' => $work['unit_id'],
                            'rate' => $work['rate'],
                            'quantity' => $work['quantity'],
                        ];
                        $tender->tender_material_works()->create($input);
                    }
                }

                $i++;
             }
             // Now we will all the items that are removed.
            if (!empty($data->work_removed)) {
                TenderMaterialWork::destroy((array) json_decode($data->work_removed));
            }
        }
        return $tender;
    }

    private function __getTenderReferenceNumber($data, $project)
    {
        // Get project unique id
        $count = 0;
        $tender = Tender::get();
        $count = str_pad( $tender->count() + 1, 2, "0", STR_PAD_LEFT );
        return $project->pUID.'_'.$project->name.'_'.$data->material_work_type_id.'_'.$count;
    }

    private function __getTenderUniqueId($data)
    {
        $project = Project::findOrFail($data->project_id);
        $pUID = explode('_', $project->pUID);
        $count = 0;
        $tender = Tender::get();
        $count = str_pad( $tender->count() + 1, 2, "0", STR_PAD_LEFT );
        return $pUID[0].'_'.$data->material_work_type_id.'_'.$count;
    }

    private function _generateTenderPassword()
    {

    }
    private function __importTenderMaterialWorks($data, $tenderId)
    {
        $file = $data->file('work_list')->store('import');
        try {
            $importResponse = Excel::import(new TenderWorkListImport($data, $tenderId), $file);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $response = [
                'success' => false,
                'message' => '(s) are not imported.',
                'failures' => $failures,
             ];
        }
    }

    public function show($tender)
    {
        return TenderResource::make(Tender::findOrFail($tender->id));
    }

    private function __contractorMaterialType(): mixed
    {
        if (in_array('Contractor', Auth::user()->roles->pluck('name')->toArray())) {
            $contractor = Contractor::where('user_id', Auth::user()->id)->first();
            return $contractor->material_work_type_id;
        }
        return false;
    }

    public function upcoming_tenders($request, $userId)
    {
        if ($userId) {
            $user = User::findOrFail($userId);
            $userRole = $user->roles->pluck('name', 'id')->toArray();

            if (in_array('Organization', $userRole)) {
                $organization = Organization::where('user_id', $userId)->first();
                $organizationId = $organization->id;
            }
        }

        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        if (isset($organizationId)) {
            // Now, get list of all the projects of selected organization
            $projects = array_keys($organization->projects->pluck('name', 'id')->toArray());

            $collection = ProjectScheduleResource::collection(
                ProjectSchedule::when(request('search'), function ($query) {

                    $query->where('task', 'like', '%' . request('search') . '%')
                        ->orWhere('start_date', 'like', '%'. request('search') . '%')
                        ->orWhere('end_date', 'like', '%'. request('search') . '%');
                })
                ->whereIn('project_id', array_values($projects))
                ->whereBetween('start_date', [date('Y-m-d') , date('Y-m-d', strtotime('+2 months'))])
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        } else {
            $collection = ProjectScheduleResource::collection(
                ProjectSchedule::when(request('search'), function ($query) {

                    $query->where('task', 'like', '%' . request('search') . '%')
                        ->orWhere('start_date', 'like', '%'. request('search') . '%')
                        ->orWhere('end_date', 'like', '%'. request('search') . '%');
                })
                ->whereBetween('start_date', [date('Y-m-d') , date('Y-m-d', strtotime('+2 months'))])
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        }
        return $collection;

    }
}
