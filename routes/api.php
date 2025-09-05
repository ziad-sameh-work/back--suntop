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
use App\Modules\Admin\Controllers\AdminOfferController;
use App\Modules\Admin\Controllers\AdminLoyaltyController;
use App\Modules\Admin\Controllers\AdminAnalyticsController;
use App\Http\Controllers\Api\ChatController;
use App\Modules\Notifications\Controllers\NotificationController;
use App\Modules\Notifications\Controllers\AdminNotificationController;
use App\Http\Controllers\Api\PusherChatController;
use App\Http\Controllers\BroadcastingAuthController;

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

// Broadcasting Authentication Routes
Route::post('/broadcasting/auth', [BroadcastingAuthController::class, 'auth'])
    ->middleware('auth:sanctum');

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
    Route::get('/history', [OrderController::class, 'history']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::get('/{id}/status', [OrderController::class, 'getOrderStatus']);
    Route::get('/{id}/tracking', [OrderController::class, 'tracking']);
    Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/{id}/reorder', [OrderController::class, 'reorder']);
    Route::post('/{id}/rate', [OrderController::class, 'rate']);
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
        Route::get('/dashboard', [AdminOrderController::class, 'dashboard']);
        Route::get('/statistics/overview', [AdminOrderController::class, 'statistics']);
        Route::get('/export/csv', [AdminOrderController::class, 'export']);
        Route::post('/bulk-update-status', [AdminOrderController::class, 'bulkUpdateStatus']);
        Route::get('/{id}', [AdminOrderController::class, 'show']);
        Route::put('/{id}/status', [AdminOrderController::class, 'updateStatus']);
        Route::post('/{id}/cancel', [AdminOrderController::class, 'cancel']);
        Route::post('/{id}/tracking', [AdminOrderController::class, 'addTracking']);
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

    // Notifications Management
    Route::prefix('notifications')->group(function () {
        Route::get('/', [AdminNotificationController::class, 'index']);
        Route::post('/', [AdminNotificationController::class, 'store']);
        Route::post('/bulk', [AdminNotificationController::class, 'storeBulk']);
        Route::post('/send-to-all', [AdminNotificationController::class, 'sendToAll']);
        Route::get('/statistics', [AdminNotificationController::class, 'statistics']);
        Route::get('/users', [AdminNotificationController::class, 'getUsers']);
        Route::delete('/{id}', [AdminNotificationController::class, 'destroy']);
        Route::post('/clean-old', [AdminNotificationController::class, 'cleanOld']);
    });
});

// Offers Routes (Public and Protected)
Route::prefix('offers')->group(function () {
    Route::get('/', [App\Modules\Offers\Controllers\OfferController::class, 'index']);
    Route::get('/featured', [App\Modules\Offers\Controllers\OfferController::class, 'featured']);
    Route::get('/trending', [App\Modules\Offers\Controllers\OfferController::class, 'trending']);
    Route::get('/stats', [App\Modules\Offers\Controllers\OfferController::class, 'stats']);
    Route::get('/categories', [App\Modules\Offers\Controllers\OfferController::class, 'categories']);
    Route::get('/types', [App\Modules\Offers\Controllers\OfferController::class, 'types']);
    Route::get('/{id}', [App\Modules\Offers\Controllers\OfferController::class, 'show']);
    Route::get('/{id}/performance', [App\Modules\Offers\Controllers\OfferController::class, 'performance']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{id}/validate', [App\Modules\Offers\Controllers\OfferController::class, 'validateOffer']);
        Route::post('/{id}/redeem', [App\Modules\Offers\Controllers\OfferController::class, 'redeem']);
        Route::get('/user/redemptions', [App\Modules\Offers\Controllers\OfferController::class, 'userRedemptions']);
    });
});

