<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'required',
            'project_building_id' => 'required',
            'tender_type_id' => 'required',
            'form_contract_id' => 'required',
            'tender_category_id' => 'required',
            //'general_technical_evaluation' => 'required',
            //'item_wise_evaluation_allowed' => 'required',
            'allow_two_stage_bidding' => 'required',
            'tender_fee' => 'required',
            //'is_paid' => 'required',
            'emd_amount' => 'required',
            'emd_exemption_allowed' => 'required',
            'emd_fee_type_id' => 'required',
            'emd_percentage' => 'required',
            // 'material_work_type_id' => 'required',
            // 'work_title' => 'required',
            // 'work_description' => 'required',
            'pre_qualification' => 'required',
            'remarks' => 'required',
            'tender_value' => 'required',
            'location' => 'required',
            'pin_code' => 'required',
            'bid_validity_days' => 'required',
            'period_of_works' => 'required',
            'pre_bid_meeting_place_id' => 'required',
            'pre_bid_meeting_date' => 'required',
            //'pre_bid_opening_date' => 'required',
            'published_date' => 'required',
            'bid_opening_date' => 'required',
            'document_download_sale_start_date' => 'required',
            'document_download_sale_end_date' => 'required',
            'bid_submission_start_date' => 'required',
            'bid_submission_end_date' => 'required',
            'clarification_start_date' => 'required',
            'clarification_end_date' => 'required',
            'authorized_name' => 'required',
            'authorized_address' => 'required',
            //'is_verified'
        ];
    }
}
