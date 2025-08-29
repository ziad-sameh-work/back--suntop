@extends('admin.layouts.app')

@section('title', 'شات Firebase Real-Time - ' . $chat->subject)

@section('content')
<div class="container-fluid">
    <!-- Chat Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-2">🔥 Firebase Real-Time Chat</h4>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-primary me-2">{{ $chat->subject }}</span>
                                <span class="badge badge-{{ $chat->status === 'open' ? 'success' : ($chat->status === 'closed' ? 'secondary' : 'warning') }}">
                                    {{ $chat->status }}
                                </span>
                                <span class="badge badge-info ms-2">{{ $chat->priority }}</span>
                            </div>
                            <small class="text-muted mt-1">
                                العميل: {{ $chat->customer->full_name ?? $chat->customer->name }} 
                                ({{ $chat->customer->email }})
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <!-- Firebase Connection Status -->
                            <div id="firebase-status" class="mb-2">
                                <span class="badge badge-secondary" id="connection-status">
                                    <i class="fas fa-spinner fa-spin"></i> جاري الاتصال...
                                </span>
                            </div>
                            
                            <!-- Customer Online Status -->
                            <div id="customer-status" class="mb-2">
                                <span class="badge badge-light" id="customer-online-status">
                                    <i class="fas fa-circle text-muted"></i> حالة العميل غير معروفة
                                </span>
                            </div>
                            
                            <!-- Chat Actions -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                        data-bs-toggle="dropdown">
                                    إجراءات
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="updateChatStatus('in_progress')">
                                        <i class="fas fa-play"></i> قيد المعالجة
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateChatStatus('resolved')">
                                        <i class="fas fa-check"></i> تم الحل
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateChatStatus('closed')">
                                        <i class="fas fa-times"></i> إغلاق
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#assignModal">
                                        <i class="fas fa-user-plus"></i> تعيين مدير
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Interface -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <!-- Use Firebase Chat Interface -->
                    @livewire('firebase-chat-interface', ['chat' => $chat])
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Statistics (Optional Sidebar) -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> إحصائيات الشات</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="text-primary mb-1" id="total-messages">{{ $chat->messages_count ?? 0 }}</h5>
                                <small class="text-muted">إجمالي الرسائل</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-success mb-1" id="response-time">< 2 دقيقة</h5>
                            <small class="text-muted">متوسط الرد</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0"><i class="fas fa-fire"></i> حالة Firebase</h6>
                </div>
                <div class="card-body">
                    <div id="firebase-info">
                        <div class="d-flex justify-content-between mb-2">
                            <span>قاعدة البيانات:</span>
                            <span class="badge badge-success" id="db-status">متصل</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>الرسائل Real-time:</span>
                            <span class="badge badge-success" id="messages-status">فعال</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>الإشعارات:</span>
                            <span class="badge badge-success" id="notifications-status">فعال</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary w-100 mt-3" onclick="testFirebaseConnection()">
                        <i class="fas fa-sync"></i> اختبار الاتصال
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-users"></i> معلومات المشاركين</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>العميل:</strong><br>
                        <span class="text-muted">{{ $chat->customer->full_name ?? $chat->customer->name }}</span>
                        <br><small>{{ $chat->customer->email }}</small>
                    </div>
                    @if($chat->assignedAdmin)
                        <div class="mb-3">
                            <strong>المدير المعين:</strong><br>
                            <span class="text-muted">{{ $chat->assignedAdmin->full_name ?? $chat->assignedAdmin->name }}</span>
                        </div>
                    @endif
                    <div>
                        <strong>تم الإنشاء:</strong><br>
                        <small class="text-muted">{{ $chat->created_at->format('Y-m-d H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Admin Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعيين مدير للمحادثة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignForm">
                    <div class="mb-3">
                        <label for="admin_id" class="form-label">اختر المدير</label>
                        <select class="form-select" id="admin_id" name="admin_id" required>
                            <option value="">-- اختر مدير --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ $chat->assigned_admin_id == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->full_name ?? $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="assignAdmin()">تعيين</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Firebase Chat Admin JavaScript
const chatId = {{ $chat->id }};
const adminId = {{ Auth::id() }};
const adminName = '{{ Auth::user()->full_name ?? Auth::user()->name }}';
const csrfToken = '{{ csrf_token() }}';

// Firebase Connection Status
let isFirebaseConnected = false;
let firebaseStatusInterval;

// Initialize Firebase Chat
document.addEventListener('DOMContentLoaded', function() {
    initializeFirebaseChat();
    startStatusMonitoring();
    registerAdminPresence();
});

function initializeFirebaseChat() {
    // Test Firebase connection on load
    testFirebaseConnection();
    
    // Set up periodic connection checks
    firebaseStatusInterval = setInterval(testFirebaseConnection, 30000); // كل 30 ثانية
    
    console.log('Firebase Admin Chat initialized for chat:', chatId);
}

function startStatusMonitoring() {
    // محاكاة مراقبة حالة العميل
    setInterval(updateCustomerStatus, 10000);
}

function updateCustomerStatus() {
    // محاكاة حالة العميل (في التطبيق الحقيقي ستأتي من Firebase)
    const isOnline = Math.random() > 0.5;
    const statusElement = document.getElementById('customer-online-status');
    
    if (isOnline) {
        statusElement.innerHTML = '<i class="fas fa-circle text-success"></i> العميل متصل';
        statusElement.className = 'badge badge-success';
    } else {
        statusElement.innerHTML = '<i class="fas fa-circle text-muted"></i> العميل غير متصل';
        statusElement.className = 'badge badge-light';
    }
}

function registerAdminPresence() {
    // تسجيل حضور الأدمن في Firebase
    // في التطبيق الحقيقي ستستخدم Firebase SDK
    console.log('Admin presence registered:', {
        adminId: adminId,
        adminName: adminName,
        chatId: chatId,
        timestamp: Date.now()
    });
}

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
        updateConnectionStatus(result.success);
        
        if (result.success) {
            console.log('Firebase connection successful:', result);
        } else {
            console.error('Firebase connection failed:', result);
        }
        
    } catch (error) {
        console.error('Error testing Firebase:', error);
        updateConnectionStatus(false);
    }
}

function updateConnectionStatus(isConnected) {
    isFirebaseConnected = isConnected;
    const statusElement = document.getElementById('connection-status');
    
    if (isConnected) {
        statusElement.innerHTML = '<i class="fas fa-circle text-success"></i> Firebase متصل';
        statusElement.className = 'badge badge-success';
        
        // Update Firebase info
        document.getElementById('db-status').textContent = 'متصل';
        document.getElementById('messages-status').textContent = 'فعال';
        document.getElementById('notifications-status').textContent = 'فعال';
    } else {
        statusElement.innerHTML = '<i class="fas fa-circle text-danger"></i> غير متصل';
        statusElement.className = 'badge badge-danger';
        
        // Update Firebase info
        document.getElementById('db-status').textContent = 'غير متصل';
        document.getElementById('messages-status').textContent = 'معطل';
        document.getElementById('notifications-status').textContent = 'معطل';
    }
}

async function updateChatStatus(status) {
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
            showAlert('تم تحديث حالة المحادثة بنجاح', 'success');
            // Reload page to reflect changes
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('فشل في تحديث الحالة: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Error updating status:', error);
        showAlert('حدث خطأ أثناء تحديث الحالة', 'error');
    }
}

