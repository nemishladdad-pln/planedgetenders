<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bUID',
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
        'pan_no',
        'pan_file',
        'gstin',
        'gstin_file',
        'lbt_num_firm',
        'service_tax_num_firm',
        //'material_work_type_id',
        //'with_labour_material',
        'checked_by',
        'verified_by',
        'avp_by ',
        'director',
        'turnover',
        'admin_comment',
        'last_login',
        'login_attempts',
        'bank_details',
        'director_proprietor',
        'completed_works',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function organization_documents(): HasMany
    {
        return $this->hasMany(OrganizationDocument::class, 'organization_id');
    }

    public function organization_bank_details(): HasMany
    {
        return $this->hasMany(OrganizationBankDetail::class);
    }

    public function organization_directors(): HasMany
    {
        return $this->hasMany(OrganizationDirector::class, 'organization_id');
    }

    public function organization_works(): HasMany
    {
        return $this->hasMany(OrganizationWork::class, 'organization_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class);
    }
}
