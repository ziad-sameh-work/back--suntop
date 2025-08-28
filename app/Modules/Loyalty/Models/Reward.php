<?php

namespace App\Modules\Loyalty\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'points_cost',
        'discount_percentage',
        'discount_amount',
        'cashback_amount',
        'bonus_points',
        'free_product_id',
        'image_url',
        'category',
        'expiry_days',
        'usage_limit',
        'used_count',
        'is_active',
        'applicable_categories',
        'applicable_products',
        'minimum_order_amount',
        'terms_conditions',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'cashback_amount' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
    ];

    // Constants for reward types
    const TYPE_DISCOUNT = 'discount';
    const TYPE_FREE_PRODUCT = 'free_product';
    const TYPE_CASHBACK = 'cashback';
    const TYPE_BONUS_POINTS = 'bonus_points';

    const TYPES = [
        self::TYPE_DISCOUNT => 'خصم',
        self::TYPE_FREE_PRODUCT => 'منتج مجاني',
        self::TYPE_CASHBACK => 'استرداد نقدي',
        self::TYPE_BONUS_POINTS => 'نقاط إضافية',
    ];

    /**
     * Scope for active rewards
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by points range
     */
    public function scopeByPointsRange($query, $minPoints, $maxPoints = null)
    {
        $query->where('points_cost', '>=', $minPoints);
        if ($maxPoints) {
            $query->where('points_cost', '<=', $maxPoints);
        }
        return $query;
    }

    /**
     * Check if reward is available for redemption
     */
    public function isAvailable(): bool
    {
        return $this->is_active && 
               ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    /**
     * Generate unique redemption code
     */
    public static function generateRedemptionCode(): string
    {
        do {
            $code = 'RWD-' . strtoupper(substr(uniqid(), -8));
        } while (RewardRedemption::where('redemption_code', $code)->exists());
        
        return $code;
    }

    /**
     * Get reward redemptions
     */
    public function redemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Get active redemptions
     */
    public function activeRedemptions()
    {
        return $this->redemptions()->where('status', 'pending');
    }

    /**
     * Get type name in Arabic
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Get formatted points cost
     */
    public function getFormattedPointsCostAttribute(): string
    {
        return number_format($this->points_cost) . ' نقطة';
    }

    /**
     * Check if user can redeem this reward
     */
    public function canBeRedeemedBy(User $user): bool
    {
        $userPoints = LoyaltyPoint::getUserActivePoints($user->id);
        return $this->isAvailable() && $userPoints >= $this->points_cost;
    }
}
