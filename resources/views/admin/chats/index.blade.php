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
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['total']) }}</h3>
                    <p class="stat-label">إجمالي المحادثات</p>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-envelope-open"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['open']) }}</h3>
                    <p class="stat-label">محادثات مفتوحة</p>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['in_progress']) }}</h3>
                    <p class="stat-label">قيد المعالجة</p>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['resolved']) }}</h3>
                    <p class="stat-label">محلولة</p>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['unassigned']) }}</h3>
                    <p class="stat-label">غير مُعيّنة</p>
                </div>
            </div>

            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['with_unread']) }}</h3>
                    <p class="stat-label">رسائل غير مقروءة</p>
                </div>
            </div>

            <div class="stat-card orange">
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
                            <div class="chat-item" onclick="window.location.href='{{ route('admin.chats.show', $chat) }}'">
                                <div class="chat-avatar">
                                    {{ substr($chat->customer->name, 0, 1) }}
                                </div>

                                <div class="chat-content">
                                    <div class="chat-header">
                                        <h4 class="chat-customer">{{ $chat->customer->name }}</h4>
                                        <span class="chat-time">{{ $chat->formatted_last_message_time }}</span>
                                    </div>

                                    <div class="chat-subject">{{ $chat->subject }}</div>

                                    @if($chat->latestMessage->first())
                                        <div class="chat-preview">
                                            <strong>{{ $chat->latestMessage->first()->sender_type == 'customer' ? 'العميل' : 'الإدارة' }}:</strong>
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

                                        <span class="priority-badge priority-{{ $chat->priority }}">
                                            @switch($chat->priority)
                                                @case('low') منخفضة @break
                                                @case('medium') متوسطة @break
                                                @case('high') عالية @break
                                                @case('urgent') عاجلة @break
                                            @endswitch
                                        </span>

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
                                    <a href="{{ route('admin.chats.show', $chat) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                        عرض
                                    </a>

                                    @if(!$chat->assigned_admin_id)
                                        <form method="POST" action="{{ route('admin.chats.assign', $chat) }}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="admin_id" value="{{ auth()->id() }}">
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="fas fa-user-plus"></i>
                                                تعيين لي
                                            </button>
                                        </form>
                                    @endif

                                    @if($chat->status !== 'resolved')
                                        <form method="POST" action="{{ route('admin.chats.updateStatus', $chat) }}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                                حل
                                            </button>
                                        </form>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh page every 30 seconds
    setInterval(function() {
        window.location.reload();
    }, 30000);
});
</script>
@endsection
@endsection