@extends('layouts.admin')

@section('title', 'إدارة نقاط الولاء')

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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--white);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
        cursor: pointer;
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

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
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
        margin: 0;
    }

    .stat-change {
        font-size: 12px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--gray-500);
    }

    .top-users-section {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
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

    .top-users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .user-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: var(--gray-50);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .user-card:hover {
        background: var(--gray-100);
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: var(--suntop-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 600;
        font-size: 16px;
    }

    .user-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 4px 0;
    }

    .user-info p {
        font-size: 12px;
        color: var(--suntop-orange);
        margin: 0;
        font-weight: 600;
    }

    .filters-section {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 25px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 500;
        color: var(--gray-700);
    }

    .form-control {
        padding: 10px 12px;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--suntop-orange);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .filters-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 16px;
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

    .table-section {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .table-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .bulk-actions {
        display: none;
        align-items: center;
        gap: 15px;
        padding: 15px 25px;
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .bulk-actions.show {
        display: flex;
    }

    .selected-count {
        font-size: 14px;
        color: var(--gray-700);
        font-weight: 500;
    }

    .bulk-actions-dropdown {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .bulk-actions .btn-secondary {
        background: var(--white);
        border: 2px solid var(--suntop-orange);
        color: var(--suntop-orange);
        padding: 8px 14px;
        font-size: 13px;
    }

    .bulk-actions .btn-secondary:hover {
        background: var(--suntop-orange);
        color: var(--white);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 15px 12px;
        text-align: right;
        border-bottom: 1px solid var(--gray-100);
    }

    .data-table th {
        background: var(--gray-50);
        font-weight: 600;
        color: var(--gray-700);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table td {
        color: var(--gray-800);
        font-size: 14px;
    }

    .data-table tr:hover {
        background: var(--gray-50);
    }

    .user-info-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-info-cell .user-avatar {
        width: 40px;
        height: 40px;
        font-size: 14px;
    }

    .user-details h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 4px 0;
    }

    .user-details p {
        font-size: 12px;
        color: var(--gray-600);
        margin: 0;
    }

    .points-display {
        font-weight: 700;
        font-size: 16px;
    }

    .points-display.earned {
        color: var(--success);
    }

    .points-display.redeemed {
        color: var(--danger);
    }

    .points-display.admin_award {
        color: var(--suntop-orange);
    }

    .points-display.admin_deduct {
        color: var(--warning);
    }

    .type-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .type-badge.earned {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .type-badge.redeemed {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .type-badge.admin_award {
        background: rgba(255, 107, 53, 0.1);
        color: var(--suntop-orange);
    }

    .type-badge.admin_deduct {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .type-badge.expired {
        background: rgba(156, 163, 175, 0.2);
        color: var(--gray-600);
    }

    .type-badge.bonus {
        background: rgba(74, 144, 226, 0.1);
        color: var(--info);
    }

    .pagination-wrapper {
        padding: 20px 25px;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .custom-checkbox {
        position: relative;
        display: inline-block;
    }

    .custom-checkbox input[type="checkbox"] {
        opacity: 0;
        position: absolute;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .custom-checkbox .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 18px;
        width: 18px;
        background-color: var(--white);
        border: 2px solid var(--gray-300);
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .custom-checkbox:hover input ~ .checkmark {
        border-color: var(--suntop-orange);
    }

    .custom-checkbox input:checked ~ .checkmark {
        background-color: var(--suntop-orange);
        border-color: var(--suntop-orange);
    }

    .custom-checkbox .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        left: 5px;
        top: 2px;
        width: 6px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .custom-checkbox input:checked ~ .checkmark:after {
        display: block;
    }

    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .table-header {
            flex-direction: column;
            align-items: stretch;
        }

        .table-actions {
            justify-content: center;
        }

        .btn-secondary span {
            display: none;
        }

        .btn-secondary {
            padding: 8px 10px;
        }

        .data-table {
            font-size: 12px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 8px;
        }

        .top-users-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-star"></i>
        إدارة نقاط الولاء
    </h1>
    <p class="page-subtitle">إدارة نقاط العملاء وبرنامج الولاء</p>
</div>

<!-- Statistics Row -->
<div class="stats-row">
    <div class="stat-card orange">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <h3 class="stat-title">إجمالي المعاملات</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_transactions']) }}</div>
        <div class="stat-change">
            <i class="fas fa-list"></i>
            <span>معاملة</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-arrow-up"></i>
            </div>
            <h3 class="stat-title">النقاط الممنوحة</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_points_awarded']) }}</div>
        <div class="stat-change">
            <i class="fas fa-plus"></i>
            <span>نقطة</span>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-header">
            <div class="stat-icon danger">
                <i class="fas fa-arrow-down"></i>
            </div>
            <h3 class="stat-title">النقاط المستردة</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_points_redeemed']) }}</div>
        <div class="stat-change">
            <i class="fas fa-minus"></i>
            <span>نقطة</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="stat-title">المستخدمين النشطين</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['active_users']) }}</div>
        <div class="stat-change">
            <i class="fas fa-user"></i>
            <span>مستخدم</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="stat-title">متوسط النقاط</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['avg_points_per_user']) }}</div>
        <div class="stat-change">
            <i class="fas fa-calculator"></i>
            <span>للمستخدم</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-calendar-day"></i>
            </div>
            <h3 class="stat-title">معاملات اليوم</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['transactions_today']) }}</div>
        <div class="stat-change">
            <i class="fas fa-clock"></i>
            <span>اليوم</span>
        </div>
    </div>
