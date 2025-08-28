<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Products\Models\Product;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for user's favorites
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope with product details
     */
    public function scopeWithProduct($query)
    {
        return $query->with(['product' => function($q) {
            $q->select('id', 'name', 'description', 'price', 'image_url', 'category_id', 'is_available')
              ->with('category:id,name');
        }]);
    }

    /**
     * Check if product is favorited by user
     */
    public static function isFavorited(int $userId, int $productId): bool
    {
        return self::where('user_id', $userId)
                  ->where('product_id', $productId)
                  ->exists();
    }

    /**
     * Add product to favorites
     */
    public static function addToFavorites(int $userId, int $productId): self
    {
        return self::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ], [
            'added_at' => now(),
        ]);
    }

    /**
     * Remove product from favorites
     */
    public static function removeFromFavorites(int $userId, int $productId): bool
    {
        return self::where('user_id', $userId)
                  ->where('product_id', $productId)
                  ->delete() > 0;
    }

    /**
     * Get user's favorites count
     */
    public static function getUserFavoritesCount(int $userId): int
    {
        return self::where('user_id', $userId)->count();
    }

    /**
     * Get popular products (most favorited)
     */
    public static function getPopularProducts(int $limit = 10)
    {
        return self::select('product_id', \DB::raw('COUNT(*) as favorites_count'))
                  ->with('product')
                  ->groupBy('product_id')
                  ->orderBy('favorites_count', 'desc')
                  ->limit($limit)
                  ->get();
    }

    /**
     * Toggle favorite status
     */
    public static function toggleFavorite(int $userId, int $productId): array
    {
        $favorite = self::where('user_id', $userId)
                       ->where('product_id', $productId)
                       ->first();

        if ($favorite) {
            $favorite->delete();
            return [
                'action' => 'removed',
                'is_favorited' => false,
                'message' => 'تم إزالة المنتج من المفضلة'
            ];
        } else {
            self::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'added_at' => now(),
            ]);
            return [
                'action' => 'added',
                'is_favorited' => true,
                'message' => 'تم إضافة المنتج إلى المفضلة'
            ];
        }
    }
}
