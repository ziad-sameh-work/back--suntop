@extends('layouts.admin')

@section('title', 'محادثة Pusher #' . $chat->id)
@section('page-title', 'محادثة Pusher #' . $chat->id)

@push('styles')
<style>
/* Page Header Styles */
.page-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.page-title-wrapper .page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-title-wrapper .page-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: rgba(16, 185, 129, 0.2);
    color: #065f46;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-badge.closed {
    background: rgba(239, 68, 68, 0.2);
    color: #7f1d1d;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.page-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-secondary {
    background: var(--gray-600);
    color: white;
}

.btn-secondary:hover {
    background: var(--gray-700);
    transform: translateY(-1px);
}

.btn-warning {
    background: var(--warning);
    color: white;
}

.btn-warning:hover {
    background: #e08e05;
    transform: translateY(-1px);
}

/* Chat Layout */
.chat-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
    height: auto; /* ارتفاع تلقائي للتخطيط */
}

.chat-main-section {
    display: flex;
    flex-direction: column;
    height: auto; /* ارتفاع تلقائي */
}

.section-card {
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    transition: all 0.3s ease;
}

.section-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.card-header {
    background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-light) 100%);
    color: white;
    padding: 20px 25px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0; /* منع تقليص الهيدر */
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-badges {
    display: flex;
    gap: 10px;
}

.connection-badge,
.message-counter {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.connection-badge.connected {
    background: rgba(16, 185, 129, 0.9);
    color: white;
    border-color: rgba(16, 185, 129, 1);
}

.connection-badge.error {
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border-color: rgba(239, 68, 68, 1);
}

/* Messages - ارتفاع ثابت لعرض 6 رسائل فقط */
.messages-card {
    display: flex;
    flex-direction: column;
    height: 600px; /* ارتفاع ثابت للصندوق الكامل */
    max-height: 600px; /* منع الامتداد لأكثر من هذا */
}

.messages-body {
    display: flex;
    flex-direction: column;
    height: 100%; /* استخدم كامل ارتفاع الكارت */
}

.messages-container {
    height: 400px; /* ارتفاع ثابت لمنطقة الرسائل (حوالي 6 رسائل × 65px = 390px + padding) */
    padding: 20px;
    overflow-y: auto; /* تمرير عمودي داخلي فقط */
    overflow-x: hidden; /* منع التمرير الأفقي */
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    scroll-behavior: smooth; /* تمرير سلس */
    -webkit-overflow-scrolling: touch; /* تحسين التمرير على الجوال */
}

/* تخصيص شريط التمرير */
.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: var(--gray-200);
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar-thumb {
    background: var(--suntop-orange);
    border-radius: 10px;
    opacity: 0.7;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: var(--suntop-orange-dark);
    opacity: 1;
}

.message-wrapper {
    margin-bottom: 15px;
    display: flex;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.customer-message {
    justify-content: flex-start;
}

.admin-message {
    justify-content: flex-end;
}

.message-bubble {
    max-width: 70%;
    padding: 15px 18px;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.customer-message .message-bubble {
    background: var(--white);
    color: var(--gray-800);
    border-bottom-left-radius: 8px;
    border: 1px solid var(--gray-200);
}

.admin-message .message-bubble {
    background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%);
    color: white;
    border-bottom-right-radius: 8px;
}

.message-content {
    line-height: 1.5;
    word-wrap: break-word;
    margin-bottom: 8px;
}

.message-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
    opacity: 0.7;
    margin-top: 5px;
}

.sender-name {
    font-weight: 600;
}

/* Input Section */
.message-input-section {
    background: var(--white);
    border-top: 1px solid var(--gray-200);
    padding: 20px;
    height: auto; /* ارتفاع ثابت لمنطقة الإدخال */
    min-height: 120px; /* ارتفاع أدنى ثابت */
    max-height: 120px; /* ارتفاع أقصى ثابت */
}

.message-form {
    margin: 0;
}

.input-wrapper {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}

.message-textarea {
    flex: 1;
    border: 2px solid var(--gray-200);
    border-radius: 25px;
    padding: 15px 20px;
    font-size: 0.95rem;
    font-family: 'Cairo', sans-serif;
    resize: none;
    outline: none;
    transition: all 0.3s ease;
    background: var(--gray-50);
}

.message-textarea:focus {
    border-color: var(--suntop-orange);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.send-button {
    width: 50px;
    height: 50px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.send-button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.send-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.input-help {
    margin-top: 8px;
    font-size: 0.8rem;
    color: var(--gray-500);
}

.chat-closed-notice {
    padding: 30px;
    text-align: center;
    background: var(--gray-100);
    color: var(--gray-600);
    border-top: 1px solid var(--gray-200);
    height: 120px; /* ارتفاع ثابت مثل منطقة الإدخال */
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Typing Indicator */
.typing-indicator {
    padding: 15px 20px;
    background: var(--gray-100);
    border-top: 1px solid var(--gray-200);
    display: none;
    flex-shrink: 0; /* منع تقليص مؤشر الكتابة */
}

.typing-text {
    font-size: 0.85rem;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* زر الانتقال للأسفل */
.scroll-to-bottom-btn {
    position: absolute;
    bottom: 20px;
    right: 20px;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: var(--suntop-orange);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(255, 107, 53, 0.3);
    transition: all 0.3s ease;
    z-index: 100;
    opacity: 1;
    transform: scale(1);
}

.scroll-to-bottom-btn.hidden {
    opacity: 0;
    transform: scale(0.8);
    pointer-events: none;
}

.scroll-to-bottom-btn:hover {
    background: var(--suntop-orange-dark);
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
}

.scroll-to-bottom-btn i {
    font-size: 14px;
}

/* Sidebar */
.chat-sidebar-section {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.card-content {
    padding: 25px;
}

/* Customer Profile */
.customer-profile {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.customer-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.customer-details h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    color: var(--gray-800);
    font-weight: 600;
}

.customer-details p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--gray-600);
}

/* Stats */
.customer-stats,
.message-breakdown {
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 20px;
}

.stat-item,
.breakdown-item {
    text-align: center;
    flex: 1;
}

.stat-value,
.breakdown-value {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--suntop-orange);
    margin-bottom: 5px;
}

.stat-label,
.breakdown-label {
    font-size: 0.85rem;
    color: var(--gray-600);
    font-weight: 500;
}

.stat-divider {
    width: 1px;
    height: 40px;
    background: var(--gray-300);
}

.total-messages {
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 1px solid var(--gray-200);
}

.main-stat .stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--suntop-orange);
    margin-bottom: 8px;
}

.stat-description {
    font-size: 1rem;
    color: var(--gray-600);
    font-weight: 500;
}

.customer-count {
    color: var(--success);
}

.admin-count {
    color: var(--suntop-blue);
}

/* Timeline */
.chat-timeline {
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid var(--gray-200);
}

.timeline-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 0.9rem;
}

