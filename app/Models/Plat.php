<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plat extends Model
{
    protected $table = 'plats';
    protected $primaryKey = 'id_plat';
    public $timestamps = false;
    protected $fillable = ['id_vendeur', 'id_categorie', 'nom_plat', 'description', 'prix', 'devise', 'disponible', 'stock_limite', 'quantite_disponible', 'temps_preparation_min', 'image_principale', 'images_supplementaires', 'nombre_commandes', 'nombre_vues', 'en_promotion', 'prix_promotion', 'date_debut_promotion', 'date_fin_promotion', 'date_creation', 'date_modification'];

    protected $casts = [
        'images_supplementaires' => 'array',
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }

    public function categorie()
    {
        return $this->belongsTo(CategoryPlat::class, 'id_categorie', 'id_categorie');
    }

    public function medias()
    {
        return $this->hasMany(Media::class, 'id_plat', 'id_plat');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'plat_tag', 'id_plat', 'id_tag');
    }

    public function groupesVariantes()
    {
        return $this->hasMany(GroupeVariante::class, 'id_plat', 'id_plat');
    }
}
