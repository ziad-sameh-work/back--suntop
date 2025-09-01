<?php

namespace App\Modules\Orders\Models;

use App\Models\User;
use App\Modules\Merchants\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'delivery_fee',
        'discount',
        'category_discount',
        'loyalty_discount',
        'tax',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        'delivery_address',
        'tracking_number',
        'estimated_delivery_time',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'delivery_address' => 'array',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'category_discount' => 'decimal:2',
        'loyalty_discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'estimated_delivery_time' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'في انتظار التأكيد',
        self::STATUS_CONFIRMED => 'تم تأكيد الطلب',
        self::STATUS_PREPARING => 'جاري التحضير',
        self::STATUS_SHIPPED => 'تم الشحن',
        self::STATUS_DELIVERED => 'تم التوصيل',
        self::STATUS_CANCELLED => 'ملغي',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Order items relationship
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Order tracking relationship
     */
    public function trackings(): HasMany
    {
        return $this->hasMany(OrderTracking::class);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Scope for user orders
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }



    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Generate order number
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = static::whereYear('created_at', $year)
                          ->orderBy('id', 'desc')
                          ->first();

        $nextNumber = $lastOrder ? (int) substr($lastOrder->order_number, -3) + 1 : 1;
        
        return 'ORD-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate tracking number
     */
    public static function generateTrackingNumber(): string
    {
        return 'TRK' . strtoupper(uniqid());
    }
}
