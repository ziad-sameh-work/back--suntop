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

// Admin channel for all chats
Broadcast::channel('admin.chats', function ($user) {
    if ($user->role === 'admin') {
        return ['id' => $user->id, 'name' => $user->name, 'role' => 'admin'];
    }
    return false;
});
