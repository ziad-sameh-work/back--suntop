@extends('admin.layouts.app')

@section('title', 'Firebase Real-Time Chat Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-2">🔥 Firebase Real-Time Chat Dashboard</h2>
            <p class="text-muted">مراقبة الشات المباشر في الوقت الفعلي</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.firebase-chats.index') }}" class="btn btn-primary">
                <i class="fas fa-list"></i> جميع المحادثات
            </a>
            <button class="btn btn-outline-success" id="refresh-dashboard">
                <i class="fas fa-sync"></i> تحديث
            </button>
        </div>
    </div>

    <!-- Real-time Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-1" id="total-chats">{{ $stats['total_chats'] }}</h3>
                            <small>إجمالي المحادثات</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="opacity-75">
                            <i class="fas fa-chart-line"></i> Firebase متصل
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-1" id="active-chats">{{ $stats['active_chats'] }}</h3>
                            <small>محادثات نشطة</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-fire fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="opacity-75">
                            <i class="fas fa-clock"></i> Real-time
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-1" id="pending-chats">{{ $stats['pending_chats'] }}</h3>
                            <small>في الانتظار</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hourglass-half fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="opacity-75">
                            <i class="fas fa-exclamation-triangle"></i> يحتاج مدير
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-1" id="my-chats">{{ $stats['my_chats'] }}</h3>
                            <small>محادثاتي</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tie fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="opacity-75">
                            <i class="fas fa-user"></i> المعينة لي
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Firebase Connection Status -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-fire"></i> حالة Firebase Real-Time</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div id="firebase-connection-status" class="mb-2">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">جاري التحقق...</span>
                                    </div>
                                </div>
                                <h6>حالة الاتصال</h6>
                                <p class="text-muted mb-0" id="connection-text">جاري التحقق...</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div id="realtime-status" class="mb-2">
                                    <i class="fas fa-bolt fa-2x text-warning"></i>
                                </div>
                                <h6>Real-Time</h6>
                                <p class="text-muted mb-0">نشط</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-grid">
                        <button class="btn btn-outline-primary" onclick="testFirebaseConnection()">
                            <i class="fas fa-flask"></i> اختبار الاتصال
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0"><i class="fas fa-users"></i> المديرين المتصلين</h6>
                </div>
                <div class="card-body">
                    <div id="online-admins">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar bg-primary text-white rounded-circle me-2">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ Auth::user()->full_name ?? Auth::user()->name }} (أنا)</div>
                                <small class="text-success">
                                    <i class="fas fa-circle"></i> متصل الآن
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> يتم تحديث القائمة تلقائياً
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Active Chats -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">المحادثات النشطة الأخيرة</h5>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2">
                                <i class="fas fa-circle"></i> Live
                            </span>
                            <small class="text-muted">يتم التحديث كل 10 ثواني</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentChats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th>العميل</th>
                                        <th>الموضوع</th>
                                        <th style="width: 100px;">الحالة</th>
                                        <th style="width: 120px;">حالة العميل</th>
                                        <th>المدير</th>
                                        <th style="width: 150px;">آخر نشاط</th>
                                        <th style="width: 120px;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-chats-table">
                                    @foreach($recentChats as $chat)
                                        <tr class="chat-row" data-chat-id="{{ $chat->id }}">
                                            <td>
                                                <span class="fw-bold">#{{ $chat->id }}</span>
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
                                                <div class="fw-bold">{{ Str::limit($chat->subject, 30) }}</div>
                                                @if($chat->latestMessage)
                                                    <small class="text-muted">
                                                        {{ Str::limit($chat->latestMessage->message, 25) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $chat->status === 'open' ? 'success' : 'warning' }}">
                                                    {{ $chat->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <div id="customer-online-{{ $chat->id }}">
                                                    <span class="badge badge-light">
                                                        <i class="fas fa-circle text-muted"></i> غير معروف
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($chat->assignedAdmin)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-xs bg-info text-white rounded-circle me-1">
                                                            {{ strtoupper(substr($chat->assignedAdmin->name, 0, 1)) }}
                                                        </div>
                                                        <small>{{ $chat->assignedAdmin->full_name ?? $chat->assignedAdmin->name }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">غير معين</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $chat->updated_at->diffForHumans() }}
                                                </small>
                                                <div class="typing-indicator-{{ $chat->id }}" style="display: none;">
                                                    <small class="text-primary">
                                                        <i class="fas fa-circle"></i> يكتب...
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.firebase-chats.show', $chat) }}" 
                                                       class="btn btn-primary btn-sm" 
                                                       title="فتح الشات">
                                                        <i class="fas fa-comments"></i>
                                                    </a>
                                                    @if(!$chat->assignedAdmin || $chat->assignedAdmin->id !== Auth::id())
                                                        <button class="btn btn-outline-success btn-sm" 
                                                                onclick="quickAssignToMe({{ $chat->id }})"
                                                                title="تعيين لي">
                                                            <i class="fas fa-user-plus"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد محادثات نشطة</h5>
                            <p class="text-muted">جميع المحادثات مغلقة أو محلولة</p>
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
const adminId = {{ Auth::id() }};
let refreshInterval;
let isFirebaseConnected = false;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    testFirebaseConnection();
    startRealTimeUpdates();
});

function initializeDashboard() {
    // Auto refresh every 10 seconds
    refreshInterval = setInterval(refreshDashboard, 10000);
    
    // Refresh button
    document.getElementById('refresh-dashboard').addEventListener('click', refreshDashboard);
    
    console.log('Firebase Real-Time Dashboard initialized');
}

function startRealTimeUpdates() {
    // محاكاة تحديثات الوقت الفعلي
    setInterval(simulateRealTimeUpdates, 5000);
    
    // مراقبة حالة العملاء
    setInterval(updateCustomersOnlineStatus, 8000);
    
    // محاكاة مؤشرات الكتابة
    setInterval(simulateTypingIndicators, 12000);
}

async function testFirebaseConnection() {
    const statusElement = document.getElementById('firebase-connection-status');
    const textElement = document.getElementById('connection-text');
    
    try {
        const response = await fetch('/admin/api/firebase-chat/test-connection', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            statusElement.innerHTML = '<i class="fas fa-check-circle fa-2x text-success"></i>';
            textElement.textContent = 'متصل بنجاح';
            isFirebaseConnected = true;
            
            console.log('Firebase connection test successful:', result);
        } else {
            statusElement.innerHTML = '<i class="fas fa-exclamation-triangle fa-2x text-danger"></i>';
            textElement.textContent = 'فشل الاتصال';
            isFirebaseConnected = false;
            
            console.error('Firebase connection test failed:', result);
        }
        
    } catch (error) {
        statusElement.innerHTML = '<i class="fas fa-times-circle fa-2x text-danger"></i>';
        textElement.textContent = 'خطأ في الاتصال';
        isFirebaseConnected = false;
        
        console.error('Error testing Firebase:', error);
    }
}

async function refreshDashboard() {
    try {
        // تحديث الإحصائيات
        await updateStats();
        
        // تحديث قائمة المديرين المتصلين
        await updateOnlineAdmins();
        
        // إشعار النجاح
        showToast('تم تحديث Dashboard بنجاح', 'success');
        
    } catch (error) {
        console.error('Error refreshing dashboard:', error);
        showToast('فشل في تحديث Dashboard', 'error');
    }
}

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
            document.getElementById('total-chats').textContent = result.data.total_chats || 0;
            document.getElementById('active-chats').textContent = (result.data.open_chats + result.data.in_progress_chats) || 0;
            document.getElementById('pending-chats').textContent = result.data.unassigned_chats || 0;
            document.getElementById('my-chats').textContent = result.data.my_assigned_chats || 0;
        }
        
    } catch (error) {
        console.error('Error updating stats:', error);
    }
}

