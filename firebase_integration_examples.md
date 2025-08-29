-# 🔥 Firebase Real-Time Chat Integration Examples

## ⚙️ إعداد Firebase في التطبيق

### 1. إضافة Firebase URL إلى .env
```env
FIREBASE_DATABASE_URL=https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app
```

## 📱 Flutter Integration

### 1. إضافة Dependencies
```yaml
dependencies:
  firebase_database: ^10.4.0
  firebase_core: ^2.24.2
```

### 2. Firebase Service في Flutter
```dart
import 'package:firebase_database/firebase_database.dart';

class FirebaseChatService {
  static final FirebaseDatabase _database = FirebaseDatabase.instanceFor(
    app: Firebase.app(),
    databaseURL: 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app',
  );

  // استمع للرسائل الجديدة
  static Stream<DatabaseEvent> listenToMessages(String chatId) {
    return _database.ref('chats/$chatId/messages').onValue;
  }

  // استمع لتحديثات معلومات الشات
  static Stream<DatabaseEvent> listenToChatInfo(String chatId) {
    return _database.ref('chats/$chatId/info').onValue;
  }

  // استمع للإشعارات
  static Stream<DatabaseEvent> listenToNotifications(String customerId) {
    return _database.ref('customer_notifications/$customerId').onValue;
  }
}
```

### 3. استخدام في Flutter Widget
```dart
class ChatScreen extends StatefulWidget {
  final String chatId;
  final String customerId;

  const ChatScreen({required this.chatId, required this.customerId});

  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  late StreamSubscription<DatabaseEvent> _messagesSubscription;
  late StreamSubscription<DatabaseEvent> _notificationsSubscription;
  List<Message> messages = [];

  @override
  void initState() {
    super.initState();
    _listenToMessages();
    _listenToNotifications();
  }

  void _listenToMessages() {
    _messagesSubscription = FirebaseChatService
        .listenToMessages(widget.chatId)
        .listen((DatabaseEvent event) {
      if (event.snapshot.value != null) {
        final data = event.snapshot.value as Map<dynamic, dynamic>;
        setState(() {
          messages = data.entries
              .map((entry) => Message.fromMap(entry.value))
              .toList()
            ..sort((a, b) => a.timestamp.compareTo(b.timestamp));
        });
      }
    });
  }

  void _listenToNotifications() {
    _notificationsSubscription = FirebaseChatService
        .listenToNotifications(widget.customerId)
        .listen((DatabaseEvent event) {
      if (event.snapshot.value != null) {
        // معالجة الإشعارات الجديدة
        _handleNewNotification(event.snapshot.value);
      }
    });
  }

  @override
  void dispose() {
    _messagesSubscription.cancel();
    _notificationsSubscription.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('الدعم الفني')),
      body: Column(
        children: [
          Expanded(
            child: ListView.builder(
              itemCount: messages.length,
              itemBuilder: (context, index) {
                return MessageWidget(message: messages[index]);
              },
            ),
          ),
          MessageInput(onSend: _sendMessage),
        ],
      ),
    );
  }

  void _sendMessage(String message) async {
    // إرسال الرسالة عبر API
    await ChatAPI.sendMessage(widget.chatId, message);
    // Firebase سيتم تحديثه تلقائياً من الباك إند
  }
}
```

## 🌐 JavaScript/Web Integration

### 1. إعداد Firebase في JavaScript
```javascript
import { initializeApp } from 'firebase/app';
import { getDatabase, ref, onValue, off } from 'firebase/database';

const firebaseConfig = {
  databaseURL: 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app'
};

const app = initializeApp(firebaseConfig);
const database = getDatabase(app);

class FirebaseChatService {
  static listenToMessages(chatId, callback) {
    const messagesRef = ref(database, `chats/${chatId}/messages`);
    onValue(messagesRef, callback);
    return () => off(messagesRef, callback);
  }

  static listenToNotifications(customerId, callback) {
    const notificationsRef = ref(database, `customer_notifications/${customerId}`);
    onValue(notificationsRef, callback);
    return () => off(notificationsRef, callback);
  }
}
```

