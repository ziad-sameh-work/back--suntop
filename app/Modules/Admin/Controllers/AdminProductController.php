<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Products\Models\Product;
use App\Modules\Admin\Requests\CreateProductRequest;
use App\Modules\Admin\Requests\UpdateProductRequest;
use App\Modules\Admin\Resources\AdminProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminProductController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Get all products with admin details
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::withTrashed();

            // Apply filters
            if ($request->has('category') && $request->category !== 'all') {
                $query->where(function($q) use ($request) {
                    $q->where('category', $request->category)
                      ->orWhere('volume_category', $request->category);
                });
            }

            if ($request->has('availability') && $request->availability !== 'all') {
                if ($request->availability === 'available') {
                    $query->where('is_available', true)->where('stock_quantity', '>', 0);
                } elseif ($request->availability === 'out_of_stock') {
                    $query->where('stock_quantity', '<=', 0);
                } elseif ($request->availability === 'unavailable') {
                    $query->where('is_available', false);
                }
            }

            if ($request->has('featured') && $request->featured !== 'all') {
                $query->where('is_featured', $request->featured === 'true');
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('barcode', 'LIKE', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('limit', 20);
            $products = $query->paginate($perPage);

            return $this->successResponse([
                'products' => AdminProductResource::collection($products),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'total_pages' => $products->lastPage(),
                    'has_next' => $products->hasMorePages(),
                    'has_prev' => $products->currentPage() > 1,
                ],
                'summary' => [
                    'total_products' => Product::count(),
                    'active_products' => Product::where('is_available', true)->count(),
                    'featured_products' => Product::where('is_featured', true)->count(),
                    'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
                    'deleted_products' => Product::onlyTrashed()->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Create new product
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $product = Product::create($data);

            return $this->successResponse(
                ['product' => new AdminProductResource($product)],
                'تم إنشاء المنتج بنجاح',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get specific product
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::withTrashed()
                             ->with(['reviews.user'])
                             ->withCount('reviews')
                             ->findOrFail($id);

            return $this->successResponse([
                'product' => new AdminProductResource($product),
                'stats' => [
                    'total_reviews' => $product->reviews_count,
                    'average_rating' => $product->rating,
                    'total_sold' => $product->orderItems()->sum('quantity'),
                    'revenue' => $product->orderItems()->sum('total_price'),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 404);
        }
    }

    /**
     * Update product
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $data = $request->validated();
            
            $product->update($data);

            return $this->successResponse(
                ['product' => new AdminProductResource($product)],
                'تم تحديث المنتج بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete product (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return $this->successResponse(null, 'تم حذف المنتج بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Restore deleted product
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();

            return $this->successResponse(
                ['product' => new AdminProductResource($product)],
                'تم استعادة المنتج بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Permanently delete product
     */
    public function forceDelete(int $id): JsonResponse
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            
            // Check if product has orders
            if ($product->orderItems()->count() > 0) {
                return $this->errorResponse('لا يمكن حذف المنتج نهائياً لوجود طلبات مرتبطة به');
            }

            $product->forceDelete();

            return $this->successResponse(null, 'تم حذف المنتج نهائياً');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Toggle product availability
     */
    public function toggleAvailability(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->update(['is_available' => !$product->is_available]);

            $status = $product->is_available ? 'متاح' : 'غير متاح';
            return $this->successResponse(
                ['product' => new AdminProductResource($product)],
                "تم تحديث حالة المنتج إلى {$status}"
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->update(['is_featured' => !$product->is_featured]);

            $status = $product->is_featured ? 'مميز' : 'عادي';
            return $this->successResponse(
                ['product' => new AdminProductResource($product)],
                "تم تحديث المنتج إلى {$status}"
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'stock_quantity' => 'required|integer|min:0',
                'action' => 'nullable|in:set,add,subtract',
            ]);

            $product = Product::findOrFail($id);
            $action = $request->get('action', 'set');
            $quantity = $request->stock_quantity;

            switch ($action) {
                case 'add':
                    $product->increment('stock_quantity', $quantity);
                    break;
                case 'subtract':
                    $product->decrement('stock_quantity', $quantity);
                    break;
                default:
                    $product->update(['stock_quantity' => $quantity]);
            }

            // Auto-update availability based on stock
            if ($product->stock_quantity <= 0) {
                $product->update(['is_available' => false]);
            }

            return $this->successResponse(
                ['product' => new AdminProductResource($product)],
                'تم تحديث المخزون بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'action' => 'required|in:delete,activate,deactivate,feature,unfeature',
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'exists:products,id',
            ]);

            $productIds = $request->product_ids;
            $action = $request->action;

            switch ($action) {
                case 'delete':
                    Product::whereIn('id', $productIds)->delete();
                    $message = 'تم حذف المنتجات المحددة';
                    break;
                case 'activate':
                    Product::whereIn('id', $productIds)->update(['is_available' => true]);
                    $message = 'تم تفعيل المنتجات المحددة';
                    break;
                case 'deactivate':
                    Product::whereIn('id', $productIds)->update(['is_available' => false]);
                    $message = 'تم إلغاء تفعيل المنتجات المحددة';
                    break;
                case 'feature':
                    Product::whereIn('id', $productIds)->update(['is_featured' => true]);
                    $message = 'تم جعل المنتجات المحددة مميزة';
                    break;
                case 'unfeature':
                    Product::whereIn('id', $productIds)->update(['is_featured' => false]);
                    $message = 'تم إلغاء تمييز المنتجات المحددة';
                    break;
            }

            return $this->successResponse(null, $message);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get product categories and statistics
     */
    public function categories(): JsonResponse
    {
        try {
            $allCategories = collect([]);
            $categoryStats = [];

            if (\Illuminate\Support\Facades\Schema::hasTable('product_categories')) {
                $categories = \App\Modules\Products\Models\ProductCategory::withCount('products')->get();
                
                $allCategories = $categories->pluck('display_name');
                
                foreach ($categories as $category) {
                    $categoryStats[] = [
                        'name' => $category->display_name,
                        'count' => $category->products_count,
                    ];
                }
            }

            return $this->successResponse([
                'categories' => $allCategories,
                'statistics' => $categoryStats,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Product analytics
     */
    public function analytics(): JsonResponse
    {
        try {
            $analytics = [
                'overview' => [
                    'total_products' => Product::count(),
                    'active_products' => Product::where('is_available', true)->count(),
                    'featured_products' => Product::where('is_featured', true)->count(),
                    'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
                ],
                'top_rated' => Product::where('rating', '>', 0)
                                   ->orderBy('rating', 'desc')
                                   ->take(5)
                                   ->get(['id', 'name', 'rating', 'review_count']),
                'low_stock' => Product::where('stock_quantity', '<=', 10)
                                    ->where('stock_quantity', '>', 0)
                                    ->orderBy('stock_quantity', 'asc')
                                    ->take(10)
                                    ->get(['id', 'name', 'stock_quantity']),
                'categories_distribution' => Product::selectRaw('category, COUNT(*) as count')
                                                  ->whereNotNull('category')
                                                  ->groupBy('category')
                                                  ->orderBy('count', 'desc')
                                                  ->get(),
            ];

            return $this->successResponse(['analytics' => $analytics]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
