<?php

namespace App\Http\Controllers;

use App\Models\Vendeur;
use Illuminate\Http\Request;

class VendorMapController extends Controller
{
    /**
     * Afficher la carte des vendeurs proches
     */
    public function index()
    {
        return view('vendors.map');
    }

    /**
     * API pour récupérer les vendeurs proches basés sur la géolocalisation
     */
    public function getNearbyVendors(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:0.1|max:50', // En kilomètres
        ]);

        $userLat = $request->lat;
        $userLng = $request->lng;
        $radius = $request->radius ?? 5; // Rayon par défaut: 5 km (converti en 200m si demandé)

        // Convertir 200m en km si c'est le rayon souhaité
        if ($request->has('radius_meters') && $request->radius_meters == 200) {
            $radius = 0.2; // 200 mètres = 0.2 km
        }

        // Récupérer tous les vendeurs actifs avec coordonnées et les 6 premiers produits
        $vendeurs = Vendeur::where('actif', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['category', 'categories', 'plats' => function($query) {
                $query->where('disponible', true)->take(6);
            }])
            ->get();

        // Calculer la distance et filtrer
        $nearbyVendors = $vendeurs->map(function ($vendeur) use ($userLat, $userLng) {
            $distance = $this->calculateDistance(
                $userLat,
                $userLng,
                $vendeur->latitude,
                $vendeur->longitude
            );

            $vendeur->distance = round($distance, 2);
            return $vendeur;
        })
        ->filter(function ($vendeur) use ($radius) {
            return $vendeur->distance <= $radius;
        })
        ->sortBy('distance')
        ->values()
        ->map(function ($vendeur) {
            // Récupérer les spécialités (ce qu'ils vendent)
            $specialties = $vendeur->categories->pluck('nom_categorie')->toArray();
            
            return [
                'id' => $vendeur->id_vendeur,
                'nom' => $vendeur->nom_commercial,
                'slug' => $vendeur->slug,
                'description' => $vendeur->description,
                'latitude' => (float) $vendeur->latitude,
                'longitude' => (float) $vendeur->longitude,
                'distance' => $vendeur->distance,
                'distance_text' => $vendeur->distance < 1 
                    ? round($vendeur->distance * 1000) . ' m' 
                    : $vendeur->distance . ' km',
                'image' => $vendeur->image_principale 
                    ? asset('storage/' . dirname($vendeur->image_principale) . '/thumbnails/' . basename($vendeur->image_principale))
                    : asset('images/default-vendor.jpg'),
                'image_full' => $vendeur->image_principale 
                    ? asset('storage/' . $vendeur->image_principale) 
                    : asset('images/default-vendor.jpg'),
                'note_moyenne' => $vendeur->note_moyenne ?? 0,
                'nombre_avis' => $vendeur->nombre_avis ?? 0,
                'category' => $vendeur->category ? $vendeur->category->nom : 'Restaurant',
                'products' => $vendeur->plats->pluck('nom_plat')->toArray(), // Les 6 premiers produits
                'specialties' => $specialties, 
                'specialties_text' => !empty($specialties) ? implode(', ', $specialties) : 'Divers',
                'adresse' => $vendeur->adresse_complete,
                'url' => route('vendor.show', ['id' => $vendeur->id_vendeur, 'slug' => $vendeur->slug]),
                'actif' => $vendeur->actif,
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $nearbyVendors->count(),
            'radius' => $radius,
            'radius_text' => $radius < 1 ? ($radius * 1000) . ' m' : $radius . ' km',
            'user_location' => [
                'lat' => (float) $userLat,
                'lng' => (float) $userLng,
            ],
            'vendors' => $nearbyVendors,
        ]);
    }

    /**
     * Calculer la distance entre deux points GPS (formule Haversine)
     * 
     * @param float $lat1 Latitude point 1
     * @param float $lon1 Longitude point 1
     * @param float $lat2 Latitude point 2
     * @param float $lon2 Longitude point 2
     * @return float Distance en kilomètres
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Rayon de la Terre en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}
