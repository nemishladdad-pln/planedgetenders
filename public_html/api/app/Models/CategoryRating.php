<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryRating extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(CategoryRating::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(CategoryRating::class, 'parent_id');
    }

    public function contractor_category_ratings(): HasMany
    {
        return $this->hasMany(ContractorCategoryRating::class);
    }

    public static function tree()
    {
        $allCategories = CategoryRating::get();
        $rootCategories = $allCategories->whereNull('parent_id');

        foreach ($rootCategories as $rootCategory) {
            $rootCategory->children = $allCategories->where('parent_id', $rootCategory->id)->values();

            foreach ($rootCategory->children as $child) {
                $child->children = $allCategories->where('parent_id', $child->id)->values();
            }
        }

        return $rootCategories;
    }
}
