@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <div class="conversation-messages mb-4" style="max-height: 500px; overflow-y: auto;">
                        @if($messages->count() > 0)
                            <div class="d-flex justify-content-center mb-3">
                                {{ $messages->links() }}
                            </div>
                            
                            @foreach($messages->sortBy('created_at') as $message)
                                <div class="message-item mb-3 {{ $message->user_id === Auth::id() ? 'text-end' : '' }}">
                                    <div class="d-inline-block p-3 rounded-3 {{ $message->user_id === Auth::id() ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%;">
                                        <div class="message-content">
                                            {{ $message->content }}
                                        </div>
                                        <div class="message-meta mt-1">
                                            <small class="{{ $message->user_id === Auth::id() ? 'text-white-50' : 'text-muted' }}">
                                                {{ $message->created_at->format('M d, Y g:i A') }}
                                            </small>
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
                        <form action="{{ route('conversations.messages.store', $conversation) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <textarea name="message" class="form-control" placeholder="{{ __('Type your message...') }}" required></textarea>
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
        });
    </script>
    @endpush
@endsection
