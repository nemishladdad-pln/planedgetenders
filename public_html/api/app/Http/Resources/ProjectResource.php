<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'pUID' => $this->pUID,
            'name' => $this->name,
            'billing_name' => $this->billing_name,
            'location' => $this->location,
            'organization_id'=> $this->organization_id,
            'organization_name' => $this->organization->name,
            'organization' => $this->organization,
            'project_type_id' => $this->project_type_id,
            'general_manager_id' => $this->general_manager_id,
            'site_project_manager_id' => $this->site_project_manager_id,
            'site_project_manager_name' => $this->site_project_manager_id ? $this->site_project_manager->name: null,
            'general_manager_name' => $this->general_manager_id ? $this->general_manager->name: null,
            'project_type_name' => $this->project_type->name,
            'number_of_floors' => $this->number_of_floors,
            'total_project_area' => $this->total_project_area,
            'created_by' => 'created_by',
            'updated_by' => 'updated_by',
            'is_active' => 'is_active',
            'project_buildings' => $this->project_buildings,
            'project_schedules' => $this->project_schedules,
            'get_active_tenders_count' => $this->tenders->count(),
            'active_tenders' => TenderResource::collection($this->tenders->where('status', 'active')->take(3)),
            'start_date' => $this->start_date,
            'completion_date' => $this->completion_date,
        ];
    }
}
