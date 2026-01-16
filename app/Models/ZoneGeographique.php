<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ZoneGeographique extends Model
{
    protected $table = 'zones_geographiques';
    protected $primaryKey = 'id_zone';
    public $timestamps = false;
    protected $fillable = [
        'nom',
        'nom_zone',
        'description',
        'ville',
        'quartier',
        'code_postal',
        'latitude',
        'latitude_centre',
        'longitude',
        'longitude_centre',
        'rayon_km',
        'population_estimee',
        'actif'
    ];

    public function vendeurs()
    {
        return $this->hasMany(Vendeur::class, 'id_zone', 'id_zone');
    }

    /**
     * Scope pour trouver les zones à proximité d'une localisation.
     * Utilise la formule Haversine pour calculer la distance.
     */
    public function scopeNearby($query, $latitude, $longitude)
    {
        return $query
            ->selectRaw("
                *,
                (
                    6371 * acos(
                        cos(radians(?)) *
                        cos(radians(COALESCE(latitude, latitude_centre))) *
                        cos(radians(COALESCE(longitude, longitude_centre)) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(COALESCE(latitude, latitude_centre)))
                    )
                ) AS distance
            ", [$latitude, $longitude, $latitude])
            ->where('actif', true)
            ->havingRaw('distance <= rayon_km')
            ->orderBy('distance', 'asc');
    }

    /**
     * Trouve la zone la plus proche pour une localisation donnée.
     */
    public function scopeClosestZone($query, $latitude, $longitude)
    {
        return $query->nearby($latitude, $longitude)->first();
    }
}
