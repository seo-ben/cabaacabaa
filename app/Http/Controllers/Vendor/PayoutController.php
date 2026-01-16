<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PayoutController extends Controller
{
    public function index()
    {
        $vendeur = Auth::user()->vendeur;
        $payouts = PayoutRequest::where('id_vendeur', $vendeur->id_vendeur)
            ->latest()
            ->paginate(10);

        return view('vendeur.payouts.index', compact('vendeur', 'payouts'));
    }

    public function store(Request $request)
    {
        $vendeur = Auth::user()->vendeur;

        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric|min:5000', // Minimum payout amount
            'methode_paiement' => 'required|in:momo,flooz,banque,cheque',
            'informations_paiement' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->montant > $vendeur->wallet_balance) {
            return redirect()->back()->with('error', 'Solde insuffisant pour cette demande.');
        }

        // Create payout request
        PayoutRequest::create([
            'id_vendeur' => $vendeur->id_vendeur,
            'montant' => $request->montant,
            'methode_paiement' => $request->methode_paiement,
            'informations_paiement' => $request->informations_paiement,
            'statut' => 'en_attente',
        ]);

        // Deduct from wallet balance
        $vendeur->wallet_balance -= $request->montant;
        $vendeur->save();

        return redirect()->back()->with('success', 'Votre demande de paiement a été soumise.');
    }
}
