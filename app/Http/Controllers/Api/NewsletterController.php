<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

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

        return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
    }
}
