# 🚀 Pusher Real-Time Chat Setup Guide

## Overview

This is a complete real-time chat system built with Laravel and Pusher. It includes customer chat interface, admin management panel, and real-time broadcasting capabilities.

## 📋 Features

- ✅ **Real-time messaging** with Pusher WebSockets
- ✅ **Customer chat interface** with Laravel Echo
- ✅ **Admin management panel** with live updates
- ✅ **Message persistence** in MySQL database
- ✅ **Authentication & authorization** via Laravel Sanctum
- ✅ **Private channels** for secure communication
- ✅ **Connection status indicators**
- ✅ **Responsive design** for mobile/desktop
- ✅ **Message history** and pagination
- ✅ **Chat status management** (active/closed)

## 🛠️ Installation & Setup

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Broadcasting Driver
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

# Mix Variables (for frontend)
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 2. Install Pusher Package

```bash
composer require pusher/pusher-php-server
```

### 3. Run Database Migrations

```bash
php artisan migrate
```

### 4. Enable Broadcasting Service Provider

Uncomment in `config/app.php`:

```php
App\Providers\BroadcastServiceProvider::class,
```

### 5. Install Frontend Dependencies

```bash
npm install --save laravel-echo pusher-js
```

## 🗄️ Database Schema

### Tables Created

1. **pusher_chats**
   - `id` - Chat ID
   - `user_id` - Customer ID
   - `status` - active/closed/archived
   - `title` - Chat title
   - `last_message_at` - Last activity timestamp
   - `metadata` - Additional JSON data

2. **pusher_messages**
   - `id` - Message ID
   - `chat_id` - Related chat
   - `user_id` - Message sender
   - `message` - Message content
   - `sender_type` - customer/admin
   - `is_read` - Read status
   - `metadata` - Additional JSON data

## 🌐 API Endpoints

### Customer Endpoints

```http
GET  /api/pusher-chat/start
POST /api/pusher-chat/messages
GET  /api/pusher-chat/messages/{chat_id}
```

### Admin Endpoints

```http
GET  /api/pusher-chat/chats
POST /api/pusher-chat/chats/{chat_id}/reply
POST /api/pusher-chat/chats/{chat_id}/close
```

### Example API Usage

**Start a chat (Customer):**
```bash
curl -X GET https://suntop-eg.com/api/pusher-chat/start \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Send a message:**
```bash
curl -X POST https://suntop-eg.com/api/pusher-chat/messages \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"chat_id": 1, "message": "Hello support!"}'
```

## 🎨 Frontend Integration

### Laravel Echo Setup

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    auth: {
        headers: {
            'Authorization': 'Bearer ' + userToken,
            'X-CSRF-TOKEN': csrfToken
        }
    }
});
```

### Customer Chat Widget

```javascript
// Initialize customer chat
const chat = new PusherChatCustomer({
    pusherKey: 'your_pusher_key',
    pusherCluster: 'mt1',
    csrfToken: document.querySelector('meta[name="csrf-token"]').content,
    apiToken: userApiToken,
    baseUrl: 'https://suntop-eg.com',
    containerId: 'chat-container'
});
```

### Listen for Messages

```javascript
// Listen to specific chat
Echo.private(`chat.${chatId}`)
    .listen('message.sent', (data) => {
        console.log('New message:', data.message);
        // Update UI with new message
    });

// Admin listen to all chats
Echo.private('admin.chats')
    .listen('message.sent', (data) => {
        console.log('New message in chat:', data.chat.id);
        // Update admin dashboard
    });
```

## 👨‍💼 Admin Panel

### Web Routes

- `/admin/pusher-chat` - Chat dashboard
- `/admin/pusher-chat/{chat}` - Individual chat view

### Admin Features

- 📊 **Live dashboard** with chat statistics
- 💬 **Real-time chat interface**
- 👥 **Customer information display**
- 📈 **Chat analytics and metrics**
- 🔄 **Auto-refresh capabilities**
- ✅ **Chat status management**

## 🔐 Security & Authentication

### Channel Authorization

Private channels are automatically secured:

```php
// routes/channels.php
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Only allow chat owner or admins
    if ($user->role === 'admin') {
        return ['id' => $user->id, 'name' => $user->name, 'role' => 'admin'];
    }
    
    $chat = PusherChat::find($chatId);
    if ($chat && $chat->user_id === $user->id) {
        return ['id' => $user->id, 'name' => $user->name, 'role' => 'customer'];
    }
    
    return false;
});
```

### API Authentication

All API endpoints require Laravel Sanctum authentication:

```php
Route::middleware('auth:sanctum')->group(function () {
    // Protected chat routes
});
```

## 🧪 Testing

### Manual Testing

1. **Customer Flow:**
   - Login as customer
   - Access `/customer-chat-demo.html`
   - Send messages

2. **Admin Flow:**
   - Login as admin
   - Go to `/admin/pusher-chat`
   - View and reply to chats

### API Testing with Postman

Import the provided Postman collection for complete API testing.

## 🚨 Troubleshooting

### Common Issues

1. **Pusher connection fails**
   - Check credentials in `.env`
   - Verify Pusher app settings
   - Check network connectivity

2. **Messages not appearing in real-time**
   - Verify broadcasting is enabled
   - Check channel subscriptions
   - Inspect browser console for errors

3. **Authentication errors**
   - Ensure valid API tokens
   - Check middleware configuration
   - Verify user roles

### Debug Commands

```bash
# Test Pusher connection
php artisan tinker
> broadcast(new App\Events\MessageSent($message));

# Check broadcast config
php artisan config:show broadcasting

# Clear config cache
php artisan config:clear
```

## 📈 Performance Considerations

### Optimization Tips

1. **Database Indexing**
   - Indexes are already added to migration files
   - Consider additional indexes for heavy usage

2. **Message Pagination**
   - API endpoints support pagination
   - Implement infinite scroll for better UX

3. **Pusher Limits**
   - Monitor connection limits
   - Consider Pusher plan upgrades for production

4. **Caching**
   - Cache chat statistics
   - Use Redis for session management

## 🔄 Deployment

### Production Checklist

- [ ] Set proper Pusher credentials
- [ ] Configure SSL/TLS for WebSockets
- [ ] Set up queue workers for background jobs
- [ ] Configure proper CORS settings
- [ ] Set up monitoring for Pusher usage
- [ ] Test WebSocket connectivity in production environment

### Environment Variables for Production

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=prod_app_id
PUSHER_APP_KEY=prod_app_key
PUSHER_APP_SECRET=prod_app_secret
PUSHER_APP_CLUSTER=mt1

# Enable TLS
PUSHER_SCHEME=https
PUSHER_PORT=443
```

## 📚 Additional Resources

- [Pusher Documentation](https://pusher.com/docs)
- [Laravel Broadcasting](https://laravel.com/docs/broadcasting)
- [Laravel Echo Documentation](https://laravel.com/docs/broadcasting#client-side-installation)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)

## 🆘 Support

For issues or questions:

1. Check the troubleshooting section
2. Review Laravel and Pusher documentation
3. Check browser console for JavaScript errors
4. Verify API responses in Network tab

## 📝 License

This chat system is built on Laravel and follows the same MIT license.
