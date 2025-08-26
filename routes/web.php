<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminMerchantController;
use App\Http\Controllers\AdminOfferController;
use App\Http\Controllers\AdminLoyaltyController;
use App\Http\Controllers\AdminUserCategoryController;
use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\AdminChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Development helper route to create admin user
Route::get('/create-admin', [AuthController::class, 'createAdminUser']);

// Redirect root to admin login for easier access
Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    // Redirect to admin login as default page
    return redirect()->route('admin.login.form');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', function () {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    })->name('login.form');
    
    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth()->attempt($credentials)) {
            if (auth()->user()->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                auth()->logout();
                return back()->withErrors(['email' => 'ليس لديك صلاحية للوصول لهذه الصفحة']);
            }
        }

        return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
    })->name('login');

    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login.form');
    })->name('logout');
});

// Welcome page route
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Admin Dashboard Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [AdminDashboardController::class, 'getData'])->name('dashboard.data');
    
    // User Management Routes
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
    
    // Product Management Routes
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/toggle-availability', [AdminProductController::class, 'toggleAvailability'])->name('products.toggle-availability');
    Route::post('products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::post('products/{product}/update-stock', [AdminProductController::class, 'updateStock'])->name('products.update-stock');
    Route::post('products/bulk-action', [AdminProductController::class, 'bulkAction'])->name('products.bulk-action');

    // Order Management Routes
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/update-payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');
    Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/bulk-action', [AdminOrderController::class, 'bulkAction'])->name('orders.bulk-action');
    Route::get('orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');
    Route::get('orders/export', [AdminOrderController::class, 'export'])->name('orders.export');

    // Merchant Management Routes
    Route::resource('merchants', AdminMerchantController::class);
    Route::post('merchants/{merchant}/toggle-status', [AdminMerchantController::class, 'toggleStatus'])->name('merchants.toggle-status');
    Route::post('merchants/{merchant}/toggle-open', [AdminMerchantController::class, 'toggleOpenStatus'])->name('merchants.toggle-open');
    Route::post('merchants/bulk-action', [AdminMerchantController::class, 'bulkAction'])->name('merchants.bulk-action');
    Route::get('merchants/{merchant}/analytics', [AdminMerchantController::class, 'analytics'])->name('merchants.analytics');
    Route::get('merchants/export', [AdminMerchantController::class, 'export'])->name('merchants.export');

    // Offers Management Routes
    Route::resource('offers', AdminOfferController::class);
    Route::post('offers/{offer}/toggle-status', [AdminOfferController::class, 'toggleStatus'])->name('offers.toggle-status');
    Route::post('offers/bulk-action', [AdminOfferController::class, 'bulkAction'])->name('offers.bulk-action');
    Route::get('offers/{offer}/analytics', [AdminOfferController::class, 'analytics'])->name('offers.analytics');
    Route::get('offers/export', [AdminOfferController::class, 'export'])->name('offers.export');

    // Loyalty Points Management Routes
    Route::get('loyalty', [AdminLoyaltyController::class, 'index'])->name('loyalty.index');
    Route::get('loyalty/{user}', [AdminLoyaltyController::class, 'show'])->name('loyalty.show');
    Route::post('loyalty/award-points', [AdminLoyaltyController::class, 'awardPoints'])->name('loyalty.award-points');
    Route::post('loyalty/deduct-points', [AdminLoyaltyController::class, 'deductPoints'])->name('loyalty.deduct-points');
    Route::post('loyalty/bulk-action', [AdminLoyaltyController::class, 'bulkAction'])->name('loyalty.bulk-action');
    Route::get('loyalty-settings', [AdminLoyaltyController::class, 'settings'])->name('loyalty.settings');
    Route::post('loyalty-settings', [AdminLoyaltyController::class, 'updateSettings'])->name('loyalty.settings.update');
    Route::get('loyalty-analytics', [AdminLoyaltyController::class, 'analytics'])->name('loyalty.analytics');
    Route::get('loyalty/export', [AdminLoyaltyController::class, 'export'])->name('loyalty.export');

    // User Categories Management Routes
    Route::resource('user-categories', AdminUserCategoryController::class);
    Route::post('user-categories/{userCategory}/toggle-status', [AdminUserCategoryController::class, 'toggleStatus'])->name('user-categories.toggle-status');
    Route::post('user-categories/bulk-action', [AdminUserCategoryController::class, 'bulkAction'])->name('user-categories.bulk-action');
    Route::post('user-categories/recalculate', [AdminUserCategoryController::class, 'recalculateCategories'])->name('user-categories.recalculate');
    Route::get('user-categories-analytics', [AdminUserCategoryController::class, 'analytics'])->name('user-categories.analytics');
    Route::get('user-categories/export', [AdminUserCategoryController::class, 'export'])->name('user-categories.export');

    // Analytics Routes
    Route::get('analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export', [AdminAnalyticsController::class, 'export'])->name('analytics.export');

    // Chat Routes
    Route::get('chats', [AdminChatController::class, 'index'])->name('chats.index');
    Route::get('chats/{chat}', [AdminChatController::class, 'show'])->name('chats.show');
    Route::post('chats/{chat}/assign', [AdminChatController::class, 'assign'])->name('chats.assign');
    Route::post('chats/{chat}/status', [AdminChatController::class, 'updateStatus'])->name('chats.updateStatus');
    Route::post('chats/{chat}/priority', [AdminChatController::class, 'updatePriority'])->name('chats.updatePriority');
    Route::get('chats-admins', [AdminChatController::class, 'getAdmins'])->name('chats.admins');
});
