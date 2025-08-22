/**
 * MechaMap Chat Widget
 * Real-time messaging system for authenticated users
 */

class ChatWidget {
    constructor() {
        this.isOpen = false;
        this.activeConversationId = null;
        this.conversations = [];
        this.unreadCount = 0;
        this.refreshInterval = null;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadConversations();
        this.startRefreshTimer();
    }

    bindEvents() {
        // Toggle chat panel
        document.getElementById('chatToggle')?.addEventListener('click', () => {
            this.toggleChat();
        });

        // Minimize chat
        document.getElementById('chatMinimize')?.addEventListener('click', () => {
            this.closeChat();
        });

        // New chat button
        document.getElementById('newChatBtn')?.addEventListener('click', () => {
            this.openNewChatModal();
        });

        // Message form
        document.getElementById('messageForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // New chat form
        document.getElementById('sendNewChat')?.addEventListener('click', () => {
            this.createNewChat();
        });

        // Recipient search
        document.getElementById('recipientSearch')?.addEventListener('input', (e) => {
            this.searchUsers(e.target.value);
        });

        // Conversation search
        document.getElementById('conversationSearch')?.addEventListener('input', (e) => {
            this.filterConversations(e.target.value);
        });

        // Close chat when clicking outside
        document.addEventListener('click', (e) => {
            const chatWidget = document.getElementById('chatWidget');
            if (chatWidget && !chatWidget.contains(e.target) && this.isOpen) {
                this.closeChat();
            }
        });
    }

    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }

    openChat() {
        const panel = document.getElementById('chatPanel');
        if (panel) {
            panel.classList.remove('d-none');
            this.isOpen = true;
            this.loadConversations();
        }
    }

    closeChat() {
        const panel = document.getElementById('chatPanel');
        if (panel) {
            panel.classList.add('d-none');
            this.isOpen = false;
        }
    }

