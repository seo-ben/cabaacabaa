<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvisEvaluation extends Model
{
    protected $table = 'avis_evaluations';
    protected $primaryKey = 'id_avis';
    public $timestamps = false;
    protected $fillable = ['id_client','id_vendeur','id_commande','note','commentaire','note_qualite','note_rapidite','note_rapport_qualite_prix','statut_avis','date_publication','date_modification','reponse_vendeur','date_reponse'];

    protected $casts = [
        'date_publication' => 'datetime',
        'date_modification' => 'datetime',
        'date_reponse' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'id_client', 'id_user');
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
