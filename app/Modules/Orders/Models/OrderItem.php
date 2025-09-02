<?php

namespace App\Modules\Orders\Models;

use App\Modules\Products\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'quantity',
        'unit_price',
        'total_price',
        'selling_type',
        'cartons_count',
        'packages_count',
        'units_count',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'cartons_count' => 'integer',
        'packages_count' => 'integer',
        'units_count' => 'integer',
    ];

    // Selling type constants
    const SELLING_TYPE_UNIT = 'unit';
    const SELLING_TYPE_PACKAGE = 'package';
    const SELLING_TYPE_CARTON = 'carton';

    /**
     * Order relationship
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate totals based on selling type from product
     */
    public function calculateFromProduct(Product $product, int $requestedQuantity, string $sellingType = 'unit'): void
    {
        $this->selling_type = $sellingType;
        $this->quantity = $requestedQuantity;
        $this->unit_price = $product->getEffectivePrice($sellingType);
        
        // Calculate cartons, packages, and units
        switch ($sellingType) {
            case self::SELLING_TYPE_CARTON:
                $this->cartons_count = $requestedQuantity;
                $this->packages_count = 0;
                $this->units_count = $product->calculateActualQuantity($requestedQuantity, $sellingType);
                break;
                
            case self::SELLING_TYPE_PACKAGE:
                $this->cartons_count = 0;
                $this->packages_count = $requestedQuantity;
                $this->units_count = $product->calculateActualQuantity($requestedQuantity, $sellingType);
                break;
                
            default: // unit
                $this->cartons_count = 0;
                $this->packages_count = 0;
                $this->units_count = $requestedQuantity;
                break;
        }
        
        $this->total_price = $this->unit_price * $this->quantity;
    }

    /**
     * Get loyalty points for this item
     */
    public function getLoyaltyPoints(): int
    {
        // Return 0 if product relationship is null (product was deleted)
        if (!$this->product || !$this->product->id) {
            return 0;
        }

        try {
            return $this->product->getLoyaltyPoints($this->selling_type) * $this->quantity;
        } catch (\Exception $e) {
            \Log::error('Error calculating loyalty points for order item', [
                'order_item_id' => $this->id,
                'product_id' => $this->product_id,
                'selling_type' => $this->selling_type,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Check if this item qualifies for category discount
     */
    public function qualifiesForCategoryDiscount(): bool
    {
        // Only cartons and packages qualify for category discounts
        return in_array($this->selling_type, [self::SELLING_TYPE_CARTON, self::SELLING_TYPE_PACKAGE]);
    }

    /**
     * Get formatted selling type
     */
    public function getFormattedSellingTypeAttribute(): string
    {
        switch ($this->selling_type) {
            case self::SELLING_TYPE_CARTON:
                return 'كرتون';
            case self::SELLING_TYPE_PACKAGE:
                return 'علبة';
            default:
                return 'قطعة';
        }
    }
}
