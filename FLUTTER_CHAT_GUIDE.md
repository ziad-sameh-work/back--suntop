# 📱 Flutter Chat Integration Guide - Real-time Chat API

## 🚀 Base URL
```
https://suntop-eg.com/api/
```

## 🔐 Authentication
All chat endpoints require **Sanctum Bearer Token**:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## 📡 **Chat API Endpoints**

### 1. **بدء/الحصول على محادثة** 
```
GET /api/chat/start
```

**Parameters (Optional):**
```json
{
  "subject": "مشكلة في الطلب",
  "priority": "high" // high, medium, low
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "chat": {
      "id": 123,
      "subject": "دعم عام",
      "status": "open",
      "priority": "medium",
      "created_at": "2024-01-01T12:00:00.000000Z",
      "assigned_admin": {
        "id": 1,
        "name": "أحمد محمد",
        "full_name": "أحمد محمد علي"
      },
      "unread_count": 0
    },
    "messages": [
      {
        "id": 456,
        "message": "مرحباً، كيف يمكنني مساعدتك؟",
        "message_type": "text",
        "sender_type": "admin",
        "sender": {
          "id": 1,
          "name": "أحمد محمد",
          "full_name": "أحمد محمد علي"
        },
        "attachment_url": null,
        "attachment_name": null,
        "created_at": "2024-01-01T12:00:00.000000Z",
        "formatted_time": "12:00",
        "is_read": false
      }
    ]
  }
}
```

### 2. **إرسال رسالة**
```
POST /api/chat/send
```

**Parameters:**
```json
{
  "chat_id": 123,
  "message": "مرحباً، أحتاج مساعدة",
  "attachment": "file (optional)" // Max 10MB, jpg,jpeg,png,gif,pdf,doc,docx,txt
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": {
      "id": 789,
      "message": "مرحباً، أحتاج مساعدة",
      "message_type": "text",
      "sender_type": "customer",
      "sender": {
        "id": 2,
        "name": "محمد أحمد",
        "full_name": "محمد أحمد علي"
      },
      "attachment_url": null,
      "attachment_name": null,
      "created_at": "2024-01-01T12:05:00.000000Z",
      "formatted_time": "12:05"
    }
  }
}
```

### 3. **الحصول على رسائل المحادثة**
```
GET /api/chat/{chatId}/messages
```

**Parameters:**
```
?page=1&per_page=50
```

**Response:**
```json
{
  "success": true,
  "data": {
    "messages": [...],
    "pagination": {
      "current_page": 1,
      "last_page": 3,
      "per_page": 50,
      "total": 150,
      "has_more": true
    }
  }
}
```

### 4. **تاريخ المحادثات**
```
GET /api/chat/history
```

**Parameters:**
```
?per_page=20
```

### 5. **تعليم الرسائل كمقروءة**
```
POST /api/chat/{chatId}/read
```

---

## 🔄 **Real-time Integration with Pusher**

### **Pusher Configuration**
```dart
// pubspec.yaml
dependencies:
  pusher_channels_flutter: ^2.2.1
```

### **Dart Code Example:**
```dart
import 'package:pusher_channels_flutter/pusher_channels_flutter.dart';

class ChatService {
  PusherChannelsFlutter pusher = PusherChannelsFlutter.getInstance();
  
  Future<void> initializePusher() async {
    try {
      await pusher.init(
        apiKey: "f546bf192457a6d47ed5", // Your Pusher key
        cluster: "eu", // Your cluster
        onConnectionStateChange: onConnectionStateChange,
        onError: onError,
        onSubscriptionSucceeded: onSubscriptionSucceeded,
        onEvent: onEvent,
        onSubscriptionError: onSubscriptionError,
        onDecryptionFailure: onDecryptionFailure,
        onMemberAdded: onMemberAdded,
        onMemberRemoved: onMemberRemoved,
        authEndpoint: "https://suntop-eg.com/api/broadcasting/auth",
        onAuthorizer: onAuthorizer
      );

      await pusher.connect();
    } catch (e) {
      print("Error initializing Pusher: $e");
    }
  }

  // Authentication for private channels
  dynamic onAuthorizer(String channelName, String socketId, dynamic options) {
    return {
      'Authorization': 'Bearer $yourAuthToken',
      'Content-Type': 'application/json',
    };
  }

  void onConnectionStateChange(dynamic currentState, dynamic previousState) {
    print("Connection: $currentState");
  }

  void onError(String message, int? code, dynamic e) {
    print("Error: $message");
  }

  void onEvent(PusherEvent event) {
    print("Event received: ${event.eventName}");
    
    if (event.eventName == 'message.new') {
      // Handle new message
      final data = jsonDecode(event.data);
      handleNewMessage(data);
    }
  }
  
  void handleNewMessage(Map<String, dynamic> data) {
    final message = data['message'];
    final chat = data['chat'];
    
    // Update your Flutter UI with new message
    print("New message: ${message['message']}");
    print("From: ${message['sender']['name']}");
    print("Chat ID: ${chat['id']}");
    
    // Add to your local message list
    // Update unread count
    // Show notification
  }

  // Subscribe to chat channel for specific chat
  Future<void> subscribeToChatChannel(int chatId) async {
    await pusher.subscribe(channelName: "chat.$chatId");
  }

  // Subscribe to private admin channel (for customer updates)
  Future<void> subscribeToAdminChannel() async {
    await pusher.subscribe(channelName: "private-admin.chats");
  }
}
```

