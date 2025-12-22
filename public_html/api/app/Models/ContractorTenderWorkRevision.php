<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorTenderWorkRevision extends Model
{
    use HasFactory;

    protected $fillable = ['contractor_tender_id', 'material_work_type_id', 'total_amount', 'revision'];

    public function contractor_tenders(): HasMany
    {
        return $this->hasMany(ContractorTender::class);
    }

    public function material_work_type(): BelongsTo
    {
        return $this->belongsTo(MaterialWorkType::class);
    }
}
