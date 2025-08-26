@extends('layouts.admin')

@section('title', 'محادثة مع ' . $chat->customer->name)

@section('styles')
@livewireStyles
@endsection

@section('content')
<div class="dashboard-content">
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
/* Dashboard Content */
.dashboard-content {
    padding: 20px;
    background: #f8f9fa;
    min-height: 100vh;
}

/* Chat Layout */
.chat-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 20px;
    height: calc(100vh - 40px);
    max-width: 1600px;
    margin: 0 auto;
}

/* Sidebar */
.chat-sidebar {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    padding: 25px;
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
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.chat-header {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    padding: 25px 30px;
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
        padding: 20px;
    }
    
    .messages-container {
        padding: 15px;
    }
}
</style>
@endsection

@section('scripts')
@livewireScripts
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to bottom on page load
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
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
</script>
@endsection