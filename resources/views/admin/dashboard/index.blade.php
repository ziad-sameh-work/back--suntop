@extends('layouts.admin')

@section('title', 'لوحة التحكم الرئيسية - SunTop')
@section('page-title', 'لوحة التحكم')

@push('styles')
<style>
    /* Creative Dashboard Styles */
    .dashboard-container {
        display: grid;
        gap: 30px;
        grid-template-columns: 1fr;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 50%, #4a90e2 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .welcome-section::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite reverse;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .welcome-content {
        position: relative;
        z-index: 2;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .welcome-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 20px;
    }

    .welcome-stats {
        display: flex;
        gap: 30px;
        margin-top: 20px;
    }

    .welcome-stat {
        text-align: center;
    }

    .welcome-stat-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }

    .welcome-stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* Stats Cards with Creative Design */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    @media (min-width: 1400px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .stat-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 
            0 10px 30px rgba(0, 0, 0, 0.1),
            0 1px 8px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #ff6b35, #4a90e2, #10b981, #8b5cf6);
        background-size: 300% 100%;
        animation: gradientShift 3s ease infinite;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .stat-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.15),
            0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        position: relative;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .stat-icon::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 22px;
        padding: 2px;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: exclude;
    }

    .stat-icon.orange { 
        background: linear-gradient(135deg, #ff6b35, #ff8c42, #ffa726);
        animation: pulse-orange 2s ease-in-out infinite alternate;
    }
    .stat-icon.blue { 
        background: linear-gradient(135deg, #4a90e2, #5ba3f5, #42a5f5);
        animation: pulse-blue 2s ease-in-out infinite alternate;
    }
    .stat-icon.green { 
        background: linear-gradient(135deg, #10b981, #34d399, #6ee7b7);
        animation: pulse-green 2s ease-in-out infinite alternate;
    }
    .stat-icon.purple { 
        background: linear-gradient(135deg, #8b5cf6, #a78bfa, #c4b5fd);
        animation: pulse-purple 2s ease-in-out infinite alternate;
    }
    .stat-icon.warning { 
        background: linear-gradient(135deg, #f59e0b, #fbbf24, #fcd34d);
        animation: pulse-warning 2s ease-in-out infinite alternate;
    }
    .stat-icon.success { 
        background: linear-gradient(135deg, #059669, #10b981, #34d399);
        animation: pulse-success 2s ease-in-out infinite alternate;
    }

    @keyframes pulse-orange {
        0% { box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(255, 107, 53, 0.5); }
    }
    @keyframes pulse-blue {
        0% { box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(74, 144, 226, 0.5); }
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(16, 185, 129, 0.5); }
    }
    @keyframes pulse-purple {
        0% { box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(139, 92, 246, 0.5); }
    }
    @keyframes pulse-warning {
        0% { box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(245, 158, 11, 0.5); }
    }
    @keyframes pulse-success {
        0% { box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(5, 150, 105, 0.5); }
    }

    .stat-title {
        font-size: 16px;
        color: #64748b;
        margin: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, #1e293b, #475569);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 15px 0;
        direction: ltr;
        text-align: right;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stat-change {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }

    .stat-change.positive { 
        color: #059669; 
        background: rgba(16, 185, 129, 0.1);
    }
    .stat-change.negative { 
        color: #dc2626; 
        background: rgba(239, 68, 68, 0.1);
    }
    .stat-change.warning { 
        color: #d97706; 
        background: rgba(245, 158, 11, 0.1);
    }
    .stat-change.info { 
        color: #2563eb; 
        background: rgba(59, 130, 246, 0.1);
    }

    /* Clickable Cards */
    .stat-card.clickable {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .stat-card.clickable:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    /* Charts Section */
    .charts-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }

    .chart-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        padding: 35px;
        box-shadow: 
            0 15px 35px rgba(0, 0, 0, 0.08),
            0 5px 15px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .chart-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff6b35, #4a90e2, #10b981);
        background-size: 200% 100%;
        animation: gradientMove 4s ease infinite;
    }

    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--gray-100);
    }

    .chart-title {
        font-size: 22px;
        font-weight: 700;
        background: linear-gradient(135deg, #1e293b, #475569);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-title i {
        color: #ff6b35;
        font-size: 20px;
    }

    .chart-period {
        font-size: 13px;
        color: #64748b;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.8);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .chart-container {
        position: relative;
        height: 320px;
        border-radius: 16px;
        overflow: hidden;
    }

    /* Tables */
    .data-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .data-card {
        background: var(--white);
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-100);
    }

    .data-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--gray-100);
    }

    .data-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-800);
    }

    .view-all-btn {
        color: var(--suntop-orange);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .view-all-btn:hover {
        color: var(--suntop-orange-dark);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 12px 8px;
        text-align: right;
        border-bottom: 1px solid var(--gray-100);
    }

    .data-table th {
        font-weight: 600;
        color: var(--gray-700);
        font-size: 14px;
    }

    .data-table td {
        color: var(--gray-600);
        font-size: 13px;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
    }

    .status-pending { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .status-completed { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: #DC2626; }

    /* Alerts */
    .alerts-section {
        margin-bottom: 30px;
    }

    .alert {
        background: var(--white);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-100);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .alert:hover {
        transform: translateX(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .alert-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: var(--white);
    }

    .alert-warning .alert-icon { background: var(--warning); }
    .alert-info .alert-icon { background: var(--suntop-blue); }
    .alert-danger .alert-icon { background: var(--danger); }

    .alert-content {
        flex: 1;
    }

    .alert-title {
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 4px;
    }

    .alert-message {
        color: var(--gray-600);
        font-size: 14px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .charts-section {
            grid-template-columns: 1fr;
        }
        
        .data-section {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-value {
            font-size: 24px;
        }
        
        .chart-container {
            height: 250px;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h1 class="welcome-title">مرحباً بك في SunTop</h1>
            <p class="welcome-subtitle">لوحة التحكم الذكية لإدارة متجرك الإلكتروني</p>
            <div class="welcome-stats">
                <div class="welcome-stat">
                    <span class="welcome-stat-number">{{ number_format($stats['total_users']) }}</span>
                    <span class="welcome-stat-label">مستخدم</span>
                </div>
                <div class="welcome-stat">
                    <span class="welcome-stat-number">{{ number_format($orderStats['total_orders']) }}</span>
                    <span class="welcome-stat-label">طلب</span>
                </div>
                <div class="welcome-stat">
                    <span class="welcome-stat-number">{{ number_format($stats['today_revenue']) }}</span>
                    <span class="welcome-stat-label">إيرادات اليوم</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Total Users -->
        <div class="stat-card clickable" onclick="window.location.href='{{ route('admin.users.index') }}'">
            <div class="stat-header">
                <div class="stat-icon orange">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="stat-title">إجمالي المستخدمين</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>+12.5% عن الشهر الماضي</span>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="stat-card clickable" onclick="window.location.href='{{ route('admin.orders.index') }}'">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="stat-title">إجمالي الطلبات</h3>
            </div>
            <div class="stat-value">{{ number_format($orderStats['total_orders']) }}</div>
            <div class="stat-change {{ $stats['orders_growth'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $stats['orders_growth'] >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ $stats['orders_growth'] >= 0 ? '+' : '' }}{{ $stats['orders_growth'] }}% عن الشهر الماضي</span>
            </div>
        </div>

        <!-- Revenue -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h3 class="stat-title">إيرادات هذا الشهر</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['this_month_revenue'], 2) }} ج.م</div>
            <div class="stat-change {{ $stats['revenue_growth'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $stats['revenue_growth'] >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ $stats['revenue_growth'] >= 0 ? '+' : '' }}{{ $stats['revenue_growth'] }}% عن الشهر الماضي</span>
            </div>
        </div>

        <!-- Products -->
        <div class="stat-card clickable" onclick="window.location.href='{{ route('admin.products.index') }}'">
            <div class="stat-header">
                <div class="stat-icon purple">
                    <i class="fas fa-box"></i>
                </div>
                <h3 class="stat-title">إجمالي المنتجات</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-change warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ $stats['low_stock_products'] }} منتج بمخزون منخفض</span>
            </div>
        </div>


        <!-- Offers -->
        <div class="stat-card clickable" onclick="window.location.href='{{ route('admin.offers.index') }}'">
            <div class="stat-header">
                <div class="stat-icon purple">
                    <i class="fas fa-gift"></i>
                </div>
                <h3 class="stat-title">إجمالي العروض</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_offers']) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-tags"></i>
                <span>عرض ترويجي</span>
            </div>
        </div>

        <!-- Loyalty Points Users -->
        <div class="stat-card clickable" onclick="window.location.href='{{ route('admin.loyalty.index') }}'">
            <div class="stat-header">
                <div class="stat-icon warning">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="stat-title">مستخدمي نقاط الولاء</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_loyalty_users']) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-users"></i>
                <span>مستخدم نشط</span>
            </div>
        </div>

        <!-- User Categories -->
        <div class="stat-card clickable" onclick="window.location.href='{{ route('admin.user-categories.index') }}'">
            <div class="stat-header">
                <div class="stat-icon info">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3 class="stat-title">فئات المستخدمين</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_user_categories']) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-tags"></i>
                <span>فئة</span>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="stat-card clickable" onclick="window.location.href='{{ route('admin.orders.index', ['status' => 'pending']) }}'">
            <div class="stat-header">
                <div class="stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="stat-title">طلبات معلقة</h3>
            </div>
            <div class="stat-value">{{ number_format($orderStats['pending_orders']) }}</div>
            <div class="stat-change info">
                <i class="fas fa-info-circle"></i>
                <span>تحتاج مراجعة</span>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon success">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3 class="stat-title">إيرادات اليوم</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['today_revenue'], 2) }} ج.م</div>
            <div class="stat-change positive">
                <i class="fas fa-check-circle"></i>
                <span>{{ $stats['today_orders'] }} طلب اليوم</span>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if(count($alerts) > 0)
    <div class="alerts-section">
        @foreach($alerts as $alert)
        <div class="alert alert-{{ $alert['type'] }}">
            <div class="alert-icon">
                <i class="{{ $alert['icon'] }}"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">{{ $alert['title'] }}</div>
                <div class="alert-message">{{ $alert['message'] }}</div>
            </div>
            @if(isset($alert['url']))
            <a href="{{ $alert['url'] }}" class="view-all-btn">عرض التفاصيل</a>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Charts Section -->
    <div class="charts-section">
        <!-- Revenue Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-line"></i>
                    إيرادات آخر 6 أشهر
                </h3>
                <span class="chart-period">الأشهر الماضية</span>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- User Categories Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    توزيع فئات المستخدمين
                </h3>
                <span class="chart-period">الوضع الحالي</span>
            </div>
            <div class="chart-container">
                <canvas id="categoriesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="data-section">
        <!-- Recent Orders -->
        <div class="data-card">
            <div class="data-header">
                <h3 class="data-title">أحدث الطلبات</h3>
                <a href="#" class="view-all-btn" onclick="alert('صفحة الطلبات قيد التطوير')">عرض الكل</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ number_format($order->total_amount, 2) }} ج.م</td>
                        <td>
                            <span class="status-badge status-{{ $order->status }}">
                                @switch($order->status)
                                    @case('pending') معلق @break
                                    @case('completed') مكتمل @break
                                    @case('cancelled') ملغي @break
                                    @default {{ $order->status }}
                                @endswitch
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--gray-500);">لا توجد طلبات حديثة</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Top Products -->
        <div class="data-card">
            <div class="data-header">
                <h3 class="data-title">أكثر المنتجات مبيعاً</h3>
                <a href="#" class="view-all-btn" onclick="alert('صفحة المنتجات قيد التطوير')">عرض الكل</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>المبيعات</th>
                        <th>السعر</th>
                        <th>المخزون</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->total_sold ?? 0 }}</td>
                        <td>{{ number_format($product->price, 2) }} ج.م</td>
                        <td>{{ $product->stock_quantity }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--gray-500);">لا توجد بيانات مبيعات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Chart.js Configuration
