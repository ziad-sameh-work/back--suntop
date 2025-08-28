<?php

namespace App\Modules\Loyalty\Services;

use App\Modules\Core\BaseService;
use App\Modules\Loyalty\Models\Reward;
use App\Modules\Loyalty\Models\RewardRedemption;
use App\Modules\Loyalty\Models\LoyaltyPoint;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RewardService extends BaseService
{
    public function __construct(Reward $reward)
    {
        $this->model = $reward;
    }

    /**
     * Get available rewards with filters
     */
    public function getAvailableRewards(
        array $filters = [],
        string $sortBy = 'points_cost',
        string $sortOrder = 'asc',
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = $this->model->active();

        // Apply filters
        if (isset($filters['category']) && $filters['category']) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['min_points'])) {
            $query->where('points_cost', '>=', $filters['min_points']);
        }

        if (isset($filters['max_points'])) {
            $query->where('points_cost', '<=', $filters['max_points']);
        }

        // Add user affordability info if user_id provided
        if (isset($filters['user_id']) && $filters['user_id']) {
            $userPoints = LoyaltyPoint::getUserActivePoints($filters['user_id']);
            $query->selectRaw('*, CASE WHEN points_cost <= ? THEN 1 ELSE 0 END as can_afford', [$userPoints]);
        }

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    /**
     * Get reward details
     */
    public function getRewardDetails(string $id): ?Reward
    {
        return $this->model->find($id);
    }

    /**
     * Redeem reward for user
     */
    public function redeemReward(string $rewardId, int $userId, ?int $orderId = null): RewardRedemption
    {
        $reward = $this->model->find($rewardId);
        
        if (!$reward) {
            throw new \Exception('المكافأة غير موجودة');
        }

        if (!$reward->isAvailable()) {
            throw new \Exception('المكافأة غير متاحة حالياً');
        }

        $userPoints = LoyaltyPoint::getUserActivePoints($userId);
        
        if ($userPoints < $reward->points_cost) {
            throw new \Exception('نقاطك غير كافية لاستبدال هذه المكافأة');
        }

        // Deduct points from user
        LoyaltyPoint::create([
            'user_id' => $userId,
            'points' => -$reward->points_cost,
            'type' => LoyaltyPoint::TYPE_REDEEMED,
            'description' => "استبدال مكافأة: {$reward->title}",
            'reference_type' => Reward::class,
            'reference_id' => $rewardId,
        ]);

        // Create redemption record
        $redemption = RewardRedemption::create([
            'user_id' => $userId,
            'reward_id' => $rewardId,
            'order_id' => $orderId,
            'redemption_code' => Reward::generateRedemptionCode(),
            'points_deducted' => $reward->points_cost,
            'discount_amount' => $reward->discount_amount,
            'status' => RewardRedemption::STATUS_PENDING,
            'expires_at' => now()->addDays($reward->expiry_days),
            'metadata' => [
                'redeemed_at' => now()->toISOString(),
                'reward_type' => $reward->type,
            ],
        ]);

        // Update reward usage count
        $reward->increment('used_count');

        return $redemption;
    }

    /**
     * Get available categories
     */
    public function getAvailableCategories(): array
    {
        return $this->model->active()
                          ->distinct()
                          ->whereNotNull('category')
                          ->pluck('category')
                          ->toArray();
    }

    /**
     * Get points range
     */
    public function getPointsRange(): array
    {
        $min = $this->model->active()->min('points_cost') ?? 0;
        $max = $this->model->active()->max('points_cost') ?? 0;

        return [
            'min' => $min,
            'max' => $max,
        ];
    }

    /**
     * Get reward analytics
     */
    public function getRewardAnalytics(string $rewardId): array
    {
        $reward = $this->model->find($rewardId);
        
        if (!$reward) {
            throw new \Exception('المكافأة غير موجودة');
        }

        $totalRedemptions = RewardRedemption::where('reward_id', $rewardId)->count();
        $usedRedemptions = RewardRedemption::where('reward_id', $rewardId)
                                         ->where('status', RewardRedemption::STATUS_USED)
                                         ->count();
        $pendingRedemptions = RewardRedemption::where('reward_id', $rewardId)
                                            ->where('status', RewardRedemption::STATUS_PENDING)
                                            ->count();

        $totalPointsRedeemed = RewardRedemption::where('reward_id', $rewardId)
                                             ->where('status', RewardRedemption::STATUS_USED)
                                             ->sum('points_deducted');

        $usageRate = $reward->usage_limit ? ($reward->used_count / $reward->usage_limit) * 100 : 0;
        $conversionRate = $totalRedemptions > 0 ? ($usedRedemptions / $totalRedemptions) * 100 : 0;

        return [
            'total_redemptions' => $totalRedemptions,
            'used_redemptions' => $usedRedemptions,
            'pending_redemptions' => $pendingRedemptions,
            'usage_count' => $reward->used_count,
            'usage_limit' => $reward->usage_limit,
            'usage_rate_percentage' => round($usageRate, 2),
            'conversion_rate_percentage' => round($conversionRate, 2),
            'total_points_redeemed' => $totalPointsRedeemed,
            'average_points_per_redemption' => $usedRedemptions > 0 ? round($totalPointsRedeemed / $usedRedemptions, 2) : 0,
        ];
    }

    /**
     * Get popular rewards
     */
    public function getPopularRewards(int $limit = 5): Collection
    {
        return $this->model->active()
                          ->orderBy('used_count', 'desc')
                          ->take($limit)
                          ->get();
    }

    /**
     * Get rewards by points budget
     */
    public function getRewardsByBudget(int $userPoints, int $limit = 10): Collection
    {
        return $this->model->active()
                          ->where('points_cost', '<=', $userPoints)
                          ->orderBy('points_cost', 'desc')
                          ->take($limit)
                          ->get();
    }

    /**
     * Get recommended rewards for user
     */
    public function getRecommendedRewards(int $userId, int $limit = 5): Collection
    {
        $userPoints = LoyaltyPoint::getUserActivePoints($userId);
        
        // Get rewards user can afford, ordered by popularity and value
        return $this->model->active()
                          ->where('points_cost', '<=', $userPoints)
                          ->orderByRaw('(used_count * 0.3) + (points_cost * 0.7) DESC')
                          ->take($limit)
                          ->get();
    }
}
