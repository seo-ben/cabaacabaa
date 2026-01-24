<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\OrderMessage;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderChatController extends Controller
{
    /**
     * Check if a user is authorized to view/chat about an order
     */
    private function isAuthorized($user, $order)
    {
        // Session-based authorization for guests/client tracking
        if (session('viewed_order_' . $order->id_commande)) {
            return true;
        }

        if (!$user) return false;

        // Admin/Super Admin check
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }

        // Client check
        if ($user->id_user == $order->id_client) {
            return true;
        }

        // Vendor Owner check
        if ($user->vendeur && $user->vendeur->id_vendeur == $order->id_vendeur) {
            return true;
        }

        // Vendor Staff check
        $isStaff = \App\Models\VendorStaff::where('id_user', $user->id_user)
            ->where('id_vendeur', $order->id_vendeur)
            ->exists();

        return $isStaff;
    }

    /**
     * Get messages for an order
     */
    public function getMessages($orderId)
    {
        $order = Commande::findOrFail($orderId);

        if (!$this->isAuthorized(Auth::user(), $order)) {
            abort(403, 'Non autorisé');
        }

        $messages = OrderMessage::where('id_commande', $orderId)
            ->with('user:id_user,name,photo_profil')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read for the current user
        $myId = Auth::id();
        OrderMessage::where('id_commande', $orderId)
            ->where(function ($q) use ($myId) {
                if ($myId) {
                    $q->where('id_user', '!=', $myId)->orWhereNull('id_user');
                } else {
                    $q->whereNotNull('id_user');
                }
            })
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

        if (!$this->isAuthorized(Auth::user(), $order)) {
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

        // Notify participants
        $this->notifyParticipants($order, $message);

        $message->load('user:id_user,name,photo_profil');

        return response()->json($message, 201);
    }

    /**
     * Notify relevant parties about a new message
     */
    private function notifyParticipants($order, $message)
    {
        $senderId = Auth::id();
        $vendeur = $order->vendeur;
        
        // Notify Vendor and Staff if message is from guest or client
        if (!$senderId || $senderId == $order->id_client) {
            // Notify Vendor Owner
            if ($vendeur->id_user) {
                Notification::create([
                    'id_utilisateur' => $vendeur->id_user,
                    'type_notification' => 'nouveau_message',
                    'titre' => 'Nouveau message client',
                    'message' => "Client: \"$message->message\"",
                    'id_commande' => $order->id_commande,
                    'lue' => false,
                    'date_creation' => now(),
                ]);
            }
            
            // Notify Vendor Staff
            $staffIds = \App\Models\VendorStaff::where('id_vendeur', $order->id_vendeur)->pluck('id_user');
            foreach($staffIds as $staffId) {
                Notification::create([
                    'id_utilisateur' => $staffId,
                    'type_notification' => 'nouveau_message',
                    'titre' => 'Nouveau message client',
                    'message' => "Client: \"$message->message\"",
                    'id_commande' => $order->id_commande,
                    'lue' => false,
                    'date_creation' => now(),
                ]);
            }
        }
        
        // Notify Client if message is from vendor/admin
        if ($senderId && $senderId != $order->id_client && $order->id_client) {
            Notification::create([
                'id_utilisateur' => $order->id_client,
                'type_notification' => 'nouveau_message',
                'titre' => 'Nouveau message du restaurant',
                'message' => "Vendeur: \"$message->message\"",
                'id_commande' => $order->id_commande,
                'lue' => false,
                'date_creation' => now(),
            ]);
        }
    }

    /**
     * Get unread message count for an order
     */
    public function getUnreadCount($orderId)
    {
        $order = Commande::findOrFail($orderId);

        if (!$this->isAuthorized(Auth::user(), $order)) {
            abort(403);
        }

        $myId = Auth::id();
        $count = OrderMessage::where('id_commande', $orderId)
            ->where(function ($q) use ($myId) {
                if ($myId) {
                    $q->where('id_user', '!=', $myId)->orWhereNull('id_user');
                } else {
                    $q->whereNotNull('id_user');
                }
            })
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
