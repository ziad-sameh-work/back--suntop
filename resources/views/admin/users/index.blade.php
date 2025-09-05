@extends('layouts.admin')

@section('title', 'إدارة المستخدمين - SunTop')
@section('page-title', 'إدارة المستخدمين')

@push('styles')
<style>
    /* Creative Users Management Styles */
    .users-container {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Hero Section */
    .users-hero {
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 50%, #4a90e2 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
    }

    .users-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: heroFloat 8s ease-in-out infinite;
    }

    .users-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        animation: heroFloat 6s ease-in-out infinite reverse;
    }

    @keyframes heroFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(180deg); }
    }

    .users-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }

    .users-hero-text h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .users-hero-text p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    .users-hero-stats {
        display: flex;
        gap: 30px;
    }

    .hero-stat {
        text-align: center;
        background: rgba(255, 255, 255, 0.15);
        padding: 15px 20px;
        border-radius: 16px;
        backdrop-filter: blur(10px);
    }

    .hero-stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        display: block;
        margin-bottom: 5px;
    }

    .hero-stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* Enhanced Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
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
        margin-bottom: 12px;
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

    .stat-title {
        font-size: 16px;
        color: #64748b;
        margin: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #1e293b, #475569);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 15px 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stat-change {
        font-size: 14px;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 12px;
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        backdrop-filter: blur(10px);
    }

    /* Enhanced Filters & Actions */
    .filters-section {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        padding: 35px;
        margin-bottom: 30px;
        box-shadow: 
            0 15px 35px rgba(0, 0, 0, 0.08),
            0 5px 15px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .filters-section::before {
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

    .filters-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filters-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark));
        color: var(--white);
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        color: var(--white);
        text-decoration: none;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 14px;
        font-weight: 500;
        color: var(--gray-700);
    }

    .form-input, .form-select {
        padding: 10px 12px;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--white);
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--suntop-orange);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: var(--gray-200);
        border-color: var(--gray-300);
    }

    /* Enhanced Users Table */
    .users-table-section {
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

    .users-table-section::before {
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

    .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .bulk-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .users-table th,
    .users-table td {
        padding: 15px 12px;
        text-align: right;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
    }

    .users-table th {
        background: var(--gray-50);
        font-weight: 600;
        color: var(--gray-700);
        font-size: 14px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .users-table td {
        font-size: 14px;
        color: var(--gray-600);
    }

    .users-table tbody tr:hover {
        background: var(--gray-50);
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .user-avatar:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .user-avatar-fallback {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff6b35, #4a90e2);
        color: white;
        display: none;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        border: 3px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .user-avatar-fallback:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-details h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
    }

    .user-details p {
        margin: 2px 0 0 0;
        font-size: 12px;
        color: var(--gray-500);
    }

    .status-badge {
        padding: 6px 16px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .status-active {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .status-inactive {
        background: linear-gradient(135deg, #ef4444, #f87171);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .role-badge {
        padding: 6px 16px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .role-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .role-customer {
        background: linear-gradient(135deg, #4a90e2, #60a5fa);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .role-admin {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .actions-dropdown {
        left: 0;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        min-width: 150px;
        z-index: 100;
        display: none;
    }

    .actions-menu.show {
        display: block;
    }

    .actions-menu a,
    .actions-menu button {
        display: block;
        width: 100%;
        padding: 10px 15px;
        text-align: right;
        border: none;
        background: none;
        color: var(--gray-700);
        text-decoration: none;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .actions-menu a:hover,
    .actions-menu button:hover {
        background: var(--gray-50);
    }

    .actions-menu .danger {
        color: var(--danger);
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: between;
        margin-top: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .pagination-info {
        color: var(--gray-600);
        font-size: 14px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .users-table {
            font-size: 12px;
        }

        .users-table th,
        .users-table td {
            padding: 10px 8px;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
        }
    }

    /* Loading & Empty States */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        display: none;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 107, 53, 0.3);
        border-radius: 50%;
        border-top-color: var(--suntop-orange);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>
@endpush

@section('content')
<div class="users-container">
    <!-- Hero Section -->
    <div class="users-hero">
        <div class="users-hero-content">
            <div class="users-hero-text">
                <h1>إدارة المستخدمين</h1>
                <p>تحكم شامل في حسابات المستخدمين وإدارة الصلاحيات</p>
            </div>
            <div class="users-hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['total_users']) }}</span>
                    <span class="hero-stat-label">إجمالي المستخدمين</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['active_users']) }}</span>
                    <span class="hero-stat-label">مستخدم نشط</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['customers']) }}</span>
                    <span class="hero-stat-label">عميل</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon orange">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="stat-title">إجمالي المستخدمين</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-change">{{ $stats['active_percentage'] }}% نشط</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 class="stat-title">المستخدمين النشطين</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['active_users']) }}</div>
            <div class="stat-change">من إجمالي {{ number_format($stats['total_users']) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon green">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="stat-title">العملاء</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['customers']) }}</div>
            <div class="stat-change">مستخدم عادي</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon purple">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="stat-title">المديرين</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['admins']) }}</div>
            <div class="stat-change">حساب مدير</div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="filters-section">
        <div class="filters-header">
            <h3 class="filters-title">البحث والتصفية</h3>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                إضافة مستخدم جديد
            </a>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" id="filtersForm">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">البحث</label>
                    <input type="text" name="search" class="form-input" 
                           placeholder="البحث بالاسم، البريد، اسم المستخدم..."
                           value="{{ $search }}">
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الحساب</label>
                    <select name="role" class="form-select">
                        <option value="">جميع الأنواع</option>
                        <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>عميل</option>
                        <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>مدير</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الفئة</label>
                    <select name="category" class="form-select">
                        <option value="">جميع الفئات</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                                {{ $cat->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">عدد النتائج</label>
                    <select name="per_page" class="form-select">
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="users-table-section">
        <div class="table-header">
            <h3 class="table-title">قائمة المستخدمين ({{ $users->total() }})</h3>
            <div class="bulk-actions">
                <select id="bulkAction" class="form-select">
                    <option value="">إجراءات جماعية</option>
                    <option value="activate">تفعيل المحدد</option>
                    <option value="deactivate">تعطيل المحدد</option>
                    <option value="delete">حذف المحدد</option>
                </select>
                <button type="button" class="btn-secondary" onclick="executeBulkAction()">تنفيذ</button>
            </div>
        </div>

        @if($users->count() > 0)
        <div style="overflow-x: auto;">
            <table class="users-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" onchange="toggleAllUsers()">
                        </th>
                        <th>المستخدم</th>
                        <th>نوع الحساب</th>
                        <th>الفئة</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>إجمالي المشتريات</th>
                        <th style="width: 100px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                        </td>
                        <td>
                            <div class="user-info">
                                <img src="{{ $user->profile_image_url }}" 
                                     alt="صورة المستخدم" class="user-avatar"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="user-avatar-fallback">
                                    {{ $user->initial }}
                                </div>
                                <div class="user-details">
                                    <h4>{{ $user->name }}</h4>
                                    <p>{{ $user->email }}</p>
                                    @if($user->phone)
                                        <p>{{ $user->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge role-{{ $user->role }}">
                                {{ $user->role === 'customer' ? 'عميل' : 'مدير' }}
                            </span>
                        </td>
                        <td>
                            @if($user->userCategory)
                                <span class="status-badge status-active">{{ $user->userCategory->display_name }}</span>
                            @else
                                <span style="color: var(--gray-400);">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge status-{{ $user->is_active ? 'active' : 'inactive' }}">
                                {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y/m/d') }}</td>
                        <td>{{ number_format($user->total_purchase_amount, 2) }} ج.م</td>
                        <td>
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="toggleActionsMenu({{ $user->id }})">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="actions-menu" id="actionsMenu{{ $user->id }}">
                                    <a href="{{ route('admin.users.show', $user->id) }}">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <button onclick="toggleUserStatus({{ $user->id }})">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                        {{ $user->is_active ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                    <button onclick="resetUserPassword({{ $user->id }})">
                                        <i class="fas fa-key"></i> إعادة تعيين كلمة المرور
                                    </button>
                                    <button class="danger" onclick="deleteUser({{ $user->id }})">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            <div class="pagination-info">
                عرض {{ $users->firstItem() ?? 0 }} إلى {{ $users->lastItem() ?? 0 }} 
                من أصل {{ $users->total() }} نتيجة
            </div>
            {{ $users->appends(request()->query())->links() }}
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>لا توجد مستخدمين</h3>
            <p>لم يتم العثور على مستخدمين يطابقون معايير البحث</p>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                إضافة أول مستخدم
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>
@endsection

@push('scripts')
<script>
// Actions Menu Toggle
function toggleActionsMenu(userId) {
    const menu = document.getElementById(`actionsMenu${userId}`);
    
    // Close all other menus
    document.querySelectorAll('.actions-menu').forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
    menu.classList.toggle('show');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(m => {
            m.classList.remove('show');
        });
    }
});

// Select All Users
function toggleAllUsers() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Toggle User Status
async function toggleUserStatus(userId) {
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.users.index') }}/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث الحالة', 'error');
    } finally {
        hideLoading();
    }
}

// Reset User Password
async function resetUserPassword(userId) {
    if (!confirm('هل أنت متأكد من إعادة تعيين كلمة المرور؟')) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.users.index') }}/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(`${data.message}. كلمة المرور الجديدة: ${data.new_password}`, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء إعادة تعيين كلمة المرور', 'error');
    } finally {
        hideLoading();
    }
}

// Delete User
async function deleteUser(userId) {
    if (!confirm('هل أنت متأكد من حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه.')) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.users.index') }}/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء حذف المستخدم', 'error');
    } finally {
        hideLoading();
    }
}

// Bulk Actions
async function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    
    if (!action) {
        showNotification('الرجاء اختيار إجراء', 'error');
        return;
    }
    
    if (selectedUsers.length === 0) {
        showNotification('الرجاء اختيار مستخدمين على الأقل', 'error');
        return;
    }
    
    const actionText = {
        'activate': 'تفعيل',
        'deactivate': 'تعطيل',
        'delete': 'حذف'
    };
    
    if (!confirm(`هل أنت متأكد من ${actionText[action]} ${selectedUsers.length} مستخدم؟`)) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.users.index') }}/bulk-action`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: action,
                user_ids: selectedUsers
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تنفيذ العملية', 'error');
    } finally {
        hideLoading();
    }
}

// Utility Functions
function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

function showNotification(message, type = 'info') {
    // Simple notification system
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 10000;
        max-width: 400px;
        background: ${type === 'success' ? '#10B981' : '#EF4444'};
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Auto-submit filters on change
document.querySelectorAll('#filtersForm select').forEach(select => {
    select.addEventListener('change', () => {
        document.getElementById('filtersForm').submit();
    });
});

// Search input debounce
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filtersForm').submit();
    }, 500);
});
</script>
@endpush
