@extends('layouts.admin')

@section('title', 'إدارة المنتجات - SunTop')
@section('page-title', 'إدارة المنتجات')

@push('styles')
<style>
    /* Products Management Styles */
    .products-container {
        display: grid;
        gap: 25px;
        grid-template-columns: 1fr;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--white);
        border-radius: 16px;
        padding: 20px;
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
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--white);
    }

    .stat-icon.orange { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); }
    .stat-icon.blue { background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark)); }
    .stat-icon.green { background: linear-gradient(135deg, var(--success), #0D9488); }
    .stat-icon.purple { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
    .stat-icon.red { background: linear-gradient(135deg, var(--danger), #DC2626); }

    .stat-title {
        font-size: 13px;
        color: var(--gray-500);
        margin: 0;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--gray-800);
        margin: 8px 0;
    }

    .stat-change {
        font-size: 12px;
        color: var(--gray-600);
    }

    /* Filters & Actions */
    .filters-section {
        background: var(--white);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-100);
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

    /* Products Table */
    .products-table-section {
        background: var(--white);
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-100);
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

    .products-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .products-table th,
    .products-table td {
        padding: 15px 12px;
        text-align: right;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
    }

    .products-table th {
        background: var(--gray-50);
        font-weight: 600;
        color: var(--gray-700);
        font-size: 14px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .products-table td {
        font-size: 14px;
        color: var(--gray-600);
    }

    .products-table tbody tr:hover {
        background: var(--gray-50);
    }

    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid var(--gray-200);
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-details h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
    }

    .product-details p {
        margin: 2px 0 0 0;
        font-size: 12px;
        color: var(--gray-500);
    }

    .product-sku {
        background: var(--gray-100);
        color: var(--gray-600);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-available {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .status-unavailable {
        background: rgba(239, 68, 68, 0.1);
        color: #DC2626;
    }

    .status-featured {
        background: rgba(255, 107, 53, 0.1);
        color: #EA580C;
    }

    .stock-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .stock-in { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .stock-low { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .stock-out { background: rgba(239, 68, 68, 0.1); color: #DC2626; }

    .price-display {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .current-price {
        font-weight: 600;
        color: var(--gray-800);
        font-size: 14px;
    }

    .original-price {
        text-decoration: line-through;
        color: var(--gray-400);
        font-size: 12px;
    }

    .discount-badge {
        background: var(--danger);
        color: var(--white);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        margin-top: 2px;
    }

    .actions-dropdown {
        position: relative;
        display: inline-block;
    }

    .actions-btn {
        background: none;
        border: none;
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
        color: var(--gray-500);
        transition: all 0.3s ease;
    }

    .actions-btn:hover {
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .actions-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        min-width: 180px;
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

    /* Stock Update Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal {
        background: var(--white);
        border-radius: 16px;
        padding: 25px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .stock-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .stock-action-btn {
        padding: 10px;
        border: 2px solid var(--gray-200);
        background: var(--white);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .stock-action-btn.active {
        border-color: var(--suntop-orange);
        background: rgba(255, 107, 53, 0.1);
        color: var(--suntop-orange);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .products-table {
            font-size: 12px;
        }

        .products-table th,
        .products-table td {
            padding: 10px 8px;
        }

        .product-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .product-image {
            width: 50px;
            height: 50px;
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
<div class="products-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon orange">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3 class="stat-title">إجمالي المنتجات</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-change">{{ $stats['availability_percentage'] }}% متاح</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="stat-title">المنتجات المتاحة</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['available_products']) }}</div>
            <div class="stat-change">من إجمالي {{ number_format($stats['total_products']) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon green">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3 class="stat-title">المنتجات الحديثة</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['recent_products']) }}</div>
            <div class="stat-change">خلال آخر 30 يوم</div>
        </div>


    </div>

    <!-- Filters & Actions -->
    <div class="filters-section">
        <div class="filters-header">
            <h3 class="filters-title">البحث والتصفية</h3>
            <a href="{{ route('admin.products.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                إضافة منتج جديد
            </a>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" id="filtersForm">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">البحث</label>
                    <input type="text" name="search" class="form-input" 
                           placeholder="البحث بالاسم، الوصف، SKU..."
                           value="{{ $search }}">
                </div>



                <div class="form-group">
                    <label class="form-label">الإتاحة</label>
                    <select name="availability" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="available" {{ $availability === 'available' ? 'selected' : '' }}>متاح</option>
                        <option value="unavailable" {{ $availability === 'unavailable' ? 'selected' : '' }}>غير متاح</option>
                    </select>
                </div>



                <div class="form-group">
                    <label class="form-label">نطاق السعر</label>
                    <select name="price_range" class="form-select">
                        <option value="">جميع الأسعار</option>
                        <option value="under_100" {{ $price_range === 'under_100' ? 'selected' : '' }}>أقل من 100 ج.م</option>
                        <option value="100_500" {{ $price_range === '100_500' ? 'selected' : '' }}>100 - 500 ج.م</option>
                        <option value="500_1000" {{ $price_range === '500_1000' ? 'selected' : '' }}>500 - 1000 ج.م</option>
                        <option value="over_1000" {{ $price_range === 'over_1000' ? 'selected' : '' }}>أكثر من 1000 ج.م</option>
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

    <!-- Products Table -->
    <div class="products-table-section">
        <div class="table-header">
            <h3 class="table-title">قائمة المنتجات ({{ $products->total() }})</h3>
            <div class="bulk-actions">
                <select id="bulkAction" class="form-select">
                    <option value="">إجراءات جماعية</option>
                    <option value="activate">تفعيل المحدد</option>
                    <option value="deactivate">إخفاء المحدد</option>
                    <option value="delete">حذف المحدد</option>
                </select>
                <button type="button" class="btn-secondary" onclick="executeBulkAction()">تنفيذ</button>
            </div>
        </div>

        @if($products->count() > 0)
        <div style="overflow-x: auto;">
            <table class="products-table">
                                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAll" onchange="toggleAllProducts()">
                                </th>
                                <th>المنتج</th>
                                <th>الفئة</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                                <th>تاريخ الإضافة</th>
                                <th style="width: 120px;">الإجراءات</th>
                            </tr>
                        </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                        </td>
                        <td>
                            <div class="product-info">
                                <img src="{{ $product->first_image }}" 
                                     alt="صورة المنتج" class="product-image"
                                     onerror="this.src='{{ asset('images/no-product.png') }}'">
                                <div class="product-details">
                                    <h4>{{ $product->name }}</h4>
                                    <p>{{ Str::limit($product->description, 50) }}</p>
                                    <span class="product-id">#{{ $product->id }}</span>
                                    @if($product->back_color)
                                        <div class="product-color" style="background-color: {{ $product->back_color }}; width: 20px; height: 20px; border-radius: 50%; display: inline-block; margin-top: 5px;"></div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td>
                            @if($product->category)
                                <span class="category-badge">{{ $product->category->display_name }}</span>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>

                        <td>
                            <div class="price-display">
                                <span class="current-price">{{ number_format($product->price, 2) }} ج.م</span>
                            </div>
                        </td>

                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="status-badge status-{{ $product->is_available ? 'available' : 'unavailable' }}">
                                    {{ $product->is_available ? 'متاح' : 'غير متاح' }}
                                </span>
                                {{-- إزالة عرض حالة المنتج المميز --}}
                            </div>
                        </td>
                        <td>{{ $product->created_at->format('Y/m/d') }}</td>
                        <td>
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="toggleActionsMenu({{ $product->id }})">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="actions-menu" id="actionsMenu{{ $product->id }}">
                                    <a href="{{ route('admin.products.show', $product->id) }}">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <button class="danger" onclick="deleteProduct({{ $product->id }})">
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
                عرض {{ $products->firstItem() ?? 0 }} إلى {{ $products->lastItem() ?? 0 }} 
                من أصل {{ $products->total() }} نتيجة
            </div>
            {{ $products->appends(request()->query())->links() }}
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-boxes"></i>
            <h3>لا توجد منتجات</h3>
            <p>لم يتم العثور على منتجات تطابق معايير البحث</p>
            <a href="{{ route('admin.products.create') }}" class="btn-primary">
                إضافة أول منتج
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Stock Update Modal -->
<div class="modal-overlay" id="stockModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">تحديث المخزون</h3>
        </div>
        
        <div class="stock-actions">
            <button class="stock-action-btn active" onclick="setStockAction('set')" id="setBtn">
                <i class="fas fa-edit"></i><br>تحديد
            </button>
            <button class="stock-action-btn" onclick="setStockAction('add')" id="addBtn">
                <i class="fas fa-plus"></i><br>إضافة
            </button>
            <button class="stock-action-btn" onclick="setStockAction('subtract')" id="subtractBtn">
                <i class="fas fa-minus"></i><br>خصم
            </button>
        </div>

        <div class="form-group">
            <label class="form-label">الكمية</label>
            <input type="number" id="stockQuantity" class="form-input" min="0" placeholder="أدخل الكمية">
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button class="btn-primary" onclick="updateStock()">حفظ</button>
            <button class="btn-secondary" onclick="closeStockModal()">إلغاء</button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>
@endsection

@push('scripts')
<script>
let currentProductId = null;
let currentStockAction = 'set';

// Actions Menu Toggle
function toggleActionsMenu(productId) {
    const menu = document.getElementById(`actionsMenu${productId}`);
    
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

// Select All Products
function toggleAllProducts() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Toggle Product Availability
async function toggleAvailability(productId) {
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${productId}/toggle-availability`, {
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

// Toggle Product Featured - DISABLED (feature removed)
async function toggleFeatured(productId) {
    showNotification('ميزة المنتجات المميزة لم تعد متاحة', 'error');
}

// Stock Modal Functions
function openStockModal(productId, currentStock) {
    currentProductId = productId;
    document.getElementById('stockQuantity').value = currentStock;
    document.getElementById('stockModal').classList.add('show');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.remove('show');
    currentProductId = null;
}

function setStockAction(action) {
    currentStockAction = action;
    
    // Update button states
    document.querySelectorAll('.stock-action-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(action + 'Btn').classList.add('active');
    
    // Update placeholder
    const input = document.getElementById('stockQuantity');
    switch(action) {
        case 'set':
            input.placeholder = 'أدخل الكمية الجديدة';
            break;
        case 'add':
            input.placeholder = 'أدخل الكمية المراد إضافتها';
            break;
        case 'subtract':
            input.placeholder = 'أدخل الكمية المراد خصمها';
            break;
    }
}

async function updateStock() {
    if (!currentProductId) return;
    
    const quantity = document.getElementById('stockQuantity').value;
    if (!quantity || quantity < 0) {
        showNotification('الرجاء إدخال كمية صحيحة', 'error');
        return;
    }
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${currentProductId}/update-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                stock_quantity: parseInt(quantity),
                action: currentStockAction
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            closeStockModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث المخزون', 'error');
    } finally {
        hideLoading();
    }
}

// Delete Product
async function deleteProduct(productId) {
    if (!confirm('هل أنت متأكد من حذف هذا المنتج؟ هذا الإجراء لا يمكن التراجع عنه.')) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${productId}`, {
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
        showNotification('حدث خطأ أثناء حذف المنتج', 'error');
    } finally {
        hideLoading();
    }
}

// Bulk Actions
async function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
    
    if (!action) {
        showNotification('الرجاء اختيار إجراء', 'error');
        return;
    }
    
    if (selectedProducts.length === 0) {
        showNotification('الرجاء اختيار منتجات على الأقل', 'error');
        return;
    }
    
    const actionText = {
        'activate': 'تفعيل',
        'deactivate': 'إخفاء',
        'feature': 'إضافة للمميزة',
        'unfeature': 'إزالة من المميزة',
        'delete': 'حذف'
    };
    
    if (!confirm(`هل أنت متأكد من ${actionText[action]} ${selectedProducts.length} منتج؟`)) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/bulk-action`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: action,
                product_ids: selectedProducts
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

// Close stock modal when clicking outside
document.getElementById('stockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStockModal();
    }
});
</script>
@endpush
