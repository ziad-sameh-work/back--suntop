<div class="chat-interface">
    <!-- Messages Container -->
    <div class="messages-container" id="messagesContainer">
        @if(count($messages) > 0)
            @foreach($messages as $message)
                <div class="message {{ $message['sender_type'] }}">
                    <div class="message-bubble">
                        <div class="message-header">
                            <span class="message-sender">
                                @if($message['sender_type'] === 'admin')
                                    {{ $message['sender']['name'] }} (الإدارة)
                                @else
                                    {{ $message['sender']['name'] }}
                                @endif
                            </span>
                            <span class="message-time">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</span>
                        </div>
                        
                        @if($message['message'])
                            <div class="message-content">
                                {!! nl2br(e($message['message'])) !!}
                            </div>
                        @endif

                        @if($message['attachment_path'])
                            <div class="message-attachment">
                                <div class="attachment-icon">
                                    @if($message['message_type'] === 'image')
                                        <i class="fas fa-image"></i>
                                    @else
                                        <i class="fas fa-file"></i>
                                    @endif
                                </div>
                                <div class="attachment-info">
                                    <div class="attachment-name">{{ $message['attachment_name'] }}</div>
                                    @if($message['message_type'] === 'image')
                                        <div class="attachment-size">صورة</div>
                                    @else
                                        <div class="attachment-size">ملف</div>
                                    @endif
                                </div>
                                <a href="{{ Storage::url($message['attachment_path']) }}" 
                                   target="_blank" class="attachment-download">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </a>
                            </div>

                            @if($message['message_type'] === 'image')
                                <img src="{{ Storage::url($message['attachment_path']) }}" 
                                     alt="{{ $message['attachment_name'] }}" 
                                     class="message-image">
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-messages">
                <i class="fas fa-comments"></i>
                <p>لا توجد رسائل حتى الآن. ابدأ المحادثة!</p>
            </div>
        @endif
    </div>

    <!-- Message Input -->
    <div class="message-input-container">
        <form wire:submit.prevent="sendMessage" class="message-form">
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

            <div class="message-input-wrapper">
                <!-- File Input -->
                <label for="attachment" class="attachment-btn">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="attachment" wire:model="attachment" 
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt" style="display: none;">
                </label>

                <!-- Text Input -->
                <div class="text-input-wrapper">
                    <textarea wire:model.defer="newMessage" 
                              wire:keydown.enter.prevent="sendMessage"
                              placeholder="اكتب رسالتك هنا..." 
                              class="message-textarea"
                              rows="1"></textarea>
                    
                    <!-- Emoji Button -->
                    <button type="button" wire:click="toggleEmojiPicker" class="emoji-btn">
                        <i class="fas fa-smile"></i>
                    </button>
                </div>

                <!-- Send Button -->
                <button type="submit" class="send-btn" wire:loading.attr="disabled" wire:target="sendMessage">
                    <i class="fas fa-paper-plane" wire:loading.remove wire:target="sendMessage"></i>
                    <i class="fas fa-spinner fa-spin" wire:loading wire:target="sendMessage" style="display: none;"></i>
                </button>
            </div>

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
        </form>
    </div>
</div>

<script>
    // التمرير إلى أسفل عند تحديث الرسائل
    document.addEventListener('livewire:load', function () {
        // التمرير لأسفل عند التحميل
        scrollToBottom();
        
        // الاستماع للحدث
        window.addEventListener('scrollToBottom', event => {
            scrollToBottom();
        });
        
        // الاستماع لحدث تحديث الرسائل القسري
        window.addEventListener('forceRefreshMessages', event => {
            console.log('Force refresh messages triggered:', event.detail);
            // إعادة تحميل الصفحة جزئياً أو تحديث المحتوى
            setTimeout(() => {
                scrollToBottom();
            }, 100);
        });
        
        // دالة التمرير لأسفل
        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    });
</script>

<style>
/* Chat Interface */
.chat-interface {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
}

/* Messages Container */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
    max-height: calc(100vh - 300px);
}

.message {
    display: flex;
    margin-bottom: 20px;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
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

.message-attachment {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
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

.message-image {
    max-width: 100%;
    border-radius: 10px;
    margin-top: 10px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.message-image:hover {
    transform: scale(1.02);
}

.empty-messages {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.empty-messages i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-messages p {
    font-size: 1.1rem;
    margin: 0;
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

.send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.4);
}

/* Removed disabled styles as button is always enabled */

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

/* Auto-resize textarea */
.message-textarea {
    overflow-y: hidden;
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
    
    .emoji-picker {
        right: 10px;
        max-width: calc(100vw - 40px);
    }
    
    .emoji-grid {
        max-width: 100%;
    }
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea
    const textarea = document.querySelector('.message-textarea');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
    
    // Auto-scroll to bottom when new message arrives
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        const scrollToBottom = () => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        };
        
        // Initial scroll
        scrollToBottom();
        
        // Observer for new messages
        const observer = new MutationObserver(scrollToBottom);
        observer.observe(messagesContainer, { childList: true, subtree: true });
    }
    
    // Close emoji picker when clicking outside
    document.addEventListener('click', function(e) {
        const emojiPicker = document.querySelector('.emoji-picker');
        const emojiBtn = document.querySelector('.emoji-btn');
        
        if (emojiPicker && !emojiPicker.contains(e.target) && !emojiBtn.contains(e.target)) {
            @this.call('toggleEmojiPicker');
        }
    });
});

// Livewire hooks
document.addEventListener('livewire:load', function () {
    // Scroll to bottom after message sent
    Livewire.hook('message.processed', (message, component) => {
        if (message.updateQueue && message.updateQueue.some(update => update.method === 'sendMessage')) {
            setTimeout(() => {
                const messagesContainer = document.querySelector('.messages-container');
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }, 100);
        }
    });
});
</script>