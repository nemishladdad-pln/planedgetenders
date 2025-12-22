<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class SettingResource extends JsonResource
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
            'name' => $this->name,
            'value' => $this->value,
            'field_type' => $this->field_type,
            'setting_type_id' => $this->setting_type_id,
            'file_url' => ($this->field_type === 'file' && file_exists($this->value)) ? URL::to($this->value) : URL::to("storage/images/no-image.jpg"),
        ];
    }
}
