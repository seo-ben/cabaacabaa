<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionFinanciere extends Model
{
    protected $table = 'transactions_financieres';
    protected $primaryKey = 'id_transaction';
    public $timestamps = false;
    protected $fillable = ['id_commande','id_vendeur','id_abonnement','type_transaction','montant','devise','statut','date_transaction','reference_paiement','notes'];

    protected $casts = [
        'date_transaction' => 'datetime',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande', 'id_commande');
    }

    public function abonnement()
    {
        return $this->belongsTo(AbonnementTarification::class, 'id_abonnement', 'id_abonnement');
    }
}
