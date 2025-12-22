<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ContractorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $materials = array();
        if (!empty($this->material)) {
            foreach ($this->material as $material) {
                $materials[$material->id] = $material->id;
            }
        }

        $approvedStatus = "primary:Pending";
        if ($this->avp_by != null) {
            $approvedStatus = 'success:Approved';
        }

        $panNumber = $gstNumber = $companyAddress = $directorAddressProof = null;

        if ($this->contractor_documents->where('document_type_id', 1)) {
            $panResource = $this->contractor_documents->where('document_type_id', 1);

            if (isset($panResource[0]['value'])) {
                $panNumber = $panResource[0]['value'];
            }
        }
        if ($this->contractor_documents->where('document_type_id', 2)) {
            $gstResource = $this->contractor_documents->where('document_type_id', 2);
            if (isset($gstResource[0]['value'])) {
                $gstNumber = $gstResource[0]['value'];
            } else if (isset($gstResource[1]['value'])) {
                $gstNumber = $gstResource[1]['value'];
            }
        }
        if ($this->contractor_documents->where('document_type_id', 3)) {
            $companyResource = $this->contractor_documents->where('document_type_id', 3);
            if (isset($companyResource[0]['value'])) {
                $companyAddress = $companyResource[0]['value'];
            }
        }
        if ($this->contractor_documents->where('document_type_id', 4)) {
            $directorAddressProofResource = $this->contractor_documents->where('document_type_id', 4);
            if (isset($directorAddressProofResource[0]['value'])) {
                $directorAddressProof = $directorAddressProofResource[0]['value'];
            }
        }
        return [
            'id' => $this->id,
            'cUID' => $this->cUID,
            'name' => $this->name,
            'company_name' => $this->company_name,
            'est_year' => $this->est_year,
            'director_name' => $this->director_name,
            'address' => $this->address,
            'director_dob' => $this->director_dob,
            'director_address' => $this->director_address,
            'director_avatar' => $this->director_avatar,
            'director_avatar_url' => (file_exists($this->director_avatar)) ? URL::to($this->director_avatar) : URL::to("storage/images/no-image.jpg"),
            'email' => $this->email,
            'mobile_no' => $this->mobile_no,
            'company_landline_no' => $this->company_landline_no,
            'material_work_type_id' => $this->material_work_type_id,
            'material_work_type' => $this->material_work_type->name ?? $this->material_work_type_id,
            'with_labour_material' => $materials,
            'material' => $this->material,
            'checked_by' => $this->checked_by,
            'checked_name' => $this->checked_by ? $this->checked->name: null,
            'verified_by' => $this->verified_by,
            'avp_by ' => $this->avp_by,
            'approved_status' => $approvedStatus,
            'director' => $this->director,
            'last_login' => $this->last_login,
            'login_attempts' => $this->login_attempts,
            'contractor_bank_details' => $this->contractor_bank_details,
            'completed_works' => ContractorWorkResource::collection($this->contractor_works->where('completed', '=', 1)),
            'ongoing_works' => ContractorWorkResource::collection($this->contractor_works->where('completed', '=', 0)),
            'contact_persons' => $this->contractor_contacts,
            'director_proprietor' => ContractorDirectorTechnicalStaffResource::collection($this->contractor_director_technical_staffs->where('working_with_company_since', '=', null)),
            'technical_staff' => ContractorDirectorTechnicalStaffResource::collection($this->contractor_director_technical_staffs->where('working_with_company_since', '!=', null)),
            'equipments' => ContractorEquipmentResource::collection($this->contractor_equipments),
            'contractor_documents' => ContractorDocumentResource::collection($this->contractor_documents),
            'quality_certificates' => ContractorQualityCertificateResource::collection($this->contractor_quality_certificates),
            'turnovers' => ContractorTurnoverResource::collection($this->contractor_turnovers),
            'grade' => $this->grade,
            'user' => $this->user,
            'role' => implode(', ', $this->user->roles->pluck('name')->toArray()),
            'pan_no' => $panNumber,
            'gstin_no' => $gstNumber,
            'company_address' => $companyAddress,
            'director_address_proof' => $directorAddressProof,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
