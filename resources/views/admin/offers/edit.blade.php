@extends('layouts.admin')

@section('title', 'تعديل العرض - ' . $offer->title)

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

    .form-container {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .form-header {
        padding: 25px 30px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .offer-code {
        background: var(--suntop-orange);
        color: var(--white);
        padding: 6px 12px;
        border-radius: 6px;
        font-family: monospace;
        font-weight: 600;
        font-size: 12px;
    }

    .form-content {
        padding: 30px;
    }

    .form-section {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-icon {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 14px;
    }

    .section-icon.orange { background: var(--suntop-orange); }
    .section-icon.info { background: var(--info); }
    .section-icon.success { background: var(--success); }
    .section-icon.warning { background: var(--warning); }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .form-label .required {
        color: var(--danger);
    }

    .form-control {
        padding: 12px 15px;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--white);
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--suntop-orange);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .form-control:invalid {
        border-color: var(--danger);
    }

    .form-help {
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 4px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        background: var(--gray-50);
        border-radius: 8px;
        border: 2px solid var(--gray-200);
        transition: all 0.3s ease;
    }

    .checkbox-group:hover {
        border-color: var(--suntop-orange);
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--suntop-orange);
    }

    .checkbox-group label {
        font-size: 14px;
        font-weight: 500;
        color: var(--gray-700);
        cursor: pointer;
        margin: 0;
    }

    .upload-area {
        border: 2px dashed var(--gray-300);
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        background: var(--gray-50);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .upload-area:hover {
        border-color: var(--suntop-orange);
        background: rgba(255, 107, 53, 0.05);
    }

    .upload-area.dragover {
        border-color: var(--suntop-orange);
        background: rgba(255, 107, 53, 0.1);
    }

    .upload-icon {
        font-size: 48px;
        color: var(--gray-400);
        margin-bottom: 15px;
    }

    .upload-text {
        font-size: 16px;
        color: var(--gray-600);
        margin-bottom: 8px;
    }

    .upload-hint {
        font-size: 12px;
        color: var(--gray-500);
    }

    .current-image {
        margin-bottom: 15px;
        text-align: center;
    }

    .current-image img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .current-image-label {
        font-size: 12px;
        color: var(--gray-600);
        margin-bottom: 10px;
    }

    .preview-container {
        margin-top: 15px;
        display: none;
    }

    .preview-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .multi-select {
        max-height: 200px;
        overflow-y: auto;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        background: var(--white);
    }

    .select-option {
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .select-option:hover {
        background: var(--gray-50);
    }

    .select-option input[type="checkbox"] {
        accent-color: var(--suntop-orange);
    }

    .form-actions {
        padding: 25px 30px;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
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
        border: 2px solid var(--suntop-orange);
    }

    .btn-primary:hover {
        background: #e55a2b;
        border-color: #e55a2b;
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

    .discount-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .discount-option {
        padding: 20px;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .discount-option:hover {
        border-color: var(--suntop-orange);
    }

    .discount-option.selected {
        border-color: var(--suntop-orange);
        background: rgba(255, 107, 53, 0.05);
    }

    .discount-option input[type="radio"] {
        display: none;
    }

    .discount-option-icon {
        font-size: 30px;
        color: var(--gray-400);
        margin-bottom: 10px;
    }

    .discount-option.selected .discount-option-icon {
        color: var(--suntop-orange);
    }

    .discount-option-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 5px;
    }

    .discount-option-desc {
        font-size: 12px;
        color: var(--gray-600);
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            justify-content: center;
        }
        
        .form-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit"></i>
        تعديل العرض
    </h1>
    <p class="page-subtitle">تعديل بيانات العرض الترويجي</p>
</div>

<form action="{{ route('admin.offers.update', $offer) }}" method="POST" enctype="multipart/form-data" class="form-container">
    @csrf
    @method('PUT')
    
    <div class="form-header">
        <h2 class="form-title">
            <i class="fas fa-gift"></i>
            {{ $offer->title }}
        </h2>
        <div class="offer-code">{{ $offer->code }}</div>
    </div>

    <div class="form-content">
        <!-- Basic Information -->
        <div class="form-section">
            <h3 class="section-title">
                <div class="section-icon orange">
                    <i class="fas fa-info-circle"></i>
                </div>
                المعلومات الأساسية
            </h3>
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">
                        عنوان العرض <span class="required">*</span>
                    </label>
                    <input type="text" name="title" class="form-control" 
                           placeholder="مثال: خصم 20% على جميع المنتجات" 
                           value="{{ old('title', $offer->title) }}" required>
                    <div class="form-help">اختر عنواناً جذاباً وواضحاً للعرض</div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">وصف العرض</label>
                    <textarea name="description" class="form-control" rows="3" 
                              placeholder="وصف تفصيلي للعرض وشروط الاستخدام...">{{ old('description', $offer->description) }}</textarea>
                    <div class="form-help">اكتب وصفاً تفصيلياً يوضح تفاصيل العرض</div>
                </div>
            </div>
        </div>

        <!-- Discount Type -->
        <div class="form-section">
            <h3 class="section-title">
                <div class="section-icon info">
                    <i class="fas fa-percentage"></i>
                </div>
                نوع الخصم
            </h3>
            
            <div class="discount-options">
                <div class="discount-option {{ old('type', $offer->type) === 'percentage' ? 'selected' : '' }}" 
                     onclick="selectDiscountType('percentage')">
                    <input type="radio" name="type" value="percentage" 
                           {{ old('type', $offer->type) === 'percentage' ? 'checked' : '' }}>
                    <div class="discount-option-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="discount-option-title">نسبة مئوية</div>
                    <div class="discount-option-desc">خصم بنسبة مئوية من قيمة الطلب</div>
                </div>
                
                <div class="discount-option {{ old('type', $offer->type) === 'fixed_amount' ? 'selected' : '' }}" 
                     onclick="selectDiscountType('fixed_amount')">
                    <input type="radio" name="type" value="fixed_amount" 
                           {{ old('type', $offer->type) === 'fixed_amount' ? 'checked' : '' }}>
                    <div class="discount-option-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="discount-option-title">مبلغ ثابت</div>
                    <div class="discount-option-desc">خصم بمبلغ ثابت من قيمة الطلب</div>
                </div>
            </div>

            <div class="form-grid" style="margin-top: 25px;">
                <div class="form-group" id="percentageGroup">
                    <label class="form-label">نسبة الخصم (%)</label>
                    <input type="number" name="discount_percentage" class="form-control" 
                           min="0" max="100" step="0.01" placeholder="20"
                           value="{{ old('discount_percentage', $offer->discount_percentage) }}">
                    <div class="form-help">نسبة الخصم من 0 إلى 100%</div>
                </div>

                <div class="form-group" id="amountGroup">
                    <label class="form-label">مبلغ الخصم (ج.م)</label>
                    <input type="number" name="discount_amount" class="form-control" 
                           min="0" step="0.01" placeholder="50"
                           value="{{ old('discount_amount', $offer->discount_amount) }}">
                    <div class="form-help">مبلغ الخصم بالجنيه المصري</div>
                </div>

            </div>
        </div>

        <!-- Validity Period -->
        <div class="form-section">
            <h3 class="section-title">
                <div class="section-icon success">
                    <i class="fas fa-calendar"></i>
                </div>
                فترة الصلاحية
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        صالح من <span class="required">*</span>
                    </label>
                    <input type="datetime-local" name="valid_from" class="form-control" 
                           value="{{ old('valid_from', $offer->valid_from->format('Y-m-d\TH:i')) }}" required>
                    <div class="form-help">تاريخ ووقت بداية العرض</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        صالح حتى <span class="required">*</span>
                    </label>
                    <input type="datetime-local" name="valid_until" class="form-control" 
                           value="{{ old('valid_until', $offer->valid_until->format('Y-m-d\TH:i')) }}" required>
                    <div class="form-help">تاريخ ووقت انتهاء العرض</div>
                </div>

            </div>
        </div>

        <!-- Target Settings -->
        <div class="form-section">
            <h3 class="section-title">
                <div class="section-icon warning">
                    <i class="fas fa-users"></i>
                </div>
                إعدادات الاستهداف
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">فئة المستخدمين المستهدفة</label>
                    <select name="user_category_id" class="form-control">
                        <option value="">جميع الفئات</option>
                        @foreach($userCategories as $category)
                        <option value="{{ $category->id }}" {{ old('user_category_id', $offer->user_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->display_name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="form-help">اختر فئة المستخدمين التي تريد استهدافها بهذا العرض</div>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع العرض</label>
                    <input type="text" name="type" class="form-control" 
                           placeholder="مثال: عرض الجمعة البيضاء، عرض العملاء الجدد"
                           value="{{ old('type', $offer->type) }}">
                    <div class="form-help">اكتب نوع أو تصنيف العرض (اختياري)</div>
                </div>

            </div>
        </div>

    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            حفظ التعديلات
        </button>
        <a href="{{ route('admin.offers.show', $offer) }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            إلغاء
        </a>
    </div>
</form>

<script>
// Discount type selection
function selectDiscountType(type) {
    document.querySelectorAll('.discount-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    event.currentTarget.classList.add('selected');
    event.currentTarget.querySelector('input[type="radio"]').checked = true;
    
    // Show/hide relevant fields
    const percentageGroup = document.getElementById('percentageGroup');
    const amountGroup = document.getElementById('amountGroup');
    const maxDiscountGroup = document.getElementById('maxDiscountGroup');
    
    if (type === 'percentage') {
        percentageGroup.style.display = 'block';
        amountGroup.style.display = 'none';
    } else {
        percentageGroup.style.display = 'none';
        amountGroup.style.display = 'block';
    }
}

// Initialize discount type on page load
document.addEventListener('DOMContentLoaded', function() {
    const selectedType = document.querySelector('input[name="type"]:checked').value;
    selectDiscountType(selectedType);
});


// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const type = document.querySelector('input[name="type"]:checked').value;
    
    if (type === 'percentage') {
        const percentage = document.querySelector('input[name="discount_percentage"]').value;
        if (!percentage || percentage <= 0 || percentage > 100) {
            e.preventDefault();
            alert('يرجى إدخال نسبة خصم صحيحة بين 1 و 100');
            return false;
        }
    } else {
        const amount = document.querySelector('input[name="discount_amount"]').value;
        if (!amount || amount <= 0) {
            e.preventDefault();
            alert('يرجى إدخال مبلغ خصم صحيح');
            return false;
        }
    }
    
    const validFrom = new Date(document.querySelector('input[name="valid_from"]').value);
    const validUntil = new Date(document.querySelector('input[name="valid_until"]').value);
    
    if (validFrom >= validUntil) {
        e.preventDefault();
        alert('تاريخ انتهاء العرض يجب أن يكون بعد تاريخ البداية');
        return false;
    }
});
</script>
@endsection
