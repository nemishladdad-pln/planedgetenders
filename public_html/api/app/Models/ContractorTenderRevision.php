<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorTenderRevision extends Model
{
    use HasFactory;

    protected $fillable = ['contractor_tender_id', 'revision', 'percentage_difference'];

    public function contractor_tender(): BelongsTo
    {
        return $this->belongsTo(ContractorTender::class);
    }

}
