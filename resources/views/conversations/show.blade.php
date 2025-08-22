@extends('layouts.app')

@section('title', $conversation->title ?? 'Conversation')

@section('content')

    <div class="py-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                @php
                    $otherParticipant = $conversation->participants->where('user_id', '!=', Auth::id())->first()->user ?? null;
                @endphp

                <div class="d-flex align-items-center">
                    <a href="{{ route('conversations.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="h3 mb-0">
                        @if($conversation->title)
                            {{ $conversation->title }}
                        @elseif($otherParticipant)
                            {{ $otherParticipant->name }}
                        @else
                            {{ __('conversations.conversation') }}
                        @endif
                    </h1>
                </div>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="conversationActions" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="conversationActions">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-plus me-2"></i> {{ __('conversations.invite_participants') }}</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-bell-slash me-2"></i> {{ __('conversations.mute_conversation') }}</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-flag me-2"></i> {{ __('conversations.report') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i> {{ __('conversations.leave_conversation') }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('conversations.messages') }}</h5>
                        <div>
                            <span class="text-muted small">{{ $messages->total() }} {{ __('conversations.messages_count') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="conversation-messages mb-4" style="max-height: 600px; overflow-y: auto;">
                        @if($messages->count() > 0)
                            <div class="d-flex justify-content-center mb-3">
                                {{ $messages->links() }}
                            </div>

                            @php
                                $previousDate = null;
                                $sortedMessages = $messages->sortBy('created_at');
                            @endphp

                            @foreach($sortedMessages as $message)
                                @php
                                    $currentDate = $message->created_at->format('Y-m-d');
                                    $showDateDivider = $previousDate !== $currentDate;
                                    $previousDate = $currentDate;
                                    $isMine = $message->user_id === Auth::id();
                                @endphp

                                @if($showDateDivider)
                                    <div class="message-date-divider my-3 text-center">
                                        <span class="badge bg-light text-dark px-3 py-2">
                                            @if($message->created_at->isToday())
                                                {{ __('conversations.today') }}
                                            @elseif($message->created_at->isYesterday())
                                                {{ __('conversations.yesterday') }}
                                            @else
                                                {{ $message->created_at->format('F j, Y') }}
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                <div class="message-item mb-3 {{ $isMine ? 'text-end' : '' }}">
                                    <div class="d-flex {{ $isMine ? 'justify-content-end' : '' }}">
                                        @if(!$isMine)
                                            <div class="me-2">
                                                <img src="{{ $message->user->getAvatarUrl() }}" alt="{{ $message->user->name }}" class="rounded-circle" width="32" height="32">
                                            </div>
                                        @endif

                                        <div class="d-inline-block p-3 rounded-3 {{ $isMine ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%;">
                                            @if(!$isMine)
                                                <div class="message-sender mb-1">
                                                    <small class="fw-bold">{{ $message->user->name }}</small>
                                                </div>
                                            @endif

                                            <div class="message-content">
                                                {{ $message->content }}
                                            </div>

                                            <div class="message-meta mt-1 text-end">
                                                <small class="{{ $isMine ? 'text-white-50' : 'text-muted' }}">
                                                    {{ $message->created_at->format('g:i A') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-comment fs-1 text-muted mb-3"></i>
                                <p class="mb-0">{{ __('conversations.no_messages_yet') }}</p>
                                <p class="text-muted">{{ __('conversations.send_message_to_start') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="message-form">
                        <form action="{{ route('conversations.messages.store', $conversation) }}" method="POST" id="messageForm">
                            @csrf
                            <div class="input-group">
                                <textarea name="message" id="messageInput" class="form-control" placeholder="{{ __('conversations.type_your_message') }}" required rows="2"></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> {{ __('conversations.send') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Conversation realtime messaging
        const conversationId = {{ $conversation->id }};
        const currentUserId = {{ Auth::id() }};
        let conversationMessages = null;
        let messageInput = null;

        // Scroll to bottom of conversation on page load
        document.addEventListener('DOMContentLoaded', function() {
            conversationMessages = document.querySelector('.conversation-messages');
            messageInput = document.getElementById('messageInput');

            if (conversationMessages) {
                conversationMessages.scrollTop = conversationMessages.scrollHeight;
            }

            // Focus on message input
            if (messageInput) {
                messageInput.focus();
            }

            // Submit form on Ctrl+Enter
            messageInput.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('messageForm').submit();
                }
            });

            // Setup realtime messaging
            setupRealtimeMessaging();
        });

        // Setup realtime messaging with WebSocket
        function setupRealtimeMessaging() {
            // Wait for WebSocket to be ready
            function waitForWebSocket() {
                if (typeof window.MechaMapWebSocket !== 'undefined' &&
                    window.MechaMapWebSocket.isConnected() &&
                    window.MechaMapWebSocket.getSocket()) {

                    console.log('🔔 Setting up realtime messaging for conversation:', conversationId);

                    const socket = window.MechaMapWebSocket.getSocket();

                    // Subscribe to conversation channel
                    socket.emit('subscribe', {
                        channel: `conversation.${conversationId}`
                    });

                    // Listen for new messages
                    window.MechaMapWebSocket.addCallback('conversation-message', function(data) {
                        if (data.message && data.message.conversation_id == conversationId) {
                            handleNewMessage(data.message);
                        }
                    });

                    // Listen for message sent events
                    socket.on('message.sent', function(data) {
                        if (data.message && data.message.conversation_id == conversationId) {
                            handleNewMessage(data.message);
                        }
                    });

                    console.log('✅ Realtime messaging setup complete');
                } else {
                    console.log('⏳ Waiting for WebSocket connection...');
                    setTimeout(waitForWebSocket, 500); // Retry after 500ms
                }
            }

            waitForWebSocket();
        }

        // Handle new message received via WebSocket
        function handleNewMessage(message) {
            // Don't add message if it's from current user (already added by form submission)
            if (message.sender.id == currentUserId) {
                return;
            }

            console.log('📨 New message received:', message);

            // Create message HTML
            const messageHtml = createMessageHtml(message);

            // Add to conversation
            if (conversationMessages) {
                conversationMessages.insertAdjacentHTML('beforeend', messageHtml);
                conversationMessages.scrollTop = conversationMessages.scrollHeight;
            }

            // Show notification if page is not visible
            if (document.hidden) {
                showDesktopNotification(message);
            }
        }

        // Create HTML for new message
        function createMessageHtml(message) {
            const messageTime = new Date(message.created_at).toLocaleString();
            const avatarUrl = message.sender.avatar || '/images/default-avatar.png';

            return `
                <div class="message-item mb-3">
                    <div class="d-flex">
                        <img src="${avatarUrl}" alt="${message.sender.name}" class="rounded-circle me-3" width="40" height="40">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0">${message.sender.name}</h6>
                                <small class="text-muted">${messageTime}</small>
                            </div>
                            <div class="message-content">
                                <p class="mb-0">${message.content}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Show desktop notification for new message
        function showDesktopNotification(message) {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(`Tin nhắn mới từ ${message.sender.name}`, {
                    body: message.content.substring(0, 100),
                    icon: message.sender.avatar || '/images/default-avatar.png'
                });
            }
        }

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    </script>
    @endpush
@endsection
