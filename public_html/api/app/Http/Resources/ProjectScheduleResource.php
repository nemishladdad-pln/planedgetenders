<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $projectName = 'Not Available';
        if ($this->project_id && $this->project && $this->project->name) {
            $projectName = $this->project->name;
        }

        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'project_name' => $projectName,
            'task' => $this->task,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
