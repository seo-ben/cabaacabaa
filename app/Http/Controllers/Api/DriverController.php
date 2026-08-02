<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\Driver;

class DriverController extends Controller
{
    public function available()
    {
        // Placeholder for available deliveries near driver
        return response()->json([]);
    }

    public function active(Request $request)
    {
        $activeOrders = Commande::where('id_livreur', $request->user()->id_user)
            ->whereIn('statut', ['en_livraison', 'attribuee'])
            ->get();
        return response()->json($activeOrders);
    }

    public function accept(Request $request, $id)
    {
        $order = Commande::findOrFail($id);
        $order->id_livreur = $request->user()->id_user;
        $order->statut = 'en_livraison';
        $order->save();

        return response()->json(['message' => 'Delivery accepted']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $order = Commande::where('id_livreur', $request->user()->id_user)->findOrFail($id);
        $order->statut = $request->status;
        $order->save();

        return response()->json(['message' => 'Status updated']);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        // Dispatcher le job sur la file dédiée 'geolocation'
        \App\Jobs\ProcessDriverLocationJob::dispatch(
            $request->user()->id_user,
            (float) $request->lat,
            (float) $request->lng
        );

        return response()->json(['message' => 'Location updated', 'queued' => true]);
    }
}
