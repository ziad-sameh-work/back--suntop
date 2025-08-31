@extends('layouts.admin')

@section('title', 'تفاصيل المنتج - ' . $product->name)
@section('page-title', 'تفاصيل المنتج')

@push('styles')
<style>
    .product-details-container { display: grid; gap: 25px; }
    .product-header {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark));
        border-radius: 16px; padding: 30px; color: var(--white); position: relative; overflow: hidden;
    }
    .product-header-content { position: relative; z-index: 2; display: grid; grid-template-columns: auto 1fr auto; gap: 25px; align-items: center; }
    .product-main-image { width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 4px solid rgba(255, 255, 255, 0.3); }
    .product-name { font-size: 32px; font-weight: 700; margin: 0 0 8px 0; }
    .product-description { font-size: 16px; opacity: 0.9; margin: 0 0 15px 0; line-height: 1.5; }
    .product-badges { display: flex; gap: 10px; flex-wrap: wrap; }
    .badge { padding: 6px 15px; border-radius: 20px; font-size: 14px; font-weight: 500; background: rgba(255, 255, 255, 0.2); }
    .header-actions { display: flex; flex-direction: column; gap: 10px; }
    .btn-white { background: var(--white); color: var(--suntop-orange); border: none; padding: 12px 20px; border-radius: 10px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s ease; cursor: pointer; white-space: nowrap; }
    .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); color: var(--suntop-orange); text-decoration: none; }
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
    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-50); }
    .info-item:last-child { border-bottom: none; }
    .info-label { font-weight: 500; color: var(--gray-600); width: 120px; flex-shrink: 0; }
    .info-value { color: var(--gray-800); flex: 1; }
    .price-section { background: linear-gradient(135deg, #10B981, #059669); color: var(--white); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
    .price-main { font-size: 28px; font-weight: 700; margin: 0; }
    .price-original { font-size: 18px; text-decoration: line-through; opacity: 0.7; margin-top: 5px; }
    .price-discount { background: rgba(255, 255, 255, 0.2); padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-top: 10px; display: inline-block; }
    .stock-status { text-align: center; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
    .stock-in { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .stock-low { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .stock-out { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .stock-quantity { font-size: 24px; font-weight: 700; margin: 0 0 5px 0; }
    .stock-label { font-size: 14px; opacity: 0.8; }
    .image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin-top: 15px; }
    .gallery-image { width: 100%; aspect-ratio: 1; border-radius: 8px; object-fit: cover; border: 2px solid var(--gray-200); cursor: pointer; transition: all 0.3s ease; }
    .gallery-image:hover { transform: scale(1.05); border-color: var(--suntop-orange); }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
    .stat-card-small { background: var(--gray-50); border-radius: 12px; padding: 20px; text-align: center; transition: all 0.3s ease; }
    .stat-card-small:hover { background: var(--gray-100); transform: translateY(-2px); }
    .stat-value-small { font-size: 24px; font-weight: 700; color: var(--suntop-orange); margin: 0 0 5px 0; }
    .stat-label-small { font-size: 14px; color: var(--gray-600); margin: 0; }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
    .status-available { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .status-unavailable { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .status-featured { background: rgba(255, 107, 53, 0.1); color: #EA580C; }
    @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 768px) { 
        .product-header-content { grid-template-columns: 1fr; text-align: center; gap: 20px; }
        .product-main-image { width: 100px; height: 100px; }
        .product-name { font-size: 24px; }
        .header-actions { flex-direction: row; justify-content: center; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')
<div class="product-details-container">
    <!-- Product Header -->
    <div class="product-header">
        <div class="product-header-content">
            <img src="{{ $product->first_image }}" 
                 alt="صورة المنتج" class="product-main-image"
                 onerror="this.src='{{ asset('images/no-product.png') }}'">
            
            <div class="product-info-header">
                <h1 class="product-name">{{ $product->name }}</h1>
                <p class="product-description">{{ $product->short_description ?? Str::limit($product->description, 150) }}</p>
                <div class="product-badges">
                    <span class="badge">{{ $product->is_available ? 'متاح' : 'غير متاح' }}</span>
                    @if($product->is_featured)<span class="badge">مميز</span>@endif
                    @if(isset($product->sku))<span class="badge">{{ $product->sku }}</span>@endif
                </div>
            </div>

            <div class="header-actions">
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-white">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <button class="btn-white" onclick="toggleAvailability({{ $product->id }})">
                    <i class="fas fa-{{ $product->is_available ? 'eye-slash' : 'eye' }}"></i>
                    {{ $product->is_available ? 'إخفاء' : 'إظهار' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn-white">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- Product Information -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon orange"><i class="fas fa-info-circle"></i></div>
                    <h3 class="card-title">معلومات المنتج</h3>
                </div>
                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">اسم المنتج:</span>
                        <span class="info-value">{{ $product->name }}</span>
                    </li>
                    @if(isset($product->sku))
                    <li class="info-item">
                        <span class="info-label">كود المنتج:</span>
                        <span class="info-value">{{ $product->sku }}</span>
                    </li>
                    @endif

                    <li class="info-item">
                        <span class="info-label">تاريخ الإضافة:</span>
                        <span class="info-value">{{ $product->created_at->format('Y/m/d H:i') }}</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">آخر تحديث:</span>
                        <span class="info-value">{{ $product->updated_at->format('Y/m/d H:i') }}</span>
                    </li>
                    @if(isset($product->weight) && $product->weight)
                    <li class="info-item">
                        <span class="info-label">الوزن:</span>
                        <span class="info-value">{{ $product->weight }} كجم</span>
                    </li>
                    @endif
                    @if(isset($product->dimensions) && $product->dimensions)
                    <li class="info-item">
                        <span class="info-label">الأبعاد:</span>
                        <span class="info-value">{{ $product->dimensions }}</span>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Product Description -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon blue"><i class="fas fa-align-left"></i></div>
                    <h3 class="card-title">وصف المنتج</h3>
                </div>
                <div style="line-height: 1.6; color: var(--gray-700);">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

            <!-- Product Images -->
            @if(isset($product->images) && count($product->images) > 0)
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon purple"><i class="fas fa-images"></i></div>
                    <h3 class="card-title">صور المنتج ({{ count($product->images) }})</h3>
                </div>
                <div class="image-gallery">
                    @foreach($product->images as $image)
                        <img src="{{ asset($image) }}" alt="صورة المنتج" class="gallery-image"
                             onclick="openImageModal('{{ asset($image) }}')"
                             onerror="this.src='{{ asset('images/no-product.png') }}'">
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Price Information -->
            <div class="price-section">
                <div class="price-main">
                    @if(isset($product->discount_price) && $product->discount_price && $product->discount_price < $product->price)
                        {{ number_format($product->discount_price, 2) }} ج.م
                    @else
                        {{ number_format($product->price, 2) }} ج.م
                    @endif
                </div>
                @if(isset($product->discount_price) && $product->discount_price && $product->discount_price < $product->price)
                    <div class="price-original">{{ number_format($product->price, 2) }} ج.م</div>
                    <div class="price-discount">
                        خصم {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                        (توفير {{ number_format($product->price - $product->discount_price, 2) }} ج.م)
                    </div>
                @endif
            </div>



            <!-- Product Statistics -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon green"><i class="fas fa-chart-bar"></i></div>
                    <h3 class="card-title">إحصائيات المنتج</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-card-small">
                        <div class="stat-value-small">{{ number_format($productStats['total_sales']) }}</div>
                        <div class="stat-label-small">إجمالي المبيعات</div>
                    </div>
                    <div class="stat-card-small">
                        <div class="stat-value-small">{{ number_format($productStats['total_revenue'], 2) }}</div>
                        <div class="stat-label-small">إجمالي الإيرادات</div>
                    </div>

                </div>
                <ul class="info-list">
                    @if(isset($product->min_quantity) && $product->min_quantity)
                    <li class="info-item">
                        <span class="info-label">الحد الأدنى:</span>
                        <span class="info-value">{{ $product->min_quantity }} قطعة</span>
                    </li>
                    @endif
                    <li class="info-item">
                        <span class="info-label">الحالة:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $product->is_available ? 'available' : 'unavailable' }}">
                                {{ $product->is_available ? 'متاح' : 'غير متاح' }}
                            </span>
                        </span>
                    </li>
                    {{-- إزالة عرض حالة المنتج المميز --}}
                </ul>
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon orange"><i class="fas fa-cog"></i></div>
                    <h3 class="card-title">إجراءات سريعة</h3>
                </div>
                <div style="display: grid; gap: 10px;">
                    <button class="btn-primary" onclick="openStockModal({{ $product->id }}, {{ $product->stock_quantity }})">
                        <i class="fas fa-boxes"></i> تحديث المخزون
                    </button>
                    <button class="btn-secondary" onclick="toggleFeatured({{ $product->id }})">
                        <i class="fas fa-{{ $product->is_featured ? 'star-half-alt' : 'star' }}"></i>
                        {{ $product->is_featured ? 'إزالة من المميزة' : 'إضافة للمميزة' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentProductId = {{ $product->id }};

async function toggleAvailability(productId) {
    if (!confirm('هل أنت متأكد من تغيير حالة المنتج؟')) return;
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${productId}/toggle-availability`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء تحديث الحالة');
    }
}

async function toggleFeatured(productId) {
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${productId}/toggle-featured`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء تحديث الحالة');
    }
}

function openImageModal(imageSrc) {
    window.open(imageSrc, '_blank');
}

function openStockModal(productId, currentStock) {
    const newStock = prompt('أدخل الكمية الجديدة:', currentStock);
    if (newStock !== null && newStock !== '') {
        updateStock(productId, newStock);
    }
}

async function updateStock(productId, quantity) {
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${productId}/update-stock`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ stock_quantity: parseInt(quantity), action: 'set' })
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء تحديث المخزون');
    }
}
</script>
@endpush