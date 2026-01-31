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

        // Register Gates from Permissions
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
                // Fetch all permissions once to avoid N+1 in loop ideally, but for definition we can just loop if efficient enough or use a closure.
                // Better: define a global "before" gate or dynamic gate interception if using a package, 
                // but for native Laravel, defining specific gates is good for @can('key').

                // However, fetching ALL permissions on every boot might be heavy if many.
                // A better approach for Gate definition:
                \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
                    if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                        return true;
                    }
                });

                // Dynamic Gate definition for each permission in DB is possible but expensive on boot.
                // Instead, we can use a general check or let the User model handle it.
                // But standard Laravel usage is Gate::define('key', ...).
                // Optimization: Cache permissions? For now, we'll try to keep it simple.
                // Actually, the standard way without a package is often to defining a policy or just using the helper.
                // But to work with @can('view_vendors'), we MUST define the gate 'view_vendors'.

                // Let's fetch just the keys.
                $permissionKeys = \App\Models\Permission::pluck('key');
                foreach ($permissionKeys as $key) {
                    \Illuminate\Support\Facades\Gate::define($key, function ($user) use ($key) {
                        return $user->hasPermission($key);
                    });
                }
            }
        } catch (\Exception $e) {
            // Migrations not run yet or DB error
        }

        // Share system currency globally (safe fallback if migrations not run)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                view()->share('currency', \App\Models\AppSetting::get('system_currency', 'XOF'));
                view()->share('siteName', \App\Models\AppSetting::get('site_name', 'CabaaCabaa'));

                $logoPath = \App\Models\AppSetting::get('site_logo');
                $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('assets/logo/logo-cabaa.png'); // Fallback to default
                view()->share('siteLogo', $logoUrl);

                $faviconPath = \App\Models\AppSetting::get('site_favicon');
                $faviconUrl = $faviconPath ? asset('storage/' . $faviconPath) : null;
                view()->share('siteFavicon', $faviconUrl);
            } else {
                view()->share('currency', 'XOF');
                view()->share('siteName', 'CabaaCabaa');
                view()->share('siteLogo', asset('assets/logo/logo-cabaa.png'));
                view()->share('siteFavicon', null);
            }
        } catch (\Exception $e) {
            view()->share('currency', 'XOF');
        }

        // View Composer for Vendor Layout stats
        view()->composer('layouts.vendor', function ($view) {
            $user = auth()->user();
            if ($user) {
                // Determine which vendor we are looking at
                $vendeur = request()->get('current_vendor') ?? ($user->vendeur ?? null);
                
                if ($vendeur) {
                    $activeOrdersCount = \App\Models\Commande::where('id_vendeur', $vendeur->id_vendeur)
                        ->whereIn('statut', ['en_attente', 'en_preparation', 'pret'])
                        ->count();
                        
                    $unreadMessagesCount = \App\Models\OrderMessage::whereHas('commande', function($q) use ($vendeur) {
                            $q->where('id_vendeur', $vendeur->id_vendeur);
                        })
                        ->where('is_read', false)
                        ->where(function($q) use ($user) {
                            $q->where('id_user', '!=', $user->id_user)
                              ->orWhereNull('id_user');
                        })
                        ->count();
                        
                    $view->with('activeOrders', $activeOrdersCount);
                    $view->with('unreadChatMessagesCount', $unreadMessagesCount);
                }
            }
        });

        // Pulse Authorization
        \Illuminate\Support\Facades\Gate::define('viewPulse', function ($user) {
            return method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();
        });
    }
}
