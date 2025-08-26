@extends('layouts.admin')

@section('title', 'تحليلات التاجر - ' . $merchant->name)

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

    .merchant-info {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .merchant-logo {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .merchant-logo img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    .merchant-logo i {
        font-size: 30px;
        color: var(--suntop-orange);
    }

    .merchant-details h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 5px 0;
    }

    .merchant-details p {
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

    .merchant-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
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

    .top-products {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 30px;
    }

    .top-products h3 {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        background: var(--gray-50);
        transition: all 0.3s ease;
    }

    .product-item:hover {
        background: var(--gray-100);
    }

    .product-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .product-image img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
    }

    .product-image i {
        color: var(--gray-400);
        font-size: 18px;
    }

    .product-info {
        flex: 1;
    }

    .product-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 4px 0;
    }

    .product-info p {
        font-size: 12px;
        color: var(--gray-600);
        margin: 0;
    }

    .product-stats {
        text-align: left;
        color: var(--gray-700);
        font-size: 12px;
    }

    .product-stats strong {
        color: var(--gray-900);
        font-size: 14px;
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

        .merchant-info {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .merchant-details h1 {
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

        .merchant-actions {
            justify-content: center;
        }
    }
</style>

<div class="analytics-header">
    <div class="merchant-info">
        <div class="merchant-logo">
            @if($merchant->logo_url)
                <img src="{{ $merchant->logo_url }}" alt="{{ $merchant->name }}">
            @else
                <i class="fas fa-store"></i>
            @endif
        </div>
        <div class="merchant-details">
            <h1>{{ $merchant->name }}</h1>
            <p>{{ $merchant->business_name ?? $merchant->name }}</p>
            <p>{{ $merchant->category }} • {{ $merchant->city }}</p>
        </div>
    </div>
    <div class="merchant-actions">
        <a href="{{ route('admin.merchants.show', $merchant) }}" class="btn btn-secondary">
            <i class="fas fa-eye"></i>
            عرض التفاصيل
        </a>
        <a href="{{ route('admin.merchants.edit', $merchant) }}" class="btn btn-secondary">
            <i class="fas fa-edit"></i>
            تعديل البيانات
        </a>
        <a href="{{ route('admin.merchants.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
    </div>
</div>

<div class="analytics-stats">
    <div class="analytics-card">
        <div class="icon orange">
            <i class="fas fa-box"></i>
        </div>
        <h3>إجمالي المنتجات</h3>
        <div class="value">{{ number_format($merchantStats['total_products'] ?? 0) }}</div>
        <div class="change neutral">
            <i class="fas fa-cube"></i>
            <span>منتج مسجل</span>
        </div>
    </div>

    <div class="analytics-card">
        <div class="icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3>المنتجات النشطة</h3>
        <div class="value">{{ number_format($merchantStats['active_products'] ?? 0) }}</div>
        <div class="change positive">
            <i class="fas fa-arrow-up"></i>
            <span>متاح للبيع</span>
        </div>
    </div>

    <div class="analytics-card">
        <div class="icon blue">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h3>إجمالي الطلبات</h3>
        <div class="value">{{ number_format($merchantStats['total_orders'] ?? 0) }}</div>
        <div class="change neutral">
            <i class="fas fa-receipt"></i>
            <span>طلب</span>
        </div>
    </div>

    <div class="analytics-card">
        <div class="icon success">
            <i class="fas fa-truck"></i>
        </div>
        <h3>الطلبات المكتملة</h3>
        <div class="value">{{ number_format($merchantStats['completed_orders'] ?? 0) }}</div>
        <div class="change positive">
            <i class="fas fa-check"></i>
            <span>تم التسليم</span>
        </div>
    </div>

    <div class="analytics-card">
        <div class="icon orange">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <h3>إجمالي الإيرادات</h3>
        <div class="value">{{ number_format($merchantStats['total_revenue'] ?? 0) }} ج.م</div>
        <div class="change positive">
            <i class="fas fa-arrow-up"></i>
            <span>إيراد</span>
        </div>
    </div>

    <div class="analytics-card">
        <div class="icon info">
            <i class="fas fa-calendar-month"></i>
        </div>
        <h3>إيرادات هذا الشهر</h3>
        <div class="value">{{ number_format($merchantStats['this_month_revenue'] ?? 0) }} ج.م</div>
        <div class="change positive">
            <i class="fas fa-chart-line"></i>
            <span>شهر {{ now()->format('m') }}</span>
        </div>
    </div>

    <div class="analytics-card">
        <div class="icon warning">
            <i class="fas fa-calculator"></i>
        </div>
        <h3>متوسط قيمة الطلب</h3>
        <div class="value">{{ number_format($merchantStats['avg_order_value'] ?? 0) }} ج.م</div>
        <div class="change neutral">
            <i class="fas fa-equals"></i>
            <span>متوسط</span>
        </div>
    </div>

    <div class="analytics-card">
        <div class="icon success">
            <i class="fas fa-percentage"></i>
        </div>
        <h3>العمولة المكتسبة</h3>
        <div class="value">{{ number_format($merchantStats['commission_earned'] ?? 0) }} ج.م</div>
        <div class="change positive">
            <i class="fas fa-coins"></i>
            <span>عمولة {{ $merchant->commission_rate ?? 5 }}%</span>
        </div>
    </div>
</div>

<div class="charts-section">
    <div class="chart-card">
        <h3>
            <i class="fas fa-chart-line"></i>
            تطور المبيعات (آخر 12 شهر)
        </h3>
        <div class="chart-container">
            <div class="chart-placeholder">
                <div style="text-align: center;">
                    <i class="fas fa-chart-line" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                    <p>مخطط تطور المبيعات الشهرية</p>
                    <small style="color: var(--gray-400);">قريباً - ميزة قيد التطوير</small>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-card">
        <h3>
            <i class="fas fa-chart-pie"></i>
            توزيع حالات الطلبات
        </h3>
        <div class="chart-container">
            <div class="chart-placeholder">
                <div style="text-align: center;">
                    <i class="fas fa-chart-pie" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                    <p>مخطط دائري لحالات الطلبات</p>
                    <small style="color: var(--gray-400);">قريباً - ميزة قيد التطوير</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="top-products">
    <h3>
        <i class="fas fa-star"></i>
        أفضل المنتجات مبيعاً
    </h3>
    
    @if(isset($topProducts) && $topProducts->count() > 0)
        @foreach($topProducts as $product)
        <div class="product-item">
            <div class="product-image">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @else
                    <i class="fas fa-image"></i>
                @endif
            </div>
            <div class="product-info">
                <h4>{{ $product->name }}</h4>
                <p>{{ $product->category ?? 'غير محدد' }}</p>
            </div>
            <div class="product-stats">
                <strong>{{ $product->order_items_count ?? 0 }}</strong><br>
                <span>عملية بيع</span>
            </div>
        </div>
        @endforeach
    @else
        <div class="product-item">
            <div class="product-image">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="product-info">
                <h4>لا توجد منتجات</h4>
                <p>لم يتم بيع أي منتجات بعد</p>
            </div>
        </div>
    @endif
</div>

<script>
// إحصائيات إضافية
const merchantStats = {
    joinDate: '{{ $merchantStats['join_date'] ? $merchantStats['join_date']->format('Y-m-d') : 'غير محدد' }}',
    lastOrderDate: '{{ $merchantStats['last_order_date'] ? $merchantStats['last_order_date']->format('Y-m-d') : 'لا توجد طلبات' }}',
    commissionRate: {{ $merchant->commission_rate ?? 5 }}
};

console.log('بيانات التاجر:', merchantStats);

// يمكن إضافة مخططات Chart.js هنا لاحقاً
// مثال:
// const ctx = document.getElementById('salesChart').getContext('2d');
// const salesChart = new Chart(ctx, { ... });
</script>
@endsection
