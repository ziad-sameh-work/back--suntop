@extends('layouts.admin')

@section('title', 'إضافة مستخدم جديد - SunTop')
@section('page-title', 'إضافة مستخدم جديد')

@push('styles')
<style>
    /* Create User Form Styles */
    .create-user-container {
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
        min-height: 100px;
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
        display: none;
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

    /* Password Strength Indicator */
    .password-strength {
        margin-top: 8px;
    }

    .strength-bar {
        height: 4px;
        background: var(--gray-200);
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 5px;
    }

    .strength-fill {
        height: 100%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .strength-weak { background: var(--danger); width: 25%; }
    .strength-fair { background: var(--warning); width: 50%; }
    .strength-good { background: var(--suntop-blue); width: 75%; }
    .strength-strong { background: var(--success); width: 100%; }

    .strength-text {
        font-size: 12px;
        font-weight: 500;
    }

    /* Loading State */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: var(--white);
        border-radius: 50%;
        animation: spin 1s ease infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .create-user-container {
            padding: 0 15px;
        }
    }
</style>
@endpush

@section('content')
<div class="create-user-container">
    <div class="form-card">
        <div class="form-header">
            <h2>إضافة مستخدم جديد</h2>
            <p>املأ النموذج أدناه لإنشاء حساب مستخدم جديد في النظام</p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" id="createUserForm">
            @csrf

            <!-- Basic Information -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        الاسم الكامل <span class="required">*</span>
                    </label>
                    <input type="text" name="name" class="form-input" 
                           value="{{ old('name') }}" required
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
                           value="{{ old('username') }}" required
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
                           value="{{ old('email') }}" required
                           placeholder="example@domain.com">
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="tel" name="phone" class="form-input" 
                           value="{{ old('phone') }}"
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
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>عميل</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>مدير</option>
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
                                    {{ old('user_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->display_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_category_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Password Section -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        كلمة المرور <span class="required">*</span>
                    </label>
                    <input type="password" name="password" class="form-input" 
                           required placeholder="أدخل كلمة مرور قوية"
                           onkeyup="checkPasswordStrength(this.value)">
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText">أدخل كلمة مرور</div>
                    </div>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        تأكيد كلمة المرور <span class="required">*</span>
                    </label>
                    <input type="password" name="password_confirmation" class="form-input" 
                           required placeholder="أعد إدخال كلمة المرور">
                    @error('password_confirmation')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Profile Image -->
            <div class="form-group full-width">
                <label class="form-label">صورة الملف الشخصي</label>
                <div class="image-upload" onclick="document.getElementById('profileImage').click()">
                    <input type="file" name="profile_image" id="profileImage" 
                           accept="image/*" onchange="previewImage(this)">
                    <img class="image-preview" id="imagePreview" alt="معاينة الصورة">
                    <div class="upload-content">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <p class="upload-text">اضغط لاختيار صورة أو اسحب الصورة هنا</p>
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
                               {{ old('is_active', true) ? 'checked' : '' }}>
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
                    إنشاء المستخدم
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
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

// Password Strength Checker
function checkPasswordStrength(password) {
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    let strengthClass = '';
    let strengthLabel = '';

    if (password.length === 0) {
        strengthLabel = 'أدخل كلمة مرور';
        strengthClass = '';
    } else if (password.length < 6) {
        strength = 1;
        strengthLabel = 'ضعيفة جداً';
        strengthClass = 'strength-weak';
    } else if (password.length < 8) {
        strength = 2;
        strengthLabel = 'ضعيفة';
        strengthClass = 'strength-weak';
    } else {
        strength = 2;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        switch (strength) {
            case 3:
                strengthLabel = 'مقبولة';
                strengthClass = 'strength-fair';
                break;
            case 4:
                strengthLabel = 'جيدة';
                strengthClass = 'strength-good';
                break;
            case 5:
            case 6:
                strengthLabel = 'قوية';
                strengthClass = 'strength-strong';
                break;
            default:
                strengthLabel = 'ضعيفة';
                strengthClass = 'strength-weak';
        }
    }

    strengthFill.className = `strength-fill ${strengthClass}`;
    strengthText.textContent = strengthLabel;
    strengthText.className = `strength-text ${strengthClass}`;
}

// Form Submission
document.getElementById('createUserForm').addEventListener('submit', function(e) {
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

// Auto-generate username from name
document.querySelector('input[name="name"]').addEventListener('input', function(e) {
    const usernameField = document.querySelector('input[name="username"]');
    if (!usernameField.value) {
        const name = this.value.toLowerCase()
            .replace(/[أإآ]/g, 'a')
            .replace(/[ب]/g, 'b')
            .replace(/[ت]/g, 't')
            .replace(/[ث]/g, 'th')
            .replace(/[ج]/g, 'j')
            .replace(/[ح]/g, 'h')
            .replace(/[خ]/g, 'kh')
            .replace(/[د]/g, 'd')
            .replace(/[ذ]/g, 'th')
            .replace(/[ر]/g, 'r')
            .replace(/[ز]/g, 'z')
            .replace(/[س]/g, 's')
            .replace(/[ش]/g, 'sh')
            .replace(/[ص]/g, 's')
            .replace(/[ض]/g, 'd')
            .replace(/[ط]/g, 't')
            .replace(/[ظ]/g, 'th')
            .replace(/[ع]/g, 'a')
            .replace(/[غ]/g, 'gh')
            .replace(/[ف]/g, 'f')
            .replace(/[ق]/g, 'q')
            .replace(/[ك]/g, 'k')
            .replace(/[ل]/g, 'l')
            .replace(/[م]/g, 'm')
            .replace(/[ن]/g, 'n')
            .replace(/[ه]/g, 'h')
            .replace(/[و]/g, 'w')
            .replace(/[ي]/g, 'y')
            .replace(/[ة]/g, 'h')
            .replace(/[ى]/g, 'a')
            .replace(/\s+/g, '_')
            .replace(/[^a-z0-9_]/g, '');
            
        usernameField.value = name;
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
