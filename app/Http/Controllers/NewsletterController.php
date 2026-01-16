<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Try to persist subscription to a simple file (non-blocking)
        try {
            Storage::append('newsletter_subscriptions.txt', $data['email']);
        } catch (\Throwable $e) {
            // ignore storage errors in minimal implementation
        }

        return back()->with('success', 'Merci ! Vous êtes inscrit(e) à la newsletter.');
    }
}
