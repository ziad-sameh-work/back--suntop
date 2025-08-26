@extends('layouts.admin')

@section('title', 'تفاصيل الطلب #' . $order->id)
@section('page-title', 'تفاصيل الطلب #' . $order->id)

@push('styles')
<style>
    .order-details-container { display: grid; gap: 25px; max-width: 1400px; margin: 0 auto; }
    
    /* Header Section */
    .order-header { background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%); border-radius: 16px; padding: 30px; color: var(--white); position: relative; overflow: hidden; }
    .order-header::before { content: ''; position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" fill-opacity="0.1"/></svg>') repeat; animation: float 20s ease-in-out infinite; }
    @keyframes float { 0%, 100% { transform: translateX(0px) translateY(0px); } 50% { transform: translateX(-20px) translateY(-20px); } }
    .order-header-content { position: relative; z-index: 2; display: grid; grid-template-columns: auto 1fr auto; gap: 25px; align-items: center; }
    .order-icon { width: 80px; height: 80px; border-radius: 50%; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 32px; }
    .order-info-header h1 { font-size: 32px; font-weight: 700; margin: 0 0 8px 0; }
    .order-info-header p { font-size: 16px; opacity: 0.9; margin: 0 0 15px 0; }
    .order-badges { display: flex; gap: 10px; flex-wrap: wrap; }
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

    /* Order Items */
    .order-items-list { display: flex; flex-direction: column; gap: 15px; }
    .order-item { display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--gray-50); border-radius: 12px; transition: all 0.3s ease; }
    .order-item:hover { background: var(--gray-100); }
    .item-image { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; border: 2px solid var(--gray-200); }
    .item-details { flex: 1; }
    .item-name { font-weight: 600; color: var(--gray-800); margin: 0 0 5px 0; }
    .item-info { font-size: 14px; color: var(--gray-600); margin: 0; }
    .item-price { text-align: left; }
    .item-unit-price { font-size: 14px; color: var(--gray-600); }
    .item-total-price { font-size: 16px; font-weight: 600; color: var(--gray-800); }

    /* Customer Info */
    .customer-card { background: linear-gradient(135deg, #F8FAFC, #E2E8F0); border: 1px solid var(--gray-200); }
    .customer-avatar { width: 60px; height: 60px; border-radius: 50%; background: var(--suntop-orange); display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 24px; font-weight: bold; }
    .customer-details { flex: 1; }
    .customer-name { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0 0 5px 0; }
    .customer-info-item { font-size: 14px; color: var(--gray-600); margin: 2px 0; }

    /* Order Summary */
    .order-summary { background: linear-gradient(135deg, #10B981, #059669); color: var(--white); border-radius: 12px; padding: 20px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
    .summary-row:last-child { margin-bottom: 0; font-size: 18px; font-weight: 600; padding-top: 15px; border-top: 1px solid rgba(255, 255, 255, 0.2); }
    .summary-label { opacity: 0.9; }
    .summary-value { font-weight: 600; }

    /* Timeline */
    .timeline { position: relative; }
    .timeline::before { content: ''; position: absolute; right: 22px; top: 0; bottom: 0; width: 2px; background: var(--gray-200); }
    .timeline-item { position: relative; padding-right: 60px; margin-bottom: 30px; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-icon { position: absolute; right: 10px; top: 0; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 12px; z-index: 2; }
    .timeline-icon.blue { background: var(--suntop-blue); }
    .timeline-icon.green { background: var(--success); }
    .timeline-icon.orange { background: var(--suntop-orange); }
    .timeline-icon.purple { background: #8B5CF6; }
    .timeline-icon.red { background: var(--danger); }
    .timeline-content { background: var(--white); border: 1px solid var(--gray-200); border-radius: 8px; padding: 15px; }
    .timeline-title { font-weight: 600; color: var(--gray-800); margin: 0 0 5px 0; }
    .timeline-description { font-size: 14px; color: var(--gray-600); margin: 0 0 10px 0; }
    .timeline-time { font-size: 12px; color: var(--gray-500); }

    /* Status badges */
    .status-badge { padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 500; text-align: center; }
    .status-pending { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .status-confirmed { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .status-processing { background: rgba(255, 107, 53, 0.1); color: #EA580C; }
    .status-shipped { background: rgba(139, 92, 246, 0.1); color: #7C3AED; }
    .status-delivered { background: rgba(34, 197, 94, 0.1); color: #16A34A; }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .status-refunded { background: rgba(107, 114, 128, 0.1); color: #6B7280; }

    /* Payment badges */
    .payment-badge { padding: 6px 12px; border-radius: 15px; font-size: 12px; font-weight: 500; }
    .payment-pending { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .payment-paid { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .payment-failed { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .payment-refunded { background: rgba(107, 114, 128, 0.1); color: #6B7280; }

    /* Statistics */
    .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
    .stat-item { background: var(--gray-50); border-radius: 8px; padding: 15px; text-align: center; }
    .stat-value { font-size: 20px; font-weight: 700; color: var(--suntop-orange); margin: 0 0 5px 0; }
    .stat-label { font-size: 12px; color: var(--gray-600); margin: 0; }

    /* Info Lists */
    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-50); }
    .info-item:last-child { border-bottom: none; }
    .info-label { font-weight: 500; color: var(--gray-600); width: 120px; flex-shrink: 0; }
    .info-value { color: var(--gray-800); flex: 1; }

    /* Quick Actions */
    .quick-actions { display: grid; gap: 10px; }

    /* Responsive */
    @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 768px) {
        .order-header-content { grid-template-columns: 1fr; text-align: center; gap: 20px; }
        .order-icon { width: 60px; height: 60px; font-size: 24px; }
        .order-info-header h1 { font-size: 24px; }
        .header-actions { flex-direction: row; justify-content: center; }
        .order-item { flex-direction: column; text-align: center; }
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="order-details-container">
    <!-- Order Header -->
    <div class="order-header">
        <div class="order-header-content">
            <div class="order-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            
            <div class="order-info-header">
                <h1>طلب رقم #{{ $order->id }}</h1>
                <p>تم الإنشاء في {{ $order->created_at->format('Y/m/d H:i') }}</p>
                <div class="order-badges">
                    <span class="badge">{{ $orderStats['total_items'] }} منتج</span>
                    <span class="badge">{{ $orderStats['total_quantity'] }} قطعة</span>
                    <span class="badge">{{ number_format($order->total_amount, 2) }} ج.م</span>
                </div>
            </div>

            <div class="header-actions">
                @if(!in_array($order->status, ['delivered', 'cancelled', 'refunded']))
                    <button class="btn-white" onclick="openStatusModal({{ $order->id }}, '{{ $order->status }}')">
                        <i class="fas fa-edit"></i> تحديث الحالة
                    </button>
                @endif
                @if($order->payment_status !== 'paid')
                    <button class="btn-white" onclick="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')">
                        <i class="fas fa-credit-card"></i> تحديث الدفع
                    </button>
                @endif
                <button class="btn-white" onclick="printOrder({{ $order->id }})">
                    <i class="fas fa-print"></i> طباعة
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn-white">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- Order Items -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon orange">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3 class="card-title">منتجات الطلب ({{ $order->items ? $order->items->count() : 0 }})</h3>
                </div>

                <div class="order-items-list">
                    @foreach($order->items ?? [] as $item)
                        <div class="order-item">
                            <img src="{{ ($item->product && $item->product->images && count($item->product->images) > 0) ? asset($item->product->images[0]) : asset('images/no-product.png') }}" 
                                 alt="صورة المنتج" class="item-image"
                                 onerror="this.src='{{ asset('images/no-product.png') }}'">
                            
                            <div class="item-details">
                                <h4 class="item-name">{{ $item->product->name ?? 'منتج محذوف' }}</h4>
                                <p class="item-info">
                                    الكمية: {{ $item->quantity }} قطعة
                                    @if($item->product && isset($item->product->sku))
                                        | كود المنتج: {{ $item->product->sku }}
                                    @endif
                                </p>
                            </div>
                            
                            <div class="item-price">
                                <div class="item-unit-price">{{ number_format($item->unit_price, 2) }} ج.م / قطعة</div>
                                <div class="item-total-price">{{ number_format($item->unit_price * $item->quantity, 2) }} ج.م</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon purple">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="card-title">تتبع الطلب</h3>
                </div>

                <div class="timeline">
                    @foreach($timeline as $event)
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $event['color'] }}">
                                <i class="{{ $event['icon'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">{{ $event['title'] }}</h4>
                                <p class="timeline-description">{{ $event['description'] }}</p>
                                <div class="timeline-time">{{ $event['timestamp']->format('Y/m/d H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Customer Information -->
            <div class="content-card customer-card">
                <div class="card-header">
                    <div class="card-icon blue">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="card-title">معلومات العميل</h3>
                </div>

                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                    <div class="customer-avatar">
                        {{ strtoupper(substr($order->user->name, 0, 2)) }}
                    </div>
                    <div class="customer-details">
                        <h4 class="customer-name">{{ $order->user->name }}</h4>
                        <div class="customer-info-item">{{ $order->user->email }}</div>
                        @if($order->user->phone)
                            <div class="customer-info-item">{{ $order->user->phone }}</div>
                        @endif
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">{{ $orderStats['customer_orders_count'] }}</div>
                        <div class="stat-label">إجمالي الطلبات</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($orderStats['customer_total_spent'], 0) }}</div>
                        <div class="stat-label">إجمالي المشتريات</div>
                    </div>
                </div>

                @if($order->user->userCategory)
                    <div style="margin-top: 15px; text-align: center;">
                        <span class="status-badge" style="background: rgba(255, 107, 53, 0.1); color: #EA580C;">
                            فئة {{ $order->user->userCategory->display_name }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3 style="margin: 0 0 20px 0; color: white;">ملخص الطلب</h3>
                
                <div class="summary-row">
                    <span class="summary-label">المجموع الفرعي:</span>
                    <span class="summary-value">{{ number_format($order->items ? $order->items->sum(function($item) { return $item->unit_price * $item->quantity; }) : 0, 2) }} ج.م</span>
                </div>
                
                @if($order->category_discount > 0)
                    <div class="summary-row">
                        <span class="summary-label">خصم الفئة:</span>
                        <span class="summary-value">-{{ number_format($order->category_discount, 2) }} ج.م</span>
                    </div>
                @endif
                
                <div class="summary-row">
                    <span class="summary-label">الشحن:</span>
                    <span class="summary-value">{{ number_format($order->shipping_cost ?? 0, 2) }} ج.م</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">المجموع الإجمالي:</span>
                    <span class="summary-value">{{ number_format($order->total_amount, 2) }} ج.م</span>
                </div>
            </div>

            <!-- Order Status -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon green">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="card-title">حالة الطلب</h3>
                </div>

                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">الحالة:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $order->status }}">
                                @switch($order->status)
                                    @case('pending') معلق @break
                                    @case('confirmed') مؤكد @break
                                    @case('processing') قيد التجهيز @break
                                    @case('shipped') تم الشحن @break
                                    @case('delivered') تم التسليم @break
                                    @case('cancelled') ملغي @break
                                    @case('refunded') مسترد @break
                                    @default {{ $order->status }}
                                @endswitch
                            </span>
                        </span>
                    </li>
                    
                    <li class="info-item">
                        <span class="info-label">الدفع:</span>
                        <span class="info-value">
                            <span class="payment-badge payment-{{ $order->payment_status }}">
                                @switch($order->payment_status)
                                    @case('pending') معلق @break
                                    @case('paid') مدفوع @break
                                    @case('failed') فشل @break
                                    @case('refunded') مسترد @break
                                    @default {{ $order->payment_status }}
                                @endswitch
                            </span>
                        </span>
                    </li>
                    
                    @if($order->paid_at)
                    <li class="info-item">
                        <span class="info-label">تاريخ الدفع:</span>
                        <span class="info-value">{{ $order->paid_at->format('Y/m/d H:i') }}</span>
                    </li>
                    @endif
                    
                    @if($order->cancelled_at)
                    <li class="info-item">
                        <span class="info-label">تاريخ الإلغاء:</span>
                        <span class="info-value">{{ $order->cancelled_at->format('Y/m/d H:i') }}</span>
                    </li>
                    @endif
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
                    @if(!in_array($order->status, ['delivered', 'cancelled', 'refunded']))
                        <button class="btn-primary" onclick="quickStatusUpdate('{{ $order->id }}', 'confirmed')">
                            <i class="fas fa-check"></i> تأكيد الطلب
                        </button>
                        <button class="btn-primary" onclick="quickStatusUpdate('{{ $order->id }}', 'processing')">
                            <i class="fas fa-cog"></i> بدء التجهيز
                        </button>
                        <button class="btn-primary" onclick="quickStatusUpdate('{{ $order->id }}', 'shipped')">
                            <i class="fas fa-truck"></i> تم الشحن
                        </button>
                        <button class="btn-secondary" onclick="cancelOrder({{ $order->id }})">
                            <i class="fas fa-times"></i> إلغاء الطلب
                        </button>
                    @endif
                    
                    @if($order->payment_status === 'pending')
                        <button class="btn-secondary" onclick="quickPaymentUpdate('{{ $order->id }}', 'paid')">
                            <i class="fas fa-credit-card"></i> تأكيد الدفع
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include modals from orders index -->
@include('admin.orders.modals')
@endsection

@push('scripts')
<script>
// Include script functions from orders index
async function quickStatusUpdate(orderId, status) {
    if (!confirm(`هل أنت متأكد من تحديث حالة الطلب إلى "${getStatusText(status)}"؟`)) return;
    
    try {
        const response = await fetch(`{{ route('admin.orders.index') }}/${orderId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: status,
                notes: `تحديث سريع إلى ${getStatusText(status)}`
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء تحديث الحالة');
    }
}

async function quickPaymentUpdate(orderId, status) {
    if (!confirm('هل أنت متأكد من تأكيد الدفع؟')) return;
    
    try {
        const response = await fetch(`{{ route('admin.orders.index') }}/${orderId}/update-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_status: status,
                payment_notes: 'تأكيد دفع سريع من الإدارة'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء تحديث حالة الدفع');
    }
}

function getStatusText(status) {
    const statusTexts = {
        'pending': 'معلق',
        'confirmed': 'مؤكد',
        'processing': 'قيد التجهيز',
        'shipped': 'تم الشحن',
        'delivered': 'تم التسليم',
        'cancelled': 'ملغي',
        'refunded': 'مسترد'
    };
    return statusTexts[status] || status;
}

// Print function
function printOrder(orderId) {
    window.open(`{{ route('admin.orders.index') }}/${orderId}/print`, '_blank');
}

// Modal functions (if needed)
let currentOrderId = {{ $order->id }};

function openStatusModal(orderId, currentStatus) {
    // Implementation similar to orders index
    alert('تحديث الحالة - هذه الوظيفة قيد التطوير');
}

function openPaymentModal(orderId, currentStatus) {
    // Implementation similar to orders index
    alert('تحديث الدفع - هذه الوظيفة قيد التطوير');
}

async function cancelOrder(orderId) {
    const reason = prompt('الرجاء إدخال سبب الإلغاء:');
    if (!reason) return;
    
    if (!confirm('هل أنت متأكد من إلغاء هذا الطلب؟ سيتم إعادة المخزون تلقائياً.')) return;
    
    try {
        const response = await fetch(`{{ route('admin.orders.index') }}/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                cancellation_reason: reason
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء إلغاء الطلب');
    }
}
</script>
@endpush