### **Complete Flutter Chat Screen Example:**
```dart
class ChatScreen extends StatefulWidget {
  final int chatId;
  
  ChatScreen({required this.chatId});
  
  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  final ChatService _chatService = ChatService();
  final TextEditingController _messageController = TextEditingController();
  List<ChatMessage> messages = [];
  
  @override
  void initState() {
    super.initState();
    _initializeChat();
  }
  
  Future<void> _initializeChat() async {
    // Initialize Pusher
    await _chatService.initializePusher();
    
    // Subscribe to chat channel
    await _chatService.subscribeToChatChannel(widget.chatId);
    
    // Load existing messages
    await _loadMessages();
  }
  
  Future<void> _loadMessages() async {
    try {
      final response = await http.get(
        Uri.parse('https://suntop-eg.com/api/chat/${widget.chatId}/messages'),
        headers: {
          'Authorization': 'Bearer $yourToken',
          'Accept': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        setState(() {
          messages = data['data']['messages'].map<ChatMessage>(
            (msg) => ChatMessage.fromJson(msg)
          ).toList();
        });
      }
    } catch (e) {
      print('Error loading messages: $e');
    }
  }
  
  Future<void> _sendMessage() async {
    if (_messageController.text.trim().isEmpty) return;
    
    try {
      final response = await http.post(
        Uri.parse('https://suntop-eg.com/api/chat/send'),
        headers: {
          'Authorization': 'Bearer $yourToken',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'chat_id': widget.chatId,
          'message': _messageController.text.trim(),
        }),
      );
      
      if (response.statusCode == 200) {
        _messageController.clear();
        // Message will be added via Pusher real-time event
      }
    } catch (e) {
      print('Error sending message: $e');
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('الدعم الفني')),
      body: Column(
        children: [
          Expanded(
            child: ListView.builder(
              reverse: true,
              itemCount: messages.length,
              itemBuilder: (context, index) {
                final message = messages[index];
                return MessageBubble(message: message);
              },
            ),
          ),
          Container(
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

## 🎯 **Real-time Events**

### **Events You'll Receive:**

1. **message.new** - رسالة جديدة
```json
{
  "message": {
    "id": 123,
    "message": "مرحباً",
    "sender_type": "admin",
    "sender": {"name": "أحمد"}
  },
  "chat": {
    "id": 456,
    "unread_admin_count": 1
  }
}
```

### **Channels to Subscribe:**

1. **`chat.{chatId}`** - للرسائل في محادثة معينة
2. **`private-admin.chats`** - لتحديثات الإدارة (اختياري)

---

## 🔧 **Testing Real-time**

### **Test Endpoint:**
```
GET https://suntop-eg.com/test-broadcast-message/{chatId}
```

هذا الـ endpoint يرسل رسالة تجريبية لاختبار الـ real-time.

---

## 💡 **Best Practices**

1. **Handle Connection States:**
   - متصل/منقطع
   - إعادة الاتصال التلقائي

2. **Local Storage:**
   - احفظ الرسائل محلياً
   - Sync مع السيرفر عند الاتصال

3. **Notifications:**
   - إشعارات push للرسائل الجديدة
   - أصوات التنبيه

4. **UI Updates:**
   - تحديث فوري للرسائل
   - مؤشرات الكتابة
   - حالة القراءة

---

## ⚡ **Error Handling**

```dart
void onError(String message, int? code, dynamic e) {
  switch (code) {
    case 4001:
      // Application does not exist
      break;
    case 4004:
      // Application disabled
      break;
    case 4100:
      // Over connection limit
      break;
    default:
      print("Pusher Error: $message");
  }
}
```

---

## 🚀 **Ready to Use!**

الـ API جاهز للاستخدام مع Flutter والـ real-time شغال 100%. 

**المطلوب منك:**
1. استخدم الـ endpoints أعلاه
2. اربط Pusher زي المثال
3. اعمل subscribe للقنوات المطلوبة
4. اتعامل مع الـ events اللي جاية

**محتاج مساعدة؟** استخدم `/test-broadcast-message/{chatId}` لاختبار الـ real-time!
