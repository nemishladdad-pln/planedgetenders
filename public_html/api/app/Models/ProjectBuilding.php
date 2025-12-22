<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectBuilding extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name', 'floors', 'is_completed', 'percentage_completed', 'status', 'active'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tenders(): BelongsToMany
    {
        return $this->belongsToMany(Tender::class)->withTimestamps();
    }
}
