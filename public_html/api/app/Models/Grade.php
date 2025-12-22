<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'min', 'max'];

    public function contractors(): HasMany
    {
        return $this->hasMany(Contractor::class);
    }
}
