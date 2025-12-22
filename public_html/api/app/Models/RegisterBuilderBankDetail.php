<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegisterBuilderBankDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'register_id',
        'rbid',
        'favouring_name',
        'account_no',
        'bank_name',
        'branch_name',
        'ifsc_code',
    ];
    public function registerbuilder(): BelongsTo
    {
        return $this->belongsTo(RegisterBuilder::class, 'register_id');
    }
}
