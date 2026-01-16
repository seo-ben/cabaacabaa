<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendeurHoraire extends Model
{
    protected $table = 'vendeur_horaires';
    protected $primaryKey = 'id';
    protected $fillable = ['id_vendeur','jour_semaine','heure_ouverture','heure_fermeture','ferme','exceptions','ordre'];

    protected $casts = [
        'jour_semaine' => 'integer',
        'ferme' => 'boolean',
        'exceptions' => 'array',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
