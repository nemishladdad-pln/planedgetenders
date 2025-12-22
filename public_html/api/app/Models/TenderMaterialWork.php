<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderMaterialWork extends Model
{
    use HasFactory;

    protected $fillable = ['tender_id', 'material_work_type_id', 'work', 'unit_id', 'quantity', 'rate'];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function material_work_type()
    {
        return $this->belongsTo(MaterialWorkType::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
