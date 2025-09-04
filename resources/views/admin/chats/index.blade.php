@extends('layouts.admin')

@section('title', 'إدارة الدردشة والدعم')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="page-header-section">
        <div class="page-header-content">
            <div class="page-title-wrapper">
                <h1 class="page-title">
                    <i class="fas fa-comments"></i>
                    إدارة الدردشة والدعم
                </h1>
                <p class="page-subtitle">إدارة جميع محادثات العملاء ومتابعة طلبات الدعم</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-section">
        <div class="stats-container">
            <div class="stat-card primary" data-stat="total">
                <div class="stat-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['total']) }}</h3>
                    <p class="stat-label">إجمالي المحادثات</p>
                </div>
            </div>

            <div class="stat-card success" data-stat="open">
                <div class="stat-icon">
                    <i class="fas fa-envelope-open"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['open']) }}</h3>
                    <p class="stat-label">محادثات مفتوحة</p>
                </div>
            </div>

            <div class="stat-card warning" data-stat="in_progress">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['in_progress']) }}</h3>
                    <p class="stat-label">قيد المعالجة</p>
                </div>
            </div>

            <div class="stat-card info" data-stat="resolved">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['resolved']) }}</h3>
                    <p class="stat-label">محلولة</p>
                </div>
            </div>

            <div class="stat-card purple" data-stat="unassigned">
                <div class="stat-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['unassigned']) }}</h3>
                    <p class="stat-label">غير مُعيّنة</p>
                </div>
            </div>

            <div class="stat-card danger" data-stat="with_unread">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['with_unread']) }}</h3>
                    <p class="stat-label">رسائل غير مقروءة</p>
                </div>
            </div>

            <div class="stat-card orange" data-stat="high_priority">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['high_priority']) }}</h3>
                    <p class="stat-label">أولوية عالية</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="content-section">
        <div class="section-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i>
                    فلاتر البحث
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.chats.index') }}">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>جميع الحالات</option>
                                <option value="open" {{ $status == 'open' ? 'selected' : '' }}>مفتوحة</option>
                                <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="resolved" {{ $status == 'resolved' ? 'selected' : '' }}>محلولة</option>
                                <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>مغلقة</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">الأولوية</label>
                            <select name="priority" class="form-control" onchange="this.form.submit()">
                                <option value="all" {{ $priority == 'all' ? 'selected' : '' }}>جميع الأولويات</option>
                                <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>منخفضة</option>
                                <option value="medium" {{ $priority == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="urgent" {{ $priority == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">التعيين</label>
                            <select name="assigned" class="form-control" onchange="this.form.submit()">
                                <option value="all" {{ $assigned == 'all' ? 'selected' : '' }}>جميع المحادثات</option>
                                <option value="unassigned" {{ $assigned == 'unassigned' ? 'selected' : '' }}>غير مُعيّنة</option>
                                <option value="assigned" {{ $assigned == 'assigned' ? 'selected' : '' }}>مُعيّنة</option>
                                <option value="mine" {{ $assigned == 'mine' ? 'selected' : '' }}>محادثاتي</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">البحث</label>
                            <input type="text" name="search" value="{{ $search }}" placeholder="البحث في المحادثات..." class="form-control">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Chats List -->
    <div class="content-section">
        <div class="section-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    قائمة المحادثات ({{ $chats->total() }})
                </h3>
            </div>
            <div class="card-body p-0">
                @if($chats->count() > 0)
                    <div class="chats-list">
                        @foreach($chats as $chat)
                            @php
                                $chatRoute = (isset($chat->is_pusher_chat) && $chat->is_pusher_chat) 
                                    ? route('admin.pusher-chat.show', $chat->id)
                                    : route('admin.chats.show', $chat->id);
                            @endphp
                            <div class="chat-item" data-chat-id="{{ $chat->id }}" onclick="window.location.href='{{ $chatRoute }}'">
                                <div class="chat-avatar">
                                    {{ substr($chat->customer->name, 0, 1) }}
                                </div>

                                <div class="chat-content">
                                    <div class="chat-header">
                                        <h4 class="chat-customer">{{ $chat->customer->name }}</h4>
                                        <span class="chat-time">{{ $chat->formatted_last_message_time }}</span>
                                    </div>

                                    <div class="chat-subject">{{ $chat->subject ?: 'محادثة مع العميل' }}</div>

                                    @if($chat->latestMessage->first())
                                        <div class="chat-preview">
                                            <strong>{{ $chat->latestMessage->first()->sender_type == 'customer' ? $chat->latestMessage->first()->sender->name : 'الإدارة' }}:</strong>
                                            {{ Str::limit($chat->latestMessage->first()->message, 100) }}
                                        </div>
                                    @endif

                                    <div class="chat-meta">
                                        <span class="status-badge status-{{ $chat->status }}">
                                            @switch($chat->status)
                                                @case('open') مفتوحة @break
                                                @case('in_progress') قيد المعالجة @break
                                                @case('resolved') محلولة @break
                                                @case('closed') مغلقة @break
                                            @endswitch
                                        </span>

                                        <span class="priority-badge priority-{{ $chat->priority ?? 'medium' }}">
                                            @switch($chat->priority ?? 'medium')
                                                @case('low') منخفضة @break
                                                @case('medium') متوسطة @break
                                                @case('high') عالية @break
                                                @case('urgent') عاجلة @break
                                            @endswitch
                                        </span>

                                        @if(isset($chat->is_pusher_chat) && $chat->is_pusher_chat)
                                            <span class="type-badge type-pusher">🚀 Pusher</span>
                                        @else
                                            <span class="type-badge type-regular">📝 عادي</span>
                                        @endif

                                        @if($chat->assignedAdmin)
                                            <span class="assigned-admin">
                                                <i class="fas fa-user"></i>
                                                {{ $chat->assignedAdmin->name }}
                                            </span>
                                        @endif

                                        @if($chat->admin_unread_count > 0)
                                            <span class="unread-badge">{{ $chat->admin_unread_count }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="chat-actions" onclick="event.stopPropagation()">
                                    <a href="{{ $chatRoute }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                        عرض
                                    </a>

                                    @if(!isset($chat->is_pusher_chat) || !$chat->is_pusher_chat)
                                        @if(isset($chat->assigned_admin_id) && !$chat->assigned_admin_id)
                                            <form method="POST" action="{{ route('admin.chats.assign', $chat->id) }}" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="admin_id" value="{{ auth()->id() }}">
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-user-plus"></i>
                                                    تعيين لي
                                                </button>
                                            </form>
                                        @endif

                                        @if($chat->status !== 'resolved')
                                            <form method="POST" action="{{ route('admin.chats.updateStatus', $chat->id) }}" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="status" value="resolved">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                    حل
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($chats->hasPages())
                        <div class="pagination-wrapper">
                            {{ $chats->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h3>لا توجد محادثات</h3>
                        <p>لم يتم العثور على أي محادثات تطابق المعايير المحددة.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Content */
.dashboard-content {
    padding: 30px;
    background: #f8f9fa;
    min-height: 100vh;
}

/* Page Header */
.page-header-section {
    margin-bottom: 30px;
}

.page-header-content {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
}

.page-title-wrapper {
    text-align: center;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

/* Statistics Section */
.stats-section {
    margin-bottom: 30px;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 5px solid;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.stat-card.primary { border-left-color: #FF6B35; }
.stat-card.success { border-left-color: #28a745; }
.stat-card.warning { border-left-color: #ffc107; }
.stat-card.info { border-left-color: #17a2b8; }
.stat-card.purple { border-left-color: #6f42c1; }
.stat-card.danger { border-left-color: #dc3545; }
.stat-card.orange { border-left-color: #fd7e14; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-card.primary .stat-icon { background: #FF6B35; }
.stat-card.success .stat-icon { background: #28a745; }
.stat-card.warning .stat-icon { background: #ffc107; }
.stat-card.info .stat-icon { background: #17a2b8; }
.stat-card.purple .stat-icon { background: #6f42c1; }
.stat-card.danger .stat-icon { background: #dc3545; }
.stat-card.orange .stat-icon { background: #fd7e14; }

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.stat-label {
    color: #7f8c8d;
    font-weight: 500;
    margin: 0;
}

/* Content Section */
.content-section {
    margin-bottom: 30px;
}

.section-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    padding: 20px 25px;
}

.card-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-body {
    padding: 25px;
}

/* Filters */
.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control {
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #FF6B35;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}

/* Chats List */
.chats-list {
    max-height: 70vh;
    overflow-y: auto;
}

.chat-item {
    display: flex;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.3s ease;
    cursor: pointer;
}

.chat-item:hover {
    background-color: #f8f9fa;
}

.chat-item:last-child {
    border-bottom: none;
}

.chat-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    margin-left: 15px;
    flex-shrink: 0;
}

.chat-content {
    flex: 1;
    min-width: 0;
}

.chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.chat-customer {
    font-weight: 600;
    color: #2c3e50;
    font-size: 16px;
    margin: 0;
}

.chat-time {
    color: #7f8c8d;
    font-size: 12px;
}

.chat-subject {
    color: #34495e;
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 500;
}

.chat-preview {
    color: #7f8c8d;
    font-size: 13px;
    margin-bottom: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.chat-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-open { background: #fee2e2; color: #dc2626; }
.status-in_progress { background: #fef3c7; color: #d97706; }
.status-resolved { background: #d1fae5; color: #059669; }
.status-closed { background: #f3f4f6; color: #6b7280; }

.priority-badge {
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 10px;
    font-weight: 600;
}

.priority-low { background: #d1fae5; color: #059669; }
.priority-medium { background: #fef3c7; color: #d97706; }
.priority-high { background: #fed7aa; color: #ea580c; }
.priority-urgent { background: #fee2e2; color: #dc2626; }

/* Type Badges */
.type-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 8px;
}

.type-pusher {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: 1px solid #5a67d8;
}

.type-regular {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    border: 1px solid #4299e1;
}

.assigned-admin {
    color: #6366f1;
    font-size: 12px;
    font-weight: 500;
}

.unread-badge {
    background: #ef4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
}

.chat-actions {
    display: flex;
    gap: 5px;
    flex-shrink: 0;
}

.btn {
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 11px;
}

.btn-primary {
    background: #FF6B35;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: #2c3e50;
}

/* Pagination */
.pagination-wrapper {
    padding: 20px 25px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-content {
        padding: 15px;
    }
    
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .chat-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .chat-header {
        width: 100%;
    }
    
    .chat-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

@section('scripts')
<!-- Pusher JavaScript Library -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Pusher for Real-time Updates
    initializePusherRealtime();
    
    // Fallback: Auto-refresh page every 5 minutes as backup
    setInterval(function() {
        refreshChatStats();
    }, 300000);
});

let pusher = null;
let adminChannel = null;

function initializePusherRealtime() {
    try {
        // Initialize Pusher with your credentials
        pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true,
            authEndpoint: '/admin/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': 'Bearer {{ auth()->user()->createToken("chat-realtime")->plainTextToken }}'
                }
            }
        });

        // Subscribe to admin chats channel for real-time updates
        adminChannel = pusher.subscribe('private-admin.chats');
        
        // Listen for NewChatMessage events (the main event from ChatMessage model)
        adminChannel.bind('App\\Events\\NewChatMessage', function(data) {
            console.log('🔔 New chat message received:', data);
            handleNewChatMessage(data);
        });

        // Also listen to all individual chat channels for broader coverage
        pusher.bind_global(function(eventName, data) {
            console.log('Global Pusher event:', eventName, data);
            if (eventName === 'App\\Events\\NewChatMessage') {
                handleNewChatMessage(data);
            }
        });

        // Listen for chat status changes
        adminChannel.bind('chat.status.updated', function(data) {
            console.log('📊 Chat status updated:', data);
            updateChatStatus(data.chat_id, data.status);
        });

        // Connection status handling
        pusher.connection.bind('connected', function() {
            console.log('✅ Pusher connected successfully');
            showConnectionStatus('connected');
        });

        pusher.connection.bind('disconnected', function() {
            console.log('❌ Pusher disconnected');
            showConnectionStatus('disconnected');
        });

        pusher.connection.bind('error', function(error) {
            console.error('🔴 Pusher connection error:', error);
            showConnectionStatus('error');
        });

        adminChannel.bind('pusher:subscription_succeeded', function() {
            console.log('✅ Successfully subscribed to admin chats channel');
            showConnectionStatus('subscribed');
        });

        adminChannel.bind('pusher:subscription_error', function(error) {
            console.error('🔴 Admin channel subscription error:', error);
            showConnectionStatus('subscription_error');
        });

    } catch (error) {
        console.error('Failed to initialize Pusher:', error);
        showConnectionStatus('init_error');
    }
}

function handleNewChatMessage(data) {
    console.log('📨 Processing new chat message:', data);
    
    // Show notification with customer name
    const customerName = data.message.sender_type === 'customer' ? data.message.sender.name : 'الإدارة';
    showNotification(`رسالة جديدة من ${customerName}`, data.message.message);
    
    // Update the UI immediately without reload
    updateChatListRealtime(data);
    
    // Also emit Livewire events as backup
    if (typeof Livewire !== 'undefined') {
        console.log('🔄 Emitting Livewire events...');
        Livewire.emit('messageAdded', data.message.id);
        Livewire.emit('chatUpdated', data.message.chat_id);
    }
}

function updateChatListRealtime(data) {
    console.log('🔄 Updating chat list in real-time...');
    
    const chatId = data.message.chat_id;
    const chatItem = document.querySelector(`[data-chat-id="${chatId}"]`);
    
    if (chatItem) {
        console.log('✅ Found chat item, updating...');
        
        // Update last message time
        const timeElement = chatItem.querySelector('.chat-time');
        if (timeElement) {
            timeElement.textContent = 'الآن';
        }

        // Update last message preview
        const previewElement = chatItem.querySelector('.chat-preview');
        if (previewElement) {
            const senderName = data.message.sender_type === 'customer' ? data.message.sender.name : 'الإدارة';
            const messageText = data.message.message.length > 50 ? 
                data.message.message.substring(0, 50) + '...' : 
                data.message.message;
            previewElement.innerHTML = `<strong>${senderName}:</strong> ${messageText}`;
        }

        // Update unread count if message from customer
        if (data.message.sender_type === 'customer') {
            const unreadBadge = chatItem.querySelector('.unread-badge');
            if (unreadBadge) {
                const currentCount = parseInt(unreadBadge.textContent) || 0;
                unreadBadge.textContent = currentCount + 1;
                unreadBadge.style.display = 'inline-block';
            }
        }

        // Move chat to top of list
        const chatsList = chatItem.parentElement;
        if (chatsList) {
            chatsList.insertBefore(chatItem, chatsList.firstChild);
        }

        // Add highlight animation
        chatItem.style.backgroundColor = '#fff3cd';
        setTimeout(() => {
            chatItem.style.backgroundColor = '';
        }, 2000);
        
        console.log('✅ Chat item updated successfully');
    } else {
        console.log('⚠️ Chat item not found, may need page refresh for new chat');
    }
}

function updateChatItemRealtime(chatData) {
    const chatItem = document.querySelector(`[data-chat-id="${chatData.id}"]`);
    if (chatItem) {
        // Update last message time
        const timeElement = chatItem.querySelector('.chat-time');
        if (timeElement) {
            timeElement.textContent = 'الآن';
        }

        // Update last message preview
        const previewElement = chatItem.querySelector('.chat-preview');
        if (previewElement && chatData.last_message) {
            const senderName = chatData.last_message.sender_type === 'customer' ? (chatData.customer?.name || 'العميل') : 'الإدارة';
            previewElement.innerHTML = `<strong>${senderName}:</strong> ${chatData.last_message.message.substring(0, 100)}${chatData.last_message.message.length > 100 ? '...' : ''}`;
        }

        // Update customer name if needed
        const customerNameElement = chatItem.querySelector('.chat-customer');
        if (customerNameElement && chatData.customer?.name) {
            customerNameElement.textContent = chatData.customer.name;
        }

        // Move to top of list
        moveToTop(chatItem);
        
        // Add visual highlight
        highlightChatItem(chatItem);
    }
}

function updateChatItemUnreadCount(chatItem, unreadCount) {
    const unreadBadge = chatItem.querySelector('.unread-badge');
    
    if (unreadCount > 0) {
        if (unreadBadge) {
            unreadBadge.textContent = unreadCount;
        } else {
            // Create new unread badge
            const chatMeta = chatItem.querySelector('.chat-meta');
            const newBadge = document.createElement('span');
            newBadge.className = 'unread-badge';
            newBadge.textContent = unreadCount;
            chatMeta.appendChild(newBadge);
        }
    } else {
        if (unreadBadge) {
            unreadBadge.remove();
        }
    }
}

function moveToTop(chatItem) {
    const chatsList = document.querySelector('.chats-list');
    if (chatsList && chatItem.parentNode === chatsList) {
        chatsList.insertBefore(chatItem, chatsList.firstChild);
    }
}

function highlightChatItem(chatItem) {
    chatItem.style.background = '#fff3cd';
    chatItem.style.borderLeft = '4px solid #ffc107';
    
    setTimeout(() => {
        chatItem.style.background = '';
        chatItem.style.borderLeft = '';
    }, 3000);
}

function updateUnreadStats() {
    // Refresh unread messages count
    fetch('{{ route("admin.chats.index") }}?ajax=1&stats_only=1')
        .then(response => response.json())
        .then(data => {
            if (data.stats) {
                updateStatsDisplay(data.stats);
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}

function updateStatsDisplay(stats) {
    // Update stats cards
    const statsElements = {
        'total': document.querySelector('[data-stat="total"] .stat-number'),
        'open': document.querySelector('[data-stat="open"] .stat-number'),
        'in_progress': document.querySelector('[data-stat="in_progress"] .stat-number'),
        'resolved': document.querySelector('[data-stat="resolved"] .stat-number'),
        'unassigned': document.querySelector('[data-stat="unassigned"] .stat-number'),
        'with_unread': document.querySelector('[data-stat="with_unread"] .stat-number'),
        'high_priority': document.querySelector('[data-stat="high_priority"] .stat-number')
    };

    Object.keys(statsElements).forEach(key => {
        const element = statsElements[key];
        if (element && stats[key] !== undefined) {
            element.textContent = new Intl.NumberFormat('ar-EG').format(stats[key]);
        }
    });
}

function updateChatStatus(chatId, newStatus) {
    const chatItem = document.querySelector(`[data-chat-id="${chatId}"]`);
    if (chatItem) {
        const statusBadge = chatItem.querySelector('.status-badge');
        if (statusBadge) {
            // Update status badge
            statusBadge.className = `status-badge status-${newStatus}`;
            
            const statusTexts = {
                'open': 'مفتوحة',
                'in_progress': 'قيد المعالجة', 
                'resolved': 'محلولة',
                'closed': 'مغلقة'
            };
            
            statusBadge.textContent = statusTexts[newStatus] || newStatus;
        }
    }
}

function showNotification(title, message) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: message.substring(0, 100),
            icon: '/favicon.ico',
            badge: '/favicon.ico'
        });
    } else if ('Notification' in window && Notification.permission !== 'denied') {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                showNotification(title, message);
            }
        });
    }
    
    // Also show in-page notification
    showInPageNotification(title, message);
}

function showInPageNotification(title, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'realtime-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <strong>${title}</strong>
            <p>${message.substring(0, 80)}...</p>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 15px;
        max-width: 400px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
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
            statusText = '🟢 Real-time نشط';
            statusColor = '#28a745';
            break;
        case 'disconnected':
            statusText = '🟡 منقطع';
            statusColor = '#ffc107';
            break;
        case 'error':
        case 'subscription_error':
        case 'init_error':
            statusText = '🔴 خطأ في الاتصال';
            statusColor = '#dc3545';
            break;
    }
    
    // Create or update status indicator
    let statusIndicator = document.getElementById('pusher-status');
    if (!statusIndicator) {
        statusIndicator = document.createElement('div');
        statusIndicator.id = 'pusher-status';
        statusIndicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: white;
            padding: 10px 15px;
            border-radius: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 12px;
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

function refreshChatStats() {
    // Backup refresh function
    console.log('📊 Refreshing chat stats (backup)');
    updateUnreadStats();
}

// Request notification permission on page load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Add styles for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
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
        width: 25px;
        height: 25px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .notification-close:hover {
        background: rgba(255,255,255,0.3);
    }
    
    .realtime-notification .notification-content p {
        margin: 5px 0 0 0;
        font-size: 13px;
        opacity: 0.9;
    }
`;
document.head.appendChild(style);
</script>
@endsection
@endsection