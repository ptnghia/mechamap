{{-- Message Bubble Component --}}
@props(['message', 'isOwn' => false, 'showAvatar' => true, 'showName' => true])

<div class="message-item mb-3 {{ $isOwn ? 'text-end' : '' }}">
    @if($message->is_system_message)
        <!-- System Message -->
        <div class="text-center">
            <small class="text-muted bg-light rounded px-3 py-1 d-inline-block">
                <i class="fas fa-info-circle me-1"></i>
                {{ $message->content }}
            </small>
            <div class="small text-muted mt-1">
                {{ $message->created_at->format('H:i') }}
            </div>
        </div>
    @else
        <!-- User Message -->
        <div class="d-flex {{ $isOwn ? 'justify-content-end' : '' }}">
            @if(!$isOwn && $showAvatar)
                <div class="flex-shrink-0 me-2">
                    <img src="{{ $message->user->avatar ?? '/images/default-avatar.png' }}" 
                         class="rounded-circle" width="32" height="32" alt="{{ $message->user->name }}">
                </div>
            @endif
            
            <div class="flex-grow-1" style="max-width: 70%;">
                @if(!$isOwn && $showName)
                    <div class="small text-muted mb-1">{{ $message->user->name }}</div>
                @endif
                
                <div class="message-bubble {{ $isOwn ? 'bg-primary text-white' : 'bg-light' }} rounded-3 px-3 py-2 position-relative">
                    {{ $message->content }}
                    
                    <!-- Message status indicators for own messages -->
                    @if($isOwn)
                        <div class="message-status position-absolute bottom-0 end-0 me-2 mb-1">
                            <i class="fas fa-check text-white-50 small"></i>
                        </div>
                    @endif
                </div>
                
                <div class="small text-muted mt-1 d-flex {{ $isOwn ? 'justify-content-end' : '' }}">
                    <span title="{{ $message->created_at->format('d/m/Y H:i:s') }}">
                        {{ $message->created_at->format('H:i') }}
                    </span>
                    @if($isOwn)
                        <span class="ms-2">
                            <i class="fas fa-check-double text-muted small"></i>
                        </span>
                    @endif
                </div>
            </div>
            
            @if($isOwn && $showAvatar)
                <div class="flex-shrink-0 ms-2">
                    <img src="{{ $message->user->avatar ?? '/images/default-avatar.png' }}" 
                         class="rounded-circle" width="32" height="32" alt="{{ $message->user->name }}">
                </div>
            @endif
        </div>
    @endif
</div>

<style>
.message-bubble {
    word-wrap: break-word;
    position: relative;
}

.message-bubble.bg-primary {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #0056b3 100%) !important;
}

.message-bubble.bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef;
}

.message-status {
    opacity: 0.7;
}

/* Message animations */
.message-item {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover effects */
.message-bubble:hover .message-status {
    opacity: 1;
}
</style>
