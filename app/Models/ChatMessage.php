<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Events\NewChatMessage;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'sender_type',
        'message',
        'message_type',
        'attachment_path',
        'attachment_name',
        'is_read',
        'read_at',
        'metadata'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relationships
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Scopes
    public function scopeFromCustomer($query)
    {
        return $query->where('sender_type', 'customer');
    }

    public function scopeFromAdmin($query)
    {
        return $query->where('sender_type', 'admin');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeWithAttachment($query)
    {
        return $query->whereNotNull('attachment_path');
    }

    public function scopeTextOnly($query)
    {
        return $query->where('message_type', 'text');
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function hasAttachment()
    {
        return !empty($this->attachment_path);
    }

    public function getAttachmentUrlAttribute()
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        return Storage::url($this->attachment_path);
    }

    public function getAttachmentSizeAttribute()
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        $size = Storage::size($this->attachment_path);
        return $this->formatBytes($size);
    }

    public function isFromCustomer()
    {
        return $this->sender_type === 'customer';
    }

    public function isFromAdmin()
    {
        return $this->sender_type === 'admin';
    }

    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getFormattedDateTimeAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    // Boot method to handle automatic chat updates
    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            // Update chat's last message time
            $message->chat->updateLastMessageTime();
            
            // Increment unread count for the recipient
            $recipientType = $message->sender_type === 'customer' ? 'admin' : 'customer';
            $message->chat->incrementUnreadCount($recipientType);
            
            // إرسال الحدث لدعم الشات المباشر للتطبيقات والإدارة
            // استخدم Laravel Echo للشات المباشر
            if ($message->sender_type === 'customer' || 
                $message->sender_type === 'admin' || 
                (isset($message->metadata['sent_from']) && in_array($message->metadata['sent_from'], ['api_rt', 'admin_panel_firebase']))) {
                
                // Load necessary relationships before broadcasting
                $message->load(['sender', 'chat.customer']);
                
                // Dispatch the event
                event(new NewChatMessage($message));
                
                // Log for debugging
                \Log::info('NewChatMessage event dispatched for message ID: ' . $message->id, [
                    'chat_id' => $message->chat_id,
                    'sender_type' => $message->sender_type,
                    'sender_name' => $message->sender->name ?? 'Unknown',
                    'message' => substr($message->message, 0, 50) . '...'
                ]);
            }
        });
    }
}
