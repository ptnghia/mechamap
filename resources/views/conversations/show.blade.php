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
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h1 class="h3 mb-0">
                        @if($conversation->title)
                            {{ $conversation->title }}
                        @elseif($otherParticipant)
                            {{ $otherParticipant->name }}
                        @else
                            {{ __('Conversation') }}
                        @endif
                    </h1>
                </div>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="conversationActions" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="conversationActions">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-plus me-2"></i> {{ __('Invite participants') }}</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-bell-slash me-2"></i> {{ __('Mute conversation') }}</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-flag me-2"></i> {{ __('Report') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> {{ __('Leave conversation') }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Messages') }}</h5>
                        <div>
                            <span class="text-muted small">{{ $messages->total() }} {{ __('messages') }}</span>
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
                                                {{ __('Today') }}
                                            @elseif($message->created_at->isYesterday())
                                                {{ __('Yesterday') }}
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
                                <i class="bi bi-chat fs-1 text-muted mb-3"></i>
                                <p class="mb-0">{{ __('No messages yet.') }}</p>
                                <p class="text-muted">{{ __('Send a message to start the conversation.') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="message-form">
                        <form action="{{ route('conversations.messages.store', $conversation) }}" method="POST" id="messageForm">
                            @csrf
                            <div class="input-group">
                                <textarea name="message" id="messageInput" class="form-control" placeholder="{{ __('Type your message...') }}" required rows="2"></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> {{ __('Send') }}
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
        // Scroll to bottom of conversation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const conversationMessages = document.querySelector('.conversation-messages');
            if (conversationMessages) {
                conversationMessages.scrollTop = conversationMessages.scrollHeight;
            }

            // Focus on message input
            const messageInput = document.getElementById('messageInput');
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
        });
    </script>
    @endpush
@endsection
