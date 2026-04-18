<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Vendeur;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // summary dashboard for admin
        $totalVendeurs = Vendeur::count('*');
        $pendingVendeursCount = Vendeur::where('statut_verification', '=', 'en_cours')->count('*');
        $latestVendeurs = Vendeur::latest('date_inscription')->limit(6)->get();
        $totalRevenue = \App\Models\Commande::where('statut', '=', 'termine')->sum('montant_total');

        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function vendeurs()
    {
        $vendeurs = Vendeur::orderBy('date_inscription', 'desc')->paginate(20);
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function approveVendeur($id)
    {
        $v = Vendeur::findOrFail($id);
        $v->statut_verification = 'verifie';
        $v->date_verification = now();
        $v->actif = true;
        $v->save();

        // Update user role
        if ($v->user && $v->user->role !== 'admin') {
            $v->user->update(['role' => 'vendeur']);
        }

        // Create internal notification
        if ($v->user) {
            \App\Models\Notification::create([
                'id_utilisateur' => $v->user->id_user,
                'type_notification' => 'vendeur_approuve',
                'titre' => 'Félicitations ! Votre compte vendeur est approuvé.',
                'message' => "Votre boutique \"{$v->nom_commercial}\" a été validée par notre équipe. Vous pouvez maintenant commencer à vendre.",
                'id_vendeur' => $v->id_vendeur,
                'lue' => false,
                'date_creation' => now(),
            ]);
        }

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }
}
