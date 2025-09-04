/**
 * Pusher Chat Customer JavaScript
 * Real-time chat implementation for customers using Laravel Echo
 */

class PusherChatCustomer {
    constructor(options = {}) {
        this.options = {
            pusherKey: options.pusherKey || '',
            pusherCluster: options.pusherCluster || 'mt1',
            authEndpoint: options.authEndpoint || '/broadcasting/auth',
            csrfToken: options.csrfToken || '',
            apiToken: options.apiToken || '',
            baseUrl: options.baseUrl || '',
            containerId: options.containerId || 'chat-container',
            ...options
        };

        this.chat = null;
        this.chatChannel = null;
        this.echo = null;
        this.messages = [];
        
        this.init();
    }

    /**
     * Initialize the chat system
     */
    init() {
        this.setupEcho();
        this.setupEventListeners();
        this.loadChat();
    }

    /**
     * Setup Laravel Echo with Pusher
     */
    setupEcho() {
        if (typeof Echo === 'undefined') {
            console.error('Laravel Echo is not loaded. Please include Laravel Echo before this script.');
            return;
        }

        this.echo = new Echo({
            broadcaster: 'pusher',
            key: this.options.pusherKey,
            cluster: this.options.pusherCluster,
            forceTLS: true,
            auth: {
                headers: {
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Authorization': `Bearer ${this.options.apiToken}`
                }
            }
        });

        console.log('Laravel Echo initialized for Pusher Chat');
    }

