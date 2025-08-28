<?php

namespace App\Modules\Loyalty\Services;

use App\Modules\Core\BaseService;
use App\Modules\Loyalty\Models\LoyaltyPoint;
use App\Modules\Notifications\Services\NotificationService;

class LoyaltyService extends BaseService
{
    protected $notificationService;

    public function __construct(LoyaltyPoint $loyaltyPoint, NotificationService $notificationService)
    {
        $this->model = $loyaltyPoint;
        $this->notificationService = $notificationService;
    }

    /**
     * Award loyalty points based on cartons/packages/units purchased
     */
    public function awardPointsFromOrder(int $userId, array $orderItems, int $orderId = null): int
    {
        $totalPoints = 0;
        $description = "شراء - ";
        $descriptionParts = [];

        foreach ($orderItems as $item) {
            $itemPoints = 0;
            
            // Calculate points based on selling type
            switch ($item['selling_type']) {
                case 'carton':
                    $itemPoints = ($item['cartons_count'] ?? 0) * ($item['carton_loyalty_points'] ?? 10);
                    if ($item['cartons_count'] > 0) {
                        $descriptionParts[] = "{$item['cartons_count']} كرتون";
                    }
                    break;
                    
                case 'package':
                    $itemPoints = ($item['packages_count'] ?? 0) * ($item['package_loyalty_points'] ?? 5);
                    if ($item['packages_count'] > 0) {
                        $descriptionParts[] = "{$item['packages_count']} علبة";
                    }
                    break;
                    
                default: // unit
                    $itemPoints = ($item['units_count'] ?? 0) * ($item['unit_loyalty_points'] ?? 1);
                    if ($item['units_count'] > 0) {
                        $descriptionParts[] = "{$item['units_count']} قطعة";
                    }
                    break;
            }
            
            $totalPoints += $itemPoints;
        }

        // Create loyalty point record
        if ($totalPoints > 0) {
            $description .= implode(', ', $descriptionParts);
            
            $this->create([
                'user_id' => $userId,
                'points' => $totalPoints,
                'type' => 'earned',
                'description' => $description,
                'order_id' => $orderId,
                'expires_at' => now()->addYear(), // Points expire after 1 year
            ]);

            // Send loyalty points notification
            try {
                $this->notificationService->createLoyaltyNotification(
                    $userId,
                    $totalPoints,
                    $description,
                    'earned'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send loyalty points notification: ' . $e->getMessage());
            }
        }

        return $totalPoints;
    }

    /**
     * Legacy method: Award loyalty points based on order amount
     */
    public function awardPoints(int $userId, float $orderAmount): int
    {
        // 1 point for every 10 EGP spent (legacy calculation)
        $points = floor($orderAmount / 10);
        
        if ($points > 0) {
            $this->create([
                'user_id' => $userId,
                'points' => $points,
                'type' => 'earned',
                'description' => "شراء بقيمة {$orderAmount} جنيه (طريقة قديمة)",
                'expires_at' => now()->addYear(),
            ]);
        }

        return $points;
    }

    /**
     * Apply loyalty discount
     */
    public function applyLoyaltyDiscount(int $userId, int $points): float
    {
        $userPoints = $this->getUserPoints($userId);
        
        if ($userPoints < $points) {
            throw new \Exception('نقاط الولاء غير كافية');
        }

        // 1 point = 0.01 EGP
        $discount = $points * 0.01;

        $this->create([
            'user_id' => $userId,
            'points' => -$points,
            'type' => 'redeemed',
            'description' => "خصم على الطلب",
        ]);

        return $discount;
    }

    /**
     * Get user total points
     */
    public function getUserPoints(int $userId): int
    {
        return $this->model->where('user_id', $userId)
                          ->where(function($q) {
                              $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                          })
                          ->sum('points');
    }

    /**
     * Get user lifetime points (including expired)
     */
    public function getUserLifetimePoints(int $userId): int
    {
        return $this->model->where('user_id', $userId)
                          ->where('points', '>', 0)
                          ->sum('points');
    }

    /**
     * Get carton-based loyalty statistics
     */
    public function getCartonLoyaltyStats(): array
    {
        // Get points earned from cartons
        $cartonPoints = $this->model->where('description', 'LIKE', '%كرتون%')
                                   ->where('type', 'earned')
                                   ->sum('points');
        
        // Get points earned from packages
        $packagePoints = $this->model->where('description', 'LIKE', '%علبة%')
                                    ->where('type', 'earned')
                                    ->sum('points');
        
        // Get points earned from units
        $unitPoints = $this->model->where('description', 'LIKE', '%قطعة%')
                                 ->where('type', 'earned')
                                 ->sum('points');
        
        // Get total users with carton/package purchases
        $usersWithCartonPurchases = $this->model->where('description', 'LIKE', '%كرتون%')
                                               ->distinct('user_id')
                                               ->count();
        
        $usersWithPackagePurchases = $this->model->where('description', 'LIKE', '%علبة%')
                                                ->distinct('user_id')
                                                ->count();

        return [
            'carton_points' => $cartonPoints,
            'package_points' => $packagePoints,
            'unit_points' => $unitPoints,
            'users_with_cartons' => $usersWithCartonPurchases,
            'users_with_packages' => $usersWithPackagePurchases,
            'total_carton_package_points' => $cartonPoints + $packagePoints,
            'carton_vs_package_ratio' => $packagePoints > 0 ? ($cartonPoints / $packagePoints) : 0,
        ];
    }

    /**
     * Award bonus points for carton purchases
     */
    public function awardCartonBonusPoints(int $userId, int $cartonCount): int
    {
        // Bonus: 5 extra points for every 5 cartons purchased
        $bonusPoints = floor($cartonCount / 5) * 5;
        
        if ($bonusPoints > 0) {
            $this->create([
                'user_id' => $userId,
                'points' => $bonusPoints,
                'type' => 'bonus',
                'description' => "مكافأة شراء {$cartonCount} كرتون",
                'expires_at' => now()->addYear(),
            ]);

            // Send bonus points notification
            try {
                $this->notificationService->createLoyaltyNotification(
                    $userId,
                    $bonusPoints,
                    "مكافأة شراء {$cartonCount} كرتون",
                    'earned'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send bonus points notification: ' . $e->getMessage());
            }
        }
        
        return $bonusPoints;
    }

    /**
     * Award bonus points for package purchases
     */
    public function awardPackageBonusPoints(int $userId, int $packageCount): int
    {
        // Bonus: 2 extra points for every 10 packages purchased
        $bonusPoints = floor($packageCount / 10) * 2;
        
        if ($bonusPoints > 0) {
            $this->create([
                'user_id' => $userId,
                'points' => $bonusPoints,
                'type' => 'bonus',
                'description' => "مكافأة شراء {$packageCount} علبة",
                'expires_at' => now()->addYear(),
            ]);

            // Send bonus points notification
            try {
                $this->notificationService->createLoyaltyNotification(
                    $userId,
                    $bonusPoints,
                    "مكافأة شراء {$packageCount} علبة",
                    'earned'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send bonus points notification: ' . $e->getMessage());
            }
        }
        
        return $bonusPoints;
    }

    /**
     * Get user points summary with tier information
     */
    public function getUserPointsSummary(int $userId): array
    {
        $user = \App\Models\User::find($userId);
        $currentPoints = $this->getUserPoints($userId);
        $lifetimePoints = $this->getUserLifetimePoints($userId);
        
        $currentTier = \App\Modules\Loyalty\Models\RewardTier::getTierForUser($user);
        $nextTier = \App\Modules\Loyalty\Models\RewardTier::getNextTierForUser($user);
        
        $progressPercentage = 0;
        if ($currentTier && $nextTier) {
            $progressPercentage = $currentTier->getProgressPercentage($user);
        } elseif (!$nextTier && $currentTier) {
            $progressPercentage = 100; // Max tier reached
        }

        return [
            'current_points' => $currentPoints,
            'lifetime_points' => $lifetimePoints,
            'current_tier' => $currentTier ? [
                'id' => $currentTier->id,
                'name' => $currentTier->name,
                'display_name' => $currentTier->display_name,
                'points_required' => $currentTier->points_required,
                'color' => $currentTier->color,
                'icon_url' => $currentTier->icon_url,
                'benefits' => $currentTier->formatted_benefits,
                'discount_percentage' => $currentTier->discount_percentage,
                'bonus_multiplier' => $currentTier->bonus_multiplier,
            ] : null,
            'next_tier' => $nextTier ? [
                'id' => $nextTier->id,
                'name' => $nextTier->name,
                'display_name' => $nextTier->display_name,
                'points_required' => $nextTier->points_required,
                'points_needed' => $nextTier->points_required - $lifetimePoints,
                'color' => $nextTier->color,
                'icon_url' => $nextTier->icon_url,
            ] : null,
            'progress_percentage' => round($progressPercentage, 1),
        ];
    }

    /**
     * Get user transactions with pagination
     */
    public function getUserTransactions(int $userId, array $filters = [], int $perPage = 20)
    {
        $query = $this->model->where('user_id', $userId);

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->with(['order'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Add points manually (for merchants/admin)
     */
    public function addPoints(int $userId, int $points, string $reason, ?string $transactionId = null, ?int $addedBy = null): \App\Modules\Loyalty\Models\LoyaltyPoint
    {
        return $this->create([
            'user_id' => $userId,
            'points' => $points,
            'type' => \App\Modules\Loyalty\Models\LoyaltyPoint::TYPE_ADMIN_AWARD,
            'description' => $reason,
            'metadata' => [
                'transaction_id' => $transactionId,
                'added_by' => $addedBy,
                'added_at' => now()->toISOString(),
            ],
            'expires_at' => now()->addYear(),
        ]);
    }

    /**
     * Get user analytics
     */
    public function getUserAnalytics(int $userId): array
    {
        $earnedPoints = $this->model->where('user_id', $userId)->earned()->sum('points');
        $redeemedPoints = abs($this->model->where('user_id', $userId)->redeemed()->sum('points'));
        $currentPoints = $this->getUserPoints($userId);
        
        $monthlyEarned = $this->model->where('user_id', $userId)
                                   ->earned()
                                   ->whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->sum('points');

        $averagePerTransaction = $this->model->where('user_id', $userId)
                                           ->earned()
                                           ->avg('points');

        $totalTransactions = $this->model->where('user_id', $userId)->count();

        $pointsByType = $this->model->where('user_id', $userId)
                                  ->selectRaw('type, SUM(points) as total')
                                  ->groupBy('type')
                                  ->pluck('total', 'type')
                                  ->toArray();

        return [
            'total_earned' => $earnedPoints,
            'total_redeemed' => $redeemedPoints,
            'current_balance' => $currentPoints,
            'monthly_earned' => $monthlyEarned,
            'average_per_transaction' => round($averagePerTransaction ?? 0, 2),
            'total_transactions' => $totalTransactions,
            'points_by_type' => $pointsByType,
            'redemption_rate' => $earnedPoints > 0 ? round(($redeemedPoints / $earnedPoints) * 100, 2) : 0,
        ];
    }

    /**
     * Get earning opportunities
     */
    public function getEarningOpportunities(): array
    {
        return [
            [
                'type' => 'purchase',
                'title' => 'اكسب نقاط مع كل عملية شراء',
                'description' => 'احصل على نقاط ولاء مع كل منتج تشتريه',
                'icon' => '🛒',
                'points_info' => 'نقاط متغيرة حسب نوع المنتج',
            ],
            [
                'type' => 'carton_bonus',
                'title' => 'مكافأة الكراتين',
                'description' => 'نقاط إضافية عند شراء 5 كراتين أو أكثر',
                'icon' => '📦',
                'points_info' => '5 نقاط إضافية لكل 5 كراتين',
            ],
            [
                'type' => 'package_bonus',
                'title' => 'مكافأة العلب',
                'description' => 'نقاط إضافية عند شراء 10 علب أو أكثر',
                'icon' => '📋',
                'points_info' => '2 نقطة إضافية لكل 10 علب',
            ],
            [
                'type' => 'referral',
                'title' => 'ادع صديق',
                'description' => 'احصل على نقاط عند دعوة أصدقائك',
                'icon' => '👥',
                'points_info' => '50 نقطة لكل صديق جديد',
            ],
            [
                'type' => 'review',
                'title' => 'اكتب تقييم',
                'description' => 'نقاط إضافية لتقييم المنتجات',
                'icon' => '⭐',
                'points_info' => '10 نقاط لكل تقييم',
            ],
        ];
    }

    /**
     * Calculate tier benefits for user
     */
    public function getUserTierBenefits(int $userId): array
    {
        $user = \App\Models\User::find($userId);
        $tier = \App\Modules\Loyalty\Models\RewardTier::getTierForUser($user);
        
        if (!$tier) {
            return [
                'discount_percentage' => 0,
                'bonus_multiplier' => 1,
                'benefits' => [],
            ];
        }

        return [
            'discount_percentage' => $tier->discount_percentage,
            'bonus_multiplier' => $tier->bonus_multiplier,
            'benefits' => $tier->formatted_benefits,
            'tier_name' => $tier->display_name,
            'tier_color' => $tier->color,
        ];
    }
}
