<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class OrganizationDocumentResource extends JsonResource
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
            'organization_id' => $this->organization_id,
            'document_type_id'=> $this->document_type_id,
            'document_name' => $this->document_type->name,
            'document_value' => $this->value,
            'storage' => $this->storage,
            'media' => File::extension($this->storage),
            'storage_url' => (file_exists($this->storage)) ? URL::to($this->storage) : URL::to("storage/images/no-image.jpg")
        ];
    }
}
