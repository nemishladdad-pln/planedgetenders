<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'number_buildings',
        'number_floors',
        'total_area',
        'location',
        'planned_completion_date',
        'actual_completion_date',
        'type_construction',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
