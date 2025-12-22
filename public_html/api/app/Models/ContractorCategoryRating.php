<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorCategoryRating extends Model
{
    use HasFactory;

    protected $fillable = ['contractor_id', 'material_work_type_id', 'site_manager_id', 'category_rating_id', 'category_rating_parent_id', 'quarter_id', 'rating', 'year', 'contractor_tender_id', 'project_id'];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function material_work_type(): BelongsTo
    {
        return $this->belongsTo(MaterialWorkType::class);
    }
    public function site_manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function category_rating(): BelongsTo
    {
        return $this->belongsTo(CategoryRating::class);
    }
    public function category_rating_parent(): BelongsTo
    {
        return $this->belongsTo(CategoryRating::class, 'parent_id');
    }
    public function quarter(): BelongsTo
    {
        return $this->belongsTo(Quarter::class);
    }

    public function contractor_tender(): BelongsTo
    {
        return $this->belongsTo(ContractorTender::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
