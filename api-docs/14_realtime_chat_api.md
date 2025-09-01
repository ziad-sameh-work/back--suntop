# API Documentation - Real-Time Chat for Mobile Apps

Base URL: `https://suntop-eg.com`

## Real-Time Chat API Endpoints

هذه الإندبوينتس مخصصة للشات في الوقت الفعلي (Real-Time) لتطبيقات الهاتف المحمول. وتم تصميمها لتكون سهلة الاستخدام مع تطبيقات Flutter دون الحاجة لإعدادات معقدة.

> ملاحظة مهمة: يمكنك استخدام إحدى الطريقتين للحصول على شات في الوقت الفعلي:
> 1. **WebSockets (موضح في هذا الملف)**: باستخدام Laravel Echo وPusher (توفر تجربة real-time كاملة)
> 2. **Long-Polling (موثقة في [`15_long_polling_chat_api.md`](./15_long_polling_chat_api.md))**: لا تحتاج لأي خدمات خارجية، وتعمل على أي استضافة

### بدء أو إنشاء محادثة جديدة
- **URL**: `/api/rt-chat/start`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "chat": {
      "id": "1",
      "subject": "دعم عام",
      "status": "open",
      "priority": "medium",
      "created_at": "2024-01-20T10:30:00Z",
      "assigned_admin": {
        "id": "2",
        "name": "مدير الدعم",
        "full_name": "أحمد علي"
      },
      "unread_count": 0,
      "channel_name": "chat.1",
      "event_name": "message.new"
    },
    "messages": [
      {
        "id": "1",
        "message": "مرحباً، كيف يمكنني مساعدتك؟",
        "message_type": "text",
        "sender_type": "admin",
        "sender": {
          "id": "2",
          "name": "مدير الدعم",
          "full_name": "أحمد علي"
        },
        "attachment_url": null,
        "attachment_name": null,
        "created_at": "2024-01-20T10:30:05Z",
        "formatted_time": "10:30",
        "is_read": true
      }
    ]
  }
}
```

### إرسال رسالة جديدة
- **URL**: `/api/rt-chat/send`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
  - `Content-Type: multipart/form-data` (عند إرفاق ملف)
- **Request Body**:
```
chat_id: 1
message: "أريد الاستفسار عن منتج"
attachment: [FILE] (اختياري)
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "message": {
      "id": "2",
      "message": "أريد الاستفسار عن منتج",
      "message_type": "text",
      "sender_type": "customer",
      "sender": {
        "id": "1",
        "name": "محمد أحمد",
        "full_name": "محمد أحمد علي"
      },
      "attachment_url": null,
      "attachment_name": null,
      "created_at": "2024-01-20T10:35:00Z",
      "formatted_time": "10:35",
      "is_read": false
    }
  }
}
```

### الحصول على رسائل المحادثة
- **URL**: `/api/rt-chat/{chatId}/messages`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `per_page` (اختياري): عدد الرسائل في الصفحة (افتراضي: 50)
  - `page` (اختياري): رقم الصفحة (افتراضي: 1)
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "id": "1",
        "message": "مرحباً، كيف يمكنني مساعدتك؟",
        "message_type": "text",
        "sender_type": "admin",
        "sender": {
          "id": "2",
          "name": "مدير الدعم",
          "full_name": "أحمد علي"
        },
        "attachment_url": null,
        "attachment_name": null,
        "created_at": "2024-01-20T10:30:05Z",
        "formatted_time": "10:30",
        "is_read": true
      }
    ],
    "real_time": {
      "channel_name": "chat.1",
      "event_name": "message.new"
    },
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 50,
      "total": 1,
      "has_more": false
    }
  }
}
```

## تنفيذ Real-Time Chat في Flutter بدون تعقيد

يمكن تنفيذ الشات في الوقت الفعلي في تطبيق Flutter باستخدام مكتبة `pusher_client` بطريقة سهلة جدًا دون الحاجة لأي إعدادات معقدة أو توكين خاص.

### 1. إضافة المكتبات المطلوبة

```yaml
# pubspec.yaml
dependencies:
  pusher_client: ^2.0.0
  http: ^0.13.5
```

### 2. تنفيذ خدمة الشات

