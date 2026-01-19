<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorCategory extends Model
{
    protected $primaryKey = 'id_category_vendeur';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
    ];

    public function vendeurs()
    {
        return $this->hasMany(Vendeur::class, 'id_category_vendeur', 'id_category_vendeur');
    }
}
