<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\UserController;

Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);

    if (!File::exists($filePath)) {
        abort(404);
    }

    $mimeType = File::mimeType($filePath);
    $lastModified = File::lastModified($filePath);
    $etag = md5_file($filePath);

    $response = Response::make(File::get($filePath), 200);
    $response->header("Content-Type", $mimeType);
    $response->header("Cache-Control", "public, max-age=31536000"); // Cache 1 an
    $response->header("Last-Modified", gmdate('D, d M Y H:i:s', $lastModified) . " GMT");
    $response->header("ETag", $etag);

    // Gestion du cache côté client (If-Modified-Since et If-None-Match)
    if (
        request()->headers->get('If-Modified-Since') == gmdate('D, d M Y H:i:s', $lastModified) . " GMT" ||
        request()->headers->get('If-None-Match') == $etag
    ) {
        return response('', 304)->header('Cache-Control', 'public, max-age=31536000');
    }

    return $response;
})->where('path', '.*');

// Home routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [HomeController::class, 'explore'])->name('explore');
Route::get('/produits', [HomeController::class, 'explorePlats'])->name('explore.plats');
Route::get('/vendor/{id}-{slug?}', [HomeController::class, 'vendor'])->name('vendor.show');

// Static pages
Route::get('/conditions-utilisation', [HomeController::class, 'terms'])->name('terms');
Route::get('/politique-confidentialite', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/a-propos', [HomeController::class, 'about'])->name('about');

// Vendor Map - Géolocalisation
Route::get('/vendeurs-proches', [\App\Http\Controllers\VendorMapController::class, 'index'])->name('vendors.map');
Route::post('/api/vendeurs-proches', [\App\Http\Controllers\VendorMapController::class, 'getNearbyVendors'])->name('vendors.nearby');

// Cart routes
Route::get('/panier', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter/{id}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/modifier', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/supprimer', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/panier/vider', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::post('/panier/coupon', [\App\Http\Controllers\CouponController::class, 'apply'])->name('cart.coupon');

// Checkout & Orders routes
Route::get('/mes-commandes', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
Route::get('/checkout', [\App\Http\Controllers\OrderController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout', [\App\Http\Controllers\OrderController::class, 'processCheckout'])->name('checkout.process');
Route::post('/checkout/calculate-delivery', [\App\Http\Controllers\OrderController::class, 'calculateDeliveryFee'])->name('checkout.calculate-delivery');
Route::match(['get', 'post'], '/checkout/callback', [\App\Http\Controllers\OrderController::class, 'callback'])->name('checkout.callback');
Route::get('/mes-commandes/{id}/annuler', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
Route::get('/mes-commandes/{id}/re-commander', [\App\Http\Controllers\OrderController::class, 'reorder'])->name('orders.reorder');
Route::get('/commande-confirmee/{id}', [\App\Http\Controllers\OrderController::class, 'confirmation'])->name('order.confirmation');
Route::get('/commande/suivi', [\App\Http\Controllers\OrderController::class, 'trackOrder'])->name('orders.track');
Route::get('/commande/recu/{code}', [\App\Http\Controllers\OrderController::class, 'showReceipt'])->name('order.receipt');
Route::get('/commande/check-status/{code}', [\App\Http\Controllers\OrderController::class, 'checkStatus'])->name('order.check-status');
Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

// Newsletter subscription
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Auth routes (simple)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset routes
Route::get('/mot-de-passe-oublie', [\App\Http\Controllers\PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/mot-de-passe-oublie', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])->name('password.forgot.post');
Route::get('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.reset.post');

// Auth-dependent routes
Route::middleware('auth')->group(function () {
    // Favorites
    Route::get('/favoris', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('favoris.index');
    Route::post('/favoris/toggle/{vendorId}', [\App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favoris.toggle');
    Route::get('/favoris/check/{vendorId}', [\App\Http\Controllers\FavoriteController::class, 'check'])->name('favoris.check');

    // Notifications
    Route::get('/api/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/api/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-read');
    Route::post('/api/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');

});

// Order Chat (Authorization handled inside controller to support guests)
Route::get('/api/orders/{orderId}/messages', [\App\Http\Controllers\OrderChatController::class, 'getMessages'])->name('orders.chat.messages');
Route::post('/api/orders/{orderId}/messages', [\App\Http\Controllers\OrderChatController::class, 'sendMessage'])->name('orders.chat.send');
Route::get('/api/orders/{orderId}/messages/unread', [\App\Http\Controllers\OrderChatController::class, 'getUnreadCount'])->name('orders.chat.unread');

// Vendor application
Route::middleware('auth')->group(function () {
    Route::get('/vendeur/appliquer', [AuthController::class, 'showApply'])->name('vendor.apply');
    Route::post('/vendeur/appliquer', [AuthController::class, 'apply'])->name('vendor.apply.submit');
});

Route::middleware('auth')->get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

// Staff Login Routes (must be before slug group to avoid conflicts)
Route::get('/{vendor_slug}/staff-login', [\App\Http\Controllers\Vendor\StaffAuthController::class, 'showLogin'])->name('vendor.staff.login');
Route::post('/{vendor_slug}/staff-login', [\App\Http\Controllers\Vendor\StaffAuthController::class, 'login'])->name('vendor.staff.login.post');

// Vendeur routes with slug (e.g., /pizza-hut/dashboard)
Route::prefix('{vendor_slug}')->middleware(['auth', \App\Http\Middleware\IdentifyVendorBySlug::class])->group(function () {
    // Vendeur dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Vendor\VendorDashboardController::class, 'index'])->name('vendeur.slug.dashboard');

    // Product management
    Route::get('/plats', [\App\Http\Controllers\Vendor\PlatController::class, 'index'])->name('vendeur.slug.plats.index');
    Route::get('/plats/creer', [\App\Http\Controllers\Vendor\PlatController::class, 'create'])->name('vendeur.slug.plats.create');
    Route::post('/plats', [\App\Http\Controllers\Vendor\PlatController::class, 'store'])->name('vendeur.slug.plats.store');
    Route::get('/plats/{id}/modifier', [\App\Http\Controllers\Vendor\PlatController::class, 'edit'])->name('vendeur.slug.plats.edit');
    Route::put('/plats/{id}', [\App\Http\Controllers\Vendor\PlatController::class, 'update'])->name('vendeur.slug.plats.update');
    Route::post('/plats/{id}/toggle-availability', [\App\Http\Controllers\Vendor\PlatController::class, 'toggleAvailability'])->name('vendeur.slug.plats.toggle-availability');
    Route::delete('/plats/{id}', [\App\Http\Controllers\Vendor\PlatController::class, 'destroy'])->name('vendeur.slug.plats.destroy');

    // Order management
    Route::get('/commandes', [\App\Http\Controllers\Vendor\OrderController::class, 'index'])->name('vendeur.slug.orders.index');
    Route::patch('/commandes/{id}/statut', [\App\Http\Controllers\Vendor\OrderController::class, 'updateStatus'])->name('vendeur.slug.orders.status');

    // Settings & Profile
    Route::get('/parametres', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'index'])->name('vendeur.slug.settings.index');
    Route::post('/parametres/profil', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'updateProfile'])->name('vendeur.slug.settings.profile');
    Route::post('/parametres/horaires', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'updateHours'])->name('vendeur.slug.settings.hours');
    Route::post('/parametres/categories', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'updateCategories'])->name('vendeur.slug.settings.categories');
    Route::post('/parametres/toggle-status', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'toggleStatus'])->name('vendeur.slug.settings.toggle');
    Route::post('/parametres/toggle-busy', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'toggleBusy'])->name('vendeur.slug.settings.toggle-busy');

    // Wallet & Payouts
    Route::get('/payouts', [\App\Http\Controllers\Vendor\PayoutController::class, 'index'])->name('vendeur.slug.payouts.index');
    Route::post('/payouts', [\App\Http\Controllers\Vendor\PayoutController::class, 'store'])->name('vendeur.slug.payouts.store');

    // Coupons
    Route::get('/coupons', [\App\Http\Controllers\Vendor\CouponController::class, 'index'])->name('vendeur.slug.coupons.index');
    Route::post('/coupons', [\App\Http\Controllers\Vendor\CouponController::class, 'store'])->name('vendeur.slug.coupons.store');
    Route::patch('/coupons/{coupon}/toggle', [\App\Http\Controllers\Vendor\CouponController::class, 'toggle'])->name('vendeur.slug.coupons.toggle');
    Route::delete('/coupons/{coupon}', [\App\Http\Controllers\Vendor\CouponController::class, 'destroy'])->name('vendeur.slug.coupons.destroy');

    // Team Management
    Route::get('/team', [\App\Http\Controllers\Vendor\TeamController::class, 'index'])->name('vendeur.slug.team.index');
    Route::get('/team/create', [\App\Http\Controllers\Vendor\TeamController::class, 'create'])->name('vendeur.slug.team.create');
    Route::post('/team', [\App\Http\Controllers\Vendor\TeamController::class, 'store'])->name('vendeur.slug.team.store');
    Route::get('/team/{id}/edit', [\App\Http\Controllers\Vendor\TeamController::class, 'edit'])->name('vendeur.slug.team.edit');
    Route::put('/team/{id}', [\App\Http\Controllers\Vendor\TeamController::class, 'update'])->name('vendeur.slug.team.update');
    Route::delete('/team/{id}', [\App\Http\Controllers\Vendor\TeamController::class, 'destroy'])->name('vendeur.slug.team.destroy');

    // Delivery Management for Vendor
    Route::get('/livreurs', [\App\Http\Controllers\DeliveryController::class, 'vendorIndex'])->name('vendeur.slug.delivery.index');
    Route::post('/livreurs/request', [\App\Http\Controllers\DeliveryController::class, 'storeRequest'])->name('vendeur.slug.delivery.request');
    Route::post('/livreurs/request/close', [\App\Http\Controllers\DeliveryController::class, 'closeRequest'])->name('vendeur.slug.delivery.request.close');
    Route::post('/livreurs/application/{id}/{action}', [\App\Http\Controllers\DeliveryController::class, 'handleApplication'])->name('vendeur.slug.delivery.application');
    Route::post('/commandes/{id}/assign', [\App\Http\Controllers\DeliveryController::class, 'assignOrder'])->name('vendeur.slug.delivery.assign');
});

// Public/Driver Routes
Route::get('/devenir-livreur', [\App\Http\Controllers\DeliveryController::class, 'index'])->name('delivery.index');
    Route::middleware('auth')->group(function () {
    Route::post('/devenir-livreur/apply/{id}', [\App\Http\Controllers\DeliveryController::class, 'apply'])->name('delivery.apply');
    Route::get('/mes-livraisons', [\App\Http\Controllers\DeliveryController::class, 'myDeliveries'])->name('delivery.my-deliveries');
    Route::post('/mes-livraisons/{id}/start', [\App\Http\Controllers\DeliveryController::class, 'startDelivery'])->name('delivery.start');
    Route::post('/mes-livraisons/{id}/complete', [\App\Http\Controllers\DeliveryController::class, 'completeDelivery'])->name('delivery.complete');
});

// Real-Time Driver Map
Route::get('/carte-livreurs', [\App\Http\Controllers\DriverController::class, 'index'])->name('drivers.map');
Route::get('/api/drivers/online', [\App\Http\Controllers\DriverController::class, 'getOnlineDrivers'])->name('api.drivers.online');
Route::get('/api/vendors/active', [\App\Http\Controllers\DriverController::class, 'getActiveVendors'])->name('api.vendors.active');
Route::post('/api/driver/location', [\App\Http\Controllers\DriverController::class, 'updateLocation'])->middleware('auth')->name('api.driver.location');


// Legacy Vendeur routes (backward compatibility - redirects to slug-based URLs)
Route::prefix('vendeur')->middleware(['auth', \App\Http\Middleware\EnsureUserIsVendeur::class])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return redirect()->route('vendeur.slug.dashboard', ['vendor_slug' => $user->vendeur->slug]);
    })->name('vendeur.dashboard');

    Route::get('/plats', function () {
        $user = Auth::user();
        return redirect()->route('vendeur.slug.plats.index', ['vendor_slug' => $user->vendeur->slug]);
    })->name('vendeur.plats.index');

    Route::get('/commandes', function () {
        $user = Auth::user();
        return redirect()->route('vendeur.slug.orders.index', ['vendor_slug' => $user->vendeur->slug]);
    })->name('vendeur.orders.index');

    Route::get('/parametres', function () {
        $user = Auth::user();
        return redirect()->route('vendeur.slug.settings.index', ['vendor_slug' => $user->vendeur->slug]);
    })->name('vendeur.settings.index');

    Route::get('/payouts', function () {
        $user = Auth::user();
        return redirect()->route('vendeur.slug.payouts.index', ['vendor_slug' => $user->vendeur->slug]);
    })->name('vendeur.payouts.index');

    Route::get('/coupons', function () {
        $user = Auth::user();
        return redirect()->route('vendeur.slug.coupons.index', ['vendor_slug' => $user->vendeur->slug]);
    })->name('vendeur.coupons.index');

    // Keep POST/PATCH/DELETE routes for forms that still use old routes
    Route::post('/plats', [\App\Http\Controllers\Vendor\PlatController::class, 'store'])->name('vendeur.plats.store');
    Route::get('/plats/creer', [\App\Http\Controllers\Vendor\PlatController::class, 'create'])->name('vendeur.plats.create');
    Route::get('/plats/{id}/modifier', [\App\Http\Controllers\Vendor\PlatController::class, 'edit'])->name('vendeur.plats.edit');
    Route::put('/plats/{id}', [\App\Http\Controllers\Vendor\PlatController::class, 'update'])->name('vendeur.plats.update');
    Route::delete('/plats/{id}', [\App\Http\Controllers\Vendor\PlatController::class, 'destroy'])->name('vendeur.plats.destroy');
    Route::patch('/commandes/{id}/statut', [\App\Http\Controllers\Vendor\OrderController::class, 'updateStatus'])->name('vendeur.orders.status');
    Route::post('/parametres/profil', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'updateProfile'])->name('vendeur.settings.profile');
    Route::post('/parametres/horaires', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'updateHours'])->name('vendeur.settings.hours');
    Route::post('/parametres/categories', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'updateCategories'])->name('vendeur.settings.categories');
    Route::post('/parametres/toggle-status', [\App\Http\Controllers\Vendor\VendorSettingsController::class, 'toggleStatus'])->name('vendeur.settings.toggle');
    Route::post('/payouts', [\App\Http\Controllers\Vendor\PayoutController::class, 'store'])->name('vendeur.payouts.store');
    Route::post('/coupons', [\App\Http\Controllers\Vendor\CouponController::class, 'store'])->name('vendeur.coupons.store');
    Route::patch('/coupons/{coupon}/toggle', [\App\Http\Controllers\Vendor\CouponController::class, 'toggle'])->name('vendeur.coupons.toggle');
    Route::delete('/coupons/{coupon}', [\App\Http\Controllers\Vendor\CouponController::class, 'destroy'])->name('vendeur.coupons.destroy');
});



