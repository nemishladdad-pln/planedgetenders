<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'billing_name',
        'pUID',
        'organization_id',
        'location',
        'project_type_id',
        'total_project_area',
        'site_project_manager_id',
        'general_manager_id',
        'created_by',
        'updated_by',
        'start_date',
        'completion_date',
        'is_active',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function project_type(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function project_buildings(): HasMany
    {
        return $this->hasMany(ProjectBuilding::class);
    }

    public function site_project_manager()
    {
        return $this->belongsTo(User::class, 'site_project_manager_id');
    }

    public function general_manager()
    {
        return $this->belongsTo(User::class, 'general_manager_id');
    }

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class);
    }

    public function project_schedules(): HasMany
    {
        return $this->hasMany(ProjectSchedule::class);
    }

    public function contractor_category_ratings(): HasMany
    {
        return $this->hasMany(ContractorCategoryRating::class);
    }
}
