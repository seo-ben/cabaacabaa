<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMessage extends Model
{
    protected $fillable = [
        'id_commande',
        'id_user',
        'message',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande', 'id_commande');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
