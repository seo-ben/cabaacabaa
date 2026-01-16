<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiseEnAvant extends Model
{
    protected $table = 'mise_en_avant';
    protected $primaryKey = 'id_mise_en_avant';
    public $timestamps = false;
    protected $fillable = ['id_vendeur','type_promotion','priorite','date_debut','date_fin','id_zone','zones_ciblees','actif','date_creation','description'];

    protected $casts = [
        'zones_ciblees' => 'array',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'date_creation' => 'datetime',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }

    public function zone()
    {
        return $this->belongsTo(ZoneGeographique::class, 'id_zone', 'id_zone');
    }
}
