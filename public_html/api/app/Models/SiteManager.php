<?php

namespace App\Models;

use App\Models\MaterialWorkType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name_of_site',
        'name_of_organization',
        'name_of_site_manager',
        'company_name',
        'name_of_client',
        'period_of_payment',
        'labour_with_material',
        'material_work_type_id',

    ];

    public function material_work_type(): BelongsTo
    {
        return $this->belongsTo(MaterialWorkType::class);
    }
}
