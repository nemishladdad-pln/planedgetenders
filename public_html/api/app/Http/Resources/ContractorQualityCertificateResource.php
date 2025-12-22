<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;

class ContractorQualityCertificateResource extends JsonResource
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
            'name' => $this->name,
            'storage' => $this->storage,
            'media' => File::extension($this->storage),
            'storage_url' => (file_exists($this->storage)) ? URL::to($this->storage) : URL::to("storage/images/no-image.jpg")
        ];
    }
}
