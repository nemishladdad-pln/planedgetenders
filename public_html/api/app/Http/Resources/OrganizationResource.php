<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bUID' => $this->bUID,
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
            'checked_by' => $this->checked_by,
            'verified_by' => $this->verified_by,
            'avp_by ' => $this->avp_by,
            'director' => $this->director,
            'turnover' => $this->turnover,
            'last_login' => $this->last_login,
            'login_attempts' => $this->login_attempts,
            'organization_bank_details' => $this->organization_bank_details,
            'documents' => OrganizationDocumentResource::collection($this->organization_documents),
            'organization_directors' => $this->organization_directors,
            'organization_works' => $this->organization_works,
            'user' => $this->user,
            'role' => implode(', ', $this->user->roles->pluck('name')->toArray()),
        ];
    }
}
