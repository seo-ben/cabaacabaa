<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendeur extends Model
{
    protected $table = 'vendeurs';
    protected $primaryKey = 'id_vendeur';
    public $timestamps = false;
    protected $fillable = [
        'id_user',
        'id_zone',
        'nom_commercial',
        'slug',
        'description',
        'type_vendeur',
        'adresse_complete',
        'latitude',
        'longitude',
        'horaires_ouverture',
        'telephone_commercial',
        'registre_commerce',
        'document_identite',
        'justificatif_domicile',
        'statut_verification',
        'date_verification',
        'note_moyenne',
        'nombre_avis',
        'nombre_commandes_total',
        'nombre_commandes_mois',
        'image_principale',
        'images_galerie',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'tiktok_url',
        'whatsapp_number',
        'wallet_balance',
        'id_category_vendeur',
        'actif'
    ];

    protected $casts = [
        'horaires_ouverture' => 'array',
        'images_galerie' => 'array',
        'date_inscription' => 'datetime',
        'date_verification' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendeur) {
            if (empty($vendeur->slug)) {
                $vendeur->slug = \Str::slug($vendeur->nom_commercial);
            }
        });

        static::updating(function ($vendeur) {
            if ($vendeur->isDirty('nom_commercial') && empty($vendeur->slug)) {
                $vendeur->slug = \Str::slug($vendeur->nom_commercial);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function zone()
    {
        return $this->belongsTo(ZoneGeographique::class, 'id_zone', 'id_zone');
    }

    public function plats()
    {
        return $this->hasMany(Plat::class, 'id_vendeur', 'id_vendeur');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'id_vendeur', 'id_vendeur');
    }

    public function contacts()
    {
        return $this->hasMany(VendeurContact::class, 'id_vendeur', 'id_vendeur');
    }

    public function horaires()
    {
        return $this->hasMany(VendeurHoraire::class, 'id_vendeur', 'id_vendeur');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'id_vendeur', 'id_vendeur');
    }

    public function medias()
    {
        return $this->hasMany(Media::class, 'id_vendeur', 'id_vendeur');
    }

    public function avisEvaluations()
    {
        return $this->hasMany(AvisEvaluation::class, 'id_vendeur', 'id_vendeur');
    }

    public function categories()
    {
        return $this->belongsToMany(CategoryPlat::class, 'vendeur_categories', 'id_vendeur', 'id_categorie');
    }

    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class, 'id_vendeur', 'id_vendeur');
    }

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'id_category_vendeur', 'id_category_vendeur');
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'id_vendeur', 'id_vendeur');
    }

    public function staff()
    {
        return $this->hasMany(VendorStaff::class, 'id_vendeur', 'id_vendeur');
    }
}
