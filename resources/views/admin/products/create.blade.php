@extends('layouts.admin')

@section('title', 'إضافة منتج جديد - SunTop')
@section('page-title', 'إضافة منتج جديد')

@push('styles')
<style>
    /* Create Product Form Styles */
    .create-product-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-card {
        background: var(--white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-100);
        margin-bottom: 25px;
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--gray-100);
    }

    .form-header h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0 0 10px 0;
    }

    .form-header p {
        color: var(--gray-600);
        margin: 0;
    }

    .form-sections {
        display: grid;
        gap: 30px;
    }

    .form-section {
        background: var(--gray-50);
        border-radius: 12px;
        padding: 25px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0 0 20px 0;
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
        font-size: 16px;
        color: var(--white);
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark));
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
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
        font-weight: 500;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .required {
        color: var(--danger);
    }

    .form-input, .form-select, .form-textarea {
        padding: 12px 15px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--white);
        font-family: 'Cairo', sans-serif;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--suntop-orange);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-help {
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 4px;
    }

    .form-error {
        font-size: 12px;
        color: var(--danger);
        margin-top: 4px;
    }

    /* Image Upload */
    .images-upload {
        border: 2px dashed var(--gray-300);
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .images-upload:hover {
        border-color: var(--suntop-orange);
        background: rgba(255, 107, 53, 0.05);
    }

    .images-upload.has-images {
        border-style: solid;
        border-color: var(--success);
        background: rgba(16, 185, 129, 0.05);
    }

    .upload-icon {
        font-size: 48px;
        color: var(--gray-400);
        margin-bottom: 15px;
    }

    .upload-text {
        color: var(--gray-600);
        font-size: 14px;
        margin: 0;
    }

    .images-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .image-preview {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid var(--gray-200);
    }

    .image-preview img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: var(--danger);
        color: var(--white);
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .images-upload input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    /* Price Section */
    .price-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 15px;
        align-items: end;
    }

    .discount-calculator {
        background: var(--white);
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        border: 1px solid var(--gray-200);
    }

    .discount-result {
        font-size: 14px;
        color: var(--gray-700);
        margin-bottom: 5px;
    }

    .savings-amount {
        font-size: 16px;
        font-weight: 600;
        color: var(--success);
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--gray-300);
        transition: 0.3s;
        border-radius: 26px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background-color: var(--suntop-orange);
    }

    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    .toggle-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Stock Alert */
    .stock-alert {
        background: linear-gradient(135deg, var(--warning), #D97706);
        color: var(--white);
        border-radius: 8px;
        padding: 12px;
        margin-top: 10px;
        font-size: 13px;
        display: none;
    }

    .stock-alert.show {
        display: block;
    }

    /* Action Buttons */
    .form-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
        padding-top: 20px;
        border-top: 1px solid var(--gray-100);
        margin-top: 30px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark));
        color: var(--white);
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        color: var(--white);
        text-decoration: none;
    }

    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        background: var(--gray-200);
        border-color: var(--gray-300);
        color: var(--gray-700);
        text-decoration: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .price-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .create-product-container {
            padding: 0 15px;
        }
    }
</style>
@endpush

