<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Modules\Users\Models\UserCategory;
use App\Modules\Users\Services\UserCategoryService;
use App\Models\User;

class AdminUserCategoryController extends Controller
{
    protected $userCategoryService;

    public function __construct(UserCategoryService $userCategoryService)
    {
        $this->userCategoryService = $userCategoryService;
    }

    /**
     * Display a listing of user categories
     */
    public function index(Request $request)
    {
        $query = UserCategory::withCount('users');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('display_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('display_name_en', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Purchase amount range filter
        if ($request->filled('min_amount')) {
            $query->where('min_purchase_amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('max_purchase_amount', '<=', $request->max_amount);
        }

        $categories = $query->ordered()->paginate($request->get('per_page', 15));

        // Get statistics
        $stats = $this->getCategoryStats($request);

        if ($request->expectsJson()) {
            return response()->json([
                'categories' => $categories,
                'stats' => $stats
            ]);
        }

        return view('admin.user-categories.index', compact('categories', 'stats'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.user-categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:10|unique:user_categories,name',
            'display_name' => 'required|string|max:255',
            'display_name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'min_cartons' => 'required|integer|min:0',
            'max_cartons' => 'nullable|integer|gt:min_cartons',
            'carton_loyalty_points' => 'required|integer|min:1',
            'bonus_points_per_carton' => 'nullable|integer|min:0',
            'monthly_bonus_points' => 'nullable|integer|min:0',
            'signup_bonus_points' => 'nullable|integer|min:0',
            'has_points_multiplier' => 'boolean',
            'points_multiplier' => 'nullable|numeric|min:1|max:10',
            'requires_carton_purchase' => 'boolean',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        try {
            $category = $this->userCategoryService->createCategory($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء الفئة بنجاح',
                    'category' => $category
                ]);
            }

            return redirect()->route('admin.user-categories.index')
                           ->with('success', 'تم إنشاء الفئة بنجاح');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                           ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])
                           ->withInput();
        }
    }

