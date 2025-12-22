<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_manager_id',
        'contractor_id',
        'material_work_type_id',
        'contractor_tender_id',
        'evaluation_data',
        'rating_calculated',
        'project_id',
        'quarter_id',
    ];

    public function site_manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'site_manager_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    public function material_work_type(): BelongsTo
    {
        return $this->belongsTo(MaterialWorkType::class);
    }

    public function contractor_tender(): BelongsTo
    {
        return $this->belongsTo(ContractorTender::class);
    }
}
