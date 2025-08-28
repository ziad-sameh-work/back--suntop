<?php

namespace App\Modules\Favorites\Services;

use App\Modules\Core\BaseService;
use App\Models\Favorite;
use App\Modules\Products\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FavoriteService extends BaseService
{
    public function __construct(Favorite $favorite)
    {
        $this->model = $favorite;
    }

    /**
     * Get user's favorites with pagination
     */
    public function getUserFavorites(
        int $userId,
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = $this->model->forUser($userId)->withProduct();

        // Apply filters
        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->whereHas('product', function($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        if (isset($filters['available_only']) && $filters['available_only']) {
            $query->whereHas('product', function($q) {
                $q->where('is_available', true);
            });
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if (isset($filters['price_min'])) {
            $query->whereHas('product', function($q) use ($filters) {
                $q->where('price', '>=', $filters['price_min']);
            });
        }

        if (isset($filters['price_max'])) {
            $query->whereHas('product', function($q) use ($filters) {
                $q->where('price', '<=', $filters['price_max']);
            });
        }

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    /**
     * Add product to favorites
     */
    public function addToFavorites(int $userId, int $productId): array
    {
        // Check if product exists
        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('المنتج غير موجود');
        }

        // Check if already favorited
        if (Favorite::isFavorited($userId, $productId)) {
            return [
                'success' => false,
                'message' => 'المنتج موجود بالفعل في المفضلة',
                'is_favorited' => true,
            ];
        }

        $favorite = Favorite::addToFavorites($userId, $productId);

        return [
            'success' => true,
            'message' => 'تم إضافة المنتج إلى المفضلة بنجاح',
            'is_favorited' => true,
            'favorite_id' => $favorite->id,
            'added_at' => $favorite->added_at->toISOString(),
        ];
    }

    /**
     * Remove product from favorites
     */
    public function removeFromFavorites(int $userId, int $productId): array
    {
        $removed = Favorite::removeFromFavorites($userId, $productId);

        if (!$removed) {
            return [
                'success' => false,
                'message' => 'المنتج غير موجود في المفضلة',
                'is_favorited' => false,
            ];
        }

        return [
            'success' => true,
            'message' => 'تم إزالة المنتج من المفضلة بنجاح',
            'is_favorited' => false,
        ];
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(int $userId, int $productId): array
    {
        // Check if product exists
        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('المنتج غير موجود');
        }

        $result = Favorite::toggleFavorite($userId, $productId);

        return [
            'success' => true,
            'action' => $result['action'],
            'is_favorited' => $result['is_favorited'],
            'message' => $result['message'],
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image_url' => $product->image_url,
            ],
        ];
    }

    /**
     * Check if product is favorited by user
     */
    public function isFavorited(int $userId, int $productId): bool
    {
        return Favorite::isFavorited($userId, $productId);
    }

    /**
     * Get user's favorites count
     */
    public function getUserFavoritesCount(int $userId): int
    {
        return Favorite::getUserFavoritesCount($userId);
    }

    /**
     * Clear all user favorites
     */
    public function clearAllFavorites(int $userId): array
    {
        $count = $this->model->forUser($userId)->count();
        $this->model->forUser($userId)->delete();

        return [
            'success' => true,
            'message' => "تم حذف {$count} منتج من المفضلة",
            'deleted_count' => $count,
        ];
    }

    /**
     * Get favorite statistics for user
     */
    public function getUserFavoriteStats(int $userId): array
    {
        $totalFavorites = $this->getUserFavoritesCount($userId);
        
        $categoriesStats = $this->model->forUser($userId)
                                      ->join('products', 'favorites.product_id', '=', 'products.id')
                                      ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
                                      ->select('product_categories.name', \DB::raw('COUNT(*) as count'))
                                      ->groupBy('product_categories.id', 'product_categories.name')
                                      ->get()
                                      ->pluck('count', 'name')
                                      ->toArray();

        $averagePrice = $this->model->forUser($userId)
                                   ->join('products', 'favorites.product_id', '=', 'products.id')
                                   ->avg('products.price');

        $recentlyAdded = $this->model->forUser($userId)
                                    ->where('created_at', '>=', now()->subDays(7))
                                    ->count();

        return [
            'total_favorites' => $totalFavorites,
            'categories_breakdown' => $categoriesStats,
            'average_price' => round($averagePrice ?? 0, 2),
            'recently_added' => $recentlyAdded,
        ];
    }

    /**
     * Get popular products (most favorited)
     */
    public function getPopularProducts(int $limit = 10): Collection
    {
        return Favorite::getPopularProducts($limit);
    }

    /**
     * Get recommendations based on user favorites
     */
    public function getRecommendations(int $userId, int $limit = 10): Collection
    {
        // Get user's favorite categories
        $favoriteCategories = $this->model->forUser($userId)
                                         ->join('products', 'favorites.product_id', '=', 'products.id')
                                         ->select('products.category_id')
                                         ->distinct()
                                         ->pluck('category_id')
                                         ->toArray();

        if (empty($favoriteCategories)) {
            // If no favorites, return popular products
            return $this->getPopularProducts($limit);
        }

        // Get user's current favorite product IDs
        $favoriteProductIds = $this->model->forUser($userId)
                                         ->pluck('product_id')
                                         ->toArray();

        // Get products from same categories that user hasn't favorited
        return Product::whereIn('category_id', $favoriteCategories)
                     ->whereNotIn('id', $favoriteProductIds)
                     ->where('is_available', true)
                     ->with('category')
                     ->inRandomOrder()
                     ->limit($limit)
                     ->get();
    }

    /**
     * Bulk add products to favorites
     */
    public function bulkAddToFavorites(int $userId, array $productIds): array
    {
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($productIds as $productId) {
            try {
                $result = $this->addToFavorites($userId, $productId);
                if ($result['success']) {
                    $successCount++;
                }
                $results[] = [
                    'product_id' => $productId,
                    'success' => $result['success'],
                    'message' => $result['message'],
                ];
            } catch (\Exception $e) {
                $failCount++;
                $results[] = [
                    'product_id' => $productId,
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => $successCount > 0,
            'message' => "تم إضافة {$successCount} منتج بنجاح، فشل في {$failCount}",
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'results' => $results,
        ];
    }
}
