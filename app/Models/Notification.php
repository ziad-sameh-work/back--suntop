<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'body',
        'type',
        'alert_type',
        'user_id',
        'user_category_id',
        'target_type',
        'read_at',
        'data',
        'action_url',
        'priority',
        'is_sent',
        'scheduled_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    protected $attributes = [
        'alert_type' => self::ALERT_INFO,
        'target_type' => self::TARGET_USER,
        'priority' => self::PRIORITY_MEDIUM,
    ];

    // Notification types
    const TYPE_SHIPMENT = 'shipment';
    const TYPE_OFFER = 'offer';
    const TYPE_REWARD = 'reward';
    const TYPE_GENERAL = 'general';
    const TYPE_ORDER_STATUS = 'order_status';
    const TYPE_PAYMENT = 'payment';

    const TYPES = [
        self::TYPE_SHIPMENT => 'شحنة',
        self::TYPE_OFFER => 'عرض',
        self::TYPE_REWARD => 'مكافأة',
        self::TYPE_GENERAL => 'عام',
        self::TYPE_ORDER_STATUS => 'حالة الطلب',
        self::TYPE_PAYMENT => 'دفع',
    ];

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    const PRIORITIES = [
        self::PRIORITY_LOW => 'منخفضة',
        self::PRIORITY_MEDIUM => 'متوسطة',
        self::PRIORITY_HIGH => 'عالية',
    ];

    // Alert types for UI styling
    const ALERT_INFO = 'info';
    const ALERT_SUCCESS = 'success';
    const ALERT_WARNING = 'warning';
    const ALERT_ERROR = 'error';

    const ALERT_TYPES = [
        self::ALERT_INFO => 'معلومات',
        self::ALERT_SUCCESS => 'نجاح',
        self::ALERT_WARNING => 'تحذير',
        self::ALERT_ERROR => 'خطأ',
    ];

    // Target types
    const TARGET_USER = 'user';
    const TARGET_CATEGORY = 'category';
    const TARGET_ALL = 'all';

    const TARGET_TYPES = [
        self::TARGET_USER => 'مستخدم محدد',
        self::TARGET_CATEGORY => 'فئة مستخدمين',
        self::TARGET_ALL => 'جميع المستخدمين',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User Category relationship
     */
    public function userCategory(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Models\UserCategory::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for pending notifications (not sent yet)
     */
    public function scopePending($query)
    {
        return $query->where('is_sent', false)
                    ->where(function($q) {
                        $q->whereNull('scheduled_at')
                          ->orWhere('scheduled_at', '<=', now());
                    });
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Check if notification is read
     */
    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Get type name in Arabic
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Get priority name in Arabic
     */
    public function getPriorityNameAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    /**
     * Get alert type name in Arabic
     */
    public function getAlertTypeNameAttribute(): string
    {
        return self::ALERT_TYPES[$this->alert_type] ?? self::ALERT_TYPES[self::ALERT_INFO];
    }

    /**
     * Get target type name in Arabic
     */
    public function getTargetTypeNameAttribute(): string
    {
        return self::TARGET_TYPES[$this->target_type] ?? self::TARGET_TYPES[self::TARGET_USER];
    }

    /**
     * Get time ago in Arabic
     */
    public function getTimeAgoAttribute(): string
    {
        $diff = $this->created_at->diffForHumans();
        
        // Convert English time to Arabic
        $translations = [
            'second' => 'ثانية',
            'seconds' => 'ثواني',
            'minute' => 'دقيقة',
            'minutes' => 'دقائق',
            'hour' => 'ساعة',
            'hours' => 'ساعات',
            'day' => 'يوم',
            'days' => 'أيام',
            'week' => 'أسبوع',
            'weeks' => 'أسابيع',
            'month' => 'شهر',
            'months' => 'أشهر',
            'year' => 'سنة',
            'years' => 'سنوات',
            'ago' => 'منذ',
            'before' => 'قبل',
        ];

        foreach ($translations as $english => $arabic) {
            $diff = str_replace($english, $arabic, $diff);
        }

        return $diff;
    }

    /**
     * Create notification for specific user
     */
    public static function createForUser(
        int $userId,
        string $title,
        string $message,
        string $type,
        string $body = null,
        string $alertType = self::ALERT_INFO,
        array $data = [],
        string $priority = self::PRIORITY_MEDIUM,
        string $actionUrl = null,
        Carbon $scheduledAt = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'body' => $body,
            'type' => $type,
            'alert_type' => $alertType,
            'target_type' => self::TARGET_USER,
            'data' => $data,
            'priority' => $priority,
            'action_url' => $actionUrl,
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Create notification for user category
     */
    public static function createForUserCategory(
        int $categoryId,
        string $title,
        string $message,
        string $type,
        string $body = null,
        string $alertType = self::ALERT_INFO,
        array $data = [],
        string $priority = self::PRIORITY_MEDIUM,
        string $actionUrl = null,
        Carbon $scheduledAt = null
    ): int {
        // Get all users in this category
        $userIds = \App\Models\User::where('user_category_id', $categoryId)
                                 ->where('is_active', true)
                                 ->pluck('id')
                                 ->toArray();

        if (empty($userIds)) {
            return 0;
        }

        // Create notifications for all users
        $notifications = [];
        $now = now();

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'user_category_id' => $categoryId,
                'title' => $title,
                'message' => $message,
                'body' => $body,
                'type' => $type,
                'alert_type' => $alertType,
                'target_type' => self::TARGET_CATEGORY,
                'data' => json_encode($data),
                'priority' => $priority,
                'action_url' => $actionUrl,
                'scheduled_at' => $scheduledAt,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        self::insert($notifications);
        return count($notifications);
    }

    /**
     * Create notification for all users
     */
    public static function createForAllUsers(
        string $title,
        string $message,
        string $type,
        string $body = null,
        string $alertType = self::ALERT_INFO,
        array $data = [],
        string $priority = self::PRIORITY_MEDIUM,
        string $actionUrl = null,
        Carbon $scheduledAt = null,
        string $roleFilter = null
    ): int {
        // Get all active users (optionally filtered by role)
        $query = \App\Models\User::where('is_active', true);
        
        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }
        
        $userIds = $query->pluck('id')->toArray();

        if (empty($userIds)) {
            return 0;
        }

        // Create notifications for all users
        $notifications = [];
        $now = now();

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'user_category_id' => null,
                'title' => $title,
                'message' => $message,
                'body' => $body,
                'type' => $type,
                'alert_type' => $alertType,
                'target_type' => self::TARGET_ALL,
                'data' => json_encode($data),
                'priority' => $priority,
                'action_url' => $actionUrl,
                'scheduled_at' => $scheduledAt,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        self::insert($notifications);
        return count($notifications);
    }

    /**
     * Create notification for multiple users
     */
    public static function createForUsers(
        array $userIds,
        string $title,
        string $message,
        string $type,
        string $body = null,
        string $alertType = self::ALERT_INFO,
        array $data = [],
        string $priority = self::PRIORITY_MEDIUM,
        string $actionUrl = null,
        Carbon $scheduledAt = null
    ): int {
        $notifications = [];
        $now = now();

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'body' => $body,
                'type' => $type,
                'alert_type' => $alertType,
                'target_type' => self::TARGET_USER,
                'data' => json_encode($data),
                'priority' => $priority,
                'action_url' => $actionUrl,
                'scheduled_at' => $scheduledAt,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        self::insert($notifications);
        return count($notifications);
    }

    /**
     * Create order status notification
     */
    public static function createOrderStatusNotification(
        int $userId,
        string $orderNumber,
        string $status,
        array $additionalData = []
    ): self {
        // Check if custom message is provided
        if (isset($additionalData['custom_message']) && $additionalData['custom_message']) {
            $title = $additionalData['title'] ?? "تحديث حالة الطلب #{$orderNumber}";
            $message = $additionalData['message'] ?? "تم تحديث حالة طلبك";
        } else {
            // Use default messages
            $statusMessages = [
                'pending' => 'طلبك في انتظار التأكيد',
                'confirmed' => 'تم تأكيد طلبك وجاري التحضير',
                'preparing' => 'جاري تحضير طلبك وسيتم إشعاركم عند اكتمال التجهيز',
                'processing' => 'جاري تحضير طلبك',
                'shipping' => 'طلبك جاري الشحن وسيصل إليكم في أقرب وقت',
                'shipped' => 'تم شحن طلبك وسيصل قريباً',
                'delivered' => 'تم توصيل طلبك بنجاح',
                'cancelled' => 'تم إلغاء طلبك',
            ];

            $title = "تحديث حالة الطلب #{$orderNumber}";
            $message = $statusMessages[$status] ?? "تم تحديث حالة طلبك";
        }

        return self::createForUser(
            $userId,
            $title,
            $message,
            self::TYPE_ORDER_STATUS,
            null, // body
            self::ALERT_INFO, // alert type
            array_merge(['order_number' => $orderNumber, 'status' => $status], $additionalData), // data
            self::PRIORITY_HIGH,
            "/orders/{$orderNumber}"
        );
    }

    /**
     * Create loyalty points notification
     */
    public static function createLoyaltyNotification(
        int $userId,
        int $points,
        string $reason,
        string $type = 'earned'
    ): self {
        $title = $type === 'earned' ? 'تم إضافة نقاط ولاء' : 'تم استخدام نقاط الولاء';
        $message = $type === 'earned' 
            ? "تم إضافة {$points} نقطة ولاء إلى حسابك - {$reason}"
            : "تم استخدام {$points} نقطة ولاء من حسابك - {$reason}";

        return self::createForUser(
            $userId,
            $title,
            $message,
            self::TYPE_REWARD,
            null, // body
            self::ALERT_SUCCESS, // alert type
            ['points' => $points, 'reason' => $reason, 'type' => $type], // data
            self::PRIORITY_MEDIUM,
            "/loyalty"
        );
    }

    /**
     * Create offer notification
     */
    public static function createOfferNotification(
        int $userId,
        string $offerTitle,
        string $offerDescription,
        array $offerData = []
    ): self {
        return self::createForUser(
            $userId,
            "عرض جديد: {$offerTitle}",
            $offerDescription,
            self::TYPE_OFFER,
            null, // body
            self::ALERT_INFO, // alert type
            $offerData, // data
            self::PRIORITY_MEDIUM,
            "/offers"
        );
    }
}
