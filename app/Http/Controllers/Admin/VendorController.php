<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendeur;
use App\Models\User;
use App\Models\ZoneGeographique;
use App\Models\CategoryPlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    /**
     * Liste tous les vendeurs.
     */
    public function index(Request $request)
    {
        $query = Vendeur::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom_commercial', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('statut_verification', $request->status);
        }

        $vendeurs = $query->latest('date_inscription')->paginate(20)->withQueryString();

        $zones = ZoneGeographique::all();

        // Stats
        $stats = [
            'total' => Vendeur::count(),
            'active' => Vendeur::where('actif', true)->where('statut_verification', 'verifie')->count(),
            'pending' => Vendeur::where('statut_verification', 'en_cours')->count(),
            'suspended' => Vendeur::where('statut_verification', 'suspendu')->count(),
        ];

        $vendorCategories = \App\Models\VendorCategory::where('is_active', true)->get();

        return view('admin.vendors.index', compact('vendeurs', 'zones', 'stats', 'vendorCategories'));
    }

    /**
     * Créer un nouveau vendeur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nom_commercial' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'id_category_vendeur' => 'nullable|exists:vendor_categories,id_category_vendeur',
            'adresse_complete' => 'required|string|max:500',
            'id_zone' => 'nullable|exists:zones_geographiques,id_zone',
            'telephone_commercial' => 'nullable|string|max:20',
        ]);

        // Créer l'utilisateur associé
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('vendeur123'), // Mot de passe temporaire
            'role' => 'vendeur',
        ]);

        // Créer le profil vendeur
        Vendeur::create([
            'id_user' => $user->id_user,
            'id_zone' => $validated['id_zone'] ?? null,
            'nom_commercial' => $validated['nom_commercial'],
            'description' => $validated['description'] ?? '',
            'id_category_vendeur' => $validated['id_category_vendeur'] ?? null,
            'adresse_complete' => $validated['adresse_complete'],
            'latitude' => 0.0,
            'longitude' => 0.0,
            'horaires_ouverture' => json_encode([]),
            'telephone_commercial' => $validated['telephone_commercial'] ?? null,
            'statut_verification' => 'verifie',
            'note_moyenne' => 0.0,
            'nombre_avis' => 0,
            'actif' => true,
        ]);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendeur créé avec succès. Mot de passe temporaire: vendeur123');
    }

    /**
     * Voir les détails complets d'un vendeur.
     */
    public function show($id)
    {
        $vendeur = Vendeur::with(['user', 'zone', 'categories', 'horaires'])
            ->withCount(['plats', 'commandes', 'avisEvaluations'])
            ->findOrFail($id);

        // Paginer les commandes (10 par page)
        $recentOrders = $vendeur->commandes()
            ->with('client')
            ->latest('date_commande')
            ->paginate(10, ['*'], 'orders_page')
            ->withQueryString();

        // Paginer les avis (5 par page)
        $recentReviews = $vendeur->avisEvaluations()
            ->with('client')
            ->latest('date_publication')
            ->paginate(5, ['*'], 'reviews_page')
            ->withQueryString();

        // Paginer les produits (12 par page)
        $plats = $vendeur->plats()
            ->with('categorie')
            ->paginate(12, ['*'], 'products_page')
            ->withQueryString();

        // Statistiques financières simples
        $totalRevenue = $vendeur->commandes()->where('statut', 'termine')->sum('montant_total');

        return view('admin.vendors.show', compact('vendeur', 'recentOrders', 'recentReviews', 'plats', 'totalRevenue'));
    }

    /**
     * Afficher et éditer un vendeur.
     */
    public function edit($id)
    {
        $vendeur = Vendeur::findOrFail($id);
        $zones = ZoneGeographique::all();
        $vendorCategories = \App\Models\VendorCategory::where('is_active', true)->get();

        return view('admin.vendors.edit', compact('vendeur', 'zones', 'vendorCategories'));
    }

    /**
     * Mettre à jour un vendeur.
     */
    public function update(Request $request, $id)
    {
        $vendeur = Vendeur::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $vendeur->user->id_user . ',id_user',
            'nom_commercial' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'id_category_vendeur' => 'required|exists:vendor_categories,id_category_vendeur',
            'adresse_complete' => 'required|string|max:500',
            'id_zone' => 'nullable|exists:zones_geographiques,id_zone',
            'telephone_commercial' => 'nullable|string|max:20',
            'actif' => 'required|boolean',
            'statut_verification' => 'required|in:non_verifie,verifie,suspendu,en_cours',
        ]);

        // Mettre à jour l'utilisateur
        if ($vendeur->user) {
            $vendeur->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);
        }

        // Mettre à jour le vendeur
        $vendeur->update([
            'nom_commercial' => $validated['nom_commercial'],
            'description' => $validated['description'] ?? $vendeur->description,
            'id_category_vendeur' => $validated['id_category_vendeur'],
            'adresse_complete' => $validated['adresse_complete'],
            'id_zone' => $validated['id_zone'],
            'telephone_commercial' => $validated['telephone_commercial'],
            'actif' => (bool) $validated['actif'],
            'statut_verification' => $validated['statut_verification'],
        ]);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendeur mis à jour avec succès.');
    }

    /**
     * Supprimer un vendeur.
     */
    public function destroy($id)
    {
        $vendeur = Vendeur::findOrFail($id);
        $user = $vendeur->user;

        $vendeur->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendeur supprimé avec succès.');
    }

    /**
     * Approuver un vendeur en attente.
     */
    public function approve($id)
    {
        $vendeur = Vendeur::findOrFail($id);
        $vendeur->update([
            'statut_verification' => 'verifie',
            'date_verification' => now(),
            'actif' => true,
        ]);

        // Mise à jour du rôle utilisateur si nécessaire
        if ($vendeur->user && $vendeur->user->role !== 'admin') {
            $vendeur->user->update(['role' => 'vendeur']);
        }

        // Send Approval Email
        if ($vendeur->user) {
            try {
                \Illuminate\Support\Facades\Mail::to($vendeur->user->email)->send(new \App\Mail\VendorApproved($vendeur));
            } catch (\Throwable $e) {
                // Log error or just continue, we don't want to break the approval flow
                \Illuminate\Support\Facades\Log::error('Failed to send vendor approval email: ' . $e->getMessage());
                return back()->with('success', 'Vendeur approuvé, mais l\'envoi de l\'email a échoué. Vérifiez les logs.');
            }
        }

        return back()->with('success', 'Vendeur approuvé et activé avec succès.');
    }

    /**
     * Retirer la certification d'un vendeur.
     */
    public function unverify($id)
    {
        $vendeur = Vendeur::findOrFail($id);
        $vendeur->update([
            'statut_verification' => 'en_cours',
            'date_verification' => null,
        ]);

        return back()->with('success', 'Certification retirée avec succès. La boutique est repassée en attente.');
    }

    /**
     * Suspendre un vendeur.
     */
    public function suspend($id)
    {
        $vendeur = Vendeur::findOrFail($id);
        $vendeur->update([
            'statut_verification' => 'suspendu',
            'actif' => false,
        ]);

        return back()->with('success', 'Vendeur suspendu avec succès.');
    }

    /**
     * Réactiver un vendeur suspendu.
     */
    public function unsuspend($id)
    {
        $vendeur = Vendeur::findOrFail($id);
        $vendeur->update([
            'statut_verification' => 'verifie',
            'actif' => true,
        ]);

        return back()->with('success', 'Vendeur réactivé avec succès.');
    }

    /**
     * Voir un document de vérification.
     */
    public function showDoc($id, $type)
    {
        $vendeur = Vendeur::findOrFail($id);
        $path = $type === 'identite' ? $vendeur->document_identite : $vendeur->justificatif_domicile;

        if (!$path || !\Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return response()->file(\Storage::disk('private')->path($path));
    }

    /**
     * Pré-remplir le profil d'un vendeur à partir d'un utilisateur existant.
     */
    public function completeProfile($userId)
    {
        $user = User::findOrFail($userId);

        // Vérifier si un profil vendeur existe déjà
        $vendeur = Vendeur::where('id_user', $userId)->first();

        if ($vendeur) {
            return redirect()->route('admin.vendors.edit', $vendeur->id_vendeur);
        }

        $zones = ZoneGeographique::all();
        $categories = CategoryPlat::where('actif', true)->orderBy('nom_categorie')->get();
        $vendorCategories = \App\Models\VendorCategory::where('is_active', true)->get();

        return view('admin.vendors.complete', compact('user', 'zones', 'categories', 'vendorCategories'));
    }

    /**
     * Enregistrer l'enrichissement complet du profil vendeur (CMS).
     */
    public function storeEnrichment(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'nom_commercial' => 'required|string|max:255',
            'id_category_vendeur' => 'required|exists:vendor_categories,id_category_vendeur',
            'description' => 'nullable|string',
            'id_zone' => 'nullable|exists:zones_geographiques,id_zone',
            'contact' => 'required|array',
            'horaires' => 'required|array',
            'sections' => 'nullable|array',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories_plats,id_categorie',
            'image_principale' => 'nullable|image|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:5120',
        ]);

        return \DB::transaction(function () use ($user, $validated, $request) {
            // 1. Créer ou Mettre à jour le Vendeur
            $vendeur = Vendeur::updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nom_commercial' => $validated['nom_commercial'],
                    'id_category_vendeur' => $validated['id_category_vendeur'],
                    'description' => $validated['description'] ?? '',
                    'id_zone' => $validated['id_zone'],
                    'adresse_complete' => $validated['contact']['adresse_ligne_1'] ?? '',
                    'telephone_commercial' => $validated['contact']['telephone_principal'] ?? $user->telephone,
                    'actif' => true,
                    'statut_verification' => 'verifie',
                ]
            );

            // 2. Gérer l'image principale
            if ($request->hasFile('image_principale')) {
                $path = $request->file('image_principale')->store('vendors/banners', 'public');
                $vendeur->update(['image_principale' => $path]);
            }

            // 3. Contacts
            $vendeur->contacts()->delete();
            $vendeur->contacts()->create($validated['contact']);

            // 4. Horaires
            $vendeur->horaires()->delete();
            foreach ($validated['horaires'] as $dayData) {
                $vendeur->horaires()->create([
                    'jour_semaine' => $dayData['jour_semaine'],
                    'heure_ouverture' => $dayData['heure_ouverture'],
                    'heure_fermeture' => $dayData['heure_fermeture'],
                    'ferme' => isset($dayData['ferme']),
                ]);
            }

            // 5. Sections
            $vendeur->sections()->delete();
            if (!empty($validated['sections'])) {
                foreach ($validated['sections'] as $sectionData) {
                    $vendeur->sections()->create($sectionData);
                }
            }

            // 6. Galerie Media
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $file) {
                    $path = $file->store('vendors/gallery', 'public');
                    $vendeur->medias()->create([
                        'type' => 'image',
                        'chemin' => $path,
                        'ordre' => $index,
                    ]);
                }
            }

            // 7. Catégories du Menu (Spécialités)
            if (isset($validated['categories'])) {
                $vendeur->categories()->sync($validated['categories']);
            }

            return redirect()->route('admin.users.index')
                ->with('success', "Le profil commercial de {$vendeur->nom_commercial} a été configuré avec succès.");
        });
    }
}