    /**
     * Display the specified category
     */
    public function show(UserCategory $userCategory)
    {
        $userCategory->loadCount('users');
        
        // Get category statistics
        $categoryStats = $this->getCategoryDetails($userCategory);
        
        // Get recent users in this category
        $recentUsers = User::where('user_category_id', $userCategory->id)
                          ->orderBy('category_updated_at', 'desc')
                          ->limit(10)
                          ->get();

        // Get category distribution data
        $distribution = $this->getCategoryDistribution($userCategory);

        return view('admin.user-categories.show', compact(
            'userCategory', 
            'categoryStats', 
            'recentUsers', 
            'distribution'
        ));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(UserCategory $userCategory)
    {
        return view('admin.user-categories.edit', compact('userCategory'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, UserCategory $userCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:10|unique:user_categories,name,' . $userCategory->id,
            'display_name' => 'required|string|max:255',
            'display_name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'min_cartons' => 'required|integer|min:0',
            'max_cartons' => 'nullable|integer|gt:min_cartons',
            'carton_loyalty_points' => 'required|integer|min:1',
            'bonus_points_per_carton' => 'nullable|integer|min:0',
            'monthly_bonus_points' => 'nullable|integer|min:0',
            'signup_bonus_points' => 'nullable|integer|min:0',
            'has_points_multiplier' => 'boolean',
            'points_multiplier' => 'nullable|numeric|min:1|max:10',
            'requires_carton_purchase' => 'boolean',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        try {
            $category = $this->userCategoryService->updateCategory($userCategory->id, $validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث الفئة بنجاح',
                    'category' => $category
                ]);
            }

            return redirect()->route('admin.user-categories.index')
                           ->with('success', 'تم تحديث الفئة بنجاح');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                           ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])
                           ->withInput();
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(UserCategory $userCategory)
    {
        try {
            $this->userCategoryService->deleteCategory($userCategory->id);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الفئة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(UserCategory $userCategory)
    {
        try {
            $userCategory->update(['is_active' => !$userCategory->is_active]);

            return response()->json([
                'success' => true,
                'message' => $userCategory->is_active ? 'تم تفعيل الفئة' : 'تم إلغاء تفعيل الفئة',
                'is_active' => $userCategory->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Bulk actions on categories
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:user_categories,id',
        ]);

        $categories = UserCategory::whereIn('id', $request->category_ids)->get();
        $count = 0;

        foreach ($categories as $category) {
            switch ($request->action) {
                case 'activate':
                    $category->update(['is_active' => true]);
                    $count++;
                    break;

                case 'deactivate':
                    $category->update(['is_active' => false]);
                    $count++;
                    break;

                case 'delete':
                    try {
                        $this->userCategoryService->deleteCategory($category->id);
                        $count++;
                    } catch (\Exception $e) {
                        // Continue with other categories if one fails
                    }
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم تنفيذ العملية على {$count} فئة"
        ]);
    }

    /**
     * Recalculate all user categories
     */
    public function recalculateCategories()
    {
        try {
            $this->userCategoryService->recalculateAllUserCategories();

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة حساب فئات جميع المستخدمين بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Category analytics
     */
    public function analytics()
    {
        $stats = $this->getAnalyticsStats();
        
        return view('admin.user-categories.analytics', compact('stats'));
    }

    /**
     * Export categories data
     */
    public function export(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'ميزة تصدير فئات المستخدمين قيد التطوير'
        ]);
    }

    /**
     * Get category statistics
     */
    private function getCategoryStats(Request $request = null)
    {
        $query = UserCategory::query();
        
        // Apply same filters as index
        if ($request) {
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('display_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('display_name_en', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }
        }

        return [
            'total_categories' => $query->count(),
            'active_categories' => (clone $query)->where('is_active', true)->count(),
            'inactive_categories' => (clone $query)->where('is_active', false)->count(),
            'total_users_with_categories' => User::whereNotNull('user_category_id')->count(),
            'total_users_without_categories' => User::whereNull('user_category_id')->count(),
            'avg_loyalty_points' => (clone $query)->where('is_active', true)->avg('carton_loyalty_points'),
            'categories_with_benefits' => (clone $query)->whereNotNull('benefits')->count(),
            'highest_loyalty_points' => (clone $query)->where('is_active', true)->max('carton_loyalty_points'),
            'categories_with_multiplier' => (clone $query)->where('has_points_multiplier', true)->count(),
        ];
    }

    /**
     * Get category details
     */
    private function getCategoryDetails($category)
    {
        $users = User::where('user_category_id', $category->id);

        return [
            'total_users' => $users->count(),
            'total_purchase_amount' => $users->sum('total_purchase_amount'),
            'average_purchase_amount' => $users->avg('total_purchase_amount'),
            'total_orders' => $users->sum('total_orders_count'),
            'total_cartons' => $users->sum('total_cartons_purchased'),
            'total_packages' => $users->sum('total_packages_purchased'),
            'total_units' => $users->sum('total_units_purchased'),
            'average_cartons' => $users->avg('total_cartons_purchased'),
            'average_packages' => $users->avg('total_packages_purchased'),
            'average_units' => $users->avg('total_units_purchased'),
            'recent_upgrades' => User::where('user_category_id', $category->id)
                                   ->where('category_updated_at', '>=', now()->subDays(30))
                                   ->count(),
            'category_penetration' => $this->getCategoryPenetration($category),
            'monthly_growth' => $this->getMonthlyGrowth($category),
        ];
    }

    /**
     * Get category distribution
     */
    private function getCategoryDistribution($category)
    {
        $users = User::where('user_category_id', $category->id);

        return [
            'by_join_date' => $users->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                                   ->groupBy('year', 'month')
                                   ->orderBy('year', 'desc')
                                   ->orderBy('month', 'desc')
                                   ->limit(12)
                                   ->get(),
            'by_purchase_range' => $this->getPurchaseRangeDistribution($category),
            'by_activity' => $this->getActivityDistribution($category),
        ];
    }

    /**
     * Get category penetration rate
     */
    private function getCategoryPenetration($category)
    {
        $totalUsers = User::where('role', 'customer')->count();
        $categoryUsers = User::where('user_category_id', $category->id)->count();
        
        return $totalUsers > 0 ? round(($categoryUsers / $totalUsers) * 100, 2) : 0;
    }

    /**
     * Get monthly growth
     */
    private function getMonthlyGrowth($category)
    {
        $thisMonth = User::where('user_category_id', $category->id)
                        ->where('category_updated_at', '>=', now()->startOfMonth())
                        ->count();
                        
        $lastMonth = User::where('user_category_id', $category->id)
                        ->where('category_updated_at', '>=', now()->subMonth()->startOfMonth())
                        ->where('category_updated_at', '<', now()->startOfMonth())
                        ->count();
                        
        if ($lastMonth > 0) {
            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
        }
        
        return $thisMonth > 0 ? 100 : 0;
    }

    /**
     * Get purchase range distribution
     */
    private function getPurchaseRangeDistribution($category)
    {
        $users = User::where('user_category_id', $category->id);
        $minAmount = $category->min_purchase_amount;
        $maxAmount = $category->max_purchase_amount ?? $users->max('total_purchase_amount');
        
        if ($maxAmount <= $minAmount) {
            return [];
        }
        
        $ranges = 5;
        $step = ($maxAmount - $minAmount) / $ranges;
        $distribution = [];
        
        for ($i = 0; $i < $ranges; $i++) {
            $rangeMin = $minAmount + ($i * $step);
            $rangeMax = $minAmount + (($i + 1) * $step);
            
            $count = (clone $users)->where('total_purchase_amount', '>=', $rangeMin)
                                  ->where('total_purchase_amount', '<', $rangeMax)
                                  ->count();
                                  
            $distribution[] = [
                'range' => number_format($rangeMin, 0) . ' - ' . number_format($rangeMax, 0) . ' ج.م',
                'count' => $count
            ];
        }
        
        return $distribution;
    }

    /**
     * Get activity distribution
     */
    private function getActivityDistribution($category)
    {
        $users = User::where('user_category_id', $category->id);
        
        return [
            'active_last_30_days' => (clone $users)->where('last_login_at', '>=', now()->subDays(30))->count(),
            'active_last_7_days' => (clone $users)->where('last_login_at', '>=', now()->subDays(7))->count(),
            'never_logged_in' => (clone $users)->whereNull('last_login_at')->count(),
        ];
    }

    /**
     * Get analytics statistics
     */
    private function getAnalyticsStats()
    {
        $totalUsers = User::where('role', 'customer')->count();
        $usersWithCategories = User::whereNotNull('user_category_id')->count();
        
        return [
            'total_categories' => UserCategory::count(),
            'active_categories' => UserCategory::where('is_active', true)->count(),
            'category_coverage' => $totalUsers > 0 ? round(($usersWithCategories / $totalUsers) * 100, 2) : 0,
            'average_loyalty_points' => UserCategory::where('is_active', true)->avg('carton_loyalty_points'),
            'most_popular_category' => $this->getMostPopularCategory(),
            'highest_value_category' => $this->getHighestValueCategory(),
            'category_distribution' => $this->getAllCategoriesDistribution(),
            'monthly_migrations' => $this->getMonthlyMigrations(),
        ];
    }

    /**
     * Get most popular category
     */
    private function getMostPopularCategory()
    {
        return UserCategory::withCount('users')
                          ->where('is_active', true)
                          ->orderBy('users_count', 'desc')
                          ->first();
    }

    /**
     * Get highest value category
     */
    private function getHighestValueCategory()
    {
        return UserCategory::where('is_active', true)
                          ->orderBy('carton_loyalty_points', 'desc')
                          ->first();
    }

    /**
     * Get all categories distribution
     */
    private function getAllCategoriesDistribution()
    {
        return UserCategory::withCount('users')
                          ->where('is_active', true)
                          ->orderBy('sort_order')
                          ->get()
                          ->map(function($category) {
                              return [
                                  'name' => $category->name,
                                  'display_name' => $category->display_name,
                                  'users_count' => $category->users_count,
                                  'carton_loyalty_points' => $category->carton_loyalty_points,
                                  'bonus_points_per_carton' => $category->bonus_points_per_carton,
                                  'monthly_bonus_points' => $category->monthly_bonus_points,
                                  'signup_bonus_points' => $category->signup_bonus_points,
                                  'has_points_multiplier' => $category->has_points_multiplier,
                                  'points_multiplier' => $category->points_multiplier,
                                  'min_cartons' => $category->min_cartons,
                                  'max_cartons' => $category->max_cartons,
                              ];
                          });
    }

    /**
     * Get monthly migrations between categories
     */
    private function getMonthlyMigrations()
    {
        return User::selectRaw('YEAR(category_updated_at) as year, MONTH(category_updated_at) as month, COUNT(*) as migrations')
                  ->whereNotNull('category_updated_at')
                  ->where('category_updated_at', '>=', now()->subYear())
                  ->groupBy('year', 'month')
                  ->orderBy('year', 'desc')
                  ->orderBy('month', 'desc')
                  ->get();
    }
}
