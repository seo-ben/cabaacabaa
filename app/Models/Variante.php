<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{
    protected $table = 'variantes';
    protected $primaryKey = 'id_variante';

    protected $fillable = [
        'id_groupe',
        'nom',
        'prix_supplement'
    ];

    public function groupe()
    {
        return $this->belongsTo(GroupeVariante::class, 'id_groupe', 'id_groupe');
    }
}
