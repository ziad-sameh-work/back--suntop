<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Products\Controllers\ProductController;
use App\Modules\Orders\Controllers\OrderController;
use App\Modules\Admin\Controllers\UserCategoryController;
use App\Modules\Admin\Controllers\AdminUserController;
use App\Modules\Admin\Controllers\AdminProductController;
use App\Modules\Admin\Controllers\AdminOrderController;
use App\Modules\Admin\Controllers\AdminMerchantController;
use App\Modules\Admin\Controllers\AdminOfferController;
use App\Modules\Admin\Controllers\AdminLoyaltyController;
use App\Modules\Admin\Controllers\AdminAnalyticsController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::get('profile', [AuthController::class, 'profile']);
    });
});

// Products Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

// Orders Routes
Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::get('/{id}/tracking', [OrderController::class, 'tracking']);
    Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/{id}/reorder', [OrderController::class, 'reorder']);
});

// Admin Routes (Protected)
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    
    // Analytics & Dashboard
    Route::prefix('analytics')->group(function () {
        Route::get('/dashboard', [AdminAnalyticsController::class, 'dashboard']);
        Route::get('/sales', [AdminAnalyticsController::class, 'sales']);
        Route::get('/customers', [AdminAnalyticsController::class, 'customers']);
        Route::get('/products', [AdminAnalyticsController::class, 'products']);
        Route::get('/financial', [AdminAnalyticsController::class, 'financial']);
    });

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminUserController::class, 'index']);
        Route::post('/', [AdminUserController::class, 'store']);
        Route::get('/{id}', [AdminUserController::class, 'show']);
        Route::put('/{id}', [AdminUserController::class, 'update']);
        Route::delete('/{id}', [AdminUserController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [AdminUserController::class, 'toggleStatus']);
        Route::post('/{id}/reset-password', [AdminUserController::class, 'resetPassword']);
        Route::get('/statistics/overview', [AdminUserController::class, 'statistics']);
    });

    // Product Management
    Route::prefix('products')->group(function () {
        Route::get('/', [AdminProductController::class, 'index']);
        Route::post('/', [AdminProductController::class, 'store']);
        Route::get('/{id}', [AdminProductController::class, 'show']);
        Route::put('/{id}', [AdminProductController::class, 'update']);
        Route::delete('/{id}', [AdminProductController::class, 'destroy']);
        Route::post('/{id}/restore', [AdminProductController::class, 'restore']);
        Route::delete('/{id}/force-delete', [AdminProductController::class, 'forceDelete']);
        Route::post('/{id}/toggle-availability', [AdminProductController::class, 'toggleAvailability']);
        Route::post('/{id}/toggle-featured', [AdminProductController::class, 'toggleFeatured']);
        Route::post('/{id}/update-stock', [AdminProductController::class, 'updateStock']);
        Route::post('/bulk-action', [AdminProductController::class, 'bulkAction']);
        Route::get('/categories/list', [AdminProductController::class, 'categories']);
        Route::get('/analytics/overview', [AdminProductController::class, 'analytics']);
    });

    // Order Management
    Route::prefix('orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index']);
        Route::get('/{id}', [AdminOrderController::class, 'show']);
        Route::put('/{id}/status', [AdminOrderController::class, 'updateStatus']);
        Route::post('/{id}/cancel', [AdminOrderController::class, 'cancel']);
        Route::post('/{id}/tracking', [AdminOrderController::class, 'addTracking']);
        Route::get('/statistics/overview', [AdminOrderController::class, 'statistics']);
        Route::get('/export/csv', [AdminOrderController::class, 'export']);
    });

    // Merchant Management
    Route::prefix('merchants')->group(function () {
        Route::get('/', [AdminMerchantController::class, 'index']);
        Route::post('/', [AdminMerchantController::class, 'store']);
        Route::get('/{id}', [AdminMerchantController::class, 'show']);
        Route::put('/{id}', [AdminMerchantController::class, 'update']);
        Route::delete('/{id}', [AdminMerchantController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [AdminMerchantController::class, 'toggleStatus']);
        Route::post('/{id}/toggle-open', [AdminMerchantController::class, 'toggleOpen']);
        Route::get('/statistics/overview', [AdminMerchantController::class, 'statistics']);
    });

    // Offers Management
    Route::prefix('offers')->group(function () {
        Route::get('/', [AdminOfferController::class, 'index']);
        Route::post('/', [AdminOfferController::class, 'store']);
        Route::get('/{id}', [AdminOfferController::class, 'show']);
        Route::put('/{id}', [AdminOfferController::class, 'update']);
        Route::delete('/{id}', [AdminOfferController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [AdminOfferController::class, 'toggleStatus']);
        Route::get('/statistics/overview', [AdminOfferController::class, 'statistics']);
    });

    // Loyalty Points Management
    Route::prefix('loyalty')->group(function () {
        Route::get('/', [AdminLoyaltyController::class, 'index']);
        Route::post('/award-points', [AdminLoyaltyController::class, 'awardPoints']);
        Route::post('/deduct-points', [AdminLoyaltyController::class, 'deductPoints']);
        Route::get('/user/{userId}/summary', [AdminLoyaltyController::class, 'userSummary']);
        Route::get('/statistics/overview', [AdminLoyaltyController::class, 'statistics']);
        Route::post('/clean-expired', [AdminLoyaltyController::class, 'cleanExpired']);
    });

    // User Categories Management
    Route::prefix('user-categories')->group(function () {
        Route::get('/', [UserCategoryController::class, 'index']);
        Route::post('/', [UserCategoryController::class, 'store']);
        Route::get('/{id}', [UserCategoryController::class, 'show']);
        Route::put('/{id}', [UserCategoryController::class, 'update']);
        Route::delete('/{id}', [UserCategoryController::class, 'destroy']);
        Route::get('/statistics/overview', [UserCategoryController::class, 'statistics']);
        Route::get('/{id}/users', [UserCategoryController::class, 'users']);
        Route::post('/recalculate', [UserCategoryController::class, 'recalculateCategories']);
        Route::get('/test/amount', [UserCategoryController::class, 'getCategoryForAmount']);
    });
});

// Customer Chat Routes (Protected)
Route::prefix('chat')->middleware('auth:sanctum')->group(function () {
    Route::get('/start', [ChatController::class, 'getOrCreateChat']);
    Route::post('/send', [ChatController::class, 'sendMessage']);
    Route::get('/{chatId}/messages', [ChatController::class, 'getMessages']);
    Route::get('/history', [ChatController::class, 'getChatHistory']);
    Route::post('/{chatId}/read', [ChatController::class, 'markAsRead']);
});

// Protected user route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('userCategory');
});
