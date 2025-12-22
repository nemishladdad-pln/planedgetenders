<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorDirectorTechnicalStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'type',
        'name',
        'qualification',
        'experience',
        'working_with_company_since',
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }
}
