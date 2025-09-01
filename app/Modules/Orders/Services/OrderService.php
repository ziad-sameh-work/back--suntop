<?php

namespace App\Modules\Orders\Services;

use App\Modules\Core\BaseService;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Orders\Models\OrderTracking;
use App\Modules\Products\Services\ProductService;

use App\Modules\Loyalty\Services\LoyaltyService;
use App\Modules\Offers\Services\OfferService;
use App\Modules\Users\Services\UserCategoryService;
use App\Modules\Notifications\Services\NotificationService;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    protected $productService;
    protected $loyaltyService;
    protected $offerService;
    protected $userCategoryService;
    protected $notificationService;

    public function __construct(
        Order $order,
        ProductService $productService,
        LoyaltyService $loyaltyService,
        OfferService $offerService,
        UserCategoryService $userCategoryService,
        NotificationService $notificationService
    ) {
        $this->model = $order;
        $this->productService = $productService;
        $this->loyaltyService = $loyaltyService;
        $this->offerService = $offerService;
        $this->userCategoryService = $userCategoryService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create new order
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Get user for category discount
            $user = User::findOrFail($data['user_id']);

            // Validate products and calculate totals with carton/package support
            $orderItems = $this->validateAndCalculateItemsWithCartons($data['items']);
            $subtotal = $orderItems->sum('total_price');

            // Calculate delivery fee (fixed rate since no merchants)
            $deliveryFee = 15.0; // Default delivery fee

            // Apply offers if any
            $discount = 0;
            if (isset($data['offer_code'])) {
                $discount = $this->offerService->applyOffer($data['offer_code'], $subtotal);
            }

            // Apply user category discount based on carton/package purchases
            $categoryDiscount = $this->userCategoryService->applyCategoryDiscountToItems($user, $orderItems->toArray());

            // Apply loyalty points discount
            $loyaltyDiscount = 0;
            if (isset($data['use_loyalty_points']) && $data['use_loyalty_points'] > 0) {
                $loyaltyDiscount = $this->loyaltyService->applyLoyaltyDiscount(
                    $data['user_id'],
                    $data['use_loyalty_points']
                );
            }

            // Calculate tax (14% in Egypt)
            $taxableAmount = $subtotal + $deliveryFee - $discount - $categoryDiscount - $loyaltyDiscount;
            $tax = $taxableAmount * 0.14;

            // Calculate total
            $totalAmount = $subtotal + $deliveryFee + $tax - $discount - $categoryDiscount - $loyaltyDiscount;

            // Create order
            $order = $this->create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $data['user_id'],
                'status' => Order::STATUS_PENDING,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'category_discount' => $categoryDiscount,
                'loyalty_discount' => $loyaltyDiscount,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'currency' => 'EGP',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'delivery_address' => $data['delivery_address'],
                'tracking_number' => Order::generateTrackingNumber(),
                'estimated_delivery_time' => now()->addMinutes(45), // Default 45 minutes delivery
                'notes' => $data['notes'] ?? null,
            ]);

            // Create order items with carton/package data
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_image' => $item['product_image'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'selling_type' => $item['selling_type'],
                    'cartons_count' => $item['cartons_count'],
                    'packages_count' => $item['packages_count'],
                    'units_count' => $item['units_count'],
                ]);

                // Update product stock (using actual units)
                $this->productService->updateStock($item['product_id'], $item['units_count']);
            }

            // Create initial tracking
            OrderTracking::create([
                'order_id' => $order->id,
                'status' => Order::STATUS_PENDING,
                'status_text' => 'في انتظار التأكيد',
                'location' => $merchant->name,
                'timestamp' => now(),
            ]);

            // Award loyalty points based on cartons/packages/units
            $this->loyaltyService->awardPointsFromOrder($data['user_id'], $orderItems->toArray(), $order->id);

            // Calculate totals for user stats
            $totalCartons = $orderItems->sum('cartons_count');
            $totalPackages = $orderItems->sum('packages_count');
            $totalUnits = $orderItems->sum('units_count');

            // Update user purchase statistics and category
            $user->updatePurchaseStats($totalAmount, $totalCartons, $totalPackages, $totalUnits);

            // Award bonus points for bulk purchases
            if ($totalCartons >= 5) {
                $this->loyaltyService->awardCartonBonusPoints($data['user_id'], $totalCartons);
            }
            if ($totalPackages >= 10) {
                $this->loyaltyService->awardPackageBonusPoints($data['user_id'], $totalPackages);
            }

            // Send order confirmation notification
            try {
                $this->notificationService->createOrderStatusNotification(
                    $order->user_id,
                    $order->order_number,
                    Order::STATUS_PENDING,
                    [
                        'order_id' => $order->id,
                        'tracking_number' => $order->tracking_number,
                        'total_amount' => $order->total_amount,
                        'merchant_name' => 'سن توب',
                        'estimated_delivery' => $order->estimated_delivery_time,
                    ]
                );
            } catch (\Exception $e) {
                // Log error but don't fail the order creation
                \Log::error('Failed to send order confirmation notification: ' . $e->getMessage());
            }

            return $order->load(['items']);
        });
    }

    /**
     * Get user orders
     */
    public function getUserOrders(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['items'])
                            ->forUser($filters['user_id']);

        if ($filters['status']) {
            $query->byStatus($filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get order by ID for user
     */
    public function getOrderById(string $id, int $userId): ?Order
    {
        return $this->model->with(['items.product', 'trackings'])
                          ->forUser($userId)
                          ->find($id);
    }

    /**
     * Get order tracking
     */
    public function getOrderTracking(string $orderId, int $userId): ?Order
    {
        return $this->model->with(['trackings' => function ($query) {
            $query->orderBy('timestamp', 'asc');
        }])
        ->forUser($userId)
        ->find($orderId);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(string $orderId, int $userId): bool
    {
        $order = $this->model->forUser($userId)->find($orderId);

        if (!$order) {
            return false;
        }

        // Can only cancel pending or confirmed orders
        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED])) {
            throw new \Exception('لا يمكن إلغاء هذا الطلب في هذه المرحلة');
        }

        return DB::transaction(function () use ($order) {
            // Update order status
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
                'location' => 'النظام',
                'timestamp' => now(),
            ]);

            return true;
        });
    }

    /**
     * Reorder
     */
    public function reorder(string $orderId, int $userId): Order
    {
        $originalOrder = $this->model->with('items')->forUser($userId)->find($orderId);

        if (!$originalOrder) {
            throw new \Exception('الطلب الأصلي غير موجود');
        }

        // Prepare items data
        $items = $originalOrder->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];
        })->toArray();

        // Create new order with same data
        $orderData = [
            'user_id' => $userId,
            'merchant_id' => $originalOrder->merchant_id,
            'items' => $items,
            'delivery_address' => $originalOrder->delivery_address,
            'payment_method' => $originalOrder->payment_method,
        ];

        return $this->createOrder($orderData);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(string $orderId, string $status, array $trackingData = []): bool
    {
        $order = $this->findByIdOrFail($orderId);
        
        $order->update(['status' => $status]);

        // Add tracking entry
        OrderTracking::create(array_merge([
            'order_id' => $order->id,
            'status' => $status,
            'status_text' => Order::STATUSES[$status] ?? $status,
            'timestamp' => now(),
        ], $trackingData));

        // Mark as delivered
        if ($status === Order::STATUS_DELIVERED) {
            $order->update(['delivered_at' => now()]);
        }

        // Send notification to user about status change
        try {
            $this->notificationService->createOrderStatusNotification(
                $order->user_id,
                $order->order_number,
                $status,
                [
                    'order_id' => $order->id,
                    'tracking_number' => $order->tracking_number,
                    'total_amount' => $order->total_amount,
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't fail the order update
            \Log::error('Failed to send order status notification: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * Validate and calculate order items with carton/package support
     */
    private function validateAndCalculateItemsWithCartons(array $items): SupportCollection
    {
        $productIds = collect($items)->pluck('product_id');
        $products = $this->productService->getProductsByIds($productIds->toArray())->keyBy('id');

        return collect($items)->map(function ($item) use ($products) {
            $product = $products->get($item['product_id']);

            if (!$product) {
                throw new \Exception("المنتج غير موجود: {$item['product_id']}");
            }

            if (!$product->is_available) {
                throw new \Exception("المنتج غير متوفر: {$product->name}");
            }

            // Determine selling type (default to 'unit' if not specified)
            $sellingType = $item['selling_type'] ?? 'unit';
            
            // Validate selling type is available for this product
            $availableTypes = $product->getAvailableSellingTypes();
            if (!in_array($sellingType, $availableTypes)) {
                throw new \Exception("نوع البيع '{$sellingType}' غير متوفر للمنتج: {$product->name}");
            }

            // Check minimum purchase requirements
            $requirements = $product->requiresMinimumPurchase();
            if (!empty($requirements) && !in_array($sellingType, $requirements)) {
                throw new \Exception("المنتج {$product->name} يتطلب شراء بالكرتون أو العلبة فقط");
            }

            // Calculate actual units needed
            $requestedQuantity = $item['quantity'];
            $actualUnits = $product->calculateActualQuantity($requestedQuantity, $sellingType);

            // Check stock availability (based on actual units)
            if ($product->stock_quantity < $actualUnits) {
                throw new \Exception("الكمية المطلوبة غير متوفرة للمنتج: {$product->name}");
            }

            // Get pricing
            $unitPrice = $product->getEffectivePrice($sellingType);

            // Calculate cartons, packages, and units
            $cartonsCount = 0;
            $packagesCount = 0;
            $unitsCount = 0;

            switch ($sellingType) {
                case 'carton':
                    $cartonsCount = $requestedQuantity;
                    $unitsCount = $actualUnits;
                    break;
                case 'package':
                    $packagesCount = $requestedQuantity;
                    $unitsCount = $actualUnits;
                    break;
                default: // unit
                    $unitsCount = $requestedQuantity;
                    break;
            }

            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $product->image_url,
                'quantity' => $requestedQuantity,
                'unit_price' => $unitPrice,
                'total_price' => $unitPrice * $requestedQuantity,
                'selling_type' => $sellingType,
                'cartons_count' => $cartonsCount,
                'packages_count' => $packagesCount,
                'units_count' => $unitsCount,
                // Add loyalty points data for the service
                'carton_loyalty_points' => $product->carton_loyalty_points,
                'package_loyalty_points' => $product->package_loyalty_points,
                'unit_loyalty_points' => $product->unit_loyalty_points,
            ];
        });
    }

    /**
     * Get user orders count by status
     */
    public function getUserOrdersCountByStatus(int $userId, string $status): int
    {
        return $this->model->forUser($userId)->byStatus($status)->count();
    }

    /**
     * Get user order history with statistics
     */
    public function getUserOrderHistory(int $userId): array
    {
        $recentOrders = $this->model->with(['merchant:id,name', 'items.product'])
                                  ->forUser($userId)
                                  ->orderBy('created_at', 'desc')
                                  ->take(10)
                                  ->get();

        $stats = [
            'total_orders' => $this->model->forUser($userId)->count(),
            'total_spent' => $this->model->forUser($userId)->where('status', 'delivered')->sum('total_amount'),
            'avg_order_value' => $this->model->forUser($userId)->where('status', 'delivered')->avg('total_amount'),
            'favorite_merchant' => $this->model->with('merchant:id,name')
                                             ->forUser($userId)
                                             ->selectRaw('merchant_id, COUNT(*) as count')
                                             ->groupBy('merchant_id')
                                             ->orderBy('count', 'desc')
                                             ->first()
                                             ?->merchant
                                             ?->name ?? 'لا يوجد',
        ];

        // Get most ordered items
        $favoriteItems = OrderItem::whereHas('order', function ($q) use ($userId) {
                                      $q->where('user_id', $userId);
                                  })
                                  ->with('product:id,name,image_url,price')
                                  ->selectRaw('product_id, SUM(quantity) as total_quantity, COUNT(*) as order_count')
                                  ->groupBy('product_id')
                                  ->orderBy('total_quantity', 'desc')
                                  ->take(5)
                                  ->get()
                                  ->map(function ($item) {
                                      return [
                                          'product' => $item->product,
                                          'total_quantity' => $item->total_quantity,
                                          'order_count' => $item->order_count,
                                      ];
                                  });

        return [
            'recent' => $recentOrders,
            'stats' => $stats,
            'favorite_items' => $favoriteItems,
        ];
    }

    /**
     * Rate order
     */
    public function rateOrder(string $orderId, int $userId, int $rating, ?string $review = null): bool
    {
        $order = $this->model->forUser($userId)->find($orderId);

        if (!$order || $order->status !== Order::STATUS_DELIVERED) {
            return false;
        }

        // Create or update review
        $reviewData = [
            'user_id' => $userId,
            'order_id' => $order->id,
            'rating' => $rating,
            'review' => $review,
        ];

        // You would create a reviews table and model for this
        // For now, we'll just add it to order notes
        $order->update([
            'notes' => ($order->notes ? $order->notes . "\n\n" : '') . 
                      "تقييم العميل: {$rating}/5" . 
                      ($review ? "\nالتعليق: {$review}" : '')
        ]);

        return true;
    }

    /**
     * Legacy method: Validate and calculate order items
     */
    private function validateAndCalculateItems(array $items): SupportCollection
    {
        // Convert legacy format to new format and use new method
        $itemsWithSellingType = collect($items)->map(function ($item) {
            return array_merge($item, ['selling_type' => 'unit']);
        })->toArray();

        return $this->validateAndCalculateItemsWithCartons($itemsWithSellingType);
    }
}
