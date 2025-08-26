@extends('layouts.admin')

@section('title', 'تعديل المستخدم - ' . $user->name)
@section('page-title', 'تعديل المستخدم')

@push('styles')
<style>
    /* Edit User Form Styles - Same as create with modifications */
    .edit-user-container {
        max-width: 900px;
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
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--gray-100);
    }

    .user-avatar-edit {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--gray-200);
    }

    .form-header-content h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0 0 5px 0;
    }

    .form-header-content p {
        color: var(--gray-600);
        margin: 0;
    }

    .user-status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-top: 8px;
        display: inline-block;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #DC2626;
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
    .image-upload {
        text-align: center;
        border: 2px dashed var(--gray-300);
        border-radius: 10px;
        padding: 30px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .image-upload:hover {
        border-color: var(--suntop-orange);
        background: rgba(255, 107, 53, 0.05);
    }

    .image-upload.has-image {
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

    .image-preview {
        max-width: 150px;
        max-height: 150px;
        border-radius: 10px;
        margin: 0 auto 15px;
        display: block;
    }

    .current-image {
        margin-bottom: 15px;
    }

    .current-image img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 10px;
        border: 2px solid var(--gray-200);
    }

    .image-upload input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
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

    /* Password Section */
    .password-section {
        background: var(--gray-50);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .password-section h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .password-help {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        color: var(--gray-600);
        font-size: 14px;
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

    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark));
        color: var(--white);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-box i {
        font-size: 20px;
        opacity: 0.9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .edit-user-container {
            padding: 0 15px;
        }

        .form-header {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
<div class="edit-user-container">
    <div class="form-card">
        <div class="form-header">
            <img src="{{ $user->profile_image ? asset($user->profile_image) : asset('images/default-avatar.png') }}" 
                 alt="صورة المستخدم" class="user-avatar-edit"
                 onerror="this.src='{{ asset('images/default-avatar.png') }}'">
            
            <div class="form-header-content">
                <h2>تعديل بيانات: {{ $user->name }}</h2>
                <p>آخر تحديث: {{ $user->updated_at->format('Y/m/d H:i') }}</p>
                <span class="user-status-badge status-{{ $user->is_active ? 'active' : 'inactive' }}">
                    {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </div>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>ملاحظة:</strong> تعديل البيانات الأساسية سيتطلب من المستخدم تسجيل الدخول مرة أخرى.
                كلمة المرور اختيارية - اتركها فارغة للاحتفاظ بكلمة المرور الحالية.
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data" id="editUserForm">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        الاسم الكامل <span class="required">*</span>
                    </label>
                    <input type="text" name="name" class="form-input" 
                           value="{{ old('name', $user->name) }}" required
                           placeholder="أدخل الاسم الكامل">
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        اسم المستخدم <span class="required">*</span>
                    </label>
                    <input type="text" name="username" class="form-input" 
                           value="{{ old('username', $user->username) }}" required
                           placeholder="أدخل اسم المستخدم">
                    <div class="form-help">يجب أن يكون فريداً ولا يحتوي على مسافات</div>
                    @error('username')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        البريد الإلكتروني <span class="required">*</span>
                    </label>
                    <input type="email" name="email" class="form-input" 
                           value="{{ old('email', $user->email) }}" required
                           placeholder="example@domain.com">
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="tel" name="phone" class="form-input" 
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="+20 xxx xxx xxxx">
                    @error('phone')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        نوع الحساب <span class="required">*</span>
                    </label>
                    <select name="role" class="form-select" required>
                        <option value="">اختر نوع الحساب</option>
                        <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>عميل</option>
                        <option value="merchant" {{ old('role', $user->role) === 'merchant' ? 'selected' : '' }}>تاجر</option>
                    </select>
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">فئة المستخدم</label>
                    <select name="user_category_id" class="form-select">
                        <option value="">اختر الفئة (اختياري)</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ old('user_category_id', $user->user_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->display_name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">
                        الفئة الحالية: {{ $user->userCategory->display_name ?? 'غير محدد' }}
                    </div>
                    @error('user_category_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Password Section -->
            <div class="password-section">
                <h3>
                    <i class="fas fa-key"></i>
                    تغيير كلمة المرور
                </h3>
                
                <div class="password-help">
                    <i class="fas fa-info-circle"></i>
                    اترك هذين الحقلين فارغين للاحتفاظ بكلمة المرور الحالية
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" name="password" class="form-input" 
                               placeholder="أدخل كلمة مرور جديدة (اختياري)">
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" name="password_confirmation" class="form-input" 
                               placeholder="أعد إدخال كلمة المرور الجديدة">
                        @error('password_confirmation')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Profile Image -->
            <div class="form-group full-width">
                <label class="form-label">صورة الملف الشخصي</label>
                
                @if($user->profile_image)
                    <div class="current-image">
                        <p style="margin-bottom: 10px; color: var(--gray-600); font-size: 14px;">الصورة الحالية:</p>
                        <img src="{{ asset($user->profile_image) }}" alt="الصورة الحالية">
                    </div>
                @endif

                <div class="image-upload" onclick="document.getElementById('profileImage').click()">
                    <input type="file" name="profile_image" id="profileImage" 
                           accept="image/*" onchange="previewImage(this)">
                    <img class="image-preview" id="imagePreview" alt="معاينة الصورة" style="display: none;">
                    <div class="upload-content">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <p class="upload-text">اضغط لاختيار صورة جديدة أو اسحب الصورة هنا</p>
                        <p class="upload-text" style="font-size: 12px; margin-top: 5px;">
                            أقصى حجم: 2MB | الأنواع المدعومة: JPG, PNG, GIF
                        </p>
                    </div>
                </div>
                @error('profile_image')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Account Status -->
            <div class="form-group">
                <label class="form-label">حالة الحساب</label>
                <div class="toggle-group">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span>الحساب نشط</span>
                </div>
                <div class="form-help">يمكن للمستخدم تسجيل الدخول واستخدام النظام عند التفعيل</div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i>
                    حفظ التغييرات
                </button>
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn-secondary">
                    <i class="fas fa-eye"></i>
                    عرض التفاصيل
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    <i class="fas fa-list"></i>
                    العودة للقائمة
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Image Preview
function previewImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('imagePreview');
    const uploadContent = document.querySelector('.upload-content');
    const container = document.querySelector('.image-upload');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            uploadContent.style.display = 'none';
            container.classList.add('has-image');
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        uploadContent.style.display = 'block';
        container.classList.remove('has-image');
    }
}

// Form Submission
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.classList.add('btn-loading');
    submitBtn.disabled = true;
    
    // Re-enable if form submission fails
    setTimeout(() => {
        submitBtn.classList.remove('btn-loading');
        submitBtn.disabled = false;
    }, 10000);
});

// Username validation
document.querySelector('input[name="username"]').addEventListener('input', function(e) {
    this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
});

// Password confirmation validation
document.querySelector('input[name="password_confirmation"]').addEventListener('input', function() {
    const password = document.querySelector('input[name="password"]').value;
    const confirmation = this.value;
    
    if (password && confirmation && password !== confirmation) {
        this.setCustomValidity('كلمة المرور غير متطابقة');
    } else {
        this.setCustomValidity('');
    }
});

document.querySelector('input[name="password"]').addEventListener('input', function() {
    const confirmation = document.querySelector('input[name="password_confirmation"]');
    if (confirmation.value) {
        confirmation.dispatchEvent(new Event('input'));
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
</script>
@endpush
