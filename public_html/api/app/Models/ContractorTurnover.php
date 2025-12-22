<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorTurnover extends Model
{
    use HasFactory;

    protected $fillable = ['contractor_id', 'year', 'turnover', 'certificate_storage'];

    public function contractors(): HasMany
    {
        return $this->hasMany(Contractor::class);
    }
}
