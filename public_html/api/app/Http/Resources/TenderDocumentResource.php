<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class TenderDocumentResource extends JsonResource
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
            'tender_id' => $this->tender_id,
            'document_format_id' => $this->document_format_id,
            'tender_document_type_id'=> $this->tender_document_type_id,
            'document_name' => $this->tender_document_type->name,
            'document_format_name' => $this->document_format->name,
            'document_value' => $this->value,
            'storage' => $this->storage,
            'description' => $this->description,
            'storage_url' => (file_exists($this->storage)) ? URL::to($this->storage) : URL::to("storage/images/no-image.jpg")
        ];
    }
}
