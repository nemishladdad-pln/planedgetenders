<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegisterBuilder extends Model
{
    use HasFactory;
    protected $fillable = [
        'rbid',
        'name',
        'company_name',
        'est_year',
        'director_name',
        'address',
        'director_dob',
        'director_address',
        'director_avatar',
        'email',
        'company_mobile_no',
        'company_landline_no',
        'pan_no',
        'gstin',
        'lbt_num_firm',
        'service_tax_num_firm',
        'material_work_type_id',
        'with_labour_material',
        'checked_by',
        'verified_by',
        'avp_by ',
        'director',
        'turnover',
        'admin_comment',
        'last_login',
        'login_attempts'
    ];
    public function register_builder_bank_details(): HasMany
    {
        return $this->hasMany(RegisterBuilderBankDetail::class, 'register_id');
    }
}
