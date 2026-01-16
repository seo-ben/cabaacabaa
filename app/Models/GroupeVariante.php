<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupeVariante extends Model
{
    protected $table = 'groupe_variantes';
    protected $primaryKey = 'id_groupe';

    protected $fillable = [
        'id_plat',
        'nom',
        'obligatoire',
        'choix_multiple',
        'min_choix',
        'max_choix'
    ];

    protected $casts = [
        'obligatoire' => 'boolean',
        'choix_multiple' => 'boolean',
    ];

    public function plat()
    {
        return $this->belongsTo(Plat::class, 'id_plat', 'id_plat');
    }

    public function variantes()
    {
        return $this->hasMany(Variante::class, 'id_groupe', 'id_groupe');
    }
}
