<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\Orders\Models\Order;
use App\Modules\Products\Models\Product;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Users\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // التحقق من صلاحية المدير
        if (auth()->user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة');
        }

        // جمع الإحصائيات الأساسية
        $stats = $this->getDashboardStats();
        
        // Get order statistics
        $orderStats = $this->getOrderStatistics();
        
        // بيانات الرسوم البيانية
        $chartData = $this->getChartsData();
        
        // الطلبات الحديثة
        $recentOrders = $this->getRecentOrders();
        
        // المنتجات الأكثر مبيعاً
        $topProducts = $this->getTopProducts();
        
        // التنبيهات
        $alerts = $this->getSystemAlerts();

        return view('admin.dashboard.index', compact(
            'stats',
            'orderStats',
            'chartData', 
            'recentOrders',
            'topProducts',
            'alerts'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // إحصائيات اليوم
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        // إحصائيات الشهر الحالي
        $thisMonthOrders = Order::where('created_at', '>=', $thisMonth)->count();
        $thisMonthRevenue = Order::where('created_at', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('total_amount');

        // إحصائيات الشهر الماضي للمقارنة
        $lastMonthOrders = Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $lastMonthRevenue = Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->where('status', 'completed')
            ->sum('total_amount');

        // حساب النسب المئوية للتغيير
        $ordersGrowth = $lastMonthOrders > 0 
            ? round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : 100;

        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 100;

        return [
            'total_users' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'total_merchants' => Merchant::count(),
            'total_offers' => \App\Modules\Offers\Models\Offer::count(),
            'total_loyalty_users' => 0, // Disabled - loyalty_points table doesn't exist
            'total_user_categories' => \App\Modules\Users\Models\UserCategory::count(),
            'total_orders' => Order::count(),
            'today_orders' => $todayOrders,
            'today_revenue' => $todayRevenue,
            'this_month_orders' => $thisMonthOrders,
            'this_month_revenue' => $thisMonthRevenue,
            'orders_growth' => $ordersGrowth,
            'revenue_growth' => $revenueGrowth,
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => 0, // Disabled - stock_quantity column no longer exists
        ];
    }

    /**
     * Get charts data
     */
    private function getChartsData()
    {
        // بيانات المبيعات الشهرية لآخر 6 أشهر
        $salesData = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->where('status', 'completed')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // بيانات الطلبات اليومية لآخر 7 أيام
        $dailyOrders = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // توزيع فئات المستخدمين
        $userCategories = UserCategory::withCount('users')->get();

        // أكثر المنتجات مبيعاً
        $topProducts = Product::select(
                'products.id',
                'products.name', 
                'products.price',
                'products.images',
                'products.back_color',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name', 'products.price', 'products.images', 'products.back_color')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        return [
            'sales_data' => $salesData,
            'daily_orders' => $dailyOrders,
            'user_categories' => $userCategories,
            'top_products' => $topProducts,
        ];
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders()
    {
        return Order::with(['user', 'merchant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get top products
     */
    private function getTopProducts()
    {
        return Product::select(
                'products.id',
                'products.name', 
                'products.price',
                'products.images',
                'products.back_color',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name', 'products.price', 'products.images', 'products.back_color')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts()
    {
        $alerts = [];

        // تنبيه المنتجات منخفضة المخزون - معطل (عمود stock_quantity لم يعد موجوداً)
        // $lowStockCount = Product::where('stock_quantity', '<=', 10)->count();

        // تنبيه الطلبات المعلقة
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        if ($pendingOrdersCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'طلبات معلقة',
                'message' => "يوجد {$pendingOrdersCount} طلب في انتظار المراجعة",
                'icon' => 'fas fa-clock'
            ];
        }

        // تنبيه المنتجات غير المتوفرة
        $outOfStockCount = Product::where('is_available', false)->count();
        if ($outOfStockCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'منتجات غير متوفرة',
                'message' => "يوجد {$outOfStockCount} منتج غير متوفر",
                'icon' => 'fas fa-times-circle'
            ];
        }

        // تنبيه التجار غير النشطين
        $inactiveMerchantsCount = Merchant::where('is_active', false)->count();
        if ($inactiveMerchantsCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'تجار غير نشطين',
                'message' => "يوجد {$inactiveMerchantsCount} تاجر غير نشط",
                'icon' => 'fas fa-store-slash'
            ];
        }

        // تنبيه المتاجر المغلقة
        $closedStoresCount = Merchant::where('is_open', false)->count();
        if ($closedStoresCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'متاجر مغلقة',
                'message' => "يوجد {$closedStoresCount} متجر مغلق حالياً",
                'icon' => 'fas fa-door-closed'
            ];
        }

        // تنبيه العروض المنتهية الصلاحية
        $expiredOffersCount = \App\Modules\Offers\Models\Offer::where('valid_until', '<', now())
            ->where('is_active', true)->count();
        if ($expiredOffersCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'عروض منتهية الصلاحية',
                'message' => "يوجد {$expiredOffersCount} عرض منتهي الصلاحية",
                'icon' => 'fas fa-calendar-times'
            ];
        }

        // تنبيه العروض التي ستنتهي قريباً
        $soonExpiringOffersCount = \App\Modules\Offers\Models\Offer::where('valid_until', '>', now())
            ->where('valid_until', '<=', now()->addDays(3))
            ->where('is_active', true)->count();
        if ($soonExpiringOffersCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'عروض تنتهي قريباً',
                'message' => "يوجد {$soonExpiringOffersCount} عرض ينتهي خلال 3 أيام",
                'icon' => 'fas fa-clock'
            ];
        }

        // تنبيه نقاط الولاء المنتهية قريباً (معطل حتى إنشاء الجدول)
        $expiringSoonPointsCount = 0; // Disabled - loyalty_points table doesn't exist
        if (\Illuminate\Support\Facades\Schema::hasTable('loyalty_points')) {
            $expiringSoonPointsCount = \App\Modules\Loyalty\Models\LoyaltyPoint::where('expires_at', '>', now())
                ->where('expires_at', '<=', now()->addDays(7))
                ->where('points', '>', 0)
                ->sum('points');
        }
        if ($expiringSoonPointsCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'نقاط ولاء ستنتهي قريباً',
                'message' => "يوجد {$expiringSoonPointsCount} نقطة ستنتهي خلال 7 أيام",
                'icon' => 'fas fa-star'
            ];
        }

                // تنبيه المستخدمين غير النشطين في برنامج الولاء (معطل حتى إنشاء الجدول)
        $totalUsersCount = User::where('role', 'customer')->count();
        $loyaltyUsersCount = 0; // Disabled - loyalty_points table doesn't exist
        if (\Illuminate\Support\Facades\Schema::hasTable('loyalty_points')) {
            $loyaltyUsersCount = \App\Modules\Loyalty\Models\LoyaltyPoint::distinct('user_id')->count();
        }
        $inactiveLoyaltyRate = $totalUsersCount > 0 ? round((($totalUsersCount - $loyaltyUsersCount) / $totalUsersCount) * 100, 1) : 0;

        if ($inactiveLoyaltyRate > 50) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'معدل مشاركة منخفض في برنامج الولاء',
                'message' => "{$inactiveLoyaltyRate}% من العملاء لم يشاركوا في برنامج الولاء بعد",
                'icon' => 'fas fa-users'
            ];
        }

        // تنبيه فئات المستخدمين غير المحددة
        $usersWithoutCategories = User::where('role', 'customer')->whereNull('user_category_id')->count();
        $uncategorizedRate = $totalUsersCount > 0 ? round(($usersWithoutCategories / $totalUsersCount) * 100, 1) : 0;

        if ($uncategorizedRate > 30) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'مستخدمين بدون تصنيف',
                'message' => "{$uncategorizedRate}% من العملاء لا ينتمون إلى أي فئة",
                'icon' => 'fas fa-layer-group'
            ];
        }

        // تنبيه الفئات غير النشطة
        $inactiveCategoriesCount = \App\Modules\Users\Models\UserCategory::where('is_active', false)->count();
        if ($inactiveCategoriesCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'فئات مستخدمين غير نشطة',
                'message' => "يوجد {$inactiveCategoriesCount} فئة غير نشطة",
                'icon' => 'fas fa-toggle-off'
            ];
        }

        return $alerts;
    }

    /**
     * Get order-specific statistics
     */
    private function getOrderStatistics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'confirmed_orders' => Order::where('status', 'confirmed')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'week_orders' => Order::where('created_at', '>=', $thisWeek)->count(),
            'month_orders' => Order::where('created_at', '>=', $thisMonth)->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_payments' => Order::where('payment_status', 'pending')->sum('total_amount'),
            'failed_orders' => Order::where('payment_status', 'failed')->count(),
        ];
    }

    /**
     * Get dashboard data as JSON for AJAX requests
     */
    public function getData(Request $request)
    {
        $type = $request->get('type', 'stats');

        switch ($type) {
            case 'stats':
                return response()->json($this->getDashboardStats());
            
            case 'charts':
                return response()->json($this->getChartsData());
            
            case 'orders':
                return response()->json($this->getRecentOrders());
            
            case 'alerts':
                return response()->json($this->getSystemAlerts());
                
            default:
                return response()->json(['error' => 'Invalid data type'], 400);
        }
    }
}
