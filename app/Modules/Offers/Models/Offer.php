<?php

namespace App\Modules\Offers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'user_category_id',
        'discount_percentage',
        'discount_amount',
        'minimum_amount',
        'maximum_discount',
        'valid_from',
        'valid_until',
        'is_active',
        'image_url',
        'applicable_categories',
        'applicable_products',
        'first_order_only',
        'is_featured',
        'background_color',
        'text_color',
        'display_order',
        'short_description',
        'min_purchase_amount',
        'offer_tag',
        'trend_score',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'first_order_only' => 'boolean',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
    ];

    /**
     * Relationship with UserCategory
     */
    public function userCategory()
    {
        return $this->belongsTo(\App\Modules\Users\Models\UserCategory::class);
    }

    /**
     * Check if the offer is currently valid
     */
    public function isValid()
    {
        $now = now();
        return $this->is_active 
            && $this->valid_from <= $now 
            && $this->valid_until >= $now;
    }

    /**
     * Get the discount value for a given amount
     */
    public function getDiscountValue($amount)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($amount < $this->minimum_amount) {
            return 0;
        }

        if ($this->discount_percentage) {
            $discount = ($amount * $this->discount_percentage) / 100;
            return $this->maximum_discount ? min($discount, $this->maximum_discount) : $discount;
        }

        return $this->discount_amount ?? 0;
    }

    /**
     * Scope for featured offers
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for trending offers
     */
    public function scopeTrending($query)
    {
        return $query->where('trend_score', '>', 0)->orderBy('trend_score', 'desc');
    }

    /**
     * Scope for active and featured offers
     */
    public function scopeActiveFeatured($query)
    {
        return $query->where('is_active', true)
                    ->where('is_featured', true)
                    ->where('valid_from', '<=', now())
                    ->where('valid_until', '>=', now());
    }

    /**
     * Get applicable products with discounted prices
     */
    public function getApplicableProductsWithDiscount()
    {
        if (!$this->applicable_products) {
            return [];
        }

        $products = \App\Modules\Products\Models\Product::whereIn('id', $this->applicable_products)->get();
        
        return $products->map(function($product) {
            $discountedPrice = $product->price;
            
            if ($this->type === self::TYPE_PERCENTAGE) {
                $discountedPrice = $product->price * (1 - $this->discount_percentage / 100);
            } elseif ($this->type === self::TYPE_FIXED_AMOUNT) {
                $discountedPrice = max(0, $product->price - $this->discount_amount);
            }
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'discounted_price' => round($discountedPrice, 2),
            ];
        })->toArray();
    }

    /**
     * Calculate trend score based on activity
     */
    public function updateTrendScore()
    {
        $daysSinceCreated = $this->created_at->diffInDays(now());
        $recencyBonus = max(0, 30 - $daysSinceCreated); // Bonus for newer offers
        $categoryBonus = $this->userCategory ? 10 : 0; // Bonus for category-specific offers
        
        $this->trend_score = $recencyBonus + $categoryBonus + ($this->is_featured ? 20 : 0);
        $this->save();
    }

    /**
     * Get offer tags
     */
    public static function getOfferTags()
    {
        return [
            'new' => 'جديد',
            'exclusive' => 'حصري',
            'limited' => 'محدود',
            'hot' => 'رائج',
            'weekend' => 'نهاية الأسبوع',
            'seasonal' => 'موسمي',
        ];
    }

    /**
     * Get formatted offer tag
     */
    public function getFormattedOfferTagAttribute()
    {
        if (!$this->offer_tag) {
            return null;
        }
        
        $tags = self::getOfferTags();
        return $tags[$this->offer_tag] ?? $this->offer_tag;
    }
}
