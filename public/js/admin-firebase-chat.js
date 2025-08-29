/**
 * 🔥 Advanced Firebase Real-Time Admin Chat System
 * نظام الشات المباشر المتقدم للإدارة مع Firebase
 */

class AdminFirebaseChatSystem {
    constructor(config = {}) {
        this.config = {
            firebaseUrl: config.firebaseUrl || 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app',
            apiBaseUrl: config.apiBaseUrl || '/admin/api/firebase-chat',
            soundEnabled: config.soundEnabled !== false,
            autoScroll: config.autoScroll !== false,
            typingTimeout: config.typingTimeout || 3000,
            presenceTimeout: config.presenceTimeout || 30000,
            ...config
        };

        this.currentChatId = null;
        this.adminInfo = null;
        this.activeListeners = new Map();
        this.typingTimer = null;
        this.lastTypingTime = 0;
        this.isInitialized = false;
        
        this.initializeFirebase();
        this.setupEventListeners();
        this.registerAdminPresence();
    }

    /**
     * تهيئة Firebase
     */
    initializeFirebase() {
        if (typeof firebase === 'undefined') {
            console.error('❌ Firebase SDK not loaded');
            this.updateConnectionStatus('error', 'Firebase SDK غير محمل');
            return;
        }

        try {
            const firebaseConfig = {
                databaseURL: this.config.firebaseUrl
            };

            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            this.database = firebase.database();
            this.setupConnectionMonitoring();
            
            console.log('🔥 Firebase initialized successfully');
            this.isInitialized = true;
            
        } catch (error) {
            console.error('❌ Firebase initialization failed:', error);
            this.updateConnectionStatus('error', 'فشل في تهيئة Firebase');
        }
    }

    /**
     * مراقبة الاتصال مع Firebase
     */
    setupConnectionMonitoring() {
        this.database.ref('.info/connected').on('value', (snapshot) => {
            if (snapshot.val() === true) {
                this.updateConnectionStatus('connected', 'Firebase: متصل');
                this.onConnectionRestored();
            } else {
                this.updateConnectionStatus('disconnected', 'Firebase: منقطع');
            }
        });
    }

