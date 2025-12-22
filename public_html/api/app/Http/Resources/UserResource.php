<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $verifiedStatus = "primary:Pending";
        if ($this->email_verified_at != null) {
            $verifiedStatus = 'success:Verified';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->profile_user && $this->profile_user->mobile_no ? $this->profile_user->mobile_no: null,
            'role_id' => $this->roles[0]->id,
            'role' => $this->roles,
            'role_name' => $this->roles[0]->name,
            'email_verified_at' => $this->email_verified_at,
            'verified_status' => $verifiedStatus,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
