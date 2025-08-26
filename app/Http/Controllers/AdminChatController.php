<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
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

        $query = Chat::with(['customer', 'assignedAdmin', 'latestMessage.sender'])
            ->withCount('messages');

        // Apply filters
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority !== 'all') {
            $query->where('priority', $priority);
        }

        if ($assigned === 'unassigned') {
            $query->whereNull('assigned_admin_id');
        } elseif ($assigned === 'assigned') {
            $query->whereNotNull('assigned_admin_id');
        } elseif ($assigned === 'mine') {
            $query->where('assigned_admin_id', Auth::id());
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('subject', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($qq) use ($search) {
                      $qq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $chats = $query->orderBy('admin_unread_count', 'desc')
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get statistics
        $stats = $this->getChatStats();

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
     * Get chat statistics
     */
    private function getChatStats()
    {
        return [
            'total' => Chat::count(),
            'open' => Chat::where('status', 'open')->count(),
            'in_progress' => Chat::where('status', 'in_progress')->count(),
            'resolved' => Chat::where('status', 'resolved')->count(),
            'closed' => Chat::where('status', 'closed')->count(),
            'unassigned' => Chat::whereNull('assigned_admin_id')->count(),
            'with_unread' => Chat::where('admin_unread_count', '>', 0)->count(),
            'high_priority' => Chat::whereIn('priority', ['high', 'urgent'])->count(),
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
}
