@extends('layouts.admin')

@section('title', 'تحليلات فئات المستخدمين')

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
        --purple: #8b5cf6;
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

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .stat-card.orange { border-right: 4px solid var(--suntop-orange); }
    .stat-card.success { border-right: 4px solid var(--success); }
    .stat-card.warning { border-right: 4px solid var(--warning); }
    .stat-card.danger { border-right: 4px solid var(--danger); }
    .stat-card.info { border-right: 4px solid var(--info); }
    .stat-card.purple { border-right: 4px solid var(--purple); }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 18px;
    }

    .stat-icon.orange { background: var(--suntop-orange); }
    .stat-icon.success { background: var(--success); }
    .stat-icon.warning { background: var(--warning); }
    .stat-icon.danger { background: var(--danger); }
    .stat-icon.info { background: var(--info); }
    .stat-icon.purple { background: var(--purple); }

    .stat-title {
        font-size: 14px;
        color: var(--gray-600);
        margin: 0;
        font-weight: 500;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 8px 0;
    }

    .stat-change {
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--gray-500);
    }

    .content-section {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 14px;
        background: var(--suntop-orange);
    }

    .section-body {
        padding: 25px;
    }

    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 20px;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .category-card {
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
    }

    .category-card:hover {
        border-color: var(--suntop-orange);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .category-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .category-badge.category-a {
        background: rgba(255, 107, 53, 0.1);
        color: var(--suntop-orange);
        border: 1px solid rgba(255, 107, 53, 0.2);
    }

    .category-badge.category-b {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .category-badge.category-c {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .category-badge.category-vip {
        background: rgba(139, 92, 246, 0.1);
        color: var(--purple);
        border: 1px solid rgba(139, 92, 246, 0.2);
    }

    .category-stats {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .category-stat {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }

    .category-stat-label {
        color: var(--gray-600);
    }

    .category-stat-value {
        font-weight: 600;
        color: var(--gray-900);
    }

    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .insight-card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }

    .insight-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 20px;
        color: var(--white);
    }

    .insight-icon.orange { background: var(--suntop-orange); }
    .insight-icon.success { background: var(--success); }
    .insight-icon.warning { background: var(--warning); }
    .insight-icon.info { background: var(--info); }

    .insight-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 10px 0;
    }

    .insight-description {
        font-size: 14px;
        color: var(--gray-600);
        line-height: 1.5;
        margin: 0;
    }

    .migrations-table {
        width: 100%;
        border-collapse: collapse;
    }

    .migrations-table th,
    .migrations-table td {
        padding: 12px 15px;
        text-align: right;
        border-bottom: 1px solid var(--gray-200);
    }

    .migrations-table th {
        background: var(--gray-50);
        font-weight: 600;
        color: var(--gray-700);
        font-size: 13px;
    }

    .migrations-table td {
        color: var(--gray-800);
        font-size: 14px;
    }

    .trend-indicator {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .trend-indicator.positive {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .trend-indicator.negative {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .trend-indicator.neutral {
        background: var(--gray-100);
        color: var(--gray-600);
    }

    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
        
        .categories-grid {
            grid-template-columns: 1fr;
        }
        
        .insights-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-chart-line"></i>
        تحليلات فئات المستخدمين
    </h1>
    <p class="page-subtitle">تحليل شامل لأداء وتوزيع فئات العملاء ومعدلات التحويل</p>
</div>

<!-- Main Statistics -->
<div class="stats-row">
    <div class="stat-card orange">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-layer-group"></i>
            </div>
            <h3 class="stat-title">إجمالي الفئات</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_categories']) }}</div>
        <div class="stat-change">
            <i class="fas fa-tags"></i>
            <span>فئة مختلفة</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-toggle-on"></i>
            </div>
            <h3 class="stat-title">الفئات النشطة</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['active_categories']) }}</div>
        <div class="stat-change">
            <i class="fas fa-check-circle"></i>
            <span>فعالة حالياً</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-percentage"></i>
            </div>
            <h3 class="stat-title">معدل التغطية</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['category_coverage'], 1) }}%</div>
        <div class="stat-change">
            <i class="fas fa-users"></i>
            <span>من العملاء مصنفين</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-gift"></i>
            </div>
            <h3 class="stat-title">متوسط الخصم</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['average_discount'], 1) }}%</div>
        <div class="stat-change">
            <i class="fas fa-calculator"></i>
            <span>خصم متوسط</span>
        </div>
    </div>
