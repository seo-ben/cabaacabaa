<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZoneGeographique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZoneController extends Controller
{
    /**
     * Liste toutes les zones géographiques.
     */
    public function index()
    {
        $zones = ZoneGeographique::paginate(20);
        return view('admin.zones.index', compact('zones'));
    }

    /**
     * Affiche le formulaire de création d'une zone.
     */
    public function create()
    {
        return view('admin.zones.create');
    }

    /**
     * Crée une nouvelle zone géographique.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:zones_geographiques,nom',
            'description' => 'nullable|string|max:1000',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'rayon_km' => 'required|numeric|min:0.1|max:100',
            'actif' => 'required|boolean',
        ]);

        // Si latitude/longitude ne sont pas fournis, essayer le géocodage automatique
        if (empty($validated['latitude']) || empty($validated['longitude'])) {
            $geoData = $this->geocodeAddress($validated['ville'], $validated['code_postal'] ?? '');

            if ($geoData !== false) {
                // Géocodage réussi
                $validated['latitude'] = $geoData['latitude'];
                $validated['longitude'] = $geoData['longitude'];
                $message = 'Zone créée avec succès. Coordonnées trouvées: ' . number_format($validated['latitude'], 4) . ', ' . number_format($validated['longitude'], 4);
            } else {
                // Géocodage échoué - on crée la zone QUAND MÊME sans coordonnées
                $validated['latitude'] = null;
                $validated['longitude'] = null;
                $message = 'Zone créée sans coordonnées GPS. Veuillez les spécifier manuellement via le bouton "Compléter localisation".';
            }
        } else {
            $message = 'Zone créée avec succès.';
        }

        ZoneGeographique::create($validated);

        return redirect()->route('admin.zones.index')
                       ->with('success', $message);
    }

    /**
     * Affiche le formulaire d'édition d'une zone.
     */
    public function edit($id)
    {
        $zone = ZoneGeographique::findOrFail($id);
        return view('admin.zones.edit', compact('zone'));
    }

    /**
     * Met à jour une zone géographique.
     */
    public function update(Request $request, $id)
    {
        $zone = ZoneGeographique::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:zones_geographiques,nom,' . $id . ',id_zone',
            'description' => 'nullable|string|max:1000',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'rayon_km' => 'required|numeric|min:0.1|max:100',
            'actif' => 'required|boolean',
        ]);

        // Si l'adresse a changé et lat/lon ne sont pas fournis, essayer le géocodage
        $addressChanged = ($zone->ville !== $validated['ville'] || $zone->code_postal !== $validated['code_postal']);

        if ($addressChanged && (empty($validated['latitude']) || empty($validated['longitude']))) {
            $geoData = $this->geocodeAddress($validated['ville'], $validated['code_postal'] ?? '');

            if ($geoData !== false) {
                // Géocodage réussi
                $validated['latitude'] = $geoData['latitude'];
                $validated['longitude'] = $geoData['longitude'];
                $message = 'Zone mise à jour. Coordonnées trouvées: ' . number_format($validated['latitude'], 4) . ', ' . number_format($validated['longitude'], 4);
            } else {
                // Géocodage échoué - on met à jour quand même
                $validated['latitude'] = null;
                $validated['longitude'] = null;
                $message = 'Zone mise à jour sans coordonnées. Veuillez les spécifier manuellement.';
            }
        } elseif (!$addressChanged) {
            // Si l'adresse n'a pas changé, garder les coordonnées existantes si pas modifiées
            if (empty($validated['latitude'])) {
                $validated['latitude'] = $zone->latitude;
            }
            if (empty($validated['longitude'])) {
                $validated['longitude'] = $zone->longitude;
            }
            $message = 'Zone mise à jour avec succès.';
        } else {
            $message = 'Zone mise à jour avec succès.';
        }

        $zone->update($validated);

        return redirect()->route('admin.zones.index')
                       ->with('success', $message);
    }

    /**
     * Supprime une zone géographique.
     */
    public function destroy($id)
    {
        $zone = ZoneGeographique::findOrFail($id);

        // Vérifier si la zone est utilisée par des vendeurs
        if ($zone->vendeurs()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette zone. Elle est utilisée par ' . $zone->vendeurs()->count() . ' vendeur(s).');
        }

        $zone->delete();

        return redirect()->route('admin.zones.index')
                       ->with('success', 'Zone supprimée avec succès.');
    }

    /**
     * Détecte la localisation de l'utilisateur et retourne les zones correspondantes.
     */
    public function detectLocation(Request $request)
    {
        try {
            // Récupérer l'adresse IP du client
            $clientIp = $this->getClientIp($request);

            // Utiliser un service de géolocalisation
            $response = file_get_contents("http://ip-api.com/json/{$clientIp}?fields=lat,lon,city,zip");

            if ($response === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de détecter votre localisation.',
                ], 400);
            }

            $data = json_decode($response, true);

            if (empty($data['lat']) || empty($data['lon'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Localisation non disponible pour cette adresse IP.',
                ], 400);
            }

            $userLat = $data['lat'];
            $userLon = $data['lon'];
            $userCity = $data['city'] ?? '';
            $userZip = $data['zip'] ?? '';

            // Trouver les zones qui couvrent cette localisation en utilisant le scope
            $zones = ZoneGeographique::nearby($userLat, $userLon)
                ->get()
                ->values();

            return response()->json([
                'success' => true,
                'location' => [
                    'latitude' => $userLat,
                    'longitude' => $userLon,
                    'city' => $userCity,
                    'zip' => $userZip,
                ],
                'zones' => $zones,
                'closestZone' => $zones->first(),
                'message' => count($zones) > 0
                    ? 'Nous livrons dans votre zone!'
                    : 'Désolé, nous ne livrons pas encore dans votre zone.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la détection de localisation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère l'adresse IP du client.
     */
    private function getClientIp(Request $request)
    {
        // Vérifier les en-têtes de proxy
        if ($request->header('CF-Connecting-IP')) {
            return $request->header('CF-Connecting-IP');
        }

        if ($request->header('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            return trim($ips[0]);
        }

        // Sinon, utiliser l'IP directe
        return $request->ip() ?? '127.0.0.1';
    }

    /**
     * Récupère les zones qui couvrent une adresse donnée.
     */
    public function getCoverageByAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string|min:5',
        ]);

        try {
            // Utiliser Nominatim (OpenStreetMap) pour géocoder l'adresse
            $address = urlencode($request->input('address'));
            $response = file_get_contents("https://nominatim.openstreetmap.org/search?q={$address}&format=json&limit=1");

            if ($response === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de géocoder cette adresse.',
                ], 400);
            }

            $data = json_decode($response, true);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adresse non trouvée.',
                ], 404);
            }

            $lat = $data[0]['lat'];
            $lon = $data[0]['lon'];
            $displayName = $data[0]['display_name'];

            // Trouver les zones qui couvrent cette localisation en utilisant le scope
            $zones = ZoneGeographique::nearby($lat, $lon)
                ->get()
                ->values();

            return response()->json([
                'success' => true,
                'location' => [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'display_name' => $displayName,
                ],
                'zones' => $zones,
                'closestZone' => $zones->first(),
                'message' => count($zones) > 0
                    ? 'Zones trouvées pour cette adresse.'
                    : 'Aucune zone de couverture pour cette adresse.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cherche les coordonnées GPS pour une ville/adresse.
     * API publique pour les formulaires.
     */
    public function searchCoordinates(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2|max:255',
        ]);

        $search = $request->input('search');
        $encodedSearch = urlencode($search);

        try {
            $response = @file_get_contents(
                "https://nominatim.openstreetmap.org/search?q={$encodedSearch}&format=json&limit=5&timeout=10"
            );

            if ($response === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de chercher les coordonnées. Service indisponible.',
                    'results' => [],
                ], 503);
            }

            $data = json_decode($response, true);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun résultat trouvé pour: ' . $search,
                    'results' => [],
                ]);
            }

            // Formatter les résultats
            $results = collect($data)->map(function ($item) {
                return [
                    'lat' => (float) $item['lat'],
                    'lon' => (float) $item['lon'],
                    'display_name' => $item['display_name'] ?? '',
                    'type' => $item['type'] ?? 'location',
                ];
            })->values()->toArray();

            return response()->json([
                'success' => true,
                'message' => count($results) . ' résultat(s) trouvé(s)',
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche: ' . $e->getMessage(),
                'results' => [],
            ], 500);
        }
    }

    /**
     * Récupère les coordonnées GPS pour une adresse donnée.
     * Utilise OpenStreetMap Nominatim avec plusieurs variantes de recherche.
     */
    private function geocodeAddress($ville, $codePostal = '')
    {
        // Construire différentes variantes de recherche
        $searchVariants = [
            $codePostal ? $codePostal . ' ' . $ville : $ville,  // "12345 Lome"
            $ville . ', Togo',                                    // "Lome, Togo"
            $ville,                                               // "Lome"
        ];

        foreach ($searchVariants as $searchTerm) {
            $encodedAddress = urlencode($searchTerm);

            try {
                $response = @file_get_contents(
                    "https://nominatim.openstreetmap.org/search?q={$encodedAddress}&format=json&limit=1&timeout=10"
                );

                if ($response === false) {
                    continue; // Essayer la variante suivante
                }

                $data = json_decode($response, true);

                if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                    return [
                        'latitude' => (float) $data[0]['lat'],
                        'longitude' => (float) $data[0]['lon'],
                    ];
                }
            } catch (\Exception $e) {
                // Log et essayer la variante suivante
                // \Log::warning('Geocoding error for ' . $searchTerm . ': ' . $e->getMessage());
                continue;
            }
        }

        // Aucune variante n'a fonctionné
        // \Log::warning('Geocoding failed for all variants of: ' . $ville);
        return false;
    }
}
