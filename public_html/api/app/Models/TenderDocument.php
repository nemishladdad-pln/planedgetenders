<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderDocument extends Model
{
    use HasFactory;

    protected $fillable = ['tender_id', 'tender_document_type_id', 'document_format_id', 'storage', 'description'];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function tender_document_type(): BelongsTo
    {
        return $this->belongsTo(TenderDocumentType::class);
    }

    public function document_type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function document_format(): BelongsTo
    {
        return $this->belongsTo(DocumentFormat::class);
    }
}
