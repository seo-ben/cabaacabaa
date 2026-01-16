<?php

namespace App\Http\Controllers;

use App\Models\Vendeur;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // summary dashboard for admin
        $totalVendeurs = Vendeur::count();
        $pendingVendeursCount = Vendeur::where('statut_verification', 'en_cours')->count();
        $vendeursRecent = Vendeur::latest('date_inscription')->limit(6)->get();
        $totalRevenue = \App\Models\Commande::where('statut', 'termine')->sum('montant_total');

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
        $v->save();
        return redirect()->back()->with('status', 'Vendeur approuvé.');
    }
}
