<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Pusher Chat Channels (Public channel for easier access)
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Allow access if user is admin or owns the chat
    if ($user->role === 'admin') {
        return ['id' => $user->id, 'name' => $user->name, 'role' => 'admin'];
    }
    
    $chat = \App\Models\PusherChat::find($chatId) ?? \App\Models\Chat::find($chatId);
    if ($chat && ($chat->user_id === $user->id || $chat->customer_id === $user->id)) {
        return ['id' => $user->id, 'name' => $user->name, 'role' => 'customer'];
    }
    
    return false;
});

// Public Pusher Chat Channel (for easier real-time access)
Broadcast::channel('pusher-chat.{chatId}', function ($user, $chatId) {
    // Allow access if user is admin or owns the chat
    if ($user->role === 'admin') {
        return ['id' => $user->id, 'name' => $user->name, 'role' => 'admin'];
    }
    
    $chat = \App\Models\PusherChat::find($chatId);
    if ($chat && $chat->user_id === $user->id) {
        return ['id' => $user->id, 'name' => $user->name, 'role' => 'customer'];
    }
    
    return false;
});

// Admin channel for all chats (private channel)
Broadcast::channel('admin.chats', function ($user) {
    \Log::info('Channel authorization for admin.chats', [
        'user_id' => $user ? $user->id : null,
        'user_role' => $user ? $user->role : null,
        'is_admin' => $user && $user->role === 'admin'
    ]);
    
    if ($user && $user->role === 'admin') {
        return ['id' => $user->id, 'name' => $user->name, 'role' => 'admin'];
    }
    
    \Log::warning('Channel authorization denied for admin.chats', [
        'user_role' => $user ? $user->role : 'no_user'
    ]);
    
    return false;
});

// Public admin channel for testing (no authentication required)
Broadcast::channel('admin-chats-public', function ($user) {
    \Log::info('Public admin channel access', [
        'user_id' => $user ? $user->id : null,
        'user_role' => $user ? $user->role : null
    ]);
    
    // Allow all authenticated users for testing
    if ($user) {
        return ['id' => $user->id, 'name' => $user->name, 'role' => $user->role];
    }
    
    return false;
});

// Mobile-specific public channel for easier real-time (NO AUTH REQUIRED)
// هذه القناة للموبايل بدون authentication للسهولة
Broadcast::channel('mobile-chat.{chatId}', function ($user, $chatId) {
    // Log للتتبع
    \Log::info('Mobile chat channel access attempt', [
        'user_id' => $user ? $user->id : 'no_user',
        'chat_id' => $chatId,
        'user_role' => $user ? $user->role : 'no_user'
    ]);
    
    // السماح للجميع للتسهيل على الموبايل
    if ($user) {
        return [
            'id' => $user->id, 
            'name' => $user->name, 
            'role' => $user->role,
            'platform' => 'mobile'
        ];
    }
    
    // حتى بدون مستخدم للاختبار
    return [
        'id' => 'guest',
        'name' => 'Guest User',
        'role' => 'guest',
        'platform' => 'mobile'
    ];
});