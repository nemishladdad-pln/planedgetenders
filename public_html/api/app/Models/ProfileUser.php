<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ProfileUser extends Model
{
    use Notifiable;

    protected $fillable = [
        'user_id', 'mobile_no', 'avatar', 'google2fa_enabled',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
