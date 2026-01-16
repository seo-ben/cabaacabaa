<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\OrderMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderChatController extends Controller
{
    /**
     * Get messages for an order
     */
    public function getMessages($orderId)
    {
        $order = Commande::findOrFail($orderId);

        // Check authorization (client or vendor)
        if (Auth::id() != $order->id_client && (!Auth::user()->vendeur || Auth::user()->vendeur->id_vendeur != $order->id_vendeur)) {
            abort(403, 'Non autorisé');
        }

        $messages = OrderMessage::where('id_commande', $orderId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read for the current user
        OrderMessage::where('id_commande', $orderId)
            ->where('id_user', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, $orderId)
    {
        $order = Commande::findOrFail($orderId);

        // Check authorization
        if (Auth::id() != $order->id_client && (!Auth::user()->vendeur || Auth::user()->vendeur->id_vendeur != $order->id_vendeur)) {
            abort(403, 'Non autorisé');
        }

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = OrderMessage::create([
            'id_commande' => $orderId,
            'id_user' => Auth::id(),
            'message' => $request->message,
            'is_read' => false
        ]);

        $message->load('user');

        return response()->json($message, 201);
    }

    /**
     * Get unread message count for an order
     */
    public function getUnreadCount($orderId)
    {
        $order = Commande::findOrFail($orderId);

        if (Auth::id() != $order->id_client && (!Auth::user()->vendeur || Auth::user()->vendeur->id_vendeur != $order->id_vendeur)) {
            abort(403);
        }

        $count = OrderMessage::where('id_commande', $orderId)
            ->where('id_user', '!=', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
