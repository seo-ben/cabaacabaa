<?php

namespace App\Http\Controllers;

use App\Models\AvisEvaluation;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_commande' => 'required|exists:commandes,id_commande',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $commande = Commande::findOrFail($request->id_commande);

        // Security check
        $clientId = Auth::id() ?? $commande->id_client;

        // Check if a review already exists for this order
        $existing = AvisEvaluation::where('id_commande', $commande->id_commande)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Vous avez déjà noté cette commande.');
        }

        AvisEvaluation::create([
            'id_client' => $clientId,
            'id_vendeur' => $commande->id_vendeur,
            'id_commande' => $commande->id_commande,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'statut_avis' => 'visible',
            'date_publication' => now(),
        ]);

        // Update Vendor Stats
        $vendeur = $commande->vendeur;
        if ($vendeur) {
            $stats = AvisEvaluation::where('id_vendeur', $vendeur->id_vendeur)
                ->where('statut_avis', 'visible')
                ->selectRaw('AVG(note) as avg_note, COUNT(*) as count_avis')
                ->first();

            $vendeur->update([
                'note_moyenne' => $stats->avg_note ?? 0,
                'nombre_avis' => $stats->count_avis ?? 0,
            ]);
        }

        return redirect()->back()->with('success', 'Merci pour votre avis !');
    }
}
