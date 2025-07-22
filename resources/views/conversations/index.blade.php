@extends('layouts.app')

@section('title', 'Conversations')

@section('content')

<div class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Conversations</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                <i class="fas fa-edit-square me-1"></i> Start conversation
            </button>
        </div>

        <div class="card shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Messages</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                            id="filtersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i> Filters
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filtersDropdown">
                            <li><a class="dropdown-item" href="{{ route('conversations.index') }}">All messages</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('conversations.index', ['filter' => 'unread']) }}">Unread only</a>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('conversations.index', ['filter' => 'started']) }}">{{
                                    __('forum.threads.started_by_me') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($conversations->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($conversations as $conversation)
                    <a href="{{ route('conversations.show', $conversation) }}"
                        class="list-group-item list-group-item-action py-3 px-3 {{ $conversation->hasUnreadMessages(Auth::id()) ? 'bg-light' : '' }}">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @php
                                $otherParticipant = $conversation->participants->where('user_id', '!=',
                                Auth::id())->first()->user ?? null;
                                @endphp

                                @if($otherParticipant)
                                <img src="{{ $otherParticipant->getAvatarUrl() }}" alt="{{ $otherParticipant->name }}"
                                    class="rounded-circle me-3" width="50" height="50">
                                @else
                                <div class="rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-users-fill text-white"></i>
                                </div>
                                @endif

                                <div>
                                    <h6 class="mb-1 d-flex align-items-center">
                                        @if($conversation->hasUnreadMessages(Auth::id()))
                                        <span class="badge bg-primary me-2">{{
                                            $conversation->unreadMessagesCount(Auth::id()) }}</span>
                                        @endif

                                        <span
                                            class="{{ $conversation->hasUnreadMessages(Auth::id()) ? 'fw-bold' : '' }}">{{
                                            $otherParticipant->name ?? $conversation->title ?? __('conversations.Conversation')
                                            }}</span>
                                    </h6>
                                    <p class="mb-1 text-truncate" style="max-width: 500px;">
                                        @if($conversation->lastMessage && $conversation->lastMessage->user_id ==
                                        Auth::id())
                                        <span class="text-muted">You:</span>
                                        @elseif($conversation->lastMessage && $otherParticipant)
                                        <span class="text-muted">{{ $otherParticipant->name }}:</span>
                                        @endif
                                        {{ $conversation->lastMessage->content ?? __('conversations.No messages yet') }}
                                    </p>
                                    <small class="text-muted">
                                        {{ $conversation->lastMessage ?
                                        $conversation->lastMessage->created_at->diffForHumans() :
                                        $conversation->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $conversations->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-comment-dots fs-1 text-muted mb-3"></i>
                    <p class="mb-0">{{ __('conversations.There are no conversations to display.') }}</p>
                    <p class="text-muted">{{ __('conversations.Start a new conversation to connect with other users.') }}</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                        data-bs-target="#newConversationModal">
                        <i class="fas fa-edit-square me-1"></i> {{ __('conversations.Start conversation') }}
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- New Conversation Modal -->
<div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('conversations.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="newConversationModalLabel">{{ __('conversations.Start conversation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="recipient_id" class="form-label fw-medium">{{ __('conversations.Recipients') }}</label>
                        <p class="text-muted small mb-2">{{ __('conversations.You may enter multiple names here.') }}</p>
                        <select name="recipient_id" id="recipient_id" class="form-select" required>
                            <option value="">{{ __('conversations.Select a user') }}</option>
                            @foreach(App\Models\User::where('id', '!=', Auth::id())->orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} (@{{ $user->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="title" class="form-label fw-medium">{{ __('conversations.Title') }}</label>
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="{{ __('conversations.Conversation title...') }}">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label fw-medium">{{ __('conversations.Message') }}</label>
                        <textarea name="message" id="message" class="form-control" rows="8"
                            placeholder="{{ __('conversations.Your message...') }}" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allow_invite" id="allow_invite"
                                value="1">
                            <label class="form-check-label" for="allow_invite">
                                {{ __('conversations.Allow anyone in the conversation to invite others') }}
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_conversation"
                                id="lock_conversation" value="1">
                            <label class="form-check-label" for="lock_conversation">
                                {{ __('conversations.Lock conversation (no responses will be allowed)') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('conversations.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> {{ __('conversations.Start conversation') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
