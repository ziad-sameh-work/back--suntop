<?php

namespace App\Modules\Orders\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Orders\Services\OrderService;
use App\Modules\Orders\Requests\CreateOrderRequest;
use App\Modules\Orders\Resources\OrderResource;
use App\Modules\Orders\Resources\OrderDetailResource;
use App\Modules\Orders\Resources\TrackingResource;
use App\Modules\Notifications\Services\OrderNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected $orderService;
    protected $notificationService;

    public function __construct(OrderService $orderService, OrderNotificationService $notificationService)
    {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Create new order
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $order = $this->orderService->createOrder($data);

            // Send notification to admin about new order
            $this->notificationService->sendAdminOrderNotification(
                $order->order_number,
                $order->status,
                [
                    'order_id' => $order->id,
                    'user_name' => $request->user()->name,
                    'total_amount' => $order->total_amount,
                    'items_count' => $order->items->count(),
                ]
            );

            return $this->successResponse(
                ['order' => new OrderDetailResource($order)],
                'تم إنشاء الطلب بنجاح',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get user orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->get('status'),
                'user_id' => $request->user()->id,
            ];

            $perPage = $request->get('limit', 20);
            $orders = $this->orderService->getUserOrders($filters, $perPage);

            return $this->successResponse([
                'orders' => OrderResource::collection($orders),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'total_pages' => $orders->lastPage(),
                    'has_next' => $orders->hasMorePages(),
                    'has_prev' => $orders->currentPage() > 1,
                ],
                'summary' => [
                    'pending_count' => $this->orderService->getUserOrdersCountByStatus($request->user()->id, 'pending'),
                    'confirmed_count' => $this->orderService->getUserOrdersCountByStatus($request->user()->id, 'confirmed'),
                    'shipped_count' => $this->orderService->getUserOrdersCountByStatus($request->user()->id, 'shipped'),
                    'delivered_count' => $this->orderService->getUserOrdersCountByStatus($request->user()->id, 'delivered'),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get order details
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id, $request->user()->id);

            if (!$order) {
                return $this->errorResponse('الطلب غير موجود', null, 404);
            }

            return $this->successResponse(new OrderDetailResource($order));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Track order
     */
    public function tracking(Request $request, string $id): JsonResponse
    {
        try {
            $tracking = $this->orderService->getOrderTracking($id, $request->user()->id);

            if (!$tracking) {
                return $this->errorResponse('معلومات التتبع غير متوفرة', null, 404);
            }

            return $this->successResponse(new TrackingResource($tracking));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        try {
            $result = $this->orderService->cancelOrder($id, $request->user()->id);

            if (!$result) {
                return $this->errorResponse('لا يمكن إلغاء هذا الطلب');
            }

            return $this->successResponse(null, 'تم إلغاء الطلب بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Reorder
     */
    public function reorder(Request $request, string $id): JsonResponse
    {
        try {
            $newOrder = $this->orderService->reorder($id, $request->user()->id);

            return $this->successResponse(
                ['order' => new OrderDetailResource($newOrder)],
                'تم إعادة الطلب بنجاح',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get real-time order status
     */
    public function getOrderStatus(Request $request, string $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id, $request->user()->id);

            if (!$order) {
                return $this->errorResponse('الطلب غير موجود', null, 404);
            }

            $latestTracking = $order->trackings()->latest('timestamp')->first();

            return $this->successResponse([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_text' => $order->status_text,
                'latest_tracking' => $latestTracking ? [
                    'location' => $latestTracking->location,
                    'driver_name' => $latestTracking->driver_name,
                    'driver_phone' => $latestTracking->driver_phone,
                    'notes' => $latestTracking->notes,
                    'timestamp' => $latestTracking->timestamp->toISOString(),
                    'time_ago' => $latestTracking->timestamp->diffForHumans(),
                ] : null,
                'estimated_delivery_time' => $order->estimated_delivery_time?->toISOString(),
                'delivered_at' => $order->delivered_at?->toISOString(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get order history for user
     */
    public function history(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderService->getUserOrderHistory($request->user()->id);

            return $this->successResponse([
                'recent_orders' => OrderResource::collection($orders['recent']),
                'favorite_items' => $orders['favorite_items'],
                'statistics' => [
                    'total_orders' => $orders['stats']['total_orders'],
                    'total_spent' => $orders['stats']['total_spent'],
                    'avg_order_value' => $orders['stats']['avg_order_value'],
                    'favorite_merchant' => $orders['stats']['favorite_merchant'],
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Rate order
     */
    public function rate(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);

            $result = $this->orderService->rateOrder(
                $id,
                $request->user()->id,
                $request->rating,
                $request->review
            );

            if (!$result) {
                return $this->errorResponse('لا يمكن تقييم هذا الطلب');
            }

            return $this->successResponse(null, 'تم تقييم الطلب بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
