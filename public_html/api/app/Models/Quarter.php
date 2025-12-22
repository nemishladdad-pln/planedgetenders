<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quarter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_month', 'end_month'];

    public function contractor_category_ratings(): HasMany
    {
        return $this->hasMany(ContractorCategoryRating::class);
    }
}
