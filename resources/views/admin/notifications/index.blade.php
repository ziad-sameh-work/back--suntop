@extends('layouts.admin')

@section('title', 'إدارة الإشعارات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">
            <i class="fas fa-bell text-primary"></i>
            إدارة الإشعارات
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إنشاء إشعار
            </a>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendToAllModal">
                <i class="fas fa-broadcast-tower"></i> إرسال للجميع
            </button>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cleanOldModal">
                <i class="fas fa-trash-alt"></i> تنظيف القديمة
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h4 mb-0">{{ number_format($stats['total']) }}</div>
                            <div class="small">إجمالي الإشعارات</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h4 mb-0">{{ number_format($stats['unread']) }}</div>
                            <div class="small">غير مقروءة</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell-slash fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h4 mb-0">{{ number_format($stats['read']) }}</div>
                            <div class="small">مقروءة</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h4 mb-0">{{ count($stats['by_type']) }}</div>
                            <div class="small">أنواع مختلفة</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter"></i>
                فلاتر البحث
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.notifications.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">البحث</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ $filters['search'] ?? '' }}" placeholder="البحث في العنوان أو المحتوى">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="type" class="form-label">النوع</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">جميع الأنواع</option>
                            @foreach(\App\Models\Notification::TYPES as $key => $value)
                                <option value="{{ $key }}" {{ ($filters['type'] ?? '') === $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="priority" class="form-label">الأولوية</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="">جميع الأولويات</option>
                            @foreach(\App\Models\Notification::PRIORITIES as $key => $value)
                                <option value="{{ $key }}" {{ ($filters['priority'] ?? '') === $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="is_read" class="form-label">حالة القراءة</label>
                        <select class="form-select" id="is_read" name="is_read">
                            <option value="">الجميع</option>
                            <option value="true" {{ ($filters['is_read'] ?? '') === 'true' ? 'selected' : '' }}>مقروءة</option>
                            <option value="false" {{ ($filters['is_read'] ?? '') === 'false' ? 'selected' : '' }}>غير مقروءة</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="target_type" class="form-label">نوع الإرسال</label>
                        <select class="form-select" id="target_type" name="target_type">
                            <option value="">جميع الأنواع</option>
                            @foreach(\App\Models\Notification::TARGET_TYPES as $key => $value)
                                <option value="{{ $key }}" {{ ($filters['target_type'] ?? '') === $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="user_id" class="form-label">المستخدم</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">جميع المستخدمين</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> مسح الفلاتر
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">قائمة الإشعارات</h5>
        </div>
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>المستخدم</th>
                                <th>النوع</th>
                                <th>نوع الإرسال</th>
                                <th>الأولوية</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                                <tr class="{{ !$notification->is_read ? 'table-warning' : '' }}">
                                    <td>
                                        <div class="fw-bold">{{ $notification->title }}</div>
                                        <div class="text-muted small">{{ Str::limit($notification->message, 60) }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $notification->user->name }}</div>
                                        <div class="text-muted small">{{ $notification->user->email }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $notification->type === 'shipment' ? 'info' : ($notification->type === 'offer' ? 'success' : ($notification->type === 'reward' ? 'warning' : 'secondary')) }}">
                                            {{ $notification->type_name }}
                                        </span>
                                        @if($notification->alert_type)
                                            <br><small class="text-muted">{{ $notification->alert_type_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $notification->target_type === 'user' ? 'primary' : ($notification->target_type === 'category' ? 'info' : 'success') }}">
                                            {{ $notification->target_type_name }}
                                        </span>
                                        @if($notification->target_type === 'category' && $notification->userCategory)
                                            <br><small class="text-muted">{{ $notification->userCategory->display_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $notification->priority === 'high' ? 'danger' : ($notification->priority === 'medium' ? 'warning' : 'secondary') }}">
                                            {{ $notification->priority_name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($notification->is_read)
                                            <span class="badge bg-success">مقروء</span>
                                        @else
                                            <span class="badge bg-warning">غير مقروء</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $notification->created_at->format('Y-m-d') }}</div>
                                        <div class="text-muted small">{{ $notification->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.notifications.show', $notification->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}" 
                                                  class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $notifications->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5>لا توجد إشعارات</h5>
                    <p class="text-muted">لم يتم العثور على إشعارات تطابق معايير البحث</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Send to All Modal -->
<div class="modal fade" id="sendToAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.notifications.send-to-all') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إرسال إشعار للجميع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="broadcast_title" class="form-label">العنوان</label>
                        <input type="text" class="form-control" id="broadcast_title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="broadcast_message" class="form-label">المحتوى</label>
                        <textarea class="form-control" id="broadcast_message" name="message" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="broadcast_body" class="form-label">تفاصيل إضافية (اختياري)</label>
                        <textarea class="form-control" id="broadcast_body" name="body" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="broadcast_type" class="form-label">النوع</label>
                            <select class="form-select" id="broadcast_type" name="type" required>
                                @foreach(\App\Models\Notification::TYPES as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="broadcast_alert_type" class="form-label">نوع التنبيه</label>
                            <select class="form-select" id="broadcast_alert_type" name="alert_type" required>
                                @foreach(\App\Models\Notification::ALERT_TYPES as $key => $value)
                                    <option value="{{ $key }}" {{ $key === 'info' ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="broadcast_priority" class="form-label">الأولوية</label>
                            <select class="form-select" id="broadcast_priority" name="priority" required>
                                @foreach(\App\Models\Notification::PRIORITIES as $key => $value)
                                    <option value="{{ $key }}" {{ $key === 'medium' ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role_filter" class="form-label">فلترة حسب الدور</label>
                        <select class="form-select" id="role_filter" name="role_filter">
                            <option value="">جميع المستخدمين</option>
                            <option value="customer">العملاء فقط</option>
                            <option value="admin">الإداريين فقط</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="broadcast_action_url" class="form-label">رابط الإجراء (اختياري)</label>
                        <input type="text" class="form-control" id="broadcast_action_url" name="action_url">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إرسال للجميع</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Clean Old Modal -->
<div class="modal fade" id="cleanOldModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.notifications.clean-old') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">تنظيف الإشعارات القديمة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="days_old" class="form-label">حذف الإشعارات الأقدم من (بالأيام)</label>
                        <input type="number" class="form-control" id="days_old" name="days_old" value="30" min="1" max="365" required>
                        <div class="form-text">سيتم حذف جميع الإشعارات الأقدم من العدد المحدد من الأيام</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning" onclick="return confirm('هل أنت متأكد من حذف الإشعارات القديمة؟')">
                        تنظيف الآن
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
