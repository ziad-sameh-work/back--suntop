@extends('layouts.admin')

@section('title', 'نقاط الولاء - ' . $user->name)

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
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 30px;
        align-items: start;
    }

    .user-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        color: var(--suntop-orange);
        font-size: 32px;
        font-weight: 700;
    }

    .user-info h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 5px 0;
    }

    .user-info p {
        margin: 0;
        opacity: 0.9;
        font-size: 16px;
    }

    .user-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        opacity: 0.9;
    }

    .header-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
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
        text-align: center;
        justify-content: center;
    }

    .btn-white:hover {
        background: transparent;
        color: var(--white);
        border-color: var(--white);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .content-card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 25px;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--gray-200);
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
    .card-icon.info { background: var(--info); }
    .card-icon.purple { background: #8b5cf6; }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
        background: var(--gray-50);
        border-radius: 10px;
        border: 1px solid var(--gray-200);
    }

    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: var(--suntop-orange);
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 12px;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .progress-item {
        margin-bottom: 20px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .progress-label {
        font-size: 14px;
        font-weight: 500;
        color: var(--gray-700);
    }

    .progress-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-900);
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--gray-200);
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--suntop-orange), #ff8c42);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert.warning {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: var(--warning);
    }

    .alert.info {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: var(--info);
    }

    .recent-transactions {
        max-height: 500px;
        overflow-y: auto;
    }

    .transaction-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        background: var(--gray-50);
        transition: all 0.3s ease;
    }

    .transaction-item:hover {
        background: var(--gray-100);
    }

    .transaction-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 16px;
    }

    .transaction-icon.earned { background: var(--success); }
    .transaction-icon.redeemed { background: var(--danger); }
    .transaction-icon.admin_award { background: var(--suntop-orange); }
    .transaction-icon.admin_deduct { background: var(--warning); }
    .transaction-icon.bonus { background: var(--info); }

    .transaction-info {
        flex: 1;
    }

    .transaction-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 4px 0;
    }

    .transaction-info p {
        font-size: 12px;
        color: var(--gray-600);
        margin: 0;
    }

    .transaction-points {
        font-size: 16px;
        font-weight: 700;
        text-align: left;
    }

    .transaction-points.positive {
        color: var(--success);
    }

    .transaction-points.negative {
        color: var(--danger);
    }

    .action-buttons {
        display: grid;
        gap: 10px;
        margin-bottom: 20px;
    }

    .action-btn {
        width: 100%;
        padding: 12px;
        border: 2px solid var(--gray-200);
        background: var(--white);
        color: var(--gray-700);
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }

    .action-btn:hover {
        border-color: var(--suntop-orange);
        color: var(--suntop-orange);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .action-btn.success:hover {
        border-color: var(--success);
        color: var(--success);
    }

    .action-btn.danger:hover {
        border-color: var(--danger);
        color: var(--danger);
    }

    .monthly-chart {
        height: 300px;
        background: var(--gray-100);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-500);
        font-size: 16px;
        border: 2px dashed var(--gray-300);
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .header-content {
            grid-template-columns: 1fr;
        }

        .header-actions {
            flex-direction: row;
            justify-content: center;
        }

        .user-header {
            flex-direction: column;
            text-align: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="page-header">
    <div class="header-content">
        <div>
            <div class="user-header">
                <div class="user-avatar">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="user-info">
                    <h1>{{ $user->name }}</h1>
                    <p>{{ $user->email }}</p>
                    <div class="user-meta">
                        <div class="meta-item">
                            <i class="fas fa-star"></i>
                            <span>{{ number_format($userStats['total_active_points']) }} نقطة نشطة</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>عضو منذ {{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-exchange-alt"></i>
                            <span>{{ number_format($userStats['total_transactions']) }} معاملة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="header-actions">
            <button class="btn-white" onclick="showAwardPointsModal()">
                <i class="fas fa-plus"></i> منح نقاط
            </button>
            <button class="btn-white" onclick="showDeductPointsModal()">
                <i class="fas fa-minus"></i> خصم نقاط
            </button>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn-white">
                <i class="fas fa-user"></i> ملف المستخدم
            </a>
            <a href="{{ route('admin.loyalty.index') }}" class="btn-white">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Main Content -->
    <div>
        <!-- User Statistics -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon orange">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="card-title">إحصائيات نقاط الولاء</h3>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($userStats['total_active_points']) }}</div>
                    <div class="stat-label">النقاط النشطة</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($userStats['lifetime_earned']) }}</div>
                    <div class="stat-label">إجمالي المكتسب</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($userStats['total_redeemed']) }}</div>
                    <div class="stat-label">إجمالي المسترد</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($userStats['avg_monthly_earned']) }}</div>
                    <div class="stat-label">متوسط شهري</div>
                </div>
            </div>
        </div>

        <!-- Progress Indicators -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon success">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="card-title">مؤشرات الأداء</h3>
            </div>
            
            @if($userStats['lifetime_earned'] > 0)
            <div class="progress-item">
                <div class="progress-header">
                    <span class="progress-label">معدل الاسترداد</span>
                    <span class="progress-value">{{ round(($userStats['total_redeemed'] / $userStats['lifetime_earned']) * 100, 1) }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min(100, ($userStats['total_redeemed'] / $userStats['lifetime_earned']) * 100) }}%"></div>
                </div>
            </div>
            @endif

            @if($userStats['expiring_soon'] > 0)
            <div class="alert warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ number_format($userStats['expiring_soon']) }} نقطة ستنتهي خلال 30 يوم</span>
            </div>
            @endif

            @if($userStats['last_activity'])
            <div class="alert info">
                <i class="fas fa-clock"></i>
                <span>آخر نشاط: {{ $userStats['last_activity']->diffForHumans() }}</span>
            </div>
            @endif
        </div>

        <!-- Monthly Chart -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon info">
                    <i class="fas fa-chart-area"></i>
                </div>
                <h3 class="card-title">النشاط الشهري</h3>
            </div>
            <div class="monthly-chart">
                <div style="text-align: center;">
                    <i class="fas fa-chart-area" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                    <p>مخطط النشاط الشهري لنقاط الولاء</p>
                    <small style="color: var(--gray-400);">قريباً - ميزة قيد التطوير</small>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon purple">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="card-title">المعاملات الأخيرة ({{ $recentTransactions->count() }})</h3>
            </div>
            <div class="recent-transactions">
                @forelse($recentTransactions as $transaction)
                <div class="transaction-item">
                    <div class="transaction-icon {{ $transaction->type }}">
                        @switch($transaction->type)
                            @case('earned')
                                <i class="fas fa-plus"></i>
                                @break
                            @case('redeemed')
                                <i class="fas fa-minus"></i>
                                @break
                            @case('admin_award')
                                <i class="fas fa-gift"></i>
                                @break
                            @case('admin_deduct')
                                <i class="fas fa-exclamation"></i>
                                @break
                            @case('bonus')
                                <i class="fas fa-star"></i>
                                @break
                            @default
                                <i class="fas fa-circle"></i>
                        @endswitch
                    </div>
                    <div class="transaction-info">
                        <h4>{{ $transaction->description }}</h4>
                        <p>
                            {{ $transaction->created_at->format('Y-m-d H:i') }}
                            @if($transaction->order)
                                • <a href="{{ route('admin.orders.show', $transaction->order->id) }}" 
                                     style="color: var(--suntop-orange);">طلب #{{ $transaction->order->order_number }}</a>
                            @endif
                            @if($transaction->expires_at)
                                • ينتهي {{ $transaction->expires_at->format('Y-m-d') }}
                            @endif
                        </p>
                    </div>
                    <div class="transaction-points {{ $transaction->points > 0 ? 'positive' : 'negative' }}">
                        {{ $transaction->formatted_points }}
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                    <i class="fas fa-history" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                    <p>لا توجد معاملات بعد</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Quick Actions -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon orange">
                    <i class="fas fa-tools"></i>
                </div>
                <h3 class="card-title">إجراءات سريعة</h3>
            </div>
            <div class="action-buttons">
                <button class="action-btn success" onclick="showAwardPointsModal()">
                    <i class="fas fa-plus"></i> منح نقاط
                </button>
                <button class="action-btn danger" onclick="showDeductPointsModal()">
                    <i class="fas fa-minus"></i> خصم نقاط
                </button>
                <a href="{{ route('admin.users.show', $user->id) }}" class="action-btn">
                    <i class="fas fa-user"></i> ملف المستخدم
                </a>
                <a href="{{ route('admin.orders.index', ['user_id' => $user->id]) }}" class="action-btn">
                    <i class="fas fa-shopping-cart"></i> طلبات المستخدم
                </a>
            </div>
        </div>

        <!-- User Summary -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon info">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3 class="card-title">ملخص المستخدم</h3>
            </div>
            <div style="space-y: 15px;">
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--gray-200);">
                    <span style="color: var(--gray-600);">اسم المستخدم</span>
                    <span style="font-weight: 600;">{{ $user->username ?? $user->name }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--gray-200);">
                    <span style="color: var(--gray-600);">الدور</span>
                    <span style="font-weight: 600;">{{ $user->role === 'customer' ? 'عميل' : $user->role }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--gray-200);">
                    <span style="color: var(--gray-600);">الحالة</span>
                    <span style="font-weight: 600; color: {{ $user->is_active ? 'var(--success)' : 'var(--danger)' }};">
                        {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--gray-200);">
                    <span style="color: var(--gray-600);">تاريخ الانضمام</span>
                    <span style="font-weight: 600;">{{ $user->created_at->format('Y-m-d') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                    <span style="color: var(--gray-600);">إجمالي الطلبات</span>
                    <span style="font-weight: 600;">{{ $user->total_orders_count ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Points Breakdown -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon warning">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="card-title">تفصيل النقاط</h3>
            </div>
            <div style="space-y: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                    <span style="color: var(--gray-600); font-size: 14px;">النقاط الحالية</span>
                    <span style="font-weight: 700; color: var(--suntop-orange); font-size: 16px;">
                        {{ number_format($userStats['total_active_points']) }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                    <span style="color: var(--gray-600); font-size: 14px;">مكتسب مدى الحياة</span>
                    <span style="font-weight: 600; color: var(--success); font-size: 14px;">
                        +{{ number_format($userStats['lifetime_earned']) }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                    <span style="color: var(--gray-600); font-size: 14px;">مسترد مدى الحياة</span>
                    <span style="font-weight: 600; color: var(--danger); font-size: 14px;">
                        -{{ number_format($userStats['total_redeemed']) }}
                    </span>
                </div>
                @if($userStats['expiring_soon'] > 0)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; color: var(--warning);">
                    <span style="font-size: 14px;">ستنتهي قريباً</span>
                    <span style="font-weight: 600; font-size: 14px;">
                        {{ number_format($userStats['expiring_soon']) }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals would go here -->
<script>
const userId = {{ $user->id }};

function showAwardPointsModal() {
    const points = prompt('عدد النقاط المراد منحها:');
    const description = prompt('وصف العملية:');
    const expiryDate = prompt('تاريخ الانتهاء (اختياري، بصيغة YYYY-MM-DD):');
    
    if (points && description) {
        awardPoints(points, description, expiryDate);
    }
}

function showDeductPointsModal() {
    const currentPoints = {{ $userStats['total_active_points'] }};
    const points = prompt(`عدد النقاط المراد خصمها (الحد الأقصى: ${currentPoints}):`);
    const description = prompt('وصف العملية:');
    
    if (points && description) {
        if (parseInt(points) > currentPoints) {
            alert('عدد النقاط المطلوب خصمها أكبر من النقاط المتاحة');
            return;
        }
        deductPoints(points, description);
    }
}

function awardPoints(points, description, expiryDate) {
    const data = {
        user_id: userId,
        points: parseInt(points),
        description: description
    };
    
    if (expiryDate) {
        data.expires_at = expiryDate;
    }
    
    fetch('/admin/loyalty/award-points', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
}

function deductPoints(points, description) {
    fetch('/admin/loyalty/deduct-points', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            points: parseInt(points),
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
}

// Statistics for analytics
const userStats = {
    totalActivePoints: {{ $userStats['total_active_points'] }},
    lifetimeEarned: {{ $userStats['lifetime_earned'] }},
    totalRedeemed: {{ $userStats['total_redeemed'] }},
    avgMonthlyEarned: {{ $userStats['avg_monthly_earned'] }},
    totalTransactions: {{ $userStats['total_transactions'] }}
};

console.log('بيانات نقاط المستخدم:', userStats);
</script>
@endsection
