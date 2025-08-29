@extends('layouts.admin')

@section('title', 'إنشاء إشعار جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">
            <i class="fas fa-plus-circle text-primary"></i>
            إنشاء إشعار جديد
        </h2>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> العودة للقائمة
        </a>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bell"></i>
                        تفاصيل الإشعار
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.store') }}" method="POST" id="notificationForm">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle"></i>
                                    المعلومات الأساسية
                                </h6>
                            </div>
                            
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label required">عنوان الإشعار</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required
                                       placeholder="أدخل عنوان واضح ومختصر">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="alert_type" class="form-label required">نوع التنبيه</label>
                                <select class="form-select @error('alert_type') is-invalid @enderror" 
                                        id="alert_type" name="alert_type" required>
                                    @foreach(\App\Models\Notification::ALERT_TYPES as $key => $value)
                                        <option value="{{ $key }}" {{ old('alert_type', 'info') === $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('alert_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="message" class="form-label required">رسالة الإشعار (مختصرة)</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" name="message" rows="3" required maxlength="500"
                                          placeholder="اكتب الرسالة الرئيسية للإشعار (حد أقصى 500 حرف)">{{ old('message') }}</textarea>
                                <div class="form-text">
                                    <span id="messageCount">0</span> / 500 حرف
                                </div>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="body" class="form-label">تفاصيل إضافية (اختياري)</label>
                                <textarea class="form-control @error('body') is-invalid @enderror" 
                                          id="body" name="body" rows="4" maxlength="2000"
                                          placeholder="تفاصيل أو معلومات إضافية للإشعار (حد أقصى 2000 حرف)">{{ old('body') }}</textarea>
                                <div class="form-text">
                                    <span id="bodyCount">0</span> / 2000 حرف
                                </div>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Classification -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted border-bottom pb-2 mb-3">
                                    <i class="fas fa-tags"></i>
                                    تصنيف الإشعار
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label required">فئة الإشعار</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    @foreach(\App\Models\Notification::TYPES as $key => $value)
                                        <option value="{{ $key }}" {{ old('type', 'general') === $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label required">أولوية الإشعار</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    @foreach(\App\Models\Notification::PRIORITIES as $key => $value)
                                        <option value="{{ $key }}" {{ old('priority', 'medium') === $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="action_url" class="form-label">رابط الإجراء (اختياري)</label>
                                <input type="url" class="form-control @error('action_url') is-invalid @enderror" 
                                       id="action_url" name="action_url" value="{{ old('action_url') }}"
                                       placeholder="https://example.com/action">
                                <div class="form-text">رابط لتوجيه المستخدم عند النقر على الإشعار</div>
                                @error('action_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Target Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted border-bottom pb-2 mb-3">
                                    <i class="fas fa-users"></i>
                                    تحديد المستقبلين
                                </h6>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label required">نوع الإرسال</label>
                                <div class="form-check-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="target_user" value="user" {{ old('target_type', 'user') === 'user' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="target_user">
                                            <i class="fas fa-user text-primary"></i>
                                            مستخدمين محددين
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="target_category" value="category" {{ old('target_type') === 'category' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="target_category">
                                            <i class="fas fa-users text-info"></i>
                                            فئة مستخدمين
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="target_all" value="all" {{ old('target_type') === 'all' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="target_all">
                                            <i class="fas fa-globe text-success"></i>
                                            جميع المستخدمين
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Specific Users Selection -->
                            <div class="col-12 mb-3" id="userSelection" style="display: none;">
                                <label for="user_ids" class="form-label">اختيار المستخدمين</label>
                                <select class="form-select @error('user_ids') is-invalid @enderror" 
                                        id="user_ids" name="user_ids[]" multiple size="8">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                {{ (collect(old('user_ids'))->contains($user->id)) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                            @if($user->role !== 'customer')
                                                <span class="badge">{{ $user->role }}</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">اضغط Ctrl لاختيار عدة مستخدمين</div>
                                @error('user_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category Selection -->
                            <div class="col-12 mb-3" id="categorySelection" style="display: none;">
                                <label for="category_id" class="form-label">اختيار فئة المستخدمين</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id">
                                    <option value="">اختر فئة المستخدمين</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}
                                                data-users-count="{{ $category->users()->count() }}">
                                            {{ $category->display_name }}
                                            ({{ $category->users()->count() }} مستخدم)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    <span id="categoryInfo"></span>
                                </div>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role Filter for All Users -->
                            <div class="col-12 mb-3" id="roleFilterSelection" style="display: none;">
                                <label for="role_filter" class="form-label">تصفية حسب الدور (اختياري)</label>
                                <select class="form-select" id="role_filter" name="role_filter">
                                    <option value="">جميع الأدوار</option>
                                    <option value="customer" {{ old('role_filter') === 'customer' ? 'selected' : '' }}>العملاء فقط</option>
                                    <option value="merchant" {{ old('role_filter') === 'merchant' ? 'selected' : '' }}>التجار فقط</option>
                                    <option value="admin" {{ old('role_filter') === 'admin' ? 'selected' : '' }}>الإداريين فقط</option>
                                </select>
                                <div class="form-text">اختر نوع المستخدمين المراد إرسال الإشعار إليهم</div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle"></i>
                                        ملخص الإشعار
                                    </h6>
                                    <div id="notificationSummary">
                                        سيتم إرسال إشعار من نوع <strong id="summaryType">عام</strong> 
                                        بأولوية <strong id="summaryPriority">متوسطة</strong>
                                        إلى <strong id="summaryTarget">مستخدم محدد</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                        إرسال الإشعار
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.form-check {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #e3e6f0;
    border-radius: 5px;
}

.form-check:hover {
    background-color: #f8f9fc;
}

.form-check-input:checked + .form-check-label {
    font-weight: 600;
}

.form-check-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

@media (min-width: 768px) {
    .form-check-group {
        flex-direction: row;
    }
    
    .form-check {
        flex: 1;
        margin-bottom: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counters
    const messageInput = document.getElementById('message');
    const bodyInput = document.getElementById('body');
    const messageCounter = document.getElementById('messageCount');
    const bodyCounter = document.getElementById('bodyCount');

    messageInput.addEventListener('input', function() {
        messageCounter.textContent = this.value.length;
    });

    bodyInput.addEventListener('input', function() {
        bodyCounter.textContent = this.value.length;
    });

    // Target type handling
    const targetRadios = document.querySelectorAll('input[name="target_type"]');
    const userSelection = document.getElementById('userSelection');
    const categorySelection = document.getElementById('categorySelection');
    const roleFilterSelection = document.getElementById('roleFilterSelection');

    function handleTargetTypeChange() {
        const selectedType = document.querySelector('input[name="target_type"]:checked').value;
        
        // Hide all sections
        userSelection.style.display = 'none';
        categorySelection.style.display = 'none';
        roleFilterSelection.style.display = 'none';

        // Show relevant section
        switch(selectedType) {
            case 'user':
                userSelection.style.display = 'block';
                break;
            case 'category':
                categorySelection.style.display = 'block';
                break;
            case 'all':
                roleFilterSelection.style.display = 'block';
                break;
        }

        updateSummary();
    }

    targetRadios.forEach(radio => {
        radio.addEventListener('change', handleTargetTypeChange);
    });

    // Category info
    const categorySelect = document.getElementById('category_id');
    const categoryInfo = document.getElementById('categoryInfo');

    categorySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const usersCount = selectedOption.getAttribute('data-users-count');
            categoryInfo.textContent = `سيتم إرسال الإشعار إلى ${usersCount} مستخدم في هذه الفئة`;
        } else {
            categoryInfo.textContent = '';
        }
        updateSummary();
    });

    // Summary update
    function updateSummary() {
        const type = document.getElementById('type').options[document.getElementById('type').selectedIndex].text;
        const priority = document.getElementById('priority').options[document.getElementById('priority').selectedIndex].text;
        const targetType = document.querySelector('input[name="target_type"]:checked').value;
        
        let targetText = '';
        switch(targetType) {
            case 'user':
                const selectedUsers = document.getElementById('user_ids').selectedOptions;
                targetText = selectedUsers.length > 0 ? `${selectedUsers.length} مستخدم محدد` : 'مستخدمين محددين';
                break;
            case 'category':
                const selectedCategory = document.getElementById('category_id').selectedOptions[0];
                if (selectedCategory && selectedCategory.value) {
                    const usersCount = selectedCategory.getAttribute('data-users-count');
                    targetText = `فئة "${selectedCategory.text.split('(')[0].trim()}" (${usersCount} مستخدم)`;
                } else {
                    targetText = 'فئة مستخدمين';
                }
                break;
            case 'all':
                const roleFilter = document.getElementById('role_filter').value;
                targetText = roleFilter ? 
                    `جميع ${document.getElementById('role_filter').options[document.getElementById('role_filter').selectedIndex].text}` : 
                    'جميع المستخدمين';
                break;
        }

        document.getElementById('summaryType').textContent = type;
        document.getElementById('summaryPriority').textContent = priority;
        document.getElementById('summaryTarget').textContent = targetText;
    }

    // Update summary on field changes
    document.getElementById('type').addEventListener('change', updateSummary);
    document.getElementById('priority').addEventListener('change', updateSummary);
    document.getElementById('user_ids').addEventListener('change', updateSummary);
    document.getElementById('role_filter').addEventListener('change', updateSummary);

    // Initialize
    handleTargetTypeChange();
    updateSummary();

    // Update counters on page load
    messageCounter.textContent = messageInput.value.length;
    bodyCounter.textContent = bodyInput.value.length;
});
</script>
@endpush
@endsection
