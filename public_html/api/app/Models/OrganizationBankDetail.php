<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'vuid',
        'favouring_name',
        'account_no',
        'bank_name',
        'branch_name',
        'ifsc_code',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

}
