@extends('admin.layouts.dason')

@section('title', 'Trò chuyện - Admin Chat')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @php
                    $otherUser = $conversation->participants->where('user_id', '!=', Auth::id())->first()?->user;
                @endphp
                <h4 class="mb-sm-0 font-size-18">Trò chuyện với {{ $otherUser->name ?? 'Unknown User' }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.chat.index') }}">Chat</a></li>
                        <li class="breadcrumb-item active">Trò chuyện</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Chat Header -->
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <img src="{{ $otherUser->getAvatarUrl() }}"
                             class="rounded-circle me-3" width="40" height="40" alt=""
                             onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($otherUser->name, 0, 1))]) }}'">
                        <div>
                            <h5 class="mb-0">{{ $otherUser->name ?? 'Unknown User' }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-circle text-secondary me-1"></i>Offline
                            </small>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-outline-primary btn-sm" id="refreshBtn">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="card-body p-0">
                    <div id="messagesContainer" style="height: 500px; overflow-y: auto; padding: 1rem;">
                        @foreach($conversation->messages as $message)
                            <div class="message-item mb-3 {{ $message->user_id == Auth::id() ? 'text-end' : 'text-start' }}">
                                <div class="d-inline-block" style="max-width: 70%;">
                                    @if($message->user_id != Auth::id())
                                        <div class="d-flex align-items-start">
                                            <img src="{{ $message->user->getAvatarUrl() }}"
                                                 class="rounded-circle me-2" width="30" height="30" alt=""
                                                 onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($message->user->name, 0, 1))]) }}'">
                                            <div>
                                                <div class="fw-medium text-muted small">{{ $message->user->name }}</div>
                                                <div class="bg-light p-3 rounded">
                                                    {{ $message->content }}
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    {{ $message->created_at->format('H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <div class="bg-primary text-white p-3 rounded">
                                                {{ $message->content }}
                                            </div>
                                            <div class="text-muted small mt-1">
                                                {{ $message->created_at->format('H:i') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Message Input -->
                <div class="card-footer">
                    <form id="messageForm" class="d-flex align-items-center">
                        @csrf
                        <div class="flex-grow-1 me-2">
                            <textarea class="form-control"
                                      id="messageInput"
                                      placeholder="Nhập tin nhắn..."
                                      rows="1"
                                      style="resize: none; min-height: 38px; max-height: 120px;"
                                      maxlength="1000"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="sendBtn" disabled>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.message-item .d-inline-block {
    max-width: 70%;
}

.message-item.text-end .bg-primary {
    border-bottom-right-radius: 0.25rem !important;
}

.message-item.text-start .bg-light {
    border-bottom-left-radius: 0.25rem !important;
}

#messagesContainer {
    scroll-behavior: smooth;
}

#messageInput {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

#messageInput:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

@media (max-width: 768px) {
    .message-item .d-inline-block {
        max-width: 85% !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const messagesContainer = document.getElementById('messagesContainer');
    const refreshBtn = document.getElementById('refreshBtn');
    const conversationId = {{ $conversation->id }};

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';

        // Enable/disable send button
        sendBtn.disabled = this.value.trim() === '';
    });

    // Send message on Enter (Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) {
                messageForm.dispatchEvent(new Event('submit'));
            }
        }
    });

    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const content = messageInput.value.trim();
        if (!content) return;

        // Disable form while sending
        sendBtn.disabled = true;
        messageInput.disabled = true;

        // Send message via AJAX
        fetch(`{{ route('admin.chat.send', $conversation->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ content: content })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add message to UI
                addMessageToUI(data.message);

                // Clear input
                messageInput.value = '';
                messageInput.style.height = 'auto';

                // Scroll to bottom
                scrollToBottom();
            } else {
                alert('Có lỗi xảy ra khi gửi tin nhắn');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi gửi tin nhắn');
        })
        .finally(() => {
            // Re-enable form
            messageInput.disabled = false;
            messageInput.focus();
        });
    });

    // Add message to UI
    function addMessageToUI(message) {
        const messageHtml = `
            <div class="message-item mb-3 text-end">
                <div class="d-inline-block" style="max-width: 70%;">
                    <div>
                        <div class="bg-primary text-white p-3 rounded">
                            ${message.content}
                        </div>
                        <div class="text-muted small mt-1">
                            ${message.created_at}
                        </div>
                    </div>
                </div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    }

    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Refresh messages
    refreshBtn.addEventListener('click', function() {
        location.reload();
    });

    // Auto-scroll to bottom on page load
    scrollToBottom();

    // Focus on message input
    messageInput.focus();
});
</script>
@endsection
