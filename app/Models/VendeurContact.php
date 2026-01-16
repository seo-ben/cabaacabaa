<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendeurContact extends Model
{
    protected $table = 'vendeur_contacts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_vendeur','adresse_ligne_1','adresse_ligne_2','quartier','ville','code_postal','latitude','longitude','telephone_principal','telephone_secondaire','whatsapp','email_contact','lien_google_maps','rayon_service_metre','est_principal'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'est_principal' => 'boolean',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
