<div class="chat-updates-indicator">
    <div class="updates-info">
        <span class="update-status {{ $isPolling ? 'active' : 'paused' }}">
            <i class="fas {{ $isPolling ? 'fa-sync fa-spin' : 'fa-pause' }}"></i>
            {{ $isPolling ? 'متصل' : 'متوقف' }}
        </span>
        <span class="update-time">
            آخر تحديث: {{ $lastUpdate }}
        </span>
    </div>
    <button type="button" class="toggle-polling-btn" wire:click="togglePolling">
        {{ $isPolling ? 'إيقاف التحديث التلقائي' : 'تشغيل التحديث التلقائي' }}
    </button>
</div>

<style>
    .chat-updates-indicator {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 10px;
        border: 1px solid #dee2e6;
    }
    
    .updates-info {
        display: flex;
        align-items: center;
    }
    
    .update-status {
        margin-right: 15px;
        font-size: 14px;
        font-weight: bold;
    }
    
    .update-status.active {
        color: #28a745;
    }
    
    .update-status.paused {
        color: #6c757d;
    }
    
    .update-status i {
        margin-right: 5px;
    }
    
    .update-time {
        font-size: 13px;
        color: #6c757d;
    }
    
    .toggle-polling-btn {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .toggle-polling-btn:hover {
        background-color: #e9ecef;
    }
</style>