</div>

<!-- Category Distribution -->
<div class="content-section">
    <div class="section-header">
        <h3 class="section-title">
            <div class="section-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            توزيع الفئات
        </h3>
    </div>
    <div class="section-body">
        <div class="categories-grid">
            @if($stats['category_distribution'] && count($stats['category_distribution']) > 0)
                @foreach($stats['category_distribution'] as $category)
                    <div class="category-card">
                        <div class="category-header">
                            <span class="category-badge category-{{ strtolower($category['name']) }}">
                                {{ $category['name'] }}
                            </span>
                            <div style="font-size: 12px; color: var(--gray-500);">
                                @if(($category['carton_loyalty_points'] ?? 0) > 0)
                                    <span style="color: #FFD700;">{{ $category['carton_loyalty_points'] ?? 10 }} نقطة/كرتون</span>
                                    @if(($category['bonus_points_per_carton'] ?? 0) > 0)
                                        <span style="color: #FF6B35;"> +{{ $category['bonus_points_per_carton'] }}</span>
                                    @endif
                                    @if(($category['has_points_multiplier'] ?? false) && ($category['points_multiplier'] ?? 1) > 1)
                                        <span style="color: #9C27B0;"> ×{{ $category['points_multiplier'] }}</span>
                                    @endif
                                @else
                                    <span style="color: #999;">10 نقطة/كرتون</span>
                                @endif
                            </div>
                        </div>
                        <h4 style="margin: 0 0 15px 0; color: var(--gray-900);">{{ $category['display_name'] }}</h4>
                        <div class="category-stats">
                            <div class="category-stat">
                                <span class="category-stat-label">عدد المستخدمين</span>
                                <span class="category-stat-value">{{ number_format($category['users_count']) }}</span>
                            </div>
                            <div class="category-stat">
                                <span class="category-stat-label">نسبة التوزيع</span>
                                <span class="category-stat-value">
                                    @php
                                        $totalUsers = collect($stats['category_distribution'])->sum('users_count');
                                        $percentage = $totalUsers > 0 ? round(($category['users_count'] / $totalUsers) * 100, 1) : 0;
                                    @endphp
                                    {{ $percentage }}%
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--gray-500);">
                    <i class="fas fa-chart-pie" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                    <p>لا توجد بيانات توزيع للفئات</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Popular Categories -->
