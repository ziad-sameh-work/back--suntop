@extends('layouts.admin')

@section('title', 'تفاصيل فئة: ' . $userCategory->display_name)

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
        position: relative;
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

    .breadcrumb {
        background: none;
        padding: 0;
        margin: 0 0 15px 0;
    }

    .breadcrumb-item {
        display: inline-flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.8);
        font-size: 14px;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        margin: 0 10px;
        color: rgba(255, 255, 255, 0.6);
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: var(--white);
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: var(--white);
    }

    .header-actions {
        position: absolute;
        top: 30px;
        left: 30px;
        display: flex;
        gap: 10px;
    }

    .btn-white {
        background: var(--white);
        color: var(--suntop-orange);
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
        border: none;
        cursor: pointer;
    }

    .btn-white:hover {
        background: var(--gray-100);
        color: var(--suntop-orange);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 18px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        margin-left: 15px;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 16px;
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
        font-size: 24px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 8px 0;
    }

    .stat-change {
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--gray-500);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .content-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 14px;
    }

    .card-icon.orange { background: var(--suntop-orange); }
    .card-icon.success { background: var(--success); }
    .card-icon.warning { background: var(--warning); }
    .card-icon.info { background: var(--info); }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .card-body {
        padding: 25px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
    }

    .info-value.success { color: var(--success); }
    .info-value.warning { color: var(--warning); }
    .info-value.danger { color: var(--danger); }

    .benefits-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        background: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
    }

    .benefit-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--success);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .users-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 400px;
        overflow-y: auto;
    }

    .user-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
    }

    .user-item:hover {
        background: var(--gray-100);
        border-color: var(--gray-300);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--suntop-orange);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .user-details {
        flex: 1;
    }

    .user-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 4px 0;
    }

    .user-info {
        font-size: 12px;
        color: var(--gray-500);
        margin: 0;
    }

    .user-stats {
        text-align: left;
        font-size: 12px;
        color: var(--gray-600);
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-badge.inactive {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--gray-200);
        border-radius: 4px;
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background: var(--suntop-orange);
        transition: width 0.3s ease;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray-500);
    }

    .empty-icon {
        font-size: 48px;
        opacity: 0.3;
        margin-bottom: 15px;
        display: block;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .header-actions {
            position: static;
            margin-top: 20px;
        }
    }

    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <nav class="breadcrumb">
        <div class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fas fa-home"></i>
                الرئيسية
            </a>
        </div>
        <div class="breadcrumb-item">
            <a href="{{ route('admin.user-categories.index') }}">فئات المستخدمين</a>
        </div>
        <div class="breadcrumb-item active">{{ $userCategory->display_name }}</div>
    </nav>
    
    <div class="header-actions">
        <a href="{{ route('admin.user-categories.edit', $userCategory->id) }}" class="btn-white">
            <i class="fas fa-edit"></i>
            تعديل
        </a>
        <a href="{{ route('admin.user-categories.index') }}" class="btn-white">
            <i class="fas fa-arrow-right"></i>
            العودة
        </a>
    </div>
    
    <h1 class="page-title">
        <i class="fas fa-layer-group"></i>
        تفاصيل الفئة
        <span class="category-badge">{{ $userCategory->name }}</span>
    </h1>
    <p class="page-subtitle">{{ $userCategory->display_name }}</p>
</div>

