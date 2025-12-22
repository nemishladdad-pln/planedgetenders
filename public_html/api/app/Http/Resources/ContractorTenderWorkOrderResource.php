<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractorTenderWorkOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $gmStatus = $organizationStatus = $contractorStatus = $adminStatus = "primary:Pending";
        if ($this->is_gm_rejected == 1) {
            $gmStatus = 'danger:Rejected';
        } else if ($this->is_gm_approved == 1) {
            $gmStatus = 'success:Approved';
        }
        if ($this->is_organization_rejected == 1) {
            $organizationStatus = 'danger:Rejected';
        } else if ($this->is_organization_approved == 1) {
            $organizationStatus = 'success:Approved';
        }

        if ($this->is_contractor_rejected == 1) {
            $contractorStatus = 'sanger:Rejected';
        } else if ($this->is_contractor_approved == 1) {
            $contractorStatus = 'success:Approved';
        }

        if ($this->is_admin_rejected == 1) {
            $adminStatus = 'danger:Rejected';
        } else if ($this->is_admin_approved == 1) {
            $adminStatus = 'success:Approved';
        }
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'contractor_tender_id' => $this->contractor_tender_id,
            'tender_reference_number' => $this->contractor_tender->tender->tender_reference_number,
            'tender_id' => $this->contractor_tender->tender_id,
            'gm_id' => $this->gm_id,
            'gm_name' => $this->gm_id == null ? $this->tender->project->general_manager->name: $this->general_manager->name,
            'site_project_manager_name' => $this->tender->project->site_project_manager->name,
            'organization_id' => $this->organization_id,
            'organization_name' => $this->organization->name,
            'contractor_id' => $this->contractor_id,
            'contractor_name' => $this->contractor->name,
            'contractor_info' => $this->contractor,
            'contractor_documents_pan' => $this->contractor->contractor_documents->where('document_type_id', 1),
            'contractor_documents_gst' => $this->contractor->contractor_documents->where('document_type_id', 2),
            'general_terms_conditions' => $this->general_terms_conditions,
            'more_detail' => $this->more_detail,
            'is_gm_approved' => $this->is_gm_approved,
            'is_organization_approved' => $this->is_organization_approved,
            'is_contractor_approved' => $this->is_contractor_approved,
            'is_admin_approved' => $this->is_admin_approved,
            'organization_comments' => $this->organization_comments,
            'contractor_comments' => $this->contractor_comments,
            'gm_comments' => $this->gm_comments,
            'admin_comments' => $this->admin_comments,
            'is_awarded' => $this->is_awarded,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'gm_status' => $gmStatus,
            'admin_status' => $adminStatus,
            'contractor_status' => $contractorStatus,
            'organization_status' => $organizationStatus,
            'project_name' => $this->contractor_tender->tender->project->name,
            'work_type' => $this->contractor_tender->tender->material_work_type->name,
            'tender_cost' => $this->tender_cost,
            'storage' => $this->storage,
            'period_of_payment_id' => $this->period_of_payment_id,
            'period_of_payment_name' => $this->period_of_payment_id && $this->period_of_payment ? $this->period_of_payment->name: '',
        ];
    }
}
