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
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the first image URL
     */
    public function getFirstImageAttribute(): ?string
    {
        \Log::info("=== Product first_image DEBUG START ===");
        \Log::info("Product ID: " . $this->id);
        \Log::info("Product Name: " . $this->name);
        
        // Check if images array exists
        \Log::info("Images exists: " . ($this->images ? 'YES' : 'NO'));
        \Log::info("Images is array: " . (is_array($this->images) ? 'YES' : 'NO'));
        \Log::info("Images count: " . (is_array($this->images) ? count($this->images) : 'N/A'));
        
        if ($this->images) {
            \Log::info("Raw images data: " . json_encode($this->images));
        }
        
        if ($this->images && is_array($this->images) && count($this->images) > 0) {
            $firstImage = $this->images[0];
            \Log::info("First image path: " . $firstImage);
            
            // If it's already a full URL, return it
            if (filter_var($firstImage, FILTER_VALIDATE_URL)) {
                \Log::info("✅ First image is full URL - returning: " . $firstImage);
                return $firstImage;
            }
            
            // Check if file exists and return asset URL
            $fullPath = public_path($firstImage);
            \Log::info("Checking file path: " . $fullPath);
            \Log::info("File exists: " . (file_exists($fullPath) ? 'YES' : 'NO'));
            
            if (file_exists($fullPath)) {
                $assetUrl = asset($firstImage);
                \Log::info("✅ File exists - returning asset URL: " . $assetUrl);
                return $assetUrl;
            }
            
            // If file doesn't exist, try with different path formats
            $alternativePath = 'uploads/products/' . basename($firstImage);
            $alternativeFullPath = public_path($alternativePath);
            \Log::info("Trying alternative path: " . $alternativeFullPath);
            \Log::info("Alternative file exists: " . (file_exists($alternativeFullPath) ? 'YES' : 'NO'));
            
            if (file_exists($alternativeFullPath)) {
                $alternativeAssetUrl = asset($alternativePath);
                \Log::info("✅ Alternative path works - returning: " . $alternativeAssetUrl);
                return $alternativeAssetUrl;
            }
            
            \Log::warning("❌ No valid image path found for: " . $firstImage);
        } else {
            \Log::info("❌ No images array or empty images");
        }
        
        $defaultImage = asset('images/no-product.png');
        \Log::info("❌ Returning default image: " . $defaultImage);
        \Log::info("=== Product first_image DEBUG END ===");
        
        return $defaultImage;
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
}
