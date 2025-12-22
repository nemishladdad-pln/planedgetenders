<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;

class ContractorTurnoverResource extends JsonResource
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
            'year'=> $this->year,
            'turnover' => $this->turnover,
            'certificate_storage' => $this->certificate_storage,
            'media' => File::extension($this->certificate_storage),
            'storage_url' => (file_exists($this->certificate_storage)) ? URL::to($this->certificate_storage) : URL::to("storage/images/no-image.jpg")
        ];
    }
}
