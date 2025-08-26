<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\Orders\Models\Order;
use App\Modules\Users\Models\User;
use App\Modules\Products\Models\Product;
use App\Modules\Merchants\Models\Merchant;

class AdminOrderController extends Controller
{
    /**
     * Display orders list
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('total_amount', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Amount range filter
        if ($request->filled('amount_from')) {
            $query->where('total_amount', '>=', $request->amount_from);
        }
        if ($request->filled('amount_to')) {
            $query->where('total_amount', '<=', $request->amount_to);
        }

        // Merchant filter (if merchant relationship exists)
        if ($request->filled('merchant_id') && $request->merchant_id !== 'all') {
            $query->where('merchant_id', $request->merchant_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $orders = $query->paginate($perPage)->withQueryString();

        // Statistics
        $stats = $this->getOrdersStats($request);

        // Get merchants for filter (if available)
        $merchants = [];
        try {
            if (Schema::hasTable('merchants')) {
                $merchants = Merchant::where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            // Merchants table might not exist
        }

        return view('admin.orders.index', compact('orders', 'stats', 'merchants'));
    }

    /**
     * Show order details
     */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product',
            'items.product.merchant'
        ])->findOrFail($id);

        // Calculate order statistics
        $orderStats = $this->getOrderStatistics($order);
        
        // Get order timeline
        $timeline = $this->getOrderTimeline($order);

        return view('admin.orders.show', compact('order', 'orderStats', 'timeline'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $order = Order::findOrFail($id);
            $oldStatus = $order->status;
            
            $order->update([
                'status' => $request->status,
                'status_notes' => $request->notes,
                'updated_at' => now()
            ]);

            // Log status change if tracking table exists
            $this->logStatusChange($order, $oldStatus, $request->status, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب بنجاح',
                'new_status' => $request->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة الطلب'
            ], 500);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_notes' => 'nullable|string|max:500'
        ]);

        try {
            $order = Order::findOrFail($id);
            
            $order->update([
                'payment_status' => $request->payment_status,
                'payment_notes' => $request->payment_notes,
                'paid_at' => $request->payment_status === 'paid' ? now() : null,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الدفع بنجاح',
                'new_payment_status' => $request->payment_status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة الدفع'
            ], 500);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);
            
            if (in_array($order->status, ['delivered', 'cancelled', 'refunded'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إلغاء هذا الطلب في حالته الحالية'
                ], 422);
            }

            // Restore stock quantities
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الطلب وإعادة المخزون بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إلغاء الطلب'
            ], 500);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:update_status,cancel,delete',
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required_if:action,update_status|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $orders = Order::whereIn('id', $request->order_ids)->get();
            $processedCount = 0;

            foreach ($orders as $order) {
                switch ($request->action) {
                    case 'update_status':
                        if (!in_array($order->status, ['delivered', 'cancelled', 'refunded'])) {
                            $order->update([
                                'status' => $request->status,
                                'status_notes' => $request->notes,
                                'updated_at' => now()
                            ]);
                            $processedCount++;
                        }
                        break;

                    case 'cancel':
                        if (!in_array($order->status, ['delivered', 'cancelled', 'refunded'])) {
                            // Restore stock
                            foreach ($order->items as $item) {
                                $item->product?->increment('stock_quantity', $item->quantity);
                            }
                            
                            $order->update([
                                'status' => 'cancelled',
                                'cancellation_reason' => $request->notes ?? 'إلغاء جماعي من الإدارة',
                                'cancelled_at' => now(),
                                'updated_at' => now()
                            ]);
                            $processedCount++;
                        }
                        break;

                    case 'delete':
                        if ($order->status === 'cancelled') {
                            $order->delete();
                            $processedCount++;
                        }
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "تم تنفيذ العملية على {$processedCount} طلب بنجاح"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية الجماعية'
            ], 500);
        }
    }

    /**
     * Get orders statistics
     */
    private function getOrdersStats($request = null)
    {
        $query = Order::query();
        
        // Apply same filters as main query for consistent stats
        if ($request) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('total_amount', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        return [
            'total_orders' => $query->count(),
            'pending_orders' => (clone $query)->where('status', 'pending')->count(),
            'confirmed_orders' => (clone $query)->where('status', 'confirmed')->count(),
            'processing_orders' => (clone $query)->where('status', 'processing')->count(),
            'shipped_orders' => (clone $query)->where('status', 'shipped')->count(),
            'delivered_orders' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled_orders' => (clone $query)->where('status', 'cancelled')->count(),
            'total_revenue' => (clone $query)->where('payment_status', 'paid')->sum('total_amount'),
            'pending_payments' => (clone $query)->where('payment_status', 'pending')->sum('total_amount'),
            'failed_payments' => (clone $query)->where('payment_status', 'failed')->count(),
            'today_orders' => (clone $query)->whereDate('created_at', today())->count(),
            'this_week_orders' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_orders' => (clone $query)->whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Get order statistics for individual order
     */
    private function getOrderStatistics($order)
    {
        return [
            'total_items' => $order->items->count(),
            'total_quantity' => $order->items->sum('quantity'),
            'average_item_price' => $order->items->avg('unit_price'),
            'profit_margin' => 0, // Calculate if cost data is available
            'processing_time' => $this->calculateProcessingTime($order),
            'customer_orders_count' => Order::where('user_id', $order->user_id)->count(),
            'customer_total_spent' => Order::where('user_id', $order->user_id)
                                          ->where('payment_status', 'paid')
                                          ->sum('total_amount'),
        ];
    }

    /**
     * Get order timeline
     */
    private function getOrderTimeline($order)
    {
        $timeline = [];
        
        // Order created
        $timeline[] = [
            'status' => 'created',
            'title' => 'تم إنشاء الطلب',
            'description' => 'تم استلام طلبك وهو قيد المراجعة',
            'timestamp' => $order->created_at,
            'icon' => 'fas fa-plus-circle',
            'color' => 'blue'
        ];

        // Status-based timeline
        $statusTimeline = [
            'confirmed' => ['title' => 'تم تأكيد الطلب', 'description' => 'تم تأكيد طلبك وبدء المعالجة', 'icon' => 'fas fa-check-circle', 'color' => 'green'],
            'processing' => ['title' => 'جاري التجهيز', 'description' => 'جاري تجهيز طلبك للشحن', 'icon' => 'fas fa-cog', 'color' => 'orange'],
            'shipped' => ['title' => 'تم الشحن', 'description' => 'تم شحن طلبك وهو في الطريق إليك', 'icon' => 'fas fa-truck', 'color' => 'purple'],
            'delivered' => ['title' => 'تم التسليم', 'description' => 'تم تسليم طلبك بنجاح', 'icon' => 'fas fa-check-double', 'color' => 'green'],
            'cancelled' => ['title' => 'تم الإلغاء', 'description' => 'تم إلغاء الطلب', 'icon' => 'fas fa-times-circle', 'color' => 'red'],
        ];

        // Add current status to timeline
        if (isset($statusTimeline[$order->status]) && $order->status !== 'pending') {
            $timeline[] = [
                'status' => $order->status,
                'title' => $statusTimeline[$order->status]['title'],
                'description' => $statusTimeline[$order->status]['description'],
                'timestamp' => $order->updated_at,
                'icon' => $statusTimeline[$order->status]['icon'],
                'color' => $statusTimeline[$order->status]['color']
            ];
        }

        // Payment timeline
        if ($order->payment_status === 'paid' && $order->paid_at) {
            $timeline[] = [
                'status' => 'paid',
                'title' => 'تم الدفع',
                'description' => 'تم استلام الدفعة بنجاح',
                'timestamp' => $order->paid_at,
                'icon' => 'fas fa-credit-card',
                'color' => 'green'
            ];
        }

        // Sort by timestamp
        usort($timeline, function ($a, $b) {
            return $a['timestamp'] <=> $b['timestamp'];
        });

        return $timeline;
    }

    /**
     * Calculate processing time
     */
    private function calculateProcessingTime($order)
    {
        if ($order->status === 'delivered' && $order->updated_at) {
            return $order->created_at->diffInHours($order->updated_at);
        }
        
        return $order->created_at->diffInHours(now());
    }

    /**
     * Log status change
     */
    private function logStatusChange($order, $oldStatus, $newStatus, $notes = null)
    {
        // This would log to a tracking table if it exists
        // For now, we'll just update the order notes
        try {
            // You can implement order tracking table here
        } catch (\Exception $e) {
            // Log error if needed
        }
    }

    /**
     * Export orders (placeholder for future implementation)
     */
    public function export(Request $request)
    {
        // This would export orders to CSV/Excel
        return response()->json([
            'success' => false,
            'message' => 'ميزة التصدير قيد التطوير'
        ]);
    }

    /**
     * Print order (placeholder)
     */
    public function print($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        
        // Return a printable view
        return view('admin.orders.print', compact('order'));
    }
}
