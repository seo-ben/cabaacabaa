<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    protected $primaryKey = 'id_payout';
    protected $fillable = [
        'id_vendeur',
        'montant',
        'methode_paiement',
        'informations_paiement',
        'statut',
        'notes_admin'
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
