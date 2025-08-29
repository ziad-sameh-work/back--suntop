<?php

namespace App\Events;

use App\Models\PusherMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PusherMessage $message;

    /**
     * Create a new event instance.
     */
    public function __construct(PusherMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // Private channel for the specific chat
            new PrivateChannel('chat.' . $this->message->chat_id),
            // Public channel for easier Flutter access
            new Channel('pusher-chat.' . $this->message->chat_id),
            // Channel for all admins to see new messages
            new PrivateChannel('admin.chats'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'chat_id' => $this->message->chat_id,
                'user_id' => $this->message->user_id,
                'message' => $this->message->message,
                'sender_type' => $this->message->sender_type,
                'is_read' => $this->message->is_read,
                'created_at' => $this->message->created_at->toISOString(),
                'formatted_time' => $this->message->formatted_time,
                'formatted_date' => $this->message->formatted_date,
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'email' => $this->message->user->email,
                    'role' => $this->message->user->role ?? 'customer',
                ],
                'metadata' => $this->message->metadata,
            ],
            'chat' => [
                'id' => $this->message->chat->id,
                'user_id' => $this->message->chat->user_id,
                'status' => $this->message->chat->status,
                'title' => $this->message->chat->title,
                'last_message_at' => $this->message->chat->last_message_at?->toISOString(),
                'unread_admin_count' => $this->message->chat->unread_admin_count,
                'unread_customer_count' => $this->message->chat->unread_customer_count,
                'customer' => [
                    'id' => $this->message->chat->user->id,
                    'name' => $this->message->chat->user->name,
                    'email' => $this->message->chat->user->email,
                ],
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
