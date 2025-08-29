# 🚀 Pusher Real-Time Chat API - Flutter Documentation

## 📋 **Base Information**

**Base URL:** `https://your-domain.com/api`
**Authentication:** Bearer Token (Laravel Sanctum)
**Content-Type:** `application/json`
**Real-time:** Pusher WebSockets

---

## 🔐 **Authentication**

All endpoints require Bearer token authentication:

```http
Authorization: Bearer {your-sanctum-token}
```

---

## 📱 **Customer Endpoints**

### 1. **Start/Get Chat**
**Get or create a chat session for the current customer**

**Endpoint:** `GET /api/pusher-chat/start`

**Headers:**
```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "chat": {
      "id": 1,
      "user_id": 5,
      "status": "active",
      "title": "Chat with John Doe",
      "last_message_at": "2024-12-28T15:30:00.000000Z",
      "unread_admin_count": 0,
      "unread_customer_count": 2,
      "created_at": "2024-12-28T10:00:00.000000Z",
      "updated_at": "2024-12-28T15:30:00.000000Z",
      "customer": {
        "id": 5,
        "name": "John Doe",
        "email": "john@example.com"
      }
    },
    "messages": [
      {
        "id": 1,
        "chat_id": 1,
        "user_id": 5,
        "message": "Hello, I need help with my order",
        "sender_type": "customer",
        "is_read": true,
        "created_at": "2024-12-28T10:05:00.000000Z",
        "formatted_time": "10:05",
        "formatted_date": "Today",
        "user": {
          "id": 5,
          "name": "John Doe",
          "email": "john@example.com",
          "role": "customer"
        },
        "metadata": {
          "ip_address": "192.168.1.100",
          "user_agent": "Flutter App"
        }
      }
    ]
  }
}
```

---

### 2. **Send Message**
**Send a message in a chat**

**Endpoint:** `POST /api/pusher-chat/messages`

**Headers:**
```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "chat_id": 1,
  "message": "I'm having trouble with my recent order #1234"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "message": {
      "id": 15,
      "chat_id": 1,
      "user_id": 5,
      "message": "I'm having trouble with my recent order #1234",
      "sender_type": "customer",
      "is_read": false,
      "created_at": "2024-12-28T15:45:00.000000Z",
      "formatted_time": "15:45",
      "formatted_date": "Today",
      "user": {
        "id": 5,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "customer"
      },
      "metadata": {
        "ip_address": "192.168.1.100",
        "user_agent": "Flutter App"
      }
    }
  }
}
```

---

### 3. **Get Messages**
**Get messages for a specific chat with pagination**

**Endpoint:** `GET /api/pusher-chat/messages/{chat_id}`

**Headers:**
```http
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Messages per page (default: 50, max: 100)

**Example:** `GET /api/pusher-chat/messages/1?page=1&per_page=20`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "chat": {
      "id": 1,
      "user_id": 5,
      "status": "active",
      "title": "Chat with John Doe",
      "last_message_at": "2024-12-28T15:45:00.000000Z",
      "unread_admin_count": 0,
      "unread_customer_count": 0,
      "created_at": "2024-12-28T10:00:00.000000Z",
      "updated_at": "2024-12-28T15:45:00.000000Z",
      "customer": {
        "id": 5,
        "name": "John Doe",
        "email": "john@example.com"
      }
    },
    "messages": [
      {
        "id": 15,
        "chat_id": 1,
        "user_id": 5,
        "message": "I'm having trouble with my recent order #1234",
        "sender_type": "customer",
        "is_read": true,
        "created_at": "2024-12-28T15:45:00.000000Z",
        "formatted_time": "15:45",
        "formatted_date": "Today",
        "user": {
          "id": 5,
          "name": "John Doe",
          "email": "john@example.com",
          "role": "customer"
        },
        "metadata": {
          "ip_address": "192.168.1.100",
          "user_agent": "Flutter App"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 3,
      "per_page": 20,
      "total": 45,
      "has_more": true
    }
  }
}
```

---

## 🛡️ **Admin Endpoints**

### 4. **Get All Chats (Admin Only)**
**Get all chats for admin dashboard**

**Endpoint:** `GET /api/pusher-chat/chats`

**Headers:**
```http
Authorization: Bearer {admin-token}
```

**Query Parameters:**
- `status` (optional): Filter by status (`active`, `closed`, `pending`)
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Chats per page (default: 20)

