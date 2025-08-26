@extends('layouts.admin')

@section('title', 'تحليلات نقاط الولاء')

@section('content')
<style>
    :root {
        --suntop-orange: #ff6b35;
        --suntop-blue: #4a90e2;
        --white: #ffffff;
        --black: #333333;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
    }

    body {
        background: var(--gray-50);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--gray-800);
        direction: rtl;
    }

    .page-header {
        background: linear-gradient(135deg, var(--suntop-orange), #ff8c42);
        color: var(--white);
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(255, 107, 53, 0.2);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-subtitle {
        opacity: 0.9;
        font-size: 16px;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 15px;
    }

    .btn-white {
        background: var(--white);
        color: var(--suntop-orange);
        border: 2px solid var(--white);
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-white:hover {
        background: transparent;
        color: var(--white);
        border-color: var(--white);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .analytics-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .analytics-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--gray-50);
    }

    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 16px;
    }

    .card-icon.orange { background: var(--suntop-orange); }
    .card-icon.success { background: var(--success); }
    .card-icon.warning { background: var(--warning); }
    .card-icon.danger { background: var(--danger); }
    .card-icon.info { background: var(--info); }
    .card-icon.purple { background: #8b5cf6; }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .card-content {
        padding: 25px;
    }

    .metric-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 8px;
        line-height: 1;
    }

    .metric-label {
        font-size: 14px;
        color: var(--gray-600);
        margin-bottom: 15px;
    }

    .metric-trend {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
    }

    .metric-trend.positive {
        color: var(--success);
    }

    .metric-trend.negative {
        color: var(--danger);
    }

    .metric-trend.neutral {
        color: var(--gray-500);
    }

    .chart-section {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .chart-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-content {
        padding: 30px;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gray-100);
        border: 2px dashed var(--gray-300);
        color: var(--gray-500);
        text-align: center;
    }

    .insights-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .insights-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        padding: 25px;
    }

    .insights-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .insight-item {
        padding: 15px 0;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .insight-item:last-child {
        border-bottom: none;
    }

    .insight-label {
        font-size: 14px;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .insight-value {
        font-weight: 600;
        color: var(--gray-900);
    }

    .recommendations {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        border-radius: 12px;
        padding: 25px;
        border: 1px solid var(--gray-200);
    }

    .recommendations-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recommendation-item {
        background: var(--white);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .recommendation-item:last-child {
        margin-bottom: 0;
    }

    .recommendation-icon {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 14px;
        flex-shrink: 0;
    }

    .recommendation-text {
        font-size: 14px;
        color: var(--gray-700);
        line-height: 1.5;
    }

    @media (max-width: 1024px) {
        .insights-section {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            align-items: stretch;
        }

        .header-actions {
            justify-content: center;
        }

        .analytics-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
    }
</style>

<div class="page-header">
    <div class="header-content">
        <div>
            <h1 class="page-title">
                <i class="fas fa-chart-line"></i>
                تحليلات نقاط الولاء
            </h1>
            <p class="page-subtitle">تحليل شامل لأداء برنامج الولاء وسلوك العملاء</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.loyalty.settings') }}" class="btn-white">
                <i class="fas fa-cog"></i> الإعدادات
            </a>
            <a href="{{ route('admin.loyalty.export') }}" class="btn-white">
                <i class="fas fa-download"></i> تصدير
            </a>
            <a href="{{ route('admin.loyalty.index') }}" class="btn-white">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>
</div>

<!-- Analytics Cards -->
<div class="analytics-grid">
    <!-- Total Users with Points -->
    <div class="analytics-card">
        <div class="card-header">
            <div class="card-icon orange">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="card-title">المستخدمين النشطين</h3>
        </div>
        <div class="card-content">
            <div class="metric-value">{{ number_format($stats['total_users_with_points']) }}</div>
            <div class="metric-label">مستخدم لديه نقاط ولاء</div>
            <div class="metric-trend positive">
                <i class="fas fa-arrow-up"></i>
                <span>نمو مستمر</span>
            </div>
        </div>
    </div>

    <!-- Total Active Points -->
    <div class="analytics-card">
        <div class="card-header">
            <div class="card-icon success">
                <i class="fas fa-star"></i>
            </div>
            <h3 class="card-title">النقاط النشطة</h3>
        </div>
        <div class="card-content">
            <div class="metric-value">{{ number_format($stats['total_active_points']) }}</div>
            <div class="metric-label">نقطة قابلة للاستخدام</div>
            <div class="metric-trend positive">
                <i class="fas fa-arrow-up"></i>
                <span>في ازدياد</span>
            </div>
        </div>
    </div>

    <!-- Lifetime Earned -->
    <div class="analytics-card">
        <div class="card-header">
            <div class="card-icon info">
                <i class="fas fa-plus-circle"></i>
            </div>
            <h3 class="card-title">إجمالي المكتسب</h3>
        </div>
        <div class="card-content">
            <div class="metric-value">{{ number_format($stats['total_lifetime_earned']) }}</div>
            <div class="metric-label">نقطة مكتسبة مدى الحياة</div>
            <div class="metric-trend neutral">
                <i class="fas fa-chart-bar"></i>
                <span>تراكمي</span>
            </div>
        </div>
    </div>

    <!-- Lifetime Redeemed -->
    <div class="analytics-card">
        <div class="card-header">
            <div class="card-icon danger">
                <i class="fas fa-minus-circle"></i>
            </div>
            <h3 class="card-title">إجمالي المسترد</h3>
        </div>
        <div class="card-content">
            <div class="metric-value">{{ number_format($stats['total_lifetime_redeemed']) }}</div>
            <div class="metric-label">نقطة مستردة مدى الحياة</div>
            <div class="metric-trend neutral">
                <i class="fas fa-chart-bar"></i>
                <span>تراكمي</span>
            </div>
        </div>
    </div>

    <!-- Average Points Per User -->
    <div class="analytics-card">
        <div class="card-header">
            <div class="card-icon warning">
                <i class="fas fa-calculator"></i>
            </div>
            <h3 class="card-title">متوسط النقاط</h3>
        </div>
        <div class="card-content">
            <div class="metric-value">{{ number_format($stats['avg_points_per_user']) }}</div>
            <div class="metric-label">نقطة لكل مستخدم</div>
            <div class="metric-trend positive">
                <i class="fas fa-balance-scale"></i>
                <span>متوازن</span>
            </div>
        </div>
    </div>

    <!-- Redemption Rate -->
    <div class="analytics-card">
        <div class="card-header">
            <div class="card-icon purple">
                <i class="fas fa-percentage"></i>
            </div>
            <h3 class="card-title">معدل الاسترداد</h3>
        </div>
        <div class="card-content">
            <div class="metric-value">{{ $stats['redemption_rate'] }}%</div>
            <div class="metric-label">من النقاط المكتسبة</div>
            <div class="metric-trend {{ $stats['redemption_rate'] > 30 ? 'positive' : ($stats['redemption_rate'] > 15 ? 'neutral' : 'negative') }}">
                <i class="fas fa-{{ $stats['redemption_rate'] > 30 ? 'arrow-up' : ($stats['redemption_rate'] > 15 ? 'minus' : 'arrow-down') }}"></i>
                <span>{{ $stats['redemption_rate'] > 30 ? 'ممتاز' : ($stats['redemption_rate'] > 15 ? 'جيد' : 'منخفض') }}</span>
            </div>
        </div>
    </div>

    <!-- Expiry Rate -->
    <div class="analytics-card">
        <div class="card-header">
            <div class="card-icon danger">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="card-title">معدل الانتهاء</h3>
        </div>
        <div class="card-content">
            <div class="metric-value">{{ $stats['expiry_rate'] }}%</div>
            <div class="metric-label">من النقاط منتهية</div>
            <div class="metric-trend {{ $stats['expiry_rate'] < 10 ? 'positive' : ($stats['expiry_rate'] < 20 ? 'neutral' : 'negative') }}">
                <i class="fas fa-{{ $stats['expiry_rate'] < 10 ? 'check' : ($stats['expiry_rate'] < 20 ? 'exclamation' : 'times') }}"></i>
                <span>{{ $stats['expiry_rate'] < 10 ? 'ممتاز' : ($stats['expiry_rate'] < 20 ? 'مقبول' : 'يحتاج تحسين') }}</span>
            </div>
        </div>
    </div>

    <!-- Most Active Month -->
    <div class="analytics-card">
        <div class="card-header">
            <div class="card-icon info">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="card-title">أكثر الشهور نشاطاً</h3>
        </div>
        <div class="card-content">
            <div class="metric-value">{{ $stats['most_active_month'] }}</div>
            <div class="metric-label">شهر بأعلى معاملات</div>
            <div class="metric-trend neutral">
                <i class="fas fa-chart-line"></i>
                <span>تحليل زمني</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="chart-section">
    <div class="chart-header">
        <h3 class="chart-title">
            <i class="fas fa-chart-area"></i>
            تطور نقاط الولاء عبر الزمن
        </h3>
    </div>
    <div class="chart-content">
        <div>
            <i class="fas fa-chart-area" style="font-size: 64px; opacity: 0.3; margin-bottom: 20px; display: block;"></i>
            <h4 style="margin: 0 0 10px 0; font-size: 18px;">مخطط تطور النقاط الشهري</h4>
            <p style="margin: 0; color: var(--gray-400); font-size: 14px;">سيتم إضافة المخططات التفاعلية قريباً باستخدام Chart.js</p>
            <div style="margin-top: 20px; color: var(--gray-500); font-size: 12px;">
                <p>• مخطط خطي لتطور النقاط المكتسبة والمستردة</p>
                <p>• مخطط دائري لتوزيع أنواع المعاملات</p>
                <p>• مخطط شريطي لأداء المستخدمين الأفضل</p>
            </div>
        </div>
    </div>
</div>

<!-- Insights Section -->
<div class="insights-section">
    <!-- Performance Insights -->
    <div class="insights-card">
        <h3 class="insights-title">
            <i class="fas fa-lightbulb" style="color: var(--suntop-orange);"></i>
            رؤى الأداء
        </h3>
        
        <div class="insight-item">
            <div class="insight-label">
                <i class="fas fa-users" style="color: var(--info);"></i>
                معدل المشاركة
            </div>
            <div class="insight-value">
                @php
                    $totalUsers = \App\Models\User::where('role', 'customer')->count();
                    $participationRate = $totalUsers > 0 ? round(($stats['total_users_with_points'] / $totalUsers) * 100, 1) : 0;
                @endphp
                {{ $participationRate }}%
            </div>
        </div>

        <div class="insight-item">
            <div class="insight-label">
                <i class="fas fa-star" style="color: var(--warning);"></i>
                متوسط النقاط النشطة
            </div>
            <div class="insight-value">
                {{ $stats['total_users_with_points'] > 0 ? number_format($stats['total_active_points'] / $stats['total_users_with_points']) : 0 }}
            </div>
        </div>

        <div class="insight-item">
            <div class="insight-label">
                <i class="fas fa-exchange-alt" style="color: var(--success);"></i>
                كفاءة البرنامج
            </div>
            <div class="insight-value">
                @php
                    $efficiency = $stats['total_lifetime_earned'] > 0 ? round((($stats['total_lifetime_redeemed'] + $stats['total_active_points']) / $stats['total_lifetime_earned']) * 100, 1) : 0;
                @endphp
                {{ $efficiency }}%
            </div>
        </div>

        <div class="insight-item">
            <div class="insight-label">
                <i class="fas fa-clock" style="color: var(--danger);"></i>
                نقاط معرضة للانتهاء
            </div>
            <div class="insight-value">
                @php
                    $expiringSoon = \App\Modules\Loyalty\Models\LoyaltyPoint::where('expires_at', '>', now())
                        ->where('expires_at', '<=', now()->addDays(30))
                        ->where('points', '>', 0)
                        ->sum('points');
                @endphp
                {{ number_format($expiringSoon) }}
            </div>
        </div>
    </div>

    <!-- User Behavior -->
    <div class="insights-card">
        <h3 class="insights-title">
            <i class="fas fa-user-chart" style="color: var(--info);"></i>
            سلوك المستخدمين
        </h3>
        
        <div class="insight-item">
            <div class="insight-label">
                <i class="fas fa-trophy" style="color: var(--suntop-orange);"></i>
                أعلى رصيد نقاط
            </div>
            <div class="insight-value">
                @php
                    $topUserPoints = \Illuminate\Support\Facades\DB::table('loyalty_points')
                        ->select(\Illuminate\Support\Facades\DB::raw('SUM(points) as total_points'))
                        ->where('points', '>', 0)
                        ->where(function($q) {
                            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                        })
                        ->groupBy('user_id')
                        ->orderBy('total_points', 'desc')
                        ->first();
                @endphp
                {{ $topUserPoints ? number_format($topUserPoints->total_points) : 0 }}
            </div>
        </div>

        <div class="insight-item">
            <div class="insight-label">
                <i class="fas fa-fire" style="color: var(--danger);"></i>
                أكثر مستخدم نشاطاً
            </div>
            <div class="insight-value">
                @php
                    $mostActiveUser = \Illuminate\Support\Facades\DB::table('loyalty_points')
                        ->select(\Illuminate\Support\Facades\DB::raw('COUNT(*) as transactions'))
                        ->groupBy('user_id')
                        ->orderBy('transactions', 'desc')
                        ->first();
                @endphp
                {{ $mostActiveUser ? number_format($mostActiveUser->transactions) : 0 }} معاملة
            </div>
        </div>

        <div class="insight-item">
            <div class="insight-label">
                <i class="fas fa-calendar-day" style="color: var(--success);"></i>
                معاملات هذا الأسبوع
            </div>
            <div class="insight-value">
                @php
                    $weeklyTransactions = \App\Modules\Loyalty\Models\LoyaltyPoint::where('created_at', '>=', now()->subWeek())->count();
                @endphp
                {{ number_format($weeklyTransactions) }}
            </div>
        </div>

        <div class="insight-item">
            <div class="insight-label">
                <i class="fas fa-percent" style="color: var(--warning);"></i>
                نسبة النشطين اليوم
            </div>
            <div class="insight-value">
                @php
                    $todayActiveUsers = \App\Modules\Loyalty\Models\LoyaltyPoint::whereDate('created_at', today())->distinct('user_id')->count();
                    $todayRate = $stats['total_users_with_points'] > 0 ? round(($todayActiveUsers / $stats['total_users_with_points']) * 100, 1) : 0;
                @endphp
                {{ $todayRate }}%
            </div>
        </div>
    </div>
</div>

<!-- Recommendations -->
<div class="recommendations">
    <h3 class="recommendations-title">
        <i class="fas fa-magic" style="color: var(--suntop-orange);"></i>
        توصيات لتحسين برنامج الولاء
    </h3>
    
    @if($stats['redemption_rate'] < 20)
    <div class="recommendation-item">
        <div class="recommendation-icon" style="background: var(--warning);">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="recommendation-text">
            <strong>تحسين معدل الاسترداد:</strong> معدل الاسترداد الحالي {{ $stats['redemption_rate'] }}% منخفض. اعتبر تحسين قيمة النقاط أو تقليل الحد الأدنى للاسترداد لتشجيع الاستخدام.
        </div>
    </div>
    @endif

    @if($participationRate < 60)
    <div class="recommendation-item">
        <div class="recommendation-icon" style="background: var(--info);">
            <i class="fas fa-users"></i>
        </div>
        <div class="recommendation-text">
            <strong>زيادة المشاركة:</strong> {{ $participationRate }}% فقط من العملاء يشاركون في البرنامج. أرسل حملات توعية ومكافآت ترحيبية لجذب المزيد.
        </div>
    </div>
    @endif

    @if($stats['expiry_rate'] > 15)
    <div class="recommendation-item">
        <div class="recommendation-icon" style="background: var(--danger);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="recommendation-text">
            <strong>تقليل انتهاء النقاط:</strong> {{ $stats['expiry_rate'] }}% من النقاط تنتهي. اعتبر إرسال تذكيرات قبل الانتهاء أو زيادة فترة الصلاحية.
        </div>
    </div>
    @endif

    @if($expiringSoon > 1000)
    <div class="recommendation-item">
        <div class="recommendation-icon" style="background: var(--warning);">
            <i class="fas fa-bell"></i>
        </div>
        <div class="recommendation-text">
            <strong>تنبيه عاجل:</strong> {{ number_format($expiringSoon) }} نقطة ستنتهي خلال 30 يوم. أرسل تذكيرات للعملاء لاستخدام نقاطهم.
        </div>
    </div>
    @endif

    @if($stats['avg_points_per_user'] < 50)
    <div class="recommendation-item">
        <div class="recommendation-icon" style="background: var(--success);">
            <i class="fas fa-gift"></i>
        </div>
        <div class="recommendation-text">
            <strong>تحفيز أكثر:</strong> متوسط النقاط لكل مستخدم منخفض. أضف مكافآت إضافية للأنشطة مثل التقييمات والإحالات.
        </div>
    </div>
    @endif

    @if($stats['redemption_rate'] > 50)
    <div class="recommendation-item">
        <div class="recommendation-icon" style="background: var(--success);">
            <i class="fas fa-thumbs-up"></i>
        </div>
        <div class="recommendation-text">
            <strong>أداء ممتاز!</strong> معدل الاسترداد عالي ({{ $stats['redemption_rate'] }}%) مما يدل على فعالية البرنامج. استمر في هذا النهج.
        </div>
    </div>
    @endif
</div>

<script>
// Analytics data for future chart implementation
const analyticsData = {
    totalUsersWithPoints: {{ $stats['total_users_with_points'] }},
    totalActivePoints: {{ $stats['total_active_points'] }},
    totalLifetimeEarned: {{ $stats['total_lifetime_earned'] }},
    totalLifetimeRedeemed: {{ $stats['total_lifetime_redeemed'] }},
    redemptionRate: {{ $stats['redemption_rate'] }},
    expiryRate: {{ $stats['expiry_rate'] }},
    avgPointsPerUser: {{ $stats['avg_points_per_user'] }},
    mostActiveMonth: '{{ $stats['most_active_month'] }}',
    participationRate: {{ $participationRate }},
    expiringSoon: {{ $expiringSoon }}
};

console.log('تحليلات نقاط الولاء:', analyticsData);

// Future: Chart.js implementation
// This data can be used to create interactive charts showing:
// - Monthly points earned vs redeemed
// - User activity trends
// - Point distribution among users
// - Redemption patterns over time
</script>
@endsection
