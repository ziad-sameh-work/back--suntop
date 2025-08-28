<?php

namespace App\Modules\Offers\Models;

use App\Models\User;
use App\Modules\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;

class OfferRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'offer_id',
        'order_id',
        'redemption_code',
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
     * Offer relationship
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
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
    }

    /**
     * Cancel redemption
     */
    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Generate unique redemption code
     */
    public static function generateRedemptionCode(): string
    {
        do {
            $code = 'OFF-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('redemption_code', $code)->exists());
        
        return $code;
    }

    /**
     * Get status name in Arabic
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
