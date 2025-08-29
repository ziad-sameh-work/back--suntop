@extends('layouts.admin')

@section('title', 'Chat Management - Real-time with Pusher')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">💬 Real-time Chat Management</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="refreshStats()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button type="button" class="btn btn-info" onclick="toggleAutoRefresh()">
                <i class="fas fa-play" id="autoRefreshIcon"></i> Auto Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Chats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeChats">{{ $stats['active_chats'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Chats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalChats">{{ $stats['total_chats'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMessages">{{ $stats['total_messages'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unread Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="unreadMessages">{{ $stats['unread_messages'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Chats -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Recent Active Chats</h6>
            <span class="badge badge-success" id="onlineStatus">🟢 Live Updates Active</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="chatsTable">
                    <thead>
                        <tr>
                            <th>Chat ID</th>
                            <th>Customer</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Unread</th>
                            <th>Last Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="chatsTableBody">
                        @foreach($recentChats as $chat)
                        <tr id="chat-row-{{ $chat->id }}">
                            <td>#{{ $chat->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $chat->user->name }}</div>
                                        <div class="small text-gray-500">{{ $chat->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $chat->title }}</td>
                            <td>
                                <span class="badge badge-{{ $chat->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($chat->status) }}
                                </span>
                            </td>
                            <td>
                                @if($chat->unread_admin_count > 0)
                                    <span class="badge badge-danger">{{ $chat->unread_admin_count }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($chat->last_message_at)
                                    <span title="{{ $chat->last_message_at->format('d/m/Y H:i:s') }}">
                                        {{ $chat->last_message_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-muted">No messages</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.pusher-chat.show', $chat) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($chat->status === 'active')
                                <button class="btn btn-warning btn-sm" onclick="closeChat({{ $chat->id }})">
                                    <i class="fas fa-times"></i> Close
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
let autoRefreshInterval = null;
let pusher = null;

// Initialize Pusher
function initializePusher() {
    pusher = new Pusher('44911da009b5537ffae1', {
        cluster: 'eu',
        forceTLS: true,
        auth: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Authorization': 'Bearer {{ auth()->user()->createToken("pusher")->plainTextToken }}'
            }
        }
    });

    // Subscribe to admin chats channel
    const adminChannel = pusher.subscribe('private-admin.chats');
    
    adminChannel.bind('message.sent', function(data) {
        console.log('New message received:', data);
        handleNewMessage(data);
        refreshStats();
    });

    adminChannel.bind('pusher:subscription_succeeded', function() {
        console.log('Successfully subscribed to admin.chats channel');
        document.getElementById('onlineStatus').innerHTML = '🟢 Live Updates Active';
    });

    adminChannel.bind('pusher:subscription_error', function(error) {
        console.error('Pusher subscription error:', error);
        document.getElementById('onlineStatus').innerHTML = '🔴 Connection Error';
    });
}

// Handle new message
function handleNewMessage(data) {
    const chatRow = document.getElementById(`chat-row-${data.chat.id}`);
    if (chatRow) {
        // Update unread count
        const unreadCell = chatRow.cells[4];
        unreadCell.innerHTML = `<span class="badge badge-danger">${data.chat.unread_admin_count}</span>`;
        
        // Update last message time
        const lastMessageCell = chatRow.cells[5];
        lastMessageCell.innerHTML = `<span title="${new Date().toLocaleString()}">Just now</span>`;
        
        // Highlight row briefly
        chatRow.classList.add('table-warning');
        setTimeout(() => {
            chatRow.classList.remove('table-warning');
        }, 3000);
    }
}

// Refresh stats
function refreshStats() {
    fetch('{{ route("admin.pusher-chat.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('activeChats').textContent = data.active_chats;
            document.getElementById('totalChats').textContent = data.total_chats;
            document.getElementById('totalMessages').textContent = data.total_messages;
            document.getElementById('unreadMessages').textContent = data.unread_messages;
        })
        .catch(error => console.error('Error refreshing stats:', error));
}

// Toggle auto refresh
function toggleAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
        document.getElementById('autoRefreshIcon').className = 'fas fa-play';
    } else {
        autoRefreshInterval = setInterval(refreshStats, 30000); // Refresh every 30 seconds
        document.getElementById('autoRefreshIcon').className = 'fas fa-pause';
    }
}

// Close chat
function closeChat(chatId) {
    if (confirm('Are you sure you want to close this chat?')) {
        fetch(`/admin/pusher-chat/${chatId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
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

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializePusher();
});
</script>
@endpush
@endsection
