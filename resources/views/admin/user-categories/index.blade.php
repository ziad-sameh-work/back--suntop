@extends('layouts.admin')

@section('title', 'إدارة فئات المستخدمين')

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
    .stat-card.purple { border-right: 4px solid var(--purple); }

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

    .filters-section {
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

    .discount-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-toggle {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .status-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .status-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--gray-300);
        transition: .4s;
        border-radius: 24px;
    }

    .status-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        right: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .status-slider {
        background-color: var(--suntop-orange);
    }

    input:checked + .status-slider:before {
        transform: translateX(-26px);
    }

    .actions-dropdown {
        position: relative;
        display: inline-block;
    }

    .actions-btn {
        background: var(--gray-100);
        border: 1px solid var(--gray-200);
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        color: var(--gray-700);
    }

    .actions-btn:hover {
        background: var(--gray-200);
    }

    .actions-menu {
        display: none;
        position: absolute;
        left: 0;
        top: 100%;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        min-width: 150px;
        margin-top: 5px;
    }

    .actions-menu.show {
        display: block;
    }

    .actions-menu a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        color: var(--gray-700);
        text-decoration: none;
        font-size: 13px;
        border-bottom: 1px solid var(--gray-100);
    }

    .actions-menu a:last-child {
        border-bottom: none;
    }

    .actions-menu a:hover {
        background: var(--gray-50);
        color: var(--suntop-orange);
    }

    .actions-menu a.danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
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

    .purchase-range {
        font-size: 13px;
        color: var(--gray-600);
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .range-min {
        color: var(--success);
        font-weight: 600;
    }

    .range-max {
        color: var(--danger);
        font-weight: 600;
    }

    .benefits-list {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        max-width: 200px;
    }

    .benefit-item {
        padding: 2px 6px;
        background: var(--gray-100);
        border-radius: 4px;
        font-size: 11px;
        color: var(--gray-600);
    }

    /* Carton and Package Requirements Styles */
    .carton-package-requirements {
        max-width: 200px;
    }

    .requirement-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        margin: 2px;
        white-space: nowrap;
    }

    .requirement-badge.carton {
        background: linear-gradient(135deg, #FF6B35 0%, #FF8A65 100%);
        color: white;
    }

    .requirement-badge.package {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        color: white;
    }

    .required-badge {
        display: inline-block;
        font-size: 10px;
        background: var(--danger);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        margin: 2px;
    }

    /* Discount Rates Styles */
    .discount-rates {
        display: flex;
        flex-direction: column;
        gap: 4px;
        max-width: 140px;
    }

    .discount-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 6px;
        border-left: 3px solid;
    }

    .discount-item.carton {
        background: rgba(255, 107, 53, 0.1);
        border-left-color: #FF6B35;
        color: #FF6B35;
    }

    .discount-item.package {
        background: rgba(76, 175, 80, 0.1);
        border-left-color: #4CAF50;
        color: #4CAF50;
    }

    .discount-item.unit {
        background: rgba(33, 150, 243, 0.1);
        border-left-color: #2196F3;
        color: #2196F3;
    }

    .discount-item span {
        font-weight: 700;
        font-size: 13px;
    }

    .discount-item small {
        font-size: 10px;
        opacity: 0.8;
        font-weight: 500;
    }

    .discount-item i {
        width: 12px;
        text-align: center;
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
    }

    /* Loyalty Points Styles */
    .loyalty-points-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .points-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        background-color: rgba(255, 215, 0, 0.1);
        color: #B8860B;
        border: 1px solid rgba(255, 215, 0, 0.2);
    }

    .bonus-points,
    .multiplier {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-weight: 500;
    }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-layer-group"></i>
        إدارة فئات المستخدمين
    </h1>
    <p class="page-subtitle">إدارة فئات العملاء ونظام الخصومات المتدرج</p>
</div>

