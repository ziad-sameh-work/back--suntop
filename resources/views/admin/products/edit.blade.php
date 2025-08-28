@extends('layouts.admin')

@section('title', 'تعديل المنتج - ' . $product->name)
@section('page-title', 'تعديل المنتج')

@push('styles')
<style>
    .edit-product-container { max-width: 1000px; margin: 0 auto; }
    .form-card { background: var(--white); border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); margin-bottom: 25px; }
    .form-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--gray-100); }
    .product-avatar { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; border: 3px solid var(--gray-200); }
    .form-header h2 { font-size: 24px; font-weight: 600; color: var(--gray-800); margin: 0; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-label { font-size: 14px; font-weight: 500; color: var(--gray-700); }
    .required { color: var(--danger); }
    .form-input, .form-select, .form-textarea { padding: 12px 15px; border: 2px solid var(--gray-200); border-radius: 10px; font-size: 14px; transition: all 0.3s ease; background: var(--white); font-family: 'Cairo', sans-serif; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--suntop-orange); box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1); }
    .form-textarea { resize: vertical; min-height: 100px; }
    .form-error { font-size: 12px; color: var(--danger); margin-top: 4px; }
    .toggle-switch { position: relative; display: inline-block; width: 50px; height: 26px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-300); transition: 0.3s; border-radius: 26px; }
    .toggle-slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%; }
    input:checked + .toggle-slider { background-color: var(--suntop-orange); }
    input:checked + .toggle-slider:before { transform: translateX(24px); }
    .toggle-group { display: flex; align-items: center; gap: 10px; }
    .form-actions { display: flex; justify-content: center; gap: 15px; padding-top: 20px; border-top: 1px solid var(--gray-100); margin-top: 30px; }
    .btn-primary { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); color: var(--white); border: none; padding: 12px 30px; border-radius: 10px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; text-decoration: none; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3); color: var(--white); text-decoration: none; }
    .btn-secondary { background: var(--gray-100); color: var(--gray-700); border: 2px solid var(--gray-200); padding: 12px 30px; border-radius: 10px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary:hover { background: var(--gray-200); border-color: var(--gray-300); color: var(--gray-700); text-decoration: none; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .form-actions { flex-direction: column; } .edit-product-container { padding: 0 15px; } .form-header { flex-direction: column; text-align: center; } }
</style>
@endpush

@section('content')
<div class="edit-product-container">
    <div class="form-card">
        <div class="form-header">
            <img src="{{ ($product->images && count($product->images) > 0) ? asset($product->images[0]) : asset('images/no-product.png') }}" 
                 alt="صورة المنتج" class="product-avatar"
                 onerror="this.src='{{ asset('images/no-product.png') }}'">
            <div>
                <h2>تعديل: {{ $product->name }}</h2>
                <p style="color: var(--gray-600); margin: 0;">آخر تحديث: {{ $product->updated_at->format('Y/m/d H:i') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" id="editProductForm">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">اسم المنتج <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" 
                           value="{{ old('name', $product->name) }}" required placeholder="أدخل اسم المنتج">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                @if(isset($product->sku))
                <div class="form-group">
                    <label class="form-label">كود المنتج (SKU)</label>
                    <input type="text" name="sku" class="form-input" 
                           value="{{ old('sku', $product->sku) }}" placeholder="كود المنتج">
                    @error('sku')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                @endif

                <div class="form-group">
                    <label class="form-label">السعر <span class="required">*</span></label>
                    <input type="number" name="price" class="form-input" 
                           value="{{ old('price', $product->price) }}" required step="0.01" min="0" placeholder="0.00">
                    @error('price')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                @if(isset($product->discount_price))
                <div class="form-group">
                    <label class="form-label">سعر الخصم</label>
                    <input type="number" name="discount_price" class="form-input" 
                           value="{{ old('discount_price', $product->discount_price) }}" step="0.01" min="0" placeholder="0.00">
                    @error('discount_price')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                @endif

                <div class="form-group">
                    <label class="form-label">كمية المخزون <span class="required">*</span></label>
                    <input type="number" name="stock_quantity" class="form-input" 
                           value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0" placeholder="0">
                    @error('stock_quantity')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                @if(count($merchants) > 0)
                <div class="form-group">
                    <label class="form-label">التاجر</label>
                    <select name="merchant_id" class="form-select">
                        <option value="">اختر التاجر</option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}" {{ old('merchant_id', $product->merchant_id) == $merchant->id ? 'selected' : '' }}>
                                {{ $merchant->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('merchant_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                @endif
                
                @if(count($categories) > 0)
                <div class="form-group">
                    <label class="form-label">فئة المنتج <span class="required">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">اختر فئة المنتج</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->display_name }} ({{ $category->name }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">حدد ما إذا كان المنتج من فئة 1 لتر أو 250 مل</div>
                    @error('category_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                @endif

                <div class="form-group full-width">
                    <label class="form-label">الوصف <span class="required">*</span></label>
                    <textarea name="description" class="form-textarea" 
                              required placeholder="اكتب وصفاً للمنتج">{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">حالة المنتج</label>
                    <div class="toggle-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_available" value="1" 
                                   {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span>المنتج متاح للعرض</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">منتج مميز</label>
                    <div class="toggle-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_featured" value="1" 
                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span>عرض في المنتجات المميزة</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">لون خلفية المنتج</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="color" name="back_color" class="form-input" style="width: 60px; height: 40px; padding: 5px;"
                               value="{{ old('back_color', $product->back_color ?? '#FFFFFF') }}">
                        <input type="text" class="form-input" id="backColorText" style="flex: 1;"
                               value="{{ old('back_color', $product->back_color ?? '#FFFFFF') }}" placeholder="#FFFFFF"
                               oninput="document.querySelector('input[name=back_color]').value = this.value">
                    </div>
                    <div style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">اختر لون خلفية لعرض المنتج في التطبيق</div>
                    @error('back_color')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
                <a href="{{ route('admin.products.show', $product->id) }}" class="btn-secondary">
                    <i class="fas fa-eye"></i> عرض التفاصيل
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">
                    <i class="fas fa-list"></i> العودة للقائمة
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
    
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> حفظ التغييرات';
    }, 10000);
});

@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endpush