// Loyalty Points Routes (Protected)
Route::prefix('loyalty')->middleware('auth:sanctum')->group(function () {
    Route::get('/points', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'getPoints']);
    Route::get('/transactions', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'getTransactions']);
    Route::get('/analytics', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'getAnalytics']);
    Route::get('/earning-opportunities', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'getEarningOpportunities']);
    
    // Rewards
    Route::get('/rewards', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'getRewards']);
    Route::post('/rewards/{id}/redeem', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'redeemReward']);
    Route::get('/rewards/redemptions', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'userRedemptions']);
    
    // Tiers
    Route::get('/tiers', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'getTiers']);
    
    // For admin
    Route::post('/points/add', [App\Modules\Loyalty\Controllers\LoyaltyController::class, 'addPoints']);
});

// Legacy loyalty route
Route::prefix('loyalty')->middleware('auth:sanctum')->group(function () {
    Route::get('/points', [App\Http\Controllers\Api\LoyaltyController::class, 'getPoints']);
    Route::get('/transactions', [App\Http\Controllers\Api\LoyaltyController::class, 'getTransactions']);
});

// Enhanced Notifications Routes (Protected)
Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'index']);
    Route::get('/unread-count', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'unreadCount']);
    Route::get('/statistics', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'statistics']);
    Route::get('/types', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'types']);
    Route::get('/{id}', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'show']);
    Route::post('/{id}/read', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'markAsRead']);
    Route::post('/mark-all-read', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'destroy']);
    Route::delete('/', [App\Modules\Notifications\Controllers\UserNotificationController::class, 'destroyAll']);
});

// Favorites Routes (Protected)
Route::prefix('favorites')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Modules\Favorites\Controllers\FavoriteController::class, 'index']);
    Route::post('/', [App\Modules\Favorites\Controllers\FavoriteController::class, 'store']);
    Route::post('/toggle', [App\Modules\Favorites\Controllers\FavoriteController::class, 'toggle']);
    Route::post('/bulk-add', [App\Modules\Favorites\Controllers\FavoriteController::class, 'bulkAdd']);
    Route::post('/check-multiple', [App\Modules\Favorites\Controllers\FavoriteController::class, 'checkMultiple']);
    Route::get('/count', [App\Modules\Favorites\Controllers\FavoriteController::class, 'count']);
    Route::get('/statistics', [App\Modules\Favorites\Controllers\FavoriteController::class, 'statistics']);
    Route::get('/popular', [App\Modules\Favorites\Controllers\FavoriteController::class, 'popular']);
    Route::get('/recommendations', [App\Modules\Favorites\Controllers\FavoriteController::class, 'recommendations']);
    Route::get('/check/{product_id}', [App\Modules\Favorites\Controllers\FavoriteController::class, 'check']);
    Route::delete('/clear', [App\Modules\Favorites\Controllers\FavoriteController::class, 'clear']);
    Route::delete('/{product_id}', [App\Modules\Favorites\Controllers\FavoriteController::class, 'destroy']);
});

// Real-Time Chat Routes with Pusher (Protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chat/start', [ChatController::class, 'getOrCreateChat']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{chat}/messages', [ChatController::class, 'getMessages']);
    Route::get('/chat/history', [ChatController::class, 'getChatHistory']);
    Route::post('/chat/{chat}/read', [ChatController::class, 'markAsRead']);
});

// Test Chat routes (for testing real-time functionality)
Route::prefix('test')->group(function () {
    Route::post('/send-message', [\App\Http\Controllers\Api\TestChatController::class, 'sendTestMessage']);
    Route::post('/create-chat', [\App\Http\Controllers\Api\TestChatController::class, 'createTestChat']);
    Route::get('/chat/{chatId}/messages', [\App\Http\Controllers\Api\TestChatController::class, 'getChatMessages']);
    Route::post('/pusher-broadcast', [\App\Http\Controllers\Api\TestChatController::class, 'testPusherBroadcast']);
});

// Mobile Chat Routes - Real-time للموبايل
require __DIR__ . '/mobile-chat-api.php';

// Protected user route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('userCategory');
});
