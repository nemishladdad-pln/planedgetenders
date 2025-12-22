<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenderMaterialWorkRequest extends FormRequest
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
            'email' => 'required'|'email',
            'name_of_site' => 'required',
            'name_of_organization' => 'required',
            'name_of_site_manager' => 'required',
            'company_name' => 'required',
            'name_of_client' => 'required',
            'period_of_payment' => 'required',
            'labour_with_material' => 'required',
            'material_work_type_id' => 'required',
        ];
    }
}
