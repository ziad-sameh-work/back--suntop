<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🔥 لوحة الشات المباشر - Firebase Real-Time</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            overflow-y: auto;
        }
        
        .chat-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .chat-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .chat-item.active {
            background: rgba(255, 255, 255, 0.3);
            border-color: #fff;
        }
        
        .chat-item.unread {
            border-left: 4px solid #ff6b6b;
        }
        
        .chat-container {
            height: 60vh;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 15px;
            padding: 20px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 15px;
            max-width: 80%;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .message.customer {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            margin-left: auto;
            text-align: right;
        }
        
        .message.admin {
            background: linear-gradient(135deg, #f1f8e9 0%, #c8e6c9 100%);
            margin-right: auto;
            text-align: left;
        }
        
        .message-header {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        .message-time {
            font-size: 0.85em;
            color: #666;
            margin-top: 8px;
        }
        
        .notification {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffd93d;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            animation: bounce 0.5s ease;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .firebase-status {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 12px 20px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .firebase-connected {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .firebase-disconnected {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .typing-indicator {
            background: rgba(108, 117, 125, 0.1);
            padding: 10px;
            border-radius: 10px;
            margin: 10px 0;
            font-style: italic;
            color: #6c757d;
        }
        
        .online-users {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: bold;
        }
        
        .priority-low { background: #d1ecf1; color: #0c5460; }
        .priority-medium { background: #fff3cd; color: #856404; }
        .priority-high { background: #f8d7da; color: #721c24; }
        .priority-urgent { background: #dc3545; color: white; }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: bold;
        }
        
        .status-open { background: #d4edda; color: #155724; }
        .status-in_progress { background: #fff3cd; color: #856404; }
        .status-resolved { background: #cce5ff; color: #004085; }
        .status-closed { background: #f8f9fa; color: #6c757d; }
        
        .message-input {
            background: white;
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .message-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .send-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .sound-toggle {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 20px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <!-- Firebase Status Indicator -->
    <div id="firebase-status" class="firebase-status firebase-disconnected">
        <i class="fas fa-circle" id="status-icon"></i>
        <span id="status-text">Firebase: جاري الاتصال...</span>
    </div>

    <!-- Sound Toggle -->
    <button class="sound-toggle" onclick="toggleSound()" id="sound-toggle">
        <i class="fas fa-volume-up" id="sound-icon"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar - Chat List -->
            <div class="col-md-4 sidebar">
                <h4 class="mb-4">
                    <i class="fas fa-comments"></i>
                    المحادثات المباشرة
                </h4>
                
                <!-- Live Stats -->
                <div class="stats-card">
                    <div class="row text-center">
                        <div class="col-3">
                            <h5 id="total-chats">0</h5>
                            <small>إجمالي</small>
                        </div>
                        <div class="col-3">
                            <h5 id="open-chats">0</h5>
                            <small>مفتوحة</small>
                        </div>
                        <div class="col-3">
                            <h5 id="unread-messages">0</h5>
                            <small>غير مقروءة</small>
                        </div>
                        <div class="col-3">
                            <h5 id="online-admins">1</h5>
                            <small>أونلاين</small>
                        </div>
                    </div>
                </div>

                <!-- Chat List -->
                <div id="chat-list">
                    <div class="text-center text-white-50">
                        <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                        <p>جاري تحميل المحادثات...</p>
                    </div>
                </div>

                <!-- Online Admins -->
                <div class="online-users">
                    <h6><i class="fas fa-users"></i> الإداريين المتصلين</h6>
                    <div id="online-admins-list">
                        <!-- سيتم ملؤها ديناميكياً -->
                    </div>
                </div>

                <!-- Live Notifications -->
                <div class="mt-4">
                    <h6><i class="fas fa-bell"></i> الإشعارات المباشرة</h6>
                    <div id="live-notifications" style="max-height: 200px; overflow-y: auto;">
                        <!-- الإشعارات ستظهر هنا -->
                    </div>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="col-md-8 p-4">
                <!-- Chat Header -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 id="chat-title">
                                <i class="fas fa-comment-dots"></i>
                                اختر محادثة للبدء
                            </h5>
                            <div id="chat-customer-info" style="display: none;">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i>
                                    <span id="customer-name"></span> |
                                    <i class="fas fa-clock"></i>
                                    <span id="chat-created-time"></span>
                                </small>
                            </div>
                        </div>
                        
                        <div id="chat-controls" style="display: none;">
                            <div class="d-flex gap-2 align-items-center">
                                <select class="form-select form-select-sm" id="chat-status" onchange="updateChatStatus()">
                                    <option value="open">مفتوح</option>
                                    <option value="in_progress">قيد المعالجة</option>
                                    <option value="resolved">تم الحل</option>
                                    <option value="closed">مغلق</option>
                                </select>
                                
                                <select class="form-select form-select-sm" id="chat-priority" onchange="updateChatPriority()">
                                    <option value="low">منخفضة</option>
                                    <option value="medium">متوسطة</option>
                                    <option value="high">عالية</option>
                                    <option value="urgent">عاجل</option>
                                </select>
                                
                                <select class="form-select form-select-sm" id="assign-admin" onchange="assignChat()">
                                    <option value="">تعيين إداري</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div id="typing-indicator" class="typing-indicator" style="display: none;">
                    <i class="fas fa-ellipsis-h"></i>
                    <span id="typing-text">العميل يكتب...</span>
                </div>

                <!-- Chat Messages -->
                <div id="chat-messages" class="chat-container">
                    <div class="text-center text-muted">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>اختر محادثة لعرض الرسائل</p>
                    </div>
                </div>

                <!-- Message Input -->
                <div id="message-input-area" style="display: none;">
                    <div class="d-flex gap-3 mt-3">
                        <input type="text" class="form-control message-input" id="message-input" 
                               placeholder="اكتب رسالتك هنا..." onkeypress="handleEnterKey(event)">
                        <button class="btn send-btn" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                            إرسال
                        </button>
                    </div>
                    
                    <!-- Quick Replies -->
                    <div class="mt-2">
                        <small class="text-muted">ردود سريعة:</small>
                        <div class="d-flex gap-2 mt-1">
                            <button class="btn btn-outline-secondary btn-sm" onclick="insertQuickReply('شكراً لتواصلك معنا')">
                                شكراً لتواصلك
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="insertQuickReply('سيتم حل مشكلتك قريباً')">
                                سيتم الحل قريباً
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="insertQuickReply('هل يمكنك توضيح أكثر؟')">
                                توضيح أكثر؟
                            </button>
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

    <!-- Admin Firebase Chat System -->
    <script src="{{ asset('js/admin-firebase-chat.js') }}"></script>

    <script>
        // Simple functions for compatibility with the new system
        function sendMessage() {
            if (adminChat) {
                adminChat.sendMessage();
            }
        }

        function handleEnterKey(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        function insertQuickReply(text) {
            document.getElementById('message-input').value = text;
        }

        function toggleSound() {
            if (adminChat) {
                adminChat.config.soundEnabled = !adminChat.config.soundEnabled;
                const icon = document.getElementById('sound-icon');
                icon.className = adminChat.config.soundEnabled ? 'fas fa-volume-up' : 'fas fa-volume-mute';
            }
        }

        function updateChatSettings() {
            // Will be handled by adminChat.updateChatStatus() and adminChat.updateChatPriority()
            console.log('Updating chat settings...');
        }
    </script>
</body>
</html>