</div>

<!-- Top Users Section -->
<div class="top-users-section">
    <h3 class="section-title">
        <div class="section-icon">
            <i class="fas fa-trophy"></i>
        </div>
        أفضل 10 عملاء بالنقاط
    </h3>
    
    <div class="top-users-grid">
        @forelse($topUsers as $index => $userInfo)
        <div class="user-card">
            <div class="user-avatar">
                {{ substr($userInfo['user']->name, 0, 1) }}
            </div>
            <div class="user-info">
                <h4>{{ $userInfo['user']->name }}</h4>
                <p>{{ number_format($userInfo['total_points']) }} نقطة</p>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 20px; color: var(--gray-500);">
            <p>لا توجد بيانات نقاط بعد</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Filters Section -->
<div class="filters-section">
    <h3 class="section-title">
        <div class="section-icon">
            <i class="fas fa-filter"></i>
        </div>
        البحث والتصفية
    </h3>
    
    <form method="GET" action="{{ route('admin.loyalty.index') }}">
        <div class="filters-grid">
            <div class="form-group">
                <label>البحث في المستخدمين</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="اسم المستخدم أو البريد الإلكتروني..." 
                       value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label>نوع المعاملة</label>
                <select name="type" class="form-control">
                    <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>جميع الأنواع</option>
                    <option value="earned" {{ request('type') === 'earned' ? 'selected' : '' }}>مكتسبة</option>
                    <option value="redeemed" {{ request('type') === 'redeemed' ? 'selected' : '' }}>مستردة</option>
                    <option value="admin_award" {{ request('type') === 'admin_award' ? 'selected' : '' }}>منحة إدارية</option>
                    <option value="admin_deduct" {{ request('type') === 'admin_deduct' ? 'selected' : '' }}>خصم إداري</option>
                    <option value="bonus" {{ request('type') === 'bonus' ? 'selected' : '' }}>مكافأة</option>
                    <option value="expired" {{ request('type') === 'expired' ? 'selected' : '' }}>منتهية</option>
                </select>
            </div>

            <div class="form-group">
                <label>النقاط من</label>
                <input type="number" name="points_from" class="form-control" 
                       placeholder="0" value="{{ request('points_from') }}">
            </div>

            <div class="form-group">
                <label>النقاط إلى</label>
                <input type="number" name="points_to" class="form-control" 
                       placeholder="1000" value="{{ request('points_to') }}">
            </div>

            <div class="form-group">
                <label>من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="form-group">
                <label>إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
        </div>

        <div class="filters-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                بحث
            </button>
            <a href="{{ route('admin.loyalty.index') }}" class="btn btn-secondary">
                <i class="fas fa-undo"></i>
                إعادة تعيين
            </a>
        </div>
    </form>
</div>

