@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    @if($conversations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($conversations as $conversation)
                                <a href="{{ route('conversations.show', $conversation) }}" class="list-group-item list-group-item-action py-3 px-0">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $otherParticipant = $conversation->participants->where('user_id', '!=', Auth::id())->first()->user ?? null;
                                            @endphp
                                            
                                            @if($otherParticipant)
                                                <img src="{{ $otherParticipant->getAvatarUrl() }}" alt="{{ $otherParticipant->name }}" class="rounded-circle me-3" width="50" height="50">
                                            @else
                                                <div class="rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-people-fill text-white"></i>
                                                </div>
                                            @endif
                                            
                                            <div>
                                                <h6 class="mb-1">
                                                    @if($conversation->hasUnreadMessages(Auth::id()))
                                                        <span class="badge bg-primary me-2">{{ $conversation->unreadMessagesCount(Auth::id()) }}</span>
                                                    @endif
                                                    
                                                    {{ $otherParticipant->name ?? $conversation->title ?? __('Conversation') }}
                                                </h6>
                                                <p class="mb-1 text-truncate" style="max-width: 500px;">
                                                    {{ $conversation->lastMessage->content ?? __('No messages yet') }}
                                                </p>
                                                <small class="text-muted">
                                                    {{ $conversation->lastMessage ? $conversation->lastMessage->created_at->diffForHumans() : $conversation->created_at->diffForHumans() }}
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
                            <i class="bi bi-chat-dots fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('You don\'t have any conversations yet.') }}</p>
                            <p class="text-muted">{{ __('Start a new conversation to connect with other users.') }}</p>
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                {{ __('Start a Conversation') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- New Conversation Modal -->
    <div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('conversations.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="newConversationModalLabel">{{ __('New Conversation') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="recipient_id" class="form-label">{{ __('Recipient') }}</label>
                            <select name="recipient_id" id="recipient_id" class="form-select" required>
                                <option value="">{{ __('Select a user') }}</option>
                                @foreach(App\Models\User::where('id', '!=', Auth::id())->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} (@{{ $user->username }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">{{ __('Message') }}</label>
                            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Send Message') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
