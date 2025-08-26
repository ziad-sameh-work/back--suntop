@extends('layouts.admin')

@section('title', 'تحليلات العرض - ' . $offer->title)

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

    .analytics-header {
        background: linear-gradient(135deg, var(--suntop-orange), #ff8c42);
        color: var(--white);
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(255, 107, 53, 0.2);
    }

    .offer-info {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .offer-icon {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .offer-icon i {
        font-size: 30px;
        color: var(--suntop-orange);
    }

    .offer-details h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 5px 0;
    }

    .offer-details p {
        margin: 0;
        opacity: 0.9;
        font-size: 16px;
    }

    .analytics-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .analytics-card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
    }

    .analytics-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .analytics-card .icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 20px;
        color: var(--white);
    }

    .analytics-card .icon.orange { background: var(--suntop-orange); }
    .analytics-card .icon.blue { background: var(--suntop-blue); }
    .analytics-card .icon.success { background: var(--success); }
    .analytics-card .icon.warning { background: var(--warning); }
    .analytics-card .icon.info { background: var(--info); }

    .analytics-card h3 {
        font-size: 14px;
        color: var(--gray-600);
        margin: 0 0 8px 0;
        font-weight: 500;
    }

    .analytics-card .value {
        font-size: 28px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .analytics-card .change {
        font-size: 12px;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .analytics-card .change.positive { color: var(--success); }
    .analytics-card .change.negative { color: var(--danger); }
    .analytics-card .change.neutral { color: var(--gray-500); }

    .charts-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
    }

    .chart-card h3 {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    .chart-placeholder {
        width: 100%;
        height: 100%;
        background: var(--gray-100);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-500);
        font-size: 16px;
        border: 2px dashed var(--gray-300);
    }

    .offer-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .btn {
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: var(--suntop-orange);
        color: var(--white);
    }

    .btn-primary:hover {
        background: #e55a2b;
        color: var(--white);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
    }

    .btn-secondary:hover {
        background: var(--gray-200);
        border-color: var(--gray-300);
        color: var(--gray-800);
        text-decoration: none;
        transform: translateY(-1px);
    }

    @media (max-width: 1024px) {
        .charts-section {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .analytics-header {
            padding: 20px;
        }

        .offer-info {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .offer-details h1 {
            font-size: 24px;
        }

        .analytics-stats {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .analytics-card {
            padding: 20px;
        }

        .analytics-card .value {
            font-size: 24px;
        }

        .offer-actions {
            justify-content: center;
        }
    }
</style>

<div class="analytics-header">
    <div class="offer-info">
        <div class="offer-icon">
            <i class="fas fa-gift"></i>
        </div>
        <div class="offer-details">
            <h1>{{ $offer->title }}</h1>
            <p>{{ $offer->code }} • {{ $offer->type === 'percentage' ? $offer->discount_percentage . '%' : number_format($offer->discount_amount) . ' ج.م' }}</p>
        </div>
    </div>
    <div class="offer-actions">
        <a href="{{ route('admin.offers.show', $offer) }}" class="btn btn-secondary">
            <i class="fas fa-eye"></i>
            عرض التفاصيل
        </a>
        <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-secondary">
            <i class="fas fa-edit"></i>
            تعديل العرض
        </a>
        <a href="{{ route('admin.offers.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
    </div>
</div>

<div class="analytics-stats">
    <div class="analytics-card">
        <div class="icon orange">
            <i class="fas fa-users"></i>
        </div>
        <h3>إجمالي الاستخدام</h3>
        <div class="value">{{ number_format($offerStats['total_usage'] ?? 0) }}</div>
        <div class="change neutral">
            <i class="fas fa-tag"></i>
            <span>مرة استخدام</span>
        </div>
    </div>

    @if($offerStats['remaining_usage'] !== null)
    <div class="analytics-card">
        <div class="icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3>المتبقي من الاستخدام</h3>
        <div class="value">{{ number_format($offerStats['remaining_usage']) }}</div>
        <div class="change positive">
            <i class="fas fa-arrow-up"></i>
            <span>متاح</span>
        </div>
    </div>
    @endif

    <div class="analytics-card">
        <div class="icon {{ $offerStats['days_remaining'] >= 0 ? 'info' : 'warning' }}">
            <i class="fas fa-calendar"></i>
        </div>
        <h3>{{ $offerStats['days_remaining'] >= 0 ? 'أيام متبقية' : 'أيام منذ انتهاء' }}</h3>
        <div class="value">{{ abs($offerStats['days_remaining']) }}</div>
        <div class="change {{ $offerStats['days_remaining'] >= 0 ? 'positive' : 'negative' }}">
            <i class="fas fa-clock"></i>
            <span>يوم</span>
        </div>
    </div>

    <div class="analytics-card">
        <div class="icon success">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <h3>إجمالي التوفير</h3>
        <div class="value">{{ number_format($offerStats['total_savings'] ?? 0) }} ج.م</div>
        <div class="change positive">
            <i class="fas fa-arrow-up"></i>
            <span>توفير</span>
        </div>
    </div>

    @if($offer->usage_limit)
    <div class="analytics-card">
        <div class="icon warning">
            <i class="fas fa-percentage"></i>
        </div>
        <h3>معدل الاستخدام</h3>
        <div class="value">{{ number_format($offerStats['usage_percentage'] ?? 0, 1) }}%</div>
        <div class="change neutral">
            <i class="fas fa-chart-line"></i>
            <span>نسبة الاستهلاك</span>
        </div>
    </div>
    @endif

    <div class="analytics-card">
        <div class="icon info">
            <i class="fas fa-calendar-plus"></i>
        </div>
        <h3>تاريخ الإنشاء</h3>
        <div class="value">{{ $offer->created_at->format('d/m') }}</div>
        <div class="change neutral">
            <i class="fas fa-history"></i>
            <span>{{ $offer->created_at->diffForHumans() }}</span>
        </div>
    </div>
</div>

<div class="charts-section">
    <div class="chart-card">
        <h3>
            <i class="fas fa-chart-line"></i>
            استخدام العرض عبر الوقت (آخر 30 يوم)
        </h3>
        <div class="chart-container">
            <div class="chart-placeholder">
                <div style="text-align: center;">
                    <i class="fas fa-chart-line" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                    <p>مخطط استخدام العرض اليومي</p>
                    <small style="color: var(--gray-400);">قريباً - ميزة قيد التطوير</small>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-card">
        <h3>
            <i class="fas fa-chart-pie"></i>
            توزيع الاستخدام
        </h3>
        <div class="chart-container">
            <div class="chart-placeholder">
                <div style="text-align: center;">
                    <i class="fas fa-chart-pie" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                    <p>مخطط دائري لتوزيع الاستخدام</p>
                    <small style="color: var(--gray-400);">قريباً - ميزة قيد التطوير</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// إحصائيات إضافية
const offerStats = {
    totalUsage: {{ $offerStats['total_usage'] ?? 0 }},
    remainingUsage: {{ $offerStats['remaining_usage'] ?? 'null' }},
    daysRemaining: {{ $offerStats['days_remaining'] ?? 0 }},
    isExpired: {{ $offerStats['is_expired'] ?? 'false' ? 'true' : 'false' }},
    isUpcoming: {{ $offerStats['is_upcoming'] ?? 'false' ? 'true' : 'false' }}
};

console.log('بيانات العرض:', offerStats);

// يمكن إضافة مخططات Chart.js هنا لاحقاً
// مثال:
// const ctx = document.getElementById('usageChart').getContext('2d');
// const usageChart = new Chart(ctx, { ... });
</script>
@endsection
