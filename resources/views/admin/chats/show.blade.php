@extends('layouts.admin')

@section('title', 'محادثة مع ' . $chat->customer->name)

@section('styles')
@livewireStyles
@endsection

    @section('content')
<div class="chat-container">
    <!-- مؤشر تحديث الشات -->
    @livewire('chat-updates-indicator')
    <div class="chat-layout">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <div class="customer-info">
                    <div class="customer-avatar">
                        {{ substr($chat->customer->name, 0, 1) }}
                    </div>
                    <div class="customer-details">
                        <h3 class="customer-name">{{ $chat->customer->name }}</h3>
                        <p class="customer-email">{{ $chat->customer->email }}</p>
                    </div>
                </div>
            </div>

            <div class="chat-info">
                <div class="info-section">
                    <h4 class="info-title">تفاصيل المحادثة</h4>
                    
                    <div class="info-item">
                        <label class="info-label">الموضوع</label>
                        <div class="info-value">{{ $chat->subject }}</div>
                    </div>

                    <div class="info-item">
                        <label class="info-label">الحالة</label>
                        <div class="info-value">
                            <span class="status-badge status-{{ $chat->status }}">
                                @switch($chat->status)
                                    @case('open') مفتوحة @break
                                    @case('in_progress') قيد المعالجة @break
                                    @case('resolved') محلولة @break
                                    @case('closed') مغلقة @break
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <label class="info-label">الأولوية</label>
                        <div class="info-value">
                            <span class="priority-badge priority-{{ $chat->priority }}">
                                @switch($chat->priority)
                                    @case('low') منخفضة @break
                                    @case('medium') متوسطة @break
                                    @case('high') عالية @break
                                    @case('urgent') عاجلة @break
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <label class="info-label">المُعيّن</label>
                        <div class="info-value">
                            {{ $chat->assignedAdmin ? $chat->assignedAdmin->name : 'غير مُعيّن' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <label class="info-label">تاريخ الإنشاء</label>
                        <div class="info-value">{{ $chat->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="info-item">
                        <label class="info-label">آخر رسالة</label>
                        <div class="info-value">{{ $chat->formatted_last_message_time }}</div>
                    </div>

                    <div class="info-item">
                        <label class="info-label">عدد الرسائل</label>
                        <div class="info-value">{{ $chat->messages->count() }}</div>
                    </div>
                </div>

                <div class="actions-section">
                    <h4 class="info-title">إجراءات المحادثة</h4>
                    
                    <!-- Assign Admin -->
                    <form method="POST" action="{{ route('admin.chats.assign', $chat) }}" class="action-form">
                        @csrf
                        <label class="form-label">تعيين مدير</label>
                        <select name="admin_id" class="form-control">
                            <option value="">اختر مدير</option>
                            @foreach(App\Models\User::where('role', 'admin')->get() as $admin)
                                <option value="{{ $admin->id }}" {{ $chat->assigned_admin_id == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-block">تعيين</button>
                    </form>

                    <!-- Update Status -->
                    <form method="POST" action="{{ route('admin.chats.updateStatus', $chat) }}" class="action-form">
                        @csrf
                        <label class="form-label">تغيير الحالة</label>
                        <select name="status" class="form-control">
                            <option value="open" {{ $chat->status == 'open' ? 'selected' : '' }}>مفتوحة</option>
                            <option value="in_progress" {{ $chat->status == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="resolved" {{ $chat->status == 'resolved' ? 'selected' : '' }}>محلولة</option>
                            <option value="closed" {{ $chat->status == 'closed' ? 'selected' : '' }}>مغلقة</option>
                        </select>
                        <button type="submit" class="btn btn-success btn-block">تحديث</button>
                    </form>

                    <!-- Update Priority -->
                    <form method="POST" action="{{ route('admin.chats.updatePriority', $chat) }}" class="action-form">
                        @csrf
                        <label class="form-label">تغيير الأولوية</label>
                        <select name="priority" class="form-control">
                            <option value="low" {{ $chat->priority == 'low' ? 'selected' : '' }}>منخفضة</option>
                            <option value="medium" {{ $chat->priority == 'medium' ? 'selected' : '' }}>متوسطة</option>
                            <option value="high" {{ $chat->priority == 'high' ? 'selected' : '' }}>عالية</option>
                            <option value="urgent" {{ $chat->priority == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                        </select>
                        <button type="submit" class="btn btn-warning btn-block">تحديث</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Chat -->
        <div class="chat-main">
            <div class="chat-header">
                <div class="header-info">
                    <h2 class="chat-title">محادثة مع {{ $chat->customer->name }}</h2>
                    <p class="chat-subject">{{ $chat->subject }}</p>
                </div>
                <a href="{{ route('admin.chats.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>

            <!-- Livewire Chat Interface -->
            @livewire('chat-interface', ['chat' => $chat])
        </div>
    </div>
</div>

<style>
/* Chat Container */
.chat-container {
    background: #f8f9fa;
    min-height: calc(100vh - var(--header-height));
    padding: 0;
}

/* Chat Layout */
.chat-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 20px;
    height: calc(100vh - var(--header-height));
    padding: 20px;
    max-width: none;
}

/* Sidebar */
.chat-sidebar {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid #e3e6f0;
    display: flex;
    flex-direction: column;
    height: fit-content;
}

.sidebar-header {
    background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-light) 100%);
    color: white;
    padding: 20px 25px;
}

.customer-info {
    text-align: center;
}

.customer-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    margin: 0 auto 15px;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.customer-details h3 {
    margin: 0 0 5px 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.customer-details p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.95rem;
}

.chat-info {
    flex: 1;
    overflow-y: auto;
    padding: 0;
}

.info-section, .actions-section {
    padding: 25px;
}

.actions-section {
    border-top: 1px solid #f8f9fa;
    background: #fafbfc;
}

.info-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 20px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #f8f9fa;
}

.info-item {
    margin-bottom: 20px;
}

.info-label {
    font-weight: 600;
    color: #34495e;
    margin-bottom: 8px;
    font-size: 0.9rem;
    display: block;
}

.info-value {
    color: #7f8c8d;
    font-size: 0.95rem;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
}

.status-open { background: #fee2e2; color: #dc2626; }
.status-in_progress { background: #fef3c7; color: #d97706; }
.status-resolved { background: #d1fae5; color: #059669; }
.status-closed { background: #f3f4f6; color: #6b7280; }

.priority-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.priority-low { background: #d1fae5; color: #059669; }
.priority-medium { background: #fef3c7; color: #d97706; }
.priority-high { background: #fed7aa; color: #ea580c; }
.priority-urgent { background: #fee2e2; color: #dc2626; }

.action-form {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 0.9rem;
    display: block;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
    margin-bottom: 12px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #FF6B35;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.btn-block {
    width: 100%;
    justify-content: center;
}

.btn-primary {
    background: var(--suntop-orange);
    color: white;
}

.btn-primary:hover {
    background: var(--suntop-orange-dark);
    transform: translateY(-1px);
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-outline-light {
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    text-decoration: none;
}

/* Main Chat */
.chat-main {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid #e3e6f0;
    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--header-height) - 40px);
}

.chat-header {
    background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-light) 100%);
    color: white;
    padding: 20px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.header-info h2 {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0 0 5px 0;
}

.header-info p {
    opacity: 0.9;
    font-size: 0.95rem;
    margin: 0;
}

/* Chat Interface Overrides */
.chat-interface {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 25px;
    background: #f8f9fa;
}

.message {
    display: flex;
    margin-bottom: 20px;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message.admin {
    justify-content: flex-end;
}

.message-bubble {
    max-width: 70%;
    padding: 15px 20px;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.message.customer .message-bubble {
    background: white;
    border-bottom-left-radius: 5px;
}

.message.admin .message-bubble {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    border-bottom-right-radius: 5px;
}

.message-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.8rem;
    opacity: 0.7;
}

.message-sender {
    font-weight: 600;
}

.message-time {
    font-size: 0.75rem;
}

.message-content {
    line-height: 1.5;
    word-wrap: break-word;
}

.empty-messages {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.empty-messages i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.message-input-container {
    background: white;
    border-top: 1px solid #e9ecef;
    padding: 25px;
}

/* Responsive */
@media (max-width: 1200px) {
    .chat-layout {
        grid-template-columns: 300px 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-content {
        padding: 10px;
    }
    
    .chat-layout {
        grid-template-columns: 1fr;
        grid-template-rows: auto 1fr;
        height: auto;
    }
    
    .chat-sidebar {
        max-height: 400px;
    }
    
    .chat-info {
        max-height: 300px;
    }
    
    .message-bubble {
        max-width: 90%;
    }
    
    .chat-header {
        padding: 15px 20px;
    }
    
    .messages-container {
        padding: 15px;
    }
    
    .customer-avatar {
        width: 60px;
        height: 60px;
        font-size: 1.2rem;
    }
    
    .customer-name {
        font-size: 1.1rem;
    }
    
    .chat-title {
        font-size: 1.2rem;
    }
    
    .chat-sidebar {
        order: 2;
        height: auto;
    }
    
    .chat-main {
        order: 1;
        height: 60vh;
        min-height: 400px;
    }
}

@media (max-width: 480px) {
    .chat-layout {
        padding: 10px;
        gap: 10px;
    }
    
    .chat-main {
        height: 50vh;
        min-height: 350px;
    }
    
    .sidebar-header,
    .chat-header {
        padding: 12px 15px;
    }
    
    .customer-name,
    .chat-title {
        font-size: 1rem;
    }
}
</style>
@endsection

@section('scripts')
@livewireScripts
<!-- Pusher JavaScript Library -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializePusherChat();
    
    // Listen for Livewire events to refresh messages
    if (typeof Livewire !== 'undefined') {
        Livewire.on('refreshMessages', function() {
            console.log('Livewire refreshMessages event received');
            // Force reload of messages in Livewire component
            setTimeout(() => {
                window.location.reload();
            }, 500);
        });
    }
    
    // Auto-scroll to bottom on page load
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Initialize Pusher for real-time chat updates
    initializePusherChat();
    
    // Handle scroll to bottom event
    window.addEventListener('scrollToBottom', function() {
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
    
    // Handle image modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('message-image')) {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                cursor: pointer;
            `;
            
            const img = document.createElement('img');
            img.src = e.target.src;
            img.style.cssText = `
                max-width: 90%;
                max-height: 90%;
                border-radius: 10px;
            `;
            
            modal.appendChild(img);
            document.body.appendChild(modal);
            
            modal.addEventListener('click', function() {
                document.body.removeChild(modal);
            });
        }
    });
});

let pusher = null;
let chatChannel = null;
const chatId = {{ $chat->id }};

function initializePusherChat() {
    try {
        // Initialize Pusher with your credentials
        pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        });

        // Subscribe to specific chat channel (regular chat uses public channel)
        chatChannel = pusher.subscribe(`chat.${chatId}`);
        
        // Make pusher and channel available globally for chat interface
        window.pusher = pusher;
        window.adminChannel = chatChannel;

        // Listen for new messages using the correct event name
        chatChannel.bind('message.new', function(data) {
            console.log('🔔 New chat message received:', data);
            if (data.message.chat_id == chatId) {
                // Refresh Livewire component immediately
                if (typeof Livewire !== 'undefined') {
                    Livewire.emit('refreshMessages');
                }
                showMessageNotification(data.message);
            }
        });

        // Connection status handling
        pusher.connection.bind('connected', function() {
            console.log('✅ Pusher connected successfully for chat');
            showConnectionStatus('connected');
        });

        pusher.connection.bind('disconnected', function() {
            console.log('❌ Pusher disconnected from chat');
            showConnectionStatus('disconnected');
        });

        chatChannel.bind('pusher:subscription_succeeded', function() {
            console.log('✅ Successfully subscribed to chat channel');
            showConnectionStatus('subscribed');
        });

        chatChannel.bind('pusher:subscription_error', function(error) {
            console.error('🔴 Chat channel subscription error:', error);
            showConnectionStatus('subscription_error');
        });

    } catch (error) {
        console.error('Failed to initialize Pusher for chat:', error);
        showConnectionStatus('init_error');
    }
}

function addMessageToChat(message) {
    const messagesContainer = document.querySelector('.messages-container');
    if (!messagesContainer) {
        console.log('Messages container not found');
        return;
    }

    // Check if message already exists
    if (document.querySelector(`[data-message-id="${message.id}"]`)) {
        return; // Message already exists
    }

    const isAdmin = message.sender_type === 'admin';
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${message.sender_type}`;
    messageDiv.setAttribute('data-message-id', message.id);
    
    const timeStamp = message.formatted_time || new Date().toLocaleTimeString('ar-EG', {hour: '2-digit', minute: '2-digit'});
    const senderName = message.sender ? message.sender.name : (isAdmin ? 'الإدارة' : 'عميل');
    
    messageDiv.innerHTML = `
        <div class="message-bubble">
            <div class="message-header">
                <span class="message-sender">
                    ${isAdmin ? senderName + ' (الإدارة)' : senderName}
                </span>
                <span class="message-time">${timeStamp}</span>
            </div>
            <div class="message-content">${message.message}</div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    
    // Animate new message
    messageDiv.style.opacity = '0';
    messageDiv.style.transform = 'translateY(20px)';
    setTimeout(() => {
        messageDiv.style.transition = 'all 0.3s ease';
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translateY(0)';
    }, 50);
    
    // Scroll to bottom
    scrollToBottom();
    
    console.log('Message added to chat:', message);
}

function scrollToBottom() {
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

function showMessageNotification(message) {
    // Only show notification if message is not from current admin
    if (message.sender_type === 'admin' && message.user.id === {{ auth()->id() }}) {
        return; // Don't show notification for own messages
    }

    const senderName = message.sender_type === 'customer' ? 'العميل' : 'الإدارة';
    const title = `رسالة جديدة من ${senderName}`;
    
    // Browser notification
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: message.message.substring(0, 100),
            icon: '/favicon.ico'
        });
    }
    
    // In-page notification
    showInPageNotification(title, message.message);
}

function showInPageNotification(title, message) {
    const notification = document.createElement('div');
    notification.className = 'chat-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <strong>${title}</strong>
            <p>${message.substring(0, 80)}...</p>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #17a2b8;
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 15px;
        max-width: 400px;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 4000);
}

function showConnectionStatus(status) {
    let statusText = '';
    let statusColor = '';
    
    switch (status) {
        case 'connected':
            statusText = '🟢 متصل';
            statusColor = '#28a745';
            break;
        case 'subscribed':
            statusText = '🟢 شات مباشر';
            statusColor = '#28a745';
            break;
        case 'disconnected':
            statusText = '🟡 منقطع';
            statusColor = '#ffc107';
            break;
        case 'error':
        case 'subscription_error':
        case 'init_error':
            statusText = '🔴 خطأ';
            statusColor = '#dc3545';
            break;
    }
    
    // Create or update status indicator
    let statusIndicator = document.getElementById('chat-status');
    if (!statusIndicator) {
        statusIndicator = document.createElement('div');
        statusIndicator.id = 'chat-status';
        statusIndicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 8px 12px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 11px;
            font-weight: 600;
            z-index: 1000;
            border: 2px solid;
        `;
        document.body.appendChild(statusIndicator);
    }
    
    statusIndicator.textContent = statusText;
    statusIndicator.style.borderColor = statusColor;
    statusIndicator.style.color = statusColor;
}

// Request notification permission
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
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
    
    .notification-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    
    .notification-close:hover {
        background: rgba(255,255,255,0.3);
    }
    
    .chat-notification .notification-content p {
        margin: 5px 0 0 0;
        font-size: 13px;
        opacity: 0.9;
    }
`;
document.head.appendChild(style);
</script>
@endsection