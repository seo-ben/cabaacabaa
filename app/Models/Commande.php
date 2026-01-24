<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commandes';
    protected $primaryKey = 'id_commande';
    public $timestamps = false;
    protected $fillable = ['numero_commande', 'id_client', 'id_vendeur', 'statut', 'type_recuperation', 'mode_paiement_prevu', 'paiement_effectue', 'montant_plats', 'frais_service', 'montant_total', 'date_commande', 'heure_recuperation_souhaitee', 'heure_preparation_debut', 'heure_prete', 'heure_recuperation_effective', 'instructions_speciales', 'date_annulation', 'raison_annulation'];

    protected $casts = [
        'date_commande' => 'datetime',
        'heure_recuperation_souhaitee' => 'datetime',
        'heure_preparation_debut' => 'datetime',
        'heure_prete' => 'datetime',
        'heure_recuperation_effective' => 'datetime',
        'date_annulation' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'id_client', 'id_user');
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }

    public function lignes()
    {
        return $this->hasMany(LigneCommande::class, 'id_commande', 'id_commande');
    }

    public function avis()
    {
        return $this->hasOne(AvisEvaluation::class, 'id_commande', 'id_commande');
    }

    public function messages()
    {
        return $this->hasMany(OrderMessage::class, 'id_commande', 'id_commande');
    }
}