### 2. React Component Example
```jsx
import React, { useState, useEffect } from 'react';
import { FirebaseChatService } from './firebase-service';

function ChatComponent({ chatId, customerId }) {
  const [messages, setMessages] = useState([]);
  const [notifications, setNotifications] = useState([]);

  useEffect(() => {
    // استمع للرسائل
    const unsubscribeMessages = FirebaseChatService.listenToMessages(
      chatId,
      (snapshot) => {
        if (snapshot.val()) {
          const messagesData = Object.values(snapshot.val());
          setMessages(messagesData.sort((a, b) => a.timestamp - b.timestamp));
        }
      }
    );

    // استمع للإشعارات
    const unsubscribeNotifications = FirebaseChatService.listenToNotifications(
      customerId,
      (snapshot) => {
        if (snapshot.val()) {
          setNotifications(Object.values(snapshot.val()));
        }
      }
    );

    return () => {
      unsubscribeMessages();
      unsubscribeNotifications();
    };
  }, [chatId, customerId]);

  return (
    <div className="chat-container">
      <div className="messages">
        {messages.map(message => (
          <div key={message.id} className={`message ${message.sender_type}`}>
            <strong>{message.sender_name}:</strong> {message.message}
            <span className="time">{new Date(message.created_at).toLocaleTimeString()}</span>
          </div>
        ))}
      </div>
      
      {notifications.length > 0 && (
        <div className="notifications">
          {notifications.map(notification => (
            <div key={notification.id} className="notification">
              {notification.message}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
```

## 🛠️ Admin Panel Integration

### JavaScript للوحة الإدارة
```javascript
class AdminFirebaseService {
  static listenToAdminNotifications(callback) {
    const notificationsRef = ref(database, 'admin_notifications');
    onValue(notificationsRef, callback);
    return () => off(notificationsRef, callback);
  }

  static listenToAllChats(callback) {
    const chatsRef = ref(database, 'chats');
    onValue(chatsRef, callback);
    return () => off(chatsRef, callback);
  }
}

// في صفحة الإدارة
document.addEventListener('DOMContentLoaded', function() {
  // استمع للإشعارات الجديدة
  AdminFirebaseService.listenToAdminNotifications((snapshot) => {
    if (snapshot.val()) {
      const notifications = Object.values(snapshot.val());
      updateAdminNotifications(notifications);
    }
  });
});
```

## 🧪 Testing APIs

### 1. اختبار الاتصال مع Firebase
```bash
GET http://127.0.0.1:8000/api/firebase-chat/test-connection
Authorization: Bearer YOUR_TOKEN
```

### 2. بدء شات جديد
```bash
POST http://127.0.0.1:8000/api/firebase-chat/start
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "subject": "استفسار عام",
    "priority": "medium"
}
```

### 3. إرسال رسالة
```bash
POST http://127.0.0.1:8000/api/firebase-chat/send
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "chat_id": "1",
    "message": "مرحباً، أحتاج مساعدة"
}
```

## 🔑 المتطلبات والكرادينشيالز

### ما نحتاجه حالياً:
1. ✅ Firebase Database URL (متوفر)
2. 🔄 Firebase Project Configuration (اختياري للمزيد من الأمان)
3. 🔄 Firebase Service Account Key (للعمليات المتقدمة)

### للحصول على أمان أكثر (اختياري):
1. **Firebase Project Config**: من Firebase Console > Project Settings
2. **Service Account Key**: من Firebase Console > Project Settings > Service Accounts

### بدون كرادينشيالز إضافية:
النظام الحالي يعمل بـ Database URL فقط ولكن بدون أمان إضافي، وهو مناسب للتطوير والاختبار.
