/**
 * MechaMap Group Chat Interface
 * Real-time group chat with WebSocket integration
 */

class GroupChat {
    constructor(groupId, options = {}) {
        this.groupId = groupId;
        this.options = {
            typingTimeout: 3000,
            maxTypingUsers: 3,
            ...options
        };

        this.typingTimer = null;
        this.typingUsers = new Set();
        this.isTyping = false;
        this.memberCount = 0;

        this.init();
    }

    init() {
        this.bindEvents();
        this.setupWebSocketCallbacks();
        this.joinGroup();
        this.updateMemberCount();
    }

    bindEvents() {
        // Message input events
        const messageInput = document.querySelector('#message-input');
        if (messageInput) {
            messageInput.addEventListener('input', this.handleTyping.bind(this));
            messageInput.addEventListener('keypress', this.handleKeyPress.bind(this));
            messageInput.addEventListener('blur', this.stopTyping.bind(this));
        }

        // Send button
        const sendButton = document.querySelector('#send-message-btn');
        if (sendButton) {
            sendButton.addEventListener('click', this.sendMessage.bind(this));
        }

        // Member count refresh
        const refreshButton = document.querySelector('#refresh-members-btn');
        if (refreshButton) {
            refreshButton.addEventListener('click', this.updateMemberCount.bind(this));
        }
    }

    setupWebSocketCallbacks() {
        if (!window.GroupWebSocket) {
            console.error('GroupChat: GroupWebSocket not available');
            return;
        }

        // Group messages
        window.GroupWebSocket.addCallback('onGroupMessage', (data) => {
            if (data.group_id == this.groupId) {
                this.displayMessage(data);
            }
        });

        // Member events
        window.GroupWebSocket.addCallback('onMemberJoin', (data) => {
            if (data.group_id == this.groupId) {
                this.handleMemberJoin(data);
            }
        });

        window.GroupWebSocket.addCallback('onMemberLeave', (data) => {
            if (data.group_id == this.groupId) {
                this.handleMemberLeave(data);
            }
        });

        // Typing indicators
        window.GroupWebSocket.addCallback('onTypingStart', (data) => {
            if (data.group_id == this.groupId) {
                this.showTypingIndicator(data.user_name || data.user_id);
            }
        });

        window.GroupWebSocket.addCallback('onTypingStop', (data) => {
            if (data.group_id == this.groupId) {
                this.hideTypingIndicator(data.user_name || data.user_id);
            }
        });

        // Member count updates
        window.GroupWebSocket.addCallback('onMemberCountUpdate', (data) => {
            if (data.group_id == this.groupId) {
                this.memberCount = data.count;
                this.updateMemberCountDisplay();
            }
        });
    }

    async joinGroup() {
        if (window.GroupWebSocket) {
            try {
                // Join via WebSocket
                window.GroupWebSocket.joinGroup(this.groupId);

                // Join via API for enhanced features
                await window.GroupWebSocket.joinGroupAPI(this.groupId);

                console.log(`✅ GroupChat: Joined group ${this.groupId} via WebSocket and API`);
            } catch (error) {
                console.error('❌ GroupChat: Failed to join group:', error);
                // Fallback to WebSocket only
                window.GroupWebSocket.joinGroup(this.groupId);
            }
        }
    }

    leaveGroup() {
        if (window.GroupWebSocket) {
            window.GroupWebSocket.leaveGroup(this.groupId);
            console.log(`GroupChat: Left group ${this.groupId}`);
        }
    }

    handleTyping(event) {
        if (!window.GroupWebSocket) return;

        const input = event.target;
        const hasContent = input.value.trim().length > 0;

        if (hasContent && !this.isTyping) {
            // Start typing with enhanced API
            this.isTyping = true;
            window.GroupWebSocket.startTyping(this.groupId);
        }

        // Reset typing timer
        clearTimeout(this.typingTimer);
        this.typingTimer = setTimeout(() => {
            this.stopTyping();
        }, this.options.typingTimeout);
    }

    stopTyping() {
        if (this.isTyping && window.GroupWebSocket) {
            this.isTyping = false;
            window.GroupWebSocket.stopTyping(this.groupId);
        }
        clearTimeout(this.typingTimer);
    }

