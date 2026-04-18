<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PayoutController extends Controller
{
    // ============================================================================
    // FONCTIONNALITÉ WALLET/PAYOUT - TEMPORAIREMENT DÉSACTIVÉE
    // ============================================================================
    // TODO: Réactiver quand le système de paiement en ligne sera opérationnel
    // ============================================================================
    
    public function index(Request $request)
    {
        // Fonctionnalité désactivée temporairement
        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        
        /*
        $vendeur = Auth::user()->vendeur;
        $payouts = PayoutRequest::where('id_vendeur', $vendeur->id_vendeur)
            ->latest()
            ->paginate(10);

        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
        */
    }

    public function store(Request $request)
    {
        // Fonctionnalité désactivée temporairement
        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        
        /*
        $vendeur = Auth::user()->vendeur;

        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric|min:5000', // Minimum payout amount
            'methode_paiement' => 'required|in:momo,flooz,banque,cheque',
            'informations_paiement' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        }

        if ($request->montant > $vendeur->wallet_balance) {
            return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
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

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        */
    }
}
