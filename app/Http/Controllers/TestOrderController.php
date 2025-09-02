<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Modules\Orders\Models\Order;
use App\Models\Notification;

class TestOrderController extends Controller
{
    /**
     * Test the order status update functionality
     */
    public function testUpdateStatus(Request $request, $id)
    {
        // Log the incoming request
        \Log::info("Test Order Status Update", [
            'order_id' => $id,
            'request_method' => $request->method(),
            'request_data' => $request->all(),
            'content_type' => $request->header('Content-Type'),
            'json_data' => $request->json()->all(),
        ]);
        
        try {
            // Validate basic requirements
            if (!$request->has(['status', 'title', 'message'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields',
                    'received_data' => $request->all()
                ], 422);
            }
            
            // Validate status
            $validStatuses = ['pending','confirmed','preparing','processing','shipping','shipped','delivered','cancelled','refunded'];
            if (!in_array($request->status, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status',
                    'received_status' => $request->status,
                    'valid_statuses' => $validStatuses
                ], 422);
            }
            
            // Find order
            $order = Order::with('user')->find($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                    'order_id' => $id
                ], 404);
            }
            
            // Check if user exists
            if (!$order->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for this order',
                    'order_id' => $id,
                    'user_id' => $order->user_id
                ], 404);
            }
            
            DB::beginTransaction();
            
            // Update order status
            $oldStatus = $order->status;
            $order->update([
                'status' => $request->status,
                'status_notes' => $request->notes ?? 'Updated via test controller',
                'updated_at' => now()
            ]);
            
            // Create notification
            $notification = Notification::createOrderStatusNotification(
                $order->user->id,
                $order->order_number ?? 'TEST-' . $order->id,
                $request->status,
                [
                    'title' => $request->title,
                    'message' => $request->message,
                    'custom_message' => true
                ]
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully via test controller',
                'data' => [
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'notification_id' => $notification->id,
                    'user_id' => $order->user->id,
                    'order_number' => $order->order_number
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Test Order Status Update Error', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating order status',
                'error_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    
    /**
     * Get order information for debugging
     */
    public function getOrderInfo($id)
    {
        try {
            $order = Order::with(['user', 'items'])->find($id);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                    'order_id' => $id
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'user_id' => $order->user_id,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at
                    ],
                    'user' => $order->user ? [
                        'id' => $order->user->id,
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                    ] : null,
                    'items_count' => $order->items->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving order info',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
