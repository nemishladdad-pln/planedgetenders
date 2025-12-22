<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model_id',
        'model',
        'status',
        'transaction_id',
        'amount',
        'currency',
        'order_id',
        'method',
        'amount_refunded',
        'bank',
        'wallet',
        'entity',
        'refund_Date',
        'bank_transaction_id',
        'refund_id'
    ];
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'model_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
