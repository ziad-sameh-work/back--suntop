<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\MessageSent;

class PusherMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'user_id',
        'message',
        'sender_type',
        'is_read',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Sender type constants
     */
    const SENDER_CUSTOMER = 'customer';
    const SENDER_ADMIN = 'admin';

    /**
     * Boot model events for real-time broadcasting
     */
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($message) {
            // Update chat's last message time
            $message->chat()->update([
                'last_message_at' => now()
            ]);
            
            // Load necessary relationships before broadcasting
            $message->load(['user', 'chat.user']);
            
            // Dispatch the real-time event
            broadcast(new MessageSent($message))->toOthers();
            
            \Log::info('PusherMessage real-time event dispatched', [
                'message_id' => $message->id,
                'chat_id' => $message->chat_id,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->user->name ?? 'Unknown',
                'message_preview' => substr($message->message, 0, 50) . '...'
            ]);
        });
    }

    /**
     * Get the chat that owns the message
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(PusherChat::class, 'chat_id');
    }

    /**
     * Get the user that sent the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if message is from customer
     */
    public function isFromCustomer(): bool
    {
        return $this->sender_type === self::SENDER_CUSTOMER;
    }

    /**
     * Check if message is from admin
     */
    public function isFromAdmin(): bool
    {
        return $this->sender_type === self::SENDER_ADMIN;
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y');
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for messages by sender type
     */
    public function scopeBySenderType($query, string $senderType)
    {
        return $query->where('sender_type', $senderType);
    }
}
