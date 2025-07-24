@extends('layouts.app')

@section('title', 'Trò chuyện với ' . ($otherParticipant->name ?? 'Unknown'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Back to conversations (Mobile) -->
        <div class="col-12 d-md-none mb-3">
            <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Quay lại
            </a>
        </div>

        <!-- Conversations List (Desktop) -->
        <div class="col-lg-4 col-md-5 d-none d-md-block">
            <div class="card h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Tin nhắn
                    </h5>
                    <a href="{{ route('chat.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i>
                        Mới
                    </a>
                </div>

                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <div id="conversationsList">
                        @php
                        // Get conversations for sidebar
                        $sidebarConversations = \App\Models\Conversation::whereHas('participants', function ($query) {
                            $query->where('user_id', Auth::id());
                        })
                        ->with([
                            'participants.user:id,name,email,avatar',
                            'lastMessage:id,conversation_id,user_id,content,created_at'
                        ])
                        ->withCount(['messages as unread_count' => function ($query) {
                            $query->whereHas('conversation.participants', function ($q) {
                                $q->where('user_id', Auth::id())
                                  ->where(function ($q2) {
                                      $q2->whereNull('last_read_at')
                                         ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                                  });
                            });
                        }])
                        ->latest('updated_at')
                        ->get();
                        @endphp

                        @if($sidebarConversations->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($sidebarConversations as $conv)
                                    @php
                                        $otherUser = $conv->participants->where('user_id', '!=', Auth::id())->first()?->user;
                                        $lastMessage = $conv->lastMessage;
                                        $isUnread = $conv->unread_count > 0;
                                        $isActive = $conv->id == $conversation->id;
                                    @endphp

                                    <a href="{{ route('chat.show', $conv->id) }}"
                                       class="list-group-item list-group-item-action {{ $isActive ? 'active' : '' }} {{ $isUnread ? 'bg-light' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <img src="{{ $otherUser->avatar ?? '/images/default-avatar.png' }}"
                                                     class="rounded-circle" width="40" height="40" alt="">
                                            </div>

                                            <div class="flex-grow-1 min-width-0">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 text-truncate {{ $isUnread ? 'fw-bold' : '' }}">
                                                        {{ $otherUser->name ?? 'Unknown User' }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        {{ $lastMessage ? $lastMessage->created_at->diffForHumans() : '' }}
                                                    </small>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <p class="mb-0 text-truncate text-muted small">
                                                        @if($lastMessage)
                                                            @if($lastMessage->user_id == Auth::id())
                                                                <i class="fas fa-reply me-1"></i>
                                                            @endif
                                                            {{ Str::limit($lastMessage->content, 40) }}
                                                        @else
                                                            <em>Chưa có tin nhắn</em>
                                                        @endif
                                                    </p>

                                                    @if($isUnread)
                                                        <span class="badge bg-primary rounded-pill">
                                                            {{ $conv->unread_count }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-4">
                                <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Chưa có cuộc trò chuyện nào</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-lg-8 col-md-7">
            <div class="card h-100">
                <!-- Chat Header -->
                <div class="card-header bg-light d-flex align-items-center">
                    <div class="d-flex align-items-center flex-grow-1">
                        <img src="{{ $otherParticipant->avatar ?? '/images/default-avatar.png' }}"
                             class="rounded-circle me-3" width="40" height="40" alt="">
                        <div>
                            <h6 class="mb-0">{{ $otherParticipant->name ?? 'Unknown User' }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-circle text-muted"></i> Offline
                            </small>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="clearChat()">
                                <i class="fas fa-trash me-2"></i>Xóa cuộc trò chuyện
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="blockUser()">
                                <i class="fas fa-ban me-2"></i>Chặn người dùng
                            </a></li>
                        </ul>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="card-body p-0 d-flex flex-column" style="height: 500px;">
                    <div id="messagesContainer" class="flex-grow-1 p-3" style="overflow-y: auto;">
                        @if($messages->count() > 0)
                            @foreach($messages as $message)
                                @php
                                    $isMine = $message->user_id == auth()->id();
                                @endphp

                                <div class="message-item mb-3 {{ $isMine ? 'text-end' : 'text-start' }}">
                                    <div class="d-inline-block {{ $isMine ? 'bg-primary text-white' : 'bg-light' }}
                                               rounded-3 p-3 position-relative"
                                         style="max-width: 70%;">

                                        @if(!$isMine)
                                            <div class="small text-muted mb-1">
                                                {{ $message->user->name }}
                                            </div>
                                        @endif

                                        <div class="message-content">
                                            {{ $message->content }}
                                        </div>

                                        <div class="message-time small {{ $isMine ? 'text-white-50' : 'text-muted' }} mt-1">
                                            {{ $message->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-comment-dots fa-3x mb-3"></i>
                                <h5>Chưa có tin nhắn nào</h5>
                                <p>Hãy gửi tin nhắn đầu tiên để bắt đầu cuộc trò chuyện</p>
                            </div>
                        @endif
                    </div>

                    <!-- Message Input -->
                    <div class="border-top p-3">
                        <form id="messageForm" action="{{ route('chat.send', $conversation->id) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="content" id="messageInput"
                                       class="form-control" placeholder="Nhập tin nhắn..."
                                       maxlength="1000" required>
                                <button class="btn btn-primary" type="submit" id="sendButton">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.message-item {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message-content {
    word-wrap: break-word;
    white-space: pre-wrap;
}

#messagesContainer {
    scroll-behavior: smooth;
}

.bg-primary .message-time {
    opacity: 0.8;
}

@media (max-width: 768px) {
    .message-item .d-inline-block {
        max-width: 85% !important;
    }
}

.list-group-item-action.active {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: white !important;
}

.list-group-item-action.active .text-muted {
    color: rgba(255, 255, 255, 0.75) !important;
}

.list-group-item-action.bg-light:not(.active) {
    background-color: #e3f2fd !important;
}

.min-width-0 {
    min-width: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');

    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Initial scroll to bottom
    scrollToBottom();

    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const content = messageInput.value.trim();
        if (!content) return;

        // Disable form
        sendButton.disabled = true;
        messageInput.disabled = true;

        // Send message via AJAX
        fetch(messageForm.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: content })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add message to UI
                addMessageToUI(data.message, true);

                // Clear input
                messageInput.value = '';

                // Scroll to bottom
                scrollToBottom();
            } else {
                alert('Không thể gửi tin nhắn. Vui lòng thử lại.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        })
        .finally(() => {
            // Re-enable form
            sendButton.disabled = false;
            messageInput.disabled = false;
            messageInput.focus();
        });
    });

    // Add message to UI
    function addMessageToUI(message, isMine) {
        const messageHtml = `
            <div class="message-item mb-3 ${isMine ? 'text-end' : 'text-start'}">
                <div class="d-inline-block ${isMine ? 'bg-primary text-white' : 'bg-light'}
                           rounded-3 p-3 position-relative"
                     style="max-width: 70%;">

                    ${!isMine ? `<div class="small text-muted mb-1">${message.user.name}</div>` : ''}

                    <div class="message-content">${message.content}</div>

                    <div class="message-time small ${isMine ? 'text-white-50' : 'text-muted'} mt-1">
                        ${new Date(message.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}
                    </div>
                </div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    }

    // Enter key to send
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });

    // Auto-refresh messages every 10 seconds
    setInterval(function() {
        // You can implement message polling here if needed
        // For now, we'll rely on manual refresh
    }, 10000);
});

function clearChat() {
    if (confirm('Bạn có chắc chắn muốn xóa cuộc trò chuyện này?')) {
        // Implement clear chat functionality
        alert('Chức năng này sẽ được phát triển trong phiên bản tiếp theo.');
    }
}

function blockUser() {
    if (confirm('Bạn có chắc chắn muốn chặn người dùng này?')) {
        // Implement block user functionality
        alert('Chức năng này sẽ được phát triển trong phiên bản tiếp theo.');
    }
}
</script>
@endpush
@endsection
