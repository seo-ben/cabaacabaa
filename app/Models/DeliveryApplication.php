<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryApplication extends Model
{
    protected $table = 'delivery_applications';
    protected $fillable = ['id_delivery_request', 'id_user', 'status', 'notes'];

    public function request()
    {
        return $this->belongsTo(DeliveryRequest::class, 'id_delivery_request', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
