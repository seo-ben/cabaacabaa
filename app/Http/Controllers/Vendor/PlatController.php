<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use App\Models\Vendeur;
use App\Models\CategoryPlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ImageHelper;

class PlatController extends Controller
{
    /**
     * Liste des plats du vendeur.
     */
    public function index()
    {
        $vendeur = Auth::user()->vendeur;
        if (!$vendeur)
            return redirect()->route('home')->with('error', 'Profil vendeur introuvable.');

        $plats = $vendeur->plats()->with('categorie')->get();
        return view('vendeur.plats.index', compact('plats'));
    }

    /**
     * Formulaire d'ajout d'un plat.
     */
    public function create()
    {
        $vendeur = Auth::user()->vendeur;
        if (!$vendeur)
            return redirect()->route('home')->with('error', 'Profil vendeur introuvable.');

        // Restriction: Seulement les catégories choisies par le vendeur
        $categories = $vendeur->categories;

        if ($categories->isEmpty()) {
            return redirect()->route('vendeur.dashboard')->with('warning', 'Veuillez d\'abord configurer vos spécialités (menu) dans les paramètres de votre boutique.');
        }

        return view('vendeur.plats.create', compact('categories'));
    }

    /**
     * Enregistrer un nouveau plat.
     */
    public function store(Request $request)
    {
        $vendeur = Auth::user()->vendeur;

        $validated = $request->validate([
            'nom_plat' => 'required|string|max:100',
            'id_categorie' => 'required|exists:categories_plats,id_categorie',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        // Vérification de sécurité: la catégorie doit appartenir aux spécialités du vendeur
        if (!$vendeur->categories()->where('vendeur_categories.id_categorie', $validated['id_categorie'])->exists()) {
            return back()->withErrors(['id_categorie' => 'Cette catégorie ne fait pas partie de vos spécialités enregistrées.'])->withInput();
        }

        $plat = new Plat($validated);
        $plat->id_vendeur = $vendeur->id_vendeur;

        if ($request->hasFile('image')) {
            $plat->image_principale = ImageHelper::uploadAndConvert($request->file('image'), 'plats');
        }

        $plat->save();

        // Ensure id_plat is available for relations
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                // Créer le groupe
                $groupe = new \App\Models\GroupeVariante([
                    'id_plat' => $plat->id_plat,
                    'nom' => $variantData['groupe_nom'],
                    'obligatoire' => isset($variantData['obligatoire']),
                    'choix_multiple' => isset($variantData['choix_multiple']),
                    'min_choix' => $variantData['min_choix'] ?? 0,
                    'max_choix' => $variantData['max_choix'] ?? 1,
                ]);
                $groupe->save();

                // Créer les options
                if (isset($variantData['options']) && is_array($variantData['options'])) {
                    foreach ($variantData['options'] as $optionData) {
                        if (!empty($optionData['nom'])) {
                            $groupe->variantes()->create([
                                'nom' => $optionData['nom'],
                                'prix_supplement' => $optionData['prix'] ?? 0,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('vendeur.plats.index')->with('success', 'Plat ajouté avec succès !');
    }

    /**
     * Formulaire de modification d'un plat.
     */
    public function edit($id)
    {
        $vendeur = Auth::user()->vendeur;
        $plat = Plat::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);
        $categories = $vendeur->categories;

        return view('vendeur.plats.edit', compact('plat', 'categories'));
    }

    /**
     * Mettre à jour un plat existant.
     */
    public function update(Request $request, $id)
    {
        $vendeur = Auth::user()->vendeur;
        $plat = Plat::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);

        $validated = $request->validate([
            'nom_plat' => 'required|string|max:100',
            'id_categorie' => 'required|exists:categories_plats,id_categorie',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'en_promotion' => 'boolean',
            'prix_promotion' => 'required_if:en_promotion,1|nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'disponible' => 'boolean',
        ]);

        if (!$vendeur->categories()->where('vendeur_categories.id_categorie', $validated['id_categorie'])->exists()) {
            return back()->withErrors(['id_categorie' => 'Cette catégorie ne fait pas partie de vos spécialités.'])->withInput();
        }

        $plat->fill($validated);
        $plat->en_promotion = $request->has('en_promotion');
        $plat->disponible = $request->has('disponible');

        if ($request->hasFile('image')) {
            $plat->image_principale = ImageHelper::uploadAndConvert($request->file('image'), 'plats');
        }

        $plat->save();

        return redirect()->route('vendeur.plats.index')->with('success', 'Plat mis à jour !');
    }

    /**
     * Supprimer un plat.
     */
    public function destroy($id)
    {
        $vendeur = Auth::user()->vendeur;
        $plat = Plat::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);

        $plat->delete();

        return redirect()->route('vendeur.plats.index')->with('success', 'Plat supprimé.');
    }
}
