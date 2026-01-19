<?php

namespace App\Http\Controllers;

use App\Models\Vendeur;
use App\Models\Plat;
use App\Models\CategoryPlat;
use App\Models\ZoneGeographique;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Grand contrôleur pour l'accueil.
     * Gère les vendeurs en vedette, les plats récents, les catégories, et tout le contenu principal.
     */
    public function index()
    {
        $data = [];

        // Vendeurs populaires (basé sur la note ou l'ancienneté pour l'instant)
        try {
            $data['vendeurs'] = Vendeur::where('actif', 1)
                ->with([
                    'categories',
                    'zone',
                    'coupons' => function ($q) {
                        $q->where('actif', true)->where(function ($sq) {
                            $sq->whereNull('expire_at')->orWhere('expire_at', '>', now());
                        });
                    }
                ])
                ->withCount('avisEvaluations')
                ->orderBy('note_moyenne', 'desc')
                ->paginate(4, ['*'], 'vendeurs_page');
        } catch (\Throwable $e) {
            $data['vendeurs'] = collect([]);
        }

        // Plats du moment (ceux en promotion ou les plus récents)
        try {
            $data['plats'] = Plat::where('disponible', 1)
                ->with('categorie')
                ->orderBy('en_promotion', 'desc')
                ->orderBy('date_creation', 'desc')
                ->paginate(10, ['*'], 'plats_page');
        } catch (\Throwable $e) {
            $data['plats'] = collect([]);
        }

        // Catégories actives
        try {
            $data['categories'] = CategoryPlat::where('actif', true)
                ->orderBy('ordre_affichage')
                ->limit(6)
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

        return view('home', $data);
    }

    /**
     * Page d'exploration avec filtres.
     */
    public function explore(Request $request)
    {
        $query = Vendeur::where('actif', 1)->with([
            'categories',
            'zone',
            'coupons' => function ($q) {
                $q->where('actif', true)->where(function ($sq) {
                    $sq->whereNull('expire_at')->orWhere('expire_at', '>', now());
                });
            }
        ])->withCount('avisEvaluations');

        // Filtre par catégorie (via relation)
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories_plats.id_categorie', $request->category);
            });
        }

        // Filtre par catégorie de boutique (Type)
        if ($request->filled('type')) {
            $query->where('id_category_vendeur', $request->type);
        }

        // Filtre par zone
        if ($request->filled('zone')) {
            $query->where('id_zone', $request->zone);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom_commercial', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $vendeurs = $query->orderBy('note_moyenne', 'desc')->paginate(12);

        $categories = CategoryPlat::where('actif', true)->orderBy('nom_categorie')->get();
        $zones = ZoneGeographique::where('actif', true)->orderBy('nom')->get();
        $types = \App\Models\VendorCategory::where('is_active', true)->orderBy('name')->get();

        return view('explore', compact('vendeurs', 'categories', 'zones', 'types'));
    }

    /**
     * Page d'exploration des plats avec algorithme de mise en avant.
     */
    public function explorePlats(Request $request)
    {
        $query = Plat::where('disponible', 1)
            ->with(['categorie', 'vendeur.zone', 'groupesVariantes.variantes'])
            ->join('vendeurs', 'plats.id_vendeur', '=', 'vendeurs.id_vendeur')
            ->select('plats.*');

        // Algorithme de mise en avant (Merit-based)
        $query->orderBy('plats.en_promotion', 'desc')
            ->orderBy('plats.nombre_commandes', 'desc')
            ->orderBy('vendeurs.note_moyenne', 'desc')
            ->orderBy('plats.date_creation', 'desc');

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
                $query->where('disponible', 1)->orderBy('nom_plat');
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
}
