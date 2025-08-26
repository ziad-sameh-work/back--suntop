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
        'code',
        'type',
        'discount_percentage',
        'discount_amount',
        'minimum_amount',
        'maximum_discount',
        'valid_from',
        'valid_until',
        'usage_limit',
        'used_count',
        'is_active',
        'image_url',
        'applicable_categories',
        'applicable_products',
        'first_order_only',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'first_order_only' => 'boolean',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
    ];

    // Constants for offer types
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED_AMOUNT = 'fixed_amount';

    /**
     * Generate a unique offer code
     */
    public static function generateCode($prefix = 'OFFER')
    {
        do {
            $code = $prefix . strtoupper(substr(uniqid(), -6));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * Check if the offer is currently valid
     */
    public function isValid()
    {
        $now = now();
        return $this->is_active 
            && $this->valid_from <= $now 
            && $this->valid_until >= $now
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
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

        if ($this->type === self::TYPE_PERCENTAGE) {
            $discount = ($amount * $this->discount_percentage) / 100;
            return $this->maximum_discount ? min($discount, $this->maximum_discount) : $discount;
        }

        return $this->discount_amount;
    }
}
