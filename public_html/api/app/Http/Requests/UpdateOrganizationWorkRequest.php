<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationWorkRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title_description' => [
                'required',
            ],
            'scope_work' => [
                'required',
            ],
            'location' => [
                'required',
            ],
            'tendered_cost' => [
                'required',
            ],
            'actual_cost' => [
                'required',
            ],
            'stage_work' => [
                'required',
            ],
            'award_date' => [
                'required',
            ],
            'planned_completion_date' => [
                'required',
            ],
            'actual_completion_date' => [
                'required',
            ],
            'client_contact_person' => [
                'required',
            ],
            'architects_name_address_tel_no' => [
                'required',
            ],
            'other_consultants_name_address_tel_no' => [
                'required',
            ],
            'responsible_staff' => [
                'required',
            ],
            'completed' => [
                'required',
            ],
        ];
    }
}
