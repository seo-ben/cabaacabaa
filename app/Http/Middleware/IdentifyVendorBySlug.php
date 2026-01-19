<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Vendeur;
use Symfony\Component\HttpFoundation\Response;

class IdentifyVendorBySlug
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('vendor_slug');

        if ($slug) {
            $vendeur = Vendeur::where('slug', $slug)->first();

            if (!$vendeur) {
                abort(404, 'Boutique introuvable');
            }

            // Check if the authenticated user owns this vendor account OR is a staff member
            $user = auth()->user();
            if (!$user) {
                abort(403, 'Non authentifié');
            }

            $isOwner = $user->vendeur && $user->vendeur->id_vendeur === $vendeur->id_vendeur;
            $isStaff = \App\Models\VendorStaff::where('id_user', $user->id_user)
                ->where('id_vendeur', $vendeur->id_vendeur)
                ->exists();

            if (!$isOwner && !$isStaff) {
                // Allow Admin to view any dashboard? Optional using isSuperAdmin()
                if ($user->role === 'super_admin') {
                    // Pass
                } else {
                    abort(403, 'Accès non autorisé à cette boutique');
                }
            }

            // Store vendor in request for easy access
            $request->merge(['current_vendor' => $vendeur]);
        }

        return $next($request);
    }
}
