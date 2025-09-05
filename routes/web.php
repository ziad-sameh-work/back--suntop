<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
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
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminPusherChatController;
use App\Http\Controllers\BroadcastingAuthController;
use App\Events\NewChatMessage;
use App\Models\ChatMessage;
use App\Models\Chat;

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

// Broadcasting Authentication Routes - Moved to API routes

// Test Routes for Debugging Chat Events
Route::get('/test-chat-event/{chat_id}', function ($chatId) {
    try {
        $chat = Chat::with('customer')->findOrFail($chatId);
        
        // Create a test message
        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $chat->customer->id,
            'sender_type' => 'customer',
            'message' => '🧪 Test message from route - ' . now()->format('H:i:s'),
            'message_type' => 'text',
            'metadata' => [
                'sent_from' => 'test_route',
                'test' => true
            ]
        ]);
        
        // Load relationships
        $message->load(['sender', 'chat.customer']);
        
        // Trigger event
        event(new NewChatMessage($message));
        
        return response()->json([
            'success' => true,
            'message' => 'Event triggered successfully',
            'data' => [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'sender' => $message->sender->name,
                'message_text' => $message->message,
                'channels' => [
                    "chat.{$chat->id}",
                    "private-admin.chats"
                ],
                'event' => 'message.new'
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Test Pusher configuration
Route::get('/test-pusher-config', function () {
    return response()->json([
        'broadcasting_driver' => config('broadcasting.default'),
        'pusher_config' => [
            'key' => config('broadcasting.connections.pusher.key'),
            'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            'app_id' => config('broadcasting.connections.pusher.app_id'),
            'secret' => substr(config('broadcasting.connections.pusher.secret'), 0, 4) . '...',
        ],
        'env_vars' => [
            'BROADCAST_DRIVER' => env('BROADCAST_DRIVER'),
            'PUSHER_APP_KEY' => env('PUSHER_APP_KEY'),
            'PUSHER_APP_CLUSTER' => env('PUSHER_APP_CLUSTER'),
        ],
        'auth_endpoint' => url('/broadcasting/auth'),
        'routes_registered' => [
            'broadcasting_auth' => route_exists('broadcasting.auth') ? 'YES' : 'NO'
        ]
    ]);
});

// Test Broadcasting Auth endpoint
Route::get('/test-broadcasting-auth', function (\Illuminate\Http\Request $request) {
    $user = auth()->user();
    
    if (!$user) {
        return response()->json([
            'error' => 'Not authenticated',
            'session_id' => $request->session()->getId(),
            'has_session' => $request->hasSession(),
            'cookies' => $request->cookies->all(),
            'headers' => $request->headers->all()
        ], 401);
    }
    
    return response()->json([
        'user' => $user->only(['id', 'name', 'email', 'role']),
        'auth_endpoint' => url('/broadcasting/auth'),
        'can_access_admin_chats' => $user->role === 'admin',
        'test_channel' => 'private-admin.chats',
        'session_id' => $request->session()->getId(),
        'csrf_token' => csrf_token(),
        'cookies' => array_keys($request->cookies->all())
    ]);
})->middleware(['web', 'auth']);

// Test manual broadcasting auth
Route::post('/test-manual-auth', function (\Illuminate\Http\Request $request) {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    $user = auth()->user();
    $channelName = $request->input('channel_name', 'private-admin.chats');
    $socketId = $request->input('socket_id', 'test-socket-123');
    
    // Test the same logic as BroadcastingAuthController
    if (strpos($channelName, 'private-admin.chats') !== false) {
        if ($user->role !== 'admin') {
            return response()->json([
                'error' => 'User is not admin',
                'user_role' => $user->role,
                'required_role' => 'admin'
            ], 403);
        }
        
        try {
            $pusher = new \Pusher\Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'role' => 'admin'
            ];
            
            $auth = $pusher->socket_auth($channelName, $socketId, json_encode($userData));
            
            return response()->json([
                'success' => true,
                'auth' => $auth,
                'user' => $userData,
                'channel' => $channelName,
                'socket_id' => $socketId
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Pusher error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    return response()->json(['error' => 'Invalid channel'], 400);
})->middleware(['web', 'auth']);

// Debug endpoint to simulate Pusher auth request exactly
Route::post('/debug-pusher-auth', function (\Illuminate\Http\Request $request) {
    \Log::info('Debug Pusher Auth Request', [
        'method' => $request->method(),
        'url' => $request->fullUrl(),
        'headers' => $request->headers->all(),
        'body' => $request->all(),
        'cookies' => $request->cookies->all(),
        'session_id' => $request->session()->getId(),
        'user' => auth()->user() ? auth()->user()->only(['id', 'name', 'role']) : null
    ]);
    
    return response()->json([
        'debug' => true,
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? auth()->user()->only(['id', 'name', 'role']) : null,
        'request_data' => $request->all()
    ]);
})->middleware(['web']);

// Test real-time message broadcasting
Route::get('/test-broadcast-message/{chatId?}', function ($chatId = 1) {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    try {
        // Find or create a test chat
        $chat = \App\Models\Chat::find($chatId);
        if (!$chat) {
            $customer = \App\Models\User::where('role', 'customer')->first();
            if (!$customer) {
                return response()->json(['error' => 'No customer found'], 404);
            }
            
            $chat = \App\Models\Chat::create([
                'customer_id' => $customer->id,
                'subject' => 'Test Chat for Broadcasting',
                'status' => 'open',
                'priority' => 'medium'
            ]);
        }
        
        // Create a test message
        $message = \App\Models\ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => auth()->id(),
            'sender_type' => 'admin',
            'message' => '🧪 Test broadcast message from admin - ' . now()->format('H:i:s'),
            'message_type' => 'text'
        ]);
        
        // Load relationships for the event
        $message->load(['sender', 'chat.customer']);
        
        // Fire the event
        event(new \App\Events\NewChatMessage($message));
        
        return response()->json([
            'success' => true,
            'message' => 'Test message broadcasted successfully',
            'data' => [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'message_text' => $message->message,
                'channels' => [
                    'chat.' . $chat->id,
                    'private-admin.chats',
                    'admin-chats-public'
                ]
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to broadcast message',
            'message' => $e->getMessage()
        ], 500);
    }
})->middleware(['web', 'auth']);

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

// Broadcasting Auth Route (outside admin group for global access)
Route::post('/broadcasting/auth', [BroadcastingAuthController::class, 'auth'])->name('broadcasting.auth')->middleware(['web', 'auth']);

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
    Route::post('orders/{order}/update-status-with-notification', [AdminOrderController::class, 'updateStatusWithNotification'])->name('orders.update-status-with-notification');
    Route::post('orders/{order}/update-payment-with-notification', [AdminOrderController::class, 'updatePaymentWithNotification'])->name('orders.update-payment-with-notification');
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


    // Enhanced Notification Routes
    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/create', [AdminNotificationController::class, 'create'])->name('notifications.create');
    Route::post('notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
    Route::get('notifications/{notification}', [AdminNotificationController::class, 'show'])->name('notifications.show');
    Route::delete('notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('notifications/send-to-all', [AdminNotificationController::class, 'sendToAll'])->name('notifications.send-to-all');
    Route::post('notifications/clean-old', [AdminNotificationController::class, 'cleanOld'])->name('notifications.clean-old');
    Route::get('notifications-stats', [AdminNotificationController::class, 'getStats'])->name('notifications.stats');
    Route::get('notifications/categories/{category}/users', [AdminNotificationController::class, 'getCategoryUsers'])->name('notifications.category-users');

    // Featured Offers Management Routes
    Route::prefix('featured-offers')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminFeaturedOffersController::class, 'index'])->name('featured-offers.index');
        Route::get('/create', [App\Http\Controllers\AdminFeaturedOffersController::class, 'create'])->name('featured-offers.create');
        Route::post('/', [App\Http\Controllers\AdminFeaturedOffersController::class, 'store'])->name('featured-offers.store');
        Route::get('/{offer}/edit', [App\Http\Controllers\AdminFeaturedOffersController::class, 'edit'])->name('featured-offers.edit');
        Route::put('/{offer}', [App\Http\Controllers\AdminFeaturedOffersController::class, 'update'])->name('featured-offers.update');
        Route::delete('/{offer}', [App\Http\Controllers\AdminFeaturedOffersController::class, 'destroy'])->name('featured-offers.destroy');
        Route::post('/{offer}/toggle-featured', [App\Http\Controllers\AdminFeaturedOffersController::class, 'toggleFeatured'])->name('featured-offers.toggle-featured');
        Route::post('/update-order', [App\Http\Controllers\AdminFeaturedOffersController::class, 'updateOrder'])->name('featured-offers.update-order');
        Route::get('/update-trends', [App\Http\Controllers\AdminFeaturedOffersController::class, 'updateTrendScores'])->name('featured-offers.update-trends');
        Route::get('/stats/data', [App\Http\Controllers\AdminFeaturedOffersController::class, 'getStats'])->name('featured-offers.stats');
    });

    
    // Removed duplicate pusher-chat routes - using main chats routes instead
});

// Test routes for debugging order status update
Route::prefix('test')->middleware(['web'])->group(function () {
    Route::post('orders/{id}/update-status', [App\Http\Controllers\TestOrderController::class, 'testUpdateStatus'])->name('test.orders.update-status');
    Route::get('orders/{id}/info', [App\Http\Controllers\TestOrderController::class, 'getOrderInfo'])->name('test.orders.info');
});

// Debug routes without authentication
require __DIR__ . '/test_routes.php';
