@extends('layouts.admin')

@section('title', 'تفاصيل الإشعار')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">
            <i class="fas fa-bell text-primary"></i>
            تفاصيل الإشعار #{{ $notification->id }}
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
            <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" 
                        onclick="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                    <i class="fas fa-trash"></i> حذف
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Notification Details -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        تفاصيل الإشعار
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Alert Preview -->
                    <div class="alert alert-{{ 
                        $notification->alert_type === 'info' ? 'info' : 
                        ($notification->alert_type === 'success' ? 'success' : 
                        ($notification->alert_type === 'warning' ? 'warning' : 'danger')) 
                    }} mb-4">
                        <h5 class="alert-heading">
                            <i class="fas fa-bell"></i>
                            {{ $notification->title }}
                        </h5>
                        <p class="mb-2">{{ $notification->message }}</p>
                        @if($notification->body)
                            <hr>
                            <p class="mb-0">{{ $notification->body }}</p>
                        @endif
                        @if($notification->action_url)
                            <hr>
                            <a href="{{ $notification->action_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i>
                                عرض الإجراء
                            </a>
                        @endif
                    </div>

                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">المعلومات الأساسية</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">العنوان:</td>
                                    <td>{{ $notification->title }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">النوع:</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $notification->type_name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">نوع التنبيه:</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $notification->alert_type === 'info' ? 'info' : 
                                            ($notification->alert_type === 'success' ? 'success' : 
                                            ($notification->alert_type === 'warning' ? 'warning' : 'danger')) 
                                        }}">{{ $notification->alert_type_name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">الأولوية:</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $notification->priority === 'high' ? 'danger' : 
                                            ($notification->priority === 'medium' ? 'warning' : 'secondary') 
                                        }}">{{ $notification->priority_name }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">معلومات الإرسال</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">نوع الإرسال:</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $notification->target_type === 'user' ? 'primary' : 
                                            ($notification->target_type === 'category' ? 'info' : 'success') 
                                        }}">{{ $notification->target_type_name }}</span>
                                    </td>
                                </tr>
                                @if($notification->target_type === 'category' && $notification->userCategory)
                                <tr>
                                    <td class="fw-bold">الفئة:</td>
                                    <td>{{ $notification->userCategory->display_name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">حالة القراءة:</td>
                                    <td>
                                        @if($notification->is_read)
                                            <span class="badge bg-success">مقروء</span>
                                            <small class="text-muted d-block">{{ $notification->read_at->format('Y-m-d H:i') }}</small>
                                        @else
                                            <span class="badge bg-warning">غير مقروء</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">تاريخ الإنشاء:</td>
                                    <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">محتوى الإشعار</h6>
                        <div class="border p-3 rounded bg-light">
                            <h6>الرسالة الرئيسية:</h6>
                            <p class="mb-3">{{ $notification->message }}</p>
                            
                            @if($notification->body)
                                <h6>التفاصيل الإضافية:</h6>
                                <p class="mb-0">{{ $notification->body }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Data -->
                    @if($notification->data && count($notification->data) > 0)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">بيانات إضافية</h6>
                        <div class="bg-light p-3 rounded">
                            <pre class="mb-0"><code>{{ json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </div>
                    @endif

                    <!-- Action URL -->
                    @if($notification->action_url)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">رابط الإجراء</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $notification->action_url }}" readonly>
                            <a href="{{ $notification->action_url }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i>
                                فتح الرابط
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recipient Information -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i>
                        معلومات المستقبل
                    </h5>
                </div>
                <div class="card-body">
                    @if($notification->user)
                        <div class="text-center mb-3">
                            <div class="avatar-circle bg-primary text-white mb-2">
                                {{ substr($notification->user->name, 0, 1) }}
                            </div>
                            <h6 class="mb-1">{{ $notification->user->name }}</h6>
                            <p class="text-muted small mb-0">{{ $notification->user->email }}</p>
                            @if($notification->user->role !== 'customer')
                                <span class="badge bg-secondary mt-1">{{ $notification->user->role }}</span>
                            @endif
                        </div>

                        <div class="border-top pt-3">
                            <h6 class="text-muted mb-3">تفاصيل إضافية</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">الدور:</td>
                                    <td>{{ $notification->user->role }}</td>
                                </tr>
                                @if($notification->user->userCategory)
                                <tr>
                                    <td class="fw-bold">الفئة:</td>
                                    <td>{{ $notification->user->userCategory->display_name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">الحالة:</td>
                                    <td>
                                        @if($notification->user->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">تاريخ التسجيل:</td>
                                    <td>{{ $notification->user->created_at->format('Y-m-d') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="border-top pt-3">
                            <a href="#" class="btn btn-sm btn-outline-primary w-100 mb-2">
                                <i class="fas fa-user"></i>
                                عرض ملف المستخدم
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-info w-100">
                                <i class="fas fa-bell"></i>
                                إرسال إشعار جديد
                            </a>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p>المستخدم غير متوفر</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notification Statistics -->
            <div class="card shadow mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i>
                        إحصائيات الإشعار
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 text-primary">{{ $notification->id }}</div>
                                <div class="small text-muted">رقم الإشعار</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-{{ $notification->is_read ? 'success' : 'warning' }}">
                                {{ $notification->is_read ? 'مقروء' : 'غير مقروء' }}
                            </div>
                            <div class="small text-muted">الحالة</div>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 mt-3">
                        <div class="small text-muted mb-2">الوقت المنقضي:</div>
                        <div class="fw-bold">{{ $notification->time_ago }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 auto;
}

.table-sm td {
    padding: 0.5rem 0.25rem;
    border-top: 1px solid #dee2e6;
}

.table-sm td:first-child {
    width: 40%;
}

code {
    color: #e83e8c;
    font-size: 0.875em;
}

pre {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.25rem;
    color: #495057;
    max-height: 200px;
    overflow-y: auto;
}
</style>
@endpush
@endsection
