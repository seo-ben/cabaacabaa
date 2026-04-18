<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendeur;
use App\Models\Commande;
use App\Models\Plat;

class VendorController extends Controller
{
    private function getVendor(Request $request)
    {
        return Vendeur::where('id_proprio', $request->user()->id_user)->firstOrFail();
    }

    public function dashboard(Request $request)
    {
        $vendeur = $this->getVendor($request);
        
        $stats = [
            'total_orders' => Commande::where('id_vendeur', $vendeur->id_vendeur)->count(),
            'today_orders' => Commande::where('id_vendeur', $vendeur->id_vendeur)->whereDate('date_commande', today())->count(),
            'revenue' => Commande::where('id_vendeur', $vendeur->id_vendeur)->where('statut', 'livree')->sum('montant_total')
        ];

        return response()->json(['vendor' => $vendeur, 'stats' => $stats]);
    }

    public function orders(Request $request)
    {
        $vendeur = $this->getVendor($request);
        $orders = Commande::with('lignes.plat')->where('id_vendeur', $vendeur->id_vendeur)->orderBy('date_commande', 'desc')->get();
        return response()->json($orders);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $vendeur = $this->getVendor($request);
        $request->validate(['status' => 'required|string']);
        
        $order = Commande::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);
        $order->statut = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status updated', 'order' => $order]);
    }

    public function plats(Request $request)
    {
        $vendeur = $this->getVendor($request);
        $plats = Plat::where('id_vendeur', $vendeur->id_vendeur)->get();
        return response()->json($plats);
    }

    public function togglePlatAvailability(Request $request, $id)
    {
        $vendeur = $this->getVendor($request);
        $plat = Plat::where('id_vendeur', $vendeur->id_vendeur)->findOrFail($id);
        $plat->is_available = !$plat->is_available;
        $plat->save();

        return response()->json(['message' => 'Plat availability toggled', 'plat' => $plat]);
    }

    public function toggleStoreStatus(Request $request)
    {
        $vendeur = $this->getVendor($request);
        $request->validate(['status' => 'required|in:ouvert,ferme,occupe']);
        
        $vendeur->statut = $request->status;
        $vendeur->save();

        return response()->json(['message' => 'Store status updated', 'vendor' => $vendeur]);
    }
}
