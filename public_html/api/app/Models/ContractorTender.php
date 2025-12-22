<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorTender extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'tender_id',
        'is_awarded',
        'status',
        'is_paid',
        'approved_by',
        'created_by',
        'updated_by',
    ];
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function contractor_tender_material_works(): HasMany
    {
        return $this->hasMany(ContractorTenderMaterialWork::class);
    }

    public function contractor_tender_work_revisions(): HasMany
    {
        return $this->hasMany(ContractorTenderWorkRevision::class);
    }

    public function contractor_tender_revisions(): HasMany
    {
        return $this->hasMany(ContractorTenderRevision::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function contractor_tender_work_orders(): HasMany
    {
        return $this->hasMany(ContractorTenderWorkOrder::class);
    }

    public function contractor_category_ratings(): HasMany
    {
        return $this->hasMany(ContractorCategoryRating::class);
    }
}
