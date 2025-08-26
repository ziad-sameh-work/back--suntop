@extends('layouts.admin')

@section('title', 'إدارة التجار')
@section('page-title', 'إدارة التجار')

@push('styles')
<style>
    .merchants-container { padding: 25px; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: linear-gradient(135deg, var(--white) 0%, #f8fafc 100%); border-radius: 16px; padding: 25px; border: 1px solid var(--gray-100); transition: all 0.3s ease; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--gradient); }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12); }
    .stat-card.clickable { cursor: pointer; }
    .stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; }
    .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--white); }
    .stat-icon.merchants { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); }
    .stat-icon.active { background: linear-gradient(135deg, #10B981, #059669); }
    .stat-icon.inactive { background: linear-gradient(135deg, #EF4444, #DC2626); }
    .stat-icon.open { background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark)); }
    .stat-icon.revenue { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
    .stat-value { font-size: 32px; font-weight: 700; color: var(--gray-800); margin: 0 0 5px 0; }
    .stat-label { font-size: 14px; color: var(--gray-600); margin: 0; }

    .filters-card { background: var(--white); border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); margin-bottom: 25px; }
    .filters-header { display: flex; align-items: center; justify-content: between; margin-bottom: 20px; }
    .filters-title { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0; display: flex; align-items: center; gap: 10px; }
    .search-section { margin-bottom: 20px; }
    .search-input { width: 100%; padding: 12px 15px 12px 45px; border: 2px solid var(--gray-200); border-radius: 10px; font-size: 14px; transition: all 0.3s ease; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%236B7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>') no-repeat 15px center; background-size: 20px; }
    .search-input:focus { outline: none; border-color: var(--suntop-orange); box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1); }
    .filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
    .filter-group { display: flex; flex-direction: column; gap: 5px; }
    .filter-label { font-size: 12px; font-weight: 500; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }
    .filter-select, .filter-input { padding: 10px 12px; border: 2px solid var(--gray-200); border-radius: 8px; font-size: 14px; transition: all 0.3s ease; background: var(--white); }
    .filter-select:focus, .filter-input:focus { outline: none; border-color: var(--suntop-orange); box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1); }
    .filters-actions { display: flex; gap: 10px; margin-top: 20px; }
    .btn-filter { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); color: var(--white); border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3); }
    .btn-clear { background: var(--gray-100); color: var(--gray-700); border: 2px solid var(--gray-200); padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; }
    .btn-clear:hover { background: var(--gray-200); }

    .merchants-table-card { background: var(--white); border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); overflow: hidden; }
    .table-header { padding: 20px 25px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; justify-content: space-between; }
    .table-title { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0; }
    .table-actions { display: flex; gap: 10px; }
    .btn-secondary { background: var(--gray-100); color: var(--gray-700); border: 2px solid var(--gray-200); padding: 10px 16px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; }
    .btn-secondary:hover { background: var(--gray-200); border-color: var(--gray-300); color: var(--gray-800); text-decoration: none; transform: translateY(-1px); }
    .btn-secondary i { font-size: 13px; }
    .btn-add { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); color: var(--white); border: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3); color: var(--white); text-decoration: none; }

    .merchants-table { width: 100%; border-collapse: collapse; }
    .merchants-table th { background: var(--gray-50); padding: 15px 20px; text-align: right; font-size: 14px; font-weight: 600; color: var(--gray-700); border-bottom: 1px solid var(--gray-200); white-space: nowrap; }
    .merchants-table td { padding: 20px; border-bottom: 1px solid var(--gray-100); vertical-align: middle; }
    .merchants-table tr:hover { background: rgba(255, 107, 53, 0.02); }

    .merchant-info { display: flex; align-items: center; gap: 15px; }
    .merchant-logo { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 2px solid var(--gray-200); }
    .merchant-details { flex: 1; }
    .merchant-name { font-weight: 600; color: var(--gray-800); margin: 0 0 3px 0; }
    .merchant-business { font-size: 13px; color: var(--gray-600); margin: 0 0 2px 0; }
    .merchant-contact { font-size: 12px; color: var(--gray-500); margin: 0; }

    .merchant-stats { text-align: center; }
    .stat-number { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0 0 2px 0; }
    .stat-text { font-size: 12px; color: var(--gray-600); margin: 0; }

    .commission-badge { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); color: var(--white); padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; }

    .status-toggle { position: relative; display: inline-block; width: 50px; height: 24px; }
    .status-toggle input { opacity: 0; width: 0; height: 0; }
    .status-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-300); transition: 0.3s; border-radius: 24px; }
    .status-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%; }
    input:checked + .status-slider { background-color: var(--success); }
    input:checked + .status-slider:before { transform: translateX(26px); }

    .actions-dropdown { position: relative; display: inline-block; }
    .actions-btn { background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 8px; padding: 8px 12px; cursor: pointer; display: flex; align-items: center; gap: 5px; color: var(--gray-700); transition: all 0.3s ease; }
    .actions-btn:hover { background: var(--gray-200); }
    .actions-menu { position: absolute; left: 0; top: 100%; background: var(--white); border: 1px solid var(--gray-200); border-radius: 8px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); z-index: 1000; min-width: 180px; display: none; }
    .actions-menu.show { display: block; }
    .actions-menu a, .actions-menu button { display: block; width: 100%; padding: 10px 15px; text-decoration: none; color: var(--gray-700); border: none; background: none; text-align: right; cursor: pointer; transition: all 0.3s ease; font-size: 14px; }
    .actions-menu a:hover, .actions-menu button:hover { background: var(--gray-50); }
    .actions-menu .danger { color: var(--danger); }
    .actions-menu .danger:hover { background: rgba(239, 68, 68, 0.05); }

    .bulk-actions { background: linear-gradient(135deg, #f8fafc, #e2e8f0); padding: 15px 25px; border-bottom: 1px solid var(--gray-200); display: none; border-radius: 0 0 16px 16px; }
    .bulk-actions.show { display: flex; align-items: center; gap: 15px; }
    .bulk-actions-dropdown { display: flex; gap: 10px; }
    .bulk-actions .btn-secondary { background: var(--white); border: 2px solid var(--suntop-orange); color: var(--suntop-orange); padding: 8px 14px; font-size: 13px; }
    .bulk-actions .btn-secondary:hover { background: var(--suntop-orange); color: var(--white); }
    .selected-count { font-size: 14px; color: var(--gray-600); font-weight: 500; background: var(--white); padding: 8px 12px; border-radius: 6px; border: 1px solid var(--gray-300); }

    .pagination-wrapper { padding: 20px 25px; border-top: 1px solid var(--gray-100); display: flex; align-items: center; justify-content: space-between; }
    .pagination-info { font-size: 14px; color: var(--gray-600); }

    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-icon { font-size: 64px; color: var(--gray-300); margin-bottom: 20px; }
    .empty-title { font-size: 20px; font-weight: 600; color: var(--gray-600); margin: 0 0 10px 0; }
    .empty-description { font-size: 16px; color: var(--gray-500); margin: 0; }

    /* Custom Checkbox Styles */
    .custom-checkbox { position: relative; display: inline-block; }
    .custom-checkbox input[type="checkbox"] { opacity: 0; position: absolute; width: 18px; height: 18px; cursor: pointer; }
    .custom-checkbox .checkmark { position: absolute; top: 0; left: 0; height: 18px; width: 18px; background-color: var(--white); border: 2px solid var(--gray-300); border-radius: 4px; transition: all 0.3s ease; }
    .custom-checkbox:hover input ~ .checkmark { border-color: var(--suntop-orange); }
    .custom-checkbox input:checked ~ .checkmark { background-color: var(--suntop-orange); border-color: var(--suntop-orange); }
    .custom-checkbox .checkmark:after { content: ""; position: absolute; display: none; left: 5px; top: 2px; width: 6px; height: 10px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); }
    .custom-checkbox input:checked ~ .checkmark:after { display: block; }

    @media (max-width: 768px) {
        .merchants-container { padding: 15px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .stat-card { padding: 20px; }
        .stat-value { font-size: 24px; }
        .filters-grid { grid-template-columns: 1fr; }
        .table-header { flex-direction: column; gap: 15px; align-items: stretch; }
        .merchants-table { font-size: 13px; }
        .merchants-table th, .merchants-table td { padding: 10px 8px; }
        .pagination-wrapper { flex-direction: column; gap: 15px; }
        .bulk-actions-dropdown { flex-direction: column; }
        .btn-secondary span { display: none; }
        .btn-secondary { padding: 8px 10px; }
        .table-actions { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="merchants-container">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card clickable" onclick="filterByStatus('all')">
            <div class="stat-header">
                <div class="stat-icon merchants">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_merchants']) }}</div>
            <div class="stat-label">إجمالي التجار</div>
        </div>

        <div class="stat-card clickable" onclick="filterByStatus('active')">
            <div class="stat-header">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['active_merchants']) }}</div>
            <div class="stat-label">تجار نشطون</div>
        </div>

        <div class="stat-card clickable" onclick="filterByStatus('inactive')">
            <div class="stat-header">
                <div class="stat-icon inactive">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['inactive_merchants']) }}</div>
            <div class="stat-label">تجار غير نشطين</div>
        </div>

        <div class="stat-card clickable" onclick="filterByOpenStatus('open')">
            <div class="stat-header">
                <div class="stat-icon open">
                    <i class="fas fa-door-open"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['open_merchants']) }}</div>
            <div class="stat-label">متاجر مفتوحة</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon revenue">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-label">إجمالي المنتجات</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon revenue">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_revenue'], 0) }}</div>
            <div class="stat-label">إجمالي الإيرادات (ج.م)</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <div class="filters-header">
            <h3 class="filters-title">
                <i class="fas fa-filter" style="color: var(--suntop-orange);"></i>
                البحث والتصفية
            </h3>
        </div>

        <form method="GET" action="{{ route('admin.merchants.index') }}" id="filtersForm">
            <div class="search-section">
                <input type="text" name="search" class="search-input" 
                       placeholder="البحث باسم التاجر، اسم المحل، البريد الإلكتروني، أو رقم الهاتف..." 
                       value="{{ request('search') }}">
            </div>

            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">حالة التاجر</label>
                    <select name="status" class="filter-select">
                        <option value="all">جميع الحالات</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">حالة المتجر</label>
                    <select name="open_status" class="filter-select">
                        <option value="all">جميع الحالات</option>
                        <option value="open" {{ request('open_status') === 'open' ? 'selected' : '' }}>مفتوح</option>
                        <option value="closed" {{ request('open_status') === 'closed' ? 'selected' : '' }}>مغلق</option>
                    </select>
                </div>

                @if(count($cities) > 0)
                <div class="filter-group">
                    <label class="filter-label">المدينة</label>
                    <select name="city" class="filter-select">
                        <option value="all">جميع المدن</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="filter-group">
                    <label class="filter-label">نسبة العمولة من</label>
                    <input type="number" name="commission_from" class="filter-input" placeholder="0%" 
                           value="{{ request('commission_from') }}" step="0.1" min="0" max="100">
                </div>

                <div class="filter-group">
                    <label class="filter-label">نسبة العمولة إلى</label>
                    <input type="number" name="commission_to" class="filter-input" placeholder="100%" 
                           value="{{ request('commission_to') }}" step="0.1" min="0" max="100">
                </div>

                <div class="filter-group">
                    <label class="filter-label">عدد النتائج</label>
                    <select name="per_page" class="filter-select">
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <div class="filters-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i>
                    بحث وتصفية
                </button>
                <a href="{{ route('admin.merchants.index') }}" class="btn-clear">
                    <i class="fas fa-times"></i>
                    مسح المرشحات
                </a>
            </div>
        </form>
    </div>

    <!-- Merchants Table -->
    <div class="merchants-table-card">
        <div class="table-header">
            <h3 class="table-title">
                التجار 
                @if($merchants->total() > 0)
                    <span style="color: var(--gray-500); font-weight: normal;">({{ number_format($merchants->total()) }} تاجر)</span>
                @endif
            </h3>
            <div class="table-actions">
                <a href="{{ route('admin.merchants.create') }}" class="btn-add">
                    <i class="fas fa-plus"></i>
                    <span>إضافة تاجر</span>
                </a>
                <button class="btn-secondary" onclick="toggleBulkActions()">
                    <i class="fas fa-tasks"></i>
                    <span>إجراءات جماعية</span>
                </button>
                <button class="btn-secondary" onclick="exportMerchants()">
                    <i class="fas fa-download"></i>
                    <span>تصدير</span>
                </button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions">
            <label class="custom-checkbox">
                <input type="checkbox" id="selectAllMerchants" onchange="toggleAllMerchants()">
                <span class="checkmark"></span>
            </label>
            <span class="selected-count" id="selectedCount">0 تاجر محدد</span>
            
            <div class="bulk-actions-dropdown">
                <button class="btn-secondary" onclick="bulkAction('activate')">
                    <i class="fas fa-check"></i> تفعيل
                </button>
                <button class="btn-secondary" onclick="bulkAction('deactivate')">
                    <i class="fas fa-times"></i> إلغاء تفعيل
                </button>
                <button class="btn-secondary" onclick="bulkAction('open')">
                    <i class="fas fa-door-open"></i> فتح المتاجر
                </button>
                <button class="btn-secondary" onclick="bulkAction('close')">
                    <i class="fas fa-door-closed"></i> إغلاق المتاجر
                </button>
            </div>
        </div>

        @if($merchants->count() > 0)
            <table class="merchants-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <label class="custom-checkbox">
                                <input type="checkbox" onchange="toggleAllMerchants()">
                                <span class="checkmark"></span>
                            </label>
                        </th>
                        <th>معلومات التاجر</th>
                        <th>المدينة</th>
                        <th>المنتجات</th>
                        <th>الطلبات</th>
                        <th>الإيرادات</th>
                        <th>العمولة</th>
                        <th>الحالة</th>
                        <th>المتجر</th>
                        <th style="width: 120px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($merchants as $merchant)
                        <tr>
                            <td>
                                <label class="custom-checkbox">
                                    <input type="checkbox" class="merchant-checkbox" value="{{ $merchant->id }}" onchange="updateSelectedCount()">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>
                                <div class="merchant-info">
                                    <img src="{{ $merchant->logo ? asset($merchant->logo) : asset('images/no-merchant.png') }}" 
                                         alt="شعار التاجر" class="merchant-logo"
                                         onerror="this.src='{{ asset('images/no-merchant.png') }}'">
                                    <div class="merchant-details">
                                        <div class="merchant-name">{{ $merchant->name }}</div>
                                        <div class="merchant-business">{{ $merchant->business_name }}</div>
                                        <div class="merchant-contact">{{ $merchant->email }} | {{ $merchant->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $merchant->city }}</td>
                            <td>
                                <div class="merchant-stats">
                                    <div class="stat-number">{{ $merchant->products_count ?? 0 }}</div>
                                    <div class="stat-text">منتج</div>
                                </div>
                            </td>
                            <td>
                                <div class="merchant-stats">
                                    <div class="stat-number">{{ $merchant->orders_count ?? 0 }}</div>
                                    <div class="stat-text">طلب</div>
                                </div>
                            </td>
                            <td>
                                <div class="merchant-stats">
                                    <div class="stat-number">{{ number_format($merchant->total_revenue ?? 0, 0) }}</div>
                                    <div class="stat-text">ج.م</div>
                                </div>
                            </td>
                            <td>
                                <span class="commission-badge">{{ $merchant->commission_rate }}%</span>
                            </td>
                            <td>
                                <label class="status-toggle">
                                    <input type="checkbox" {{ $merchant->is_active ? 'checked' : '' }} 
                                           onchange="toggleStatus({{ $merchant->id }})">
                                    <span class="status-slider"></span>
                                </label>
                            </td>
                            <td>
                                <label class="status-toggle">
                                    <input type="checkbox" {{ $merchant->is_open ? 'checked' : '' }} 
                                           onchange="toggleOpenStatus({{ $merchant->id }})">
                                    <span class="status-slider"></span>
                                </label>
                            </td>
                            <td>
                                <div class="actions-dropdown">
                                    <button class="actions-btn" onclick="toggleActionsMenu({{ $merchant->id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="actions-menu" id="actionsMenu{{ $merchant->id }}">
                                        <a href="{{ route('admin.merchants.show', $merchant->id) }}">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </a>
                                        <a href="{{ route('admin.merchants.edit', $merchant->id) }}">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <a href="{{ route('admin.merchants.analytics', $merchant->id) }}">
                                            <i class="fas fa-chart-line"></i> التحليلات
                                        </a>
                                        <button onclick="deleteMerchant({{ $merchant->id }})" class="danger">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    عرض {{ $merchants->firstItem() ?? 0 }} إلى {{ $merchants->lastItem() ?? 0 }} 
                    من أصل {{ number_format($merchants->total()) }} تاجر
                </div>
                <div class="pagination-controls">
                    {{ $merchants->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-store-slash"></i>
                </div>
                <h3 class="empty-title">لا يوجد تجار</h3>
                <p class="empty-description">
                    @if(request()->hasAny(['search', 'status', 'open_status', 'city', 'commission_from', 'commission_to']))
                        لم يتم العثور على تجار تطابق معايير البحث المحددة.
                    @else
                        لم يتم إضافة أي تجار بعد. <a href="{{ route('admin.merchants.create') }}" style="color: var(--suntop-orange);">إضافة أول تاجر</a>
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedMerchants = [];

// Filter functions
function filterByStatus(status) {
    const form = document.getElementById('filtersForm');
    const statusSelect = form.querySelector('select[name="status"]');
    statusSelect.value = status === 'all' ? 'all' : status;
    form.submit();
}

function filterByOpenStatus(status) {
    const form = document.getElementById('filtersForm');
    const openStatusSelect = form.querySelector('select[name="open_status"]');
    openStatusSelect.value = status;
    form.submit();
}

// Actions menu toggle
function toggleActionsMenu(merchantId) {
    document.querySelectorAll('.actions-menu').forEach(menu => {
        if (menu.id !== `actionsMenu${merchantId}`) {
            menu.classList.remove('show');
        }
    });
    
    const menu = document.getElementById(`actionsMenu${merchantId}`);
    menu.classList.toggle('show');
}

// Toggle merchant status
async function toggleStatus(merchantId) {
    try {
        const response = await fetch(`{{ route('admin.merchants.index') }}/${merchantId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
            location.reload();
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث حالة التاجر', 'error');
        location.reload();
    }
}

// Toggle merchant open status
async function toggleOpenStatus(merchantId) {
    try {
        const response = await fetch(`{{ route('admin.merchants.index') }}/${merchantId}/toggle-open`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
            location.reload();
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث حالة المتجر', 'error');
        location.reload();
    }
}

// Delete merchant
async function deleteMerchant(merchantId) {
    if (!confirm('هل أنت متأكد من حذف هذا التاجر؟ هذا الإجراء لا يمكن التراجع عنه.')) return;
    
    try {
        const response = await fetch(`{{ route('admin.merchants.index') }}/${merchantId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء حذف التاجر', 'error');
    }
}

// Bulk actions
function toggleBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    bulkActions.classList.toggle('show');
}

function toggleAllMerchants() {
    const selectAll = document.getElementById('selectAllMerchants');
    const checkboxes = document.querySelectorAll('.merchant-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.merchant-checkbox:checked');
    selectedMerchants = Array.from(checkboxes).map(cb => cb.value);
    
    const count = selectedMerchants.length;
    document.getElementById('selectedCount').textContent = `${count} تاجر محدد`;
    
    const allCheckboxes = document.querySelectorAll('.merchant-checkbox');
    const selectAll = document.getElementById('selectAllMerchants');
    selectAll.checked = count === allCheckboxes.length;
    selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
}

async function bulkAction(action) {
    if (selectedMerchants.length === 0) {
        showNotification('الرجاء اختيار تاجر واحد على الأقل', 'error');
        return;
    }
    
    const actionNames = {
        'activate': 'تفعيل',
        'deactivate': 'إلغاء تفعيل',
        'open': 'فتح متاجر',
        'close': 'إغلاق متاجر',
        'delete': 'حذف'
    };
    
    if (!confirm(`هل أنت متأكد من ${actionNames[action]} التجار المحددين؟`)) return;
    
    try {
        const response = await fetch(`{{ route('admin.merchants.bulk-action') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: action,
                merchant_ids: selectedMerchants
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تنفيذ العملية', 'error');
    }
}

// View analytics
function viewAnalytics(merchantId) {
    window.location.href = `{{ route('admin.merchants.index') }}/${merchantId}/analytics`;
}

// Export function
function exportMerchants() {
    showNotification('ميزة التصدير قيد التطوير', 'info');
}

// Utility function
function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
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
        background: ${type === 'success' ? '#10B981' : (type === 'error' ? '#EF4444' : '#3B82F6')};
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Auto-submit search with debounce
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (e.target.value.length >= 3 || e.target.value.length === 0) {
            document.getElementById('filtersForm').submit();
        }
    }, 500);
});

// Initialize
updateSelectedCount();
</script>
@endpush
