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
        
        // Return default image with full URL
        return asset('images/no-product.png');
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
