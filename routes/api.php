<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Namespaces for API Controllers
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VendorMapController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\OrderChatController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\DriverController;

use App\Http\Controllers\Api\Vendor\VendorDashboardController;
use App\Http\Controllers\Api\Vendor\PlatController as VendorPlatController;
use App\Http\Controllers\Api\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Api\Vendor\VendorSettingsController;
use App\Http\Controllers\Api\Vendor\PayoutController as VendorPayoutController;
use App\Http\Controllers\Api\Vendor\CouponController as VendorCouponController;
use App\Http\Controllers\Api\Vendor\TeamController;

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Api\Admin\ZoneController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\VendorCategoryController;
use App\Http\Controllers\Api\Admin\PlatController as AdminPlatController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\FinanceController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\SecurityController;
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\AdminUserController as SystemAdminController;

/*
|--------------------------------------------------------------------------
| API PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/home', [HomeController::class, 'index']);
Route::get('/explore', [HomeController::class, 'explore']);
Route::get('/produits', [HomeController::class, 'explorePlats']);
Route::get('/vendor/{id}-{slug?}', [HomeController::class, 'vendor']);

Route::get('/vendeurs-proches', [VendorMapController::class, 'index']);
Route::post('/vendeurs-proches', [VendorMapController::class, 'getNearbyVendors']);
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);

/*
|--------------------------------------------------------------------------
| API AUTH & PASSWORD ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| API CART (Requires session-like identifier or token)
|--------------------------------------------------------------------------
*/
Route::get('/panier', [CartController::class, 'index']);
Route::post('/panier/ajouter/{id}', [CartController::class, 'add']);
Route::patch('/panier/modifier', [CartController::class, 'update']);
Route::delete('/panier/supprimer', [CartController::class, 'remove']);
Route::post('/panier/vider', [CartController::class, 'clear']);
Route::post('/panier/coupon', [CouponController::class, 'apply']);
Route::post('/checkout/calculate-delivery', [OrderController::class, 'calculateDeliveryFee']);

/*
|--------------------------------------------------------------------------
| API CHAT (Order Chat)
|--------------------------------------------------------------------------
*/
Route::get('/orders/{orderId}/messages', [OrderChatController::class, 'getMessages']);
Route::post('/orders/{orderId}/messages', [OrderChatController::class, 'sendMessage']);
Route::get('/orders/{orderId}/messages/unread', [OrderChatController::class, 'getUnreadCount']);


