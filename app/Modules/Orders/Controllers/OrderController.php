<?php

namespace App\Modules\Orders\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Orders\Services\OrderService;
use App\Modules\Orders\Requests\CreateOrderRequest;
use App\Modules\Orders\Resources\OrderResource;
use App\Modules\Orders\Resources\OrderDetailResource;
use App\Modules\Orders\Resources\TrackingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
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
}
