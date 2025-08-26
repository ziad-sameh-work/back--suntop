<?php

namespace App\Modules\Loyalty\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'order_id',
        'expires_at',
        'metadata',
        'reference_type',
        'reference_id',
        'is_processed',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'metadata' => 'array',
        'is_processed' => 'boolean',
    ];

    // Constants for point types
    const TYPE_EARNED = 'earned';
    const TYPE_REDEEMED = 'redeemed';
    const TYPE_ADMIN_AWARD = 'admin_award';
    const TYPE_ADMIN_DEDUCT = 'admin_deduct';
    const TYPE_EXPIRED = 'expired';
    const TYPE_BONUS = 'bonus';

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Order
     */
    public function order()
    {
        return $this->belongsTo(\App\Modules\Orders\Models\Order::class);
    }

    /**
     * Polymorphic relationship
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Scope for earned points
     */
    public function scopeEarned($query)
    {
        return $query->where('points', '>', 0);
    }

    /**
     * Scope for redeemed points
     */
    public function scopeRedeemed($query)
    {
        return $query->where('points', '<', 0);
    }

    /**
     * Scope for active points (not expired)
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Get user's total active points
     */
    public static function getUserActivePoints($userId)
    {
        return self::where('user_id', $userId)
                   ->active()
                   ->sum('points');
    }

    /**
     * Get user's lifetime earned points
     */
    public static function getUserLifetimePoints($userId)
    {
        return self::where('user_id', $userId)
                   ->earned()
                   ->sum('points');
    }

    /**
     * Get formatted points display
     */
    public function getFormattedPointsAttribute()
    {
        return $this->points >= 0 ? '+' . number_format($this->points) : number_format($this->points);
    }

    /**
     * Check if points are expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at <= now();
    }
}