    handleKeyPress(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            this.sendMessage();
        }
    }

    sendMessage() {
        const messageInput = document.querySelector('#message-input');
        if (!messageInput) return;

        const message = messageInput.value.trim();
        if (!message) return;

        // Stop typing indicator
        this.stopTyping();

        // Send via WebSocket if available, otherwise fall back to form submission
        if (window.GroupWebSocket && window.GroupWebSocket.sendMessage(this.groupId, message)) {
            messageInput.value = '';
            console.log('GroupChat: Message sent via WebSocket');
        } else {
            // Fall back to form submission
            const form = messageInput.closest('form');
            if (form) {
                form.submit();
            }
        }
    }

    displayMessage(data) {
        const messagesContainer = document.querySelector('#messages-container');
        if (!messagesContainer) return;

        const messageElement = this.createMessageElement(data);
        messagesContainer.appendChild(messageElement);

        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Add animation
        messageElement.classList.add('message-new');
        setTimeout(() => {
            messageElement.classList.remove('message-new');
        }, 500);
    }

    createMessageElement(data) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-item mb-3';

        const timestamp = new Date(data.timestamp || Date.now()).toLocaleTimeString();

        messageDiv.innerHTML = `
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-1">${data.user_name || 'Unknown User'}</h6>
                        <small class="text-muted">${timestamp}</small>
                    </div>
                    <p class="mb-0">${this.escapeHtml(data.message)}</p>
                </div>
            </div>
        `;

        return messageDiv;
    }

    showTypingIndicator(userName) {
        this.typingUsers.add(userName);
        this.updateTypingDisplay();
    }

    hideTypingIndicator(userName) {
        this.typingUsers.delete(userName);
        this.updateTypingDisplay();
    }

    updateTypingDisplay() {
        const typingIndicator = document.querySelector('#typing-indicator');
        if (!typingIndicator) return;

        if (this.typingUsers.size === 0) {
            typingIndicator.style.display = 'none';
            return;
        }

        const users = Array.from(this.typingUsers);
        let text = '';

        if (users.length === 1) {
            text = `${users[0]} đang nhập...`;
        } else if (users.length <= this.options.maxTypingUsers) {
            text = `${users.join(', ')} đang nhập...`;
        } else {
            text = `${users.slice(0, this.options.maxTypingUsers).join(', ')} và ${users.length - this.options.maxTypingUsers} người khác đang nhập...`;
        }

        typingIndicator.innerHTML = `
            <div class="text-muted small">
                <i class="fas fa-circle text-success me-1" style="font-size: 0.5rem;"></i>
                ${text}
            </div>
        `;
        typingIndicator.style.display = 'block';
    }

    handleMemberJoin(data) {
        this.showNotification(`${data.user_name || 'Thành viên mới'} đã tham gia nhóm`, 'success');
        this.updateMemberCount();
    }

    handleMemberLeave(data) {
        this.showNotification(`${data.user_name || 'Thành viên'} đã rời khỏi nhóm`, 'info');
        this.updateMemberCount();
    }

    updateMemberCount() {
        if (window.GroupWebSocket) {
            window.GroupWebSocket.updateMemberCount(this.groupId);
        }
    }

    updateMemberCountDisplay() {
        const memberCountElements = document.querySelectorAll('.member-count');
        memberCountElements.forEach(element => {
            element.textContent = this.memberCount;
        });

        // Update page title if needed
        const titleElement = document.querySelector('.group-title .member-count-badge');
        if (titleElement) {
            titleElement.textContent = `${this.memberCount} thành viên`;
        }
    }

    showNotification(message, type = 'info') {
        // Use SweetAlert2 if available
        if (window.Swal) {
            window.Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            console.log(`GroupChat Notification (${type}): ${message}`);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    destroy() {
        this.leaveGroup();
        this.stopTyping();
        clearTimeout(this.typingTimer);

        // Remove event listeners
        const messageInput = document.querySelector('#message-input');
        if (messageInput) {
            messageInput.removeEventListener('input', this.handleTyping);
            messageInput.removeEventListener('keypress', this.handleKeyPress);
            messageInput.removeEventListener('blur', this.stopTyping);
        }
    }
}

// Auto-initialize for group chat pages
document.addEventListener('DOMContentLoaded', function() {
    const groupChatContainer = document.querySelector('[data-group-id]');
    if (groupChatContainer) {
        const groupId = groupChatContainer.dataset.groupId;
        if (groupId) {
            window.groupChat = new GroupChat(groupId);
            console.log(`✅ GroupChat initialized for group ${groupId}`);
        }
    }
});

// Export for manual initialization
window.GroupChat = GroupChat;
