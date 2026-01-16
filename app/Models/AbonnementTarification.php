<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbonnementTarification extends Model
{
    protected $table = 'abonnements_tarification';
    protected $primaryKey = 'id_abonnement';
    public $timestamps = false;
    protected $fillable = ['id_vendeur','type_tarification','valeur_tarif','pourcentage','date_debut','date_fin','duree_jours','statut','periode_essai','montant_a_payer','montant_paye','date_dernier_paiement','date_creation','notes'];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'date_dernier_paiement' => 'datetime',
        'date_creation' => 'datetime',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
