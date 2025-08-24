{{-- Conversation List Component --}}
@props(['conversations', 'currentConversationId' => null])

<div class="conversation-list">
    @if($conversations->count() > 0)
        @foreach($conversations as $conversation)
            <a href="{{ route('dashboard.messages.show', $conversation['id']) }}" 
               class="conversation-item d-block text-decoration-none border-bottom p-3 {{ $conversation['id'] == $currentConversationId ? 'bg-primary bg-opacity-10 border-primary' : '' }} {{ $conversation['unread_count'] > 0 ? 'bg-light' : '' }}">
                <div class="d-flex align-items-center">
                    <!-- Avatar/Icon -->
                    <div class="flex-shrink-0 me-3 position-relative">
                        @if($conversation['is_group'])
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            @if($conversation['member_count'] > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success small">
                                    {{ $conversation['member_count'] }}
                                </span>
                            @endif
                        @else
                            <img src="{{ $conversation['other_participant']['avatar'] ?? '/images/default-avatar.png' }}" 
                                 class="rounded-circle" width="48" height="48" alt="">
                            @if($conversation['other_participant']['is_online'] ?? false)
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                                    <span class="visually-hidden">Online</span>
                                </span>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 text-truncate {{ $conversation['unread_count'] > 0 ? 'fw-bold' : '' }}">
                                {{ $conversation['title'] }}
                            </h6>
                            <div class="text-end">
                                @if($conversation['unread_count'] > 0)
                                    <span class="badge bg-danger rounded-pill small">
                                        {{ $conversation['unread_count'] }}
                                    </span>
                                @endif
                                @if($conversation['last_message'])
                                    <small class="text-muted d-block">
                                        {{ $conversation['last_message']['created_at']->diffForHumans() }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        @if($conversation['is_group'])
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-{{ $conversation['user_role_color'] ?? 'secondary' }} me-2 small">
                                    {{ $conversation['user_role'] ?? 'Thành viên' }}
                                </span>
                                @if($conversation['conversation_type'])
                                    <small class="text-muted">{{ $conversation['conversation_type'] }}</small>
                                @endif
                            </div>
                        @else
                            <small class="text-muted">
                                {{ $conversation['other_participant']['name'] ?? 'Unknown User' }}
                            </small>
                        @endif
                        
                        @if($conversation['last_message'])
                            <p class="mb-0 text-muted small text-truncate">
                                @if($conversation['last_message']['is_system'] ?? false)
                                    <i class="fas fa-info-circle me-1"></i>
                                @endif
                                {{ Str::limit($conversation['last_message']['content'], 60) }}
                            </p>
                        @else
                            <p class="mb-0 text-muted small fst-italic">
                                Chưa có tin nhắn nào
                            </p>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    @else
        <div class="text-center py-4">
            <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem;"></i>
            <h6 class="text-muted">Chưa có cuộc trò chuyện nào</h6>
            <p class="text-muted small">Bắt đầu cuộc trò chuyện đầu tiên</p>
        </div>
    @endif
</div>

<style>
.conversation-item:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
    transition: background-color 0.2s ease;
}

.conversation-item.bg-primary.bg-opacity-10 {
    border-left: 3px solid var(--bs-primary) !important;
}
</style>