.timeline-label {
    color: var(--gray-600);
    font-weight: 600;
}

.timeline-value {
    color: var(--gray-800);
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .chat-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .page-header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .page-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .messages-card {
        height: 500px; /* ارتفاع ثابت أصغر للشاشات الصغيرة */
        max-height: 500px;
    }
    
    .messages-container {
        height: 320px; /* ارتفاع أصغر لمنطقة الرسائل */
    }
    
    .message-bubble {
        max-width: 85%;
    }
}

@media (max-width: 480px) {
    .page-header-section {
        padding: 20px;
    }
    
    .card-content {
        padding: 20px;
    }
    
    .customer-avatar {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .messages-card {
        height: 400px; /* ارتفاع أصغر للهواتف */
        max-height: 400px;
    }
    
    .messages-container {
        height: 250px; /* ارتفاع أصغر لمنطقة الرسائل على الهواتف */
    }
}
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="page-header-section">
        <div class="page-header-content">
            <div class="page-title-wrapper">
                <h1 class="page-title">
                    <i class="fas fa-comments"></i>
                    محادثة Pusher #{{ $chat->id }}
                </h1>
                <p class="page-subtitle">
                    محادثة مع {{ $chat->user->name }} - {{ $chat->user->email }}
                    <span class="status-badge {{ $chat->status === 'active' ? 'active' : 'closed' }}">
                        {{ $chat->status === 'active' ? 'نشط' : 'مغلق' }}
                    </span>
                </p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.pusher-chat.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
                @if($chat->status === 'active')
                <button type="button" class="btn btn-warning" onclick="closeChat()">
                    <i class="fas fa-times"></i>
                    إغلاق المحادثة
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-section">
        <div class="chat-layout">
            <div class="chat-main-section">
                <!-- Messages Card -->
                <div class="section-card messages-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comments"></i>
                            الرسائل
                        </h3>
                        <div class="header-badges">
                            <span class="connection-badge" id="connectionStatus">🔄 جاري الاتصال...</span>
                            <span class="message-counter" id="messageCount">{{ $chat->messages->count() }} رسالة</span>
                        </div>
                    </div>
                <div class="messages-body">
                    <!-- Messages Container -->
                    <div id="messagesContainer" class="messages-container" style="position: relative;">
                        @foreach($chat->messages as $message)
                        <div class="message-wrapper {{ $message->user_id == $chat->user_id ? 'customer-message' : 'admin-message' }}" 
                             id="message-{{ $message->id }}">
                            <div class="message-bubble">
                                <div class="message-content">{{ $message->message }}</div>
                                <div class="message-meta">
                                    <span class="sender-name">{{ $message->user->name }}</span>
                                    <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- زر الانتقال للأسفل -->
                        <button id="scrollToBottomBtn" class="scroll-to-bottom-btn hidden" onclick="scrollToBottom()">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    
                    <!-- Typing Indicator -->
                    <div id="typingIndicator" class="typing-indicator">
                        <div class="typing-text">
                            <i class="fas fa-ellipsis-h"></i>
                            <span id="typingUser">أحدهم</span> يكتب...
                        </div>
                    </div>

                    <!-- Message Input -->
                    @if($chat->status === 'active')
                    <div class="message-input-section">
                        <form id="messageForm" class="message-form">
                            <div class="input-wrapper">
                                <textarea id="messageInput" class="message-textarea" placeholder="اكتب ردك هنا..." 
                                        rows="2" maxlength="2000"></textarea>
                                <button type="submit" class="send-button" id="sendButton">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="input-help">اضغط Ctrl+Enter للإرسال</div>
                        </form>
                    </div>
                    @else
                    <div class="chat-closed-notice">
                        <span>هذه المحادثة مغلقة</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

            <div class="chat-sidebar-section">
                <!-- Customer Info -->
                <div class="section-card info-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i>
                            معلومات العميل
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="customer-profile">
                            <div class="customer-avatar">
                                {{ substr($chat->user->name, 0, 1) }}
                            </div>
                            <div class="customer-details">
                                <h4 class="customer-name">{{ $chat->user->name }}</h4>
                                <p class="customer-email">{{ $chat->user->email }}</p>
                            </div>
                        </div>
                        
                        <div class="customer-stats">
                            <div class="stat-item">
                                <div class="stat-value">{{ $chat->user->total_orders_count ?? 0 }}</div>
                                <div class="stat-label">الطلبات</div>
                            </div>
                            <div class="stat-divider"></div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $chat->user->category_name ?? 'برونزي' }}</div>
                                <div class="stat-label">الفئة</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Stats -->
                <div class="section-card stats-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i>
                            إحصائيات المحادثة
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="total-messages">
                            <div class="main-stat">
                                <div class="stat-number" id="totalMessagesCount">{{ $chat->messages->count() }}</div>
                                <div class="stat-description">إجمالي الرسائل</div>
                            </div>
                        </div>
                        
                        <div class="message-breakdown">
                            <div class="breakdown-item">
                                <div class="breakdown-value customer-count">{{ $chat->messages->where('user_id', $chat->user_id)->count() }}</div>
                                <div class="breakdown-label">رسائل العميل</div>
                            </div>
                            <div class="stat-divider"></div>
                            <div class="breakdown-item">
                                <div class="breakdown-value admin-count">{{ $chat->messages->where('user_id', '!=', $chat->user_id)->count() }}</div>
                                <div class="breakdown-label">رسائل الإدارة</div>
                            </div>
                        </div>
                        
                        <div class="chat-timeline">
                            <div class="timeline-item">
                                <span class="timeline-label">تاريخ الإنشاء:</span>
                                <span class="timeline-value">{{ $chat->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($chat->updated_at && $chat->updated_at != $chat->created_at)
                            <div class="timeline-item">
                                <span class="timeline-label">آخر نشاط:</span>
                                <span class="timeline-value">{{ $chat->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
let pusher = null;
let chatChannel = null;
const chatId = {{ $chat->id }};
let messageCount = {{ $chat->messages->count() }};

// Initialize Pusher
function initializePusher() {
    pusher = new Pusher('44911da009b5537ffae1', {
        cluster: 'eu',
        forceTLS: true,
        authEndpoint: '/admin/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }
    });

    // Subscribe to private chat channel
    chatChannel = pusher.subscribe(`private-chat.${chatId}`);
    
    chatChannel.bind('message.sent', function(data) {
        console.log('New message received:', data);
        addMessageToChat(data.message);
        updateMessageCount();
    });

    chatChannel.bind('pusher:subscription_succeeded', function() {
        console.log('Successfully subscribed to chat channel');
        document.getElementById('connectionStatus').innerHTML = '🟢 متصل';
        document.getElementById('connectionStatus').className = 'connection-badge connected';
    });

    chatChannel.bind('pusher:subscription_error', function(error) {
        console.error('Pusher subscription error:', error);
        document.getElementById('connectionStatus').innerHTML = '🔴 خطأ';
        document.getElementById('connectionStatus').className = 'connection-badge error';
    });
}

// Add message to chat
function addMessageToChat(message) {
    const messagesContainer = document.getElementById('messagesContainer');
    const isAdmin = message.user_id !== {{ $chat->user_id }};
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-wrapper ${isAdmin ? 'admin-message' : 'customer-message'}`;
    messageDiv.id = `message-${message.id}`;
    
    const messageTime = new Date(message.created_at).toLocaleTimeString('ar-EG', {
        hour: '2-digit',
        minute: '2-digit'
    });
    
    messageDiv.innerHTML = `
        <div class="message-bubble">
            <div class="message-content">${message.message}</div>
            <div class="message-meta">
                <span class="sender-name">${message.user.name}</span>
                <span class="message-time">${messageTime}</span>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    smartScroll(); // استخدم التمرير الذكي للرسائل الجديدة
    
    // Animate new message
    messageDiv.style.opacity = '0';
    messageDiv.style.transform = 'translateY(20px)';
    setTimeout(() => {
        messageDiv.style.transition = 'all 0.3s ease';
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translateY(0)';
    }, 50);
}

// Send message
function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch(`{{ route('admin.pusher-chat.reply', $chat) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            messageInput.focus();
        } else {
            alert('Error sending message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message');
    })
    .finally(() => {
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
    });
}

// Update message count
function updateMessageCount() {
    messageCount++;
    document.getElementById('messageCount').textContent = `${messageCount} رسالة`;
    document.getElementById('totalMessagesCount').textContent = messageCount;
}

// Scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

// Check if user is near bottom (for auto-scroll decision)
function isNearBottom() {
    const container = document.getElementById('messagesContainer');
    if (!container) return true;
    
    const threshold = 100; // بكسل من الأسفل
    return container.scrollTop + container.clientHeight >= container.scrollHeight - threshold;
}

// Smart scroll - only auto-scroll if user is near bottom
function smartScroll() {
    if (isNearBottom()) {
        scrollToBottom();
    }
}

// Close chat
function closeChat() {
    if (confirm('Are you sure you want to close this chat?')) {
        fetch(`{{ route('admin.pusher-chat.close', $chat) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error closing chat');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error closing chat');
        });
    }
}

// Toggle scroll to bottom button visibility
function toggleScrollButton() {
    const button = document.getElementById('scrollToBottomBtn');
    if (!button) return;
    
    if (isNearBottom()) {
        button.classList.add('hidden');
    } else {
        button.classList.remove('hidden');
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializePusher();
    scrollToBottom();
    
    // Handle message form
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('messagesContainer');
    
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
        
        // Handle Ctrl+Enter
        messageInput.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });
        
        messageInput.focus();
    }
    
    // Handle scroll events to show/hide scroll button
    if (messagesContainer) {
        messagesContainer.addEventListener('scroll', function() {
            toggleScrollButton();
        });
    }
    
    // Initial button state
    toggleScrollButton();
});
</script>
@endpush
@endsection
