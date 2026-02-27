<?php

namespace App\Http\Controllers;

use App\Models\Vendeur;
use App\Models\User;
use App\Models\Plat;
use App\Models\CategoryPlat;
use App\Models\ZoneGeographique;
use App\Models\MiseEnAvant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Grand contrôleur pour l'accueil.
     * Gère les vendeurs en vedette, les plats récents, les catégories, et tout le contenu principal.
     */
    public function index()
    {
        $data = [];

        // 1. Gérer la localisation (Priorité: Paramètres > Session > Base de données)
        $lat = request('lat') ?? session('user_lat');
        $lng = request('lng') ?? session('user_lng');

        // Si l'utilisateur est connecté et qu'on a une nouvelle position, on sauvegarde en DB
        if (Auth::check()) {
            if (request('lat') && request('lng')) {
                User::where('id_user', Auth::id())->update([
                    'latitude' => request('lat'),
                    'longitude' => request('lng')
                ]);
            } elseif (!$lat && !$lng) {
                // Si rien en requête/session, on prend celle de la DB
                $lat = Auth::user()->latitude;
                $lng = Auth::user()->longitude;
            }
        }

        // Sauvegarde en session pour la navigation
        if ($lat && $lng) {
            session(['user_lat' => $lat, 'user_lng' => $lng]);
        }

        // 2. Récupération des vendeurs avec logique de priorité
        try {
            $query = Vendeur::where('actif', 1)
                ->with([
                    'categories',
                    'zone',
                    'coupons' => function ($q) {
                        $q->where('actif', true)->where(function ($sq) {
                            $sq->whereNull('expire_at')->orWhere('expire_at', '>', now());
                        });
                    }
                ])
                ->withCount('avisEvaluations');

            // Logique de tri: 
            // 1. Les boostés (is_boosted = 1) en premier
            // 2. Puis la distance (si dispo)
            // 3. Enfin la note moyenne par défaut
            
            if ($lat && $lng) {
                $query->selectRaw(
                    '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
                    [$lat, $lng, $lat]
                );
                
                $query->orderBy('is_boosted', 'desc')
                      ->orderBy('distance', 'asc');
            } else {
                $query->orderBy('is_boosted', 'desc')
                      ->orderBy('note_moyenne', 'desc');
            }

            // Filtre de recherche sur l'accueil
            if (request()->filled('search')) {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->where('nom_commercial', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%");
                });
            }

            $data['vendeurs'] = $query->paginate(8, ['*'], 'vendeurs_page');
        } catch (\Throwable $e) {
            $data['vendeurs'] = Vendeur::whereRaw('1 = 0')->paginate(8, ['*'], 'vendeurs_page');
            \Log::error("Erreur Home Vendeurs: " . $e->getMessage());
        }

        // Plats du moment (ceux en promotion ou les plus récents)
        try {
            $data['plats'] = Plat::where('disponible', 1)
                ->with('categorie')
                ->orderBy('en_promotion', 'desc')
                ->orderBy('date_creation', 'desc')
                ->paginate(10, ['*'], 'plats_page');
        } catch (\Throwable $e) {
            $data['plats'] = Plat::whereRaw('1 = 0')->paginate(10, ['*'], 'plats_page');
            \Log::error("Erreur Home Plats: " . $e->getMessage());
        }

        // Catégories actives
        try {
            $data['categories'] = CategoryPlat::where('actif', true)
                ->orderBy('ordre_affichage')
                ->get();
        } catch (\Throwable $e) {
            $data['categories'] = collect([]);
        }

        // Catégories de boutiques actives
        try {
            $data['vendorCategories'] = \App\Models\VendorCategory::where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            $data['vendorCategories'] = collect([]);
        }

        // Statistiques globales
        try {
            $data['stats'] = [
                'total_vendeurs' => Vendeur::where('actif', 1)->count(),
                'total_plats' => Plat::where('disponible', 1)->count(),
                'total_commandes' => \DB::table('commandes')->count(),
            ];
        } catch (\Throwable $e) {
            $data['stats'] = ['total_vendeurs' => 0, 'total_plats' => 0, 'total_commandes' => 0];
        }

        // Pubs / Mise en avant (Caroussel)
        try {
            $data['pubs'] = MiseEnAvant::where('actif', 1)
                ->where(function($q) {
                    $q->whereNull('date_fin')->orWhere('date_fin', '>', now());
                })
                ->orderBy('priorite', 'desc')
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            $data['pubs'] = collect([]);
        }

        return view('home', $data);
    }

    /**
     * Page d'exploration avec filtres.
     */
    public function explore(Request $request)
    {
        // 1. Initialisation de la localisation
        $lat = $request->lat ?? session('user_lat');
        $lng = $request->lng ?? session('user_lng');

        if (Auth::check() && !$lat && !$lng) {
            $lat = Auth::user()->latitude;
            $lng = Auth::user()->longitude;
        }

        $query = Vendeur::where('actif', 1)->with([
            'categories',
            'zone',
            'coupons' => function ($q) {
                $q->where('actif', true)->where(function ($sq) {
                    $sq->whereNull('expire_at')->orWhere('expire_at', '>', now());
                });
            }
        ])->withCount('avisEvaluations');

        // 2. Filtres
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories_plats.id_categorie', $request->category);
            });
        }

        if ($request->filled('type')) {
            $query->where('type_vendeur', $request->type);
        }

        if ($request->filled('zone')) {
            $query->where('id_zone', $request->zone);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom_commercial', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('plats', function($sq) use ($search) {
                        $sq->where('nom_plat', 'like', '%' . $search . '%')
                          ->orWhere('description', 'like', '%' . $search . '%');
                    });
            });
        }

        // 3. Logique de tri (Priorité Boost)
        $query->orderBy('is_boosted', 'desc');

        if ($lat && $lng) {
            $query->selectRaw(
                '*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
                [$lat, $lng, $lat]
            )->orderBy('distance', 'asc');
        } else {
            $query->orderBy('note_moyenne', 'desc');
        }

        $vendeurs = $query->paginate(12);

        $categories = CategoryPlat::where('actif', true)->orderBy('nom_categorie')->get();
        $zones = ZoneGeographique::where('actif', true)->orderBy('nom')->get();
        // Optionnel: Récupérer les types uniques de vendeurs pour le filtre
        $types = collect(['restaurant', 'boutique', 'epicerie', 'fast_food', 'autre'])->map(function($t) {
            return (object) ['id_category_vendeur' => $t, 'name' => ucfirst($t)];
        });

        return view('explore', compact('vendeurs', 'categories', 'zones', 'types'));
    }

    /**
     * Page d'exploration des plats avec algorithme de mise en avant.
     */
    public function explorePlats(Request $request)
    {
        $query = Plat::where('disponible', 1)
            ->where('is_available', 1)
            ->with(['categorie', 'vendeur.zone', 'groupesVariantes.variantes'])
            ->join('vendeurs', 'plats.id_vendeur', '=', 'vendeurs.id_vendeur')
            ->select('plats.*');

        // Localisation
        $lat = $request->lat ?? session('user_lat');
        $lng = $request->lng ?? session('user_lng');

        if ($lat && $lng) {
            $query->selectRaw(
                'plats.*, ( 6371 * acos( cos( radians(?) ) * cos( radians( vendeurs.latitude ) ) * cos( radians( vendeurs.longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( vendeurs.latitude ) ) ) ) AS distance',
                [$lat, $lng, $lat]
            );
            $query->orderBy('distance', 'asc');
        }

        // Algorithme de mise en avant (Tri)
        $sort = $request->get('sort', 'recommended');
        
        if ($sort === 'newest') {
            $query->orderBy('plats.date_creation', 'desc');
        } elseif ($sort === 'top_sales') {
            $query->orderBy('plats.nombre_commandes', 'desc');
        } else {
            $query->orderBy('plats.en_promotion', 'desc')
                ->orderBy('plats.nombre_commandes', 'desc')
                ->orderBy('vendeurs.note_moyenne', 'desc')
                ->orderBy('plats.date_creation', 'desc');
        }

        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->where('id_categorie', $request->category);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plats.nom_plat', 'like', '%' . $search . '%')
                    ->orWhere('plats.description', 'like', '%' . $search . '%');
            });
        }

        // Filtre par prix
        if ($request->filled('min_price')) {
            $query->where('plats.prix', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('plats.prix', '<=', $request->max_price);
        }

        $plats = $query->paginate(12);

        $categories = CategoryPlat::where('actif', true)->orderBy('nom_categorie')->get();
        $zones = ZoneGeographique::where('actif', true)->orderBy('nom')->get();

        return view('explore-plats', compact('plats', 'categories', 'zones'));
    }

    /**
     * Page détail d'un vendeur avec toutes les informations complètes.
     */
    public function vendor($id, $slug = null)
    {
        $vendeur = Vendeur::with([
            'plats' => function ($query) {
                $query->where('disponible', 1)->with(['categorie', 'groupesVariantes.variantes'])->orderBy('nom_plat');
            },
            'contacts',
            'horaires' => function ($query) {
                $query->orderBy('jour_semaine');
            },
            'zone',
            'user',
            'categories',
            'coupons' => function ($q) {
                $q->where('actif', true)->where(function ($sq) {
                    $sq->whereNull('expire_at')->orWhere('expire_at', '>', now());
                });
            }
        ])->findOrFail($id);

        // SEO: Rediriger vers l'URL avec le bon slug si nécessaire
        $expectedSlug = \Str::slug($vendeur->nom_commercial);
        if ($slug !== $expectedSlug) {
            return redirect()->route('vendor.show', ['id' => $id, 'slug' => $expectedSlug]);
        }

        /* 
        // Vérifier que le vendeur est actif - On ne bloque plus par 404, 
        // on gère l'affichage 'fermé' dans la vue pour une meilleure UX.
        if (!$vendeur->actif) {
            abort(404);
        }
        */

        // Compter les avis clients
        $avisCount = $vendeur->avisEvaluations()->count();
        $ratingMoyen = $vendeur->avisEvaluations()->avg('note') ?? 0;

        // Récupérer les avis récents
        $avis = $vendeur->avisEvaluations()
            ->with('client')
            ->orderBy('date_publication', 'desc')
            ->limit(5)
            ->get();

        // Compter les catégories de plats
        $categoriesCount = $vendeur->plats()
            ->distinct('id_categorie')
            ->count('id_categorie');

        // Déterminer si la boutique est ouverte
        $isOpen = (bool) ($vendeur->actif ?? false);

        return view('vendor.show', [
            'vendeur' => $vendeur,
            'avisCount' => $avisCount,
            'ratingMoyen' => $ratingMoyen,
            'avis' => $avis,
            'categoriesCount' => $categoriesCount,
            'isOpen' => $isOpen
        ]);
    }

    /**
     * Page des conditions d'utilisation.
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Page de politique de confidentialité.
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Page A propos.
     */
    public function about()
    {
        return view('pages.about');
    }
}
