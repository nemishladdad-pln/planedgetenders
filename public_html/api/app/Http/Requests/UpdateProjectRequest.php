<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'name' => [
                'required'
            ],
            'billing_name' => [
                'required',
            ],
            'location' => [
                'required'
            ],
            'project_type_id' => [
                'required',
            ],
            'total_project_area' => [
                'required',
            ],
            'site_project_manager_id' => [
                'required',
            ],
            'general_manager_id' => [
                'required',
            ],
            'start_date' => [
                'required',
            ],
            'completion_date' => [
                'required',
            ],
        ];
    }
}
