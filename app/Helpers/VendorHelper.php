<?php

if (!function_exists('vendor_route')) {
    /**
     * Generate a vendor route with slug
     */
    function vendor_route($name, $parameters = [])
    {
        $vendeur = auth()->user()->vendeur ?? null;

        // Ensure parameters is an array (to handle route('...', $id) style)
        if (!is_array($parameters)) {
            $paramName = 'id';
            // Logic to determine the param name based on route name
            if (str_contains($name, 'coupons'))
                $paramName = 'coupon';

            $parameters = [$paramName => $parameters];
        }

        if (!$vendeur || !$vendeur->slug) {
            // Fallback to old route
            return route(str_replace('.slug.', '.', $name), $parameters);
        }

        // Add vendor_slug to parameters
        $parameters = array_merge(['vendor_slug' => $vendeur->slug], $parameters);

        return route($name, $parameters);
    }
}
