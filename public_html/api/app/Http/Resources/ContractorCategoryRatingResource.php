<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractorCategoryRatingResource extends JsonResource
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
            'contractor_id' => $this->contractor_id,
            'contractor_name' => $this->contractor->name,
            'site_manager_id' => $this->site_manager_id,
            'site_manager_name' => $this->site_manager->name,
            'category_rating_id' => $this->category_rating_id,
            'category_rating_name' => $this->category_rating->name,
            'quarter_id' => $this->quarter_id,
            'quarter_name' => $this->quarter->name,
            'rating' => $this->rating,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
