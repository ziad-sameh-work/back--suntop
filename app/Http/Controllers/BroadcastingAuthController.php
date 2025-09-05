<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;
use App\Models\PusherChat;

class BroadcastingAuthController extends Controller
{
    /**
     * Authenticate user for Pusher channels
     */
    public function auth(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response('Unauthorized', 401);
        }

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');

        // Check channel authorization
        if (strpos($channelName, 'private-admin.chats') !== false) {
            // Admin chats channel
            if ($user->role !== 'admin') {
                return response('Forbidden', 403);
            }
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'role' => 'admin'
            ];
            
            $auth = $pusher->socket_auth($channelName, $socketId, json_encode($userData));
            return response($auth);
            
        } elseif (strpos($channelName, 'chat.') !== false && strpos($channelName, 'private-') === false) {
            // Public chat channel for individual chats
            $chatId = str_replace('chat.', '', $channelName);
            
            if ($user->role === 'admin') {
                // Admins can access any chat
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => 'admin'
                ];
                
                $auth = $pusher->socket_auth($channelName, $socketId, json_encode($userData));
                return response($auth);
                
            } else {
                // Customers can only access their own chat
                $chat = \App\Models\Chat::find($chatId);
                if ($chat && $chat->customer_id === $user->id) {
                    $userData = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => 'customer'
                    ];
                    
                    $auth = $pusher->socket_auth($channelName, $socketId, json_encode($userData));
                    return response($auth);
                }
            }
            
        } elseif (strpos($channelName, 'private-chat.') !== false) {
            // Individual chat channel
            $chatId = str_replace('private-chat.', '', $channelName);
            
            if ($user->role === 'admin') {
                // Admins can access any chat
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => 'admin'
                ];
                
                $auth = $pusher->socket_auth($channelName, $socketId, json_encode($userData));
                return response($auth);
                
            } else {
                // Customers can only access their own chat
                $chat = \App\Models\PusherChat::find($chatId) ?? \App\Models\Chat::find($chatId);
                if ($chat && ($chat->user_id === $user->id || $chat->customer_id === $user->id)) {
                    $userData = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => 'customer'
                    ];
                    
                    $auth = $pusher->socket_auth($channelName, $socketId, json_encode($userData));
                    return response($auth);
                }
            }
        }

        return response('Forbidden', 403);
    }
}
