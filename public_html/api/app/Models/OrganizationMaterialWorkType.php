<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrganizationMaterialWorkType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code'];


    public function registerorganization(): BelongsToMany
    {
        return $this->belongsToMany(RegisterOrganization::class, 'register_id');
    }
}
