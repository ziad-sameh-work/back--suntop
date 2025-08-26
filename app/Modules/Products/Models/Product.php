<?php

namespace App\Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'sku',
        'slug',
        'images',
        'price',
        'discount_price',
        'stock_quantity',
        'min_quantity',
        'weight',
        'dimensions',
        'merchant_id',
        'is_available',
        'is_featured',
        'meta_title',
        'meta_description',
        // Carton and Package fields
        'carton_size',
        'carton_price',
        'is_full_carton',
        'package_size',
        'package_price',
        'is_full_package',
        'allow_individual_units',
        'carton_loyalty_points',
        'package_loyalty_points',
        'unit_loyalty_points',
        // Legacy fields
        'image_url',
        'gallery',
        'original_price',
        'currency',
        'category',
        'size',
        'volume_category',
        'rating',
        'review_count',
        'tags',
        'ingredients',
        'nutrition_facts',
        'storage_instructions',
        'expiry_info',
        'barcode',
        'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        // Carton and Package casts
        'carton_price' => 'decimal:2',
        'is_full_carton' => 'boolean',
        'package_price' => 'decimal:2',
        'is_full_package' => 'boolean',
        'allow_individual_units' => 'boolean',
        // Legacy casts
        'gallery' => 'array',
        'tags' => 'array',
        'ingredients' => 'array',
        'nutrition_facts' => 'array',
        'original_price' => 'decimal:2',
        'rating' => 'decimal:1',
    ];

    protected $appends = ['image_full_url', 'gallery_full_urls'];

    /**
     * Get full image URL
     */
    public function getImageFullUrlAttribute(): ?string
    {
        return $this->image_url ? url('storage/' . $this->image_url) : null;
    }

    /**
     * Get full gallery URLs
     */
    public function getGalleryFullUrlsAttribute(): array
    {
        if (!$this->gallery) {
            return [];
        }

        return array_map(function ($image) {
            return url('storage/' . $image);
        }, $this->gallery);
    }

    /**
     * Scope for available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for category filter
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category)
                    ->orWhere('volume_category', $category);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Merchant relationship
     */
    public function merchant()
    {
        return $this->belongsTo(\App\Modules\Merchants\Models\Merchant::class);
    }

    /**
     * Reviews relationship
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Order items relationship
     */
    public function orderItems()
    {
        return $this->hasMany(\App\Modules\Orders\Models\OrderItem::class);
    }

    /**
     * Get recent reviews
     */
    public function getRecentReviewsAttribute()
    {
        return $this->reviews()
                   ->with('user:id,name')
                   ->latest()
                   ->take(5)
                   ->get()
                   ->map(function ($review) {
                       return [
                           'id' => $review->id,
                           'user_name' => $review->user->name,
                           'rating' => $review->rating,
                           'comment' => $review->comment,
                           'created_at' => $review->created_at->toISOString(),
                       ];
                   });
    }

    /**
     * Check if product can be sold as individual units
     */
    public function canSellIndividualUnits(): bool
    {
        return $this->allow_individual_units;
    }

    /**
     * Check if product can be sold as cartons
     */
    public function canSellAsCarton(): bool
    {
        return $this->carton_size > 0 && $this->carton_price > 0;
    }

    /**
     * Check if product can be sold as packages
     */
    public function canSellAsPackage(): bool
    {
        return $this->package_size > 0 && $this->package_price > 0;
    }

    /**
     * Get effective price based on selling type
     */
    public function getEffectivePrice(string $sellingType = 'unit'): float
    {
        switch ($sellingType) {
            case 'carton':
                return $this->carton_price ?? $this->price;
            case 'package':
                return $this->package_price ?? $this->price;
            default:
                return $this->price;
        }
    }

    /**
     * Get loyalty points for specific selling type
     */
    public function getLoyaltyPoints(string $sellingType = 'unit'): int
    {
        switch ($sellingType) {
            case 'carton':
                return $this->carton_loyalty_points;
            case 'package':
                return $this->package_loyalty_points;
            default:
                return $this->unit_loyalty_points;
        }
    }

    /**
     * Calculate quantity based on selling type
     */
    public function calculateActualQuantity(int $requestedQuantity, string $sellingType = 'unit'): int
    {
        switch ($sellingType) {
            case 'carton':
                return $requestedQuantity * ($this->carton_size ?? 1);
            case 'package':
                return $requestedQuantity * ($this->package_size ?? 1);
            default:
                return $requestedQuantity;
        }
    }

    /**
     * Get available selling types
     */
    public function getAvailableSellingTypes(): array
    {
        $types = [];
        
        if ($this->canSellIndividualUnits()) {
            $types[] = 'unit';
        }
        
        if ($this->canSellAsPackage()) {
            $types[] = 'package';
        }
        
        if ($this->canSellAsCarton()) {
            $types[] = 'carton';
        }
        
        return $types;
    }

    /**
     * Check if product requires minimum carton/package purchase
     */
    public function requiresMinimumPurchase(): array
    {
        $requirements = [];
        
        if ($this->is_full_carton) {
            $requirements[] = 'carton';
        }
        
        if ($this->is_full_package) {
            $requirements[] = 'package';
        }
        
        return $requirements;
    }
}
