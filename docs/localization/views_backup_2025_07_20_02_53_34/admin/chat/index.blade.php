@extends('admin.layouts.dason')

@section('title', 'Admin Chat')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Admin Chat</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Chat</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-comments me-2"></i>Tin nhắn
                        </h4>
                        <a href="{{ route('admin.chat.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Tin nhắn mới
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($conversations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($conversations as $conversation)
                                @php
                                    $otherUser = $conversation->participants->where('user_id', '!=', Auth::id())->first()?->user;
                                    $lastMessage = $conversation->lastMessage;
                                    $isUnread = $conversation->unread_count > 0;
                                @endphp

                                <a href="{{ route('admin.chat.show', $conversation->id) }}"
                                   class="list-group-item list-group-item-action {{ $isUnread ? 'bg-light' : '' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="{{ $otherUser->getAvatarUrl() }}"
                                                 class="rounded-circle" width="50" height="50" alt=""
                                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($otherUser->name, 0, 1))) }}&background=6366f1&color=fff&size=50'">
                                        </div>

                                        <div class="flex-grow-1 min-width-0">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 {{ $isUnread ? 'fw-bold' : '' }}">
                                                    {{ $otherUser->name ?? 'Unknown User' }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $lastMessage ? $lastMessage->created_at->diffForHumans() : '' }}
                                                </small>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-truncate text-muted">
                                                    @if($lastMessage)
                                                        @if($lastMessage->user_id == Auth::id())
                                                            <i class="fas fa-reply me-1"></i>
                                                        @endif
                                                        {{ Str::limit($lastMessage->content, 50) }}
                                                    @else
                                                        <em>Chưa có tin nhắn</em>
                                                    @endif
                                                </p>

                                                @if($isUnread)
                                                    <span class="badge bg-primary rounded-pill">
                                                        {{ $conversation->unread_count }}
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
                            <p class="text-muted mb-3">Bắt đầu trò chuyện với thành viên bằng cách tạo tin nhắn mới</p>
                            <a href="{{ route('admin.chat.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Tạo tin nhắn mới
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.min-width-0 {
    min-width: 0;
}

.list-group-item-action.bg-light {
    background-color: #e3f2fd !important;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto refresh unread count every 30 seconds
    setInterval(function() {
        fetch('{{ route("admin.chat.api.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                // Update unread count in header if exists
                const unreadBadge = document.querySelector('.unread-count');
                if (unreadBadge && data.count > 0) {
                    unreadBadge.textContent = data.count;
                    unreadBadge.style.display = 'inline';
                } else if (unreadBadge) {
                    unreadBadge.style.display = 'none';
                }
            })
            .catch(error => console.log('Error fetching unread count:', error));
    }, 30000);
});
</script>
@endsection
