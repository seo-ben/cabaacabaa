<?php

namespace App\Http\Controllers;

use App\Models\Vendeur;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // summary dashboard for admin
        $totalVendeurs = Vendeur::count('*');
        $pendingVendeursCount = Vendeur::where('statut_verification', '=', 'en_cours')->count('*');
        $vendeursRecent = Vendeur::latest('date_inscription')->limit(6)->get();
        $totalRevenue = \App\Models\Commande::where('statut', '=', 'termine')->sum('montant_total');

        return view('admin.dashboard', compact('totalVendeurs', 'pendingVendeursCount', 'vendeursRecent', 'totalRevenue'));
    }

    public function vendeurs()
    {
        $vendeurs = Vendeur::orderBy('date_inscription', 'desc')->paginate(20);
        return view('admin.vendeurs.index', compact('vendeurs'));
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

        return redirect()->back()->with('status', 'Vendeur approuvé et notifié.');
    }
}
