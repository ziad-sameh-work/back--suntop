@extends('layouts.admin')

@section('title', 'إدارة العروض')

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

    .filters-section {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 25px;
    }

    .filters-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
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

    .offer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .offer-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .offer-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .offer-image i {
        color: var(--gray-400);
        font-size: 18px;
    }

    .offer-details h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 4px 0;
    }

    .offer-details p {
        font-size: 12px;
        color: var(--gray-600);
        margin: 0;
    }

    .offer-code {
        background: var(--gray-100);
        color: var(--gray-800);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        font-family: monospace;
    }

    .offer-type {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .offer-type.percentage {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .offer-type.fixed {
        background: rgba(74, 144, 226, 0.1);
        color: var(--suntop-blue);
    }

    .offer-discount {
        font-weight: 600;
        color: var(--suntop-orange);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-badge.inactive {
        background: rgba(156, 163, 175, 0.2);
        color: var(--gray-600);
    }

    .status-badge.expired {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .status-badge.upcoming {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .actions-dropdown {
        position: relative;
        display: inline-block;
    }

    .actions-btn {
        background: var(--gray-100);
        border: 1px solid var(--gray-200);
        border-radius: 6px;
        padding: 8px 10px;
        cursor: pointer;
        color: var(--gray-600);
        transition: all 0.3s ease;
    }

    .actions-btn:hover {
        background: var(--gray-200);
        color: var(--gray-800);
    }

    .actions-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 150px;
        z-index: 1000;
        display: none;
    }

    .actions-menu.show {
        display: block;
    }

    .actions-menu a,
    .actions-menu button {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 10px 15px;
        text-decoration: none;
        color: var(--gray-700);
        border: none;
        background: none;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .actions-menu a:hover,
    .actions-menu button:hover {
        background: var(--gray-50);
        color: var(--gray-900);
    }

    .actions-menu button.danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .pagination-wrapper {
        padding: 20px 25px;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: between;
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
    }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-gift"></i>
        إدارة العروض
    </h1>
    <p class="page-subtitle">إدارة العروض الترويجية وأكواد الخصم</p>
</div>

<!-- Statistics Row -->
<div class="stats-row">
    <div class="stat-card orange">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-gift"></i>
            </div>
            <h3 class="stat-title">إجمالي العروض</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_offers']) }}</div>
        <div class="stat-change">
            <i class="fas fa-tags"></i>
            <span>عرض مسجل</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="stat-title">العروض النشطة</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['active_offers']) }}</div>
        <div class="stat-change">
            <i class="fas fa-arrow-up"></i>
            <span>نشط حالياً</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="stat-title">العروض المنتهية</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['expired_offers']) }}</div>
        <div class="stat-change">
            <i class="fas fa-calendar-times"></i>
            <span>منتهي الصلاحية</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="stat-title">إجمالي الاستخدام</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_usage']) }}</div>
        <div class="stat-change">
            <i class="fas fa-users"></i>
            <span>مرة استخدام</span>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="filters-section">
    <h3 class="filters-title">
        <i class="fas fa-filter"></i>
        البحث والتصفية
    </h3>
    
    <form method="GET" action="{{ route('admin.offers.index') }}">
        <div class="filters-grid">
            <div class="form-group">
                <label>البحث</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="البحث في العنوان أو الكود..." 
                       value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label>حالة العرض</label>
                <select name="status" class="form-control">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>جميع الحالات</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي الصلاحية</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>قادم</option>
                </select>
            </div>

            <div class="form-group">
                <label>نوع الخصم</label>
                <select name="type" class="form-control">
                    <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>جميع الأنواع</option>
                    <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                    <option value="fixed_amount" {{ request('type') === 'fixed_amount' ? 'selected' : '' }}>مبلغ ثابت</option>
                </select>
            </div>

            <div class="form-group">
                <label>من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="form-group">
                <label>إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="form-group">
                <label>عدد النتائج</label>
                <select name="per_page" class="form-control">
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>

        <div class="filters-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                بحث
            </button>
            <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">
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
            قائمة العروض ({{ $offers->total() }})
        </h3>
        <div class="table-actions">
            <button class="btn-secondary" onclick="toggleBulkActions()">
                <i class="fas fa-tasks"></i>
                <span>إجراءات جماعية</span>
            </button>
            <button class="btn-secondary" onclick="exportOffers()">
                <i class="fas fa-download"></i>
                <span>تصدير</span>
            </button>
            <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                عرض جديد
            </a>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <label class="custom-checkbox">
            <input type="checkbox" id="selectAllOffers" onchange="toggleAllOffers()">
            <span class="checkmark"></span>
        </label>
        <span class="selected-count" id="selectedCount">0 عرض محدد</span>
        <div class="bulk-actions-dropdown">
            <button class="btn-secondary" onclick="bulkUpdateStatus('activate')">
                <i class="fas fa-check"></i> تفعيل
            </button>
            <button class="btn-secondary" onclick="bulkUpdateStatus('deactivate')">
                <i class="fas fa-times"></i> إلغاء تفعيل
            </button>
            <button class="btn-secondary" onclick="bulkDelete()">
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
                            <input type="checkbox" onchange="toggleAllOffers()">
                            <span class="checkmark"></span>
                        </label>
                    </th>
                    <th>العرض</th>
                    <th>الكود</th>
                    <th>النوع</th>
                    <th>الخصم</th>
                    <th>صالح من</th>
                    <th>صالح حتى</th>
                    <th>الاستخدام</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($offers as $offer)
                <tr>
                    <td>
                        <label class="custom-checkbox">
                            <input type="checkbox" class="offer-checkbox" value="{{ $offer->id }}" onchange="updateSelectedCount()">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>
                        <div class="offer-info">
                            <div class="offer-image">
                                @if($offer->image_url)
                                    <img src="{{ asset('storage/' . $offer->image_url) }}" alt="{{ $offer->title }}">
                                @else
                                    <i class="fas fa-gift"></i>
                                @endif
                            </div>
                            <div class="offer-details">
                                <h4>{{ $offer->title }}</h4>
                                <p>{{ Str::limit($offer->description, 50) }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="offer-code">{{ $offer->code }}</span>
                    </td>
                    <td>
                        <span class="offer-type {{ $offer->type === 'percentage' ? 'percentage' : 'fixed' }}">
                            {{ $offer->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}
                        </span>
                    </td>
                    <td>
                        <span class="offer-discount">
                            @if($offer->type === 'percentage')
                                {{ $offer->discount_percentage }}%
                            @else
                                {{ number_format($offer->discount_amount) }} ج.م
                            @endif
                        </span>
                    </td>
                    <td>{{ $offer->valid_from->format('Y-m-d') }}</td>
                    <td>{{ $offer->valid_until->format('Y-m-d') }}</td>
                    <td>
                        {{ $offer->used_count }}
                        @if($offer->usage_limit)
                            / {{ $offer->usage_limit }}
                        @endif
                    </td>
                    <td>
                        @php
                            $now = now();
                            if (!$offer->is_active) {
                                $status = 'inactive';
                                $statusText = 'غير نشط';
                            } elseif ($offer->valid_until < $now) {
                                $status = 'expired';
                                $statusText = 'منتهي';
                            } elseif ($offer->valid_from > $now) {
                                $status = 'upcoming';
                                $statusText = 'قادم';
                            } else {
                                $status = 'active';
                                $statusText = 'نشط';
                            }
                        @endphp
                        <span class="status-badge {{ $status }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        <div class="actions-dropdown">
                            <button class="actions-btn" onclick="toggleActionsMenu({{ $offer->id }})">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="actions-menu" id="actionsMenu{{ $offer->id }}">
                                <a href="{{ route('admin.offers.show', $offer->id) }}">
                                    <i class="fas fa-eye"></i> عرض التفاصيل
                                </a>
                                <a href="{{ route('admin.offers.edit', $offer->id) }}">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <a href="{{ route('admin.offers.analytics', $offer->id) }}">
                                    <i class="fas fa-chart-line"></i> التحليلات
                                </a>
                                <button onclick="toggleOfferStatus({{ $offer->id }}, {{ $offer->is_active ? 'false' : 'true' }})">
                                    <i class="fas fa-{{ $offer->is_active ? 'ban' : 'check' }}"></i>
                                    {{ $offer->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}
                                </button>
                                <button onclick="deleteOffer({{ $offer->id }})" class="danger">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 40px; color: var(--gray-500);">
                        <i class="fas fa-gift" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                        <p>لا توجد عروض</p>
                        <a href="{{ route('admin.offers.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                            <i class="fas fa-plus"></i> إضافة أول عرض
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($offers->hasPages())
    <div class="pagination-wrapper">
        {{ $offers->links() }}
    </div>
    @endif
</div>

<script>
// Bulk actions functionality
let selectedOffers = [];

function toggleBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    bulkActions.classList.toggle('show');
}

function toggleAllOffers() {
    const checkboxes = document.querySelectorAll('.offer-checkbox');
    const selectAll = document.getElementById('selectAllOffers');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    selectedOffers = Array.from(document.querySelectorAll('.offer-checkbox:checked')).map(cb => cb.value);
    document.getElementById('selectedCount').textContent = `${selectedOffers.length} عرض محدد`;
    
    const selectAll = document.getElementById('selectAllOffers');
    const checkboxes = document.querySelectorAll('.offer-checkbox');
    selectAll.checked = selectedOffers.length === checkboxes.length;
}

function toggleActionsMenu(offerId) {
    // Close all other menus
    document.querySelectorAll('.actions-menu').forEach(menu => {
        if (menu.id !== `actionsMenu${offerId}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle current menu
    const menu = document.getElementById(`actionsMenu${offerId}`);
    menu.classList.toggle('show');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Offer management functions
function toggleOfferStatus(offerId, newStatus) {
    if (confirm('هل أنت متأكد من تغيير حالة العرض؟')) {
        fetch(`/admin/offers/${offerId}/toggle-status`, {
            method: 'POST',
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

function deleteOffer(offerId) {
    if (confirm('هل أنت متأكد من حذف هذا العرض؟ لا يمكن التراجع عن هذا الإجراء.')) {
        fetch(`/admin/offers/${offerId}`, {
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

function bulkUpdateStatus(action) {
    if (selectedOffers.length === 0) {
        alert('يرجى تحديد العروض أولاً');
        return;
    }

    const actionText = action === 'activate' ? 'تفعيل' : 'إلغاء تفعيل';
    if (confirm(`هل أنت متأكد من ${actionText} ${selectedOffers.length} عرض؟`)) {
        fetch('/admin/offers/bulk-action', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                offer_ids: selectedOffers
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

function bulkDelete() {
    if (selectedOffers.length === 0) {
        alert('يرجى تحديد العروض أولاً');
        return;
    }

    if (confirm(`هل أنت متأكد من حذف ${selectedOffers.length} عرض؟ لا يمكن التراجع عن هذا الإجراء.`)) {
        fetch('/admin/offers/bulk-action', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                offer_ids: selectedOffers
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

function exportOffers() {
    window.open('/admin/offers/export?' + new URLSearchParams(window.location.search), '_blank');
}
</script>
@endsection