    /**
     * Load or create chat
     */
    async loadChat() {
        try {
            const response = await fetch(`${this.options.baseUrl}/api/chat/start`, {
                headers: {
                    'Authorization': `Bearer ${this.options.apiToken}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.chat = data.data.chat;
                this.messages = data.data.messages || [];
                this.setupChatChannel();
                this.renderChat();
                this.displayMessages();
            } else {
                this.showError('Failed to load chat: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading chat:', error);
            this.showError('Failed to connect to chat service');
        }
    }

    /**
     * Setup chat channel for real-time updates
     */
    setupChatChannel() {
        if (!this.echo || !this.chat) return;

        this.chatChannel = this.echo.channel(`chat.${this.chat.id}`);
        
        this.chatChannel.listen('App\\Events\\NewChatMessage', (data) => {
            console.log('New message received:', data);
            this.addMessage(data.message);
        });

        this.chatChannel.error((error) => {
            console.error('Chat channel error:', error);
            this.updateConnectionStatus('error');
        });

        this.updateConnectionStatus('connected');
    }

    /**
     * Render chat interface
     */
    renderChat() {
        const container = document.getElementById(this.options.containerId);
        if (!container) {
            console.error(`Container with ID '${this.options.containerId}' not found`);
            return;
        }

        container.innerHTML = `
            <div class="pusher-chat-widget">
                <div class="chat-header">
                    <h5 class="mb-0">💬 Support Chat</h5>
                    <div class="chat-status">
                        <span id="connectionStatus" class="status-indicator connecting">🔄 Connecting...</span>
                    </div>
                </div>
                
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be displayed here -->
                </div>
                
                <div class="chat-input">
                    <form id="messageForm" class="message-form">
                        <div class="input-group">
                            <textarea id="messageInput" 
                                    placeholder="Type your message..." 
                                    class="form-control message-input"
                                    rows="2"
                                    maxlength="2000"></textarea>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary send-button" id="sendButton">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Press Ctrl+Enter to send</small>
                    </form>
                </div>
            </div>
        `;

        this.setupChatStyles();
    }

    /**
     * Setup chat styles
     */
    setupChatStyles() {
        if (document.getElementById('pusher-chat-styles')) return;

        const styles = document.createElement('style');
        styles.id = 'pusher-chat-styles';
        styles.textContent = `
            .pusher-chat-widget {
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                background: white;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                max-width: 400px;
                height: 500px;
                display: flex;
                flex-direction: column;
            }
            
            .chat-header {
                padding: 1rem;
                border-bottom: 1px solid #dee2e6;
                background: #f8f9fa;
                border-radius: 0.5rem 0.5rem 0 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .status-indicator {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                background: #e9ecef;
            }
            
            .status-indicator.connected {
                background: #d4edda;
                color: #155724;
            }
            
            .status-indicator.error {
                background: #f8d7da;
                color: #721c24;
            }
            
            .chat-messages {
                flex: 1;
                overflow-y: auto;
                padding: 1rem;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .message {
                max-width: 80%;
                word-wrap: break-word;
            }
            
            .message.customer {
                align-self: flex-end;
            }
            
            .message.admin {
                align-self: flex-start;
            }
            
            .message-content {
                padding: 0.75rem;
                border-radius: 1rem;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }
            
            .message.customer .message-content {
                background: #007bff;
                color: white;
                border-bottom-right-radius: 0.25rem;
            }
            
            .message.admin .message-content {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-bottom-left-radius: 0.25rem;
            }
            
            .message-meta {
                font-size: 0.75rem;
                margin-top: 0.25rem;
                opacity: 0.7;
            }
            
            .message.customer .message-meta {
                text-align: right;
                color: white;
            }
            
            .message.admin .message-meta {
                color: #6c757d;
            }
            
            .chat-input {
                padding: 1rem;
                border-top: 1px solid #dee2e6;
                background: #f8f9fa;
            }
            
            .message-input {
                resize: none;
                border-radius: 0.5rem 0 0 0.5rem;
            }
            
            .send-button {
                border-radius: 0 0.5rem 0.5rem 0;
            }
            
            .message-form small {
                display: block;
                margin-top: 0.5rem;
            }
            
            .new-message {
                animation: slideIn 0.3s ease-out;
            }
            
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        
        document.head.appendChild(styles);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'messageForm') {
                e.preventDefault();
                this.sendMessage();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.target.id === 'messageInput' && e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                this.sendMessage();
            }
        });
    }

    /**
     * Display messages in chat
     */
    displayMessages() {
        const messagesContainer = document.getElementById('chatMessages');
        if (!messagesContainer) return;

        messagesContainer.innerHTML = '';
        
        this.messages.forEach(message => {
            this.addMessageToDOM(message, false);
        });

        this.scrollToBottom();
    }

    /**
     * Add new message to chat
     */
    addMessage(message) {
        this.messages.push(message);
        this.addMessageToDOM(message, true);
        this.scrollToBottom();
    }

    /**
     * Add message to DOM
     */
    addMessageToDOM(message, animate = false) {
        const messagesContainer = document.getElementById('chatMessages');
        if (!messagesContainer) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.sender_type}${animate ? ' new-message' : ''}`;
        
        messageDiv.innerHTML = `
            <div class="message-content">
                ${this.escapeHtml(message.message)}
            </div>
            <div class="message-meta">
                ${message.sender ? message.sender.name : (message.sender_type === 'admin' ? 'الإدارة' : 'أنت')} • ${message.formatted_time}
            </div>
        `;

        messagesContainer.appendChild(messageDiv);
    }

    /**
     * Send message
     */
    async sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        
        if (!messageInput || !sendButton) return;

        const message = messageInput.value.trim();
        if (!message) return;

        const originalButtonText = sendButton.innerHTML;
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch(`${this.options.baseUrl}/api/chat/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.options.apiToken}`,
                    'X-CSRF-TOKEN': this.options.csrfToken
                },
                body: JSON.stringify({
                    chat_id: this.chat.id,
                    message: message
                })
            });

            const data = await response.json();
            
            if (data.success) {
                messageInput.value = '';
                messageInput.focus();
            } else {
                this.showError('Failed to send message: ' + data.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showError('Failed to send message');
        } finally {
            sendButton.disabled = false;
            sendButton.innerHTML = originalButtonText;
        }
    }

    /**
     * Update connection status
     */
    updateConnectionStatus(status) {
        const statusElement = document.getElementById('connectionStatus');
        if (!statusElement) return;

        statusElement.className = `status-indicator ${status}`;
        
        switch (status) {
            case 'connected':
                statusElement.textContent = '🟢 Connected';
                break;
            case 'error':
                statusElement.textContent = '🔴 Connection Error';
                break;
            default:
                statusElement.textContent = '🔄 Connecting...';
        }
    }

    /**
     * Scroll to bottom of messages
     */
    scrollToBottom() {
        const messagesContainer = document.getElementById('chatMessages');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        console.error('Chat Error:', message);
        // You can implement a custom error display here
        alert(message);
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Disconnect from chat
     */
    disconnect() {
        if (this.chatChannel) {
            this.chatChannel.stopListening('message.sent');
        }
        
        if (this.echo) {
            this.echo.disconnect();
        }
    }
}

// Export for use in other scripts
window.PusherChatCustomer = PusherChatCustomer;