<!-- Statistics Row -->
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
            <i class="fas fa-list"></i>
            <span>فئة</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="stat-title">الفئات النشطة</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['active_categories']) }}</div>
        <div class="stat-change">
            <i class="fas fa-toggle-on"></i>
            <span>مفعلة</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="stat-title">المستخدمين المصنفين</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_users_with_categories']) }}</div>
        <div class="stat-change">
            <i class="fas fa-user-tag"></i>
            <span>مستخدم</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-user-times"></i>
            </div>
            <h3 class="stat-title">بدون تصنيف</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_users_without_categories']) }}</div>
        <div class="stat-change">
            <i class="fas fa-exclamation-triangle"></i>
            <span>مستخدم</span>
        </div>
    </div>

    <div class="stat-card purple">
        <div class="stat-header">
            <div class="stat-icon purple">
                <i class="fas fa-star"></i>
            </div>
            <h3 class="stat-title">متوسط النقاط</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['avg_loyalty_points'] ?? 10, 0) }}</div>
        <div class="stat-change">
            <i class="fas fa-award"></i>
            <span>نقطة/كرتون</span>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-header">
            <div class="stat-icon danger">
                <i class="fas fa-gift"></i>
            </div>
            <h3 class="stat-title">فئات بمزايا</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['categories_with_benefits']) }}</div>
        <div class="stat-change">
            <i class="fas fa-star"></i>
            <span>مميزة</span>
        </div>
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
    
    <form method="GET" action="{{ route('admin.user-categories.index') }}">
        <div class="filters-grid">
            <div class="form-group">
                <label>البحث في الفئات</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="اسم الفئة أو الوصف..." 
                       value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label>حالة الفئة</label>
                <select name="status" class="form-control">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>جميع الحالات</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشطة</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشطة</option>
                </select>
            </div>

            <div class="form-group">
                <label>الحد الأدنى للشراء من</label>
                <input type="number" name="min_amount" class="form-control" 
                       placeholder="0" value="{{ request('min_amount') }}" step="0.01">
            </div>

            <div class="form-group">
                <label>الحد الأدنى للشراء إلى</label>
                <input type="number" name="max_amount" class="form-control" 
                       placeholder="10000" value="{{ request('max_amount') }}" step="0.01">
            </div>
        </div>

        <div class="filters-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                بحث
            </button>
            <a href="{{ route('admin.user-categories.index') }}" class="btn btn-secondary">
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
            فئات المستخدمين ({{ $categories->total() }})
        </h3>
        <div class="table-actions">
            <button class="btn-secondary" onclick="toggleBulkActions()">
                <i class="fas fa-tasks"></i>
                <span>إجراءات جماعية</span>
            </button>
            <button class="btn-secondary" onclick="recalculateCategories()">
                <i class="fas fa-sync-alt"></i>
                <span>إعادة حساب الفئات</span>
            </button>
            <a href="{{ route('admin.user-categories.analytics') }}" class="btn-secondary">
                <i class="fas fa-chart-bar"></i>
                <span>التحليلات</span>
            </a>
            <a href="{{ route('admin.user-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                إضافة فئة جديدة
            </a>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <label class="custom-checkbox">
            <input type="checkbox" id="selectAllCategories" onchange="toggleAllCategories()">
            <span class="checkmark"></span>
        </label>
        <span class="selected-count" id="selectedCount">0 فئة محددة</span>
        <div class="bulk-actions-dropdown">
            <button class="btn-secondary" onclick="bulkAction('activate')">
                <i class="fas fa-toggle-on"></i> تفعيل
            </button>
            <button class="btn-secondary" onclick="bulkAction('deactivate')">
                <i class="fas fa-toggle-off"></i> إلغاء تفعيل
            </button>
            <button class="btn-secondary" onclick="bulkAction('delete')" style="color: var(--danger);">
                <i class="fas fa-trash"></i> حذف
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>
                        <label class="custom-checkbox">
                            <input type="checkbox" onchange="toggleAllCategories()">
                            <span class="checkmark"></span>
                        </label>
                    </th>
                    <th>الفئة</th>
                    <th>متطلبات الكراتين</th>
                    <th>نقاط الولاء</th>
                    <th>عدد المستخدمين</th>
                    <th>المزايا</th>
                    <th>الحالة</th>
                    <th>الترتيب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>
                        <label class="custom-checkbox">
                            <input type="checkbox" class="category-checkbox" value="{{ $category->id }}" onchange="updateSelectedCount()">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="category-badge category-{{ strtolower($category->name) }}">
                                {{ $category->name }}
                            </span>
                            <div>
                                <div style="font-weight: 600; color: var(--gray-900);">{{ $category->display_name }}</div>
                                @if($category->display_name_en)
                                    <div style="font-size: 12px; color: var(--gray-500);">{{ $category->display_name_en }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="carton-requirements">
                            @if($category->min_cartons > 0)
                                <div style="margin-bottom: 8px;">
                                    <span class="requirement-badge carton">
                                        <i class="fas fa-boxes"></i>
                                        {{ $category->min_cartons }}
                                        @if($category->max_cartons)
                                            - {{ $category->max_cartons }}
                                        @else
                                            +
                                        @endif
                                        كرتون
                                    </span>
                                </div>
                                
                                @if($category->requires_carton_purchase)
                                    <div style="margin-top: 4px;">
                                        <span class="required-badge">يجب شراء كراتين كاملة</span>
                                    </div>
                                @endif
                            @else
                                <span style="color: var(--gray-500); font-style: italic;">لا توجد متطلبات</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="loyalty-points-info">
                            <div class="points-item carton">
                                <i class="fas fa-star" style="color: #FFD700;"></i>
                                <span>{{ $category->carton_loyalty_points ?? 10 }}</span>
                                <small>نقطة لكل كرتون</small>
                            </div>
                            @if(($category->bonus_points_per_carton ?? 0) > 0)
                                <div class="bonus-points" style="font-size: 11px; color: var(--suntop-orange); margin-top: 3px;">
                                    <i class="fas fa-plus"></i>
                                    {{ $category->bonus_points_per_carton }} نقطة إضافية
                                </div>
                            @endif
                            @if(($category->has_points_multiplier ?? false) && ($category->points_multiplier ?? 1) > 1)
                                <div class="multiplier" style="font-size: 11px; color: var(--success); margin-top: 3px;">
                                    <i class="fas fa-times"></i>
                                    مضاعف {{ $category->points_multiplier }}x
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.user-categories.show', $category->id) }}" 
                           style="color: var(--suntop-orange); text-decoration: none; font-weight: 600;">
                            {{ number_format($category->users_count) }} مستخدم
                        </a>
                    </td>
                    <td>
                        @if($category->benefits && count($category->benefits) > 0)
                            <div class="benefits-list">
                                @foreach(array_slice($category->benefits, 0, 2) as $benefit)
                                    <span class="benefit-item">{{ $benefit }}</span>
                                @endforeach
                                @if(count($category->benefits) > 2)
                                    <span class="benefit-item" style="background: var(--suntop-orange); color: white;">
                                        +{{ count($category->benefits) - 2 }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <span style="color: var(--gray-400);">لا توجد مزايا</span>
                        @endif
                    </td>
                    <td>
                        <label class="status-toggle">
                            <input type="checkbox" {{ $category->is_active ? 'checked' : '' }} 
                                   onchange="toggleCategoryStatus({{ $category->id }}, this)">
                            <span class="status-slider"></span>
                        </label>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: var(--gray-600);">{{ $category->sort_order }}</span>
                    </td>
                    <td>
                        <div class="actions-dropdown">
                            <button class="actions-btn" onclick="toggleActionsMenu(this)">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="actions-menu">
                                <a href="{{ route('admin.user-categories.show', $category->id) }}">
                                    <i class="fas fa-eye"></i> عرض التفاصيل
                                </a>
                                <a href="{{ route('admin.user-categories.edit', $category->id) }}">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <a href="#" onclick="deleteCategory({{ $category->id }})" class="danger">
                                    <i class="fas fa-trash"></i> حذف
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: var(--gray-500);">
                        <i class="fas fa-layer-group" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                        <p>لا توجد فئات مستخدمين</p>
                        <a href="{{ route('admin.user-categories.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                            <i class="fas fa-plus"></i>
                            إضافة فئة جديدة
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
    <div class="pagination-wrapper">
        {{ $categories->links() }}
    </div>
    @endif
</div>

<script>
// Bulk actions functionality
let selectedCategories = [];

function toggleBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    bulkActions.classList.toggle('show');
}

function toggleAllCategories() {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    const selectAll = document.getElementById('selectAllCategories');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
    document.getElementById('selectedCount').textContent = `${selectedCategories.length} فئة محددة`;
    
    const selectAll = document.getElementById('selectAllCategories');
    const checkboxes = document.querySelectorAll('.category-checkbox');
    selectAll.checked = selectedCategories.length === checkboxes.length;
}

function bulkAction(action) {
    if (selectedCategories.length === 0) {
        alert('يرجى تحديد فئات أولاً');
        return;
    }
    
    let message = '';
    switch(action) {
        case 'activate':
            message = 'تفعيل الفئات المحددة؟';
            break;
        case 'deactivate':
            message = 'إلغاء تفعيل الفئات المحددة؟';
            break;
        case 'delete':
            message = 'حذف الفئات المحددة؟ هذا الإجراء لا يمكن التراجع عنه.';
            break;
    }
    
    if (confirm(message)) {
        fetch('/admin/user-categories/bulk-action', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                category_ids: selectedCategories
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
}

function toggleCategoryStatus(categoryId, toggle) {
    fetch(`/admin/user-categories/${categoryId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            // Revert toggle if failed
            toggle.checked = !toggle.checked;
            alert('حدث خطأ: ' + data.message);
        }
    })
    .catch(error => {
        // Revert toggle if failed
        toggle.checked = !toggle.checked;
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
}

function toggleActionsMenu(button) {
    // Close all other menus
    document.querySelectorAll('.actions-menu').forEach(menu => {
        if (menu !== button.nextElementSibling) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle current menu
    button.nextElementSibling.classList.toggle('show');
}

function deleteCategory(categoryId) {
    if (confirm('هل أنت متأكد من حذف هذه الفئة؟ سيتم نقل المستخدمين إلى فئات أخرى مناسبة.')) {
        fetch(`/admin/user-categories/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
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
}

function recalculateCategories() {
    if (confirm('هل تريد إعادة حساب فئات جميع المستخدمين؟ قد تستغرق هذه العملية وقتاً.')) {
        fetch('/admin/user-categories/recalculate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
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
}

// Close actions menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});
</script>
@endsection
