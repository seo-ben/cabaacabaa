<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendeur;
use App\Models\Plat;
use App\Models\Commande;

class ClientController extends Controller
{
    public function restaurants()
    {
        $restaurants = Vendeur::where('statut', 'ouvert')->get();
        return response()->json($restaurants);
    }

    public function restaurantPlats($id)
    {
        $plats = Plat::where('id_vendeur', $id)->where('is_available', true)->get();
        return response()->json($plats);
    }

    public function searchPlats(Request $request)
    {
        $query = $request->input('q');
        $plats = Plat::where('nom', 'LIKE', "%$query%")->where('is_available', true)->get();
        return response()->json($plats);
    }

    public function orders(Request $request)
    {
        $orders = Commande::where('id_client', $request->user()->id_user)->orderBy('date_commande', 'desc')->get();
        return response()->json($orders);
    }

    public function orderDetails(Request $request, $id)
    {
        $order = Commande::with(['lignes.plat', 'vendeur'])->where('id_client', $request->user()->id_user)->findOrFail($id);
        return response()->json($order);
    }

    public function placeOrder(Request $request)
    {
        // Placeholder for order logic
        return response()->json(['message' => 'Order placed successfully'], 201);
    }

    public function checkout(Request $request, $id)
    {
        // Placeholder for payment
        return response()->json(['message' => 'Checkout valid']);
    }
}
