<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends FormRequest
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
            'name' => [
                'required',
            ],
            'company_name' => [
                'required',
            ],
            'est_year' => [
                'required',
            ],
            'director_name' => [
                'required',
            ],
            'address' => [
                'required',
            ],
            'director_DOB' => [
                'date',
            ],
            // 'director_address' => [
            //     'required',
            // ],
            // 'director_avatar' => [
            //     'required',
            // ],
            'email' => [
                'required',
                Rule::unique('organizations')->ignore($this->organization),
                Rule::unique('users')->ignore($this->organization)
            ],
            'mobile_no' => [
                'required',
            ],
            // 'pan_no' => [
            //     'required',
            // ],
            // 'gstin' => [
            //     'required',
            // ],
            // 'lbt_num_firm' => [
            //     'required',
            // ],
            // 'service_tax_num_firm' => [
            //     'required',
            // ],
            // 'material_work_type_id' => [
            //     'required',
            // ],
            /*'with_labour_material' => [
                'required',
            ],*/
        ];
    }
}