<div class="content-section">
    <div class="section-header">
        <h3 class="section-title">
            <div class="section-icon">
                <i class="fas fa-star"></i>
            </div>
            الفئات الأكثر شعبية
        </h3>
    </div>
    <div class="section-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <h4 style="color: var(--gray-900); margin: 0 0 15px 0;">الفئة الأكثر شعبية</h4>
                @if($stats['most_popular_category'])
                    <div style="padding: 20px; background: var(--gray-50); border-radius: 12px; border: 2px solid var(--success);">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <span class="category-badge category-{{ strtolower($stats['most_popular_category']->name) }}">
                                {{ $stats['most_popular_category']->name }}
                            </span>
                            <span style="font-size: 14px; color: var(--gray-600);">
                                {{ $stats['most_popular_category']->display_name }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 14px;">
                            <span>عدد المستخدمين:</span>
                            <strong>{{ number_format($stats['most_popular_category']->users_count) }}</strong>
                        </div>
                    </div>
                @else
                    <p style="color: var(--gray-500);">لا توجد بيانات</p>
                @endif
            </div>

            <div>
                <h4 style="color: var(--gray-900); margin: 0 0 15px 0;">الفئة الأعلى قيمة</h4>
                @if($stats['highest_value_category'])
                    <div style="padding: 20px; background: var(--gray-50); border-radius: 12px; border: 2px solid var(--warning);">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <span class="category-badge category-{{ strtolower($stats['highest_value_category']->name) }}">
                                {{ $stats['highest_value_category']->name }}
                            </span>
                            <span style="font-size: 14px; color: var(--gray-600);">
                                {{ $stats['highest_value_category']->display_name }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 14px;">
                            <span>نسبة الخصم:</span>
                            <strong>{{ number_format($stats['highest_value_category']->discount_percentage, 1) }}%</strong>
                        </div>
                    </div>
                @else
                    <p style="color: var(--gray-500);">لا توجد بيانات</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Monthly Migrations -->
@if(isset($stats['monthly_migrations']) && count($stats['monthly_migrations']) > 0)
<div class="content-section">
    <div class="section-header">
        <h3 class="section-title">
            <div class="section-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            انتقالات الفئات الشهرية
        </h3>
    </div>
    <div class="section-body">
        <table class="migrations-table">
            <thead>
                <tr>
                    <th>الشهر</th>
                    <th>عدد الانتقالات</th>
                    <th>الاتجاه</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['monthly_migrations']->take(6) as $migration)
                    <tr>
                        <td>{{ $migration->year }}/{{ str_pad($migration->month, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ number_format($migration->migrations) }}</td>
                        <td>
                            @php
                                $trend = 'neutral';
                                $icon = 'fas fa-minus';
                                if($loop->index > 0) {
                                    $previous = $stats['monthly_migrations'][$loop->index - 1]->migrations ?? 0;
                                    if($migration->migrations > $previous) {
                                        $trend = 'positive';
                                        $icon = 'fas fa-arrow-up';
                                    } elseif($migration->migrations < $previous) {
                                        $trend = 'negative';
                                        $icon = 'fas fa-arrow-down';
                                    }
                                }
                            @endphp
                            <span class="trend-indicator {{ $trend }}">
                                <i class="{{ $icon }}"></i>
                                {{ $trend === 'positive' ? 'صعود' : ($trend === 'negative' ? 'هبوط' : 'ثابت') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Advanced Insights -->
<div class="content-section">
    <div class="section-header">
        <h3 class="section-title">
            <div class="section-icon">
                <i class="fas fa-lightbulb"></i>
            </div>
            رؤى متقدمة وتوصيات
        </h3>
    </div>
    <div class="section-body">
        <div class="insights-grid">
            <div class="insight-card">
                <div class="insight-icon orange">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="insight-title">معدل التغطية</h4>
                <p class="insight-description">
                    {{ number_format($stats['category_coverage'], 1) }}% من العملاء مصنفين في فئات.
                    @if($stats['category_coverage'] < 80)
                        يُنصح بتحسين عملية التصنيف التلقائي.
                    @else
                        معدل ممتاز للتغطية!
                    @endif
                </p>
            </div>

            <div class="insight-card">
                <div class="insight-icon success">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4 class="insight-title">الفئة الأكثر نمواً</h4>
                <p class="insight-description">
                    @if($stats['most_popular_category'])
                        فئة "{{ $stats['most_popular_category']->name }}" تضم {{ number_format($stats['most_popular_category']->users_count) }} عميل.
                        هذه إشارة إيجابية لنمو قاعدة العملاء.
                    @else
                        لا توجد بيانات كافية لتحديد الفئة الأكثر نمواً.
                    @endif
                </p>
            </div>

            <div class="insight-card">
                <div class="insight-icon warning">
                    <i class="fas fa-percentage"></i>
                </div>
                <h4 class="insight-title">متوسط الخصومات</h4>
                <p class="insight-description">
                    متوسط نسبة الخصم {{ number_format($stats['average_discount'], 1) }}%.
                    @if($stats['average_discount'] > 15)
                        قد تحتاج لمراجعة هيكل الخصومات لضمان الربحية.
                    @else
                        هيكل خصومات متوازن ومناسب.
                    @endif
                </p>
            </div>

            <div class="insight-card">
                <div class="insight-icon info">
                    <i class="fas fa-cogs"></i>
                </div>
                <h4 class="insight-title">التوصيات</h4>
                <p class="insight-description">
                    @if($stats['active_categories'] < $stats['total_categories'])
                        يوجد {{ $stats['total_categories'] - $stats['active_categories'] }} فئة غير نشطة. 
                        فكر في تفعيلها أو حذفها.
                    @else
                        جميع الفئات نشطة ومُحسّنة!
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// يمكن إضافة مخططات Chart.js هنا لاحقاً
document.addEventListener('DOMContentLoaded', function() {
    console.log('تحليلات فئات المستخدمين محملة بنجاح');
});
</script>
@endsection
