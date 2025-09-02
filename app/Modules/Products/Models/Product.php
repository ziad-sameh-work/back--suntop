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
        'category_id',
        'price',
        'back_color',
        'images',
        'is_available',
        'stock_quantity',
        'is_featured',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    /**
     * Get the first image URL from storage
     */
    public function getFirstImageAttribute(): ?string
    {
        if ($this->images && is_array($this->images) && count($this->images) > 0) {
            $firstImage = $this->images[0];
            
            // If it's already a full URL, return it
            if (filter_var($firstImage, FILTER_VALIDATE_URL)) {
                return $firstImage;
            }
            
            // Return storage URL (this will be: https://domain.com/storage/products/filename.ext)
            return \Storage::disk('public')->url($firstImage);
        }
        
        // Return default image with full URL - ensure CORS compatibility
        return url('images/no-product.png');
    }

    /**
     * Scope for available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for category filter
     */
    public function scopeByCategory($query, $category)
    {
        if (is_numeric($category)) {
            return $query->where('category_id', $category);
        }
        
        return $query->whereHas('category', function($q) use ($category) {
            $q->where('name', $category)
              ->orWhere('display_name', 'LIKE', "%{$category}%");
        });
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhereHas('category', function($q2) use ($search) {
                  $q2->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('display_name', 'LIKE', "%{$search}%");
              });
        });
    }


    
    /**
     * Category relationship
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
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
     * Favorites relationship
     */
    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class);
    }

    /**
     * Users who favorited this product
     */
    public function favoritedByUsers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'favorites', 'product_id', 'user_id')
                   ->withTimestamps()
                   ->withPivot('added_at');
    }

    /**
     * Check if product is favorited by specific user
     */
    public function isFavoritedBy(int $userId): bool
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    /**
     * Get favorites count
     */
    public function getFavoritesCountAttribute(): int
    {
        return $this->favorites()->count();
    }

    /**
     * Get product image URL with fallback
     */
    public function getImageUrlAttribute(): string
    {
        return $this->getFirstImageAttribute();
    }

    /**
     * Check if product has valid image
     */
    public function hasValidImage(): bool
    {
        if (!$this->images || !is_array($this->images) || count($this->images) === 0) {
            return false;
        }

        $firstImage = $this->images[0];
        
        // If it's a URL, assume it's valid (API calls will validate)
        if (filter_var($firstImage, FILTER_VALIDATE_URL)) {
            return true;
        }
        
        // Check if file exists locally
        return \Storage::disk('public')->exists($firstImage);
    }

    /**
     * Get product initial for fallback icon
     */
    public function getInitialAttribute(): string
    {
        return strtoupper(substr($this->name ?? 'P', 0, 1));
    }

    /**
     * Get available selling types for this product
     */
    public function getAvailableSellingTypes(): array
    {
        // Default selling types - could be made configurable per product later
        return ['unit', 'package', 'carton'];
    }

    /**
     * Get effective price based on selling type
     */
    public function getEffectivePrice(string $sellingType = 'unit'): float
    {
        // For now, return the same price for all types
        // This can be extended later to support different prices per selling type
        switch ($sellingType) {
            case 'carton':
                // Assume carton has some discount or different pricing
                return (float) $this->price * 24; // Example: 24 units per carton
            case 'package':
                // Assume package has some discount or different pricing  
                return (float) $this->price * 6; // Example: 6 units per package
            default: // unit
                return (float) $this->price;
        }
    }

    /**
     * Calculate actual quantity (units) based on selling type
     */
    public function calculateActualQuantity(int $requestedQuantity, string $sellingType = 'unit'): int
    {
        switch ($sellingType) {
            case 'carton':
                // Example: 1 carton = 24 units
                return $requestedQuantity * 24;
            case 'package':
                // Example: 1 package = 6 units
                return $requestedQuantity * 6;
            default: // unit
                return $requestedQuantity;
        }
    }

    /**
     * Get loyalty points for this product based on selling type
     */
    public function getLoyaltyPoints(string $sellingType = 'unit'): int
    {
        switch ($sellingType) {
            case 'carton':
                // More points for buying cartons
                return 10; // Example: 10 points per carton
            case 'package':
                // Medium points for packages
                return 3; // Example: 3 points per package
            default: // unit
                return 1; // Example: 1 point per unit
        }
    }

    /**
     * Check if product has enough stock for requested quantity
     */
    public function hasEnoughStock(int $requestedQuantity, string $sellingType = 'unit'): bool
    {
        // First check if product is available
        if (!$this->is_available) {
            return false;
        }

        // If stock_quantity is null or 0, assume unlimited stock
        if (!$this->stock_quantity) {
            return true;
        }

        // Calculate actual units needed
        $actualUnits = $this->calculateActualQuantity($requestedQuantity, $sellingType);
        
        return $this->stock_quantity >= $actualUnits;
    }

    /**
     * Get minimum purchase requirements based on selling type
     */
    public function getMinimumPurchaseRequirement(string $sellingType = 'unit'): int
    {
        switch ($sellingType) {
            case 'carton':
                return 1; // Minimum 1 carton
            case 'package':
                return 1; // Minimum 1 package
            default: // unit
                return 1; // Minimum 1 unit
        }
    }

    /**
     * Check if product requires minimum purchase (only cartons/packages)
     */
    public function requiresMinimumPurchase(): array
    {
        // For now, allow all selling types
        // This can be configured per product later
        return [];
        
        // Example if some products require only cartons/packages:
        // return ['carton', 'package'];
    }
}
