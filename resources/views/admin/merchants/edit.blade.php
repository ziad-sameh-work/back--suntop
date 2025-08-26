@extends('layouts.admin')

@section('title', 'تعديل التاجر - ' . $merchant->name)
@section('page-title', 'تعديل التاجر')

@push('styles')
<style>
    .edit-merchant-container { max-width: 1000px; margin: 0 auto; }
    .form-card { background: var(--white); border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); margin-bottom: 25px; }
    .form-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--gray-100); }
    .merchant-avatar { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; border: 3px solid var(--gray-200); }
    .form-header h2 { font-size: 24px; font-weight: 600; color: var(--gray-800); margin: 0 0 5px 0; }
    .form-header p { color: var(--gray-600); margin: 0; }
    .form-sections { display: grid; gap: 30px; }
    .form-section { background: var(--gray-50); border-radius: 12px; padding: 25px; }
    .section-title { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0 0 20px 0; display: flex; align-items: center; gap: 10px; }
    .section-icon { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; color: var(--white); background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-label { font-size: 14px; font-weight: 500; color: var(--gray-700); display: flex; align-items: center; gap: 5px; }
    .required { color: var(--danger); }
    .form-input, .form-select, .form-textarea { padding: 12px 15px; border: 2px solid var(--gray-200); border-radius: 10px; font-size: 14px; transition: all 0.3s ease; background: var(--white); font-family: 'Cairo', sans-serif; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--suntop-orange); box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1); }
    .form-textarea { resize: vertical; min-height: 100px; }
    .form-help { font-size: 12px; color: var(--gray-500); margin-top: 4px; }
    .form-error { font-size: 12px; color: var(--danger); margin-top: 4px; }
    .logo-section { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; align-items: start; }
    .current-logo { text-align: center; }
    .current-logo img { width: 100px; height: 100px; border-radius: 12px; object-fit: cover; border: 3px solid var(--gray-200); }
    .current-logo p { font-size: 14px; color: var(--gray-600); margin: 10px 0 0 0; }
    .logo-upload { border: 2px dashed var(--gray-300); border-radius: 10px; padding: 30px; text-align: center; transition: all 0.3s ease; cursor: pointer; position: relative; overflow: hidden; }
    .logo-upload:hover { border-color: var(--suntop-orange); background: rgba(255, 107, 53, 0.05); }
    .logo-upload.has-logo { border-style: solid; border-color: var(--success); background: rgba(16, 185, 129, 0.05); }
    .upload-icon { font-size: 48px; color: var(--gray-400); margin-bottom: 15px; }
    .upload-text { color: var(--gray-600); font-size: 14px; margin: 0; }
    .logo-preview { display: none; margin-top: 20px; }
    .logo-preview img { max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid var(--gray-200); }
    .logo-upload input[type="file"] { position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
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
    .info-box { background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark)); color: var(--white); border-radius: 10px; padding: 15px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
    .info-box i { font-size: 20px; opacity: 0.9; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .form-actions { flex-direction: column; } .edit-merchant-container { padding: 0 15px; } .form-header { flex-direction: column; text-align: center; } .logo-section { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="edit-merchant-container">
    <div class="form-card">
        <div class="form-header">
            <img src="{{ $merchant->logo ? asset($merchant->logo) : asset('images/no-merchant.png') }}" 
                 alt="شعار التاجر" class="merchant-avatar"
                 onerror="this.src='{{ asset('images/no-merchant.png') }}'">
            <div>
                <h2>تعديل: {{ $merchant->name }}</h2>
                <p>آخر تحديث: {{ $merchant->updated_at->format('Y/m/d H:i') }}</p>
            </div>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div><strong>ملاحظة:</strong> تعديل بيانات التاجر سيؤثر على جميع المنتجات والطلبات المرتبطة به.</div>
        </div>

        <form method="POST" action="{{ route('admin.merchants.update', $merchant->id) }}" enctype="multipart/form-data" id="editMerchantForm">
            @csrf
            @method('PUT')

            <div class="form-sections">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon"><i class="fas fa-user"></i></div>
                        المعلومات الأساسية
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">اسم التاجر <span class="required">*</span></label>
                            <input type="text" name="name" class="form-input" 
                                   value="{{ old('name', $merchant->name) }}" required placeholder="أدخل اسم التاجر">
                            @error('name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">البريد الإلكتروني <span class="required">*</span></label>
                            <input type="email" name="email" class="form-input" 
                                   value="{{ old('email', $merchant->email) }}" required placeholder="أدخل البريد الإلكتروني">
                            @error('email')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">رقم الهاتف <span class="required">*</span></label>
                            <input type="tel" name="phone" class="form-input" 
                                   value="{{ old('phone', $merchant->phone) }}" required placeholder="مثل: 01000000000">
                            @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">المدينة <span class="required">*</span></label>
                            <input type="text" name="city" class="form-input" 
                                   value="{{ old('city', $merchant->city) }}" required placeholder="أدخل المدينة">
                            @error('city')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">العنوان كاملاً <span class="required">*</span></label>
                            <textarea name="address" class="form-textarea" 
                                      required placeholder="أدخل العنوان التفصيلي للمحل">{{ old('address', $merchant->address) }}</textarea>
                            @error('address')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon"><i class="fas fa-store"></i></div>
                        معلومات المحل التجاري
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">اسم المحل <span class="required">*</span></label>
                            <input type="text" name="business_name" class="form-input" 
                                   value="{{ old('business_name', $merchant->business_name) }}" required placeholder="أدخل اسم المحل">
                            @error('business_name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">نوع النشاط التجاري</label>
                            <input type="text" name="business_type" class="form-input" 
                                   value="{{ old('business_type', $merchant->business_type) }}" placeholder="مثل: مطعم، متجر ملابس، إلخ">
                            @error('business_type')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">نسبة العمولة <span class="required">*</span></label>
                            <input type="number" name="commission_rate" class="form-input" 
                                   value="{{ old('commission_rate', $merchant->commission_rate) }}" required step="0.1" min="0" max="100" placeholder="5.0">
                            <div class="form-help">النسبة المئوية للعمولة على كل عملية بيع</div>
                            @error('commission_rate')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">وصف المحل</label>
                            <textarea name="description" class="form-textarea" 
                                      placeholder="اكتب وصفاً مختصراً عن المحل ونوع المنتجات التي يقدمها">{{ old('description', $merchant->description) }}</textarea>
                            @error('description')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Logo Management -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon"><i class="fas fa-image"></i></div>
                        إدارة شعار المحل
                    </h3>

                    <div class="logo-section">
                        @if($merchant->logo)
                        <div class="current-logo">
                            <img src="{{ asset($merchant->logo) }}" alt="الشعار الحالي">
                            <p>الشعار الحالي</p>
                        </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">تحديث الشعار (اختياري)</label>
                            <div class="logo-upload" onclick="document.getElementById('merchantLogo').click()">
                                <input type="file" name="logo" id="merchantLogo" 
                                       accept="image/*" onchange="previewLogo(this)">
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <p class="upload-text">اضغط لاختيار شعار جديد أو اسحب الصورة هنا</p>
                                    <p class="upload-text" style="font-size: 12px; margin-top: 5px;">
                                        أقصى حجم: 2MB | الأنواع المدعومة: JPG, PNG, GIF
                                    </p>
                                </div>
                            </div>
                            <div class="logo-preview" id="logoPreview">
                                <img id="logoImage" src="" alt="معاينة الشعار الجديد">
                            </div>
                            @error('logo')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon"><i class="fas fa-cog"></i></div>
                        إعدادات التاجر
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">حالة التاجر</label>
                            <div class="toggle-group">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $merchant->is_active) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span>التاجر نشط ومفعل</span>
                            </div>
                            <div class="form-help">يمكن للتاجر النشط إضافة منتجات واستقبال طلبات</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">حالة المتجر</label>
                            <div class="toggle-group">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="is_open" value="1" 
                                           {{ old('is_open', $merchant->is_open) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span>المتجر مفتوح لاستقبال الطلبات</span>
                            </div>
                            <div class="form-help">يمكن للعملاء طلب منتجات من المتاجر المفتوحة فقط</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
                <a href="{{ route('admin.merchants.show', $merchant->id) }}" class="btn-secondary">
                    <i class="fas fa-eye"></i> عرض التفاصيل
                </a>
                <a href="{{ route('admin.merchants.index') }}" class="btn-secondary">
                    <i class="fas fa-list"></i> العودة للقائمة
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Logo preview
function previewLogo(input) {
    const file = input.files[0];
    const uploadContainer = document.querySelector('.logo-upload');
    const previewContainer = document.getElementById('logoPreview');
    const logoImage = document.getElementById('logoImage');
    
    if (file) {
        if (file.size > 2048000) { // 2MB
            alert('حجم الملف كبير جداً. الحد الأقصى 2MB');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            logoImage.src = e.target.result;
            previewContainer.style.display = 'block';
            uploadContainer.classList.add('has-logo');
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
        uploadContainer.classList.remove('has-logo');
    }
}

// Form submission
document.getElementById('editMerchantForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
    
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> حفظ التغييرات';
    }, 10000);
});

// Phone number formatting
document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 11) {
        value = value.slice(0, 11);
    }
    e.target.value = value;
});

// Commission rate validation
document.querySelector('input[name="commission_rate"]').addEventListener('input', function(e) {
    const value = parseFloat(e.target.value);
    if (value < 0) {
        e.target.value = 0;
    } else if (value > 100) {
        e.target.value = 100;
    }
});

@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endpush
