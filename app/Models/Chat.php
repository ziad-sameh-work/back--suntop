<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'subject',
        'status',
        'priority',
        'assigned_admin_id',
        'last_message_at',
        'customer_unread_count',
        'admin_unread_count',
        'metadata'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array',
        'customer_unread_count' => 'integer',
        'admin_unread_count' => 'integer'
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->latest();
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $adminId)
    {
        return $query->where('assigned_admin_id', $adminId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_admin_id');
    }

    public function scopeWithUnreadMessages($query, $userType = 'admin')
    {
        $column = $userType === 'admin' ? 'admin_unread_count' : 'customer_unread_count';
        return $query->where($column, '>', 0);
    }

    // Helper methods
    public function markAsRead($userType = 'admin')
    {
        $column = $userType === 'admin' ? 'admin_unread_count' : 'customer_unread_count';
        $this->update([$column => 0]);

        // Mark all messages as read for this user type
        $this->messages()
            ->where('sender_type', '!=', $userType)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    public function incrementUnreadCount($userType = 'admin')
    {
        $column = $userType === 'admin' ? 'admin_unread_count' : 'customer_unread_count';
        $this->increment($column);
    }

    public function updateLastMessageTime()
    {
        $this->update(['last_message_at' => now()]);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'red',
            'in_progress' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedLastMessageTimeAttribute()
    {
        if (!$this->last_message_at) {
            return 'لا توجد رسائل';
        }

        return $this->last_message_at->diffForHumans();
    }
}
