<?php

namespace App\Modules\Offers\Services;

use App\Modules\Core\BaseService;
use App\Modules\Offers\Models\Offer;
use App\Modules\Offers\Models\OfferRedemption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OfferService extends BaseService
{
    public function __construct(Offer $offer)
    {
        $this->model = $offer;
    }

    /**
     * Get offers with filters
     */
    public function getOffers(
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = $this->model->query();

        // Apply filters
        if (isset($filters['category']) && $filters['category']) {
            $query->where('applicable_categories', 'LIKE', "%{$filters['category']}%");
        }

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['active_only']) && $filters['active_only']) {
            $query->where('is_active', true)
                  ->where('valid_from', '<=', now())
                  ->where('valid_until', '>=', now());
        }

        // Filter out offers that reached usage limit
        $query->where(function($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    /**
     * Get featured offers
     */
    public function getFeaturedOffers(int $limit = 10, ?int $categoryId = null): Collection
    {
        $query = $this->model->activeFeatured();

        if ($categoryId) {
            $query->where('applicable_categories', 'LIKE', "%{$categoryId}%");
        }

        return $query->where(function($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    })
                    ->orderBy('display_order', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->take($limit)
                    ->get();
    }

    /**
     * Get trending offers
     */
    public function getTrendingOffers(int $limit = 3): Collection
    {
        return $this->model->where('is_active', true)
                          ->where('valid_from', '<=', now())
                          ->where('valid_until', '>=', now())
                          ->trending()
                          ->take($limit)
                          ->get();
    }

    /**
     * Get quick stats for home page
     */
    public function getQuickStats(): array
    {
        $activeOffersCount = $this->model->where('is_active', true)
                                        ->where('valid_from', '<=', now())
                                        ->where('valid_until', '>=', now())
                                        ->count();

        // Calculate total savings today (from redemptions)
        $totalSavingsToday = \App\Modules\Offers\Models\OfferRedemption::whereDate('created_at', today())
                                                                      ->where('status', 'used')
                                                                      ->sum('discount_amount');

        // Get most popular offer
        $mostPopularOffer = $this->model->where('is_active', true)
                                       ->where('used_count', '>', 0)
                                       ->orderBy('used_count', 'desc')
                                       ->first();

        return [
            'active_offers_count' => $activeOffersCount,
            'total_savings_today' => round($totalSavingsToday, 2),
            'most_popular_offer' => $mostPopularOffer ? [
                'id' => $mostPopularOffer->id,
                'title' => $mostPopularOffer->title,
                'usage_count' => $mostPopularOffer->used_count,
            ] : null,
        ];
    }

    /**
     * Get offer details
     */
    public function getOfferDetails(string $id): ?Offer
    {
        return $this->model->find($id);
    }

    /**
     * Validate offer for user
     */
    public function validateOffer(string $offerId, ?int $userId, float $orderAmount, array $items = []): array
    {
        $offer = $this->model->find($offerId);
        
        if (!$offer) {
            return [
                'valid' => false,
                'message' => 'العرض غير موجود',
                'discount_amount' => 0,
            ];
        }

        if (!$offer->isValid()) {
            return [
                'valid' => false,
                'message' => 'العرض غير صالح أو منتهي الصلاحية',
                'discount_amount' => 0,
            ];
        }

        if ($offer->minimum_amount && $orderAmount < $offer->minimum_amount) {
            return [
                'valid' => false,
                'message' => "الحد الأدنى للطلب هو {$offer->minimum_amount} جنيه",
                'discount_amount' => 0,
            ];
        }

        // Check if user already used this offer (if first_order_only)
        if ($userId && $offer->first_order_only) {
            $existingRedemption = OfferRedemption::where('user_id', $userId)
                                               ->where('offer_id', $offerId)
                                               ->whereIn('status', ['used', 'pending'])
                                               ->exists();
            
            if ($existingRedemption) {
                return [
                    'valid' => false,
                    'message' => 'هذا العرض للطلب الأول فقط',
                    'discount_amount' => 0,
                ];
            }
        }

        $discountAmount = $offer->getDiscountValue($orderAmount);

        return [
            'valid' => true,
            'message' => 'العرض صالح للاستخدام',
            'discount_amount' => $discountAmount,
            'offer' => [
                'id' => $offer->id,
                'title' => $offer->title,
                'type' => $offer->type,
                'description' => $offer->description,
            ],
        ];
    }

    /**
     * Redeem offer
     */
    public function redeemOffer(string $offerId, int $userId, float $orderAmount, ?int $orderId = null, array $items = []): OfferRedemption
    {
        $validation = $this->validateOffer($offerId, $userId, $orderAmount, $items);
        
        if (!$validation['valid']) {
            throw new \Exception($validation['message']);
        }

        $offer = $this->model->find($offerId);
        
        // Create redemption record
        $redemption = OfferRedemption::create([
            'user_id' => $userId,
            'offer_id' => $offerId,
            'order_id' => $orderId,
            'redemption_code' => OfferRedemption::generateRedemptionCode(),
            'discount_amount' => $validation['discount_amount'],
            'status' => OfferRedemption::STATUS_PENDING,
            'expires_at' => now()->addDays(7), // Expires in 7 days
            'metadata' => [
                'order_amount' => $orderAmount,
                'items' => $items,
                'redeemed_at' => now()->toISOString(),
            ],
        ]);

        // Update offer usage count
        $offer->increment('used_count');

        return $redemption;
    }

    /**
     * Apply offer discount (legacy method)
     */
    public function applyOffer(string $code, float $subtotal): float
    {
        $offer = $this->model->where('code', $code)
                            ->where('is_active', true)
                            ->where('valid_from', '<=', now())
                            ->where('valid_until', '>=', now())
                            ->first();

        if (!$offer) {
            throw new \Exception('كود العرض غير صالح أو منتهي الصلاحية');
        }

        if ($offer->usage_limit && $offer->used_count >= $offer->usage_limit) {
            throw new \Exception('تم استنفاد عدد استخدامات هذا العرض');
        }

        if ($offer->minimum_amount && $subtotal < $offer->minimum_amount) {
            throw new \Exception("الحد الأدنى للطلب هو {$offer->minimum_amount} جنيه");
        }

        $discount = $offer->getDiscountValue($subtotal);

        // Update usage count
        $offer->increment('used_count');

        return $discount;
    }

    /**
     * Get available categories
     */
    public function getAvailableCategories(): array
    {
        $categories = $this->model->whereNotNull('applicable_categories')
                                ->get()
                                ->pluck('applicable_categories')
                                ->flatten()
                                ->unique()
                                ->filter()
                                ->values()
                                ->toArray();

        return array_merge(['عصائر', 'توصيل', 'نقاط الولاء', 'موسمية'], $categories);
    }

    /**
     * Get offer analytics
     */
    public function getOfferAnalytics(string $offerId): array
    {
        $offer = $this->model->find($offerId);
        
        if (!$offer) {
            throw new \Exception('العرض غير موجود');
        }

        $totalRedemptions = OfferRedemption::where('offer_id', $offerId)->count();
        $usedRedemptions = OfferRedemption::where('offer_id', $offerId)
                                        ->where('status', OfferRedemption::STATUS_USED)
                                        ->count();
        $pendingRedemptions = OfferRedemption::where('offer_id', $offerId)
                                           ->where('status', OfferRedemption::STATUS_PENDING)
                                           ->count();

        $totalDiscountGiven = OfferRedemption::where('offer_id', $offerId)
                                           ->where('status', OfferRedemption::STATUS_USED)
                                           ->sum('discount_amount');

        $usageRate = $offer->usage_limit ? ($offer->used_count / $offer->usage_limit) * 100 : 0;
        $conversionRate = $totalRedemptions > 0 ? ($usedRedemptions / $totalRedemptions) * 100 : 0;

        return [
            'total_redemptions' => $totalRedemptions,
            'used_redemptions' => $usedRedemptions,
            'pending_redemptions' => $pendingRedemptions,
            'usage_count' => $offer->used_count,
            'usage_limit' => $offer->usage_limit,
            'usage_rate_percentage' => round($usageRate, 2),
            'conversion_rate_percentage' => round($conversionRate, 2),
            'total_discount_given' => $totalDiscountGiven,
            'average_discount_per_use' => $usedRedemptions > 0 ? round($totalDiscountGiven / $usedRedemptions, 2) : 0,
        ];
    }
}
