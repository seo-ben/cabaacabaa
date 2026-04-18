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

        try {
            Storage::append('newsletter_subscriptions.txt', $data['email']);
        } catch (\Throwable $e) {}

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Merci de faire confiance à CabaaCabaa'
            ]);
        }

        return back()->with('success', 'Merci de faire confiance à CabaaCabaa');
    }
}
