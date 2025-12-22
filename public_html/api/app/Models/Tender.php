<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tender extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'status',
        'project_id',
        'tender_reference_number',
        'tender_uid',
        'tender_type_id',
        'form_contract_id',
        'tender_category_id',
        'general_technical_evaluation',
        'item_wise_evaluation_allowed',
        'allow_two_stage_bidding',
        'tender_fee',
        'is_paid',
        'emd_amount',
        'emd_exemption_allowed',
        'emd_fee_type_id',
        'emd_percentage',
        'material_work_type_id',
        'work_title',
        'work_description',
        'pre_qualification',
        'remarks',
        'tender_value',
        'location',
        'pin_code',
        'bid_validity_days',
        'period_of_works',
        'pre_bid_meeting_place_id',
        'pre_bid_meeting_date',
        'pre_bid_opening_date',
        'pre_bid_opening_place',
        'published_date',
        'bid_opening_date',
        'document_download_sale_start_date',
        'document_download_sale_end_date',
        'bid_submission_start_date',
        'bid_submission_end_date',
        'clarification_start_date',
        'clarification_end_date',
        'authorized_name',
        'authorized_address',
        'status',
        'is_verified',
        'created_by',
        'updated_by',
        'approved_by',
        'awarded_to',
        'awarded_on',
        'tender_documents',
        'tender_material_works',
        'is_under_budget',
        'tender_password'
    ];
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function project_building()
    {
        return $this->belongsToMany(ProjectBuilding::class)->withTimestamps();
    }

    public function tender_type(): BelongsTo
    {
        return $this->belongsTo(TenderType::class);
    }

    public function form_contract(): BelongsTo
    {
        return $this->belongsTo(FormContract::class);
    }

    public function tender_category(): BelongsTo
    {
        return $this->belongsTo(TenderCategory::class);
    }

    public function emd_fee_type(): BelongsTo
    {
        return $this->belongsTo(EmdFeeType::class);
    }

    public function material_work_type(): BelongsTo
    {
        return $this->belongsTo(MaterialWorkType::class);
    }

    public function pre_bid_meeting_place(): BelongsTo
    {
        return $this->belongsTo(PreBidMeetingPlace::class);
    }

    public function tender_documents(): HasMany
    {
        return $this->hasMany(TenderDocument::class);
    }

    public function tender_material_works(): HasMany
    {
        return $this->hasMany(TenderMaterialWork::class);
    }

    public function created_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contractor_tenders(): HasMany
    {
        return $this->hasMany(ContractorTender::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
}
