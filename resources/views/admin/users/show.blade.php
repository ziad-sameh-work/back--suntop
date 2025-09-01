@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم - ' . $user->name)
@section('page-title', 'تفاصيل المستخدم')

@push('styles')
<style>
    /* User Details Styles */
    .user-details-container {
        display: grid;
        gap: 25px;
        grid-template-columns: 1fr;
    }

    /* Header Section */
    .user-header {
        background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%);
        border-radius: 16px;
        padding: 30px;
        color: var(--white);
        position: relative;
        overflow: hidden;
    }

    .user-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" fill-opacity="0.1"/></svg>') repeat;
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateX(0px) translateY(0px); }
        50% { transform: translateX(-20px) translateY(-20px); }
    }

    .user-header-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 25px;
        flex-wrap: wrap;
    }

    .user-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .user-avatar-large-fallback {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-blue));
        color: white;
        display: none;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 48px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
    }

    .user-info-header {
        flex: 1;
        min-width: 300px;
    }

    .user-name {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .user-email {
        font-size: 18px;
        opacity: 0.9;
        margin: 0 0 15px 0;
    }

    .user-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .badge {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-white {
        background: var(--white);
        color: var(--suntop-orange);
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-white:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        color: var(--suntop-orange);
        text-decoration: none;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .content-card {
        background: var(--white);
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-100);
        transition: all 0.3s ease;
    }

    .content-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--gray-100);
    }

    .card-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--white);
    }

    .card-icon.orange { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); }
    .card-icon.blue { background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark)); }
    .card-icon.green { background: linear-gradient(135deg, var(--success), #0D9488); }
    .card-icon.purple { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    /* Info Lists */
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--gray-50);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        color: var(--gray-600);
        width: 120px;
        flex-shrink: 0;
    }

    .info-value {
        color: var(--gray-800);
        flex: 1;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card-small {
        background: var(--gray-50);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card-small:hover {
        background: var(--gray-100);
        transform: translateY(-2px);
    }

    .stat-value-small {
        font-size: 24px;
        font-weight: 700;
        color: var(--suntop-orange);
        margin: 0 0 5px 0;
    }

    .stat-label-small {
        font-size: 14px;
        color: var(--gray-600);
        margin: 0;
    }

    /* Activity Timeline */
    .timeline {
        position: relative;
        padding-right: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        right: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--gray-200);
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        padding-right: 25px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        right: 3px;
        top: 5px;
        width: 12px;
        height: 12px;
        background: var(--suntop-orange);
        border-radius: 50%;
        border: 3px solid var(--white);
        box-shadow: 0 0 0 2px var(--suntop-orange);
    }

    .timeline-content {
        background: var(--gray-50);
        border-radius: 8px;
        padding: 15px;
    }

    .timeline-title {
        font-weight: 600;
        color: var(--gray-800);
        margin: 0 0 5px 0;
    }

    .timeline-text {
        color: var(--gray-600);
        font-size: 14px;
        margin: 0 0 5px 0;
    }

    .timeline-date {
        color: var(--gray-500);
        font-size: 12px;
        margin: 0;
    }

    /* Full Width Card */
    .full-width-card {
        grid-column: 1 / -1;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .user-header-content {
            flex-direction: column;
            text-align: center;
        }

        .user-avatar-large {
            width: 100px;
            height: 100px;
        }

        .user-name {
            font-size: 24px;
        }

        .header-actions {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Status Badge Styles */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #DC2626;
    }

    .role-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .role-customer {
        background: rgba(74, 144, 226, 0.1);
        color: #2563EB;
    }

    .role-admin {
        background: rgba(124, 58, 237, 0.1);
        color: #7C3AED;
    }
</style>
@endpush

@section('content')
<div class="user-details-container">
    <!-- User Header -->
    <div class="user-header">
        <div class="user-header-content">
            <img src="{{ $user->profile_image_url }}" 
                 alt="صورة المستخدم" class="user-avatar-large"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="user-avatar-large-fallback">
                {{ $user->initial }}
            </div>
            
            <div class="user-info-header">
                <h1 class="user-name">{{ $user->name }}</h1>
                <p class="user-email">{{ $user->email }}</p>
                <div class="user-badges">
                    <span class="badge">
                        {{ $user->role === 'customer' ? 'عميل' : 'مدير' }}
                    </span>
                    <span class="badge">
                        {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                    @if($user->userCategory)
                        <span class="badge">{{ $user->userCategory->display_name }}</span>
                    @endif
                </div>
            </div>

            <div class="header-actions">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-white">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <button class="btn-white" onclick="toggleUserStatus({{ $user->id }})">
                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                    {{ $user->is_active ? 'تعطيل' : 'تفعيل' }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-white">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Basic Information -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon orange">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="card-title">المعلومات الأساسية</h3>
            </div>

            <ul class="info-list">
                <li class="info-item">
                    <span class="info-label">الاسم الكامل:</span>
                    <span class="info-value">{{ $user->name }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">اسم المستخدم:</span>
                    <span class="info-value">{{ $user->username }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">البريد الإلكتروني:</span>
                    <span class="info-value">{{ $user->email }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">رقم الهاتف:</span>
                    <span class="info-value">{{ $user->phone ?? 'غير محدد' }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">نوع الحساب:</span>
                    <span class="info-value">
                        <span class="role-badge role-{{ $user->role }}">
                            {{ $user->role === 'customer' ? 'عميل' : 'مدير' }}
                        </span>
                    </span>
                </li>
                <li class="info-item">
                    <span class="info-label">الحالة:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $user->is_active ? 'active' : 'inactive' }}">
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </span>
                </li>
                <li class="info-item">
                    <span class="info-label">تاريخ التسجيل:</span>
                    <span class="info-value">{{ $user->created_at->format('Y/m/d H:i') }}</span>
                </li>
                @if($user->email_verified_at)
                <li class="info-item">
                    <span class="info-label">تاريخ تفعيل البريد:</span>
                    <span class="info-value">{{ $user->email_verified_at->format('Y/m/d H:i') }}</span>
                </li>
                @endif
            </ul>
        </div>

        <!-- Category & Purchase Info -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon blue">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3 class="card-title">الفئة والمشتريات</h3>
            </div>

            <ul class="info-list">
                <li class="info-item">
                    <span class="info-label">الفئة الحالية:</span>
                    <span class="info-value">
                        @if($user->userCategory)
                            <span class="status-badge status-active">{{ $user->userCategory->display_name }}</span>
                        @else
                            <span style="color: var(--gray-400);">غير محدد</span>
                        @endif
                    </span>
                </li>
                @if($user->userCategory)
                <li class="info-item">
                    <span class="info-label">خصم الفئة:</span>
                    <span class="info-value">{{ $user->userCategory->discount_percentage }}%</span>
                </li>
                <li class="info-item">
                    <span class="info-label">تحديث الفئة:</span>
                    <span class="info-value">
                        {{ $user->category_updated_at ? $user->category_updated_at->format('Y/m/d') : 'لم يتم التحديث' }}
                    </span>
                </li>
                @endif
                <li class="info-item">
                    <span class="info-label">إجمالي المشتريات:</span>
                    <span class="info-value">{{ number_format($user->total_purchase_amount, 2) }} ج.م</span>
                </li>
                <li class="info-item">
                    <span class="info-label">عدد الطلبات:</span>
                    <span class="info-value">{{ number_format($user->total_orders_count) }}</span>
                </li>
                @if($user->total_orders_count > 0)
                <li class="info-item">
                    <span class="info-label">متوسط قيمة الطلب:</span>
                    <span class="info-value">
                        {{ number_format($user->total_purchase_amount / $user->total_orders_count, 2) }} ج.م
                    </span>
                </li>
                @endif
            </ul>
        </div>

        <!-- Statistics -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon green">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="card-title">إحصائيات المستخدم</h3>
            </div>

            <div class="stats-grid">
                <div class="stat-card-small">
                    <div class="stat-value-small">{{ number_format($userStats['total_orders']) }}</div>
                    <div class="stat-label-small">إجمالي الطلبات</div>
                </div>
                <div class="stat-card-small">
                    <div class="stat-value-small">{{ number_format($userStats['total_spent'], 2) }}</div>
                    <div class="stat-label-small">إجمالي المبلغ (ج.م)</div>
                </div>
                <div class="stat-card-small">
                    <div class="stat-value-small">{{ number_format($userStats['avg_order_value'], 2) }}</div>
                    <div class="stat-label-small">متوسط قيمة الطلب</div>
                </div>
                <div class="stat-card-small">
                    <div class="stat-value-small">{{ $userStats['loyalty_points'] }}</div>
                    <div class="stat-label-small">نقاط الولاء</div>
                </div>
            </div>

            <ul class="info-list">
                <li class="info-item">
                    <span class="info-label">آخر طلب:</span>
                    <span class="info-value">
                        {{ $userStats['last_order_date'] ?? 'لا توجد طلبات' }}
                    </span>
                </li>
                <li class="info-item">
                    <span class="info-label">الفئة المفضلة:</span>
                    <span class="info-value">{{ $userStats['favorite_category'] }}</span>
                </li>
            </ul>
        </div>

        <!-- Account Security -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon purple">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="card-title">أمان الحساب</h3>
            </div>

            <ul class="info-list">
                <li class="info-item">
                    <span class="info-label">حالة البريد الإلكتروني:</span>
                    <span class="info-value">
                        @if($user->email_verified_at)
                            <span class="status-badge status-active">مُفعل</span>
                        @else
                            <span class="status-badge status-inactive">غير مُفعل</span>
                        @endif
                    </span>
                </li>
                <li class="info-item">
                    <span class="info-label">آخر تسجيل دخول:</span>
                    <span class="info-value">
                        {{ $user->last_login_at ? $user->last_login_at->format('Y/m/d H:i') : 'لم يسجل دخول من قبل' }}
                    </span>
                </li>
                <li class="info-item">
                    <span class="info-label">آخر تغيير كلمة مرور:</span>
                    <span class="info-value">
                        {{ $user->password_changed_at ? $user->password_changed_at->format('Y/m/d H:i') : 'لم يتم التغيير' }}
                    </span>
                </li>
            </ul>

            <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn-primary" onclick="resetUserPassword({{ $user->id }})">
                    <i class="fas fa-key"></i>
                    إعادة تعيين كلمة المرور
                </button>
                @if(!$user->email_verified_at)
                <button class="btn-secondary" onclick="sendVerificationEmail({{ $user->id }})">
                    <i class="fas fa-envelope"></i>
                    إرسال رابط التفعيل
                </button>
                @endif
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="content-card full-width-card">
            <div class="card-header">
                <div class="card-icon orange">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="card-title">سجل النشاطات</h3>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h4 class="timeline-title">إنشاء الحساب</h4>
                        <p class="timeline-text">تم إنشاء حساب {{ $user->role === 'customer' ? 'عميل' : 'تاجر' }} جديد</p>
                        <p class="timeline-date">{{ $user->created_at->format('Y/m/d H:i') }}</p>
                    </div>
                </div>

                @if($user->email_verified_at)
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h4 class="timeline-title">تفعيل البريد الإلكتروني</h4>
                        <p class="timeline-text">تم تفعيل البريد الإلكتروني بنجاح</p>
                        <p class="timeline-date">{{ $user->email_verified_at->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
                @endif

                @if($user->category_updated_at)
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h4 class="timeline-title">تحديث الفئة</h4>
                        <p class="timeline-text">تم تحديث الفئة إلى: {{ $user->userCategory->display_name ?? 'غير محدد' }}</p>
                        <p class="timeline-date">{{ $user->category_updated_at->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
                @endif

                @if($user->last_login_at)
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h4 class="timeline-title">آخر تسجيل دخول</h4>
                        <p class="timeline-text">تم تسجيل الدخول للحساب</p>
                        <p class="timeline-date">{{ $user->last_login_at->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle User Status
async function toggleUserStatus(userId) {
    if (!confirm('هل أنت متأكد من تغيير حالة المستخدم؟')) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.users.index') }}/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث الحالة', 'error');
    } finally {
        hideLoading();
    }
}

// Reset User Password
async function resetUserPassword(userId) {
    if (!confirm('هل أنت متأكد من إعادة تعيين كلمة المرور؟')) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.users.index') }}/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(`${data.message}. كلمة المرور الجديدة: ${data.new_password}`, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء إعادة تعيين كلمة المرور', 'error');
    } finally {
        hideLoading();
    }
}

// Send Verification Email
async function sendVerificationEmail(userId) {
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.users.index') }}/${userId}/send-verification`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء إرسال رابط التفعيل', 'error');
    } finally {
        hideLoading();
    }
}

// Utility Functions
function showLoading() {
    // Simple loading indicator
    document.body.style.cursor = 'wait';
}

function hideLoading() {
    document.body.style.cursor = 'default';
}

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