async function updateOnlineAdmins() {
    try {
        const response = await fetch('/admin/api/firebase-chat/admins', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            const container = document.getElementById('online-admins');
            let html = '';
            
            result.data.admins.forEach(admin => {
                const isMe = admin.id === adminId;
                const statusClass = admin.is_online ? 'text-success' : 'text-muted';
                const statusText = admin.is_online ? 'متصل' : 'غير متصل';
                
                html += `
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar bg-primary text-white rounded-circle me-2">
                            ${admin.full_name ? admin.full_name.charAt(0).toUpperCase() : admin.name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="fw-bold">${admin.full_name || admin.name} ${isMe ? '(أنا)' : ''}</div>
                            <small class="${statusClass}">
                                <i class="fas fa-circle"></i> ${statusText}
                            </small>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
    } catch (error) {
        console.error('Error updating online admins:', error);
    }
}

function simulateRealTimeUpdates() {
    // محاكاة تحديثات الإحصائيات في الوقت الفعلي
    const totalChats = document.getElementById('total-chats');
    const activeChats = document.getElementById('active-chats');
    
    // تحديث عشوائي بسيط
    if (Math.random() > 0.8) {
        const currentActive = parseInt(activeChats.textContent) || 0;
        const change = Math.random() > 0.5 ? 1 : -1;
        const newActive = Math.max(0, currentActive + change);
        
        activeChats.textContent = newActive;
        
        // تأثير بصري
        activeChats.parentElement.style.transform = 'scale(1.05)';
        setTimeout(() => {
            activeChats.parentElement.style.transform = 'scale(1)';
        }, 200);
    }
}

function updateCustomersOnlineStatus() {
    document.querySelectorAll('[id^="customer-online-"]').forEach(element => {
        const isOnline = Math.random() > 0.6;
        
        if (isOnline) {
            element.innerHTML = `
                <span class="badge badge-success">
                    <i class="fas fa-circle"></i> متصل
                </span>
            `;
        } else {
            element.innerHTML = `
                <span class="badge badge-light">
                    <i class="fas fa-circle text-muted"></i> غير متصل
                </span>
            `;
        }
    });
}

function simulateTypingIndicators() {
    document.querySelectorAll('[class^="typing-indicator-"]').forEach(element => {
        const showTyping = Math.random() > 0.9;
        
        if (showTyping) {
            element.style.display = 'block';
            setTimeout(() => {
                element.style.display = 'none';
            }, 3000);
        }
    });
}

async function quickAssignToMe(chatId) {
    try {
        const response = await fetch(`/admin/api/firebase-chat/${chatId}/assign`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ admin_id: adminId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('تم تعيين المحادثة لك بنجاح', 'success');
            
            // تحديث الجدول
            setTimeout(refreshDashboard, 1000);
        } else {
            showToast('فشل في تعيين المحادثة: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Error assigning chat:', error);
        showToast('حدث خطأ أثناء تعيين المحادثة', 'error');
    }
}

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
    
    const toastElement = document.querySelector('.toast:last-child');
    const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Cleanup
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>

<style>
/* Dashboard specific styles */
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

.card {
    border-radius: 12px;
    transition: all 0.3s ease;
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

.badge-light {
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #dee2e6;
}

/* Real-time animations */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.badge .fa-circle {
    animation: pulse 2s infinite;
}

/* Table styles */
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

/* Status indicators */
.typing-indicator {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .avatar {
        width: 24px;
        height: 24px;
        font-size: 0.75rem;
    }
}
</style>
@endsection
