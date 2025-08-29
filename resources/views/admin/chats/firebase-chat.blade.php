<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إدارة الشات المباشر - Firebase</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .chat-container {
            height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f8f9fa;
        }
        
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }
        
        .message.customer {
            background-color: #e3f2fd;
            margin-left: auto;
            text-align: right;
        }
        
        .message.admin {
            background-color: #f1f8e9;
            margin-right: auto;
            text-align: left;
        }
        
        .message-time {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
        
        .notification {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        
        .chat-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .firebase-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            z-index: 1000;
        }
        
        .firebase-connected {
            background-color: #28a745;
        }
        
        .firebase-disconnected {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- Firebase Status Indicator -->
        <div id="firebase-status" class="firebase-status firebase-disconnected">
            🔥 Firebase: غير متصل
        </div>

        <div class="row">
            <!-- Chat List Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>📋 قائمة المحادثات</h5>
                        <div id="chat-stats" class="chat-stats mt-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h6 id="total-chats">0</h6>
                                    <small>إجمالي</small>
                                </div>
                                <div class="col-4">
                                    <h6 id="open-chats">0</h6>
                                    <small>مفتوحة</small>
                                </div>
                                <div class="col-4">
                                    <h6 id="unread-messages">0</h6>
                                    <small>غير مقروءة</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chat-list">
                            <!-- قائمة المحادثات ستظهر هنا -->
                        </div>
                    </div>
                </div>

                <!-- Notifications Panel -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6>🔔 الإشعارات المباشرة</h6>
                    </div>
                    <div class="card-body">
                        <div id="admin-notifications" style="max-height: 200px; overflow-y: auto;">
                            <!-- الإشعارات ستظهر هنا -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 id="chat-title">اختر محادثة للبدء</h5>
                        <div id="chat-actions" style="display: none;">
                            <select class="form-select form-select-sm d-inline-block w-auto me-2" id="chat-status">
                                <option value="open">مفتوح</option>
                                <option value="in_progress">قيد المعالجة</option>
                                <option value="resolved">تم الحل</option>
                                <option value="closed">مغلق</option>
                            </select>
                            <select class="form-select form-select-sm d-inline-block w-auto me-2" id="chat-priority">
                                <option value="low">منخفضة</option>
                                <option value="medium">متوسطة</option>
                                <option value="high">عالية</option>
                                <option value="urgent">عاجل</option>
                            </select>
                            <button class="btn btn-sm btn-primary" onclick="updateChatSettings()">تحديث</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chat Messages -->
                        <div id="chat-messages" class="chat-container">
                            <div class="text-center text-muted">
                                اختر محادثة لعرض الرسائل
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div id="message-input-area" style="display: none;">
                            <div class="input-group mt-3">
                                <input type="text" class="form-control" id="message-input" 
                                       placeholder="اكتب رسالتك هنا..." onkeypress="handleEnterKey(event)">
                                <button class="btn btn-primary" onclick="sendMessage()">
                                    📤 إرسال
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // إعداد Firebase
        const firebaseConfig = {
            databaseURL: 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app'
        };

        firebase.initializeApp(firebaseConfig);
        const database = firebase.database();

        // متغيرات عامة
        let currentChatId = null;
        let activeListeners = [];
        let adminNotificationListener = null;

        // بدء التطبيق
        document.addEventListener('DOMContentLoaded', function() {
            initializeFirebase();
            loadChatStats();
            listenToAdminNotifications();
        });

        // تهيئة Firebase وفحص الاتصال
        function initializeFirebase() {
            database.ref('test').set({
                timestamp: Date.now(),
                message: 'Admin panel connection test'
            }).then(() => {
                updateFirebaseStatus(true);
                console.log('🔥 Firebase connected successfully');
            }).catch((error) => {
                updateFirebaseStatus(false);
                console.error('❌ Firebase connection failed:', error);
            });
        }

        // تحديث حالة Firebase
        function updateFirebaseStatus(connected) {
            const statusElement = document.getElementById('firebase-status');
            if (connected) {
                statusElement.className = 'firebase-status firebase-connected';
                statusElement.innerHTML = '🔥 Firebase: متصل';
            } else {
                statusElement.className = 'firebase-status firebase-disconnected';
                statusElement.innerHTML = '🔥 Firebase: غير متصل';
            }
        }

        // تحميل إحصائيات المحادثات
        function loadChatStats() {
            fetch('/admin/api/firebase-chat/stats', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('total-chats').textContent = data.data.total_chats;
                    document.getElementById('open-chats').textContent = data.data.open_chats;
                    document.getElementById('unread-messages').textContent = data.data.total_unread_messages;
                }
            })
            .catch(error => console.error('Error loading stats:', error));
        }

        // الاستماع للإشعارات الإدارية
        function listenToAdminNotifications() {
            if (adminNotificationListener) {
                adminNotificationListener.off();
            }

            adminNotificationListener = database.ref('admin_notifications');
            adminNotificationListener.limitToLast(10).on('child_added', function(snapshot) {
                const notification = snapshot.val();
                addNotificationToUI(notification);
            });
        }

        // إضافة إشعار إلى واجهة المستخدم
        function addNotificationToUI(notification) {
            const notificationsContainer = document.getElementById('admin-notifications');
            const notificationElement = document.createElement('div');
            notificationElement.className = 'notification';
            notificationElement.innerHTML = `
                <strong>${notification.type === 'new_chat' ? '💬 شات جديد' : '📨 رسالة جديدة'}</strong><br>
                ${notification.message}<br>
                <small>${new Date(notification.created_at).toLocaleTimeString('ar')}</small>
            `;
            
            notificationsContainer.insertBefore(notificationElement, notificationsContainer.firstChild);
            
            // إزالة الإشعارات القديمة
            if (notificationsContainer.children.length > 5) {
                notificationsContainer.removeChild(notificationsContainer.lastChild);
            }

            // تشغيل صوت إشعار
            playNotificationSound();
        }

        // تشغيل صوت الإشعار
        function playNotificationSound() {
            // يمكن إضافة ملف صوتي هنا
            console.log('🔔 New notification received');
        }

        // فتح محادثة
        function openChat(chatId) {
            currentChatId = chatId;
            
            // إيقاف الاستماع السابق
            stopCurrentListeners();
            
            // بدء الاستماع للرسائل الجديدة
            listenToChatMessages(chatId);
            
            // عرض منطقة الإدخال
            document.getElementById('message-input-area').style.display = 'block';
            document.getElementById('chat-actions').style.display = 'block';
            document.getElementById('chat-title').textContent = `محادثة #${chatId}`;
            
            // تحميل الرسائل من قاعدة البيانات
            loadChatMessages(chatId);
        }

        // الاستماع لرسائل المحادثة
        function listenToChatMessages(chatId) {
            const messagesRef = database.ref(`chats/${chatId}/messages`);
            const listener = messagesRef.on('child_added', function(snapshot) {
                const message = snapshot.val();
                addMessageToUI(message);
            });
            
            activeListeners.push({
                ref: messagesRef,
                listener: listener
            });
        }

        // إيقاف جميع المستمعين النشطين
        function stopCurrentListeners() {
            activeListeners.forEach(item => {
                item.ref.off('child_added', item.listener);
            });
            activeListeners = [];
        }

        // تحميل رسائل المحادثة
        function loadChatMessages(chatId) {
            const messagesContainer = document.getElementById('chat-messages');
            messagesContainer.innerHTML = '<div class="text-center">جاري تحميل الرسائل...</div>';
            
            fetch(`/api/firebase-chat/${chatId}/messages`, {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messagesContainer.innerHTML = '';
                    data.data.messages.forEach(message => {
                        addMessageToUI(message);
                    });
                    scrollToBottom();
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                messagesContainer.innerHTML = '<div class="text-center text-danger">خطأ في تحميل الرسائل</div>';
            });
        }

        // إضافة رسالة إلى واجهة المستخدم
        function addMessageToUI(message) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageElement = document.createElement('div');
            messageElement.className = `message ${message.sender_type}`;
            messageElement.innerHTML = `
                <div><strong>${message.sender_name}:</strong></div>
                <div>${message.message}</div>
                <div class="message-time">${new Date(message.created_at).toLocaleTimeString('ar')}</div>
            `;
            
            messagesContainer.appendChild(messageElement);
            scrollToBottom();
        }

        // التمرير إلى أسفل
        function scrollToBottom() {
            const messagesContainer = document.getElementById('chat-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // إرسال رسالة
        function sendMessage() {
            if (!currentChatId) {
                alert('يرجى اختيار محادثة أولاً');
                return;
            }

            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();
            
            if (!message) {
                alert('يرجى كتابة رسالة');
                return;
            }

            fetch('/admin/api/firebase-chat/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    chat_id: currentChatId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    console.log('✅ Message sent successfully');
                } else {
                    alert('فشل في إرسال الرسالة: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('خطأ في إرسال الرسالة');
            });
        }

        // معالجة مفتاح Enter
        function handleEnterKey(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        // تحديث إعدادات المحادثة
        function updateChatSettings() {
            if (!currentChatId) return;

            const status = document.getElementById('chat-status').value;
            const priority = document.getElementById('chat-priority').value;

            Promise.all([
                fetch(`/admin/api/firebase-chat/${currentChatId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: status })
                }),
                fetch(`/admin/api/firebase-chat/${currentChatId}/priority`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ priority: priority })
                })
            ])
            .then(responses => Promise.all(responses.map(r => r.json())))
            .then(results => {
                if (results.every(r => r.success)) {
                    alert('تم تحديث إعدادات المحادثة بنجاح');
                } else {
                    alert('فشل في تحديث بعض الإعدادات');
                }
            })
            .catch(error => {
                console.error('Error updating chat settings:', error);
                alert('خطأ في تحديث الإعدادات');
            });
        }

        // تنظيف المستمعين عند إغلاق الصفحة
        window.addEventListener('beforeunload', function() {
            stopCurrentListeners();
            if (adminNotificationListener) {
                adminNotificationListener.off();
            }
        });

        // مثال على قائمة المحادثات (يجب تحميلها من قاعدة البيانات)
        function loadChatList() {
            // هذا مجرد مثال - يجب تحميل القائمة الفعلية من الباك إند
            const chatList = document.getElementById('chat-list');
            chatList.innerHTML = `
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action" onclick="openChat(1)">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">محادثة #1</h6>
                            <small>منذ 3 دقائق</small>
                        </div>
                        <p class="mb-1">مرحباً، أحتاج مساعدة...</p>
                        <small>أحمد محمد</small>
                    </a>
                </div>
            `;
        }

        // تحميل قائمة المحادثات عند بدء الصفحة
        setTimeout(loadChatList, 1000);
    </script>
</body>
</html>
