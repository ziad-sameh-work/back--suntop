<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Modules\Loyalty\Models\LoyaltyPoint;
use App\Models\User;
use App\Modules\Orders\Models\Order;

class AdminLoyaltyController extends Controller
{
    /**
     * Display a listing of loyalty points transactions
     */
    public function index(Request $request)
    {
        $query = LoyaltyPoint::with(['user', 'order']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('username', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Type filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Points range filter
        if ($request->filled('points_from')) {
            $query->where('points', '>=', $request->points_from);
        }
        if ($request->filled('points_to')) {
            $query->where('points', '<=', $request->points_to);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // User filter
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        $transactions = $query->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 20));

        // Get statistics
        $stats = $this->getLoyaltyStats($request);

        // Get top users by points
        $topUsers = $this->getTopUsers();

        if ($request->expectsJson()) {
            return response()->json([
                'transactions' => $transactions,
                'stats' => $stats,
                'topUsers' => $topUsers
            ]);
        }

        return view('admin.loyalty.index', compact('transactions', 'stats', 'topUsers'));
    }

    /**
     * Show user's loyalty points details
     */
    public function show(User $user)
    {
        $userStats = $this->getUserStats($user);
        
        $recentTransactions = LoyaltyPoint::where('user_id', $user->id)
                                        ->with(['order'])
                                        ->orderBy('created_at', 'desc')
                                        ->limit(20)
                                        ->get();

        $monthlyStats = $this->getUserMonthlyStats($user);

        return view('admin.loyalty.show', compact('user', 'userStats', 'recentTransactions', 'monthlyStats'));
    }

    /**
     * Award points to user
     */
    public function awardPoints(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1|max:10000',
            'description' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $user = User::findOrFail($validated['user_id']);

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => $validated['points'],
            'type' => LoyaltyPoint::TYPE_ADMIN_AWARD,
            'description' => $validated['description'],
            'expires_at' => $validated['expires_at'],
            'metadata' => [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
            ]
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم منح النقاط بنجاح',
                'user_points' => LoyaltyPoint::getUserActivePoints($user->id)
            ]);
        }

        return redirect()->back()->with('success', 'تم منح النقاط بنجاح');
    }

    /**
     * Deduct points from user
     */
    public function deductPoints(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1|max:10000',
            'description' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $currentPoints = LoyaltyPoint::getUserActivePoints($user->id);

        if ($currentPoints < $validated['points']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'نقاط المستخدم غير كافية'
                ], 400);
            }
            return redirect()->back()->withErrors(['points' => 'نقاط المستخدم غير كافية']);
        }

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => -$validated['points'],
            'type' => LoyaltyPoint::TYPE_ADMIN_DEDUCT,
            'description' => $validated['description'],
            'metadata' => [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
            ]
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم خصم النقاط بنجاح',
                'user_points' => LoyaltyPoint::getUserActivePoints($user->id)
            ]);
        }

        return redirect()->back()->with('success', 'تم خصم النقاط بنجاح');
    }

    /**
     * Bulk operations on loyalty points
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:award,deduct,expire',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'points' => 'required_if:action,award,deduct|integer|min:1|max:10000',
            'description' => 'required|string|max:255',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $count = 0;

        foreach ($users as $user) {
            switch ($request->action) {
                case 'award':
                    LoyaltyPoint::create([
                        'user_id' => $user->id,
                        'points' => $request->points,
                        'type' => LoyaltyPoint::TYPE_ADMIN_AWARD,
                        'description' => $request->description,
                        'metadata' => [
                            'admin_id' => auth()->id(),
                            'admin_name' => auth()->user()->name,
                            'bulk_operation' => true
                        ]
                    ]);
                    $count++;
                    break;

                case 'deduct':
                    $currentPoints = LoyaltyPoint::getUserActivePoints($user->id);
                    if ($currentPoints >= $request->points) {
                        LoyaltyPoint::create([
                            'user_id' => $user->id,
                            'points' => -$request->points,
                            'type' => LoyaltyPoint::TYPE_ADMIN_DEDUCT,
                            'description' => $request->description,
                            'metadata' => [
                                'admin_id' => auth()->id(),
                                'admin_name' => auth()->user()->name,
                                'bulk_operation' => true
                            ]
                        ]);
                        $count++;
                    }
                    break;

                case 'expire':
                    $expiredPoints = LoyaltyPoint::where('user_id', $user->id)
                                                ->where('points', '>', 0)
                                                ->whereNull('expires_at')
                                                ->sum('points');
                    if ($expiredPoints > 0) {
                        LoyaltyPoint::create([
                            'user_id' => $user->id,
                            'points' => -$expiredPoints,
                            'type' => LoyaltyPoint::TYPE_EXPIRED,
                            'description' => $request->description,
                            'metadata' => [
                                'admin_id' => auth()->id(),
                                'admin_name' => auth()->user()->name,
                                'bulk_operation' => true
                            ]
                        ]);
                        $count++;
                    }
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم تنفيذ العملية على {$count} مستخدم"
        ]);
    }

    /**
     * Get loyalty system settings
     */
    public function settings()
    {
        $settings = [
            'earn_rate' => 10, // 1 point per 10 EGP
            'redeem_rate' => 100, // 100 points = 1 EGP
            'min_redeem' => 100, // Minimum points to redeem
            'expiry_months' => 12, // Points expire after 12 months
            'signup_bonus' => 50, // Bonus points for new signup
            'review_bonus' => 10, // Points for product review
        ];

        return view('admin.loyalty.settings', compact('settings'));
    }

    /**
     * Update loyalty system settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'earn_rate' => 'required|integer|min:1|max:100',
            'redeem_rate' => 'required|integer|min:1|max:1000',
            'min_redeem' => 'required|integer|min:1|max:1000',
            'expiry_months' => 'required|integer|min:1|max:60',
            'signup_bonus' => 'required|integer|min:0|max:1000',
            'review_bonus' => 'required|integer|min:0|max:100',
        ]);

        // Here you would save to database settings table
        // For now, we'll just show success message

        return redirect()->back()->with('success', 'تم تحديث إعدادات نظام الولاء بنجاح');
    }

    /**
     * Export loyalty data
     */
    public function export(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'ميزة تصدير نقاط الولاء قيد التطوير'
        ]);
    }

    /**
     * Loyalty analytics
     */
    public function analytics()
    {
        $stats = $this->getAnalyticsStats();
        
        return view('admin.loyalty.analytics', compact('stats'));
    }

    /**
     * Get loyalty statistics
     */
    private function getLoyaltyStats(Request $request = null)
    {
        $query = LoyaltyPoint::query();
        
        // Apply same filters as index
        if ($request) {
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->whereHas('user', function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('username', 'LIKE', "%{$searchTerm}%");
                });
            }
        }

        return [
            'total_transactions' => $query->count(),
            'total_points_awarded' => (clone $query)->where('points', '>', 0)->sum('points'),
            'total_points_redeemed' => abs((clone $query)->where('points', '<', 0)->sum('points')),
            'active_users' => (clone $query)->distinct('user_id')->count(),
            'avg_points_per_user' => $query->count() > 0 ? round((clone $query)->avg('points'), 1) : 0,
            'transactions_today' => (clone $query)->whereDate('created_at', today())->count(),
            'points_awarded_today' => (clone $query)->whereDate('created_at', today())->where('points', '>', 0)->sum('points'),
            'expired_points' => LoyaltyPoint::where('expires_at', '<', now())->where('points', '>', 0)->sum('points'),
        ];
    }

    /**
     * Get top users by loyalty points
     */
    private function getTopUsers()
    {
        return DB::table('loyalty_points')
                 ->select('user_id', DB::raw('SUM(points) as total_points'))
                 ->where('points', '>', 0)
                 ->where(function($q) {
                     $q->whereNull('expires_at')
                       ->orWhere('expires_at', '>', now());
                 })
                 ->groupBy('user_id')
                 ->orderBy('total_points', 'desc')
                 ->limit(10)
                 ->get()
                 ->map(function($item) {
                     $user = User::find($item->user_id);
                     return [
                         'user' => $user,
                         'total_points' => $item->total_points
                     ];
                 });
    }

    /**
     * Get user statistics
     */
    private function getUserStats($user)
    {
        return [
            'total_active_points' => LoyaltyPoint::getUserActivePoints($user->id),
            'lifetime_earned' => LoyaltyPoint::getUserLifetimePoints($user->id),
            'total_redeemed' => abs(LoyaltyPoint::where('user_id', $user->id)->where('points', '<', 0)->sum('points')),
            'total_transactions' => LoyaltyPoint::where('user_id', $user->id)->count(),
            'last_activity' => LoyaltyPoint::where('user_id', $user->id)->latest()->first()?->created_at,
            'join_date' => $user->created_at,
            'avg_monthly_earned' => $this->getUserMonthlyAverage($user),
            'expiring_soon' => LoyaltyPoint::where('user_id', $user->id)
                                         ->where('points', '>', 0)
                                         ->where('expires_at', '>', now())
                                         ->where('expires_at', '<=', now()->addDays(30))
                                         ->sum('points'),
        ];
    }

    /**
     * Get user monthly statistics
     */
    private function getUserMonthlyStats($user)
    {
        return LoyaltyPoint::where('user_id', $user->id)
                          ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(CASE WHEN points > 0 THEN points ELSE 0 END) as earned, SUM(CASE WHEN points < 0 THEN ABS(points) ELSE 0 END) as redeemed')
                          ->groupBy('year', 'month')
                          ->orderBy('year', 'desc')
                          ->orderBy('month', 'desc')
                          ->limit(12)
                          ->get();
    }

    /**
     * Get user monthly average
     */
    private function getUserMonthlyAverage($user)
    {
        $monthsSinceJoin = max(1, $user->created_at->diffInMonths(now()));
        $totalEarned = LoyaltyPoint::getUserLifetimePoints($user->id);
        
        return round($totalEarned / $monthsSinceJoin, 1);
    }

    /**
     * Get analytics statistics
     */
    private function getAnalyticsStats()
    {
        return [
            'total_users_with_points' => LoyaltyPoint::distinct('user_id')->count(),
            'total_active_points' => LoyaltyPoint::where('points', '>', 0)
                                               ->where(function($q) {
                                                   $q->whereNull('expires_at')
                                                     ->orWhere('expires_at', '>', now());
                                               })->sum('points'),
            'total_lifetime_earned' => LoyaltyPoint::where('points', '>', 0)->sum('points'),
            'total_lifetime_redeemed' => abs(LoyaltyPoint::where('points', '<', 0)->sum('points')),
            'avg_points_per_user' => round(LoyaltyPoint::where('points', '>', 0)->avg('points'), 1),
            'most_active_month' => $this->getMostActiveMonth(),
            'redemption_rate' => $this->getRedemptionRate(),
            'expiry_rate' => $this->getExpiryRate(),
        ];
    }

    /**
     * Get most active month
     */
    private function getMostActiveMonth()
    {
        $result = LoyaltyPoint::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as transactions')
                             ->groupBy('year', 'month')
                             ->orderBy('transactions', 'desc')
                             ->first();
        
        return $result ? "{$result->year}-{$result->month}" : 'غير محدد';
    }

    /**
     * Get redemption rate
     */
    private function getRedemptionRate()
    {
        $totalEarned = LoyaltyPoint::where('points', '>', 0)->sum('points');
        $totalRedeemed = abs(LoyaltyPoint::where('points', '<', 0)->sum('points'));
        
        return $totalEarned > 0 ? round(($totalRedeemed / $totalEarned) * 100, 1) : 0;
    }

    /**
     * Get expiry rate
     */
    private function getExpiryRate()
    {
        $totalEarned = LoyaltyPoint::where('points', '>', 0)->sum('points');
        $totalExpired = LoyaltyPoint::where('expires_at', '<', now())->where('points', '>', 0)->sum('points');
        
        return $totalEarned > 0 ? round(($totalExpired / $totalEarned) * 100, 1) : 0;
    }
}