<!-- Table Section -->
<div class="table-section">
    <div class="table-header">
        <h3 class="table-title">
            <i class="fas fa-list"></i>
            معاملات نقاط الولاء ({{ $transactions->total() }})
        </h3>
        <div class="table-actions">
            <button class="btn-secondary" onclick="toggleBulkActions()">
                <i class="fas fa-tasks"></i>
                <span>إجراءات جماعية</span>
            </button>
            <a href="{{ route('admin.loyalty.settings') }}" class="btn-secondary">
                <i class="fas fa-cog"></i>
                <span>الإعدادات</span>
            </a>
            <a href="{{ route('admin.loyalty.analytics') }}" class="btn btn-primary">
                <i class="fas fa-chart-bar"></i>
                التحليلات
            </a>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <label class="custom-checkbox">
            <input type="checkbox" id="selectAllTransactions" onchange="toggleAllTransactions()">
            <span class="checkmark"></span>
        </label>
        <span class="selected-count" id="selectedCount">0 معاملة محددة</span>
        <div class="bulk-actions-dropdown">
            <button class="btn-secondary" onclick="showAwardPointsModal()">
                <i class="fas fa-plus"></i> منح نقاط
            </button>
            <button class="btn-secondary" onclick="showDeductPointsModal()">
                <i class="fas fa-minus"></i> خصم نقاط
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>
                        <label class="custom-checkbox">
                            <input type="checkbox" onchange="toggleAllTransactions()">
                            <span class="checkmark"></span>
                        </label>
                    </th>
                    <th>المستخدم</th>
                    <th>النقاط</th>
                    <th>النوع</th>
                    <th>الوصف</th>
                    <th>الطلب</th>
                    <th>تاريخ الانتهاء</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td>
                        <label class="custom-checkbox">
                            <input type="checkbox" class="transaction-checkbox" value="{{ $transaction->user_id }}" onchange="updateSelectedCount()">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>
                        <div class="user-info-cell">
                            <div class="user-avatar">
                                {{ substr($transaction->user->name, 0, 1) }}
                            </div>
                            <div class="user-details">
                                <h4>{{ $transaction->user->name }}</h4>
                                <p>{{ $transaction->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="points-display {{ $transaction->type }}">
                            {{ $transaction->formatted_points }}
                        </span>
                    </td>
                    <td>
                        <span class="type-badge {{ $transaction->type }}">
                            @switch($transaction->type)
                                @case('earned') مكتسبة @break
                                @case('redeemed') مستردة @break
                                @case('admin_award') منحة إدارية @break
                                @case('admin_deduct') خصم إداري @break
                                @case('expired') منتهية @break
                                @case('bonus') مكافأة @break
                                @default {{ $transaction->type }}
                            @endswitch
                        </span>
                    </td>
                    <td>{{ Str::limit($transaction->description, 50) }}</td>
                    <td>
                        @if($transaction->order)
                            <a href="{{ route('admin.orders.show', $transaction->order->id) }}" 
                               style="color: var(--suntop-orange); text-decoration: none;">
                                #{{ $transaction->order->order_number }}
                            </a>
                        @else
                            <span style="color: var(--gray-400);">-</span>
                        @endif
                    </td>
                    <td>
                        @if($transaction->expires_at)
                            <span style="color: {{ $transaction->is_expired ? 'var(--danger)' : 'var(--gray-600)' }};">
                                {{ $transaction->expires_at->format('Y-m-d') }}
                            </span>
                        @else
                            <span style="color: var(--gray-400);">لا تنتهي</span>
                        @endif
                    </td>
                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: var(--gray-500);">
                        <i class="fas fa-star" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                        <p>لا توجد معاملات نقاط</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
    <div class="pagination-wrapper">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

<script>
// Bulk actions functionality
let selectedTransactions = [];

function toggleBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    bulkActions.classList.toggle('show');
}

function toggleAllTransactions() {
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    const selectAll = document.getElementById('selectAllTransactions');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    selectedTransactions = Array.from(document.querySelectorAll('.transaction-checkbox:checked')).map(cb => cb.value);
    document.getElementById('selectedCount').textContent = `${selectedTransactions.length} معاملة محددة`;
    
    const selectAll = document.getElementById('selectAllTransactions');
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    selectAll.checked = selectedTransactions.length === checkboxes.length;
}

// Modal functions (simplified - you'd implement proper modals)
function showAwardPointsModal() {
    if (selectedTransactions.length === 0) {
        alert('يرجى تحديد مستخدمين أولاً');
        return;
    }
    
    const points = prompt('عدد النقاط المراد منحها:');
    const description = prompt('وصف العملية:');
    
    if (points && description) {
        awardPointsBulk(selectedTransactions, points, description);
    }
}

function showDeductPointsModal() {
    if (selectedTransactions.length === 0) {
        alert('يرجى تحديد مستخدمين أولاً');
        return;
    }
    
    const points = prompt('عدد النقاط المراد خصمها:');
    const description = prompt('وصف العملية:');
    
    if (points && description) {
        deductPointsBulk(selectedTransactions, points, description);
    }
}

function awardPointsBulk(userIds, points, description) {
    fetch('/admin/loyalty/bulk-action', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'award',
            user_ids: userIds,
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

function deductPointsBulk(userIds, points, description) {
    fetch('/admin/loyalty/bulk-action', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'deduct',
            user_ids: userIds,
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
</script>
@endsection
