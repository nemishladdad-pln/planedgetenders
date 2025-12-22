<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'title_description',
        'scope_work',
        'location',
        'tendered_cost',
        'actual_cost',
        'stage_work',
        'award_date',
        'planned_completion_date',
        'actual_completion_date',
        'client_contact_person_name',
        'client_contact_person_address',
        'architects_name',
        'other_consultants_name',
        'responsible_staff',
        'completed',
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }
}