```dart
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:pusher_client/pusher_client.dart';
import 'package:http/http.dart' as http;

class ChatService {
  final String apiBaseUrl;
  final String userToken;
  
  PusherClient? _pusher;
  Channel? _chatChannel;
  int? _currentChatId;
  
  ChatService({required this.apiBaseUrl, required this.userToken});

  // بدء الشات وتجهيزه للاستخدام
  Future<Map<String, dynamic>> startChat() async {
    final response = await http.get(
      Uri.parse('$apiBaseUrl/api/rt-chat/start'),
      headers: {
        'Authorization': 'Bearer $userToken',
        'Accept': 'application/json'
      },
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        final chatData = data['data']['chat'];
        _currentChatId = int.parse(chatData['id']);
        _connectToPusher(chatData['channel_name'], chatData['event_name']);
        return data['data'];
      }
    }
    
    throw Exception('Failed to start chat');
  }
  
  // الاتصال بقناة Pusher للاستماع للرسائل الجديدة
  void _connectToPusher(String channelName, String eventName) {
    // تكوين Pusher بدون الحاجة لتوكين خاص
    _pusher = PusherClient(
      // استخدم القيم من .env الخاصة بالسيرفر
      'your-app-key', // قم بتغيير هذه القيمة وفقًا لإعدادات السيرفر
      PusherOptions(
        cluster: 'eu', // قم بتغيير هذه القيمة وفقًا لإعدادات السيرفر
        encrypted: true,
      ),
      enableLogging: true,
    );
    
    _chatChannel = _pusher?.subscribe(channelName);
    
    // الاستماع للأحداث
    _chatChannel?.bind(eventName, (event) {
      if (event != null && event.data != null) {
        final messageData = jsonDecode(event.data!)['message'];
        // قم باستدعاء الدالة المسؤولة عن إضافة الرسالة الجديدة هنا
        onNewMessage?.call(messageData);
      }
    });
  }
  
  // دالة يتم استدعاؤها عند وصول رسالة جديدة
  Function(Map<String, dynamic>)? onNewMessage;
  
  // إرسال رسالة جديدة
  Future<Map<String, dynamic>> sendMessage(String message, {File? attachment}) async {
    if (_currentChatId == null) {
      throw Exception('Chat not started yet');
    }
    
    var request = http.MultipartRequest(
      'POST',
      Uri.parse('$apiBaseUrl/api/rt-chat/send'),
    );
    
    request.headers['Authorization'] = 'Bearer $userToken';
    request.headers['Accept'] = 'application/json';
    
    request.fields['chat_id'] = _currentChatId.toString();
    request.fields['message'] = message;
    
    if (attachment != null) {
      request.files.add(
        await http.MultipartFile.fromPath('attachment', attachment.path)
      );
    }
    
    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['data']['message'];
      }
    }
    
    throw Exception('Failed to send message');
  }
  
  // الحصول على سجل الرسائل السابقة
  Future<Map<String, dynamic>> getMessages({int page = 1, int perPage = 50}) async {
    if (_currentChatId == null) {
      throw Exception('Chat not started yet');
    }
    
    final response = await http.get(
      Uri.parse('$apiBaseUrl/api/rt-chat/$_currentChatId/messages?page=$page&per_page=$perPage'),
      headers: {
        'Authorization': 'Bearer $userToken',
        'Accept': 'application/json'
      },
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data['data'];
      }
    }
    
    throw Exception('Failed to get messages');
  }
  
  // إلغاء الاشتراك وإغلاق الاتصال
  void dispose() {
    if (_chatChannel != null && _pusher != null) {
      _pusher?.unsubscribe(_chatChannel!.name);
    }
    _pusher?.disconnect();
  }
}
```

### 3. استخدام خدمة الشات في واجهة Flutter