async function assignAdmin() {
    const adminId = document.getElementById('admin_id').value;
    
    if (!adminId) {
        showAlert('يجب اختيار مدير', 'error');
        return;
    }
    
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
            showAlert('تم تعيين المدير بنجاح', 'success');
            // Close modal and reload
            bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('فشل في تعيين المدير: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Error assigning admin:', error);
        showAlert('حدث خطأ أثناء تعيين المدير', 'error');
    }
}

function showAlert(message, type) {
    // إنشاء alert بسيط
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // إزالة تلقائية بعد 5 ثواني
    setTimeout(() => {
        const alert = document.querySelector('.alert.position-fixed');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (firebaseStatusInterval) {
        clearInterval(firebaseStatusInterval);
    }
});

// Livewire integration
document.addEventListener('livewire:load', function () {
    // Listen for new messages from Livewire
    Livewire.on('messageAdded', function (messageId) {
        // Update message count
        const totalMessages = document.getElementById('total-messages');
        if (totalMessages) {
            const currentCount = parseInt(totalMessages.textContent) || 0;
            totalMessages.textContent = currentCount + 1;
        }
        
        // Show notification
        showAlert('تم إرسال الرسالة بنجاح', 'success');
        
        console.log('New message added:', messageId);
    });
});
</script>

<style>
/* Additional styles for Firebase Chat */
.bg-gradient-primary {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.card {
    border-radius: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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

.badge-light {
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #dee2e6;
}

.btn-outline-primary {
    border-color: #FF6B35;
    color: #FF6B35;
}

.btn-outline-primary:hover {
    background-color: #FF6B35;
    border-color: #FF6B35;
}

/* Real-time status indicators */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.badge .fas.fa-circle {
    animation: pulse 2s infinite;
}

/* Alert positioning */
.alert.position-fixed {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
@endsection