    async loadConversations() {
        try {
            const response = await fetch('/api/conversations', {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.conversations = data.data || [];
                this.renderConversations();
                this.updateUnreadCount();
            }
        } catch (error) {
            console.error('Error loading conversations:', error);
            this.showError('Không thể tải danh sách cuộc trò chuyện');
        }
    }

    renderConversations() {
        const container = document.getElementById('conversationsList');
        if (!container) return;

        if (this.conversations.length === 0) {
            container.innerHTML = `
                <div class="text-center p-3 text-muted">
                    <i class="fa-solid fa-comments fa-2x mb-2"></i>
                    <p class="mb-0">Chưa có cuộc trò chuyện nào</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="chatWidget.openNewChatModal()">
                        <i class="fa-solid fa-plus me-1"></i>
                        Bắt đầu trò chuyện
                    </button>
                </div>
            `;
            return;
        }

        const html = this.conversations.map(conv => {
            const otherUser = conv.other_participant;
            const lastMessage = conv.last_message;
            const isUnread = conv.unread_count > 0;

            return `
                <div class="conversation-item ${isUnread ? 'unread' : ''}"
                     data-conversation-id="${conv.id}"
                     onclick="chatWidget.openConversation(${conv.id})">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <img src="${otherUser?.avatar || '/images/default-avatar.png'}"
                                 class="rounded-circle" width="40" height="40" alt="">
                            ${otherUser?.is_online ? '<span class="online-indicator"></span>' : ''}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-truncate ${isUnread ? 'fw-bold' : ''}">
                                    ${otherUser?.name || 'Unknown User'}
                                </h6>
                                <small class="text-muted">
                                    ${this.formatTime(lastMessage?.created_at)}
                                </small>
                            </div>
                            <p class="mb-0 text-truncate text-muted small">
                                ${lastMessage?.content || 'Chưa có tin nhắn'}
                            </p>
                            ${isUnread ? `<span class="badge bg-primary rounded-pill">${conv.unread_count}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }

    async openConversation(conversationId) {
        this.activeConversationId = conversationId;

        // Switch to chat tab
        const chatTab = document.getElementById('active-chat-tab');
        const conversationsTab = document.getElementById('conversations-tab');

        if (chatTab && conversationsTab) {
            chatTab.style.display = 'block';
            chatTab.click();
        }

        // Load messages
        await this.loadMessages(conversationId);

        // Enable message input
        const messageInput = document.getElementById('messageInput');
        const submitBtn = document.querySelector('#messageForm button[type="submit"]');

        if (messageInput && submitBtn) {
            messageInput.disabled = false;
            submitBtn.disabled = false;
            messageInput.focus();
        }

        // Update active conversation ID
        document.getElementById('activeConversationId').value = conversationId;

        // Mark as read
        this.markAsRead(conversationId);
    }

    async loadMessages(conversationId) {
        try {
            const response = await fetch(`/api/conversations/${conversationId}/messages`, {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.renderMessages(data.data || []);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            this.showError('Không thể tải tin nhắn');
        }
    }

    renderMessages(messages) {
        const container = document.getElementById('chatMessages');
        if (!container) return;

        if (messages.length === 0) {
            container.innerHTML = `
                <div class="text-center p-3 text-muted">
                    <i class="fa-solid fa-comment-dots fa-2x mb-2"></i>
                    <p class="mb-0">Chưa có tin nhắn nào</p>
                    <p class="small">Hãy gửi tin nhắn đầu tiên!</p>
                </div>
            `;
            return;
        }

        const currentUserId = this.getCurrentUserId();
        const html = messages.map(message => {
            const isSent = message.user_id == currentUserId;
            return `
                <div class="message-bubble ${isSent ? 'sent' : 'received'}">
                    <div class="message-content">${this.escapeHtml(message.content)}</div>
                    <div class="message-time small opacity-75 mt-1">
                        ${this.formatTime(message.created_at)}
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;

        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    }

    async sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const conversationId = document.getElementById('activeConversationId').value;

        if (!messageInput || !conversationId || !messageInput.value.trim()) {
            return;
        }

        const content = messageInput.value.trim();
        messageInput.value = '';

        try {
            const response = await fetch(`/api/conversations/${conversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ content })
            });

            if (response.ok) {
                // Reload messages
                await this.loadMessages(conversationId);
                // Reload conversations to update last message
                await this.loadConversations();
            } else {
                this.showError('Không thể gửi tin nhắn');
                messageInput.value = content; // Restore message
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showError('Lỗi kết nối');
            messageInput.value = content; // Restore message
        }
    }

    openNewChatModal() {
        const modal = new bootstrap.Modal(document.getElementById('newChatModal'));
        modal.show();
    }

    async searchUsers(query) {
        if (query.length < 2) {
            document.getElementById('recipientSuggestions').style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`/api/search/users?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.renderUserSuggestions(data.data || []);
            }
        } catch (error) {
            console.error('Error searching users:', error);
        }
    }

    renderUserSuggestions(users) {
        const container = document.getElementById('recipientSuggestions');
        if (!container) return;

        if (users.length === 0) {
            container.style.display = 'none';
            return;
        }

        const html = users.map(user => `
            <div class="suggestion-item" onclick="chatWidget.selectRecipient(${user.id}, '${this.escapeHtml(user.name)}')">
                <div class="d-flex align-items-center">
                    <img src="${user.avatar || '/images/default-avatar.png'}"
                         class="rounded-circle me-2" width="30" height="30" alt="">
                    <div>
                        <div class="fw-medium">${this.escapeHtml(user.name)}</div>
                        <small class="text-muted">@${this.escapeHtml(user.username || user.email)}</small>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
        container.style.display = 'block';
    }

    selectRecipient(userId, userName) {
        document.getElementById('selectedRecipientId').value = userId;
        document.getElementById('recipientSearch').value = userName;
        document.getElementById('recipientSuggestions').style.display = 'none';
    }

    async createNewChat() {
        const recipientId = document.getElementById('selectedRecipientId').value;
        const message = document.getElementById('firstMessage').value.trim();

        if (!recipientId || !message) {
            this.showError('Vui lòng chọn người nhận và nhập tin nhắn');
            return;
        }

        try {
            const response = await fetch('/api/conversations', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    recipient_id: recipientId,
                    message: message
                })
            });

            if (response.ok) {
                const data = await response.json();

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('newChatModal')).hide();

                // Reset form
                document.getElementById('newChatForm').reset();
                document.getElementById('selectedRecipientId').value = '';

                // Reload conversations
                await this.loadConversations();

                // Open the new conversation
                if (data.data?.conversation?.id) {
                    this.openConversation(data.data.conversation.id);
                }
            } else {
                this.showError('Không thể tạo cuộc trò chuyện');
            }
        } catch (error) {
            console.error('Error creating conversation:', error);
            this.showError('Lỗi kết nối');
        }
    }

    filterConversations(query) {
        const items = document.querySelectorAll('.conversation-item');
        items.forEach(item => {
            const name = item.querySelector('h6').textContent.toLowerCase();
            const message = item.querySelector('p').textContent.toLowerCase();
            const matches = name.includes(query.toLowerCase()) || message.includes(query.toLowerCase());
            item.style.display = matches ? 'block' : 'none';
        });
    }

    async markAsRead(conversationId) {
        try {
            await fetch(`/api/conversations/${conversationId}/read`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }

    updateUnreadCount() {
        const totalUnread = this.conversations.reduce((sum, conv) => sum + (conv.unread_count || 0), 0);
        const badge = document.getElementById('unreadBadge');

        if (badge) {
            if (totalUnread > 0) {
                badge.textContent = totalUnread > 99 ? '99+' : totalUnread;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
    }

    startRefreshTimer() {
        // Real-time updates handled by WebSocket - no polling needed
        console.log('ChatWidget: Using WebSocket for real-time updates');
    }

    // Utility methods
    getAuthToken() {
        return document.querySelector('meta[name="api-token"]')?.getAttribute('content') || '';
    }

    getCurrentUserId() {
        return document.querySelector('meta[name="user-id"]')?.getAttribute('content') || '';
    }

    formatTime(timestamp) {
        if (!timestamp) return '';

        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;

        if (diff < 60000) return 'Vừa xong';
        if (diff < 3600000) return `${Math.floor(diff / 60000)} phút`;
        if (diff < 86400000) return `${Math.floor(diff / 3600000)} giờ`;
        if (diff < 604800000) return `${Math.floor(diff / 86400000)} ngày`;

        return date.toLocaleDateString('vi-VN');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showError(message) {
        // You can implement a toast notification here
        console.error(message);
        alert(message); // Temporary solution
    }
}

// Initialize chat widget when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('chatWidget')) {
        window.chatWidget = new ChatWidget();
    }
});
