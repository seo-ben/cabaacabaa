<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorStaff extends Model
{
    protected $table = 'vendor_staff';
    protected $fillable = ['id_vendeur', 'id_user', 'role_name', 'permissions', 'access_token'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
