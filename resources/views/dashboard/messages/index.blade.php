@extends('dashboard.layouts.app')

@section('title', 'Tin nhắn')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Tin nhắn
                    </h1>
                    <p class="text-muted mb-0">Quản lý cuộc trò chuyện và nhóm thảo luận</p>
                </div>
                <div class="d-flex gap-2">
                    @if($canCreateGroupConversations ?? true)
                        <a href="{{ route('dashboard.messages.groups.create') }}" class="btn btn-success">
                            <i class="fas fa-users me-1"></i>
                            Tạo nhóm
                        </a>
                    @endif
                    <a href="{{ route('dashboard.messages.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Tin nhắn mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-comments text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng cuộc trò chuyện</h6>
                            <h4 class="mb-0">{{ $stats['total_conversations'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-users text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Nhóm thảo luận</h6>
                            <h4 class="mb-0">{{ $stats['group_conversations'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-user text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Trò chuyện riêng</h6>
                            <h4 class="mb-0">{{ $stats['private_conversations'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-bell text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tin nhắn chưa đọc</h6>
                            <h4 class="mb-0">{{ $stats['unread_messages'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.messages.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm theo tên hoặc nội dung..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Lọc theo</label>
                            <select name="filter" class="form-select">
                                <option value="all" {{ request('filter') === 'all' ? 'selected' : '' }}>Tất cả</option>
                                <option value="groups" {{ request('filter') === 'groups' ? 'selected' : '' }}>Nhóm thảo luận</option>
                                <option value="private" {{ request('filter') === 'private' ? 'selected' : '' }}>Trò chuyện riêng</option>
                                <option value="unread" {{ request('filter') === 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>
                                    Tìm kiếm
                                </button>
                                <a href="{{ route('dashboard.messages.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Danh sách cuộc trò chuyện
                        @if($conversations->count() > 0)
                            <span class="badge bg-primary ms-2">{{ $conversations->count() }}</span>
                        @endif
                    </h5>
                </div>
                
                <div class="card-body p-0">
                    @if($conversations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($conversations as $conversation)
                                <a href="{{ route('dashboard.messages.show', $conversation['id']) }}" 
                                   class="list-group-item list-group-item-action border-0 {{ $conversation['unread_count'] > 0 ? 'bg-light' : '' }}">
                                    <div class="d-flex align-items-center p-3">
                                        <!-- Avatar/Icon -->
                                        <div class="flex-shrink-0 me-3 position-relative">
                                            @if($conversation['is_group'])
                                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-users text-primary fs-5"></i>
                                                </div>
                                                @if($conversation['member_count'] > 0)
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                        {{ $conversation['member_count'] }}
                                                    </span>
                                                @endif
                                            @else
                                                <img src="{{ $conversation['other_participant']['avatar'] ?? '/images/default-avatar.png' }}" 
                                                     class="rounded-circle" width="50" height="50" alt="">
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
                                                <div class="flex-grow-1 min-width-0">
                                                    <h6 class="mb-0 text-truncate {{ $conversation['unread_count'] > 0 ? 'fw-bold' : '' }}">
                                                        {{ $conversation['title'] }}
                                                    </h6>
                                                    @if($conversation['is_group'])
                                                        <div class="d-flex align-items-center mt-1">
                                                            <span class="badge bg-{{ $conversation['user_role_color'] ?? 'secondary' }} me-2">
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
                                                </div>
                                                
                                                <div class="text-end">
                                                    @if($conversation['unread_count'] > 0)
                                                        <span class="badge bg-danger rounded-pill">
                                                            {{ $conversation['unread_count'] }}
                                                        </span>
                                                    @endif
                                                    @if($conversation['last_message'])
                                                        <small class="text-muted d-block mt-1">
                                                            {{ $conversation['last_message']['created_at']->diffForHumans() }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($conversation['last_message'])
                                                <p class="mb-0 text-muted small text-truncate">
                                                    @if($conversation['last_message']['is_system'] ?? false)
                                                        <i class="fas fa-info-circle me-1"></i>
                                                    @endif
                                                    {{ Str::limit($conversation['last_message']['content'], 100) }}
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
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-comments text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted mb-3">Chưa có cuộc trò chuyện nào</h5>
                            <p class="text-muted mb-4">Bắt đầu cuộc trò chuyện đầu tiên của bạn</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('dashboard.messages.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Tin nhắn mới
                                </a>
                                @if($canCreateGroupConversations ?? true)
                                    <a href="{{ route('dashboard.messages.groups.create') }}" class="btn btn-success">
                                        <i class="fas fa-users me-1"></i>
                                        Tạo nhóm
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Group Requests Section (if any) -->
    @if(isset($groupRequests) && $groupRequests->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Yêu cầu tạo nhóm của bạn
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($groupRequests as $request)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">{{ $request['title'] }}</h6>
                                            <span class="badge bg-{{ $request['status_color'] }}">
                                                {{ $request['status_label'] }}
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-2">{{ $request['conversation_type'] }}</p>
                                        <small class="text-muted">
                                            Gửi lúc: {{ $request['requested_at']->format('d/m/Y H:i') }}
                                        </small>
                                        @if($request['can_edit'])
                                            <div class="mt-2">
                                                <a href="{{ route('dashboard.messages.groups.request.edit', $request['id']) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit me-1"></i>
                                                    Chỉnh sửa
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh unread count every 30 seconds
    setInterval(function() {
        // This would be implemented with AJAX to update unread counts
        console.log('Checking for new messages...');
    }, 30000);
    
    // Mark conversations as read when clicked
    document.querySelectorAll('.list-group-item-action').forEach(function(item) {
        item.addEventListener('click', function() {
            const unreadBadge = this.querySelector('.badge.bg-danger');
            if (unreadBadge) {
                unreadBadge.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
