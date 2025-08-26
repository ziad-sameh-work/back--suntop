@extends('layouts.admin')

@section('title', 'تفاصيل التاجر - ' . $merchant->name)
@section('page-title', 'تفاصيل التاجر')

@push('styles')
<style>
    .merchant-details-container { display: grid; gap: 25px; max-width: 1400px; margin: 0 auto; }
    
    /* Header Section */
    .merchant-header { background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%); border-radius: 16px; padding: 30px; color: var(--white); position: relative; overflow: hidden; }
    .merchant-header::before { content: ''; position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" fill-opacity="0.1"/></svg>') repeat; animation: float 20s ease-in-out infinite; }
    @keyframes float { 0%, 100% { transform: translateX(0px) translateY(0px); } 50% { transform: translateX(-20px) translateY(-20px); } }
    .merchant-header-content { position: relative; z-index: 2; display: grid; grid-template-columns: auto 1fr auto; gap: 25px; align-items: center; }
    .merchant-logo-large { width: 100px; height: 100px; border-radius: 12px; object-fit: cover; border: 4px solid rgba(255, 255, 255, 0.3); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); }
    .merchant-info-header h1 { font-size: 32px; font-weight: 700; margin: 0 0 8px 0; }
    .merchant-info-header p { font-size: 16px; opacity: 0.9; margin: 0 0 15px 0; }
    .merchant-badges { display: flex; gap: 10px; flex-wrap: wrap; }
    .badge { padding: 6px 15px; border-radius: 20px; font-size: 14px; font-weight: 500; background: rgba(255, 255, 255, 0.2); }
    .header-actions { display: flex; flex-direction: column; gap: 10px; }
    .btn-white { background: var(--white); color: var(--suntop-orange); border: none; padding: 12px 20px; border-radius: 10px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s ease; cursor: pointer; white-space: nowrap; }
    .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); color: var(--suntop-orange); text-decoration: none; }

    /* Main Grid */
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
    .content-card { background: var(--white); border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); transition: all 0.3s ease; }
    .content-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1); }
    .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--gray-100); }
    .card-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--white); }
    .card-icon.orange { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); }
    .card-icon.blue { background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark)); }
    .card-icon.green { background: linear-gradient(135deg, var(--success), #0D9488); }
    .card-icon.purple { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
    .card-title { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0; }

    /* Merchant Products */
    .products-list { display: flex; flex-direction: column; gap: 15px; }
    .product-item { display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--gray-50); border-radius: 12px; transition: all 0.3s ease; }
    .product-item:hover { background: var(--gray-100); }
    .product-image { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; border: 2px solid var(--gray-200); }
    .product-details { flex: 1; }
    .product-name { font-weight: 600; color: var(--gray-800); margin: 0 0 5px 0; }
    .product-info { font-size: 14px; color: var(--gray-600); margin: 0; }
    .product-price { text-align: left; }
    .product-main-price { font-size: 16px; font-weight: 600; color: var(--gray-800); }
    .product-stock { font-size: 13px; color: var(--gray-600); }

    /* Recent Orders */
    .orders-list { display: flex; flex-direction: column; gap: 15px; }
    .order-item { display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--gray-50); border-radius: 12px; transition: all 0.3s ease; }
    .order-item:hover { background: var(--gray-100); }
    .order-details { flex: 1; }
    .order-id { font-weight: 600; color: var(--suntop-orange); margin: 0 0 3px 0; }
    .order-customer { font-size: 14px; color: var(--gray-600); margin: 0 0 2px 0; }
    .order-date { font-size: 13px; color: var(--gray-500); margin: 0; }
    .order-amount { text-align: left; }
    .order-total { font-size: 16px; font-weight: 600; color: var(--gray-800); }
    .order-status { font-size: 12px; padding: 2px 8px; border-radius: 10px; }

    /* Info Lists */
    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-50); }
    .info-item:last-child { border-bottom: none; }
    .info-label { font-weight: 500; color: var(--gray-600); width: 120px; flex-shrink: 0; }
    .info-value { color: var(--gray-800); flex: 1; }

    /* Statistics */
    .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
    .stat-item { background: var(--gray-50); border-radius: 8px; padding: 15px; text-align: center; }
    .stat-value { font-size: 20px; font-weight: 700; color: var(--suntop-orange); margin: 0 0 5px 0; }
    .stat-label { font-size: 12px; color: var(--gray-600); margin: 0; }

    /* Status badges */
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; text-align: center; }
    .status-active { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .status-inactive { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .status-open { background: rgba(59, 130, 246, 0.1); color: #2563EB; }
    .status-closed { background: rgba(107, 114, 128, 0.1); color: #6B7280; }

    /* Quick Actions */
    .quick-actions { display: grid; gap: 10px; }

    /* Commission Info */
    .commission-info { background: linear-gradient(135deg, #10B981, #059669); color: var(--white); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
    .commission-rate { font-size: 24px; font-weight: 700; margin: 0 0 5px 0; }
    .commission-label { font-size: 14px; opacity: 0.9; margin: 0; }
    .commission-earned { background: rgba(255, 255, 255, 0.2); padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-top: 10px; display: inline-block; }

    /* Full Width Card */
    .full-width-card { grid-column: 1 / -1; }

    /* Responsive */
    @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 768px) {
        .merchant-header-content { grid-template-columns: 1fr; text-align: center; gap: 20px; }
        .merchant-logo-large { width: 80px; height: 80px; }
        .merchant-info-header h1 { font-size: 24px; }
        .header-actions { flex-direction: row; justify-content: center; }
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="merchant-details-container">
    <!-- Merchant Header -->
    <div class="merchant-header">
        <div class="merchant-header-content">
            <img src="{{ $merchant->logo ? asset($merchant->logo) : asset('images/no-merchant.png') }}" 
                 alt="شعار التاجر" class="merchant-logo-large"
                 onerror="this.src='{{ asset('images/no-merchant.png') }}'">
            
            <div class="merchant-info-header">
                <h1>{{ $merchant->name }}</h1>
                <p>{{ $merchant->business_name }} - {{ $merchant->city }}</p>
                <div class="merchant-badges">
                    <span class="badge">{{ $merchantStats['total_products'] }} منتج</span>
                    <span class="badge">{{ $merchantStats['total_orders'] }} طلب</span>
                    <span class="badge">{{ $merchant->commission_rate }}% عمولة</span>
                </div>
            </div>

            <div class="header-actions">
                <a href="{{ route('admin.merchants.analytics', $merchant->id) }}" class="btn-white">
                    <i class="fas fa-chart-line"></i> التحليلات
                </a>
                <a href="{{ route('admin.merchants.edit', $merchant->id) }}" class="btn-white">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <button class="btn-white" onclick="toggleMerchantStatus({{ $merchant->id }}, {{ $merchant->is_active ? 'false' : 'true' }})">
                    <i class="fas fa-{{ $merchant->is_active ? 'ban' : 'check' }}"></i>
                    {{ $merchant->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                </button>
                <button class="btn-white" onclick="toggleOpenStatus({{ $merchant->id }}, {{ $merchant->is_open ? 'false' : 'true' }})">
                    <i class="fas fa-{{ $merchant->is_open ? 'door-closed' : 'door-open' }}"></i>
                    {{ $merchant->is_open ? 'إغلاق المتجر' : 'فتح المتجر' }}
                </button>
                <a href="{{ route('admin.merchants.index') }}" class="btn-white">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- Merchant Information -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon orange">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="card-title">معلومات التاجر</h3>
                </div>

                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">اسم التاجر:</span>
                        <span class="info-value">{{ $merchant->name }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">اسم المحل:</span>
                        <span class="info-value">{{ $merchant->business_name }}</span>
                    </li>
                    @if($merchant->business_type)
                    <li class="info-item">
                        <span class="info-label">نوع النشاط:</span>
                        <span class="info-value">{{ $merchant->business_type }}</span>
                    </li>
                    @endif
                    <li class="info-item">
                        <span class="info-label">البريد الإلكتروني:</span>
                        <span class="info-value">{{ $merchant->email }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">رقم الهاتف:</span>
                        <span class="info-value">{{ $merchant->phone }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">العنوان:</span>
                        <span class="info-value">{{ $merchant->address }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">المدينة:</span>
                        <span class="info-value">{{ $merchant->city }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">تاريخ الانضمام:</span>
                        <span class="info-value">{{ $merchant->created_at->format('Y/m/d') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Merchant Description -->
            @if($merchant->description)
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon blue">
                        <i class="fas fa-align-left"></i>
                    </div>
                    <h3 class="card-title">وصف المحل</h3>
                </div>
                <div style="line-height: 1.6; color: var(--gray-700);">
                    {!! nl2br(e($merchant->description)) !!}
                </div>
            </div>
            @endif

            <!-- Merchant Products -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon purple">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3 class="card-title">منتجات التاجر ({{ $products->count() }})</h3>
                </div>

                @if($products->count() > 0)
                    <div class="products-list">
                        @foreach($products as $product)
                            <div class="product-item">
                                <img src="{{ ($product->images && count($product->images) > 0) ? asset($product->images[0]) : asset('images/no-product.png') }}" 
                                     alt="صورة المنتج" class="product-image"
                                     onerror="this.src='{{ asset('images/no-product.png') }}'">
                                
                                <div class="product-details">
                                    <h4 class="product-name">{{ $product->name }}</h4>
                                    <p class="product-info">
                                        المخزون: {{ $product->stock_quantity }} قطعة
                                        @if(isset($product->sku))
                                            | كود: {{ $product->sku }}
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="product-price">
                                    <div class="product-main-price">{{ number_format($product->price, 2) }} ج.م</div>
                                    <div class="product-stock">
                                        @if($product->stock_quantity > 10)
                                            <span style="color: var(--success);">متوفر</span>
                                        @elseif($product->stock_quantity > 0)
                                            <span style="color: var(--warning);">مخزون منخفض</span>
                                        @else
                                            <span style="color: var(--danger);">غير متوفر</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($merchantStats['total_products'] > 10)
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="{{ route('admin.products.index', ['merchant_id' => $merchant->id]) }}" 
                               style="color: var(--suntop-orange); text-decoration: none;">
                                عرض جميع المنتجات ({{ $merchantStats['total_products'] }})
                            </a>
                        </div>
                    @endif
                @else
                    <p style="text-align: center; color: var(--gray-500); margin: 20px 0;">
                        لا يوجد منتجات لهذا التاجر بعد
                    </p>
                @endif
            </div>

            <!-- Recent Orders -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon green">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="card-title">الطلبات الحديثة ({{ $recentOrders->count() }})</h3>
                </div>

                @if($recentOrders->count() > 0)
                    <div class="orders-list">
                        @foreach($recentOrders as $order)
                            <div class="order-item">
                                <div class="order-details">
                                    <h4 class="order-id">#{{ $order->id }}</h4>
                                    <p class="order-customer">{{ $order->user->name }}</p>
                                    <p class="order-date">{{ $order->created_at->format('Y/m/d H:i') }}</p>
                                </div>
                                
                                <div class="order-amount">
                                    <div class="order-total">{{ number_format($order->total_amount, 2) }} ج.م</div>
                                    <div class="order-status status-{{ $order->status }}" style="
                                        @switch($order->status)
                                            @case('pending') background: rgba(251, 191, 36, 0.1); color: #D97706; @break
                                            @case('confirmed') background: rgba(16, 185, 129, 0.1); color: #059669; @break
                                            @case('processing') background: rgba(255, 107, 53, 0.1); color: #EA580C; @break
                                            @case('shipped') background: rgba(139, 92, 246, 0.1); color: #7C3AED; @break
                                            @case('delivered') background: rgba(34, 197, 94, 0.1); color: #16A34A; @break
                                            @case('cancelled') background: rgba(239, 68, 68, 0.1); color: #DC2626; @break
                                            @default background: var(--gray-100); color: var(--gray-600);
                                        @endswitch
                                    ">
                                        @switch($order->status)
                                            @case('pending') معلق @break
                                            @case('confirmed') مؤكد @break
                                            @case('processing') قيد التجهيز @break
                                            @case('shipped') تم الشحن @break
                                            @case('delivered') تم التسليم @break
                                            @case('cancelled') ملغي @break
                                            @default {{ $order->status }}
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($merchantStats['total_orders'] > 10)
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="{{ route('admin.orders.index', ['merchant_id' => $merchant->id]) }}" 
                               style="color: var(--suntop-orange); text-decoration: none;">
                                عرض جميع الطلبات ({{ $merchantStats['total_orders'] }})
                            </a>
                        </div>
                    @endif
                @else
                    <p style="text-align: center; color: var(--gray-500); margin: 20px 0;">
                        لا توجد طلبات لهذا التاجر بعد
                    </p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Commission Information -->
            <div class="commission-info">
                <div class="commission-rate">{{ $merchant->commission_rate }}%</div>
                <div class="commission-label">نسبة العمولة</div>
                <div class="commission-earned">
                    عمولة مكتسبة: {{ number_format($merchantStats['commission_earned'], 2) }} ج.م
                </div>
            </div>

            <!-- Merchant Statistics -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon green">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="card-title">إحصائيات التاجر</h3>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($merchantStats['total_products']) }}</div>
                        <div class="stat-label">إجمالي المنتجات</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($merchantStats['active_products']) }}</div>
                        <div class="stat-label">منتجات متاحة</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($merchantStats['total_orders']) }}</div>
                        <div class="stat-label">إجمالي الطلبات</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($merchantStats['completed_orders']) }}</div>
                        <div class="stat-label">طلبات مكتملة</div>
                    </div>
                </div>

                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">إجمالي الإيرادات:</span>
                        <span class="info-value">{{ number_format($merchantStats['total_revenue'], 2) }} ج.م</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">إيرادات الشهر:</span>
                        <span class="info-value">{{ number_format($merchantStats['this_month_revenue'], 2) }} ج.م</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">متوسط الطلب:</span>
                        <span class="info-value">{{ number_format($merchantStats['avg_order_value'], 2) }} ج.م</span>
                    </li>
                    @if($merchantStats['last_order_date'])
                    <li class="info-item">
                        <span class="info-label">آخر طلب:</span>
                        <span class="info-value">{{ $merchantStats['last_order_date']->format('Y/m/d') }}</span>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Merchant Status -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon blue">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="card-title">حالة التاجر</h3>
                </div>

                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">حالة التفعيل:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $merchant->is_active ? 'active' : 'inactive' }}">
                                {{ $merchant->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">حالة المتجر:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $merchant->is_open ? 'open' : 'closed' }}">
                                {{ $merchant->is_open ? 'مفتوح' : 'مغلق' }}
                            </span>
                        </span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">تاريخ الانضمام:</span>
                        <span class="info-value">{{ $merchant->created_at->format('Y/m/d') }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">آخر تحديث:</span>
                        <span class="info-value">{{ $merchant->updated_at->format('Y/m/d H:i') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon orange">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="card-title">إجراءات سريعة</h3>
                </div>

                <div class="quick-actions">
                    <button class="btn-primary" onclick="toggleMerchantStatus({{ $merchant->id }}, {{ $merchant->is_active ? 'false' : 'true' }})">
                        <i class="fas fa-{{ $merchant->is_active ? 'ban' : 'check' }}"></i>
                        {{ $merchant->is_active ? 'إلغاء التفعيل' : 'تفعيل التاجر' }}
                    </button>
                    
                    <button class="btn-secondary" onclick="toggleOpenStatus({{ $merchant->id }}, {{ $merchant->is_open ? 'false' : 'true' }})">
                        <i class="fas fa-{{ $merchant->is_open ? 'door-closed' : 'door-open' }}"></i>
                        {{ $merchant->is_open ? 'إغلاق المتجر' : 'فتح المتجر' }}
                    </button>
                    
                    <a href="{{ route('admin.merchants.edit', $merchant->id) }}" class="btn-secondary">
                        <i class="fas fa-edit"></i> تعديل البيانات
                    </a>
                    
                    <button class="btn-secondary" onclick="viewAnalytics({{ $merchant->id }})">
                        <i class="fas fa-chart-line"></i> عرض التحليلات
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle merchant status
async function toggleMerchantStatus(merchantId, newStatus) {
    const action = newStatus === 'true' ? 'تفعيل' : 'إلغاء تفعيل';
    
    if (!confirm(`هل أنت متأكد من ${action} هذا التاجر؟`)) return;
    
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
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث حالة التاجر', 'error');
    }
}

// Toggle open status
async function toggleOpenStatus(merchantId, newStatus) {
    const action = newStatus === 'true' ? 'فتح' : 'إغلاق';
    
    if (!confirm(`هل أنت متأكد من ${action} متجر هذا التاجر؟`)) return;
    
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
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث حالة المتجر', 'error');
    }
}

// View analytics
function viewAnalytics(merchantId) {
    window.location.href = `{{ route('admin.merchants.index') }}/${merchantId}/analytics`;
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
</script>
@endpush
