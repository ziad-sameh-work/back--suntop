<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Loyalty\Services\LoyaltyService;
use App\Modules\Loyalty\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LoyaltyController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Get user's loyalty points summary
     */
    public function getPoints(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'current_balance' => $this->loyaltyService->getUserPoints($userId),
                    'lifetime_points' => $this->loyaltyService->getUserLifetimePoints($userId),
                    'points_to_expire' => $this->getPointsToExpire($userId)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب نقاط الولاء: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's loyalty points transactions history
     */
    public function getTransactions(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $perPage = $request->get('per_page', 10);
            
            $transactions = LoyaltyPoint::where('user_id', $userId)
                                      ->orderBy('created_at', 'desc')
                                      ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => $transactions->items(),
                    'summary' => [
                        'total_earned' => LoyaltyPoint::where('user_id', $userId)
                                                    ->where('points', '>', 0)
                                                    ->sum('points'),
                        'total_redeemed' => abs(LoyaltyPoint::where('user_id', $userId)
                                                         ->where('points', '<', 0)
                                                         ->sum('points'))
                    ],
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'total_pages' => $transactions->lastPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب معاملات نقاط الولاء: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get points that will expire soon
     */
    private function getPointsToExpire(int $userId): array
    {
        $now = now();
        $oneMonthFromNow = $now->copy()->addMonth();
        $threeMonthsFromNow = $now->copy()->addMonths(3);
        
        // Points expiring in next month
        $expiringNextMonth = LoyaltyPoint::where('user_id', $userId)
                                        ->where('points', '>', 0)
                                        ->whereNotNull('expires_at')
                                        ->whereBetween('expires_at', [$now, $oneMonthFromNow])
                                        ->sum('points');
        
        // Points expiring in 1-3 months
        $expiringInThreeMonths = LoyaltyPoint::where('user_id', $userId)
                                            ->where('points', '>', 0)
                                            ->whereNotNull('expires_at')
                                            ->whereBetween('expires_at', [$oneMonthFromNow, $threeMonthsFromNow])
                                            ->sum('points');
        
        return [
            'next_month' => $expiringNextMonth,
            'next_three_months' => $expiringInThreeMonths
        ];
    }
}
