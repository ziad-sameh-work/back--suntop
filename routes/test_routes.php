<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminOrderController;

// Temporary test routes without authentication for debugging
Route::prefix('debug')->group(function () {
    Route::post('orders/{id}/update-status-with-notification', [AdminOrderController::class, 'updateStatusWithNotification'])
        ->withoutMiddleware(['auth', 'role:admin', 'web'])
        ->name('debug.orders.update-status-with-notification');
        
    Route::get('orders/{id}/info', function($id) {
        try {
            $order = \App\Modules\Orders\Models\Order::with('user')->find($id);
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'user_id' => $order->user_id,
                    'user' => $order->user ? [
                        'id' => $order->user->id,
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                        'role' => $order->user->role
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('debug.orders.info');
});
