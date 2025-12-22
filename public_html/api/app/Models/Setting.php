<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'setting_type_id', 'field_type'];

    public function setting_types()
    {
        return $this->hasMany(SettingType::class);
    }
}
