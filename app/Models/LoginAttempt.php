<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'id_user',
        'email',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
        'location',
        'attempted_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