<!-- Statistics Row -->
<div class="stats-row">
    <div class="stat-card orange">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="stat-title">إجمالي المستخدمين</h3>
        </div>
        <div class="stat-value">{{ number_format($categoryStats['total_users']) }}</div>
        <div class="stat-change">
            <i class="fas fa-user-tag"></i>
            <span>مستخدم</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="stat-title">إجمالي المشتريات</h3>
        </div>
        <div class="stat-value">{{ number_format($categoryStats['total_purchase_amount'], 2) }}</div>
        <div class="stat-change">
            <i class="fas fa-money-bill"></i>
            <span>ج.م</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-calculator"></i>
            </div>
            <h3 class="stat-title">متوسط المشتريات</h3>
        </div>
        <div class="stat-value">{{ number_format($categoryStats['average_purchase_amount'], 2) }}</div>
        <div class="stat-change">
            <i class="fas fa-chart-line"></i>
            <span>ج.م لكل عميل</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-boxes"></i>
            </div>
            <h3 class="stat-title">إجمالي الطلبات</h3>
        </div>
        <div class="stat-value">{{ number_format($categoryStats['total_orders']) }}</div>
        <div class="stat-change">
            <i class="fas fa-shopping-bag"></i>
            <span>طلب</span>
        </div>
    </div>

    <div class="stat-card purple">
        <div class="stat-header">
            <div class="stat-icon purple">
                <i class="fas fa-percentage"></i>
            </div>
            <h3 class="stat-title">معدل الاختراق</h3>
        </div>
        <div class="stat-value">{{ number_format($categoryStats['category_penetration'], 1) }}%</div>
        <div class="stat-change">
            <i class="fas fa-chart-pie"></i>
            <span>من إجمالي العملاء</span>
        </div>
    </div>

    <div class="stat-card {{ $categoryStats['monthly_growth'] >= 0 ? 'success' : 'danger' }}">
        <div class="stat-header">
            <div class="stat-icon {{ $categoryStats['monthly_growth'] >= 0 ? 'success' : 'danger' }}">
                <i class="fas fa-trending-{{ $categoryStats['monthly_growth'] >= 0 ? 'up' : 'down' }}"></i>
            </div>
            <h3 class="stat-title">النمو الشهري</h3>
        </div>
        <div class="stat-value">{{ number_format($categoryStats['monthly_growth'], 1) }}%</div>
        <div class="stat-change">
            <i class="fas fa-calendar"></i>
            <span>{{ $categoryStats['monthly_growth'] >= 0 ? 'نمو' : 'انخفاض' }}</span>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Main Content -->
    <div>
        <!-- Category Information -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon orange">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3 class="card-title">معلومات الفئة</h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">رمز الفئة</div>
                        <div class="info-value">{{ $userCategory->name }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة</div>
                        <div class="info-value {{ $userCategory->is_active ? 'success' : 'danger' }}">
                            {{ $userCategory->is_active ? 'نشطة' : 'غير نشطة' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحد الأدنى للشراء</div>
                        <div class="info-value">{{ number_format($userCategory->min_purchase_amount, 2) }} ج.م</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحد الأقصى للشراء</div>
                        <div class="info-value">
                            @if($userCategory->max_purchase_amount)
                                {{ number_format($userCategory->max_purchase_amount, 2) }} ج.م
                            @else
                                <span style="color: var(--info);">لا يوجد حد أقصى</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">نسبة الخصم</div>
                        <div class="info-value warning">{{ number_format($userCategory->discount_percentage, 1) }}%</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">ترتيب الفئة</div>
                        <div class="info-value">{{ $userCategory->sort_order }}</div>
                    </div>

                    @if($userCategory->display_name_en)
                    <div class="info-item">
                        <div class="info-label">الاسم بالإنجليزية</div>
                        <div class="info-value">{{ $userCategory->display_name_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">تاريخ الإنشاء</div>
                        <div class="info-value">{{ $userCategory->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>

                @if($userCategory->description)
                <div style="margin-top: 20px;">
                    <div class="info-label">وصف الفئة</div>
                    <p style="color: var(--gray-700); margin: 8px 0 0 0; line-height: 1.6;">
                        {{ $userCategory->description }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Category Benefits -->
        @if($userCategory->benefits && count($userCategory->benefits) > 0)
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon success">
                    <i class="fas fa-gift"></i>
                </div>
                <h3 class="card-title">مزايا الفئة ({{ count($userCategory->benefits) }})</h3>
            </div>
            <div class="card-body">
                <div class="benefits-list">
                    @foreach($userCategory->benefits as $benefit)
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>{{ $benefit }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Users -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="card-title">أحدث المستخدمين ({{ $recentUsers->count() }})</h3>
            </div>
            <div class="card-body">
                @if($recentUsers->count() > 0)
                    <div class="users-list">
                        @foreach($recentUsers as $user)
                            <div class="user-item">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-info">
                                        {{ $user->email }} • 
                                        انضم في {{ $user->category_updated_at ? $user->category_updated_at->format('d/m/Y') : 'غير محدد' }}
                                    </div>
                                </div>
                                <div class="user-stats">
                                    <div>{{ number_format($user->total_purchase_amount, 2) }} ج.م</div>
                                    <div>{{ $user->total_orders_count }} طلب</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users empty-icon"></i>
                        <p>لا يوجد مستخدمين في هذه الفئة حالياً</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Quick Stats -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon warning">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="card-title">إحصائيات سريعة</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="info-item">
                        <div class="info-label">المستخدمين الجدد (30 يوم)</div>
                        <div class="info-value success">{{ number_format($categoryStats['recent_upgrades']) }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">متطلبات الكراتين</div>
                        <div class="info-value">
                            @if($userCategory->min_cartons > 0)
                                <div style="margin-bottom: 5px;">
                                    <i class="fas fa-boxes" style="color: #FF6B35; margin-left: 5px;"></i>
                                    {{ $userCategory->min_cartons }}
                                    @if($userCategory->max_cartons)
                                        - {{ $userCategory->max_cartons }}
                                    @else
                                        +
                                    @endif
                                    كرتون
                                </div>
                                
                                @if($userCategory->requires_carton_purchase)
                                    <div style="font-size: 11px; color: var(--danger); margin-top: 5px;">
                                        <i class="fas fa-info-circle" style="margin-left: 3px;"></i>
                                        يجب شراء كراتين كاملة فقط
                                    </div>
                                @endif
                            @else
                                <span style="color: var(--gray-500);">لا توجد متطلبات</span>
                            @endif
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min($categoryStats['category_penetration'], 100) }}%;"></div>
                        </div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-top: 5px;">
                            {{ number_format($categoryStats['category_penetration'], 1) }}% من إجمالي العملاء
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">نقاط الولاء</div>
                        <div class="info-value">
                            <div style="color: #FFD700; margin-bottom: 5px;">
                                <i class="fas fa-star" style="margin-left: 5px;"></i>
                                {{ $userCategory->carton_loyalty_points ?? 10 }} نقطة لكل كرتون
                            </div>
                            
                            @if(($userCategory->bonus_points_per_carton ?? 0) > 0)
                                <div style="color: #FF6B35; font-size: 12px; margin-bottom: 3px;">
                                    <i class="fas fa-plus" style="margin-left: 3px;"></i>
                                    {{ $userCategory->bonus_points_per_carton }} نقطة إضافية لكل كرتون
                                </div>
                            @endif
                            
                            @if(($userCategory->monthly_bonus_points ?? 0) > 0)
                                <div style="color: #4CAF50; font-size: 12px; margin-bottom: 3px;">
                                    <i class="fas fa-gift" style="margin-left: 3px;"></i>
                                    {{ $userCategory->monthly_bonus_points }} نقطة شهرية
                                </div>
                            @endif
                            
                            @if(($userCategory->signup_bonus_points ?? 0) > 0)
                                <div style="color: #2196F3; font-size: 12px; margin-bottom: 3px;">
                                    <i class="fas fa-user-plus" style="margin-left: 3px;"></i>
                                    {{ $userCategory->signup_bonus_points }} نقطة عند الانضمام
                                </div>
                            @endif
                            
                            @if(($userCategory->has_points_multiplier ?? false) && ($userCategory->points_multiplier ?? 1) > 1)
                                <div style="color: #9C27B0; font-size: 12px;">
                                    <i class="fas fa-times" style="margin-left: 3px;"></i>
                                    مضاعف النقاط: {{ $userCategory->points_multiplier }}x
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">حالة الفئة</div>
                        <span class="status-badge {{ $userCategory->is_active ? 'active' : 'inactive' }}">
                            {{ $userCategory->is_active ? 'نشطة' : 'غير نشطة' }}
                        </span>
                    </div>

                    @if(isset($categoryStats['total_cartons']) && $categoryStats['total_cartons'] > 0)
                    <div class="info-item">
                        <div class="info-label">إجمالي الكراتين المشتراة</div>
                        <div class="info-value" style="color: #FF6B35;">
                            <i class="fas fa-boxes" style="margin-left: 5px;"></i>
                            {{ number_format($categoryStats['total_cartons']) }}
                        </div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-top: 5px;">
                            متوسط {{ number_format($categoryStats['average_cartons'], 1) }} كرتون لكل مستخدم
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Category Performance -->
        @if(isset($distribution['by_activity']))
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon info">
                    <i class="fas fa-activity"></i>
                </div>
                <h3 class="card-title">نشاط المستخدمين</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="info-item">
                        <div class="info-label">نشطين آخر 7 أيام</div>
                        <div class="info-value success">{{ number_format($distribution['by_activity']['active_last_7_days']) }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">نشطين آخر 30 يوم</div>
                        <div class="info-value warning">{{ number_format($distribution['by_activity']['active_last_30_days']) }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">لم يسجلوا الدخول مطلقاً</div>
                        <div class="info-value danger">{{ number_format($distribution['by_activity']['never_logged_in']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