**Example:** `GET /api/pusher-chat/chats?status=active&page=1&per_page=10`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "chats": [
      {
        "id": 1,
        "user_id": 5,
        "status": "active",
        "title": "Chat with John Doe",
        "last_message_at": "2024-12-28T15:45:00.000000Z",
        "unread_admin_count": 3,
        "unread_customer_count": 0,
        "created_at": "2024-12-28T10:00:00.000000Z",
        "updated_at": "2024-12-28T15:45:00.000000Z",
        "customer": {
          "id": 5,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "latest_message": {
          "id": 15,
          "chat_id": 1,
          "user_id": 5,
          "message": "I'm having trouble with my recent order #1234",
          "sender_type": "customer",
          "is_read": false,
          "created_at": "2024-12-28T15:45:00.000000Z",
          "formatted_time": "15:45",
          "formatted_date": "Today",
          "user": {
            "id": 5,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "customer"
          },
          "metadata": {}
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 5,
      "has_more": false
    }
  }
}
```

---

### 5. **Admin Reply**
**Send admin reply to a customer chat**

**Endpoint:** `POST /api/pusher-chat/chats/{chat_id}/reply`

**Headers:**
```http
Authorization: Bearer {admin-token}
Content-Type: application/json
```

**Body:**
```json
{
  "message": "Hello! I can help you with your order. Let me check the details for order #1234."
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "message": {
      "id": 16,
      "chat_id": 1,
      "user_id": 2,
      "message": "Hello! I can help you with your order. Let me check the details for order #1234.",
      "sender_type": "admin",
      "is_read": false,
      "created_at": "2024-12-28T15:50:00.000000Z",
      "formatted_time": "15:50",
      "formatted_date": "Today",
      "user": {
        "id": 2,
        "name": "Admin User",
        "email": "admin@suntop.com",
        "role": "admin"
      },
      "metadata": {
        "admin_id": 2,
        "admin_name": "Admin User",
        "ip_address": "192.168.1.50"
      }
    }
  }
}
```

---

### 6. **Close Chat (Admin Only)**
**Close a chat session**

**Endpoint:** `POST /api/pusher-chat/chats/{chat_id}/close`

**Headers:**
```http
Authorization: Bearer {admin-token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Chat closed successfully"
}
```

---

## 🌐 **Real-Time Integration (Pusher)**

### **Pusher Configuration**

```dart
// Flutter Pusher Configuration
final pusher = PusherChannelsFlutter.getInstance();

await pusher.init(
  apiKey: "44911da009b5537ffae1",
  cluster: "eu",
  useTLS: true,
  authEndpoint: "https://your-domain.com/api/broadcasting/auth",
  authParams: {
    'headers': {
      'Authorization': 'Bearer $yourSanctumToken',
      'Accept': 'application/json',
    }
  }
);

await pusher.connect();
```

### **Channel Subscription**

```dart
// Option 1: Subscribe to public chat channel (Recommended for Flutter)
final channel = await pusher.subscribe(
  channelName: "pusher-chat.${chatId}",
);

// Option 2: Subscribe to private chat channel (requires auth)
final privateChannel = await pusher.subscribe(
  channelName: "private-chat.${chatId}",
);

// Listen for new messages on either channel
channel.bind("message.sent", (event) {
  final data = jsonDecode(event.data);
  final message = data['message'];
  
  // Update your chat UI with the new message
  updateChatWithNewMessage(message);
});
```

### **Admin Channel (for admins)**

```dart
// Subscribe to admin channel for all chat notifications
final adminChannel = await pusher.subscribe(
  channelName: "private-admin.chats",
);

adminChannel.bind("message.sent", (event) {
  final data = jsonDecode(event.data);
  // Update admin chat list with notification
  updateAdminChatList(data);
});
```

---

## 📱 **Flutter Implementation Example**

### **Chat Service Class**

```dart
class PusherChatService {
  static const String baseUrl = "https://your-domain.com/api";
  static const String pusherKey = "44911da009b5537ffae1";
  static const String pusherCluster = "eu";
  
  late Dio _dio;
  late PusherChannelsFlutter _pusher;
  String? _currentChatId;
  
  Future<void> initialize(String token) async {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));
    
    _pusher = PusherChannelsFlutter.getInstance();
    await _pusher.init(
      apiKey: pusherKey,
      cluster: pusherCluster,
      useTLS: true,
      authEndpoint: "$baseUrl/broadcasting/auth",
      authParams: {
        'headers': {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        }
      }
    );
    
    await _pusher.connect();
  }
  
  Future<Map<String, dynamic>> startChat() async {
    final response = await _dio.get('/pusher-chat/start');
    _currentChatId = response.data['data']['chat']['id'].toString();
    await _subscribeToChat(_currentChatId!);
    return response.data;
  }
  
  Future<Map<String, dynamic>> sendMessage(String chatId, String message) async {
    final response = await _dio.post('/pusher-chat/messages', data: {
      'chat_id': int.parse(chatId),
      'message': message,
    });
    return response.data;
  }
  
  Future<Map<String, dynamic>> getMessages(String chatId, {int page = 1, int perPage = 50}) async {
    final response = await _dio.get('/pusher-chat/messages/$chatId?page=$page&per_page=$perPage');
    return response.data;
  }
  
  Future<void> _subscribeToChat(String chatId) async {
    // Use public channel for easier access (no auth required)
    final channel = await _pusher.subscribe(channelName: "pusher-chat.$chatId");
    
    channel.bind("message.sent", (event) {
      final data = jsonDecode(event.data);
      // Handle new message in your UI
      onNewMessage?.call(data['message']);
    });
  }
  
  Function(Map<String, dynamic>)? onNewMessage;
}
```

### **Usage Example**

```dart
class ChatScreen extends StatefulWidget {
  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  final PusherChatService _chatService = PusherChatService();
  final List<Map<String, dynamic>> _messages = [];
  final TextEditingController _messageController = TextEditingController();
  
