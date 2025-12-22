<?php
namespace App\Repositories;

use App\Http\Resources\ContractorTenderResource;
use App\Http\Resources\ContractorTenderWorkOrderResource;
use App\Models\Contractor;
use App\Models\ContractorTender;
use App\Models\ContractorTenderMaterialWork;
use App\Models\ContractorTenderRevision;
use App\Models\ContractorTenderWorkOrder;
use App\Models\ContractorTenderWorkRevision;
use App\Models\MaterialWorkType;
use App\Models\Organization;
use App\Models\Tender;
use App\Models\TenderMaterialWork;
use App\Models\User;
use App\Repositories\Interfaces\ContractorTenderWorkOrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractorTenderWorkOrderRepository implements ContractorTenderWorkOrderRepositoryInterface
{
    public function all($request, $userId = null)
    {
        if ($userId) {
            $user = User::findOrFail($userId);
            $roles = $user->roles->pluck('name', 'id')->toArray();

            if (in_array('Organization', $roles)) {
                $organization = Organization::where('user_id', $userId)->first();
                $organizationId = $organization->id;
            }

            if (in_array('Contractor', $roles)) {
                $contractor = Contractor::where('user_id', $userId)->first();
                $contractorId = $contractor->id;
            }
        }

        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        $projectName = $organizationName = $contractorName = $adminName = '';
        if (request('search')) {
            $searchObj = json_decode(request('search'));
            if ($searchObj->project_name) {
                $projectName = $searchObj->project_name;
            }
            if ($searchObj->organization_name) {
                $organizationName = $searchObj->organization_name;
            }
            if ($searchObj->contractor_name) {
                $contractorName = $searchObj->contractor_name;
            }
            if ($searchObj->admin_name) {
                $adminName = $searchObj->admin_name;
            }
        }

        if (isset($organizationId)) {
            return ContractorTenderWorkOrderResource::collection(
                ContractorTenderWorkOrder::when(request('search'), function ($query) {
                    $searchObj = json_decode(request('search'));

                    //$query->where('contractor_name', ' LIKE % ' . request('item'). '%');
                })
                ->whereHas('tender.project', function ($query) use($projectName) {
                    $query->where('name', 'like', '%' . $projectName . '%');
                })
                ->whereHas('organization', function ($query) use($organizationName) {
                    $query->where('name', 'like', '%' . $organizationName . '%');
                })
                ->whereHas('contractor', function ($query) use($contractorName) {
                    $query->where('name', 'like', '%' . $contractorName . '%');
                })
                ->whereHas('user', function ($query) use($adminName) {
                    $query->where('name', 'like', '%' . $adminName . '%');
                })
                ->where('organization_id', $organizationId)
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        } else if (in_array('Contractor', $roles)) {

            return ContractorTenderWorkOrderResource::collection(
                ContractorTenderWorkOrder::when(request('search'), function ($query) {
                    //$query->where('contractor_name', '] LIKE % ' . request('item'). '%');
                })
                ->whereHas('tender.project', function ($query) use($projectName) {
                    $query->where('name', 'like', '%' . $projectName . '%');
                })
                ->whereHas('organization', function ($query) use($organizationName) {
                    $query->where('name', 'like', '%' . $organizationName . '%');
                })
                ->whereHas('contractor', function ($query) use($contractorName) {
                    $query->where('name', 'like', '%' . $contractorName . '%');
                })
                ->whereHas('user', function ($query) use($adminName) {
                    $query->where('name', 'like', '%' . $adminName . '%');
                })
                ->where('is_organization_approved', 1)
                ->where('contractor_id', $contractorId)
                ->orderBy($field, $order)
                ->paginate($perPage)
            );
        }
        return ContractorTenderWorkOrderResource::collection(
            ContractorTenderWorkOrder::when(request('search'), function ($query) {
                //$query->where('contractor_name', '] LIKE % ' . request('item'). '%');
            })
            ->whereHas('tender.project', function ($query) use($projectName) {
                $query->where('name', 'like', '%' . $projectName . '%');
            })
            ->whereHas('organization', function ($query) use($organizationName) {
                $query->where('name', 'like', '%' . $organizationName . '%');
            })
            ->whereHas('contractor', function ($query) use($contractorName) {
                $query->where('name', 'like', '%' . $contractorName . '%');
            })
            ->whereHas('user', function ($query) use($adminName) {
                $query->where('name', 'like', '%' . $adminName . '%');
            })
            ->orderBy($field, $order)->paginate($perPage)
        );
    }

    public function create($data, $userId = null)
    {
        $contractorTender = ContractorTender::findOrFail($data->contractor_tender_id);
        // First we need to check, if work order is already created for this tender
        $contractorTenderWorkOrder = ContractorTenderWorkOrder::where('contractor_tender_id', $data->contractor_tender_id)->first();
        if (!$contractorTenderWorkOrder) {
            $input = [
                'tender_id' => $contractorTender->tender_id,
                'contractor_tender_id' => $data->contractor_tender_id,
                'user_id' => $userId,
                'organization_id' => $data->organization_id,
                'contractor_id' => $data->contractor_id,
                'general_terms_conditions' => $data->general_terms_conditions,
                'more_detail' => $data->more_detail,
                'tender_cost' => $data->tender_cost,
                'period_of_payment_id' => $data->period_of_payment_id,
            ];

            $contractorTenderWorkOrder = ContractorTenderWorkOrder::create($input);

            if ($data->file('storage')) {
                $contractorTenderWorkOrder->storage = save_documents($data->file('storage'), 'contractor_tender_work_orders', $contractorTenderWorkOrder->id);
                $contractorTenderWorkOrder->save();
            }

            $tender = Tender::findOrFail($contractorTender->tender_id);
            $tender->is_work_order_generated = 1;
            $tender->save();

            $user = User::findOrFail($userId);

            // Log Activity
            activity()
                ->performedOn($contractorTenderWorkOrder)
                ->causedBy($user)
                ->withProperties(['customProperty' => 'customValue'])
                ->log($user->name . ' has generated work order for tender reference number: '.$contractorTender->tender->tender_reference_number);
        }

        return $contractorTenderWorkOrder;
    }

    public function check_work_order_already_generated($contractorTenderId)
    {
        $contractorTender = ContractorTender::findOrFail((int)$contractorTenderId);

        $contractorTenderWorkOrder = ContractorTenderWorkOrder::where('tender_id', $contractorTender->tender_id)->first();

        $success = false;
        $message = "Generate work order.";
        if ($contractorTenderWorkOrder) {
            $message = "Work order is already generated for this tender.";
            $success = true;
        }
        return ['message' => $message, 'success' => $success];
    }

    public function show($data)
    {

    }

    public function update($data, $userId = null)
    {
        // First of all, we need to check if logged user is gm, organization or contractor
        $contractorTenderWorkOrder = ContractorTenderWorkOrder::findOrFail($data->id);
        $user = User::findOrFail($userId);
        $userRoles = $user->roles->pluck('name')->toArray();

        $input = [];
        $status = 'approved';
        if (in_array('General Manager', $userRoles)) {
            $contractorTenderWorkOrder->is_gm_rejected = 0;
            $contractorTenderWorkOrder->is_gm_approved = 1;

            if ($data->is_rejected == 1) {
                $status = 'rejected';
                $contractorTenderWorkOrder->is_gm_rejected = 1;
                $contractorTenderWorkOrder->is_gm_approved = 0;

            }
            $contractorTenderWorkOrder->gm_comments = $data->gm_comments;
            $contractorTenderWorkOrder->gm_id = $user->id;

        }  else if (in_array('Organization', $userRoles)) {
            $contractorTenderWorkOrder->is_organization_rejected = 0;
            $contractorTenderWorkOrder->is_organization_approved = 1;

            if ($data->is_rejected == 1) {
                $status = 'rejected';
                $contractorTenderWorkOrder->is_organization_rejected = 1;
                $contractorTenderWorkOrder->is_organization_approved = 0;
            }
            $contractorTenderWorkOrder->organization_comments = $data->organization_comments;

        } else if (in_array('Contractor', $userRoles)) {
            $contractorTenderWorkOrder->is_contractor_rejected = 0;
            $contractorTenderWorkOrder->is_contractor_approved = 1;
            if ($data->is_rejected == 1) {
                $status = 'rejected';
                $contractorTenderWorkOrder->is_contractor_rejected = 1;
                $contractorTenderWorkOrder->is_contractor_approved = 0;
            }
            $contractorTenderWorkOrder->contractor_comments = $data->contractor_comments;

        } else if (in_array('Admin', $userRoles)) {
            $contractorTenderWorkOrder->is_admin_rejected = 0;
            $contractorTenderWorkOrder->is_admin_approved = 1;

            if ($data->is_rejected == 1) {
                $status = 'rejected';

                $contractorTenderWorkOrder->is_admin_rejected = 1;
                $contractorTenderWorkOrder->is_admin_approved = 0;
            }
            $contractorTenderWorkOrder->admin_comments = $data->admin_comments;
        }

        $contractorTenderWorkOrder->more_detail = $data->more_detail;
        $contractorTenderWorkOrder->save();
        activity()
            ->performedOn($contractorTenderWorkOrder)
            ->causedBy($user)
            ->withProperties(['customProperty' => 'customValue'])
            ->log($userRoles[0].' ' . $user->name . ' has '.$status.' work order for tender reference number: '.$contractorTenderWorkOrder->tender->tender_reference_number);

        //Once admin, gm, organization and contractor has approved the work order, it will be awarded to contractor.
        if ($contractorTenderWorkOrder->is_gm_approved &&
            $contractorTenderWorkOrder->is_organization_approved &&
            $contractorTenderWorkOrder->is_contractor_approved &&
            $contractorTenderWorkOrder->is_admin_approved
            ) {
            $contractorTenderWorkOrder->is_awarded = 1;
            $contractorTenderWorkOrder->save();

            $contractorTender = ContractorTender::findOrFail($contractorTenderWorkOrder->contractor_tender_id);
            $contractorTender->is_awarded = 1;
            $contractorTender->save();


            $tender = Tender::findOrFail($contractorTender->tender_id);
            $tender->awarded_to = $contractorTender->contractor_id;
            $tender->awarded_on = date("Y-m-d");
            $tender->approved_by = $user->id;
            $tender->status = 'inactive';
            $tender->is_under_budget = (int)$contractorTenderWorkOrder->tender_cost > (int)$tender->tender_value ? 0 : 1;
            $tender->save();

            activity()
            ->performedOn($contractorTenderWorkOrder)
            ->causedBy($user)
            ->withProperties(['customProperty' => 'customValue'])
            ->log('Tender with reference number: '.$contractorTenderWorkOrder->tender->tender_reference_number .' is awarded to contractor '.$contractorTender->contractor->name.' on '.$tender->awarded_on.'.');
        }
        return $contractorTenderWorkOrder;
    }


    public function delete($data)
    {

    }

}
