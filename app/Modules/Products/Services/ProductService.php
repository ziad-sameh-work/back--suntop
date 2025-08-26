<?php

namespace App\Modules\Products\Services;

use App\Modules\Core\BaseService;
use App\Modules\Products\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService extends BaseService
{
    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    /**
     * Get products with filters and pagination
     */
    public function getProducts(array $filters, string $sortBy = 'created_at', string $sortOrder = 'desc', int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Apply filters
        if ($filters['category']) {
            $query->byCategory($filters['category']);
        }

        if ($filters['search']) {
            $query->search($filters['search']);
        }

        if (isset($filters['is_available']) && $filters['is_available']) {
            $query->available();
        }

        if (isset($filters['is_featured']) && $filters['is_featured']) {
            $query->featured();
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get product by ID with reviews
     */
    public function getProductById(string $id): ?Product
    {
        return $this->model->with(['reviews' => function ($query) {
            $query->approved()->with('user:id,name')->latest()->take(5);
        }])->find($id);
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->model->featured()
                          ->available()
                          ->orderBy('sort_order')
                          ->orderBy('created_at', 'desc')
                          ->take($limit)
                          ->get();
    }

    /**
     * Search products
     */
    public function searchProducts(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->search($query)
                          ->available()
                          ->orderBy('rating', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        $categories = $this->model->select('category')
                                 ->distinct()
                                 ->whereNotNull('category')
                                 ->pluck('category')
                                 ->toArray();

        $volumeCategories = $this->model->select('volume_category')
                                       ->distinct()
                                       ->whereNotNull('volume_category')
                                       ->pluck('volume_category')
                                       ->toArray();

        return array_unique(array_merge($categories, $volumeCategories));
    }

    /**
     * Get price range
     */
    public function getPriceRange(): array
    {
        $min = $this->model->available()->min('price') ?? 0;
        $max = $this->model->available()->max('price') ?? 0;

        return [
            'min' => (float) $min,
            'max' => (float) $max,
        ];
    }

    /**
     * Update product stock
     */
    public function updateStock(string $productId, int $quantity): bool
    {
        $product = $this->findByIdOrFail($productId);
        
        if ($product->stock_quantity < $quantity) {
            throw new \Exception('الكمية المطلوبة غير متوفرة في المخزون');
        }

        $product->decrement('stock_quantity', $quantity);

        // Mark as unavailable if stock is zero
        if ($product->stock_quantity <= 0) {
            $product->update(['is_available' => false]);
        }

        return true;
    }

    /**
     * Get products by IDs
     */
    public function getProductsByIds(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }
}
