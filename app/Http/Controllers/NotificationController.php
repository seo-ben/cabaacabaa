<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Fetch unread notifications for the user.
     */
    public function getUnread(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([]);
        }

        $notifications = Notification::where('id_utilisateur', Auth::id())
            ->where('lue', false)
            ->latest('date_creation')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark all as read.
     */
    public function markAllRead()
    {
        Notification::where('id_utilisateur', Auth::id())
            ->where('lue', false)
            ->update([
                'lue' => true,
                'date_lecture' => now()
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id_utilisateur', Auth::id())
            ->findOrFail($id);

        $notification->update([
            'lue' => true,
            'date_lecture' => now()
        ]);

        return response()->json(['success' => true]);
    }
}
