<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorDashboardController extends Controller
{
    /**
     * Dashboard principal du vendeur.
     */
    public function index(Request $request, $vendor_slug)
    {
        $vendeur = $request->get('current_vendor');

        if (!$vendeur) {
            // Fallback for legacy routes if they don't use IdentifyVendorBySlug middleware
            $vendeur = Auth::user()->vendeur;
        }

        if (!$vendeur) {
            return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        }

        // Statistiques de base
        $totalSales = Commande::where('id_vendeur', $vendeur->id_vendeur)
            ->where('statut', 'termine')
            ->sum('montant_total');

        $activeOrders = Commande::where('id_vendeur', $vendeur->id_vendeur)
            ->whereIn('statut', ['en_attente', 'en_preparation', 'pret'])
            ->count();

        $totalPlats = Plat::where('id_vendeur', $vendeur->id_vendeur)->count();

        // Dernières commandes
        $recentOrders = Commande::where('id_vendeur', $vendeur->id_vendeur)
            ->with('client')
            ->latest('date_commande')
            ->limit(5)
            ->get();

        // Vérifier si le vendeur a configuré ses spécialités
        $hasCategories = $vendeur->categories()->exists();

        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }
}