```dart
class ChatScreen extends StatefulWidget {
  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  final ChatService _chatService = ChatService(
    apiBaseUrl: 'https://suntop-eg.com',
    userToken: 'your_auth_token_here'
  );
  
  List<Map<String, dynamic>> messages = [];
  Map<String, dynamic>? chatInfo;
  bool isLoading = true;
  final TextEditingController messageController = TextEditingController();
  
  @override
  void initState() {
    super.initState();
    _initChat();
  }
  
  Future<void> _initChat() async {
    try {
      setState(() {
        isLoading = true;
      });
      
      // بدء الشات
      final chatData = await _chatService.startChat();
      
      // تعيين معلومات الشات
      chatInfo = chatData['chat'];
      
      // تحميل الرسائل السابقة
      messages = List<Map<String, dynamic>>.from(chatData['messages']);
      
      // الاستماع للرسائل الجديدة
      _chatService.onNewMessage = (newMessage) {
        setState(() {
          messages.add(newMessage);
        });
      };
      
      setState(() {
        isLoading = false;
      });
    } catch (e) {
      print('Error initializing chat: $e');
      setState(() {
        isLoading = false;
      });
    }
  }
  
  Future<void> _sendMessage() async {
    if (messageController.text.trim().isEmpty) return;
    
    final messageText = messageController.text;
    messageController.clear();
    
    try {
      await _chatService.sendMessage(messageText);
      // لا نحتاج لإضافة الرسالة يدويًا لأن onNewMessage سيتم استدعاؤه تلقائيًا
    } catch (e) {
      print('Error sending message: $e');
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('فشل إرسال الرسالة'))
      );
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(chatInfo?['subject'] ?? 'المحادثة'),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : Column(
              children: [
                Expanded(
                  child: ListView.builder(
                    itemCount: messages.length,
                    reverse: true,
                    itemBuilder: (context, index) {
                      final message = messages[messages.length - 1 - index];
                      final isFromMe = message['sender_type'] == 'customer';
                      
                      return Align(
                        alignment: isFromMe ? Alignment.centerRight : Alignment.centerLeft,
                        child: Container(
                          margin: EdgeInsets.all(8),
                          padding: EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: isFromMe ? Colors.blue[100] : Colors.grey[200],
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              if (message['message_type'] == 'text')
                                Text(message['message']),
                              if (message['message_type'] == 'image')
                                Image.network(message['attachment_url']),
                              if (message['message_type'] == 'file')
                                TextButton.icon(
                                  icon: Icon(Icons.attachment),
                                  label: Text(message['attachment_name'] ?? 'مرفق'),
                                  onPressed: () {
                                    // فتح الملف المرفق
                                  },
                                ),
                              SizedBox(height: 4),
                              Text(
                                message['formatted_time'],
                                style: TextStyle(fontSize: 10, color: Colors.grey),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
                Padding(
                  padding: EdgeInsets.all(8.0),
                  child: Row(
                    children: [
                      IconButton(
                        icon: Icon(Icons.attach_file),
                        onPressed: () {
                          // تنفيذ وظيفة إرفاق الملفات
                        },
                      ),
                      Expanded(
                        child: TextField(
                          controller: messageController,
                          decoration: InputDecoration(
                            hintText: 'اكتب رسالتك هنا...',
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(24),
                            ),
                          ),
                        ),
                      ),
                      IconButton(
                        icon: Icon(Icons.send),
                        onPressed: _sendMessage,
                      ),
                    ],
                  ),
                ),
              ],
            ),
    );
  }
  
  @override
  void dispose() {
    _chatService.dispose();
    messageController.dispose();
    super.dispose();
  }
}
```

## الطريقة البديلة: Long-Polling

> **هل تواجه صعوبات مع Pusher؟** 
>
> نوفر أيضًا حلاً بديلاً باستخدام Long-Polling، وهو أسهل في التنفيذ ولا يتطلب أي إعدادات خارجية. 
>
> راجع التوثيق الكامل في [Long-Polling Chat API](./15_long_polling_chat_api.md)

## أنواع الرسائل

- **text**: رسالة نصية عادية
- **image**: رسالة تحتوي على صورة
- **file**: رسالة تحتوي على ملف

## أنواع المرسلين

- **customer**: العميل
- **admin**: مدير النظام

## استجابات الخطأ

### غير مصرح (401)
```json
{
  "success": false,
  "error": {
    "message": "Unauthorized. Customer access required.",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

### خطأ في التحقق (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "chat_id": [
      "The chat id field is required."
    ],
    "message": [
      "The message field is required when attachment is not present."
    ]
  }
}
```