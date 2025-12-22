<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorTenderWorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tender_id',
        'contractor_tender_id',
        'organization_id',
        'contractor_id',
        'gm_id',
        'general_terms_conditions',
        'more_detail',
        'is_organization_approved',
        'is_contractor_approved',
        'organization_comments',
        'contractor_comments',
        'is_awarded',
        'storage',
        'tender_cost',
        'period_of_payment_id',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function contractor_tender(): BelongsTo
    {
        return $this->belongsTo(ContractorTender::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }


    public function contractor() :BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function general_manager()
    {
        return $this->belongsTo(User::class, 'gm_id');
    }

    public function period_of_payment(): BelongsTo
    {
        return $this->belongsTo(PeriodOfPayment::class);
    }
}