// Admin routes protected by auth + EnsureUserIsAdmin middleware
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Vendeurs management
    Route::get('/vendors', [VendorController::class, 'index'])->name('admin.vendors.index');
    Route::post('/vendors', [VendorController::class, 'store'])->name('admin.vendors.store');
    Route::get('/vendors/{id}', [VendorController::class, 'show'])->name('admin.vendors.show');
    Route::get('/vendors/{id}/edit', [VendorController::class, 'edit'])->name('admin.vendors.edit');
    Route::put('/vendors/{id}', [VendorController::class, 'update'])->name('admin.vendors.update');
    Route::delete('/vendors/{id}', [VendorController::class, 'destroy'])->name('admin.vendors.destroy');
    Route::post('/vendors/{id}/approve', [VendorController::class, 'approve'])->name('admin.vendors.approve');
    Route::post('/vendors/{id}/unverify', [VendorController::class, 'unverify'])->name('admin.vendors.unverify');
    Route::post('/vendors/{id}/suspend', [VendorController::class, 'suspend'])->name('admin.vendors.suspend');
    Route::post('/vendors/{id}/unsuspend', [VendorController::class, 'unsuspend'])->name('admin.vendors.unsuspend');

    // Zones management
    Route::resource('/zones', \App\Http\Controllers\Admin\ZoneController::class, [
        'names' => [
            'index' => 'admin.zones.index',
            'create' => 'admin.zones.create',
            'store' => 'admin.zones.store',
            'show' => 'admin.zones.show',
            'edit' => 'admin.zones.edit',
            'update' => 'admin.zones.update',
            'destroy' => 'admin.zones.destroy',
        ]
    ]);
    Route::post('/zones/detect-location', [\App\Http\Controllers\Admin\ZoneController::class, 'detectLocation'])->name('admin.zones.detect-location');
    Route::post('/zones/coverage-by-address', [\App\Http\Controllers\Admin\ZoneController::class, 'getCoverageByAddress'])->name('admin.zones.coverage-by-address');
    Route::post('/zones/search-coordinates', [\App\Http\Controllers\Admin\ZoneController::class, 'searchCoordinates'])->name('admin.zones.search-coordinates');

    Route::get('/vendeurs', [AdminController::class, 'vendeurs'])->name('admin.vendeurs');
    Route::post('/vendeurs/{id}/approve', [AdminController::class, 'approveVendeur'])->name('admin.vendeurs.approve');

    // Categories management
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class, [
        'names' => [
            'index' => 'admin.categories.index',
            'create' => 'admin.categories.create',
            'store' => 'admin.categories.store',
            'show' => 'admin.categories.show',
            'edit' => 'admin.categories.edit',
            'update' => 'admin.categories.update',
            'destroy' => 'admin.categories.destroy',
        ]
    ]);

    // Vendor Categories management
    Route::resource('vendor-categories', \App\Http\Controllers\Admin\VendorCategoryController::class, [
        'names' => [
            'index' => 'admin.vendor-categories.index',
            'create' => 'admin.vendor-categories.create',
            'store' => 'admin.vendor-categories.store',
            'show' => 'admin.vendor-categories.show',
            'edit' => 'admin.vendor-categories.edit',
            'update' => 'admin.vendor-categories.update',
            'destroy' => 'admin.vendor-categories.destroy',
        ]
    ]);

    // Catalogue management (Produits)
    Route::resource('produits', \App\Http\Controllers\Admin\PlatController::class, [
        'names' => [
            'index' => 'admin.plats.index',
            'show' => 'admin.plats.show',
            'edit' => 'admin.plats.edit',
            'update' => 'admin.plats.update',
            'destroy' => 'admin.plats.destroy',
        ]
    ]);
    Route::patch('/produits/{id}/toggle-availability', [\App\Http\Controllers\Admin\PlatController::class, 'toggleAvailability'])->name('admin.plats.toggle-availability');

    // Users management
    Route::get('/users/export', [UserController::class, 'export'])->name('admin.users.export');
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('admin.users.bulk-action');

    Route::resource('users', UserController::class, [
        'names' => [
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]
    ]);

    Route::prefix('users/{id}')->group(function () {
        Route::patch('/status', [UserController::class, 'updateStatus'])->name('admin.users.status');
        Route::patch('/suspend', [UserController::class, 'suspend'])->name('admin.users.suspend');
        Route::patch('/unsuspend', [UserController::class, 'unsuspend'])->name('admin.users.unsuspend');
        Route::patch('/lock', [UserController::class, 'lock'])->name('admin.users.lock');
        Route::patch('/unlock', [UserController::class, 'unlock'])->name('admin.users.unlock');
        Route::patch('/verify', [UserController::class, 'verify'])->name('admin.users.verify');
        Route::patch('/unverify', [UserController::class, 'unverify'])->name('admin.users.unverify');
        Route::patch('/reset-risk-score', [UserController::class, 'resetRiskScore'])->name('admin.users.reset-risk-score');
        Route::post('/suspicious-flags', [UserController::class, 'addSuspiciousFlag'])->name('admin.users.suspicious-flags.add');
        Route::delete('/suspicious-flags', [UserController::class, 'clearSuspiciousFlags'])->name('admin.users.suspicious-flags.clear');
        Route::patch('/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
        Route::patch('/restore', [UserController::class, 'restore'])->name('admin.users.restore');
        Route::delete('/force-delete', [UserController::class, 'forceDelete'])->name('admin.users.force-delete');
    });

    // Orders management
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.status');

    // Finance & Payouts management
    Route::get('/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance.index');
    Route::get('/finance/transactions', [\App\Http\Controllers\Admin\FinanceController::class, 'transactions'])->name('admin.finance.transactions');
    Route::get('/finance/payouts', [\App\Http\Controllers\Admin\FinanceController::class, 'payouts'])->name('admin.finance.payouts');
    Route::patch('/finance/payouts/{id}', [\App\Http\Controllers\Admin\FinanceController::class, 'updatePayout'])->name('admin.finance.payouts.update');

    Route::get('/vendors/verification-doc/{id}/{type}', [VendorController::class, 'showDoc'])->name('admin.vendors.show-doc');
    Route::get('/vendors/complete/{userId}', [VendorController::class, 'completeProfile'])->name('admin.vendors.complete');

    // System Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');

    // Security logs
    Route::get('/security', [\App\Http\Controllers\Admin\SecurityController::class, 'index'])->name('admin.security.index');
    Route::get('/users/{id}/security', [\App\Http\Controllers\Admin\SecurityController::class, 'userSecurity'])->name('admin.security.user');

    // Countries management (Settings sub-domain)
    Route::get('/countries', [\App\Http\Controllers\Admin\CountryController::class, 'index'])->name('admin.countries.index');
    Route::post('/countries/update-selection', [\App\Http\Controllers\Admin\CountryController::class, 'updateSelection'])->name('admin.countries.update-selection');
    Route::post('/countries/{id}/toggle', [\App\Http\Controllers\Admin\CountryController::class, 'toggle'])->name('admin.countries.toggle');

    // Admin User Management
    Route::resource('admins', \App\Http\Controllers\Admin\AdminUserController::class, [
        'names' => 'admin.admins'
    ]);
});