    /**
     * إعداد مستمعي الأحداث
     */
    setupEventListeners() {
        // إرسال رسالة عند الضغط على Enter
        document.addEventListener('keypress', (e) => {
            if (e.target.id === 'message-input' && e.key === 'Enter') {
                this.sendMessage();
            }
        });

        // Typing indicator عند الكتابة
        document.addEventListener('input', (e) => {
            if (e.target.id === 'message-input') {
                this.handleTyping();
            }
        });

        // تنظيف عند إغلاق الصفحة
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });

        // تحديث النشاط دورياً
        setInterval(() => {
            this.updateAdminActivity('active');
        }, 30000); // كل 30 ثانية
    }

    /**
     * تسجيل حضور الإداري
     */
    async registerAdminPresence() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/update-activity`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ activity: 'online' })
            });

            const data = await response.json();
            if (data.success) {
                console.log('✅ Admin presence registered');
                this.startRealTimeListeners();
            }
        } catch (error) {
            console.error('❌ Failed to register admin presence:', error);
        }
    }

    /**
     * بدء المستمعين المباشرين
     */
    startRealTimeListeners() {
        this.listenToAdminNotifications();
        this.listenToChats();
        this.listenToOnlineAdmins();
        this.updateChatStats();
        
        // تحديث دوري للإحصائيات
        setInterval(() => {
            this.updateChatStats();
        }, 10000); // كل 10 ثواني
    }

    /**
     * الاستماع للإشعارات الإدارية
     */
    listenToAdminNotifications() {
        const notificationsRef = this.database.ref('admin_notifications');
        
        notificationsRef.limitToLast(20).on('child_added', (snapshot) => {
            const notification = snapshot.val();
            if (this.isRecentNotification(notification.timestamp)) {
                this.displayNotification(notification);
                this.playNotificationSound();
            }
        });
        
        this.activeListeners.set('admin_notifications', notificationsRef);
    }

    /**
     * الاستماع للمحادثات
     */
    listenToChats() {
        const chatsRef = this.database.ref('chats');
        
        chatsRef.on('value', (snapshot) => {
            const chats = snapshot.val();
            this.updateChatsList(chats);
            this.updateLiveStats(chats);
        });
        
        this.activeListeners.set('chats', chatsRef);
    }

    /**
     * الاستماع للإداريين المتصلين
     */
    listenToOnlineAdmins() {
        const adminsRef = this.database.ref('admin_presence');
        
        adminsRef.on('value', (snapshot) => {
            const admins = snapshot.val();
            this.updateOnlineAdminsList(admins);
        });
        
        this.activeListeners.set('admin_presence', adminsRef);
    }

    /**
     * فتح محادثة
     */
    async openChat(chatId) {
        if (this.currentChatId === chatId) return;
        
        this.currentChatId = chatId;
        this.stopCurrentChatListeners();
        
        // تحديث واجهة المستخدم
        this.updateChatUI(chatId);
        
        // بدء الاستماع للرسائل الجديدة
        this.listenToChatMessages(chatId);
        this.listenToChatInfo(chatId);
        this.listenToTypingIndicator(chatId);
        
        // تحميل الرسائل من قاعدة البيانات
        await this.loadChatMessages(chatId);
        
        // تمييز كمقروء
        this.markChatAsRead(chatId);
        
        // تحديث النشاط
        this.updateAdminActivity(`viewing_chat_${chatId}`);
    }

    /**
     * الاستماع لرسائل المحادثة
     */
    listenToChatMessages(chatId) {
        const messagesRef = this.database.ref(`chats/${chatId}/messages`);
        
        messagesRef.on('child_added', (snapshot) => {
            const message = snapshot.val();
            if (this.isRecentMessage(message.timestamp)) {
                this.addMessageToUI(message, false);
                
                if (message.sender_type === 'customer') {
                    this.playNotificationSound();
                    this.showDesktopNotification(message);
                }
            }
        });
        
        this.activeListeners.set(`messages_${chatId}`, messagesRef);
    }

    /**
     * الاستماع لمعلومات المحادثة
     */
    listenToChatInfo(chatId) {
        const infoRef = this.database.ref(`chats/${chatId}/info`);
        
        infoRef.on('value', (snapshot) => {
            const info = snapshot.val();
            if (info) {
                this.updateChatHeader(info);
            }
        });
        
        this.activeListeners.set(`info_${chatId}`, infoRef);
    }

    /**
     * الاستماع لـ typing indicator
     */
    listenToTypingIndicator(chatId) {
        const typingRef = this.database.ref(`chats/${chatId}/typing`);
        
        typingRef.on('value', (snapshot) => {
            const typingData = snapshot.val();
            this.updateTypingIndicator(typingData);
        });
        
        this.activeListeners.set(`typing_${chatId}`, typingRef);
    }

    /**
     * إرسال رسالة
     */
    async sendMessage() {
        if (!this.currentChatId) {
            this.showAlert('يرجى اختيار محادثة أولاً', 'warning');
            return;
        }

        const messageInput = document.getElementById('message-input');
        const message = messageInput.value.trim();
        
        if (!message) {
            this.showAlert('يرجى كتابة رسالة', 'warning');
            return;
        }

        try {
            messageInput.disabled = true;
            
            // إيقاف typing indicator
            this.sendTypingIndicator(false);
            
            const response = await fetch(`${this.config.apiBaseUrl}/send-message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    chat_id: this.currentChatId,
                    message: message
                })
            });

            const data = await response.json();
            
            if (data.success) {
                messageInput.value = '';
                this.showAlert('تم إرسال الرسالة بنجاح', 'success');
            } else {
                this.showAlert('فشل في إرسال الرسالة: ' + data.message, 'danger');
            }
            
        } catch (error) {
            console.error('Error sending message:', error);
            this.showAlert('خطأ في إرسال الرسالة', 'danger');
        } finally {
            messageInput.disabled = false;
            messageInput.focus();
        }
    }

    /**
     * إرسال typing indicator
     */
    async sendTypingIndicator(isTyping) {
        if (!this.currentChatId) return;
        
        try {
            await fetch(`${this.config.apiBaseUrl}/typing-indicator`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    chat_id: this.currentChatId,
                    is_typing: isTyping
                })
            });
        } catch (error) {
            console.error('Error sending typing indicator:', error);
        }
    }

    /**
     * معالجة الكتابة
     */
    handleTyping() {
        const now = Date.now();
        
        // إرسال typing indicator إذا لم يتم إرساله مؤخراً
        if (now - this.lastTypingTime > 2000) {
            this.sendTypingIndicator(true);
            this.lastTypingTime = now;
        }
        
        // إلغاء timer السابق
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
        }
        
        // إيقاف typing indicator بعد فترة
        this.typingTimer = setTimeout(() => {
            this.sendTypingIndicator(false);
        }, this.config.typingTimeout);
    }

    /**
     * تحديث قائمة المحادثات
     */
    updateChatsList(chats) {
        const chatListContainer = document.getElementById('chat-list');
        
        if (!chats) {
            chatListContainer.innerHTML = '<div class="text-center text-white-50"><p>لا توجد محادثات</p></div>';
            return;
        }
        
        let chatItems = '';
        Object.keys(chats).forEach(chatId => {
            const chat = chats[chatId];
            if (chat.info) {
                const unreadClass = chat.info.admin_unread_count > 0 ? 'unread' : '';
                const activeClass = this.currentChatId === chatId ? 'active' : '';
                const priorityClass = `priority-${chat.info.priority}`;
                
                chatItems += `
                    <div class="chat-item ${unreadClass} ${activeClass} ${priorityClass}" onclick="adminChat.openChat('${chatId}')">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="user-avatar">
                                ${chat.info.customer_name.charAt(0)}
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-1">${chat.info.customer_name}</h6>
                                <small class="text-white-50">${chat.info.subject}</small>
                            </div>
                            ${chat.info.admin_unread_count > 0 ? `<span class="badge bg-danger">${chat.info.admin_unread_count}</span>` : ''}
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="status-badge status-${chat.info.status}">${this.getStatusText(chat.info.status)}</span>
                            <span class="priority-badge priority-${chat.info.priority}">${this.getPriorityText(chat.info.priority)}</span>
                        </div>
                        
                        ${chat.info.last_message ? `
                            <div class="mt-2">
                                <small class="text-white-50">
                                    ${chat.info.last_message.message.substring(0, 50)}${chat.info.last_message.message.length > 50 ? '...' : ''}
                                </small>
                                <br>
                                <small class="text-white-25">
                                    ${this.formatTimeAgo(chat.info.last_message.timestamp)}
                                </small>
                            </div>
                        ` : ''}
                    </div>
                `;
            }
        });
        
        chatListContainer.innerHTML = chatItems;
    }

    /**
     * تحديث الإحصائيات المباشرة
     */
    updateLiveStats(chats) {
        let totalChats = 0;
        let openChats = 0;
        let unreadMessages = 0;
        
        if (chats) {
            Object.keys(chats).forEach(chatId => {
                const chat = chats[chatId];
                if (chat.info) {
                    totalChats++;
                    if (chat.info.status === 'open' || chat.info.status === 'in_progress') {
                        openChats++;
                    }
                    unreadMessages += chat.info.admin_unread_count || 0;
                }
            });
        }
        
        this.updateStatElement('total-chats', totalChats);
        this.updateStatElement('open-chats', openChats);
        this.updateStatElement('unread-messages', unreadMessages);
    }

    /**
     * إضافة رسالة إلى واجهة المستخدم
     */
    addMessageToUI(message, isHistorical = false) {
        const messagesContainer = document.getElementById('chat-messages');
        const messageElement = document.createElement('div');
        messageElement.className = `message ${message.sender_type}`;
        messageElement.innerHTML = `
            <div class="message-header">
                <i class="fas fa-user"></i>
                <strong>${message.sender_name}</strong>
                ${message.sender_type === 'admin' ? '<i class="fas fa-shield-alt text-primary ms-1"></i>' : ''}
            </div>
            <div class="message-content">${this.formatMessageContent(message.message)}</div>
            <div class="message-time">
                <i class="fas fa-clock"></i>
                ${this.formatMessageTime(message.created_at)}
            </div>
        `;
        
        if (!isHistorical) {
            messageElement.style.opacity = '0';
            messagesContainer.appendChild(messageElement);
            
            // Animation
            setTimeout(() => {
                messageElement.style.transition = 'opacity 0.3s ease';
                messageElement.style.opacity = '1';
            }, 10);
            
            if (this.config.autoScroll) {
                this.scrollToBottom();
            }
        } else {
            messagesContainer.appendChild(messageElement);
        }
    }

    /**
     * تحديث typing indicator
     */
    updateTypingIndicator(typingData) {
        const typingIndicator = document.getElementById('typing-indicator');
        const typingText = document.getElementById('typing-text');
        
        if (typingData && typingData.user_type === 'customer') {
            typingIndicator.style.display = 'block';
            typingText.textContent = `${typingData.user_name} يكتب...`;
            
            // إخفاء بعد 5 ثواني
            setTimeout(() => {
                typingIndicator.style.display = 'none';
            }, 5000);
        } else {
            typingIndicator.style.display = 'none';
        }
    }

    /**
     * عرض إشعار سطح المكتب
     */
    showDesktopNotification(message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(`رسالة جديدة من ${message.sender_name}`, {
                body: message.message.substring(0, 100),
                icon: '/images/notification-icon.png'
            });
        }
    }

    /**
     * تشغيل صوت الإشعار
     */
    playNotificationSound() {
        if (this.config.soundEnabled) {
            // يمكن إضافة ملف صوتي هنا
            console.log('🔔 Playing notification sound');
        }
    }

    /**
     * Utility Functions
     */
    getStatusText(status) {
        const statusTexts = {
            'open': 'مفتوح',
            'in_progress': 'قيد المعالجة',
            'resolved': 'تم الحل',
            'closed': 'مغلق'
        };
        return statusTexts[status] || status;
    }

    getPriorityText(priority) {
        const priorityTexts = {
            'low': 'منخفضة',
            'medium': 'متوسطة',
            'high': 'عالية',
            'urgent': 'عاجل'
        };
        return priorityTexts[priority] || priority;
    }

    formatMessageTime(timestamp) {
        return new Date(timestamp).toLocaleTimeString('ar-SA');
    }

    formatTimeAgo(timestamp) {
        const now = Date.now();
        const diff = now - timestamp;
        const minutes = Math.floor(diff / 60000);
        
        if (minutes < 1) return 'الآن';
        if (minutes < 60) return `منذ ${minutes} دقيقة`;
        if (minutes < 1440) return `منذ ${Math.floor(minutes / 60)} ساعة`;
        return `منذ ${Math.floor(minutes / 1440)} يوم`;
    }

    formatMessageContent(content) {
        // تحويل الروابط إلى clickable links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return content.replace(urlRegex, '<a href="$1" target="_blank">$1</a>');
    }

    isRecentNotification(timestamp) {
        return Date.now() - timestamp < 30000; // 30 seconds
    }

    isRecentMessage(timestamp) {
        return Date.now() - timestamp < 30000; // 30 seconds
    }

    updateConnectionStatus(status, message) {
        const statusElement = document.getElementById('firebase-status');
        const statusIcon = document.getElementById('status-icon');
        const statusText = document.getElementById('status-text');
        
        if (!statusElement) return;
        
        statusElement.className = 'firebase-status';
        
        switch(status) {
            case 'connected':
                statusElement.classList.add('firebase-connected');
                statusIcon.className = 'fas fa-circle';
                break;
            case 'disconnected':
                statusElement.classList.add('firebase-disconnected');
                statusIcon.className = 'fas fa-exclamation-circle';
                break;
            case 'error':
                statusElement.classList.add('firebase-disconnected');
                statusIcon.className = 'fas fa-times-circle';
                break;
        }
        
        statusText.textContent = message;
    }

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.style.cssText = 'position:fixed;top:100px;right:20px;z-index:9999;max-width:400px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chat-messages');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    updateStatElement(id, value) {
        const element = document.getElementById(id);
        if (element && element.textContent !== value.toString()) {
            element.style.transform = 'scale(1.2)';
            element.textContent = value;
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 200);
        }
    }

    // Additional methods for admin functions
    async updateChatStats() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/stats`);
            const data = await response.json();
            
            if (data.success) {
                // Stats are updated via Firebase real-time
                console.log('📊 Stats updated');
            }
        } catch (error) {
            console.error('Error updating stats:', error);
        }
    }

    async updateAdminActivity(activity) {
        try {
            await fetch(`${this.config.apiBaseUrl}/update-activity`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ activity })
            });
        } catch (error) {
            console.error('Error updating admin activity:', error);
        }
    }

    stopCurrentChatListeners() {
        this.activeListeners.forEach((ref, key) => {
            if (key.includes('messages_') || key.includes('info_') || key.includes('typing_')) {
                ref.off();
                this.activeListeners.delete(key);
            }
        });
    }

    cleanup() {
        this.activeListeners.forEach(ref => ref.off());
        this.activeListeners.clear();
        
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
        }
    }

    onConnectionRestored() {
        console.log('🔄 Connection restored, re-initializing listeners');
        this.startRealTimeListeners();
        
        if (this.currentChatId) {
            this.listenToChatMessages(this.currentChatId);
            this.listenToChatInfo(this.currentChatId);
            this.listenToTypingIndicator(this.currentChatId);
        }
    }
}

// Global instance
let adminChat;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    adminChat = new AdminFirebaseChatSystem({
        firebaseUrl: 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app',
        soundEnabled: true,
        autoScroll: true
    });
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    console.log('🚀 Admin Firebase Chat System initialized');
});
