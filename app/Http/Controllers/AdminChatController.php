<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\PusherChat;
use App\Models\PusherMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminChatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display chat list for admin
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');
        $assigned = $request->get('assigned', 'all');
        $search = $request->get('search');

        // Check if chats table exists
        $chatTableExists = \Schema::hasTable('chats');

        // Get statistics
        $stats = $this->getChatStats();

        // Handle AJAX request for stats only
        if ($request->ajax() && $request->get('stats_only')) {
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        }

        // Get regular chats (only if table exists)
        $regularChats = $chatTableExists ? Chat::with(['customer', 'assignedAdmin', 'latestMessage.sender'])
            ->withCount('messages') : collect();

        // Get pusher chats
        $pusherChats = PusherChat::with(['customer', 'messages.user'])
            ->withCount('messages');

        // Apply filters to regular chats (only if table exists)
        if ($chatTableExists && $status !== 'all') {
            $regularChats->where('status', $status);
        }

        if ($chatTableExists && $priority !== 'all') {
            $regularChats->where('priority', $priority);
        }

        if ($chatTableExists && $assigned === 'unassigned') {
            $regularChats->whereNull('assigned_admin_id');
        } elseif ($chatTableExists && $assigned === 'assigned') {
            $regularChats->whereNotNull('assigned_admin_id');
        } elseif ($chatTableExists && $assigned === 'mine') {
            $regularChats->where('assigned_admin_id', Auth::id());
        }

        if ($chatTableExists && $search) {
            $regularChats->where(function($q) use ($search) {
                $q->where('subject', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($qq) use ($search) {
                      $qq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });

            // Apply same search to pusher chats
            $pusherChats->where(function($q) use ($search) {
                $q->where('subject', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($qq) use ($search) {
                      $qq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Apply status filter to pusher chats (map status values)
        if ($status !== 'all') {
            $pusherChats->where('status', $status);
        }

        // Execute queries
        $regularChatsResults = $chatTableExists ? $regularChats->orderBy('admin_unread_count', 'desc')
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        $pusherChatsResults = $pusherChats->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Transform pusher chats to match regular chat structure
        $transformedPusherChats = $pusherChatsResults->map(function ($pusherChat) {
            // Get the latest message
            $latestMessage = $pusherChat->messages->sortByDesc('created_at')->first();
            
            // Create a pseudo Chat model that can work with routes
            $pseudoChat = new \stdClass();
            $pseudoChat->id = $pusherChat->id;
            $pseudoChat->customer_id = $pusherChat->user_id;
            $pseudoChat->subject = $pusherChat->subject ?: 'محادثة Pusher';
            $pseudoChat->status = $pusherChat->status;
            $pseudoChat->priority = 'medium'; // Default priority for pusher chats
            $pseudoChat->admin_unread_count = 1; // Mark as unread for visibility
            $pseudoChat->customer_unread_count = 0;
            $pseudoChat->last_message_at = $latestMessage ? $latestMessage->created_at : $pusherChat->created_at;
            $pseudoChat->created_at = $pusherChat->created_at;
            $pseudoChat->updated_at = $pusherChat->updated_at;
            $pseudoChat->customer = $pusherChat->customer;
            $pseudoChat->assignedAdmin = null;
            $pseudoChat->messages_count = $pusherChat->messages_count;
            $pseudoChat->latestMessage = $latestMessage ? collect([
                (object) [
                    'id' => $latestMessage->id,
                    'message' => $latestMessage->message,
                    'sender_type' => $latestMessage->user_id == $pusherChat->user_id ? 'customer' : 'admin',
                    'created_at' => $latestMessage->created_at,
                    'sender' => $latestMessage->user
                ]
            ]) : collect([]);
            $pseudoChat->is_pusher_chat = true; // Flag to identify pusher chats
            $pseudoChat->formatted_last_message_time = $this->formatMessageTime($pseudoChat->last_message_at);
            
            return $pseudoChat;
        });

        // Combine and sort all chats
        $allChats = $regularChatsResults->concat($transformedPusherChats)
            ->sortByDesc(function ($chat) {
                return $chat->last_message_at ?: $chat->created_at;
            });

        // Manually paginate the combined results
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $chatsForPage = $allChats->slice($offset, $perPage);
        
        $chats = new \Illuminate\Pagination\LengthAwarePaginator(
            $chatsForPage,
            $allChats->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.chats.index', compact('chats', 'stats', 'status', 'priority', 'assigned', 'search'));
    }

    /**
     * Show specific chat
     */
    public function show(Chat $chat)
    {
        $chat->load(['customer', 'assignedAdmin', 'messages.sender']);
        
        // Mark messages as read for admin
        $chat->markAsRead('admin');

        return view('admin.chats.show', compact('chat'));
    }

    /**
     * Assign chat to admin
     */
    public function assign(Request $request, Chat $chat)
    {
        $request->validate([
            'admin_id' => 'nullable|exists:users,id'
        ]);

        $adminId = $request->admin_id;
        
        // Verify the admin user exists and has admin role
        if ($adminId) {
            $admin = User::where('id', $adminId)->where('role', 'admin')->first();
            if (!$admin) {
                return back()->with('error', 'المستخدم المحدد ليس مدير صالح');
            }
        }

        $chat->update([
            'assigned_admin_id' => $adminId,
            'status' => $adminId ? 'in_progress' : 'open'
        ]);

        $message = $adminId ? 'تم تعيين المحادثة بنجاح' : 'تم إلغاء تعيين المحادثة';
        
        return back()->with('success', $message);
    }

    /**
     * Update chat status
     */
    public function updateStatus(Request $request, Chat $chat)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $chat->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة المحادثة بنجاح');
    }

    /**
     * Update chat priority
     */
    public function updatePriority(Request $request, Chat $chat)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $chat->update(['priority' => $request->priority]);

        return back()->with('success', 'تم تحديث أولوية المحادثة بنجاح');
    }

    /**
     * Get chat statistics (including pusher chats)
     */
    private function getChatStats()
    {
        // Check if chats table exists
        $chatTableExists = \Schema::hasTable('chats');
        
        // Regular chat stats
        $regularStats = [
            'total' => $chatTableExists ? Chat::count() : 0,
            'open' => $chatTableExists ? Chat::where('status', 'open')->count() : 0,
            'in_progress' => $chatTableExists ? Chat::where('status', 'in_progress')->count() : 0,
            'resolved' => $chatTableExists ? Chat::where('status', 'resolved')->count() : 0,
            'closed' => $chatTableExists ? Chat::where('status', 'closed')->count() : 0,
            'unassigned' => $chatTableExists ? Chat::whereNull('assigned_admin_id')->count() : 0,
            'with_unread' => $chatTableExists ? Chat::where('admin_unread_count', '>', 0)->count() : 0,
            'high_priority' => $chatTableExists ? Chat::whereIn('priority', ['high', 'urgent'])->count() : 0,
        ];

        // Pusher chat stats
        $pusherStats = [
            'total' => PusherChat::count(),
            'open' => PusherChat::where('status', 'open')->count(),
            'in_progress' => PusherChat::where('status', 'in_progress')->count(),
            'resolved' => PusherChat::where('status', 'resolved')->count(),
            'closed' => PusherChat::where('status', 'closed')->count(),
        ];

        // Combined stats
        return [
            'total' => $regularStats['total'] + $pusherStats['total'],
            'open' => $regularStats['open'] + $pusherStats['open'],
            'in_progress' => $regularStats['in_progress'] + $pusherStats['in_progress'],
            'resolved' => $regularStats['resolved'] + $pusherStats['resolved'],
            'closed' => $regularStats['closed'] + $pusherStats['closed'],
            'unassigned' => $regularStats['unassigned'], // Pusher chats don't have assigned admin
            'with_unread' => $regularStats['with_unread'] + $pusherStats['total'], // All pusher chats considered unread
            'high_priority' => $regularStats['high_priority'],
            'pusher_chats' => $pusherStats['total'], // Additional stat for pusher chats
            'regular_chats' => $regularStats['total'], // Additional stat for regular chats
        ];
    }

    /**
     * Get admin users for assignment
     */
    public function getAdmins()
    {
        return User::where('role', 'admin')
            ->select('id', 'name', 'full_name', 'email')
            ->get();
    }

    /**
     * Format message time for display
     */
    private function formatMessageTime($timestamp)
    {
        if (!$timestamp) {
            return 'منذ قليل';
        }

        $carbon = \Carbon\Carbon::parse($timestamp);
        $diffInMinutes = $carbon->diffInMinutes(now());
        
        if ($diffInMinutes < 1) {
            return 'الآن';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' دقيقة';
        } elseif ($diffInMinutes < 1440) {
            return $carbon->format('H:i');
        } else {
            return $carbon->format('M d');
        }
    }
}