/*
|--------------------------------------------------------------------------
| API PROTECTED ROUTES (Clients & General Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Checkout & Orders
    Route::post('/checkout', [OrderController::class, 'processCheckout']);
    Route::get('/mes-commandes', [OrderController::class, 'index']);
    Route::get('/mes-commandes/{id}/annuler', [OrderController::class, 'cancel']);
    Route::get('/mes-commandes/{id}/re-commander', [OrderController::class, 'reorder']);
    Route::get('/commande/suivi/{id}', [OrderController::class, 'trackOrder']);
    Route::get('/commande/recu/{code}', [OrderController::class, 'showReceipt']);
    Route::get('/commande/check-status/{code}', [OrderController::class, 'checkStatus']);
    
    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::post('/reviews/vendor', [ReviewController::class, 'storeVendorReview']);

    // Favorites
    Route::get('/favoris', [FavoriteController::class, 'index']);
    Route::post('/favoris/toggle/{vendorId}', [FavoriteController::class, 'toggle']);
    Route::get('/favoris/check/{vendorId}', [FavoriteController::class, 'check']);

    // Notifications
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);
    
    // Vendor application
    Route::post('/vendeur/appliquer', [AuthController::class, 'apply']);
    Route::get('/dashboard', [AuthController::class, 'dashboard']); // General entry point dashboard
});


/*
|--------------------------------------------------------------------------
| API DRIVER / LIVREUR ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/api/drivers/online', [DriverController::class, 'getOnlineDrivers']);
Route::get('/api/vendors/active', [DriverController::class, 'getActiveVendors']);

Route::prefix('driver')->middleware('auth:sanctum')->group(function () {
    Route::post('/apply/{id}', [DeliveryController::class, 'apply']);
    Route::get('/mes-livraisons', [DeliveryController::class, 'myDeliveries']);
    Route::post('/mes-livraisons/{id}/start', [DeliveryController::class, 'startDelivery']);
    Route::post('/mes-livraisons/{id}/complete', [DeliveryController::class, 'completeDelivery']);
    Route::post('/location', [DriverController::class, 'updateLocation']);
});


/*
|--------------------------------------------------------------------------
| API VENDOR ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/vendor/staff-login', [App\Http\Controllers\Api\Vendor\StaffAuthController::class, 'login']);

Route::prefix('vendor')->middleware(['auth:sanctum'])->group(function () {
    // Vendeur dashboard
    Route::get('/dashboard', [VendorDashboardController::class, 'index']);

    // Product management
    Route::get('/plats', [VendorPlatController::class, 'index']);
    Route::post('/plats', [VendorPlatController::class, 'store']);
    Route::get('/plats/{id}', [VendorPlatController::class, 'show']);
    Route::put('/plats/{id}', [VendorPlatController::class, 'update']);
    Route::post('/plats/{id}/toggle-availability', [VendorPlatController::class, 'toggleAvailability']);
    Route::delete('/plats/{id}', [VendorPlatController::class, 'destroy']);

    // Order management
    Route::get('/commandes', [VendorOrderController::class, 'index']);
    Route::patch('/commandes/{id}/statut', [VendorOrderController::class, 'updateStatus']);

    // Settings & Profile
    Route::get('/parametres', [VendorSettingsController::class, 'index']);
    Route::post('/parametres/profil', [VendorSettingsController::class, 'updateProfile']);
    Route::post('/parametres/horaires', [VendorSettingsController::class, 'updateHours']);
    Route::post('/parametres/categories', [VendorSettingsController::class, 'updateCategories']);
    Route::post('/parametres/toggle-status', [VendorSettingsController::class, 'toggleStatus']);
    Route::post('/parametres/toggle-busy', [VendorSettingsController::class, 'toggleBusy']);

    // Wallet & Payouts
    Route::get('/payouts', [VendorPayoutController::class, 'index']);
    Route::post('/payouts', [VendorPayoutController::class, 'store']);

    // Coupons
    Route::get('/coupons', [VendorCouponController::class, 'index']);
    Route::post('/coupons', [VendorCouponController::class, 'store']);
    Route::patch('/coupons/{coupon}/toggle', [VendorCouponController::class, 'toggle']);
    Route::delete('/coupons/{coupon}', [VendorCouponController::class, 'destroy']);

    // Team Management
    Route::get('/team', [TeamController::class, 'index']);
    Route::post('/team', [TeamController::class, 'store']);
    Route::put('/team/{id}', [TeamController::class, 'update']);
    Route::delete('/team/{id}', [TeamController::class, 'destroy']);

    // Delivery Management for Vendor
    Route::get('/livreurs', [DeliveryController::class, 'vendorIndex']);
    Route::post('/livreurs/request', [DeliveryController::class, 'storeRequest']);
    Route::post('/livreurs/request/close', [DeliveryController::class, 'closeRequest']);
    Route::post('/livreurs/application/{id}/{action}', [DeliveryController::class, 'handleApplication']);
    Route::post('/commandes/{id}/assign', [DeliveryController::class, 'assignOrder']);
});


/*
|--------------------------------------------------------------------------
| API ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index']);
    Route::get('/vendeurs', [AdminController::class, 'vendeurs']);
    Route::post('/vendeurs/{id}/approve', [AdminController::class, 'approveVendeur']);

    // Vendeurs management
    Route::apiResource('vendors', AdminVendorController::class);
    Route::post('/vendors/{id}/approve', [AdminVendorController::class, 'approve']);
    Route::post('/vendors/{id}/unverify', [AdminVendorController::class, 'unverify']);
    Route::post('/vendors/{id}/suspend', [AdminVendorController::class, 'suspend']);
    Route::post('/vendors/{id}/unsuspend', [AdminVendorController::class, 'unsuspend']);
    Route::get('/vendors/verification-doc/{id}/{type}', [AdminVendorController::class, 'showDoc']);
    Route::get('/vendors/complete/{userId}', [AdminVendorController::class, 'completeProfile']);

    // Zones management
    Route::apiResource('zones', ZoneController::class);
    Route::post('/zones/detect-location', [ZoneController::class, 'detectLocation']);
    Route::post('/zones/coverage-by-address', [ZoneController::class, 'getCoverageByAddress']);
    Route::post('/zones/search-coordinates', [ZoneController::class, 'searchCoordinates']);

    // Categories management
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('vendor-categories', VendorCategoryController::class);

    // Catalogue management (Produits)
    Route::apiResource('produits', AdminPlatController::class);
    Route::patch('/produits/{id}/toggle-availability', [AdminPlatController::class, 'toggleAvailability']);

    // Users management
    Route::get('/users/export', [AdminUserController::class, 'export']);
    Route::post('/users/bulk-action', [AdminUserController::class, 'bulkAction']);
    Route::apiResource('users', AdminUserController::class);
    
    Route::prefix('users/{id}')->group(function () {
        Route::patch('/status', [AdminUserController::class, 'updateStatus']);
        Route::patch('/suspend', [AdminUserController::class, 'suspend']);
        Route::patch('/unsuspend', [AdminUserController::class, 'unsuspend']);
        Route::patch('/lock', [AdminUserController::class, 'lock']);
        Route::patch('/unlock', [AdminUserController::class, 'unlock']);
        Route::patch('/verify', [AdminUserController::class, 'verify']);
        Route::patch('/unverify', [AdminUserController::class, 'unverify']);
        Route::patch('/reset-risk-score', [AdminUserController::class, 'resetRiskScore']);
        Route::post('/suspicious-flags', [AdminUserController::class, 'addSuspiciousFlag']);
        Route::delete('/suspicious-flags', [AdminUserController::class, 'clearSuspiciousFlags']);
        Route::patch('/reset-password', [AdminUserController::class, 'resetPassword']);
        Route::patch('/restore', [AdminUserController::class, 'restore']);
        Route::delete('/force-delete', [AdminUserController::class, 'forceDelete']);
    });

    // Orders management
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

    // Finance & Payouts management
    Route::get('/finance', [FinanceController::class, 'index']);
    Route::get('/finance/transactions', [FinanceController::class, 'transactions']);
    Route::get('/finance/payouts', [FinanceController::class, 'payouts']);
    Route::patch('/finance/payouts/{id}', [FinanceController::class, 'updatePayout']);

    // System Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);

    // Security logs
    Route::get('/security', [SecurityController::class, 'index']);
    Route::get('/users/{id}/security', [SecurityController::class, 'userSecurity']);

    // Countries management
    Route::get('/countries', [CountryController::class, 'index']);
    Route::post('/countries/update-selection', [CountryController::class, 'updateSelection']);
    Route::post('/countries/{id}/toggle', [CountryController::class, 'toggle']);

    // Admin User Management
    Route::apiResource('admins', AdminUserController::class);
});
