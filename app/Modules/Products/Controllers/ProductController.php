<?php

namespace App\Modules\Products\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Products\Services\ProductService;
use App\Modules\Products\Resources\ProductResource;
use App\Modules\Products\Resources\ProductDetailResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all products with filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'category' => $request->get('category'),
                'search' => $request->get('search'),
                'is_available' => $request->get('available', true),
                'is_featured' => $request->get('featured'),
            ];

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $perPage = $request->get('limit', 20);

            $products = $this->productService->getProducts($filters, $sortBy, $sortOrder, $perPage);
            $categories = $this->productService->getCategories();
            $priceRange = $this->productService->getPriceRange();

            $response = [
                'products' => ProductResource::collection($products),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'total_pages' => $products->lastPage(),
                    'has_next' => $products->hasMorePages(),
                    'has_prev' => $products->currentPage() > 1,
                ],
                'filters' => [
                    'categories' => $categories,
                    'price_range' => $priceRange,
                ]
            ];

            return $this->successResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get product by ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            
            if (!$product) {
                return $this->errorResponse('المنتج غير موجود', null, 404);
            }

            return $this->successResponse(new ProductDetailResource($product));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get featured products
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $products = $this->productService->getFeaturedProducts($limit);

            return $this->successResponse([
                'products' => ProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = $this->productService->getCategories();
            return $this->successResponse(['categories' => $categories]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            $perPage = $request->get('limit', 20);

            if (!$query) {
                return $this->errorResponse('نص البحث مطلوب');
            }

            $products = $this->productService->searchProducts($query, $perPage);

            return $this->successResponse([
                'products' => ProductResource::collection($products),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'total_pages' => $products->lastPage(),
                    'has_next' => $products->hasMorePages(),
                    'has_prev' => $products->currentPage() > 1,
                ],
                'query' => $query,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
