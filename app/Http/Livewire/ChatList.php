<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ChatList extends Component
{
    use WithPagination;

    public $status = 'all';
    public $priority = 'all';
    public $assigned = 'all';
    public $search = '';
    public $selectedChat = null;
    public $refreshInterval = 5000; // 5 seconds

    protected $listeners = [
        'chatUpdated' => 'refreshList',
        'messageAdded' => 'refreshList'
    ];

    protected $queryString = [
        'status' => ['except' => 'all'],
        'priority' => ['except' => 'all'],
        'assigned' => ['except' => 'all'],
        'search' => ['except' => '']
    ];

    public function mount()
    {
        // Auto-refresh every 5 seconds
        $this->dispatchBrowserEvent('startAutoRefresh', ['interval' => $this->refreshInterval]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPriority()
    {
        $this->resetPage();
    }

    public function updatingAssigned()
    {
        $this->resetPage();
    }

    public function selectChat($chatId)
    {
        $this->selectedChat = $chatId;
        $this->emit('chatSelected', $chatId);
    }

    public function refreshList()
    {
        // This will trigger a re-render
        $this->render();
    }

    public function assignToMe($chatId)
    {
        $chat = Chat::find($chatId);
        if ($chat) {
            $chat->update([
                'assigned_admin_id' => Auth::id(),
                'status' => 'in_progress'
            ]);
            
            $this->dispatchBrowserEvent('showNotification', [
                'type' => 'success',
                'message' => 'تم تعيين المحادثة لك بنجاح'
            ]);
        }
    }

    public function markAsResolved($chatId)
    {
        $chat = Chat::find($chatId);
        if ($chat && ($chat->assigned_admin_id === Auth::id() || Auth::user()->role === 'admin')) {
            $chat->update(['status' => 'resolved']);
            
            $this->dispatchBrowserEvent('showNotification', [
                'type' => 'success',
                'message' => 'تم وضع علامة على المحادثة كمحلولة'
            ]);
        }
    }

    public function getChatsProperty()
    {
        $query = Chat::with(['customer', 'assignedAdmin', 'latestMessage.sender'])
            ->withCount('messages');

        // Apply filters
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->priority !== 'all') {
            $query->where('priority', $this->priority);
        }

        if ($this->assigned === 'unassigned') {
            $query->whereNull('assigned_admin_id');
        } elseif ($this->assigned === 'assigned') {
            $query->whereNotNull('assigned_admin_id');
        } elseif ($this->assigned === 'mine') {
            $query->where('assigned_admin_id', Auth::id());
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('subject', 'LIKE', "%{$this->search}%")
                  ->orWhereHas('customer', function($qq) {
                      $qq->where('name', 'LIKE', "%{$this->search}%")
                         ->orWhere('email', 'LIKE', "%{$this->search}%");
                  });
            });
        }

        return $query->orderBy('admin_unread_count', 'desc')
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getStatsProperty()
    {
        return [
            'total' => Chat::count(),
            'open' => Chat::where('status', 'open')->count(),
            'in_progress' => Chat::where('status', 'in_progress')->count(),
            'resolved' => Chat::where('status', 'resolved')->count(),
            'unassigned' => Chat::whereNull('assigned_admin_id')->count(),
            'with_unread' => Chat::where('admin_unread_count', '>', 0)->count(),
            'high_priority' => Chat::whereIn('priority', ['high', 'urgent'])->count(),
            'my_chats' => Chat::where('assigned_admin_id', Auth::id())->count(),
        ];
    }

    public function render()
    {
        return view('livewire.chat-list', [
            'chats' => $this->chats,
            'stats' => $this->stats,
            'admins' => User::where('role', 'admin')->get(['id', 'name', 'full_name'])
        ]);
    }
}
