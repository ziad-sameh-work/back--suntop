<div class="firebase-chat-interface" x-data="firebaseChatAdmin({{ $chat->id }})" x-init="initializeChat()">
    <!-- Chat Header with Firebase Status -->
    <div class="chat-header">
        <div class="chat-title">
            <h3>{{ $chat->subject }}</h3>
            <p>العميل: {{ $chat->customer->full_name ?? $chat->customer->name }}</p>
        </div>
        <div class="chat-status">
            <!-- Firebase Connection Status -->
            <div x-show="isConnected" class="status-indicator connected">
                <div class="status-dot"></div>
                <span>Firebase متصل</span>
            </div>
            <div x-show="!isConnected" class="status-indicator disconnected">
                <div class="status-dot"></div>
                <span>غير متصل</span>
            </div>
            
            <!-- Customer Online Status -->
            <div x-show="customerOnline" class="status-indicator customer-online">
                <div class="status-dot"></div>
                <span>العميل متصل</span>
            </div>
        </div>
    </div>

    <!-- Messages Container with Firebase Real-time -->
    <div class="messages-container" x-ref="messagesContainer">
        <!-- Loading indicator -->
        <div x-show="isLoading" class="loading-indicator">
            <div class="spinner"></div>
            <p>جاري التحميل...</p>
        </div>

        <!-- Messages from Firebase -->
        <template x-for="message in messages" :key="message.id">
            <div :class="`message ${message.sender_type}`">
                <div class="message-bubble">
                    <div class="message-header">
                        <span class="message-sender" x-text="message.sender_name || message.sender?.full_name || message.sender?.name"></span>
                        <span class="message-time" x-text="formatTime(message.created_at)"></span>
                    </div>
                    
                    <div x-show="message.message" class="message-content" x-text="message.message"></div>
                    
                    <!-- Attachment Display -->
                    <template x-if="message.attachment_url">
                        <div class="message-attachment">
                            <template x-if="message.message_type === 'image'">
                                <div class="attachment-image">
                                    <img :src="message.attachment_url" :alt="message.attachment_name" class="message-image">
                                </div>
                            </template>
                            <template x-if="message.message_type === 'file'">
                                <div class="attachment-file">
                                    <div class="attachment-icon">
                                        <i class="fas fa-file"></i>
                                    </div>
                                    <div class="attachment-info">
                                        <div class="attachment-name" x-text="message.attachment_name"></div>
                                        <div class="attachment-size">ملف</div>
                                    </div>
                                    <a :href="message.attachment_url" target="_blank" class="attachment-download">
                                        <i class="fas fa-download"></i> تحميل
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Typing Indicator -->
        <div x-show="customerTyping" class="typing-indicator">
            <div class="typing-bubble">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="typing-text">العميل يكتب...</div>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="messages.length === 0 && !isLoading" class="empty-messages">
            <i class="fas fa-comments"></i>
            <p>لا توجد رسائل حتى الآن</p>
            <p class="sub-text">ابدأ المحادثة مع العميل</p>
        </div>
    </div>

    <!-- Message Input with Firebase Integration -->
    <div class="message-input-container">
        <!-- Attachment Preview -->
        @if($attachment)
            <div class="attachment-preview">
                <div class="attachment-preview-item">
                    <div class="attachment-preview-icon">
                        @if(in_array($attachment->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']))
                            <i class="fas fa-image"></i>
                        @else
                            <i class="fas fa-file"></i>
                        @endif
                    </div>
                    <div class="attachment-preview-info">
                        <div class="attachment-preview-name">{{ $attachment->getClientOriginalName() }}</div>
                        <div class="attachment-preview-size">{{ number_format($attachment->getSize() / 1024, 2) }} KB</div>
                    </div>
                    <button type="button" wire:click="removeAttachment" class="attachment-remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="sendMessage" class="message-form" @submit="sendTypingIndicator(false)">
            <div class="message-input-wrapper">
                <!-- File Upload -->
                <label for="attachment" class="attachment-btn">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="attachment" wire:model="attachment" 
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt" style="display: none;">
                </label>

                <!-- Text Input -->
                <div class="text-input-wrapper">
                    <textarea wire:model.defer="newMessage" 
                              placeholder="اكتب رسالتك هنا..." 
                              class="message-textarea"
                              rows="1"
                              @input="sendTypingIndicator(true)"
                              @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); $wire.sendMessage(); sendTypingIndicator(false); }"
                              x-ref="messageInput"></textarea>
                    
                    <!-- Emoji Button -->
                    <button type="button" wire:click="toggleEmojiPicker" class="emoji-btn">
                        <i class="fas fa-smile"></i>
                    </button>
                </div>

                <!-- Send Button -->
                <button type="submit" 
                        class="send-btn" 
                        wire:loading.attr="disabled" 
                        wire:target="sendMessage"
                        :disabled="!$wire.newMessage?.trim() && !$wire.attachment">
                    <i class="fas fa-paper-plane" wire:loading.remove wire:target="sendMessage"></i>
                    <i class="fas fa-spinner fa-spin" wire:loading wire:target="sendMessage" style="display: none;"></i>
                </button>
            </div>
        </form>

        <!-- Emoji Picker -->
        @if($showEmojiPicker)
            <div class="emoji-picker">
                <div class="emoji-grid">
                    @php
                        $emojis = ['😀', '😃', '😄', '😁', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳'];
                    @endphp
                    @foreach($emojis as $emoji)
                        <button type="button" wire:click="addEmoji('{{ $emoji }}')" class="emoji-item">{{ $emoji }}</button>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Firebase Status -->
        <div class="firebase-status">
            <span x-show="isConnected" class="status-text connected">✓ الرسائل ستصل فوراً عبر Firebase</span>
            <span x-show="!isConnected" class="status-text disconnected">⚠ استخدام النظام العادي</span>
        </div>
    </div>
</div>

<!-- Firebase Chat JavaScript -->
<script>
function firebaseChatAdmin(chatId) {
    return {
        chatId: chatId,
        messages: @json($messages),
        isConnected: false,
        customerOnline: false,
        customerTyping: false,
        isLoading: false,
        firebaseEnabled: true,
        typingTimer: null,
        firebaseRef: null,
        
        initializeChat() {
            this.loadInitialMessages();
            this.initializeFirebase();
            this.scrollToBottom();
        },
        
        loadInitialMessages() {
            // تحويل رسائل Livewire إلى تنسيق Firebase
            this.messages = this.messages.map(msg => ({
                id: msg.id,
                message: msg.message,
                message_type: msg.message_type,
                sender_type: msg.sender_type,
                sender_name: msg.sender?.full_name || msg.sender?.name,
                sender_id: msg.sender?.id,
                attachment_url: msg.attachment_path ? `/storage/${msg.attachment_path}` : null,
                attachment_name: msg.attachment_name,
                created_at: msg.created_at,
                is_read: msg.is_read
            }));
        },
        
        async initializeFirebase() {
            try {
                const firebaseUrl = 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app';
                
                if (!firebaseUrl) {
                    console.warn('Firebase URL not configured');
                    this.firebaseEnabled = false;
                    return;
                }
                
                // تسجيل Admin Presence
                await this.registerAdminPresence();
                
                // بدء الاستماع للأحداث
                this.listenToMessages();
                this.listenToCustomerPresence();
                this.listenToTypingIndicator();
                
                this.isConnected = true;
                console.log('Firebase admin chat initialized successfully');
                
                // إشعار العميل أن الأدمن متصل
                this.notifyCustomerAdminOnline();
                
            } catch (error) {
                console.error('Firebase initialization failed:', error);
                this.isConnected = false;
                this.firebaseEnabled = false;
            }
        },
        
        async registerAdminPresence() {
            // تسجيل حضور الأدمن في Firebase
            try {
                const adminData = {
                    id: {{ Auth::id() }},
                    name: '{{ Auth::user()->full_name ?? Auth::user()->name }}',
                    isOnline: true,
                    lastSeen: Date.now(),
                    chatId: this.chatId
                };
                
                await this.sendToFirebase(`/admin_presence/{{ Auth::id() }}`, adminData);
                
                // تحديث الحضور كل 30 ثانية
                setInterval(() => {
                    this.sendToFirebase(`/admin_presence/{{ Auth::id() }}/lastSeen`, Date.now());
                }, 30000);
                
            } catch (error) {
                console.error('Failed to register admin presence:', error);
            }
        },
        
        listenToMessages() {
            // محاكاة الاستماع لرسائل Firebase جديدة
            // في التطبيق الحقيقي ستستخدم Firebase SDK
            
            setInterval(() => {
                // محاكاة وصول رسالة جديدة من العميل
                if (Math.random() > 0.95) {
                    const newMessage = {
                        id: Date.now(),
                        message: 'رسالة تجريبية من العميل',
                        message_type: 'text',
                        sender_type: 'customer',
                        sender_name: '{{ $chat->customer->full_name ?? $chat->customer->name }}',
                        sender_id: {{ $chat->customer_id }},
                        attachment_url: null,
                        attachment_name: null,
                        created_at: new Date().toISOString(),
                        is_read: false
                    };
                    
                    this.addMessage(newMessage);
                }
            }, 10000);
        },
        
        listenToCustomerPresence() {
            // محاكاة حالة اتصال العميل
            setInterval(() => {
                this.customerOnline = Math.random() > 0.4;
            }, 15000);
        },
        
        listenToTypingIndicator() {
            // محاكاة مؤشر كتابة العميل
            setInterval(() => {
                if (Math.random() > 0.9) {
                    this.customerTyping = true;
                    setTimeout(() => {
                        this.customerTyping = false;
                    }, 3000);
                }
            }, 20000);
        },
        
        addMessage(message) {
            // إضافة رسالة جديدة
            this.messages.push(message);
            this.$nextTick(() => {
                this.scrollToBottom();
            });
            
            // تشغيل صوت إشعار
            this.playNotificationSound();
            
            // إشعار بصري
            this.showNotification(message);
        },
        
        sendTypingIndicator(isTyping) {
            clearTimeout(this.typingTimer);
            
            if (isTyping) {
                // إرسال إشارة أن الأدمن يكتب
                this.sendToFirebase(`/chats/${this.chatId}/admin_typing`, {
                    isTyping: true,
                    adminId: {{ Auth::id() }},
                    adminName: '{{ Auth::user()->full_name ?? Auth::user()->name }}',
                    timestamp: Date.now()
                });
                
                // إيقاف مؤشر الكتابة بعد 3 ثواني
                this.typingTimer = setTimeout(() => {
                    this.sendToFirebase(`/chats/${this.chatId}/admin_typing`, {
                        isTyping: false,
                        timestamp: Date.now()
                    });
                }, 3000);
            } else {
                this.sendToFirebase(`/chats/${this.chatId}/admin_typing`, {
                    isTyping: false,
                    timestamp: Date.now()
                });
            }
        },
        
        notifyCustomerAdminOnline() {
            // إشعار العميل أن الأدمن متصل
            this.sendToFirebase(`/customer_notifications/${this.chatId}/admin_online`, {
                type: 'admin_online',
                adminName: '{{ Auth::user()->full_name ?? Auth::user()->name }}',
                message: 'مندوب الدعم متصل الآن',
                timestamp: Date.now()
            });
        },
        
        sendToFirebase(path, data) {
            // محاكاة إرسال البيانات إلى Firebase
            console.log('Sending to Firebase:', path, data);
            
            // في التطبيق الحقيقي، ستستخدم:
            // return fetch(`https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app${path}.json`, {
            //     method: 'PUT',
            //     body: JSON.stringify(data)
            // });
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },
        
        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('ar-EG', {
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        playNotificationSound() {
            try {
                const audio = new Audio('/sounds/notification.mp3');
                audio.volume = 0.3;
                audio.play().catch(() => {});
            } catch (error) {}
        },
        
        showNotification(message) {
            // إشعار بصري مؤقت
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('رسالة جديدة من العميل', {
                    body: message.message.substring(0, 50),
                    icon: '/images/chat-icon.png'
                });
            }
        }
    }
}

// تحديث عند إرسال رسالة من Livewire
document.addEventListener('livewire:load', function () {
    Livewire.on('messageAdded', function (messageId) {
        // يمكن إضافة منطق إضافي هنا
        console.log('Message added:', messageId);
    });
    
    // طلب إذن الإشعارات
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});
</script>

<!-- Styles for Firebase Chat -->
<style>
.firebase-chat-interface {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

/* Chat Header */
.chat-header {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-title h3 {
    margin: 0 0 5px 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.chat-title p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

.chat-status {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    background: rgba(255, 255, 255, 0.15);
    padding: 4px 8px;
    border-radius: 12px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.status-indicator.connected .status-dot {
    background: #10b981;
}

.status-indicator.disconnected .status-dot {
    background: #ef4444;
}

.status-indicator.customer-online .status-dot {
    background: #3b82f6;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Messages Container */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
    max-height: calc(100vh - 350px);
}

.loading-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: #7f8c8d;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e9ecef;
    border-top: 4px solid #FF6B35;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.message {
    display: flex;
    margin-bottom: 20px;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message.admin {
    justify-content: flex-end;
}

.message.customer {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 70%;
    padding: 15px 20px;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    word-wrap: break-word;
}

.message.customer .message-bubble {
    background: white;
    color: #2c3e50;
    border-bottom-left-radius: 5px;
    border: 1px solid #e9ecef;
}

.message.admin .message-bubble {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    border-bottom-right-radius: 5px;
}

.message-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.8rem;
    opacity: 0.8;
}

.message-sender {
    font-weight: 600;
}

.message-time {
    font-size: 0.75rem;
}

.message-content {
    line-height: 1.5;
    word-wrap: break-word;
    white-space: pre-wrap;
}

/* Attachment Styles */
.message-attachment {
    margin-top: 10px;
}

.attachment-image img {
    max-width: 100%;
    border-radius: 10px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.attachment-image img:hover {
    transform: scale(1.02);
}

.attachment-file {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.attachment-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.attachment-info {
    flex: 1;
}

.attachment-name {
    font-weight: 600;
    margin-bottom: 2px;
}

.attachment-size {
    font-size: 0.8rem;
    opacity: 0.8;
}

.attachment-download {
    color: inherit;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.2);
    font-size: 0.8rem;
    transition: background 0.3s ease;
}

.attachment-download:hover {
    background: rgba(255, 255, 255, 0.3);
    color: inherit;
    text-decoration: none;
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 20px;
    animation: fadeIn 0.3s ease-out;
}

.typing-bubble {
    background: white;
    border: 1px solid #e9ecef;
    padding: 15px 20px;
    border-radius: 20px;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.typing-dots {
    display: flex;
    gap: 4px;
    margin-bottom: 5px;
}

.typing-dots span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #7f8c8d;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(1) { animation-delay: 0s; }
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.5;
    }
    30% {
        transform: translateY(-10px);
        opacity: 1;
    }
}

.typing-text {
    font-size: 0.8rem;
    color: #7f8c8d;
    font-style: italic;
}

/* Empty State */
.empty-messages {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.empty-messages i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
    color: #FF6B35;
}

.empty-messages p {
    font-size: 1.1rem;
    margin: 10px 0;
}

.empty-messages .sub-text {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Message Input */
.message-input-container {
    background: white;
    border-top: 1px solid #e9ecef;
    padding: 20px;
}

.message-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.attachment-preview {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 15px;
}

.attachment-preview-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.attachment-preview-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #FF6B35;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.attachment-preview-info {
    flex: 1;
}

.attachment-preview-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
}

.attachment-preview-size {
    color: #7f8c8d;
    font-size: 0.9rem;
}

.attachment-remove {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.attachment-remove:hover {
    background: #c82333;
    transform: scale(1.1);
}

.message-input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    padding: 8px;
    transition: all 0.3s ease;
}

.message-input-wrapper:focus-within {
    border-color: #FF6B35;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}

.attachment-btn {
    background: transparent;
    border: none;
    color: #7f8c8d;
    cursor: pointer;
    padding: 10px;
    border-radius: 50%;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
}

.attachment-btn:hover {
    background: #e9ecef;
    color: #FF6B35;
}

.text-input-wrapper {
    flex: 1;
    display: flex;
    align-items: flex-end;
    background: white;
    border-radius: 20px;
    padding: 8px 12px;
    min-height: 40px;
}

.message-textarea {
    flex: 1;
    border: none;
    outline: none;
    resize: none;
    font-size: 14px;
    line-height: 1.5;
    padding: 6px 0;
    background: transparent;
    color: #2c3e50;
    max-height: 120px;
    min-height: 24px;
    overflow-y: hidden;
}

.message-textarea::placeholder {
    color: #adb5bd;
}

.emoji-btn {
    background: transparent;
    border: none;
    color: #7f8c8d;
    cursor: pointer;
    padding: 6px;
    border-radius: 50%;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    margin-left: 5px;
}

.emoji-btn:hover {
    color: #FF6B35;
    background: #f8f9fa;
}

.send-btn {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    border: none;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
    box-shadow: 0 2px 10px rgba(255, 107, 53, 0.3);
}

.send-btn:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.4);
}

.send-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: 0 2px 10px rgba(255, 107, 53, 0.3);
}

/* Emoji Picker */
.emoji-picker {
    position: absolute;
    bottom: 100%;
    right: 0;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 15px;
    padding: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    margin-bottom: 10px;
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
    max-width: 240px;
}

.emoji-item {
    background: transparent;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.emoji-item:hover {
    background: #f8f9fa;
}

/* Firebase Status */
.firebase-status {
    margin-top: 8px;
    text-align: center;
}

.status-text {
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 12px;
    display: inline-block;
}

.status-text.connected {
    color: #10b981;
    background: #ecfdf5;
}

.status-text.disconnected {
    color: #f59e0b;
    background: #fffbeb;
}

/* Scrollbar Styling */
.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive */
@media (max-width: 768px) {
    .message-bubble {
        max-width: 85%;
    }
    
    .messages-container {
        padding: 15px;
    }
    
    .message-input-container {
        padding: 15px;
    }
    
    .chat-header {
        padding: 15px;
    }
    
    .chat-status {
        align-items: center;
    }
    
    .emoji-picker {
        right: 10px;
        max-width: calc(100vw - 40px);
    }
    
    .emoji-grid {
        max-width: 100%;
    }
}

/* RTL Support */
.firebase-chat-interface {
    direction: rtl;
    text-align: right;
}
</style>
