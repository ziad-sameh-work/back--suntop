@extends('layouts.admin')

@section('title', 'تعديل فئة المستخدمين')

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

    .breadcrumb {
        background: none;
        padding: 0;
        margin: 0 0 15px 0;
    }

    .breadcrumb-item {
        display: inline-flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.8);
        font-size: 14px;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        margin: 0 10px;
        color: rgba(255, 255, 255, 0.6);
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: var(--white);
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: var(--white);
    }

    .form-section {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
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

    .section-body {
        padding: 25px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .required {
        color: var(--danger);
    }

    .form-control {
        padding: 12px 16px;
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

    .form-help {
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 4px;
    }

    .input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .input-group .form-control {
        flex: 1;
    }

    .input-addon {
        padding: 12px 16px;
        background: var(--gray-100);
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 14px;
        color: var(--gray-600);
        white-space: nowrap;
    }

    .benefits-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .benefit-item {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .benefit-item .form-control {
        flex: 1;
    }

    .btn-icon {
        width: 44px;
        height: 44px;
        border: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-add {
        background: var(--success);
        color: var(--white);
    }

    .btn-add:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-remove {
        background: var(--danger);
        color: var(--white);
    }

    .btn-remove:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .switch-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--gray-300);
        transition: .4s;
        border-radius: 30px;
    }

    .switch-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        right: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .switch-slider {
        background-color: var(--suntop-orange);
    }

    input:checked + .switch-slider:before {
        transform: translateX(-30px);
    }

    .switch-label {
        font-size: 14px;
        color: var(--gray-700);
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        padding: 25px;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
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

    .btn-warning {
        background: var(--warning);
        color: var(--white);
    }

    .btn-warning:hover {
        background: #d97706;
        color: var(--white);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.2);
        color: var(--danger);
    }

    .alert-warning {
        background: rgba(245, 158, 11, 0.1);
        border-color: rgba(245, 158, 11, 0.2);
        color: var(--warning);
    }

    .alert-info {
        background: rgba(59, 130, 246, 0.1);
        border-color: rgba(59, 130, 246, 0.2);
        color: var(--info);
    }

    .preview-section {
        background: var(--gray-50);
        border: 2px dashed var(--gray-300);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }

    .preview-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(255, 107, 53, 0.1);
        color: var(--suntop-orange);
        border: 1px solid rgba(255, 107, 53, 0.2);
        margin-bottom: 10px;
    }

    .preview-details {
        color: var(--gray-600);
        font-size: 14px;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .stats-info {
        background: var(--info);
        color: var(--white);
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
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
    }
</style>

<div class="page-header">
    <nav class="breadcrumb">
        <div class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fas fa-home"></i>
                الرئيسية
            </a>
        </div>
        <div class="breadcrumb-item">
            <a href="{{ route('admin.user-categories.index') }}">فئات المستخدمين</a>
        </div>
        <div class="breadcrumb-item">
            <a href="{{ route('admin.user-categories.show', $userCategory->id) }}">{{ $userCategory->display_name }}</a>
        </div>
        <div class="breadcrumb-item active">تعديل</div>
    </nav>
    
    <h1 class="page-title">
        <i class="fas fa-edit"></i>
        تعديل فئة: {{ $userCategory->display_name }}
    </h1>
    <p class="page-subtitle">تعديل معلومات ومتطلبات فئة المستخدمين</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <strong>يرجى تصحيح الأخطاء التالية:</strong>
            <ul style="margin: 5px 0 0 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="stats-info">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong>معلومات:</strong>
        هذه الفئة تحتوي على {{ $userCategory->users_count ?? 0 }} مستخدم. 
        سيتم إعادة حساب فئات جميع المستخدمين تلقائياً بعد حفظ التغييرات.
    </div>
</div>

<form method="POST" action="{{ route('admin.user-categories.update', $userCategory->id) }}" id="categoryForm">
    @csrf
    @method('PUT')
    
    <!-- Basic Information -->
    <div class="form-section">
        <div class="section-header">
            <h3 class="section-title">
                <div class="section-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                المعلومات الأساسية
            </h3>
        </div>
        <div class="section-body">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">
                        رمز الفئة <span class="required">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="{{ old('name', $userCategory->name) }}" placeholder="A, B, C, VIP..."
                           maxlength="10" required>
                    <div class="form-help">رمز مختصر للفئة (مثل A, B, C أو VIP)</div>
                </div>

                <div class="form-group">
                    <label for="sort_order">
                        ترتيب الفئة <span class="required">*</span>
                    </label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" 
                           value="{{ old('sort_order', $userCategory->sort_order) }}" min="0" required>
                    <div class="form-help">ترتيب الفئة في القائمة (الأصغر أولاً)</div>
                </div>

                <div class="form-group full-width">
                    <label for="display_name">
                        اسم الفئة بالعربية <span class="required">*</span>
                    </label>
                    <input type="text" id="display_name" name="display_name" class="form-control" 
                           value="{{ old('display_name', $userCategory->display_name) }}" placeholder="فئة A - عملاء مميزين"
                           required>
                    <div class="form-help">الاسم الذي سيظهر للمستخدمين</div>
                </div>

                <div class="form-group full-width">
                    <label for="display_name_en">
                        اسم الفئة بالإنجليزية
                    </label>
                    <input type="text" id="display_name_en" name="display_name_en" class="form-control" 
                           value="{{ old('display_name_en', $userCategory->display_name_en) }}" placeholder="Category A - Premium Customers">
                    <div class="form-help">الاسم بالإنجليزية (اختياري)</div>
                </div>

                <div class="form-group full-width">
                    <label for="description">
                        وصف الفئة
                    </label>
                    <textarea id="description" name="description" class="form-control" rows="3" 
                              placeholder="وصف تفصيلي للفئة ومميزاتها...">{{ old('description', $userCategory->description) }}</textarea>
                    <div class="form-help">وصف مفصل لهذه الفئة ومن يمكنه الانضمام إليها</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Range -->
    <div class="form-section">
        <div class="section-header">
            <h3 class="section-title">
                <div class="section-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                متطلبات الكراتين ونقاط الولاء
            </h3>
        </div>
        <div class="section-body">
            @if($userCategory->users_count > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>تحذير:</strong>
                        تغيير متطلبات الكراتين ونقاط الولاء قد يؤثر على {{ $userCategory->users_count }} مستخدم في هذه الفئة.
                        سيتم إعادة تصنيفهم تلقائياً حسب المتطلبات الجديدة.
                    </div>
                </div>
            @endif

            <div class="form-grid">
                <!-- Carton Requirements -->
                <div class="form-group">
                    <label for="min_cartons">
                        الحد الأدنى للكراتين <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" id="min_cartons" name="min_cartons" 
                               class="form-control" value="{{ old('min_cartons', $userCategory->min_cartons) }}" 
                               min="0" step="1" required>
                        <div class="input-addon">كرتون</div>
                    </div>
                    <div class="form-help">أقل عدد كراتين مطلوب للانضمام لهذه الفئة</div>
                </div>

                <div class="form-group">
                    <label for="max_cartons">
                        الحد الأقصى للكراتين
                    </label>
                    <div class="input-group">
                        <input type="number" id="max_cartons" name="max_cartons" 
                               class="form-control" value="{{ old('max_cartons', $userCategory->max_cartons) }}" 
                               min="0" step="1">
                        <div class="input-addon">كرتون</div>
                    </div>
                    <div class="form-help">أعلى عدد كراتين لهذه الفئة (اتركه فارغاً لعدم وجود حد أقصى)</div>
                </div>

                <!-- Loyalty Points Configuration -->
                <div class="form-group">
                    <label for="carton_loyalty_points">
                        نقاط لكل كرتون <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" id="carton_loyalty_points" name="carton_loyalty_points" 
                               class="form-control" value="{{ old('carton_loyalty_points', $userCategory->carton_loyalty_points ?? 10) }}" 
                               min="1" step="1" required>
                        <div class="input-addon">نقطة</div>
                    </div>
                    <div class="form-help">عدد نقاط الولاء الأساسية التي يحصل عليها المستخدم لكل كرتون</div>
                </div>

                <div class="form-group">
                    <label for="bonus_points_per_carton">
                        نقاط إضافية لكل كرتون
                    </label>
                    <div class="input-group">
                        <input type="number" id="bonus_points_per_carton" name="bonus_points_per_carton" 
                               class="form-control" value="{{ old('bonus_points_per_carton', $userCategory->bonus_points_per_carton ?? 0) }}" 
                               min="0" step="1">
                        <div class="input-addon">نقطة</div>
                    </div>
                    <div class="form-help">نقاط إضافية يحصل عليها أعضاء هذه الفئة لكل كرتون (مكافأة فئة)</div>
                </div>

                <div class="form-group">
                    <label for="monthly_bonus_points">
                        المكافأة الشهرية
                    </label>
                    <div class="input-group">
                        <input type="number" id="monthly_bonus_points" name="monthly_bonus_points" 
                               class="form-control" value="{{ old('monthly_bonus_points', $userCategory->monthly_bonus_points ?? 0) }}" 
                               min="0" step="1">
                        <div class="input-addon">نقطة</div>
                    </div>
                    <div class="form-help">نقاط يحصل عليها أعضاء هذه الفئة شهرياً</div>
                </div>

                <div class="form-group">
                    <label for="signup_bonus_points">
                        مكافأة الانضمام للفئة
                    </label>
                    <div class="input-group">
                        <input type="number" id="signup_bonus_points" name="signup_bonus_points" 
                               class="form-control" value="{{ old('signup_bonus_points', $userCategory->signup_bonus_points ?? 0) }}" 
                               min="0" step="1">
                        <div class="input-addon">نقطة</div>
                    </div>
                    <div class="form-help">نقاط يحصل عليها المستخدم عند الانضمام لهذه الفئة لأول مرة</div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="has_points_multiplier" name="has_points_multiplier" 
                               value="1" {{ old('has_points_multiplier', $userCategory->has_points_multiplier ?? false) ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        تفعيل مضاعف النقاط
                    </label>
                    <div class="form-help">إذا تم تفعيلها، ستضاعف جميع النقاط المكتسبة</div>
                </div>

                <div class="form-group" id="multiplier_group" style="display: {{ ($userCategory->has_points_multiplier ?? false) ? 'block' : 'none' }};">
                    <label for="points_multiplier">
                        مضاعف النقاط
                    </label>
                    <div class="input-group">
                        <input type="number" id="points_multiplier" name="points_multiplier" 
                               class="form-control" value="{{ old('points_multiplier', $userCategory->points_multiplier ?? 1.5) }}" 
                               min="1" max="10" step="0.1">
                        <div class="input-addon">x</div>
                    </div>
                    <div class="form-help">قيمة المضاعف (مثال: 1.5 يعني زيادة 50%، 2.0 يعني مضاعفة النقاط)</div>
                </div>

                <!-- Purchase Requirements -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="requires_carton_purchase" name="requires_carton_purchase" 
                               value="1" {{ old('requires_carton_purchase', $userCategory->requires_carton_purchase) ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        يجب شراء كراتين كاملة فقط
                    </label>
                    <div class="form-help">إذا تم تفعيلها، لن يتمكن المستخدم من شراء قطع فردية</div>
                </div>

                <!-- Legacy fields for compatibility -->
                <input type="hidden" name="min_packages" value="{{ $userCategory->min_packages ?? 0 }}">
                <input type="hidden" name="max_packages" value="{{ $userCategory->max_packages ?? '' }}">
                <input type="hidden" name="package_discount_percentage" value="{{ $userCategory->package_discount_percentage ?? 0 }}">
                <input type="hidden" name="unit_discount_percentage" value="{{ $userCategory->unit_discount_percentage ?? 0 }}">
                <input type="hidden" name="requires_package_purchase" value="{{ $userCategory->requires_package_purchase ?? 0 }}">
                <input type="hidden" name="min_purchase_amount" value="{{ $userCategory->min_purchase_amount ?? 0 }}">
                <input type="hidden" name="max_purchase_amount" value="{{ $userCategory->max_purchase_amount ?? '' }}">
                <input type="hidden" id="discount_percentage" name="discount_percentage" value="{{ $userCategory->discount_percentage ?? 0 }}">



                <div class="form-group">
                    <label>
                        حالة الفئة
                    </label>
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $userCategory->is_active) ? 'checked' : '' }}>
                            <span class="switch-slider"></span>
                        </label>
                        <span class="switch-label">فعال</span>
                    </div>
                    <div class="form-help">هل هذه الفئة نشطة ويمكن تطبيقها على المستخدمين؟</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits -->
    <div class="form-section">
        <div class="section-header">
            <h3 class="section-title">
                <div class="section-icon">
                    <i class="fas fa-gift"></i>
                </div>
                مزايا الفئة
            </h3>
        </div>
        <div class="section-body">
            <div class="form-group">
                <label>المزايا الإضافية</label>
                <div class="benefits-container" id="benefitsContainer">
                    @php
                        $benefits = old('benefits', $userCategory->benefits ?? []);
                        if(empty($benefits)) $benefits = [''];
                    @endphp
                    @foreach($benefits as $index => $benefit)
                        <div class="benefit-item">
                            <input type="text" name="benefits[]" class="form-control" 
                                   value="{{ $benefit }}" placeholder="مزية للعملاء...">
                            <button type="button" class="btn-icon btn-remove" onclick="removeBenefit(this)">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn-icon btn-add" onclick="addBenefit()" style="margin-top: 10px;">
                    <i class="fas fa-plus"></i>
                </button>
                <div class="form-help">المزايا الإضافية التي يحصل عليها أعضاء هذه الفئة</div>
            </div>
        </div>
    </div>

    <!-- Preview -->
    <div class="form-section">
        <div class="section-header">
            <h3 class="section-title">
                <div class="section-icon">
                    <i class="fas fa-eye"></i>
                </div>
                معاينة الفئة
            </h3>
        </div>
        <div class="section-body">
            <div class="preview-section" id="categoryPreview">
                <div class="preview-badge" id="previewBadge">
                    {{ $userCategory->name }} - {{ $userCategory->display_name }}
                </div>
                <div class="preview-details" id="previewDetails">
                    <div>📦 كراتين: {{ $userCategory->min_cartons }}{{ $userCategory->max_cartons ? ' - '.$userCategory->max_cartons : '+' }} كرتون</div>
                    <div>⭐ {{ $userCategory->carton_loyalty_points ?? 10 }} نقطة لكل كرتون{{ ($userCategory->bonus_points_per_carton ?? 0) > 0 ? ' + '.$userCategory->bonus_points_per_carton.' إضافية' : '' }}{{ (($userCategory->has_points_multiplier ?? false) && ($userCategory->points_multiplier ?? 1) > 1) ? ' (مضاعف '.$userCategory->points_multiplier.'x)' : '' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('admin.user-categories.show', $userCategory->id) }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            إلغاء
        </a>
        <button type="button" class="btn btn-warning" onclick="recalculateAfterSave()">
            <i class="fas fa-sync-alt"></i>
            حفظ وإعادة حساب الفئات
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            حفظ التغييرات
        </button>
    </div>
</form>

<script>
// Benefits management
function addBenefit() {
    const container = document.getElementById('benefitsContainer');
    const benefitItem = document.createElement('div');
    benefitItem.className = 'benefit-item';
    benefitItem.innerHTML = `
        <input type="text" name="benefits[]" class="form-control" placeholder="مزية للعملاء...">
        <button type="button" class="btn-icon btn-remove" onclick="removeBenefit(this)">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(benefitItem);
}

function removeBenefit(button) {
    const container = document.getElementById('benefitsContainer');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}

// Real-time preview
function updatePreview() {
    const name = document.getElementById('name').value || '{{ $userCategory->name }}';
    const displayName = document.getElementById('display_name').value || '{{ $userCategory->display_name }}';
    const minAmount = document.getElementById('min_purchase_amount').value || '0';
    const maxAmount = document.getElementById('max_purchase_amount').value;
    const discount = document.getElementById('discount_percentage').value || '0';
    
    // Update badge
    document.getElementById('previewBadge').textContent = name + ' - ' + displayName;
    
    // Update details
    const rangeText = maxAmount ? 
        `${parseFloat(minAmount).toLocaleString()} - ${parseFloat(maxAmount).toLocaleString()}` : 
        `${parseFloat(minAmount).toLocaleString()} - غير محدد`;
    
    document.getElementById('previewDetails').innerHTML = `
        <div>نطاق الشراء: ${rangeText} ج.م</div>
        <div>خصم: ${parseFloat(discount)}%</div>
    `;
}

// Real-time preview
function updatePreview() {
    const name = document.getElementById('name').value || 'فئة جديدة';
    const displayName = document.getElementById('display_name').value || 'فئة جديدة';
    const minCartons = document.getElementById('min_cartons').value || '0';
    const maxCartons = document.getElementById('max_cartons').value;
    const loyaltyPoints = document.getElementById('carton_loyalty_points').value || '10';
    const bonusPoints = document.getElementById('bonus_points_per_carton').value || '0';
    const hasMultiplier = document.getElementById('has_points_multiplier').checked;
    const multiplier = document.getElementById('points_multiplier').value || '1.5';
    
    // Update badge
    document.getElementById('previewBadge').textContent = name + ' - ' + displayName;
    
    // Update details
    const cartonRangeText = maxCartons ? 
        `${parseInt(minCartons)} - ${parseInt(maxCartons)}` : 
        `${parseInt(minCartons)}+ `;
    
    let pointsInfo = `⭐ ${parseInt(loyaltyPoints)} نقطة لكل كرتون`;
    if (parseInt(bonusPoints) > 0) {
        pointsInfo += ` + ${parseInt(bonusPoints)} إضافية`;
    }
    if (hasMultiplier) {
        pointsInfo += ` (مضاعف ${parseFloat(multiplier)}x)`;
    }
    
    document.getElementById('previewDetails').innerHTML = `
        <div>📦 كراتين: ${cartonRangeText} كرتون</div>
        <div>${pointsInfo}</div>
    `;
}

// Validation
function validateCartonRange() {
    const minCartons = parseInt(document.getElementById('min_cartons').value) || 0;
    const maxCartonsField = document.getElementById('max_cartons');
    const maxCartons = parseInt(maxCartonsField.value);
    
    if (maxCartons && maxCartons <= minCartons) {
        alert('الحد الأقصى للكراتين يجب أن يكون أكبر من الحد الأدنى');
        maxCartonsField.focus();
        return false;
    }
    return true;
}

function validateLoyaltyPoints() {
    const loyaltyPoints = parseInt(document.getElementById('carton_loyalty_points').value) || 0;
    const bonusPoints = parseInt(document.getElementById('bonus_points_per_carton').value) || 0;
    const monthlyBonus = parseInt(document.getElementById('monthly_bonus_points').value) || 0;
    const signupBonus = parseInt(document.getElementById('signup_bonus_points').value) || 0;
    const multiplier = parseFloat(document.getElementById('points_multiplier').value) || 1;
    
    if (loyaltyPoints < 1) {
        alert('نقاط الولاء يجب أن تكون أكبر من 0');
        document.getElementById('carton_loyalty_points').focus();
        return false;
    }
    
    if (bonusPoints < 0) {
        alert('النقاط الإضافية يجب أن تكون 0 أو أكثر');
        document.getElementById('bonus_points_per_carton').focus();
        return false;
    }
    
    if (monthlyBonus < 0) {
        alert('المكافأة الشهرية يجب أن تكون 0 أو أكثر');
        document.getElementById('monthly_bonus_points').focus();
        return false;
    }
    
    if (signupBonus < 0) {
        alert('مكافأة الانضمام يجب أن تكون 0 أو أكثر');
        document.getElementById('signup_bonus_points').focus();
        return false;
    }
    
    if (multiplier < 1 || multiplier > 10) {
        alert('مضاعف النقاط يجب أن يكون بين 1 و 10');
        document.getElementById('points_multiplier').focus();
        return false;
    }
    
    return true;
}

// Recalculate categories after save
function recalculateAfterSave() {
    if (confirm('هل تريد حفظ التغييرات وإعادة حساب فئات جميع المستخدمين؟ قد تستغرق هذه العملية وقتاً.')) {
        const form = document.getElementById('categoryForm');
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'recalculate';
        hiddenInput.value = '1';
        form.appendChild(hiddenInput);
        form.submit();
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Real-time preview updates
    const previewFields = [
        'name', 'display_name', 'min_cartons', 'max_cartons', 
        'carton_loyalty_points', 'bonus_points_per_carton', 
        'has_points_multiplier', 'points_multiplier'
    ];
    previewFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updatePreview);
            field.addEventListener('change', updatePreview);
        }
    });
    
    // Multiplier group toggle
    const multiplierCheckbox = document.getElementById('has_points_multiplier');
    const multiplierGroup = document.getElementById('multiplier_group');
    
    function toggleMultiplierGroup() {
        if (multiplierCheckbox.checked) {
            multiplierGroup.style.display = 'block';
        } else {
            multiplierGroup.style.display = 'none';
        }
        updatePreview();
    }
    
    multiplierCheckbox.addEventListener('change', toggleMultiplierGroup);
    
    // Initial preview
    updatePreview();
    
    // Form validation
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        if (!validateCartonRange() || !validateLoyaltyPoints()) {
            e.preventDefault();
        }
    });
    
    // Range validation
    document.getElementById('max_cartons').addEventListener('blur', validateCartonRange);
    
    // Loyalty points validation
    const loyaltyFields = [
        'carton_loyalty_points', 'bonus_points_per_carton', 
        'monthly_bonus_points', 'signup_bonus_points', 'points_multiplier'
    ];
    loyaltyFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', validateLoyaltyPoints);
        }
    });
});
</script>
@endsection
