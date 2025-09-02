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

        // Load relationships
        $query->with(['category']);

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

        // إزالة فلتر is_featured - لم يعد موجوداً
        // if (isset($filters['is_featured']) && $filters['is_featured']) {
        //     $query->featured();
        // }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get product by ID with reviews
     */
    public function getProductById(string $id): ?Product
    {
        return $this->model->with([
            'category',
            'reviews' => function ($query) {
                $query->latest()->take(5);
            }
        ])->find($id);
    }

    /**
     * Get featured products - DISABLED (feature removed)
     */
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        // إرجاع أحدث المنتجات المتاحة بدلاً من المميزة
        return $this->model->with(['category'])
                          ->available()
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
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('product_categories')) {
            return [];
        }

        return \App\Modules\Products\Models\ProductCategory::select('name', 'display_name')
                                                           ->get()
                                                           ->pluck('display_name', 'name')
                                                           ->toArray();
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
     * Update product stock (disabled - inventory not tracked)
     */
    public function updateStock(string $productId, int $quantity): bool
    {
        // Stock update disabled - inventory tracking removed
        // This method exists for backward compatibility but does nothing
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
