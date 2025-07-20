{{-- Chat Widget Component - Chỉ hiển thị khi đăng nhập --}}
@auth
<div id="chatWidget" class="chat-widget">
    <!-- Chat Toggle Button -->
    <button id="chatToggle" class="chat-toggle-btn" type="button">
        <i class="fas fa-comments"></i>
        <span id="unreadBadge" class="unread-badge d-none">0</span>
    </button>

    <!-- Chat Panel -->
    <div id="chatPanel" class="chat-panel d-none">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-comments me-2"></i>
                    {{ __('nav.messages') }}
                </h6>
                <div class="chat-header-actions">
                    <button id="newChatBtn" class="btn btn-sm btn-primary" title="{{ __('content.new_message') }}">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button id="chatMinimize" class="btn btn-sm btn-outline-secondary" title="{{ __('content.minimize') }}">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Tabs -->
        <div class="chat-tabs">
            <ul class="nav nav-tabs nav-fill" id="chatTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="conversations-tab" data-bs-toggle="tab"
                            data-bs-target="#conversations" type="button" role="tab">
                        <i class="fas fa-list me-1"></i>
                        {{ __('content.list') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-chat-tab" data-bs-toggle="tab"
                            data-bs-target="#active-chat" type="button" role="tab" style="display: none;">
                        <i class="fas fa-comment-dots me-1"></i>
                        <span id="activeChatTitle">{{ __('content.chat') }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <!-- Chat Content -->
        <div class="chat-content">
            <div class="tab-content" id="chatTabContent">
                <!-- Conversations List -->
                <div class="tab-pane fade show active" id="conversations" role="tabpanel">
                    <div class="conversations-search p-2">
                        <input type="text" class="form-control form-control-sm"
                               id="conversationSearch" placeholder="{{ t_ui('forms.search_conversations_placeholder') }}">
                    </div>
                    <div id="conversationsList" class="conversations-list">
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Chat -->
                <div class="tab-pane fade" id="active-chat" role="tabpanel">
                    <div id="chatMessages" class="chat-messages">
                        <div class="text-center p-3 text-muted">
                            <i class="fas fa-comment-dots fa-2x mb-2"></i>
                            <p class="mb-0">Chọn một cuộc trò chuyện để bắt đầu</p>
                        </div>
                    </div>
                    <div class="chat-input">
                        <form id="messageForm" class="d-flex">
                            <input type="hidden" id="activeConversationId" value="">
                            <input type="text" class="form-control" id="messageInput"
                                   placeholder="{{ t_ui('forms.enter_message_placeholder') }}" disabled>
                            <button type="submit" class="btn btn-primary ms-2" disabled>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Tin nhắn mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newChatForm">
                    <div class="mb-3">
                        <label for="recipientSearch" class="form-label">Người nhận:</label>
                        <input type="text" class="form-control" id="recipientSearch"
                               placeholder="{{ t_ui('forms.search_members_placeholder') }}">
                        <div id="recipientSuggestions" class="suggestions-dropdown"></div>
                        <input type="hidden" id="selectedRecipientId" value="">
                    </div>
                    <div class="mb-3">
                        <label for="firstMessage" class="form-label">Tin nhắn đầu tiên:</label>
                        <textarea class="form-control" id="firstMessage" rows="3"
                                  placeholder="Nhập tin nhắn..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ t_ui('buttons.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="sendNewChat">
                    <i class="fas fa-paper-plane me-1"></i>
                    Gửi tin nhắn
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1050;
}

.chat-toggle-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    color: white;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    transition: all 0.3s ease;
    position: relative;
}

.chat-toggle-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
}

.unread-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.chat-panel {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    border: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chat-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 15px;
    border-radius: 12px 12px 0 0;
}

.chat-tabs .nav-tabs {
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 0;
}

.chat-tabs .nav-link {
    border: none;
    padding: 10px 15px;
    font-size: 14px;
}

.chat-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.conversations-list {
    flex: 1;
    overflow-y: auto;
    max-height: 350px;
}

.conversation-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: background-color 0.2s;
}

.conversation-item:hover {
    background-color: #f8f9fa;
}

.conversation-item.active {
    background-color: #e3f2fd;
    border-left: 3px solid #007bff;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    max-height: 350px;
}

.chat-input {
    padding: 15px;
    border-top: 1px solid #dee2e6;
    background: #f8f9fa;
}

.message-bubble {
    max-width: 80%;
    margin-bottom: 10px;
    padding: 8px 12px;
    border-radius: 18px;
    word-wrap: break-word;
}

.message-bubble.sent {
    background: #007bff;
    color: white;
    margin-left: auto;
    text-align: right;
}

.message-bubble.received {
    background: #e9ecef;
    color: #333;
}

.suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.suggestion-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
}

.suggestion-item:hover {
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .chat-panel {
        width: 300px;
        height: 450px;
    }

    .chat-toggle-btn {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/chat-widget.js') }}"></script>
@endpush

@endauth
