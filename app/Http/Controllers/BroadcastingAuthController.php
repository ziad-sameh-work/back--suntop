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
        // First try to get user from session
        $user = Auth::user();
        
        // If no user from session, try to get from guard
        if (!$user) {
            $user = Auth::guard('web')->user();
        }
        
        // Debug logging
        \Log::info('Broadcasting auth attempt', [
            'user_id' => $user ? $user->id : null,
            'user_role' => $user ? $user->role : null,
            'channel' => $request->input('channel_name'),
            'socket_id' => $request->input('socket_id'),
            'session_id' => $request->session()->getId(),
            'has_session' => $request->hasSession(),
            'csrf_token' => $request->input('_token'),
            'headers' => $request->headers->all(),
            'all_input' => $request->all()
        ]);
        
        if (!$user) {
            \Log::warning('Broadcasting auth failed: No authenticated user', [
                'session_data' => $request->session()->all()
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
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
        \Log::info('Broadcasting auth: Channel check', [
            'channel_name' => $channelName,
            'user_role' => $user->role,
            'is_private_admin_chats' => strpos($channelName, 'private-admin.chats') !== false
        ]);
        
        if (strpos($channelName, 'private-admin.chats') !== false) {
            // Admin chats channel
            \Log::info('Checking admin.chats channel access', [
                'user_role' => $user->role,
                'channel' => $channelName
            ]);
            
            if ($user->role !== 'admin') {
                \Log::warning('Broadcasting auth failed: User is not admin', [
                    'user_role' => $user->role,
                    'required_role' => 'admin'
                ]);
                return response()->json(['error' => 'Forbidden - Not admin'], 403);
            }
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'role' => 'admin'
            ];
            
            $auth = $pusher->socket_auth($channelName, $socketId, json_encode($userData));
            \Log::info('Broadcasting auth successful for admin.chats', [
                'user_id' => $user->id,
                'channel' => $channelName
            ]);
            return response($auth, 200, [
                'Content-Type' => 'application/json'
            ]);
            
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

        } elseif (strpos($channelName, 'admin-chats-public') !== false) {
            // Public admin channel for testing
            \Log::info('Public admin channel access', [
                'user_role' => $user->role,
                'channel' => $channelName
            ]);
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role
            ];
            
            $auth = $pusher->socket_auth($channelName, $socketId, json_encode($userData));
            \Log::info('Broadcasting auth successful for public admin channel', [
                'user_id' => $user->id,
                'channel' => $channelName
            ]);
            return response($auth, 200, [
                'Content-Type' => 'application/json'
            ]);
        }
        
        \Log::warning('Broadcasting auth: No matching channel found', [
            'channel_name' => $channelName,
            'user_role' => $user->role
        ]);
        
        return response()->json(['error' => 'Channel not found'], 403);
    }
}
