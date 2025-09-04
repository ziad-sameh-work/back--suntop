<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChatInterface extends Component
{
    use WithFileUploads;

    public $chat;
    public $messages = [];
    public $newMessage = '';
    public $attachment;
    public $showEmojiPicker = false;
    public $isTyping = false;
    public $typingIndicator = '';
    
    
    protected $listeners = [
        'chatUpdated' => 'refreshMessages',
        'messageAdded' => 'addMessage',
        'stopPolling' => 'disablePolling',
        'resumePolling' => 'enablePolling'
    ];

    // تحديث تلقائي كل 3 ثواني
    public function getPollingInterval()
    {
        return $this->pollingEnabled ? 3000 : null; // بالمللي ثانية
    }
    
    public $pollingEnabled = true;
    
    public function disablePolling()
    {
        $this->pollingEnabled = false;
    }
    
    public function enablePolling()
    {
        $this->pollingEnabled = true;
    }

    public function mount(Chat $chat)
    {
        $this->chat = $chat;
        $this->loadMessages();
        
        // Mark messages as read for admin
        if (Auth::user()->role === 'admin') {
            $this->chat->markAsRead('admin');
        }
    }

    public function loadMessages()
    {
        $this->messages = $this->chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
            
        // Debug: Log message count
        \Log::info('ChatInterface: Loaded ' . count($this->messages) . ' messages for chat ' . $this->chat->id);
    }

    public function sendMessage()
    {
        // Debug: Log that method was called
        \Log::info('ChatInterface: sendMessage called with message: ' . $this->newMessage);
        
        // Check if message is empty and no attachment
        if ((!$this->newMessage || trim($this->newMessage) === '') && !$this->attachment) {
            \Log::info('ChatInterface: Message empty, returning');
            return; // Do nothing if both message and attachment are empty
        }
        
        try {
            $this->validate([
                'newMessage' => 'nullable|string|max:2000',
                'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt'
            ]);
        } catch (\Exception $e) {
            \Log::error('ChatInterface: Validation failed: ' . $e->getMessage());
            return;
        }

        $user = Auth::user();
        $senderType = $user->role === 'admin' ? 'admin' : 'customer';

        // Handle file attachment if present
        $attachmentPath = null;
        $attachmentName = null;
        $messageType = 'text';

        if ($this->attachment) {
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentPath = $this->attachment->store('chat-attachments/' . $this->chat->id, 'public');
            
            if (in_array($this->attachment->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                $messageType = 'image';
            } else {
                $messageType = 'file';
            }
        }

        $message = DB::transaction(function () use ($user, $senderType, $messageType, $attachmentPath, $attachmentName) {
            $message = ChatMessage::create([
                'chat_id' => $this->chat->id,
                'sender_id' => $user->id,
                'sender_type' => $senderType,
                'message' => $this->newMessage,
                'message_type' => $messageType,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'metadata' => [
                    'sent_from' => 'admin_panel_livewire' // Changed to trigger real-time events
                ]
            ]);
            
            // سيتم بث الرسالة تلقائياً عبر Pusher من خلال ChatMessage model
            
            return $message;
        });
        
        // Debug: Log message creation
        \Log::info('ChatInterface: Created message ' . $message->id . ' for chat ' . $this->chat->id);

        // Update chat status if needed
        if (in_array($this->chat->status, ['resolved', 'closed'])) {
            $this->chat->update(['status' => 'in_progress']);
        }

        // Clear the form
        $this->newMessage = '';
        $this->attachment = null;

        // Refresh messages immediately
        $this->loadMessages();

        // Force Livewire to re-render the component
        $this->emit('refreshMessages');
        $this->emit('messageAdded', $message->id);
        $this->emit('chatUpdated', $this->chat->id);

        // Scroll to bottom
        $this->dispatchBrowserEvent('scrollToBottom');
        
        // Force browser to refresh the messages container
        $this->dispatchBrowserEvent('forceRefreshMessages', [
            'messageId' => $message->id,
            'chatId' => $this->chat->id
        ]);
        
        // Debug: Log success
        \Log::info('ChatInterface: Message sent successfully, form cleared');
    }

    public function refreshMessages()
    {
        $this->loadMessages();
        $this->dispatchBrowserEvent('scrollToBottom');
    }

    public function addMessage($messageId)
    {
        // This method is called when a new message is added from API or other sources
        $this->loadMessages();
        $this->dispatchBrowserEvent('scrollToBottom');
    }
    
    // يستخدم هذا التابع تلقائياً مع polling
    public function render()
    {
        // فحص ما إذا كان هناك رسائل جديدة
        $latestMessageId = 0;
        if (count($this->messages) > 0) {
            $latestMessageId = collect($this->messages)->max('id');
        }
        
        // التحقق من وجود رسائل جديدة
        $hasNewMessages = false;
        if ($latestMessageId > 0) {
            $hasNewMessages = $this->chat->messages()
                ->where('id', '>', $latestMessageId)
                ->exists();
        }
        
        // إذا كانت هناك رسائل جديدة، قم بتحميلها
        if ($hasNewMessages) {
            $this->loadMessages();
            $this->dispatchBrowserEvent('scrollToBottom');
            
            // وضع علامة "مقروء" على الرسائل إذا كان المستخدم مدير
            if (Auth::user()->role === 'admin') {
                $this->chat->markAsRead('admin');
            }
        }
        
        // إعادة تحميل الرسائل في كل render لضمان التحديث الفوري
        $this->loadMessages();
        
        return view('livewire.chat-interface');
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function toggleEmojiPicker()
    {
        $this->showEmojiPicker = !$this->showEmojiPicker;
    }

    public function addEmoji($emoji)
    {
        $this->newMessage .= $emoji;
        $this->showEmojiPicker = false;
    }
}
