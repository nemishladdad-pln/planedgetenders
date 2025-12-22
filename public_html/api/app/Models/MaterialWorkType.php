<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MaterialWorkType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'grade_A', 'grade_B', 'grade_C'];


    public function contractors(): BelongsToMany
    {
        return $this->belongsToMany(Contractor::class);
    }
}
