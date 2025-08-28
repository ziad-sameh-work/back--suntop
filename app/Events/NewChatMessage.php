<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMessage;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatMessage;
    public $formattedMessage;

    /**
     * Create a new event instance.
     *
     * @param  ChatMessage  $chatMessage
     * @return void
     */
    public function __construct(ChatMessage $chatMessage)
    {
        $this->chatMessage = $chatMessage;
        
        // Load sender relationship
        $chatMessage->load('sender');
        
        // Format the message for broadcasting
        $this->formattedMessage = [
            'id' => $chatMessage->id,
            'chat_id' => $chatMessage->chat_id,
            'message' => $chatMessage->message,
            'sender_type' => $chatMessage->sender_type,
            'sender' => [
                'id' => $chatMessage->sender->id,
                'name' => $chatMessage->sender->name ?? 'غير معروف'
            ],
            'attachment_path' => $chatMessage->attachment_path,
            'attachment_url' => $chatMessage->attachment_path ? url('storage/' . $chatMessage->attachment_path) : null,
            'attachment_name' => $chatMessage->attachment_name,
            'message_type' => $chatMessage->message_type,
            'created_at' => $chatMessage->created_at->toISOString(),
            'formatted_time' => $chatMessage->created_at->format('H:i'),
            'is_read' => $chatMessage->is_read,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // Use a public channel instead of private to simplify integration with Flutter
        return new Channel('chat.' . $this->chatMessage->chat_id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.new';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->formattedMessage
        ];
    }
}
