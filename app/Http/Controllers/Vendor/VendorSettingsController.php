<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CategoryPlat;
use App\Models\Vendeur;
use App\Models\VendeurContact;
use App\Models\VendeurHoraire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ImageHelper;

class VendorSettingsController extends Controller
{
    /**
     * Page des paramètres de la boutique.
     */
    public function index()
    {
        $vendeur = Auth::user()->vendeur;
        $vendeur->load(['horaires', 'contacts', 'categories']);
        $allCategories = CategoryPlat::where('actif', true)->orderBy('nom_categorie')->get();
        $vendorCategories = \App\Models\VendorCategory::where('is_active', true)->get();

        return view('vendeur.settings.index', compact('vendeur', 'allCategories', 'vendorCategories'));
    }

    /**
     * Mettre à jour les infos commerciales.
     */
    public function updateProfile(Request $request)
    {
        $vendeur = Auth::user()->vendeur;

        $rules = [
            'nom_commercial' => 'required|string|max:100',
            'description' => 'nullable|string',
            'adresse_complete' => 'nullable|string',
            'image_principale' => 'nullable|image|max:2048',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'website_url' => 'nullable|url|max:255',
        ];

        // Only allow setting category if not already set
        if (!$vendeur->id_category_vendeur) {
            $rules['id_category_vendeur'] = 'required|exists:vendor_categories,id_category_vendeur';
        }

        $validated = $request->validate($rules);

        // Remove from data if it's already set to prevent accidental overrides (security)
        if ($vendeur->id_category_vendeur) {
            unset($validated['id_category_vendeur']);
        }

        if ($request->hasFile('image_principale')) {
            $validated['image_principale'] = ImageHelper::uploadAndConvert($request->file('image_principale'), 'vendeurs');
        }

        $vendeur->update($validated);

        return back()->with('success', 'Profil boutique mis à jour !');
    }

    /**
     * Mettre à jour les horaires d'ouverture.
     */
    public function updateHours(Request $request)
    {
        $vendeur = Auth::user()->vendeur;

        $request->validate([
            'hours' => 'required|array',
            'hours.*.heure_ouverture' => 'nullable|date_format:H:i',
            'hours.*.heure_fermeture' => 'nullable|date_format:H:i',
        ]);

        $horairesJson = [];
        foreach ($request->hours as $jour => $data) {
            $horaire = VendeurHoraire::updateOrCreate(
                ['id_vendeur' => $vendeur->id_vendeur, 'jour_semaine' => $jour],
                [
                    'heure_ouverture' => $data['heure_ouverture'] ? $data['heure_ouverture'] . ':00' : null,
                    'heure_fermeture' => $data['heure_fermeture'] ? $data['heure_fermeture'] . ':00' : null,
                    'ferme' => isset($data['ferme']) ? true : false
                ]
            );

            $horairesJson[] = [
                'jour_semaine' => (int) $jour,
                'heure_ouverture' => $horaire->heure_ouverture,
                'heure_fermeture' => $horaire->heure_fermeture,
                'ferme' => (bool) $horaire->ferme
            ];
        }

        // Synchroniser avec le champ JSON legacy pour la compatibilité
        $vendeur->update(['horaires_ouverture' => $horairesJson]);

        return back()->with('success', 'Horaires mis à jour !');
    }

    public function updateCategories(Request $request)
    {
        $vendeur = Auth::user()->vendeur;

        $request->validate([
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories_plats,id_categorie',
            'new_specialty' => 'nullable|string|max:50',
        ]);

        $categoryIds = $request->input('categories', []);

        if ($request->filled('new_specialty')) {
            $newCat = CategoryPlat::firstOrCreate(
                ['nom_categorie' => trim($request->new_specialty)],
                ['actif' => true, 'ordre_affichage' => 99]
            );
            if (!in_array($newCat->id_categorie, $categoryIds)) {
                $categoryIds[] = $newCat->id_categorie;
            }
        }

        $vendeur->categories()->sync($categoryIds);

        return back()->with('success', 'Vos spécialités ont été mises à jour !');
    }

    /**
     * Mettre à jour l'état actif de la boutique.
     */
    public function toggleStatus()
    {
        $vendeur = Auth::user()->vendeur;
        $vendeur->actif = !$vendeur->actif;
        $vendeur->save();

        $msg = $vendeur->actif ? 'Boutique ouverte !' : 'Boutique fermée !';
        return back()->with('success', $msg);
    }
}
