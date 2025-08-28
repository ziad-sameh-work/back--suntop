<?php

namespace App\Modules\Notifications\Services;

use App\Modules\Core\BaseService;
use App\Models\Notification;
use App\Modules\Orders\Models\Order;
use App\Models\User;

class OrderNotificationService extends BaseService
{
    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    /**
     * Send order status update notification
     */
    public function sendOrderStatusNotification(int $userId, string $orderNumber, string $status, array $extraData = []): Notification
    {
        $statusMessages = [
            Order::STATUS_PENDING => [
                'title' => 'تم إنشاء طلبك بنجاح',
                'message' => "تم إنشاء طلبك رقم {$orderNumber} وهو في انتظار التأكيد من التاجر"
            ],
            Order::STATUS_CONFIRMED => [
                'title' => 'تم تأكيد طلبك',
                'message' => "تم تأكيد طلبك رقم {$orderNumber} وسيتم تحضيره قريباً"
            ],
            Order::STATUS_PREPARING => [
                'title' => 'جاري تحضير طلبك',
                'message' => "طلبك رقم {$orderNumber} جاري تحضيره الآن"
            ],
            Order::STATUS_SHIPPED => [
                'title' => 'تم شحن طلبك',
                'message' => "تم شحن طلبك رقم {$orderNumber} وهو في الطريق إليك"
            ],
            Order::STATUS_DELIVERED => [
                'title' => 'تم توصيل طلبك',
                'message' => "تم توصيل طلبك رقم {$orderNumber} بنجاح. نأمل أن تكون راضياً عن الخدمة"
            ],
            Order::STATUS_CANCELLED => [
                'title' => 'تم إلغاء طلبك',
                'message' => "تم إلغاء طلبك رقم {$orderNumber}. إذا كان لديك أي استفسار، يمكنك التواصل معنا"
            ],
        ];

        $notificationData = $statusMessages[$status] ?? [
            'title' => 'تحديث على طلبك',
            'message' => "تم تحديث حالة طلبك رقم {$orderNumber}"
        ];

        return $this->create([
            'title' => $notificationData['title'],
            'message' => $notificationData['message'],
            'type' => 'order_status',
            'user_id' => $userId,
            'data' => array_merge([
                'order_number' => $orderNumber,
                'status' => $status,
                'status_text' => Order::STATUSES[$status] ?? $status,
            ], $extraData),
            'action_url' => "/orders/{$extraData['order_id'] ?? ''}",
            'priority' => $this->getNotificationPriority($status),
        ]);
    }

    /**
     * Send tracking update notification
     */
    public function sendTrackingUpdateNotification(int $userId, string $orderNumber, array $trackingData): Notification
    {
        $message = "طلبك رقم {$orderNumber}";
        
        if (isset($trackingData['location'])) {
            $message .= " موجود الآن في: {$trackingData['location']}";
        }
        
        if (isset($trackingData['driver_name'])) {
            $message .= " مع السائق: {$trackingData['driver_name']}";
        }

        return $this->create([
            'title' => 'تحديث على موقع طلبك',
            'message' => $message,
            'type' => 'order_tracking',
            'user_id' => $userId,
            'data' => array_merge([
                'order_number' => $orderNumber,
            ], $trackingData),
            'action_url' => "/orders/{$trackingData['order_id'] ?? ''}/tracking",
            'priority' => 'medium',
        ]);
    }

    /**
     * Send delivery notification
     */
    public function sendDeliveryNotification(int $userId, string $orderNumber, array $deliveryData): Notification
    {
        $message = "طلبك رقم {$orderNumber} سيصل إليك خلال ";
        
        if (isset($deliveryData['estimated_minutes'])) {
            $message .= "{$deliveryData['estimated_minutes']} دقيقة";
        } else {
            $message .= "وقت قريب";
        }

        if (isset($deliveryData['driver_name'])) {
            $message .= " مع السائق: {$deliveryData['driver_name']}";
        }

        if (isset($deliveryData['driver_phone'])) {
            $message .= " - للتواصل: {$deliveryData['driver_phone']}";
        }

        return $this->create([
            'title' => 'طلبك في الطريق إليك',
            'message' => $message,
            'type' => 'order_delivery',
            'user_id' => $userId,
            'data' => array_merge([
                'order_number' => $orderNumber,
            ], $deliveryData),
            'action_url' => "/orders/{$deliveryData['order_id'] ?? ''}/tracking",
            'priority' => 'high',
        ]);
    }

    /**
     * Send admin order notification
     */
    public function sendAdminOrderNotification(string $orderNumber, string $status, array $orderData): void
    {
        // Get all admin users
        $adminUsers = User::where('role', 'admin')->get();

        $statusMessages = [
            Order::STATUS_PENDING => "طلب جديد رقم {$orderNumber} في انتظار التأكيد",
            Order::STATUS_CONFIRMED => "تم تأكيد الطلب رقم {$orderNumber}",
            Order::STATUS_PREPARING => "جاري تحضير الطلب رقم {$orderNumber}",
            Order::STATUS_SHIPPED => "تم شحن الطلب رقم {$orderNumber}",
            Order::STATUS_DELIVERED => "تم توصيل الطلب رقم {$orderNumber}",
            Order::STATUS_CANCELLED => "تم إلغاء الطلب رقم {$orderNumber}",
        ];

        foreach ($adminUsers as $admin) {
            $this->create([
                'title' => 'تحديث على الطلبات',
                'message' => $statusMessages[$status] ?? "تحديث على الطلب رقم {$orderNumber}",
                'type' => 'admin_order_update',
                'user_id' => $admin->id,
                'data' => array_merge([
                    'order_number' => $orderNumber,
                    'status' => $status,
                ], $orderData),
                'action_url' => "/admin/orders/{$orderData['order_id'] ?? ''}",
                'priority' => $status === Order::STATUS_PENDING ? 'high' : 'medium',
            ]);
        }
    }

    /**
     * Get notification priority based on status
     */
    private function getNotificationPriority(string $status): string
    {
        return match($status) {
            Order::STATUS_SHIPPED, Order::STATUS_DELIVERED => 'high',
            Order::STATUS_CANCELLED => 'high',
            Order::STATUS_CONFIRMED, Order::STATUS_PREPARING => 'medium',
            default => 'medium',
        };
    }

    /**
     * Send bulk status update notifications
     */
    public function sendBulkStatusNotifications(array $orderIds, string $status, array $extraData = []): int
    {
        $orders = Order::with('user')->whereIn('id', $orderIds)->get();
        $count = 0;

        foreach ($orders as $order) {
            try {
                $this->sendOrderStatusNotification(
                    $order->user_id,
                    $order->order_number,
                    $status,
                    array_merge($extraData, ['order_id' => $order->id])
                );
                $count++;
            } catch (\Exception $e) {
                \Log::error("Failed to send notification for order {$order->id}: " . $e->getMessage());
            }
        }

        return $count;
    }
}
