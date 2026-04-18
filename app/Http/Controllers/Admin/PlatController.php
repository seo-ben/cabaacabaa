<?php

namespace App\Http\Controllers\Admin;

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

        return view('admin.plats.index', compact('plats', 'stats', 'categories', 'vendors'));
    }

    /**
     * Voir les détails d'un produit
     */
    public function show($id)
    {
        $plat = Plat::with(['vendeur', 'categorie', 'groupesVariantes.variantes'])->findOrFail($id);
        return view('admin.plats.show', compact('plat'));
    }

    /**
     * Basculer la disponibilité d'un produit (Action rapide)
     */
    public function toggleAvailability($id)
    {
        $plat = Plat::findOrFail($id);
        $plat->update(['disponible' => !$plat->disponible]);

        return back()->with('success', 'La disponibilité du produit a été mise à jour.');
    }

    /**
     * Formulaire d'ajout d'un produit (Admin)
     */
    public function create()
    {
        $categories = CategoryPlat::orderBy('nom_categorie')->get();
        $vendors = Vendeur::orderBy('nom_commercial')->get(['id_vendeur', 'nom_commercial']);
        
        return view('admin.plats.create', compact('categories', 'vendors'));
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_vendeur' => 'required|exists:vendeurs,id_vendeur',
            'id_categorie' => 'required|exists:categories_plats,id_categorie',
            'nom_plat' => 'required|string|max:100',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $plat = new Plat($validated);
        
        if ($request->hasFile('image')) {
            // Utiliser le helper s'il existe ou une méthode standard
            if (class_exists('\App\Helpers\ImageHelper')) {
                $plat->image_principale = \App\Helpers\ImageHelper::uploadAndConvert($request->file('image'), 'plats', 80, 1200, true);
            } else {
                $plat->image_principale = $request->file('image')->store('plats', 'public');
            }
        }

        $plat->save();

        return redirect()->route('admin.plats.index')->with('success', 'Le produit a été créé avec succès.');
    }

    /**
     * Supprimer un produit (Action admin)
     */
    public function destroy($id)
    {
        $plat = Plat::findOrFail($id);
        $name = $plat->nom_plat;
        $plat->delete();

        return redirect()->route('admin.plats.index')
            ->with('success', "Le produit \"{$name}\" a été supprimé définitivement du catalogue.");
    }
}
