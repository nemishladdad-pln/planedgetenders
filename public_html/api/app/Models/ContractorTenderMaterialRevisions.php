<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorTenderMaterialRevisions extends Model
{
    use HasFactory;

    protected $fillable = ['contractor_tender_id', 'material_work_type_id', 'total_amount', 'revision', 'percentage_difference'];

    public function contractor_tender(): BelongsTo
    {
        return $this->belongsTo(ContractorTender::class);
    }

    public function material_work_type(): BelongsTo
    {
        return $this->belongsTo(MaterialWorkType::class);
    }
}
