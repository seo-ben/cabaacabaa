<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useTailwind();

        // Share system currency globally (safe fallback if migrations not run)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                view()->share('currency', \App\Models\AppSetting::get('system_currency', 'XOF'));
            } else {
                view()->share('currency', 'XOF');
            }
        } catch (\Exception $e) {
            view()->share('currency', 'XOF');
        }
    }
}
