<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderTracking;
use App\Modules\Admin\Requests\UpdateOrderStatusRequest;
use App\Modules\Admin\Resources\AdminOrderResource;
use App\Modules\Admin\Resources\AdminOrderDetailResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Get all orders with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['user', 'merchant', 'items.product'])
                         ->withCount('items');

            // Apply filters
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_status') && $request->payment_status !== 'all') {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('merchant_id') && $request->merchant_id !== 'all') {
                $query->where('merchant_id', $request->merchant_id);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'LIKE', "%{$search}%")
                      ->orWhere('tracking_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'LIKE', "%{$search}%")
                                   ->orWhere('email', 'LIKE', "%{$search}%")
                                   ->orWhere('phone', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('limit', 20);
            $orders = $query->paginate($perPage);

            return $this->successResponse([
                'orders' => AdminOrderResource::collection($orders),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'total_pages' => $orders->lastPage(),
                    'has_next' => $orders->hasMorePages(),
                    'has_prev' => $orders->currentPage() > 1,
                ],
                'summary' => [
                    'total_orders' => Order::count(),
                    'pending_orders' => Order::where('status', 'pending')->count(),
                    'confirmed_orders' => Order::where('status', 'confirmed')->count(),
                    'shipped_orders' => Order::where('status', 'shipped')->count(),
                    'delivered_orders' => Order::where('status', 'delivered')->count(),
                    'cancelled_orders' => Order::where('status', 'cancelled')->count(),
                    'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get specific order details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $order = Order::with([
                'user.userCategory',
                'merchant',
                'items.product',
                'trackings' => function ($query) {
                    $query->orderBy('timestamp', 'asc');
                }
            ])->findOrFail($id);

            return $this->successResponse([
                'order' => new AdminOrderDetailResource($order)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 404);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $data = $request->validated();

            // Validate status transition
            if (!$this->isValidStatusTransition($order->status, $data['status'])) {
                return $this->errorResponse('لا يمكن تغيير حالة الطلب من ' . $order->status_text . ' إلى ' . Order::STATUSES[$data['status']]);
            }

            $order->update(['status' => $data['status']]);

            // Create tracking entry
            OrderTracking::create([
                'order_id' => $order->id,
                'status' => $data['status'],
                'status_text' => Order::STATUSES[$data['status']],
                'location' => $data['location'] ?? 'مركز الإدارة',
                'driver_name' => $data['driver_name'] ?? null,
                'driver_phone' => $data['driver_phone'] ?? null,
                'notes' => $data['notes'] ?? null,
                'timestamp' => now(),
            ]);

            // Special handling for delivered status
            if ($data['status'] === Order::STATUS_DELIVERED) {
                $order->update(['delivered_at' => now()]);
            }

            return $this->successResponse(
                ['order' => new AdminOrderDetailResource($order->load(['user', 'merchant', 'items.product', 'trackings']))],
                'تم تحديث حالة الطلب بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500',
            ], [
                'reason.required' => 'سبب الإلغاء مطلوب',
                'reason.max' => 'سبب الإلغاء يجب ألا يزيد عن 500 حرف',
            ]);

            $order = Order::findOrFail($id);

            // Can only cancel pending or confirmed orders
            if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED])) {
                return $this->errorResponse('لا يمكن إلغاء هذا الطلب في هذه المرحلة');
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);

            // Restore product stock
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                    $product->update(['is_available' => true]);
                }
            }

            // Add tracking entry
            OrderTracking::create([
                'order_id' => $order->id,
                'status' => Order::STATUS_CANCELLED,
                'status_text' => 'تم إلغاء الطلب',
                'location' => 'مركز الإدارة',
                'notes' => 'السبب: ' . $request->reason,
                'timestamp' => now(),
            ]);

            return $this->successResponse(
                ['order' => new AdminOrderDetailResource($order->load(['user', 'merchant', 'items.product', 'trackings']))],
                'تم إلغاء الطلب بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Add tracking update
     */
    public function addTracking(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,confirmed,preparing,shipped,delivered,cancelled',
                'location' => 'required|string|max:255',
                'driver_name' => 'nullable|string|max:255',
                'driver_phone' => 'nullable|string|max:20',
                'notes' => 'nullable|string|max:500',
            ]);

            $order = Order::findOrFail($id);

            OrderTracking::create([
                'order_id' => $order->id,
                'status' => $request->status,
                'status_text' => Order::STATUSES[$request->status],
                'location' => $request->location,
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'notes' => $request->notes,
                'timestamp' => now(),
            ]);

            return $this->successResponse(null, 'تم إضافة تحديث التتبع بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $dateFrom = $request->get('date_from', now()->startOfMonth());
            $dateTo = $request->get('date_to', now()->endOfMonth());

            $stats = [
                'overview' => [
                    'total_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                    'total_revenue' => Order::where('status', 'delivered')
                                           ->whereBetween('created_at', [$dateFrom, $dateTo])
                                           ->sum('total_amount'),
                    'average_order_value' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                                                  ->avg('total_amount'),
                    'orders_today' => Order::whereDate('created_at', today())->count(),
                ],
                'by_status' => Order::selectRaw('status, COUNT(*) as count, SUM(total_amount) as revenue')
                                   ->whereBetween('created_at', [$dateFrom, $dateTo])
                                   ->groupBy('status')
                                   ->get()
                                   ->map(function ($item) {
                                       return [
                                           'status' => $item->status,
                                           'status_text' => Order::STATUSES[$item->status] ?? $item->status,
                                           'count' => $item->count,
                                           'revenue' => $item->revenue,
                                       ];
                                   }),
                'by_merchant' => Order::with('merchant:id,name')
                                     ->selectRaw('merchant_id, COUNT(*) as count, SUM(total_amount) as revenue')
                                     ->whereBetween('created_at', [$dateFrom, $dateTo])
                                     ->groupBy('merchant_id')
                                     ->orderBy('revenue', 'desc')
                                     ->take(10)
                                     ->get(),
                'daily_orders' => Order::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
                                      ->whereBetween('created_at', [$dateFrom, $dateTo])
                                      ->groupBy('date')
                                      ->orderBy('date')
                                      ->get(),
                'top_customers' => Order::with('user:id,name,email')
                                       ->selectRaw('user_id, COUNT(*) as order_count, SUM(total_amount) as total_spent')
                                       ->whereBetween('created_at', [$dateFrom, $dateTo])
                                       ->groupBy('user_id')
                                       ->orderBy('total_spent', 'desc')
                                       ->take(10)
                                       ->get(),
            ];

            return $this->successResponse(['statistics' => $stats]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['user', 'merchant']);

            // Apply same filters as index
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $orders = $query->get();

            $csvData = [];
            $csvData[] = [
                'رقم الطلب',
                'العميل',
                'التاجر',
                'الحالة',
                'المبلغ الإجمالي',
                'طريقة الدفع',
                'تاريخ الإنشاء',
                'تاريخ التوصيل'
            ];

            foreach ($orders as $order) {
                $csvData[] = [
                    $order->order_number,
                    $order->user->name,
                    $order->merchant->name,
                    $order->status_text,
                    $order->total_amount . ' ' . $order->currency,
                    $this->getPaymentMethodText($order->payment_method),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : '-',
                ];
            }

            return $this->successResponse([
                'csv_data' => $csvData,
                'filename' => 'orders_' . now()->format('Y_m_d_H_i_s') . '.csv'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Validate status transition
     */
    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $transitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['preparing', 'cancelled'],
            'preparing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
        ];

        return in_array($newStatus, $transitions[$currentStatus] ?? []);
    }

    /**
     * Get payment method text
     */
    private function getPaymentMethodText(string $method): string
    {
        return match($method) {
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'credit_card' => 'بطاقة ائتمان',
            'wallet' => 'محفظة إلكترونية',
            default => $method,
        };
    }
}
