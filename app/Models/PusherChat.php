<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PusherChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'title',
        'subject',
        'last_message_at',
        'metadata',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Chat status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Get the user that owns the chat
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer that owns the chat (alias for user)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all messages for this chat
     */
    public function messages(): HasMany
    {
        return $this->hasMany(PusherMessage::class, 'chat_id');
    }

    /**
     * Get the latest message
     */
    public function latestMessage()
    {
        return $this->hasOne(PusherMessage::class, 'chat_id')->latest();
    }

    /**
     * Get unread messages count for admins
     */
    public function getUnreadAdminCountAttribute(): int
    {
        return $this->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get unread messages count for customers
     */
    public function getUnreadCustomerCountAttribute(): int
    {
        return $this->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark messages as read
     */
    public function markMessagesAsRead(string $userType): void
    {
        $senderType = $userType === 'admin' ? 'customer' : 'admin';
        
        $this->messages()
            ->where('sender_type', $senderType)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Scope for active chats
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for chats with recent activity
     */
    public function scopeWithRecentActivity($query)
    {
        return $query->whereNotNull('last_message_at')
            ->orderBy('last_message_at', 'desc');
    }

    /**
     * Get subject attribute (fallback to title if subject is null)
     */
    public function getSubjectAttribute($value)
    {
        return $value ?: $this->title;
    }

    /**
     * Get formatted last message time
     */
    public function getFormattedLastMessageTimeAttribute()
    {
        if (!$this->last_message_at) {
            return $this->created_at->diffForHumans();
        }
        
        $diffInMinutes = $this->last_message_at->diffInMinutes(now());
        
        if ($diffInMinutes < 1) {
            return 'الآن';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' دقيقة';
        } elseif ($diffInMinutes < 1440) {
            return $this->last_message_at->format('H:i');
        } else {
            return $this->last_message_at->format('M d');
        }
    }
}
