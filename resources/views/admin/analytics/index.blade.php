@extends('layouts.admin')

@section('title', 'التحليلات الشاملة')

@section('content')
<div class="analytics-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">
                    <div class="title-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    التحليلات الشاملة
                </h1>
                <p class="page-description">
                    مراقبة شاملة لأداء النظام والمبيعات والمستخدمين
                </p>
            </div>
            <div class="header-actions">
                <div class="time-range-selector">
                    <select name="time_range" class="form-control" onchange="updateTimeRange(this.value)">
                        <option value="7" {{ request('time_range') == '7' ? 'selected' : '' }}>آخر 7 أيام</option>
                        <option value="30" {{ request('time_range') == '30' || !request('time_range') ? 'selected' : '' }}>آخر 30 يوم</option>
                        <option value="90" {{ request('time_range') == '90' ? 'selected' : '' }}>آخر 3 شهور</option>
                        <option value="365" {{ request('time_range') == '365' ? 'selected' : '' }}>آخر سنة</option>
                    </select>
                </div>
                <a href="{{ route('admin.analytics.export', ['time_range' => request('time_range', 30)]) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i>
                    تصدير البيانات
                </a>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="overview-section">
        <h3 class="section-title">
            <div class="section-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            نظرة عامة
        </h3>

        <div class="stats-grid">
            <!-- Total Revenue -->
            <div class="stat-card revenue">
                <div class="stat-header">
                    <div class="stat-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="stat-title">إجمالي الإيرادات</h3>
                </div>
                <div class="stat-value">{{ number_format($overviewStats['total_revenue'], 2) }} ج.م</div>
                <div class="stat-change {{ $overviewStats['revenue_change'] >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas {{ $overviewStats['revenue_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    <span>{{ number_format(abs($overviewStats['revenue_change']), 1) }}%</span>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="stat-card orders">
                <div class="stat-header">
                    <div class="stat-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="stat-title">إجمالي الطلبات</h3>
                </div>
                <div class="stat-value">{{ number_format($overviewStats['total_orders']) }}</div>
                <div class="stat-change {{ $overviewStats['orders_change'] >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas {{ $overviewStats['orders_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    <span>{{ number_format(abs($overviewStats['orders_change']), 1) }}%</span>
                </div>
            </div>

            <!-- New Users -->
            <div class="stat-card users">
                <div class="stat-header">
                    <div class="stat-icon users">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="stat-title">مستخدمين جدد</h3>
                </div>
                <div class="stat-value">{{ number_format($overviewStats['new_users']) }}</div>
                <div class="stat-change {{ $overviewStats['users_change'] >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas {{ $overviewStats['users_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    <span>{{ number_format(abs($overviewStats['users_change']), 1) }}%</span>
                </div>
            </div>

            <!-- Average Order Value -->
            <div class="stat-card average">
                <div class="stat-header">
                    <div class="stat-icon average">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="stat-title">متوسط قيمة الطلب</h3>
                </div>
                <div class="stat-value">{{ number_format($overviewStats['avg_order_value'], 2) }} ج.م</div>
                <div class="stat-change">
                    <i class="fas fa-calculator"></i>
                    <span>حسابي</span>
                </div>
            </div>

            <!-- Conversion Rate -->
            <div class="stat-card conversion">
                <div class="stat-header">
                    <div class="stat-icon conversion">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <h3 class="stat-title">معدل التحويل</h3>
                </div>
                <div class="stat-value">{{ number_format($overviewStats['conversion_rate'], 1) }}%</div>
                <div class="stat-change">
                    <i class="fas fa-exchange-alt"></i>
                    <span>تحويل</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="charts-grid">
            <!-- Sales Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        إيرادات المبيعات
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Orders Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-shopping-bag"></i>
                        عدد الطلبات
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="ordersChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- New Users Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-users"></i>
                        المستخدمين الجدد
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="usersChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Revenue Trend Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-money-bill-trend-up"></i>
                        اتجاه الإيرادات
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="detailed-stats-section">
        <div class="stats-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="sales">المبيعات</button>
                <button class="tab-btn" data-tab="users">المستخدمين</button>
                <button class="tab-btn" data-tab="products">المنتجات</button>
                <button class="tab-btn" data-tab="loyalty">نقاط الولاء</button>
            </div>

            <!-- Sales Tab -->
            <div class="tab-content active" id="sales-tab">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي المبيعات</span>
                        <span class="stat-value success">{{ number_format($salesStats['total_sales'], 2) }} ج.م</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مبيعات معلقة</span>
                        <span class="stat-value warning">{{ number_format($salesStats['pending_sales'], 2) }} ج.م</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مبيعات فاشلة</span>
                        <span class="stat-value danger">{{ number_format($salesStats['failed_sales'], 2) }} ج.م</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مبيعات مسترجعة</span>
                        <span class="stat-value info">{{ number_format($salesStats['refunded_sales'], 2) }} ج.م</span>
                    </div>
                </div>

                <div class="orders-by-status">
                    <h4>توزيع الطلبات حسب الحالة</h4>
                    <div class="status-grid">
                        @foreach($salesStats['orders_by_status'] as $status => $count)
                        <div class="status-item {{ $status }}">
                            <div class="status-label">{{ ucfirst($status) }}</div>
                            <div class="status-count">{{ $count }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if(!empty($salesStats['payment_methods']))
                <div class="payment-methods">
                    <h4>طرق الدفع</h4>
                    <div class="payment-grid">
                        @foreach($salesStats['payment_methods'] as $method => $count)
                        <div class="payment-item">
                            <div class="payment-label">{{ $method ?? 'غير محدد' }}</div>
                            <div class="payment-count">{{ $count }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Users Tab -->
            <div class="tab-content" id="users-tab">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي المستخدمين</span>
                        <span class="stat-value primary">{{ number_format($userStats['total_users']) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مستخدمين جدد</span>
                        <span class="stat-value success">{{ number_format($userStats['new_users']) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مستخدمين نشطين</span>
                        <span class="stat-value info">{{ number_format($userStats['active_users']) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مستخدمين بطلبات</span>
                        <span class="stat-value warning">{{ number_format($userStats['users_with_orders']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Products Tab -->
            <div class="tab-content" id="products-tab">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي المنتجات</span>
                        <span class="stat-value primary">{{ number_format($productStats['total_products']) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">منتجات نشطة</span>
                        <span class="stat-value success">{{ number_format($productStats['active_products']) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">منتجات نفدت</span>
                        <span class="stat-value danger">{{ number_format($productStats['out_of_stock']) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مخزون منخفض</span>
                        <span class="stat-value warning">{{ number_format($productStats['low_stock']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Loyalty Tab -->
            <div class="tab-content" id="loyalty-tab">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">نقاط ممنوحة</span>
                        <span class="stat-value success">{{ number_format($loyaltyStats['total_points_awarded'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">نقاط مستبدلة</span>
                        <span class="stat-value info">{{ number_format($loyaltyStats['total_points_redeemed'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مستخدمين بنقاط</span>
                        <span class="stat-value primary">{{ number_format($loyaltyStats['active_users_with_points'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">متوسط النقاط</span>
                        <span class="stat-value warning">{{ number_format($loyaltyStats['avg_points_per_user'] ?? 0, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers Section -->
    <div class="top-performers-section">
        <h3 class="section-title">
            <div class="section-icon">
                <i class="fas fa-trophy"></i>
            </div>
            الأفضل أداءً
        </h3>

        <div class="performers-grid">
            <!-- Top Products -->
            <div class="performer-card">
                <div class="performer-header">
                    <h4>أفضل المنتجات</h4>
                    <i class="fas fa-box"></i>
                </div>
                <div class="performer-list">
                    @forelse($topPerformers['products'] as $product)
                    <div class="performer-item">
                        <div class="performer-info">
                            <div class="performer-name">{{ $product->name }}</div>
                            <div class="performer-detail">الكمية: {{ $product->order_items_sum_quantity ?? 0 }}</div>
                        </div>
                        <div class="performer-value">{{ number_format($product->order_items_sum_total_price ?? 0, 2) }} ج.م</div>
                    </div>
                    @empty
                    <div class="no-data">لا توجد بيانات</div>
                    @endforelse
                </div>
            </div>

            <!-- Top Categories -->
            <div class="performer-card">
                <div class="performer-header">
                    <h4>أفضل فئات المستخدمين</h4>
                    <i class="fas fa-tags"></i>
                </div>
                <div class="performer-list">
                    @forelse($topPerformers['categories'] as $category)
                    <div class="performer-item">
                        <div class="performer-info">
                            <div class="performer-name">{{ $category->display_name }}</div>
                            <div class="performer-detail">المستخدمين: {{ $category->users_count ?? 0 }}</div>
                        </div>
                        <div class="performer-value">{{ number_format($category->total_revenue ?? 0, 2) }} ج.م</div>
                    </div>
                    @empty
                    <div class="no-data">لا توجد بيانات</div>
                    @endforelse
                </div>
            </div>

            <!-- Top Merchants -->
            <div class="performer-card">
                <div class="performer-header">
                    <h4>أفضل التجار</h4>
                    <i class="fas fa-store"></i>
                </div>
                <div class="performer-list">
                    @forelse($topPerformers['merchants'] as $merchant)
                    <div class="performer-item">
                        <div class="performer-info">
                            <div class="performer-name">{{ $merchant->name }}</div>
                            <div class="performer-detail">الطلبات: {{ $merchant->orders_count ?? 0 }}</div>
                        </div>
                        <div class="performer-value">{{ number_format($merchant->orders_sum_total_amount ?? 0, 2) }} ج.م</div>
                    </div>
                    @empty
                    <div class="no-data">لا توجد بيانات</div>
                    @endforelse
                </div>
            </div>

            <!-- Top Users -->
            <div class="performer-card">
                <div class="performer-header">
                    <h4>أفضل العملاء</h4>
                    <i class="fas fa-crown"></i>
                </div>
                <div class="performer-list">
                    @forelse($topPerformers['users'] as $user)
                    <div class="performer-item">
                        <div class="performer-info">
                            <div class="performer-name">{{ $user->name }}</div>
                            <div class="performer-detail">الطلبات: {{ $user->orders_count ?? 0 }}</div>
                        </div>
                        <div class="performer-value">{{ number_format($user->orders_sum_total_amount ?? 0, 2) }} ج.م</div>
                    </div>
                    @empty
                    <div class="no-data">لا توجد بيانات</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart data from backend
const chartData = @json($chartData);

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeTabs();
});

function initializeCharts() {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: chartData.salesChart.labels,
            datasets: [{
                label: 'الإيرادات',
                data: chartData.salesChart.data,
                borderColor: '#ff6b35',
                backgroundColor: 'rgba(255, 107, 53, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
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
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Orders Chart
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ordersCtx, {
        type: 'bar',
        data: {
            labels: chartData.ordersChart.labels,
            datasets: [{
                label: 'الطلبات',
                data: chartData.ordersChart.data,
                backgroundColor: '#4dabf7',
                borderColor: '#339af0',
                borderWidth: 1
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
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Users Chart
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'line',
        data: {
            labels: chartData.usersChart.labels,
            datasets: [{
                label: 'مستخدمين جدد',
                data: chartData.usersChart.data,
                borderColor: '#51cf66',
                backgroundColor: 'rgba(81, 207, 102, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
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
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'area',
        data: {
            labels: chartData.revenueChart.labels,
            datasets: [{
                label: 'الإيرادات التراكمية',
                data: chartData.revenueChart.data,
                borderColor: '#845ef7',
                backgroundColor: 'rgba(132, 94, 247, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
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
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function initializeTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabName = btn.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            btn.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        });
    });
}

function updateTimeRange(range) {
    const url = new URL(window.location);
    url.searchParams.set('time_range', range);
    window.location.href = url.toString();
}
</script>
@endpush

@push('styles')
<style>
.analytics-container {
    padding: 0;
}

.page-header {
    background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    color: white;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.header-text h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.title-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.page-description {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.time-range-selector select {
    padding: 0.75rem 1rem;
    border-radius: 10px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-weight: 500;
}

.time-range-selector select option {
    color: #333;
}

.btn.btn-primary {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn.btn-primary:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

/* Overview Statistics */
.overview-section {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-icon {
    width: 40px;
    height: 40px;
    background: #ff6b35;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f3f4;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stat-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.stat-icon.revenue { background: linear-gradient(135deg, #28a745, #20c997); }
.stat-icon.orders { background: linear-gradient(135deg, #007bff, #4dabf7); }
.stat-icon.users { background: linear-gradient(135deg, #6f42c1, #845ef7); }
.stat-icon.average { background: linear-gradient(135deg, #fd7e14, #ff8c42); }
.stat-icon.conversion { background: linear-gradient(135deg, #e83e8c, #f06292); }

.stat-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
    margin: 0;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.stat-change {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
}

.stat-change.positive {
    color: #28a745;
}

.stat-change.negative {
    color: #dc3545;
}

/* Charts Section */
.charts-section {
    margin-bottom: 3rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.chart-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f3f4;
}

.chart-header {
    margin-bottom: 1.5rem;
}

.chart-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
}

.chart-container {
    height: 300px;
    position: relative;
}

/* Detailed Statistics */
.detailed-stats-section {
    margin-bottom: 3rem;
}

.stats-tabs {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.tab-buttons {
    display: flex;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.tab-btn {
    flex: 1;
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tab-btn.active {
    background: #ff6b35;
    color: white;
}

.tab-content {
    display: none;
    padding: 2rem;
}

.tab-content.active {
    display: block;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
}

.stat-value.success { color: #28a745; }
.stat-value.warning { color: #ffc107; }
.stat-value.danger { color: #dc3545; }
.stat-value.info { color: #17a2b8; }
.stat-value.primary { color: #007bff; }

.orders-by-status h4,
.payment-methods h4 {
    margin-bottom: 1rem;
    color: #2c3e50;
    font-weight: 600;
}

.status-grid,
.payment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.status-item,
.payment-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.status-item:hover,
.payment-item:hover {
    border-color: #ff6b35;
    transform: translateY(-2px);
}

.status-label,
.payment-label {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
    text-transform: capitalize;
}

.status-count,
.payment-count {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
}

/* Top Performers */
.top-performers-section {
    margin-bottom: 3rem;
}

.performers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.performer-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f3f4;
    overflow: hidden;
}

.performer-header {
    background: linear-gradient(135deg, #ff6b35, #ff8c42);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.performer-header h4 {
    margin: 0;
    font-weight: 600;
}

.performer-header i {
    font-size: 1.5rem;
    opacity: 0.8;
}

.performer-list {
    padding: 1rem;
}

.performer-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.3s ease;
}

.performer-item:last-child {
    border-bottom: none;
}

.performer-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.performer-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.performer-detail {
    font-size: 0.85rem;
    color: #6c757d;
}

.performer-value {
    font-weight: 700;
    color: #ff6b35;
}

.no-data {
    text-align: center;
    color: #6c757d;
    padding: 2rem;
    font-style: italic;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
    }

    .header-actions {
        width: 100%;
        justify-content: center;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .charts-grid {
        grid-template-columns: 1fr;
    }

    .performers-grid {
        grid-template-columns: 1fr;
    }

    .tab-buttons {
        flex-wrap: wrap;
    }

    .tab-btn {
        flex: none;
        min-width: 120px;
    }
}
</style>
@endpush
