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

            // Check if the authenticated user owns this vendor account
            if (!auth()->check() || !auth()->user()->vendeur || auth()->user()->vendeur->id_vendeur !== $vendeur->id_vendeur) {
                abort(403, 'Accès non autorisé à cette boutique');
            }

            // Store vendor in request for easy access
            $request->merge(['current_vendor' => $vendeur]);
        }

        return $next($request);
    }
}
