<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Models\TransactionFinanciere;
use App\Models\Commande;
use App\Models\Vendeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Financial Overview Dashboard.
     */
    public function index()
    {
        $stats = [
            'total_volume' => Commande::where('statut', 'termine')->sum('montant_total'),
            'platform_revenue' => Commande::where('statut', 'termine')->sum('frais_service'),
            'pending_payouts' => PayoutRequest::where('statut', 'en_attente')->sum('montant'),
            'total_payouts' => PayoutRequest::where('statut', 'complete')->sum('montant'),
        ];

        $recentTransactions = TransactionFinanciere::with(['vendeur', 'commande'])
            ->latest('date_transaction')
            ->limit(10)
            ->get();

        $pendingPayouts = PayoutRequest::with('vendeur')
            ->where('statut', 'en_attente')
            ->latest()
            ->get();

        return view('admin.finance.index', compact('stats', 'recentTransactions', 'pendingPayouts'));
    }

    /**
     * List all payout requests.
     */
    public function payouts(Request $request)
    {
        $status = $request->get('status');
        $query = PayoutRequest::with('vendeur')->latest();

        if ($status) {
            $query->where('statut', $status);
        }

        $payouts = $query->paginate(20);

        return view('admin.finance.payouts', compact('payouts', 'status'));
    }

    /**
     * Handle Payout Request Approval/Rejection.
     */
    public function updatePayout(Request $request, $id)
    {
        $payout = PayoutRequest::findOrFail($id);
        $vendeur = $payout->vendeur;

        $request->validate([
            'statut' => 'required|in:complete,rejete',
            'notes_admin' => 'nullable|string'
        ]);

        if ($payout->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        DB::beginTransaction();
        try {
            $payout->statut = $request->statut;
            $payout->notes_admin = $request->notes_admin;
            $payout->save();

            if ($request->statut === 'rejete') {
                // Return amount to wallet if rejected
                $vendeur->increment('wallet_balance', $payout->montant);
            }

            DB::commit();
            return back()->with('status', 'La demande de retrait a été ' . ($request->statut == 'complete' ? 'validée' : 'rejetée') . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors du traitement.');
        }
    }

    /**
     * List all financial transactions.
     */
    public function transactions(Request $request)
    {
        $search = $request->get('search');
        $query = TransactionFinanciere::with(['vendeur', 'commande'])->latest('date_transaction');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_paiement', 'LIKE', "%{$search}%")
                    ->orWhereHas('vendeur', function ($vq) use ($search) {
                        $vq->where('nom_commercial', 'LIKE', "%{$search}%");
                    });
            });
        }

        $transactions = $query->paginate(30);

        return view('admin.finance.transactions', compact('transactions', 'search'));
    }
}
