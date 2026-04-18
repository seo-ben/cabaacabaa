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
    public function index(Request $request)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
        if (!$vendeur)
            return redirect()->route('home')->with('error', 'Profil vendeur introuvable.');

        $plats = $vendeur->plats()->with('categorie')->get();
        return view('vendeur.plats.index', compact('plats'));
    }

    /**
     * Formulaire d'ajout d'un plat.
     */
    public function create(Request $request)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
        if (!$vendeur)
            return redirect()->route('home')->with('error', 'Profil vendeur introuvable.');

        // Restriction: Seulement les catégories choisies par le vendeur
        $categories = $vendeur->categories;

        if ($categories->isEmpty()) {
            return redirect()->route('vendeur.slug.settings.index', ['vendor_slug' => $vendeur->slug])
                ->with('warning', 'Avant d\'ajouter des articles, veuillez définir les catégories (spécialités) de votre menu ici.');
        }

        return view('vendeur.plats.create', compact('categories'));
    }

    /**
     * Enregistrer un nouveau plat.
     */
    public function store(Request $request)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;

        $validated = $request->validate([
            'nom_plat' => 'required|string|max:100',
            'id_categorie' => 'required|exists:categories_plats,id_categorie',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        // Vérification de la limite du plan d'abonnement
        $platCount = $vendeur->plats()->count();
        if ($platCount >= $vendeur->getPlatLimit()) {
            return back()->with('error', 'Vous avez atteint la limite de ' . $vendeur->getPlatLimit() . ' articles pour votre plan actuel. Passez au niveau supérieur pour en ajouter plus !')->withInput();
        }

        // Vérification de sécurité: la catégorie doit appartenir aux spécialités du vendeur
        if (!$vendeur->categories()->where('vendeur_categories.id_categorie', $validated['id_categorie'])->exists()) {
            return back()->withErrors(['id_categorie' => 'Cette catégorie ne fait pas partie de vos spécialités enregistrées.'])->withInput();
        }

        $plat = new Plat($validated);
        $plat->id_vendeur = $vendeur->id_vendeur;

        if ($request->hasFile('image')) {
            $plat->image_principale = ImageHelper::uploadAndConvert($request->file('image'), 'plats', 80, 1200, true);
        }

        $plat->save();

        // Gestion de la galerie et vidéo (si le plan le permet)
        if ($vendeur->canUseGallery()) {
            // Galerie photos
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $path = ImageHelper::uploadAndConvert($image, 'plats_gallery', 80, 1200, true);
                    $plat->medias()->create([
                        'id_vendeur' => $vendeur->id_vendeur,
                        'type' => 'image',
                        'chemin' => $path
                    ]);
                }
            }

            // Vidéo (URL YouTube / MP4)
            if ($request->filled('video_url')) {
                $plat->medias()->create([
                    'id_vendeur' => $vendeur->id_vendeur,
                    'type' => 'video',
                    'chemin' => $request->video_url,
                    'titre' => 'Vidéo de présentation'
                ]);
            }
        }

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

        return redirect()->route('vendeur.slug.plats.index', ['vendor_slug' => $vendeur->slug])->with('success', 'Plat ajouté avec succès !');
    }

    /**
     * Formulaire de modification d'un plat.
     */
    public function edit(Request $request, $vendor_slug, $id)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
        $plat = Plat::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);
        $categories = $vendeur->categories;

        return view('vendeur.plats.edit', compact('plat', 'categories'));
    }

    /**
     * Mettre à jour un plat existant.
     */
    public function update(Request $request, $vendor_slug, $id)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
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
            $plat->image_principale = ImageHelper::uploadAndConvert($request->file('image'), 'plats', 80, 1200, true);
        }

        $plat->save();

        // Gestion de la galerie et vidéo (vendeur Premium/Standard)
        if ($vendeur->canUseGallery()) {
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $path = ImageHelper::uploadAndConvert($image, 'plats_gallery', 80, 1200, true);
                    $plat->medias()->create([
                        'id_vendeur' => $vendeur->id_vendeur,
                        'type' => 'image',
                        'chemin' => $path
                    ]);
                }
            }

            if ($request->filled('video_url')) {
                // Pour simplifier, on remplace la vidéo existante si une nouvelle URL est fournie
                $plat->medias()->where('type', 'video')->delete();
                $plat->medias()->create([
                    'id_vendeur' => $vendeur->id_vendeur,
                    'type' => 'video',
                    'chemin' => $request->video_url,
                    'titre' => 'Vidéo de présentation'
                ]);
            }

            // Suppression de médias spécifiques (si demandé via AJAX/checkbox)
            if ($request->has('remove_medias')) {
                $plat->medias()->whereIn('id', $request->remove_medias)->delete();
            }
        }

        return redirect()->route('vendeur.slug.plats.index', ['vendor_slug' => $vendeur->slug])->with('success', 'Plat mis à jour !');
    }

    /**
     * Supprimer un plat.
     */
    public function destroy(Request $request, $vendor_slug, $id)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
        $plat = Plat::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);

        $plat->delete();

        return redirect()->route('vendeur.slug.plats.index', ['vendor_slug' => $vendeur->slug])->with('success', 'Plat supprimé.');
    }

    /**
     * Basculer la disponibilité d'un plat (Rupture de stock).
     */
    public function toggleAvailability(Request $request, $vendor_slug, $id)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
        $plat = Plat::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);

        $plat->disponible = !$plat->disponible;
        $plat->save();

        $msg = $plat->disponible ? 'Article à nouveau disponible !' : 'Article marqué comme épuisé.';
        return back()->with('success', $msg);
    }
}
