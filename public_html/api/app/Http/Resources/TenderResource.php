<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $project_buildings = $project_building_names = array();
        if (!empty($this->project_building)) {
            foreach ($this->project_building as $project_building) {
                $project_buildings[$project_building->id] = $project_building->id;
                $project_building_names[$project_building->id] = $project_building->name;
            }
        }

        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'project' => $this->project,
            'project_name' => $this->project->name,
            'project_building_ids' => $project_buildings,
            //'project_building' => $this->project_building,
            'project_building_name' => $project_building_names ? implode(', ', $project_building_names): null,
            'organization_name' => $this->project->organization->name,
            'organization_id'=> $this->project->organization_id,
            'organization' => $this->project->organization,
            'tender_reference_number' => $this->tender_reference_number,
            'tender_uid' => $this->tender_uid,
            'tender_type_id' => $this->tender_type_id,
            'tender_type_name'=> $this->tender_type->name,
            'form_contract_id' => $this->form_contract_id,
            'form_contract_name'=> $this->form_contract->name,
            'tender_category_id' => $this->tender_category_id,
            'tender_category_name' => $this->tender_category->name,
            'general_technical_evaluation' => $this->general_technical_evaluation ? 'Yes': 'No',
            'item_wise_evaluation_allowed' => $this->item_wise_evaluation_allowed ? 'Yes': 'No',
            'allow_two_stage_bidding' => $this->allow_two_stage_bidding ? 'Yes': 'No',
            //'tender_fee' => number_format($this->tender_fee, 2),
            'tender_fee' => $this->tender_fee,
            'is_paid' => $this->is_paid ? 'Yes': 'No',
            //'emd_amount' => number_format($this->emd_amount,2),
            'emd_amount' => $this->emd_amount,
            'emd_exemption_allowed' => $this->emd_exemption_allowed ? 'Yes': 'No',
            'emd_fee_type_id' => $this->emd_fee_type_id,
            'emd_fee_type_name' => $this->emd_fee_type->name,
            'emd_percentage' => $this->emd_percentage,
            'material_work_type_id' => $this->material_work_type_id,
            'material_work_type_name' => $this->material_work_type->name,
            'work_type_name' => $this->material_work_type->name,
            'work_title' => $this->work_title,
            'work_description' => $this->work_description,
            'pre_qualification' => $this->pre_qualification,
            'remarks' => $this->remarks,
            //'tender_value' => number_format($this->tender_value,2),
            'tender_value' => $this->tender_value,
            'location' => $this->location,
            'pin_code' => $this->pin_code,
            'bid_validity_days' => $this->bid_validity_days,
            'period_of_works' => $this->period_of_works,
            'pre_bid_meeting_place_id' => $this->pre_bid_meeting_place_id,
            'pre_bid_meeting_place_name' => $this->pre_bid_meeting_place->name,
            'pre_bid_meeting_date' => $this->pre_bid_meeting_date,
            'pre_bid_opening_date' => $this->pre_bid_opening_date,
            'pre_bid_opening_place' => $this->pre_bid_opening_place,
            'published_date' => $this->published_date,
            'bid_opening_date' => $this->bid_opening_date,
            'document_download_sale_start_date' => $this->document_download_sale_start_date,
            'document_download_sale_end_date' => $this->document_download_sale_end_date,
            'bid_submission_start_date' => $this->bid_submission_start_date,
            'bid_submission_end_date' => $this->bid_submission_end_date,
            'clarification_start_date' => $this->clarification_start_date,
            'clarification_end_date' => $this->clarification_end_date,
            'authorized_name' => $this->authorized_name,
            'authorized_address' => $this->authorized_address,
            'is_verified' => $this->is_verified,
            'tender_documents' => TenderDocumentResource::collection($this->tender_documents),
            'tender_material_works' => TenderMaterialWorkResource::collection($this->tender_material_works)->collection->groupBy('material_work_type_id'),
            'created_by' => $this->created_by,
            'created_user' => $this->created_user->name,
            'status' => $this->status,
        ];
    }
}
