<?php

if (!function_exists('vendor_route')) {
    /**
     * Generate a vendor route with slug
     */
    function vendor_route($name, $parameters = [])
    {
        $user = auth()->user();
        $vendeur = request()->get('current_vendor') ?? ($user ? $user->vendeur : null);

        // Ensure parameters is an array (to handle route('...', $id) style)
        if (!is_array($parameters)) {
            $paramName = 'id';
            // Logic to determine the param name based on route name
            if (str_contains($name, 'coupons'))
                $paramName = 'coupon';

            $parameters = [$paramName => $parameters];
        }

        if (!$vendeur || !$vendeur->slug) {
            // If we are strictly using slug routes, we should not fallback to legacy names 
            // if the legacy route doesn't exist.
            $legacyName = str_replace('.slug.', '.', $name);
            
            try {
                return route($legacyName, $parameters);
            } catch (\Exception $e) {
                // If it fails, we are probably in a staff context without slug in URL yet
                // or the legacy route simply doesn't exist.
                // Return home as safe fallback
                return route('home');
            }
        }

        // Add vendor_slug to parameters
        $parameters = array_merge(['vendor_slug' => $vendeur->slug], $parameters);

        return route($name, $parameters);
    }
}
