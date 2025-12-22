<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'favouring_name',
        'account_no',
        'bank_name',
        'branch_name',
        'ifsc_code',
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }
}
