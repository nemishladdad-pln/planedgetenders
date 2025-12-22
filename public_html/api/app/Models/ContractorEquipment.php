<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorEquipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'name_description',
        'make',
        'mfg_year',
        'year_purchase',
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }
}
