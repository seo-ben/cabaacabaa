<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    protected $table = 'lignes_commande';
    protected $primaryKey = 'id_ligne';
    public $timestamps = false;
    protected $fillable = ['id_commande', 'id_plat', 'nom_plat_snapshot', 'quantite', 'prix_unitaire', 'sous_total', 'notes', 'options'];

    protected $casts = [
        'options' => 'array',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande', 'id_commande');
    }

    public function plat()
    {
        return $this->belongsTo(Plat::class, 'id_plat', 'id_plat');
    }
}
