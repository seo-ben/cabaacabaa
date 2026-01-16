<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs avec filtres avancés
     */
    public function index(Request $request)
    {
        $query = User::with(['vendeur', 'commandes']);

        // Filtres
        $role = $request->input('role', 'tous');
        $status = $request->input('status', 'tous');
        $search = $request->input('search', '');
        $verified = $request->input('verified');
        $suspicious = $request->input('suspicious');
        $locked = $request->input('locked');
        $sortBy = $request->input('sort_by', 'date_creation');
        $sortOrder = $request->input('sort_order', 'desc');

        // Application des scopes
        $query->byRole($role)
            ->byStatus($status)
            ->search($search);

        if ($verified !== null) {
            $query->verified($verified === '1');
        }

        if ($suspicious !== null) {
            $query->suspicious($suspicious === '1');
        }

        if ($locked !== null) {
            $query->locked($locked === '1');
        }

        // Tri
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $users = $query->paginate(20)->withQueryString();

        // Statistiques globales
        $statistics = [
            'total' => User::count(),
            'actif' => User::where('status', 'actif')->whereNull('deleted_at')->count(),
            'inactif' => User::where('status', 'inactif')->whereNull('deleted_at')->count(),
            'suspendu' => User::where('status', 'suspendu')->whereNull('deleted_at')->count(),
            'supprime' => User::onlyTrashed()->count(),
            'verrouille' => User::whereNotNull('locked_until')
                ->where('locked_until', '>', now())
                ->count(),
            'suspect' => User::where('risk_score', '>', 5)->count(),
            'non_verifie' => User::where('is_verified', false)->count(),
            'clients' => User::where('role', 'client')->count(),
            'vendeurs' => User::where('role', 'vendeur')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        // Activités récentes
        $recentActivities = User::whereNotNull('date_derniere_connexion')
            ->orderBy('date_derniere_connexion', 'desc')
            ->take(5)
            ->get(['id_user', 'nom_complet', 'date_derniere_connexion', 'derniere_ip']);

        return view('admin.users.index', compact(
            'users',
            'statistics',
            'recentActivities',
            'role',
            'status',
            'search',
            'verified',
            'suspicious',
            'locked',
            'sortBy',
            'sortOrder'
        ));
    }

    /**
     * Afficher les détails complets d'un utilisateur
     */
    public function show($id)
    {
        $user = User::withTrashed()->with(['vendeur', 'commandes', 'avis', 'favoris'])->findOrFail($id);

        // Statistiques utilisateur
        $stats = [
            'commandes_total' => $user->commandes()->count(),
            'commandes_completes' => $user->commandes()->where('statut', 'livree')->count(),
            'commandes_annulees' => $user->commandes()->where('statut', 'annulee')->count(),
            'montant_total' => $user->commandes()->where('statut', 'livree')->sum('montant_total'),
            'avis_donnes' => $user->avis()->count(),
            'favoris' => $user->favoris()->count(),
            'note_moyenne' => $user->avis()->avg('note'),
        ];

        // Historique des connexions (10 dernières)
        $loginHistory = collect($user->failed_logins ?? [])->take(10);

        // Drapeaux suspects
        $suspiciousFlags = $user->suspicious_flags ?? [];

        return view('admin.users.show', compact('user', 'stats', 'loginHistory', 'suspiciousFlags'));
    }

    /**
     * Formulaire de création d'utilisateur
     */
    public function create()
    {
        return redirect()->route('admin.users.index')->with('showCreate', true);
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:20|unique:users,telephone',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'role' => 'required|in:client,vendeur,admin',
            'status' => 'required|in:actif,inactif',
            'is_verified' => 'boolean',
        ]);

        $validated['name'] = $validated['nom_complet'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['date_creation'] = now();
        $validated['is_verified'] = $request->boolean('is_verified');
        $validated['email_verified_at'] = $validated['is_verified'] ? now() : null;
        $validated['risk_score'] = 0;
        $validated['login_attempts'] = 0;

        // Sync statut_compte
        $validated['statut_compte'] = $validated['status'] === 'actif' ? 'actif' : 'en_attente';

        $user = User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->nom_complet} a été créé avec succès.");
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        return redirect()->route('admin.users.show', $id);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
            'telephone' => 'required|string|max:20|unique:users,telephone,' . $id . ',id_user',
            'role' => 'required|in:client,vendeur,admin',
            'status' => 'required|in:actif,inactif,suspendu,en_attente_verification',
            'langue_preferee' => 'nullable|string|in:fr,en',
        ]);

        // Sync statut_compte for compatibility
        $validated['statut_compte'] = $validated['status'] === 'actif' ? 'actif' : ($validated['status'] === 'suspendu' ? 'suspendu' : 'en_attente');

        // Protection : empêcher un admin de se rétrograder lui-même
        if ($user->id_user === auth()->id() && $validated['role'] !== 'admin') {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas modifier votre propre rôle d\'administrateur.');
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur "' . $user->nom_complet . '" mis à jour avec succès.');
    }

    /**
     * Changer le statut (actif/inactif/suspendu)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:actif,inactif,suspendu',
        ]);

        // Protection admin
        if ($user->role === 'admin' && $user->id_user !== auth()->id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas modifier le statut d\'un autre administrateur.');
        }

        $user->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', "Le statut de {$user->nom_complet} a été changé en : {$validated['status']}.");
    }

    /**
     * Suspendre avec raison
     */
    public function suspend(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Protection admin
        if ($user->role === 'admin') {
            return redirect()->back()
                ->with('error', 'Impossible de suspendre un administrateur.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'duration' => 'nullable|integer|min:1|max:365', // jours
        ]);

        $user->suspend($validated['reason']);

        // Log l'action (Désactivé car package non installé)
        /*
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['reason' => $validated['reason']])
            ->log('user_suspended');
        */

        return redirect()->back()
            ->with('success', "{$user->nom_complet} a été suspendu. Raison : {$validated['reason']}");
    }

    /**
     * Lever la suspension
     */
    public function unsuspend($id)
    {
        $user = User::findOrFail($id);
        $user->unsuspend();

        /*
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('user_unsuspended');
        */

        return redirect()->back()
            ->with('success', "La suspension de {$user->nom_complet} a été levée.");
    }

    /**
     * Verrouiller le compte
     */
    public function lock(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Impossible de verrouiller un administrateur.');
        }

        $minutes = $request->input('minutes', 30);
        $user->lock($minutes);

        return redirect()->back()
            ->with('success', "Le compte de {$user->nom_complet} a été verrouillé pour {$minutes} minutes.");
    }

    /**
     * Déverrouiller le compte
     */
    public function unlock($id)
    {
        $user = User::findOrFail($id);
        $user->unlock();

        return redirect()->back()
            ->with('success', "Le compte de {$user->nom_complet} a été déverrouillé.");
    }

    /**
     * Marquer comme vérifié
     */
    public function verify($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', "{$user->nom_complet} a été vérifié manuellement.");
    }

    /**
     * Retirer la vérification
     */
    public function unverify($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_verified' => false,
            'email_verified_at' => null,
        ]);

        return redirect()->back()
            ->with('success', "La vérification de {$user->nom_complet} a été retirée.");
    }

    /**
     * Réinitialiser le risk score
     */
    public function resetRiskScore($id)
    {
        $user = User::findOrFail($id);
        $user->resetRiskScore();

        /*
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('risk_score_reset');
        */

        return redirect()->back()
            ->with('success', "Le risk score de {$user->nom_complet} a été réinitialisé.");
    }

    /**
     * Ajouter un drapeau suspect
     */
    public function addSuspiciousFlag(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'flag' => 'required|string|max:100',
            'reason' => 'required|string|max:500',
        ]);

        $user->addSuspiciousFlag($validated['flag']);

        /*
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'flag' => $validated['flag'],
                'reason' => $validated['reason']
            ])
            ->log('suspicious_flag_added');
        */

        return redirect()->back()
            ->with('success', "Drapeau suspect ajouté : {$validated['flag']}");
    }

    /**
     * Supprimer tous les drapeaux suspects
     */
    public function clearSuspiciousFlags($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'suspicious_flags' => null,
            'last_suspicious_activity' => null,
        ]);

        return redirect()->back()
            ->with('success', "Tous les drapeaux suspects ont été effacés.");
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // TODO: Envoyer email de notification

        return redirect()->back()
            ->with('success', "Le mot de passe de {$user->nom_complet} a été réinitialisé.");
    }

    /**
     * Supprimer (soft delete)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Protections
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Impossible de supprimer un administrateur.');
        }

        if ($user->id_user === auth()->id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $name = $user->nom_complet;
        $user->delete();

        /*
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('user_soft_deleted');
        */

        return redirect()->route('admin.users.index')
            ->with('success', "{$name} a été supprimé (soft delete).");
    }

    /**
     * Restaurer un utilisateur supprimé
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            return redirect()->back()->with('error', "Cet utilisateur n'est pas supprimé.");
        }

        $user->restore();

        /*
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('user_restored');
        */

        return redirect()->back()
            ->with('success', "{$user->nom_complet} a été restauré avec succès.");
    }

    /**
     * Suppression définitive (force delete)
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Protection ultime
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Impossible de supprimer définitivement un administrateur.');
        }

        $name = $user->nom_complet;

        DB::transaction(function () use ($user) {
            // Supprimer les relations en cascade si nécessaire
            $user->commandes()->delete();
            $user->avis()->delete();
            $user->favoris()->delete();
            $user->notifications()->delete();

            $user->forceDelete();
        });

        /*
        activity()
            ->withProperties(['user_name' => $name])
            ->causedBy(auth()->user())
            ->log('user_force_deleted');
        */

        return redirect()->route('admin.users.index')
            ->with('success', "{$name} a été supprimé définitivement de la base de données.");
    }

    /**
     * Actions groupées (bulk actions)
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,suspend,activate,verify,lock',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id_user',
            'reason' => 'required_if:action,suspend|string|max:500',
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];

        // Protéger les admins
        $users = User::whereIn('id_user', $userIds)
            ->where('role', '!=', 'admin')
            ->where('id_user', '!=', auth()->id())
            ->get();

        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun utilisateur valide sélectionné.');
        }

        $count = 0;

        foreach ($users as $user) {
            switch ($action) {
                case 'delete':
                    $user->delete();
                    $count++;
                    break;
                case 'suspend':
                    $user->update([
                        'status' => 'suspendu',
                        'statut_compte' => 'suspendu',
                        'suspended_at' => now(),
                        'suspension_reason' => $validated['reason'] ?? 'Action groupée'
                    ]);
                    $count++;
                    break;
                case 'activate':
                    $user->update([
                        'status' => 'actif',
                        'statut_compte' => 'actif'
                    ]);
                    $count++;
                    break;
                case 'verify':
                    $user->update(['is_verified' => true, 'email_verified_at' => now()]);
                    $count++;
                    break;
                case 'lock':
                    $user->lock(30);
                    $count++;
                    break;
            }
        }

        return redirect()->back()
            ->with('success', "Action '{$action}' appliquée à {$count} utilisateur(s).");
    }

    /**
     * Export des utilisateurs (CSV)
     */
    public function export(Request $request)
    {
        $query = User::withTrashed();

        // Appliquer les mêmes filtres que l'index
        if ($role = $request->input('role')) {
            $query->byRole($role);
        }
        if ($status = $request->input('status')) {
            $query->byStatus($status);
        }
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $users = $query->get();

        $filename = 'users_export_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // En-têtes
            fputcsv($file, [
                'ID',
                'Nom complet',
                'Email',
                'Téléphone',
                'Rôle',
                'Statut',
                'Vérifié',
                'Risk Score',
                'Date création',
                'Dernière connexion'
            ]);

            // Données
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id_user,
                    $user->nom_complet,
                    $user->email,
                    $user->telephone,
                    $user->role,
                    $user->status,
                    $user->is_verified ? 'Oui' : 'Non',
                    $user->risk_score,
                    $user->date_creation?->format('Y-m-d H:i'),
                    $user->date_derniere_connexion?->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}