<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderMaterialWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (!is_string($this->quantity) && round($this->quantity, 2)) {
            $quantity = round($this->quantity, 2);
        } else {
            $quantity = $this->quantity;
        }
        
        return [
            'id' => $this->id,
            'tender_id' => $this->tender_id,
            'material_work_type_id' => $this->material_work_type_id,
            'material_work_type_name' => $this->material_work_type->name,
            'work' => $this->work,
            'rate' => $this->rate,
            'unit' => $this->unit,
            'unit_id' => $this->unit_id,
            'entered_rate' => '',
            'quantity' => $quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
