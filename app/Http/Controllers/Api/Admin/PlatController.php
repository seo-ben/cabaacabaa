<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use App\Models\Vendeur;
use App\Models\CategoryPlat;
use Illuminate\Http\Request;

class PlatController extends Controller
{
    /**
     * Liste complète des produits de tous les vendeurs
     */
    public function index(Request $request)
    {
        $query = Plat::with(['vendeur', 'categorie']);

        // Recherche
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nom_plat', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('vendeur', function ($qv) use ($search) {
                        $qv->where('nom_commercial', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre par catégorie
        if ($category = $request->input('category')) {
            $query->where('id_categorie', $category);
        }

        // Filtre par vendeur
        if ($vendor = $request->input('vendor')) {
            $query->where('id_vendeur', $vendor);
        }

        // Filtre par disponibilité
        if ($request->has('available')) {
            $query->where('disponible', $request->boolean('available'));
        }

        $plats = $query->latest('id_plat')->paginate(15)->withQueryString();

        $stats = [
            'total' => Plat::count(),
            'available' => Plat::where('disponible', true)->count(),
            'unavailable' => Plat::where('disponible', false)->count(),
            'promoted' => Plat::where('en_promotion', true)->count(),
        ];

        $categories = CategoryPlat::orderBy('nom_categorie')->get();
        $vendors = Vendeur::orderBy('nom_commercial')->get(['id_vendeur', 'nom_commercial']);

        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    /**
     * Voir les détails d'un produit
     */
    public function show($id)
    {
        $plat = Plat::with(['vendeur', 'categorie', 'groupesVariantes.variantes'])->findOrFail($id);
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    /**
     * Basculer la disponibilité d'un produit (Action rapide)
     */
    public function toggleAvailability($id)
    {
        $plat = Plat::findOrFail($id);
        $plat->update(['disponible' => !$plat->disponible]);

        return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
    }

    /**
     * Supprimer un produit (Action admin)
     */
    public function destroy($id)
    {
        $plat = Plat::findOrFail($id);
        $name = $plat->nom_plat;
        $plat->delete();

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }
}
