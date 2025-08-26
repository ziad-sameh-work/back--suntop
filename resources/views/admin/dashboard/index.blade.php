@extends('layouts.admin')

@section('title', 'لوحة التحكم الرئيسية - SunTop')
@section('page-title', 'لوحة التحكم')

@push('styles')
<style>
    /* Dashboard Specific Styles */
    .dashboard-container {
        display: grid;
        gap: 25px;
        grid-template-columns: 1fr;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (min-width: 1400px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .stat-card {
        background: var(--white);
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-100);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--suntop-orange), var(--suntop-blue));
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: var(--white);
    }

    .stat-icon.orange { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); }
    .stat-icon.blue { background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark)); }
    .stat-icon.green { background: linear-gradient(135deg, var(--success), #0D9488); }
    .stat-icon.purple { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
    .stat-icon.warning { background: linear-gradient(135deg, var(--warning), #D97706); }
    .stat-icon.success { background: linear-gradient(135deg, var(--success), #059669); }

    .stat-title {
        font-size: 14px;
        color: var(--gray-500);
        margin: 0;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--gray-800);
        margin: 10px 0;
        direction: ltr;
        text-align: right;
    }

    .stat-change {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        font-weight: 500;
    }

    .stat-change.positive { color: var(--success); }
    .stat-change.negative { color: var(--danger); }
    .stat-change.warning { color: var(--warning); }
    .stat-change.info { color: var(--suntop-blue); }

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
        gap: 25px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: var(--white);
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-100);
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
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
    }

    .chart-period {
        font-size: 12px;
        color: var(--gray-500);
        background: var(--gray-50);
        padding: 6px 12px;
        border-radius: 20px;
    }

    .chart-container {
        position: relative;
        height: 300px;
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

        <!-- Merchants -->
        <div class="stat-card clickable" onclick="window.location.href='{{ route('admin.merchants.index') }}'">
            <div class="stat-header">
                <div class="stat-icon orange">
                    <i class="fas fa-store"></i>
                </div>
                <h3 class="stat-title">إجمالي التجار</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_merchants']) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-store"></i>
                <span>تاجر مسجل</span>
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
                <h3 class="chart-title">إيرادات آخر 6 أشهر</h3>
                <span class="chart-period">الأشهر الماضية</span>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- User Categories Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">توزيع فئات المستخدمين</h3>
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
