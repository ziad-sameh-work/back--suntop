<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message)
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
                'user_id' => $this->message->sender_id,
                'message' => $this->message->message,
                'sender_type' => $this->message->sender_type,
                'is_read' => $this->message->is_read,
                'created_at' => $this->message->created_at->toISOString(),
                'formatted_time' => $this->message->created_at->format('H:i'),
                'formatted_date' => $this->message->created_at->format('d/m/Y'),
                'user' => [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                    'email' => $this->message->sender->email,
                    'role' => $this->message->sender->role ?? 'customer',
                ],
                'metadata' => $this->message->metadata ?? [],
            ],
            'chat' => [
                'id' => $this->message->chat->id,
                'user_id' => $this->message->chat->customer_id,
                'status' => $this->message->chat->status,
                'subject' => $this->message->chat->subject,
                'priority' => $this->message->chat->priority,
                'last_message_at' => $this->message->chat->last_message_at?->toISOString(),
                'unread_admin_count' => $this->message->chat->admin_unread_count,
                'unread_customer_count' => $this->message->chat->customer_unread_count,
                'customer' => [
                    'id' => $this->message->chat->customer->id,
                    'name' => $this->message->chat->customer->name,
                    'email' => $this->message->chat->customer->email,
                ],
                'last_message' => [
                    'message' => $this->message->message,
                    'sender_type' => $this->message->sender_type,
                    'created_at' => $this->message->created_at->toISOString(),
                ]
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
