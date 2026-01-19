<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role ?? 'client', ['admin', 'super_admin'])) {
            // Option: redirect to home or 403
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Admins only.'], 403);
            }
            return redirect('/')->with('status', 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
