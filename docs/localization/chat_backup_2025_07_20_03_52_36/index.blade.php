@extends('layouts.app')

@section('title', 'Tin nhắn')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Conversations List -->
        <div class="col-lg-4 col-md-5">
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
                
                <div class="card-body p-0">
                    @if($conversations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($conversations as $conversation)
                                @php
                                    $otherUser = $conversation['other_participant'];
                                    $lastMessage = $conversation['last_message'];
                                    $isUnread = $conversation['unread_count'] > 0;
                                @endphp
                                
                                <a href="{{ route('chat.show', $conversation['id']) }}" 
                                   class="list-group-item list-group-item-action {{ $isUnread ? 'bg-light' : '' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="{{ $otherUser['avatar'] ?? '/images/default-avatar.png' }}" 
                                                 class="rounded-circle" width="50" height="50" alt="">
                                            @if($otherUser['is_online'] ?? false)
                                                <span class="position-absolute translate-middle p-1 bg-success border border-light rounded-circle" 
                                                      style="top: 40px; left: 45px;">
                                                    <span class="visually-hidden">Online</span>
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 text-truncate {{ $isUnread ? 'fw-bold' : '' }}">
                                                    {{ $otherUser['name'] ?? 'Unknown User' }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $lastMessage ? $lastMessage['created_at']->diffForHumans() : '' }}
                                                </small>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-truncate text-muted small">
                                                    @if($lastMessage)
                                                        @if($lastMessage['is_mine'])
                                                            <i class="fas fa-reply me-1"></i>
                                                        @endif
                                                        {{ Str::limit($lastMessage['content'], 50) }}
                                                    @else
                                                        <em>Chưa có tin nhắn</em>
                                                    @endif
                                                </p>
                                                
                                                @if($isUnread)
                                                    <span class="badge bg-primary rounded-pill">
                                                        {{ $conversation['unread_count'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có cuộc trò chuyện nào</h5>
                            <p class="text-muted mb-3">Bắt đầu trò chuyện với các thành viên khác trong cộng đồng</p>
                            <a href="{{ route('chat.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Bắt đầu trò chuyện
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Chat Area -->
        <div class="col-lg-8 col-md-7">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <i class="fas fa-comment-dots fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Chọn một cuộc trò chuyện</h4>
                        <p class="text-muted">Chọn một cuộc trò chuyện từ danh sách bên trái để bắt đầu nhắn tin</p>
                        
                        @if($conversations->count() == 0)
                            <a href="{{ route('chat.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>
                                Tạo cuộc trò chuyện mới
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.min-width-0 {
    min-width: 0;
}

.list-group-item-action:hover {
    background-color: #f8f9fa !important;
}

.list-group-item-action.bg-light {
    background-color: #e3f2fd !important;
}

.position-absolute {
    position: absolute !important;
}

@media (max-width: 768px) {
    .col-md-5, .col-md-7 {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh unread count every 30 seconds
    setInterval(function() {
        fetch('/api/chat/unread-count')
            .then(response => response.json())
            .then(data => {
                // Update notification badge if exists
                const badge = document.querySelector('.notification-badge');
                if (badge && data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('d-none');
                } else if (badge) {
                    badge.classList.add('d-none');
                }
            })
            .catch(error => console.log('Error fetching unread count:', error));
    }, 30000);
});
</script>
@endpush
@endsection
