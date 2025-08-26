<?php

namespace App\Modules\Loyalty\Services;

use App\Modules\Core\BaseService;
use App\Modules\Loyalty\Models\LoyaltyPoint;

class LoyaltyService extends BaseService
{
    public function __construct(LoyaltyPoint $loyaltyPoint)
    {
        $this->model = $loyaltyPoint;
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
        }
        
        return $bonusPoints;
    }
}
