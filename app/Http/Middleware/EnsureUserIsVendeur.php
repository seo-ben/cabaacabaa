<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsVendeur
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ($user->role ?? null) !== 'vendeur') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Vendeur only.'], 403);
            }

            return redirect()->route('login')->with('error', 'Accès réservé aux vendeurs.');
        }

        return $next($request);
    }
}
