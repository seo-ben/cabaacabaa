<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

use App\Models\User;
use App\Models\Vendeur;
use App\Models\Plat;
use App\Models\Permission;
use App\Models\AppSetting;
use App\Models\Commande;
use App\Models\OrderMessage;
use App\Observers\VendeurObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        // Observers pour l'invalidation du cache Redis
        Vendeur::observe(VendeurObserver::class);
        Plat::observe(VendeurObserver::class);

        // Register Gates from Permissions
        try {
            if (Schema::hasTable('permissions')) {
                Gate::before(function ($user, $ability) {
                    if ($user instanceof User && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                        return true;
                    }
                    return null;
                });

                // Pluck avec 2 arguments pour la compatibilité Intelephense ('key', 'key')
                $permissionKeys = Permission::pluck('key', 'key');
                foreach ($permissionKeys as $key) {
                    Gate::define($key, function ($user) use ($key) {
                        return ($user instanceof User && method_exists($user, 'hasPermission')) ? $user->hasPermission($key) : false;
                    });
                }
            }
        } catch (\Throwable $e) {
            // Migrations non exécutées ou erreur DB
        }

        // Share system currency globally (safe fallback if migrations not run)
        try {
            if (Schema::hasTable('app_settings')) {
                View::share('currency', AppSetting::get('system_currency', 'XOF'));
                View::share('siteName', AppSetting::get('site_name', 'CabaaCabaa'));

                $logoPath = AppSetting::get('site_logo');
                $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('assets/logo/logo-cabaa.png');
                View::share('siteLogo', $logoUrl);

                $faviconPath = AppSetting::get('site_favicon');
                $faviconUrl = $faviconPath ? asset('storage/' . $faviconPath) : null;
                View::share('siteFavicon', $faviconUrl);
            } else {
                View::share('currency', 'XOF');
                View::share('siteName', 'CabaaCabaa');
                View::share('siteLogo', asset('assets/logo/logo-cabaa.png'));
                View::share('siteFavicon', null);
            }
        } catch (\Throwable $e) {
            View::share('currency', 'XOF');
        }

        // View Composer for Vendor Layout stats
        View::composer('layouts.vendor', function ($view) {
            /** @var User|null $user */
            $user = Auth::user();

            if ($user instanceof User) {
                // Determine which vendor we are looking at
                $vendeur = request()->get('current_vendor') ?? ($user->vendeur ?? null);

                if ($vendeur) {
                    $activeOrdersCount = Commande::where('id_vendeur', $vendeur->id_vendeur)
                        ->whereIn('statut', ['en_attente', 'en_preparation', 'pret'])
                        ->count();

                    // whereHas avec 4 arguments ('commande', Closure, '>=', 1) pour l'analyse Intelephense
                    $unreadMessagesCount = OrderMessage::whereHas('commande', function ($q) use ($vendeur) {
                            $q->where('id_vendeur', $vendeur->id_vendeur);
                        }, '>=', 1)
                        ->where('is_read', false)
                        ->where(function ($q) use ($user) {
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
        Gate::define('viewPulse', function ($user) {
            return ($user instanceof User && method_exists($user, 'isSuperAdmin')) ? $user->isSuperAdmin() : false;
        });
    }
}
