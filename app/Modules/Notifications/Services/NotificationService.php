<?php

namespace App\Modules\Notifications\Services;

use App\Models\Notification;
use App\Models\User;
use App\Modules\Core\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class NotificationService extends BaseService
{
    protected $model;

    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    /**
     * Get notifications for a user with filters
     */
    public function getUserNotifications(
        int $userId,
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = $this->model->where('user_id', $userId);

        // Apply filters
        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_read'])) {
            if ($filters['is_read'] === true || $filters['is_read'] === 'true') {
                $query->whereNotNull('read_at');
            } elseif ($filters['is_read'] === false || $filters['is_read'] === 'false') {
                $query->whereNull('read_at');
            }
        }

        if (isset($filters['priority']) && $filters['priority']) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    /**
     * Get unread notifications count for user
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->model->where('user_id', $userId)->unread()->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = $this->model->where('id', $notificationId)
                                   ->where('user_id', $userId)
                                   ->first();

        if (!$notification) {
            return false;
        }

        $notification->markAsRead();
        return true;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(int $userId): int
    {
        return $this->model->where('user_id', $userId)
                          ->whereNull('read_at')
                          ->update(['read_at' => now()]);
    }

    /**
     * Delete notification
     */
    public function deleteNotification(int $notificationId, int $userId): bool
    {
        return $this->model->where('id', $notificationId)
                          ->where('user_id', $userId)
                          ->delete() > 0;
    }

    /**
     * Delete all notifications for user
     */
    public function deleteAllForUser(int $userId): int
    {
        return $this->model->where('user_id', $userId)->delete();
    }

    /**
     * Create notification for user
     */
    public function createNotification(
        int $userId,
        string $title,
        string $message,
        string $type,
        array $data = [],
        string $priority = Notification::PRIORITY_MEDIUM,
        string $actionUrl = null,
        Carbon $scheduledAt = null
    ): Notification {
        return Notification::createForUser(
            $userId,
            $title,
            $message,
            $type,
            $data,
            $priority,
            $actionUrl,
            $scheduledAt
        );
    }

    /**
     * Create notification for multiple users
     */
    public function createBulkNotification(
        array $userIds,
        string $title,
        string $message,
        string $type,
        array $data = [],
        string $priority = Notification::PRIORITY_MEDIUM,
        string $actionUrl = null,
        Carbon $scheduledAt = null
    ): int {
        return Notification::createForUsers(
            $userIds,
            $title,
            $message,
            $type,
            $data,
            $priority,
            $actionUrl,
            $scheduledAt
        );
    }

    /**
     * Create order status notification
     */
    public function createOrderStatusNotification(
        int $userId,
        string $orderNumber,
        string $status,
        array $additionalData = []
    ): Notification {
        return Notification::createOrderStatusNotification(
            $userId,
            $orderNumber,
            $status,
            $additionalData
        );
    }

    /**
     * Create loyalty points notification
     */
    public function createLoyaltyNotification(
        int $userId,
        int $points,
        string $reason,
        string $type = 'earned'
    ): Notification {
        return Notification::createLoyaltyNotification($userId, $points, $reason, $type);
    }

    /**
     * Create offer notification for eligible users
     */
    public function createOfferNotification(
        string $offerTitle,
        string $offerDescription,
        array $offerData = [],
        array $userIds = null
    ): int {
        // If no specific users provided, send to all active users
        if ($userIds === null) {
            $userIds = User::where('is_active', true)->pluck('id')->toArray();
        }

        return $this->createBulkNotification(
            $userIds,
            "عرض جديد: {$offerTitle}",
            $offerDescription,
            Notification::TYPE_OFFER,
            $offerData,
            Notification::PRIORITY_MEDIUM,
            "/offers"
        );
    }

    /**
     * Get notification statistics for user
     */
    public function getUserNotificationStats(int $userId): array
    {
        $total = $this->model->where('user_id', $userId)->count();
        $unread = $this->model->where('user_id', $userId)->unread()->count();
        $byType = $this->model->where('user_id', $userId)
                             ->selectRaw('type, count(*) as count')
                             ->groupBy('type')
                             ->pluck('count', 'type')
                             ->toArray();

        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $total - $unread,
            'by_type' => $byType,
        ];
    }

    /**
     * Get all notifications for admin (with pagination)
     */
    public function getAllNotifications(
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = $this->model->with('user:id,name,email');

        // Apply filters
        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_read'])) {
            if ($filters['is_read'] === true || $filters['is_read'] === 'true') {
                $query->whereNotNull('read_at');
            } elseif ($filters['is_read'] === false || $filters['is_read'] === 'false') {
                $query->whereNull('read_at');
            }
        }

        if (isset($filters['priority']) && $filters['priority']) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    /**
     * Get notification statistics for admin
     */
    public function getAdminNotificationStats(): array
    {
        $total = $this->model->count();
        $unread = $this->model->unread()->count();
        $byType = $this->model->selectRaw('type, count(*) as count')
                             ->groupBy('type')
                             ->pluck('count', 'type')
                             ->toArray();
        $byPriority = $this->model->selectRaw('priority, count(*) as count')
                                 ->groupBy('priority')
                                 ->pluck('count', 'priority')
                                 ->toArray();

        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $total - $unread,
            'by_type' => $byType,
            'by_priority' => $byPriority,
        ];
    }

    /**
     * Clean old notifications (older than specified days)
     */
    public function cleanOldNotifications(int $daysOld = 30): int
    {
        return $this->model->where('created_at', '<', now()->subDays($daysOld))->delete();
    }

    /**
     * Get pending notifications to be sent
     */
    public function getPendingNotifications(): Collection
    {
        return $this->model->pending()->get();
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent(int $notificationId): bool
    {
        return $this->model->where('id', $notificationId)
                          ->update(['is_sent' => true]) > 0;
    }
}
