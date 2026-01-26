<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRequest extends Model
{
    protected $table = 'delivery_requests';
    protected $fillable = ['id_vendeur', 'message', 'status'];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }

    public function applications()
    {
        return $this->hasMany(DeliveryApplication::class, 'id_delivery_request', 'id');
    }
}
