<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cUID',
        'name',
        'company_name',
        'est_year',
        'director_name',
        'address',
        'director_dob',
        'director_address',
        'director_avatar',
        'email',
        'mobile_no',
        'company_landline_no',
        'material_work_type_id',
        'with_labour_material',
        'bank_details',
        'director_proprietor',
        'completed_works',
        'checked_by',
        'verified_by',
        'avp_by ',
        'director',
        'turnover',
        'admin_comment',
        'last_login',
        'login_attempts',
        'grade_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function contractor_bank_details(): HasMany
    {
        return $this->hasMany(ContractorBankDetail::class, 'contractor_id');
    }

    public function contractor_contacts(): HasMany
    {
        return $this->hasMany(ContractorContact::class, 'contractor_id');
    }

    public function contractor_director_technical_staffs(): HasMany
    {
        return $this->hasMany(ContractorDirectorTechnicalStaff::class);
    }

    public function contractor_documents(): HasMany
    {
        return $this->hasMany(ContractorDocument::class, 'contractor_id');
    }

    public function contractor_turnovers(): HasMany
    {
        return $this->hasMany(ContractorTurnover::class, 'contractor_id');
    }

    public function contractor_equipments(): HasMany
    {
        return $this->hasMany(ContractorEquipment::class, 'contractor_id');
    }

    public function contractor_quality_certificates(): HasMany
    {
        return $this->hasMany(ContractorQualityCertificate::class, 'contractor_id');
    }

    public function contractor_works(): HasMany
    {
        return $this->hasMany(ContractorWork::class);
    }

    public function material_work_type(): BelongsTo
    {
        return $this->belongsTo(MaterialWorkType::class, 'material_work_type_id');
    }

    public function material(): BelongsToMany
    {
        return $this->belongsToMany(Material::class);
    }

    public function contractor_tenders(): HasMany
    {
        return $this->hasMany(ContractorTender::class);
    }

    public function checked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payments::class, 'model_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
}
