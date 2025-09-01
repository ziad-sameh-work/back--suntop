# API Documentation - Long-Polling Chat for Mobile Apps

Base URL: `https://suntop-eg.com`

## Long-Polling Chat API Endpoints

هذه الإندبوينتس مخصصة للشات في الوقت الفعلي (Real-Time) باستخدام تقنية Long-Polling لتطبيقات الهاتف المحمول مثل Flutter. هذه الطريقة لا تحتاج إلى توكين أو إعدادات معقدة.

### بدء أو إنشاء محادثة جديدة
- **URL**: `/api/lp-chat/start`
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
      "last_message_timestamp": 1714384381
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
        "timestamp": 1705746605,
        "formatted_time": "10:30",
        "is_read": true
      }
    ]
  }
}
```

### إرسال رسالة جديدة
- **URL**: `/api/lp-chat/send`
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
      "timestamp": 1705746900,
      "formatted_time": "10:35",
      "is_read": false
    },
    "last_message_timestamp": 1714384381
  }
}
```

### الحصول على رسائل المحادثة
- **URL**: `/api/lp-chat/{chatId}/messages`
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
        "timestamp": 1705746605,
        "formatted_time": "10:30",
        "is_read": true
      }
    ],
    "last_message_timestamp": 1714384381,
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

### استطلاع الرسائل الجديدة (Long-Polling)
- **URL**: `/api/lp-chat/poll`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Request Body**:
```json
{
  "chat_id": 1,
  "last_timestamp": 1714384381
}
```
- **Response (200 OK) - عندما توجد رسائل جديدة**:
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "id": "3",
        "message": "كيف يمكنني مساعدتك؟",
        "message_type": "text",
        "sender_type": "admin",
        "sender": {
          "id": "2",
          "name": "مدير الدعم",
          "full_name": "أحمد علي"
        },
        "attachment_url": null,
        "attachment_name": null,
        "created_at": "2024-05-29T14:30:05Z",
        "timestamp": 1714384385,
        "formatted_time": "14:30",
        "is_read": false
      }
    ],
    "last_message_timestamp": 1714384385
  }
}
```

- **Response (200 OK) - عندما لا توجد رسائل جديدة**:
```json
{
  "success": true,
  "data": {
    "messages": [],
    "last_message_timestamp": 1714384381
  }
}
```

## تفاصيل تقنية Long-Polling

### كيفية عمل Long-Polling:
1. يرسل التطبيق طلبًا إلى `/api/lp-chat/poll` مع آخر timestamp تم استلامه
2. يحتفظ الخادم بالاتصال مفتوحًا لمدة تصل إلى 20 ثانية
3. إذا وصلت رسائل جديدة خلال هذه الفترة، يتم إرجاعها فورًا
4. إذا لم تصل أي رسائل جديدة خلال 20 ثانية، يتم إرجاع استجابة فارغة
5. يقوم التطبيق بإرسال طلب جديد فورًا بعد استلام الاستجابة

### مميزات هذه الطريقة:
1. لا تحتاج إلى خدمات خارجية مثل Pusher أو Firebase
2. لا تحتاج إلى توكين أو مفاتيح خاصة
3. تعمل على أي خادم ويب بدون إعدادات إضافية
4. سهلة التنفيذ في Flutter وجميع تطبيقات الموبايل

### تنفيذ Flutter للشات بتقنية Long-Polling:

```dart
class ChatService {
  final String baseUrl;
  final String token;
  bool isPolling = false;
  int lastTimestamp = 0;
  
  ChatService({required this.baseUrl, required this.token});
  
  // بدء استطلاع الرسائل
  Future<void> startPolling(int chatId, Function(List<Message>) onNewMessages) async {
    if (isPolling) return;
    isPolling = true;
    
    while (isPolling) {
      try {
        final response = await http.post(
          Uri.parse('$baseUrl/api/lp-chat/poll'),
          headers: {
            'Authorization': 'Bearer $token',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: jsonEncode({
            'chat_id': chatId,
            'last_timestamp': lastTimestamp
          }),
        );
        
        if (response.statusCode == 200) {
          final data = jsonDecode(response.body);
          if (data['success']) {
            final messagesJson = data['data']['messages'] as List<dynamic>;
            if (messagesJson.isNotEmpty) {
              final messages = messagesJson.map((m) => Message.fromJson(m)).toList();
              onNewMessages(messages);
            }
            
            // تحديث آخر timestamp
            lastTimestamp = data['data']['last_message_timestamp'];
          }
        }
      } catch (e) {
        print('Polling error: $e');
        // انتظر قبل المحاولة مرة أخرى في حالة الخطأ
        await Future.delayed(Duration(seconds: 5));
      }
    }
  }
  
  // إيقاف استطلاع الرسائل
  void stopPolling() {
    isPolling = false;
  }
}
```

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
    "last_timestamp": [
      "The last timestamp field is required."
    ]
  }
}
```
