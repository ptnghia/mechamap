<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Chat Widget</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Test Chat Widget</h1>

        @auth
        <div class="alert alert-success">
            <h4>✅ Đã đăng nhập</h4>
            <p><strong>User:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>ID:</strong> {{ auth()->id() }}</p>
        </div>
        @else
        <div class="alert alert-warning">
            <h4>❌ Chưa đăng nhập</h4>
            <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để xem chat widget.</p>
        </div>
        @endauth

        <div class="row">
            <div class="col-md-6">
                <h3>Test Chat Widget</h3>
                <p>Chat widget sẽ xuất hiện ở góc dưới bên phải nếu bạn đã đăng nhập.</p>

                <button class="btn btn-primary" onclick="testChatWidget()">Test Chat Widget</button>
                <button class="btn btn-secondary" onclick="checkElements()">Check Elements</button>
            </div>
            <div class="col-md-6">
                <h3>Debug Info</h3>
                <div id="debugInfo" class="bg-light p-3 rounded">
                    <p>Đang kiểm tra...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Widget - Chỉ hiển thị khi đăng nhập -->
    @auth
    <div id="chatWidget" class="chat-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;">
        <button id="chatToggle" class="btn btn-primary rounded-circle" style="width: 60px; height: 60px; box-shadow: 0 4px 12px rgba(0,123,255,0.3);">
            <i class="fas fa-comments"></i>
            <span id="unreadBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                0
            </span>
        </button>

        <div id="chatPanel" class="d-none" style="position: absolute; bottom: 80px; right: 0; width: 350px; height: 500px; background: white; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.15); border: 1px solid #e9ecef;">
            <div class="p-3 bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-comments me-2"></i>
                    Tin nhắn
                </h6>
                <div>
                    <button id="newChatBtn" class="btn btn-sm btn-light me-1" title="Tin nhắn mới">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button id="chatMinimize" class="btn btn-sm btn-light" title="Thu gọn">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="p-3">
                <div class="alert alert-success">
                    <h6>🎉 Chat widget đang hoạt động!</h6>
                    <p class="mb-1"><strong>User:</strong> {{ auth()->user()->name }}</p>
                    <p class="mb-0"><strong>ID:</strong> {{ auth()->id() }}</p>
                </div>

                <div class="mb-3">
                    <h6>Test API:</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="testAPI()">Load Conversations</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="testUserSearch()">Search Users</button>
                </div>

                <div id="apiResults" class="small bg-light p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                    <p class="mb-0">Kết quả API sẽ hiển thị ở đây...</p>
                </div>
            </div>

            <!-- Chat Messages Area -->
            <div class="border-top mt-3 pt-3">
                <h6>💬 Chat Messages:</h6>
                <div id="chatMessages" class="bg-light p-2 rounded mb-2" style="height: 120px; overflow-y: auto;">
                    <div class="text-center text-muted small">
                        <i class="fas fa-comment-dots"></i>
                        <p class="mb-0">Tin nhắn sẽ hiển thị ở đây</p>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="input-group">
                    <input type="text" id="messageInput" class="form-control" placeholder="Nhập tin nhắn..." disabled>
                    <button class="btn btn-primary" id="sendMessageBtn" type="button" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <small class="text-muted">Chọn conversation để bắt đầu chat</small>
            </div>
        </div>
    </div>
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Test page loaded');
        checkElements();

        // Chat toggle functionality
        const chatToggle = document.getElementById('chatToggle');
        const chatPanel = document.getElementById('chatPanel');
        const chatMinimize = document.getElementById('chatMinimize');

        if (chatToggle && chatPanel) {
            chatToggle.addEventListener('click', function() {
                chatPanel.classList.toggle('d-none');
                console.log('Chat panel toggled');
            });
        }

        if (chatMinimize && chatPanel) {
            chatMinimize.addEventListener('click', function() {
                chatPanel.classList.add('d-none');
                console.log('Chat panel minimized');
            });
        }
    });

    function testChatWidget() {
        const widget = document.getElementById('chatWidget');
        const toggle = document.getElementById('chatToggle');
        const panel = document.getElementById('chatPanel');

        console.log('Chat Widget:', widget);
        console.log('Chat Toggle:', toggle);
        console.log('Chat Panel:', panel);

        if (widget) {
            alert('✅ Chat widget found!');
            if (toggle) {
                toggle.click();
            }
        } else {
            alert('❌ Chat widget not found!');
        }
    }

    function checkElements() {
        const debugInfo = document.getElementById('debugInfo');
        const widget = document.getElementById('chatWidget');
        const toggle = document.getElementById('chatToggle');
        const panel = document.getElementById('chatPanel');

        const info = {
            'Chat Widget': widget ? '✅ Found' : '❌ Not found',
            'Chat Toggle': toggle ? '✅ Found' : '❌ Not found',
            'Chat Panel': panel ? '✅ Found' : '❌ Not found',
            'User Authenticated': '{{ auth()->check() ? "✅ Yes" : "❌ No" }}',
            'User ID': '{{ auth()->id() ?? "N/A" }}',
            'CSRF Token': document.querySelector('meta[name="csrf-token"]') ? '✅ Found' : '❌ Not found'
        };

        let html = '<h6>Debug Information:</h6><ul class="list-unstyled">';
        for (const [key, value] of Object.entries(info)) {
            html += `<li><strong>${key}:</strong> ${value}</li>`;
        }
        html += '</ul>';

        debugInfo.innerHTML = html;
        console.log('Debug info:', info);
    }

    async function testAPI() {
        const resultsDiv = document.getElementById('apiResults');
        resultsDiv.innerHTML = '<p class="text-info">Đang tải conversations...</p>';

        try {
            const response = await fetch('/api/conversations', {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok) {
                resultsDiv.innerHTML = `
                    <h6 class="text-success">✅ API Success</h6>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>Conversations:</strong> ${data.data ? data.data.length : 0}</p>
                    <pre class="small">${JSON.stringify(data, null, 2)}</pre>
                `;
            } else {
                resultsDiv.innerHTML = `
                    <h6 class="text-danger">❌ API Error</h6>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <pre class="small">${JSON.stringify(data, null, 2)}</pre>
                `;
            }
        } catch (error) {
            resultsDiv.innerHTML = `
                <h6 class="text-danger">❌ Network Error</h6>
                <p>${error.message}</p>
            `;
        }
    }

    async function testUserSearch() {
        const resultsDiv = document.getElementById('apiResults');
        resultsDiv.innerHTML = '<p class="text-info">Đang tìm kiếm users...</p>';

        try {
            const response = await fetch('/api/search/users?q=test', {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok) {
                resultsDiv.innerHTML = `
                    <h6 class="text-success">✅ User Search Success</h6>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>Users found:</strong> ${data.data ? data.data.length : 0}</p>
                    <pre class="small">${JSON.stringify(data, null, 2)}</pre>
                `;
            } else {
                resultsDiv.innerHTML = `
                    <h6 class="text-danger">❌ User Search Error</h6>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <pre class="small">${JSON.stringify(data, null, 2)}</pre>
                `;
            }
        } catch (error) {
            resultsDiv.innerHTML = `
                <h6 class="text-danger">❌ Network Error</h6>
                <p>${error.message}</p>
            `;
        }
    }

    // Chat functionality
    let currentConversationId = null;
    let conversations = [];

    async function loadConversations() {
        try {
            const response = await fetch('/api/conversations', {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok && data.data) {
                conversations = data.data;
                displayConversations(conversations);

                // Auto-select first conversation if available
                if (conversations.length > 0) {
                    selectConversation(conversations[0].id);
                }
            }
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }

    function displayConversations(convs) {
        const apiResults = document.getElementById('apiResults');

        if (convs.length === 0) {
            apiResults.innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-comments fa-2x mb-2"></i>
                    <p class="mb-0">Chưa có cuộc trò chuyện nào</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="createTestConversation()">
                        Tạo conversation test
                    </button>
                </div>
            `;
            return;
        }

        let html = '<h6>📋 Conversations:</h6>';
        convs.forEach((conv, index) => {
            const otherUser = conv.other_participant;
            const lastMessage = conv.last_message;

            html += `
                <div class="conversation-item p-2 border rounded mb-2 ${currentConversationId === conv.id ? 'bg-primary text-white' : 'bg-white'}"
                     style="cursor: pointer;" onclick="selectConversation(${conv.id})">
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <i class="fas fa-user-circle fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">${otherUser ? otherUser.name : 'Unknown User'}</div>
                            <div class="text-muted small text-truncate">
                                ${lastMessage ? lastMessage.content : 'Chưa có tin nhắn'}
                            </div>
                        </div>
                        ${conv.unread_count > 0 ? `<span class="badge bg-danger">${conv.unread_count}</span>` : ''}
                    </div>
                </div>
            `;
        });

        apiResults.innerHTML = html;
    }

    async function selectConversation(conversationId) {
        currentConversationId = conversationId;

        // Update UI
        displayConversations(conversations);

        // Enable message input
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendMessageBtn');

        messageInput.disabled = false;
        sendBtn.disabled = false;
        messageInput.placeholder = 'Nhập tin nhắn...';

        // Load messages
        await loadMessages(conversationId);

        // Setup send message handler
        setupMessageSending();
    }

    async function loadMessages(conversationId) {
        try {
            const response = await fetch(`/api/conversations/${conversationId}/messages`, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok && data.data) {
                displayMessages(data.data);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    function displayMessages(messages) {
        const messagesDiv = document.getElementById('chatMessages');
        const currentUserId = '{{ auth()->id() }}';

        if (messages.length === 0) {
            messagesDiv.innerHTML = `
                <div class="text-center text-muted small">
                    <i class="fas fa-comment-dots"></i>
                    <p class="mb-0">Chưa có tin nhắn nào</p>
                </div>
            `;
            return;
        }

        let html = '';
        messages.forEach(message => {
            const isSent = message.user_id == currentUserId;
            const time = new Date(message.created_at).toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });

            html += `
                <div class="message mb-2 ${isSent ? 'text-end' : 'text-start'}">
                    <div class="d-inline-block p-2 rounded ${isSent ? 'bg-primary text-white' : 'bg-light'}"
                         style="max-width: 80%;">
                        <div class="small">${message.content}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">${time}</div>
                    </div>
                </div>
            `;
        });

        messagesDiv.innerHTML = html;
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function setupMessageSending() {
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendMessageBtn');

        // Remove existing listeners
        sendBtn.replaceWith(sendBtn.cloneNode(true));
        const newSendBtn = document.getElementById('sendMessageBtn');

        // Add click handler
        newSendBtn.addEventListener('click', sendMessage);

        // Add enter key handler
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }

    async function sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const content = messageInput.value.trim();

        if (!content || !currentConversationId) {
            return;
        }

        try {
            const response = await fetch(`/api/conversations/${currentConversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ content })
            });

            if (response.ok) {
                messageInput.value = '';
                await loadMessages(currentConversationId);
                await loadConversations(); // Refresh conversations list
            } else {
                alert('Không thể gửi tin nhắn');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Lỗi kết nối');
        }
    }

    async function createTestConversation() {
        try {
            // First, get a list of users to create conversation with
            const response = await fetch('/api/search/users?q=', {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok && data.data && data.data.length > 0) {
                // Find a user that's not the current user
                const currentUserId = '{{ auth()->id() }}';
                const otherUser = data.data.find(user => user.id != currentUserId);

                if (otherUser) {
                    // Create conversation
                    const createResponse = await fetch('/api/conversations', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            recipient_id: otherUser.id,
                            message: 'Chào bạn! Đây là tin nhắn test từ chat widget.'
                        })
                    });

                    if (createResponse.ok) {
                        alert('✅ Đã tạo conversation test thành công!');
                        await loadConversations();
                    } else {
                        alert('❌ Không thể tạo conversation');
                    }
                } else {
                    alert('❌ Không tìm thấy user khác để tạo conversation');
                }
            } else {
                alert('❌ Không thể lấy danh sách users');
            }
        } catch (error) {
            console.error('Error creating test conversation:', error);
            alert('❌ Lỗi kết nối');
        }
    }

    // Auto-load conversations when chat panel opens
    document.addEventListener('DOMContentLoaded', function() {
        // Load conversations when page loads
        setTimeout(() => {
            loadConversations();
        }, 1000);
    });
    </script>
</body>
</html>
