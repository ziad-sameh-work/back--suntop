<?php

namespace App\Modules\Favorites\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Favorites\Services\FavoriteService;
use App\Modules\Favorites\Resources\FavoriteResource;
use App\Modules\Products\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends BaseController
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Get user's favorites
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $filters = [
                'category_id' => $request->get('category_id'),
                'available_only' => $request->get('available_only', true),
                'search' => $request->get('search'),
                'price_min' => $request->get('price_min'),
                'price_max' => $request->get('price_max'),
            ];

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $perPage = $request->get('per_page', 20);

            $favorites = $this->favoriteService->getUserFavorites(
                $userId,
                $filters,
                $sortBy,
                $sortOrder,
                $perPage
            );

            $favoritesCount = $this->favoriteService->getUserFavoritesCount($userId);

            return $this->successResponse([
                'favorites' => FavoriteResource::collection($favorites),
                'pagination' => [
                    'current_page' => $favorites->currentPage(),
                    'per_page' => $favorites->perPage(),
                    'total' => $favorites->total(),
                    'last_page' => $favorites->lastPage(),
                    'has_next' => $favorites->hasMorePages(),
                    'has_prev' => $favorites->currentPage() > 1,
                ],
                'total_favorites' => $favoritesCount,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Add product to favorites
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
            ]);

            $userId = $request->user()->id;
            $result = $this->favoriteService->addToFavorites($userId, $request->product_id);

            if (!$result['success']) {
                return $this->errorResponse($result['message']);
            }

            return $this->successResponse([
                'is_favorited' => $result['is_favorited'],
                'favorite_id' => $result['favorite_id'],
                'added_at' => $result['added_at'],
            ], $result['message']);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove product from favorites
     */
    public function destroy(Request $request, int $productId): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $result = $this->favoriteService->removeFromFavorites($userId, $productId);

            if (!$result['success']) {
                return $this->errorResponse($result['message']);
            }

            return $this->successResponse([
                'is_favorited' => $result['is_favorited'],
            ], $result['message']);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Toggle favorite status
     */
    public function toggle(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
            ]);

            $userId = $request->user()->id;
            $result = $this->favoriteService->toggleFavorite($userId, $request->product_id);

            return $this->successResponse([
                'action' => $result['action'],
                'is_favorited' => $result['is_favorited'],
                'product' => $result['product'],
            ], $result['message']);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Check if product is favorited
     */
    public function check(Request $request, int $productId): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $isFavorited = $this->favoriteService->isFavorited($userId, $productId);

            return $this->successResponse([
                'is_favorited' => $isFavorited,
                'product_id' => $productId,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get favorites count
     */
    public function count(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $count = $this->favoriteService->getUserFavoritesCount($userId);

            return $this->successResponse([
                'favorites_count' => $count,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Clear all favorites
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $result = $this->favoriteService->clearAllFavorites($userId);

            return $this->successResponse([
                'deleted_count' => $result['deleted_count'],
            ], $result['message']);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get user's favorite statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $stats = $this->favoriteService->getUserFavoriteStats($userId);

            return $this->successResponse($stats);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get popular products (most favorited)
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $popularProducts = $this->favoriteService->getPopularProducts($limit);

            return $this->successResponse([
                'popular_products' => $popularProducts->map(function($item) {
                    return [
                        'product' => new ProductResource($item->product),
                        'favorites_count' => $item->favorites_count,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get recommendations based on user favorites
     */
    public function recommendations(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $limit = $request->get('limit', 10);
            $recommendations = $this->favoriteService->getRecommendations($userId, $limit);

            return $this->successResponse([
                'recommendations' => ProductResource::collection($recommendations),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Bulk add products to favorites
     */
    public function bulkAdd(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'integer|exists:products,id',
            ]);

            $userId = $request->user()->id;
            $result = $this->favoriteService->bulkAddToFavorites($userId, $request->product_ids);

            return $this->successResponse([
                'success_count' => $result['success_count'],
                'fail_count' => $result['fail_count'],
                'results' => $result['results'],
            ], $result['message']);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get favorites with product status (for product listing pages)
     */
    public function checkMultiple(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'integer',
            ]);

            $userId = $request->user()->id;
            $productIds = $request->product_ids;
            
            $favoritedProducts = \App\Models\Favorite::where('user_id', $userId)
                                                   ->whereIn('product_id', $productIds)
                                                   ->pluck('product_id')
                                                   ->toArray();

            $results = [];
            foreach ($productIds as $productId) {
                $results[] = [
                    'product_id' => $productId,
                    'is_favorited' => in_array($productId, $favoritedProducts),
                ];
            }

            return $this->successResponse([
                'favorites_status' => $results,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
