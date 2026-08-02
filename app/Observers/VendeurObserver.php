<?php

namespace App\Observers;

use App\Models\Vendeur;
use App\Models\Plat;
use Illuminate\Support\Facades\Cache;

class VendeurObserver
{
    /**
     * Invalider le cache des vendeurs proches
     */
    public function clearNearbyVendorsCache(): void
    {
        try {
            if (Cache::supportsTags()) {
                Cache::tags(['nearby_vendors'])->flush();
            } else {
                Cache::forget('nearby_vendors_all');
            }
        } catch (\Exception $e) {
            // Ignorer silencieusement si la connexion cache échoue temporairement
        }
    }

    public function saved($model): void
    {
        $this->clearNearbyVendorsCache();
    }

    public function deleted($model): void
    {
        $this->clearNearbyVendorsCache();
    }

    public function updated($model): void
    {
        $this->clearNearbyVendorsCache();
    }
}
