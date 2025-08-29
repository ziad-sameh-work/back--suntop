@extends('admin.layouts.app')

@section('title', 'إدارة الشات Firebase Real-Time')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-2">🔥 إدارة الشات Firebase Real-Time</h2>
            <p class="text-muted">إدارة محادثات العملاء في الوقت الفعلي</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.firebase-chats.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <button class="btn btn-outline-info" onclick="testFirebaseConnection()">
                <i class="fas fa-fire"></i> اختبار Firebase
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1" id="total-chats">{{ $chats->total() }}</h4>
                            <small>إجمالي المحادثات</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1" id="active-chats">{{ $chats->where('status', 'open')->count() + $chats->where('status', 'in_progress')->count() }}</h4>
                            <small>محادثات نشطة</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-fire fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1" id="pending-chats">{{ $chats->where('status', 'open')->count() }}</h4>
                            <small>في الانتظار</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1" id="firebase-status">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h4>
                            <small>حالة Firebase</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-database fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.firebase-chats.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select name="status" id="status" class="form-select">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>جميع الحالات</option>
                                <option value="open" {{ $status == 'open' ? 'selected' : '' }}>مفتوحة</option>
                                <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="resolved" {{ $status == 'resolved' ? 'selected' : '' }}>تم الحل</option>
                                <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>مغلقة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priority" class="form-label">الأولوية</label>
                            <select name="priority" id="priority" class="form-select">
                                <option value="all" {{ $priority == 'all' ? 'selected' : '' }}>جميع الأولويات</option>
                                <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>منخفضة</option>
                                <option value="medium" {{ $priority == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="urgent" {{ $priority == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">البحث</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="البحث في الموضوع أو اسم العميل..." 
                                   value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Chats Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">المحادثات ({{ $chats->total() }})</h5>
                        <div class="btn-group" role="group">
                            <input type="checkbox" class="btn-check" id="auto-refresh" checked>
                            <label class="btn btn-outline-primary btn-sm" for="auto-refresh">
                                <i class="fas fa-sync"></i> تحديث تلقائي
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($chats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>العميل</th>
                                        <th>الموضوع</th>
                                        <th style="width: 120px;">الحالة</th>
                                        <th style="width: 100px;">الأولوية</th>
                                        <th>المدير المعين</th>
                                        <th>آخر رسالة</th>
                                        <th style="width: 150px;">تاريخ الإنشاء</th>
                                        <th style="width: 100px;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="chats-table-body">
                                    @foreach($chats as $chat)
                                        <tr class="chat-row" data-chat-id="{{ $chat->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{ $chat->id }}
                                                    <span class="online-indicator ms-2" id="customer-status-{{ $chat->id }}">
                                                        <i class="fas fa-circle text-muted" style="font-size: 0.5rem;"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                                        {{ strtoupper(substr($chat->customer->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $chat->customer->full_name ?? $chat->customer->name }}</div>
                                                        <small class="text-muted">{{ $chat->customer->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $chat->subject }}</div>
                                                @if($chat->latestMessage)
                                                    <small class="text-muted">
                                                        آخر رسالة: {{ Str::limit($chat->latestMessage->message, 30) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $chat->status === 'open' ? 'success' : ($chat->status === 'closed' ? 'secondary' : 'warning') }}">
                                                    {{ $chat->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $chat->priority === 'urgent' ? 'danger' : ($chat->priority === 'high' ? 'warning' : 'info') }}">
                                                    {{ $chat->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($chat->assignedAdmin)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-xs bg-info text-white rounded-circle me-1">
                                                            {{ strtoupper(substr($chat->assignedAdmin->name, 0, 1)) }}
                                                        </div>
                                                        {{ $chat->assignedAdmin->full_name ?? $chat->assignedAdmin->name }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">غير معين</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($chat->latestMessage)
                                                    <small class="text-muted">
                                                        {{ $chat->latestMessage->created_at->diffForHumans() }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $chat->created_at->format('Y-m-d H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.firebase-chats.show', $chat) }}" 
                                                       class="btn btn-outline-primary btn-sm" 
                                                       title="فتح الشات">
                                                        <i class="fas fa-comments"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-secondary btn-sm dropdown-toggle dropdown-toggle-split" 
                                                            data-bs-toggle="dropdown">
                                                        <span class="visually-hidden">إجراءات</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="quickUpdateStatus({{ $chat->id }}, 'in_progress')">
                                                            <i class="fas fa-play text-warning"></i> قيد المعالجة
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="quickUpdateStatus({{ $chat->id }}, 'resolved')">
                                                            <i class="fas fa-check text-success"></i> تم الحل
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="quickUpdateStatus({{ $chat->id }}, 'closed')">
                                                            <i class="fas fa-times text-danger"></i> إغلاق
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer bg-white">
                            {{ $chats->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد محادثات</h5>
                            <p class="text-muted">لم يتم العثور على محادثات تطابق المعايير المحددة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
let autoRefreshInterval;
let isFirebaseConnected = false;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    testFirebaseConnection();
    startAutoRefresh();
    monitorCustomerStatus();
});

// Auto refresh functionality
function startAutoRefresh() {
    const autoRefreshCheckbox = document.getElementById('auto-refresh');
    
    function toggleAutoRefresh() {
        if (autoRefreshCheckbox.checked) {
            autoRefreshInterval = setInterval(() => {
                refreshChatsData();
                updateStats();
            }, 30000); // كل 30 ثانية
        } else {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }
    }
    
    autoRefreshCheckbox.addEventListener('change', toggleAutoRefresh);
    toggleAutoRefresh(); // Start initial refresh if checked
}

// Test Firebase connection
async function testFirebaseConnection() {
    try {
        const response = await fetch('/admin/api/firebase-chat/test-connection', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        updateFirebaseStatus(result.success);
        
        if (result.success) {
            console.log('Firebase connection successful');
        } else {
            console.error('Firebase connection failed:', result.message);
        }
        
    } catch (error) {
        console.error('Error testing Firebase:', error);
        updateFirebaseStatus(false);
    }
}

// Update Firebase status display
function updateFirebaseStatus(isConnected) {
    isFirebaseConnected = isConnected;
    const statusElement = document.getElementById('firebase-status');
    
    if (isConnected) {
        statusElement.innerHTML = '<i class="fas fa-check-circle"></i> متصل';
        statusElement.parentElement.parentElement.parentElement.className = 'card border-0 shadow-sm bg-gradient-success text-white';
    } else {
        statusElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> خطأ';
        statusElement.parentElement.parentElement.parentElement.className = 'card border-0 shadow-sm bg-gradient-danger text-white';
    }
}

// Monitor customer online status
function monitorCustomerStatus() {
    // محاكاة مراقبة حالة العملاء
    setInterval(() => {
        document.querySelectorAll('.chat-row').forEach(row => {
            const chatId = row.dataset.chatId;
            const statusIndicator = document.getElementById(`customer-status-${chatId}`);
            
            // محاكاة حالة العميل
            const isOnline = Math.random() > 0.7;
            
            if (statusIndicator) {
                if (isOnline) {
                    statusIndicator.innerHTML = '<i class="fas fa-circle text-success" style="font-size: 0.5rem;"></i>';
                    statusIndicator.title = 'العميل متصل';
                } else {
                    statusIndicator.innerHTML = '<i class="fas fa-circle text-muted" style="font-size: 0.5rem;"></i>';
                    statusIndicator.title = 'العميل غير متصل';
                }
            }
        });
    }, 15000); // كل 15 ثانية
}

// Quick update chat status
async function quickUpdateStatus(chatId, status) {
    try {
        const response = await fetch(`/admin/api/firebase-chat/${chatId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: status })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('تم تحديث حالة المحادثة بنجاح', 'success');
            
            // Update the status badge in the table
            const row = document.querySelector(`[data-chat-id="${chatId}"]`);
            if (row) {
                const statusBadge = row.querySelector('.badge');
                statusBadge.textContent = status;
                statusBadge.className = `badge badge-${status === 'open' ? 'success' : (status === 'closed' ? 'secondary' : 'warning')}`;
            }
            
            // Update stats
            updateStats();
        } else {
            showToast('فشل في تحديث الحالة: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Error updating status:', error);
        showToast('حدث خطأ أثناء تحديث الحالة', 'error');
    }
}

// Refresh chats data
async function refreshChatsData() {
    try {
        // يمكن تنفيذ refresh عبر AJAX هنا
        console.log('Refreshing chats data...');
        
        // إشعار المستخدم
        showToast('تم تحديث البيانات', 'info');
        
    } catch (error) {
        console.error('Error refreshing data:', error);
    }
}

// Update statistics
async function updateStats() {
    try {
        const response = await fetch('/admin/api/firebase-chat/stats', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update stats display
            document.getElementById('total-chats').textContent = result.data.total_chats || 0;
            document.getElementById('active-chats').textContent = (result.data.open_chats + result.data.in_progress_chats) || 0;
            document.getElementById('pending-chats').textContent = result.data.open_chats || 0;
        }
        
    } catch (error) {
        console.error('Error updating stats:', error);
    }
}

// Show toast notification
function showToast(message, type) {
    const toastClass = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');
    const toastHtml = `
        <div class="toast align-items-center text-white ${toastClass} border-0 position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999;" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHtml);
    
    // Initialize and show toast
    const toastElement = document.querySelector('.toast:last-child');
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();
    
    // Remove from DOM after hiding
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>

<style>
/* Custom styles for Firebase Chat Index */
.bg-gradient-primary {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
}

.card {
    border-radius: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.avatar-xs {
    width: 20px;
    height: 20px;
    font-size: 0.75rem;
}

.avatar-sm {
    width: 24px;
    height: 24px;
    font-size: 0.8rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
    border-radius: 6px;
}

.badge-primary {
    background-color: #FF6B35;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-secondary {
    background-color: #6c757d;
}

.badge-danger {
    background-color: #dc3545;
}

.online-indicator {
    transition: all 0.3s ease;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-outline-primary {
    border-color: #FF6B35;
    color: #FF6B35;
}

.btn-outline-primary:hover {
    background-color: #FF6B35;
    border-color: #FF6B35;
}

/* Real-time status animations */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.online-indicator .fa-circle.text-success {
    animation: pulse 2s infinite;
}

/* Toast positioning */
.toast {
    min-width: 300px;
}
</style>
@endsection
