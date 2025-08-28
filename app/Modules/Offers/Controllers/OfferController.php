<?php

namespace App\Modules\Offers\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Offers\Services\OfferService;
use App\Modules\Offers\Resources\OfferResource;
use App\Modules\Offers\Resources\OfferDetailResource;
use App\Modules\Offers\Models\Offer;
use App\Modules\Offers\Models\OfferRedemption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends BaseController
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    /**
     * Get all active offers
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'category' => $request->get('category'),
                'type' => $request->get('type'),
                'active_only' => $request->get('active_only', true),
                'user_id' => $request->user() ? $request->user()->id : null,
            ];

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $perPage = $request->get('per_page', 20);

            $offers = $this->offerService->getOffers($filters, $sortBy, $sortOrder, $perPage);

            return $this->successResponse([
                'offers' => OfferResource::collection($offers),
                'pagination' => [
                    'current_page' => $offers->currentPage(),
                    'per_page' => $offers->perPage(),
                    'total' => $offers->total(),
                    'last_page' => $offers->lastPage(),
                    'has_next' => $offers->hasMorePages(),
                    'has_prev' => $offers->currentPage() > 1,
                ],
                'filters' => [
                    'categories' => $this->offerService->getAvailableCategories(),
                    'types' => Offer::TYPES,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get featured/special offers for home page
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 5);
            $categoryId = $request->get('category_id');
            $offers = $this->offerService->getFeaturedOffers($limit, $categoryId);

            $featuredOffers = $offers->map(function($offer) {
                return [
                    'id' => $offer->id,
                    'title' => $offer->title,
                    'description' => $offer->short_description ?: $offer->description,
                    'discount_percentage' => $offer->discount_percentage,
                    'discount_amount' => $offer->discount_amount,
                    'image_url' => $offer->image_url ? url('storage/' . $offer->image_url) : null,
                    'background_color' => $offer->background_color,
                    'text_color' => $offer->text_color,
                    'offer_type' => [
                        'id' => $offer->id,
                        'name' => $offer->type,
                        'display_name' => \App\Modules\Offers\Models\Offer::TYPES[$offer->type] ?? $offer->type,
                    ],
                    'category' => $this->getCategoryInfo($offer),
                    'merchant' => [
                        'id' => '1',
                        'name' => 'سن توب',
                        'logo_url' => url('storage/merchants/suntop-logo.jpg'),
                    ],
                    'valid_from' => $offer->valid_from->toISOString(),
                    'valid_until' => $offer->valid_until->toISOString(),
                    'is_active' => $offer->is_active,
                    'is_featured' => $offer->is_featured,
                    'usage_count' => $offer->used_count,
                    'max_usage' => $offer->usage_limit,
                    'min_purchase_amount' => $offer->min_purchase_amount ?: $offer->minimum_amount,
                    'offer_tag' => $offer->formatted_offer_tag,
                    'applicable_products' => $offer->getApplicableProductsWithDiscount(),
                ];
            });

            return $this->successResponse([
                'featured_offers' => $featuredOffers,
                'total_featured' => $offers->count(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get quick stats for home page
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $stats = $this->offerService->getQuickStats();

            return $this->successResponse($stats);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get trending offers
     */
    public function trending(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 3);
            $offers = $this->offerService->getTrendingOffers($limit);

            $trendingOffers = $offers->map(function($offer) {
                // Calculate trend percentage (dummy calculation for now)
                $trendPercentage = min(100, ($offer->trend_score / 100) * 100);
                
                return [
                    'id' => $offer->id,
                    'title' => $offer->title,
                    'offer_type' => [
                        'name' => $this->getOfferTypeName($offer->type),
                        'display_name' => \App\Modules\Offers\Models\Offer::TYPES[$offer->type] ?? $offer->type,
                    ],
                    'usage_count' => $offer->used_count,
                    'trend_percentage' => round($trendPercentage, 1),
                    'trend_score' => $offer->trend_score,
                ];
            });

            return $this->successResponse([
                'trending_offers' => $trendingOffers,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get offer categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = $this->offerService->getAvailableCategories();
            
            return $this->successResponse([
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get offer types
     */
    public function types(): JsonResponse
    {
        try {
            $types = [];
            foreach (Offer::TYPES as $key => $value) {
                $types[] = [
                    'key' => $key,
                    'name' => $value,
                ];
            }

            return $this->successResponse([
                'types' => $types
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get specific offer details
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $offer = $this->offerService->getOfferDetails($id);
            
            if (!$offer) {
                return $this->errorResponse('العرض غير موجود', null, 404);
            }

            return $this->successResponse([
                'offer' => new OfferDetailResource($offer)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Validate offer for user
     */
    public function validate(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'order_amount' => 'required|numeric|min:0',
                'items' => 'sometimes|array',
            ]);

            $userId = $request->user() ? $request->user()->id : null;
            $validation = $this->offerService->validateOffer(
                $id, 
                $userId, 
                $request->order_amount,
                $request->items ?? []
            );

            return $this->successResponse($validation);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Redeem/Use offer
     */
    public function redeem(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'order_id' => 'sometimes|exists:orders,id',
                'order_amount' => 'required|numeric|min:0',
                'items' => 'sometimes|array',
            ]);

            if (!$request->user()) {
                return $this->errorResponse('يجب تسجيل الدخول لاستخدام العروض', null, 401);
            }

            $redemption = $this->offerService->redeemOffer(
                $id,
                $request->user()->id,
                $request->order_amount,
                $request->order_id ?? null,
                $request->items ?? []
            );

            return $this->successResponse([
                'redemption' => [
                    'id' => $redemption->id,
                    'redemption_code' => $redemption->redemption_code,
                    'discount_amount' => $redemption->discount_amount,
                    'expires_at' => $redemption->expires_at->toISOString(),
                    'status' => $redemption->status,
                ]
            ], 'تم تفعيل العرض بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get user's offer redemptions
     */
    public function userRedemptions(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return $this->errorResponse('يجب تسجيل الدخول', null, 401);
            }

            $status = $request->get('status');
            $perPage = $request->get('per_page', 20);

            $query = OfferRedemption::where('user_id', $request->user()->id)
                                  ->with(['offer', 'order']);

            if ($status) {
                $query->where('status', $status);
            }

            $redemptions = $query->orderBy('created_at', 'desc')
                                ->paginate($perPage);

            return $this->successResponse([
                'redemptions' => $redemptions->map(function($redemption) {
                    return [
                        'id' => $redemption->id,
                        'offer' => [
                            'id' => $redemption->offer->id,
                            'title' => $redemption->offer->title,
                            'type' => $redemption->offer->type,
                        ],
                        'redemption_code' => $redemption->redemption_code,
                        'discount_amount' => $redemption->discount_amount,
                        'status' => $redemption->status,
                        'status_name' => $redemption->status_name,
                        'expires_at' => $redemption->expires_at ? $redemption->expires_at->toISOString() : null,
                        'used_at' => $redemption->used_at ? $redemption->used_at->toISOString() : null,
                        'created_at' => $redemption->created_at->toISOString(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $redemptions->currentPage(),
                    'per_page' => $redemptions->perPage(),
                    'total' => $redemptions->total(),
                    'last_page' => $redemptions->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get offer performance/analytics
     */
    public function performance(Request $request, string $id): JsonResponse
    {
        try {
            $offer = Offer::findOrFail($id);
            $analytics = $this->offerService->getOfferAnalytics($id);

            return $this->successResponse([
                'offer' => [
                    'id' => $offer->id,
                    'title' => $offer->title,
                    'type' => $offer->type,
                ],
                'analytics' => $analytics
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get category info for offer
     */
    private function getCategoryInfo($offer)
    {
        if (!$offer->applicable_categories || empty($offer->applicable_categories)) {
            return [
                'id' => '1',
                'name' => 'عام',
            ];
        }

        $categories = $offer->applicable_categories;
        return [
            'id' => '1',
            'name' => is_array($categories) ? $categories[0] : $categories,
        ];
    }

    /**
     * Get offer type name
     */
    private function getOfferTypeName($type)
    {
        $typeNames = [
            'discount' => 'discount',
            'bogo' => 'bogo',
            'freebie' => 'free_shipping',
            'cashback' => 'cashback',
            'percentage' => 'percentage_discount',
            'fixed_amount' => 'fixed_discount',
        ];

        return $typeNames[$type] ?? $type;
    }
}