@section('content')
<div class="create-product-container">
    <div class="form-card">
        <div class="form-header">
            <h2>إضافة منتج جديد</h2>
            <p>املأ النموذج أدناه لإضافة منتج جديد إلى المتجر</p>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="createProductForm">
            @csrf

            <div class="form-sections">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        المعلومات الأساسية
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                اسم المنتج <span class="required">*</span>
                            </label>
                            <input type="text" name="name" class="form-input" 
                                   value="{{ old('name') }}" required
                                   placeholder="أدخل اسم المنتج">
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>




                        
                        @if(count($categories) > 0)
                        <div class="form-group">
                            <label class="form-label">
                                فئة المنتج <span class="required">*</span>
                            </label>
                            <select name="category_id" class="form-select" required>
                                <option value="">اختر فئة المنتج</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->display_name }} ({{ $category->name }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-help">حدد ما إذا كان المنتج من فئة 1 لتر أو 250 مل</div>
                            @error('category_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                        <div class="form-group">
                            <div style="background: #FEF3CD; border: 1px solid #F59E0B; color: #92400E; padding: 12px; border-radius: 8px; font-size: 14px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                لا توجد فئات منتجات مسجلة في النظام. يمكنك المتابعة بدون تحديد فئة.
                            </div>
                        </div>
                        @endif

                        <div class="form-group full-width">
                            <label class="form-label">
                                وصف المنتج <span class="required">*</span>
                            </label>
                            <textarea name="description" class="form-textarea" 
                                      required placeholder="اكتب وصفاً للمنتج">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        الأسعار
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                السعر الأساسي <span class="required">*</span>
                            </label>
                            <input type="number" name="price" class="form-input" 
                                   value="{{ old('price') }}" required step="0.01" min="0"
                                   placeholder="0.00">
                            @error('price')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                لون الخلفية <span class="required">*</span>
                            </label>
                            <input type="color" name="back_color" class="form-input" 
                                   value="{{ old('back_color', '#FF6B35') }}" required>
                            @error('back_color')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        صور المنتج
                    </h3>

                    <div class="form-group">
                        <label class="form-label">صور المنتج (حد أقصى 5 صور)</label>
                        <div class="images-upload" onclick="document.getElementById('productImages').click()">
                            <input type="file" name="images[]" id="productImages" 
                                   accept="image/*" multiple onchange="previewImages(this)">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <p class="upload-text">اضغط لاختيار الصور أو اسحب الصور هنا</p>
                                <p class="upload-text" style="font-size: 12px; margin-top: 5px;">
                                    أقصى حجم لكل صورة: 2MB | الأنواع المدعومة: JPG, PNG, GIF
                                </p>
                            </div>
                        </div>
                        <div class="images-preview" id="imagesPreview"></div>
                        @error('images')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Settings -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        إعدادات المنتج
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">حالة المنتج</label>
                            <div class="toggle-group">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="is_available" value="1" 
                                           {{ old('is_available', true) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span>المنتج متاح للعرض</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i>
                    إنشاء المنتج
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedImages = [];

// Images Preview
function previewImages(input) {
    const files = Array.from(input.files);
    const previewContainer = document.getElementById('imagesPreview');
    const uploadContainer = document.querySelector('.images-upload');
    
    if (files.length > 5) {
        showNotification('يمكنك اختيار 5 صور كحد أقصى', 'error');
        input.value = '';
        return;
    }

    selectedImages = files;
    previewContainer.innerHTML = '';

    if (files.length > 0) {
        uploadContainer.classList.add('has-images');
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imagePreview = document.createElement('div');
                imagePreview.className = 'image-preview';
                imagePreview.innerHTML = `
                    <img src="${e.target.result}" alt="معاينة الصورة ${index + 1}">
                    <button type="button" class="remove-image" onclick="removeImage(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                previewContainer.appendChild(imagePreview);
            };
            reader.readAsDataURL(file);
        });
    } else {
        uploadContainer.classList.remove('has-images');
    }
}

function removeImage(index) {
    selectedImages.splice(index, 1);
    
    // Create new FileList
    const dt = new DataTransfer();
    selectedImages.forEach(file => dt.items.add(file));
    document.getElementById('productImages').files = dt.files;
    
    previewImages(document.getElementById('productImages'));
}

// Discount Calculator
function calculateDiscount() {
    const price = parseFloat(document.querySelector('input[name="price"]').value) || 0;
    const discountPrice = parseFloat(document.querySelector('input[name="discount_price"]').value) || 0;
    const calculator = document.getElementById('discountCalculator');
    const result = document.getElementById('discountResult');
    const savings = document.getElementById('savingsAmount');

    if (price > 0 && discountPrice > 0 && discountPrice < price) {
        const discountPercent = ((price - discountPrice) / price * 100).toFixed(1);
        const savingsAmount = (price - discountPrice).toFixed(2);
        
        calculator.style.display = 'block';
        result.textContent = `خصم ${discountPercent}%`;
        savings.textContent = `توفير ${savingsAmount} ج.م`;
    } else {
        calculator.style.display = 'none';
    }
}

// Stock Alert
function checkStock() {
    const stock = parseInt(document.querySelector('input[name="stock_quantity"]').value) || 0;
    const alert = document.getElementById('stockAlert');
    
    if (stock < 10) {
        alert.classList.add('show');
    } else {
        alert.classList.remove('show');
    }
}

// Auto-generate SKU from name
document.querySelector('input[name="name"]').addEventListener('input', function(e) {
    const skuField = document.querySelector('input[name="sku"]');
    if (!skuField.value) {
        const name = this.value.toUpperCase()
            .replace(/[أإآا]/g, 'A')
            .replace(/[ب]/g, 'B')
            .replace(/[ت]/g, 'T')
            .replace(/[ث]/g, 'TH')
            .replace(/[ج]/g, 'J')
            .replace(/[ح]/g, 'H')
            .replace(/[خ]/g, 'KH')
            .replace(/[د]/g, 'D')
            .replace(/[ذ]/g, 'TH')
            .replace(/[ر]/g, 'R')
            .replace(/[ز]/g, 'Z')
            .replace(/[س]/g, 'S')
            .replace(/[ش]/g, 'SH')
            .replace(/[ص]/g, 'S')
            .replace(/[ض]/g, 'D')
            .replace(/[ط]/g, 'T')
            .replace(/[ظ]/g, 'TH')
            .replace(/[ع]/g, 'A')
            .replace(/[غ]/g, 'GH')
            .replace(/[ف]/g, 'F')
            .replace(/[ق]/g, 'Q')
            .replace(/[ك]/g, 'K')
            .replace(/[ل]/g, 'L')
            .replace(/[م]/g, 'M')
            .replace(/[ن]/g, 'N')
            .replace(/[ه]/g, 'H')
            .replace(/[و]/g, 'W')
            .replace(/[ي]/g, 'Y')
            .replace(/\s+/g, '-')
            .replace(/[^A-Z0-9-]/g, '')
            .substring(0, 10);
            
        if (name) {
            skuField.value = 'PRD-' + name + '-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        }
    }
});

// Form Submission
document.getElementById('createProductForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.classList.add('btn-loading');
    submitBtn.disabled = true;
    
    // Re-enable if form submission fails
    setTimeout(() => {
        submitBtn.classList.remove('btn-loading');
        submitBtn.disabled = false;
    }, 10000);
});

// Price validation
document.querySelector('input[name="discount_price"]').addEventListener('input', function() {
    const price = parseFloat(document.querySelector('input[name="price"]').value) || 0;
    const discountPrice = parseFloat(this.value) || 0;
    
    if (discountPrice >= price && price > 0) {
        this.setCustomValidity('سعر الخصم يجب أن يكون أقل من السعر الأساسي');
    } else {
        this.setCustomValidity('');
    }
});

// Success message display
@if(session('success'))
    showNotification('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    showNotification('{{ session('error') }}', 'error');
@endif

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

// Initialize
calculateDiscount();
checkStock();
</script>
@endpush