Chart.defaults.font.family = 'Cairo';
Chart.defaults.font.size = 12;

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($chartData['sales_data'] as $sale)
                '{{ date('F Y', mktime(0, 0, 0, $sale->month, 1, $sale->year)) }}',
            @endforeach
        ],
        datasets: [{
            label: 'الإيرادات (ج.م)',
            data: [
                @foreach($chartData['sales_data'] as $sale)
                    {{ $sale->total }},
                @endforeach
            ],
            borderColor: '#FF6B35',
            backgroundColor: 'rgba(255, 107, 53, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#FF6B35',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' ج.م';
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        elements: {
            point: {
                hoverBackgroundColor: '#FF6B35'
            }
        }
    }
});

// User Categories Chart
const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
const categoriesChart = new Chart(categoriesCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($chartData['user_categories'] as $category)
                '{{ $category->display_name }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($chartData['user_categories'] as $category)
                    {{ $category->users_count }},
                @endforeach
            ],
            backgroundColor: [
                '#FF6B35',
                '#4A90E2',
                '#10B981',
                '#8B5CF6',
                '#F59E0B'
            ],
            borderWidth: 0,
            cutout: '70%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Auto-refresh function
function refreshDashboardData() {
    fetch('{{ route("admin.dashboard.data") }}?type=stats')
        .then(response => response.json())
        .then(data => {
            // Update stats without full page reload
            console.log('Dashboard data refreshed', data);
        })
        .catch(error => {
            console.error('Error refreshing dashboard data:', error);
        });
}

// Real-time updates simulation
setInterval(() => {
    const elements = document.querySelectorAll('.stat-value');
    elements.forEach(el => {
        el.style.transform = 'scale(1.05)';
        setTimeout(() => {
            el.style.transform = 'scale(1)';
        }, 200);
    });
}, 30000); // Every 30 seconds
</script>
@endpush