  @override
  void initState() {
    super.initState();
    _initializeChat();
  }
  
  Future<void> _initializeChat() async {
    await _chatService.initialize(userToken);
    
    _chatService.onNewMessage = (message) {
      setState(() {
        _messages.add(message);
      });
    };
    
    final chatData = await _chatService.startChat();
    setState(() {
      _messages.addAll(List<Map<String, dynamic>>.from(chatData['data']['messages']));
    });
  }
  
  Future<void> _sendMessage() async {
    final message = _messageController.text.trim();
    if (message.isEmpty) return;
    
    _messageController.clear();
    
    await _chatService.sendMessage(_chatService._currentChatId!, message);
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('دعم العملاء')),
      body: Column(
        children: [
          Expanded(
            child: ListView.builder(
              itemCount: _messages.length,
              itemBuilder: (context, index) {
                final message = _messages[index];
                final isCustomer = message['sender_type'] == 'customer';
                
                return Align(
                  alignment: isCustomer ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: EdgeInsets.all(8),
                    padding: EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: isCustomer ? Colors.blue[100] : Colors.grey[200],
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(message['message']),
                        SizedBox(height: 4),
                        Text(
                          message['formatted_time'],
                          style: TextStyle(fontSize: 12, color: Colors.grey),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
          Padding(
            padding: EdgeInsets.all(8),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _messageController,
                    decoration: InputDecoration(
                      hintText: 'اكتب رسالتك...',
                      border: OutlineInputBorder(),
                    ),
                  ),
                ),
                SizedBox(width: 8),
                IconButton(
                  onPressed: _sendMessage,
                  icon: Icon(Icons.send),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
```

---

## ⚠️ **Error Responses**

### **Common Error Formats**

**401 Unauthorized:**
```json
{
  "success": false,
  "message": "Unauthorized. Customer access required."
}
```

**403 Forbidden:**
```json
{
  "success": false,
  "message": "Unauthorized to access this chat."
}
```

**422 Validation Error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "message": ["The message field is required."],
    "chat_id": ["The selected chat id is invalid."]
  }
}
```

**500 Server Error:**
```json
{
  "success": false,
  "message": "Failed to send message: Database connection error"
}
```

---

## 🔧 **Testing & Debugging**

### **Test API with Postman/Insomnia**

1. **Get Bearer Token:**
   ```http
   POST /api/auth/login
   Content-Type: application/json
   
   {
     "email": "test@example.com",
     "password": "password"
   }
   ```

2. **Start Chat:**
   ```http
   GET /api/pusher-chat/start
   Authorization: Bearer {token}
   ```

3. **Send Message:**
   ```http
   POST /api/pusher-chat/messages
   Authorization: Bearer {token}
   Content-Type: application/json
   
   {
     "chat_id": 1,
     "message": "Test message from API"
   }
   ```

### **Pusher Debug Console**

Monitor real-time events at: `https://dashboard.pusher.com/apps/{app_id}/console`

---

## 📝 **Notes & Best Practices**

1. **Message Limits:** Maximum 2000 characters per message
2. **Rate Limiting:** Consider implementing rate limiting in Flutter
3. **Offline Support:** Store messages locally and sync when online
4. **Error Handling:** Always handle network errors gracefully
5. **Real-time Reconnection:** Handle Pusher disconnections and reconnect automatically
6. **Pagination:** Load older messages with pagination for better performance
7. **Message Status:** Track message delivery and read status
8. **Typing Indicators:** Can be implemented using Pusher presence channels

---

## 🚀 **Ready to Use!**

The API is fully functional and tested. All endpoints support real-time messaging through Pusher WebSockets. The Flutter implementation will provide a smooth, real-time chat experience for your users.

**Base URL:** `https://your-domain.com/api`
**Pusher App Key:** `44911da009b5537ffae1`
**Pusher Cluster:** `eu`

Contact support team for any questions or additional features! 🎉
