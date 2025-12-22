<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractorTenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $workOrderGenerated = 'No';
        if ($this->tender->is_work_order_generated == 1) {
            $workOrderGenerated = 'Yes';
        }
        return [
            'id' => $this->id,
            'contractor_id' => $this->contractor_id,
            'tender_id' => $this->tender_id,
            'tender_uid' => $this->tender->tender_uid,
            'tender_reference_number' => $this->tender->tender_reference_number,
            'is_work_order_generated' => $workOrderGenerated,
            'bid_end_date' => $this->tender->bid_submission_end_date,
            'work_type' => $this->tender->material_work_type->name,
            'is_awarded' => $this->is_awarded,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'contractor_name' => $this->contractor->name,
            'company_name' => $this->contractor->company_name,
            'gm_name' => $this->tender->project->general_manager->name,
            'site_project_manager_name' => $this->tender->project->site_project_manager->name,
            'materials' => $this->contractor->material,
            'material_names' => $this->contractor->material->pluck('name'),
            'revisions' => $this->contractor_tender_revisions->count(),
            'created_at' => $this->created_at,
            'contractor_info' => $this->contractor,
            'contractor_documents_pan' => $this->contractor->contractor_documents->where('document_type_id', 1),
            'contractor_documents_gst' => $this->contractor->contractor_documents->where('document_type_id', 2),
            'project_name' => $this->tender->project->name,
            'organization_id' => $this->tender->project->organization->id,
            'organization_name' => $this->tender->project->organization->name,
            'contract_value' => $this->tender->tender_value,
            'work_order_collection' => ContractorTenderWorkOrderResource::collection($this->contractor_tender_work_orders),
            'work_orders' => $this->contractor_tender_work_orders,
            //'works' => $this->contractor_tender_material_works,
            'start_date' => $this->tender->project->start_date,
            'completion_date' => $this->tender->project->completion_date,
            'created_at_formatted' => Carbon::parse($this->created_at)->format('M d Y') ,
        ];
    }
}
