<?php

namespace App\Modules\Loyalty\Models;

use App\Models\User;
use App\Modules\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;

class RewardRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'reward_id',
        'order_id',
        'redemption_code',
        'points_deducted',
        'discount_amount',
        'status',
        'expires_at',
        'used_at',
        'metadata',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'في الانتظار',
        self::STATUS_USED => 'مستخدم',
        self::STATUS_EXPIRED => 'منتهي الصلاحية',
        self::STATUS_CANCELLED => 'ملغي',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reward relationship
     */
    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Order relationship
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for active redemptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope for expired redemptions
     */
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', self::STATUS_EXPIRED)
              ->orWhere('expires_at', '<=', now());
        });
    }

    /**
     * Check if redemption is valid
     */
    public function isValid(): bool
    {
        return $this->status === self::STATUS_PENDING && 
               $this->expires_at > now();
    }

    /**
     * Mark as used
     */
    public function markAsUsed(Order $order = null): void
    {
        $this->update([
            'status' => self::STATUS_USED,
            'used_at' => now(),
            'order_id' => $order ? $order->id : null,
        ]);
    }

    /**
     * Mark as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
        ]);

        // Refund points to user
        LoyaltyPoint::create([
            'user_id' => $this->user_id,
            'points' => $this->points_deducted,
            'type' => LoyaltyPoint::TYPE_ADMIN_AWARD,
            'description' => "استرداد نقاط من انتهاء صلاحية المكافأة: {$this->reward->title}",
            'reference_type' => self::class,
            'reference_id' => $this->id,
        ]);
    }

    /**
     * Cancel redemption
     */
    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);

        // Refund points to user
        LoyaltyPoint::create([
            'user_id' => $this->user_id,
            'points' => $this->points_deducted,
            'type' => LoyaltyPoint::TYPE_ADMIN_AWARD,
            'description' => "استرداد نقاط من إلغاء المكافأة: {$this->reward->title}",
            'reference_type' => self::class,
            'reference_id' => $this->id,
        ]);
    }

    /**
     * Get status name in Arabic
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
