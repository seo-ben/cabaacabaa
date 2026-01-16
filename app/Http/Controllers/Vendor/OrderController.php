<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Liste des commandes du vendeur.
     */
    public function index(Request $request)
    {
        $vendeur = Auth::user()->vendeur;
        $status = $request->get('status');
        $search = $request->get('search');

        $query = Commande::where('id_vendeur', $vendeur->id_vendeur)
            ->with(['client', 'lignes'])
            ->latest('date_commande');

        // Filtres
        if ($status) {
            $query->where('statut', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_commande', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Stats pour le tableau de bord
        $stats = [
            'total' => Commande::where('id_vendeur', $vendeur->id_vendeur)->count(),
            'en_attente' => Commande::where('id_vendeur', $vendeur->id_vendeur)->where('statut', 'en_attente')->count(),
            'en_preparation' => Commande::where('id_vendeur', $vendeur->id_vendeur)->where('statut', 'en_preparation')->count(),
            'pret' => Commande::where('id_vendeur', $vendeur->id_vendeur)->where('statut', 'pret')->count(),
            'termine' => Commande::where('id_vendeur', $vendeur->id_vendeur)->where('statut', 'termine')->count(),
        ];

        $orders = $query->paginate(10);

        return view('vendeur.orders.index', compact('orders', 'status', 'search', 'stats'));
    }

    /**
     * Mettre à jour le statut d'une commande.
     */
    public function updateStatus(Request $request, $id)
    {
        $vendeur = Auth::user()->vendeur;
        $order = Commande::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);

        $request->validate([
            'statut' => 'required|in:en_attente,en_preparation,pret,termine,annule'
        ]);

        $previousStatus = $order->statut;
        $order->statut = $request->statut;

        // Logique auto dates
        if ($request->statut === 'en_preparation' && !$order->heure_preparation_debut) {
            $order->heure_preparation_debut = now();
        }
        if ($request->statut === 'pret' && !$order->heure_prete) {
            $order->heure_prete = now();
        }

        $order->save();

        // LOGIQUE FINANCIÈRE : Créditer le wallet si la commande est terminée et payée en ligne
        if ($request->statut === 'termine' && $previousStatus !== 'termine') {
            if ($order->mode_paiement_prevu === 'mobile_money') {
                // Taux de commission (10% par défaut sur le montant des plats uniquement)
                $tauxCommission = 0.10;
                $commission = $order->montant_plats * $tauxCommission;

                // Montant à reverser = Total (Plats + Livraison) - Commission
                // Le vendeur récupère ses frais de livraison s'il gère la livraison
                $montantVendeur = intval($order->montant_total - $commission);

                // 1. Créditer le vendeur
                $vendeur->increment('wallet_balance', $montantVendeur);

                // 2. Enregistrer la commission comme revenu plateforme
                $order->frais_service = $commission;
                $order->save();

                // 3. Enregistrer la transaction pour historique (Crédit Net)
                \App\Models\TransactionFinanciere::create([
                    'id_commande' => $order->id_commande,
                    'id_vendeur' => $vendeur->id_vendeur,
                    'type_transaction' => 'credit_vente',
                    'montant' => $montantVendeur,
                    'devise' => 'XOF',
                    'statut' => 'succes',
                    'date_transaction' => now(),
                    'reference_paiement' => 'WALLET-' . $order->numero_commande,
                    'notes' => 'Crédit vente (Total: ' . $order->montant_total . ' - Com 10%: ' . $commission . ')'
                ]);
            }
        }

        // Notify customer in real-time
        event(new \App\Events\OrderStatusChanged($order));

        return back()->with('success', 'Statut de la commande mis à jour !');
    }
}
