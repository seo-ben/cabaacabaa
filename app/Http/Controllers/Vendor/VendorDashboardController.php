<?php

namespace App\Http\Controllers\Vendor;

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
            return redirect()->route('home')->with('error', 'Profil vendeur introuvable.');
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

        // Statistiques d'utilisation de l'abonnement
        $usage = [
            'plats' => [
                'used' => $totalPlats,
                'limit' => $vendeur->getPlatLimit(),
                'percent' => $vendeur->getPlatLimit() > 0 ? min(100, ($totalPlats / $vendeur->getPlatLimit()) * 100) : 100
            ],
            'staff' => [
                'used' => $vendeur->staff()->count(),
                'limit' => $vendeur->getStaffLimit(),
                'percent' => $vendeur->getStaffLimit() > 0 ? min(100, ($vendeur->staff()->count() / $vendeur->getStaffLimit()) * 100) : 100
            ],
            'coupons' => [
                'used' => $vendeur->coupons()->count(),
                'limit' => $vendeur->getCouponLimit(),
                'percent' => $vendeur->getCouponLimit() > 0 ? min(100, ($vendeur->coupons()->count() / $vendeur->getCouponLimit()) * 100) : 100
            ]
        ];

        return view('vendeur.dashboard', compact(
            'vendeur',
            'totalSales',
            'activeOrders',
            'totalPlats',
            'recentOrders',
            'hasCategories',
            'usage'
        ));
    }
}
