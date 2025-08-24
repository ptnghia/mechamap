@extends('dashboard.layouts.app')

@section('title', 'Quản lý nhóm thảo luận')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/group-management.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard-mobile.css') }}">
@endpush

@section('content')
<div class="container-fluid groups-search-container group-container">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center flex-mobile-column align-items-mobile-start">
                    <div class="mb-mobile-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard.messages.index') }}">
                                        <i class="fas fa-comments me-1"></i>
                                        Tin nhắn
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">Nhóm thảo luận</li>
                            </ol>
                        </nav>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-users text-primary me-2"></i>
                            Quản lý nhóm thảo luận
                        </h1>
                        <p class="text-muted mb-0">Quản lý các nhóm thảo luận bạn tham gia và yêu cầu tạo nhóm</p>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('dashboard.messages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                        <a href="{{ route('dashboard.messages.groups.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>
                            Tạo nhóm mới
                        </a>
                    </div>
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
                                <i class="fas fa-users text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng nhóm</h6>
                            <h4 class="mb-0">{{ $stats['total_groups'] ?? 0 }}</h4>
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
                                <i class="fas fa-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Nhóm hoạt động</h6>
                            <h4 class="mb-0">{{ $stats['active_groups'] ?? 0 }}</h4>
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
                                <i class="fas fa-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Chờ duyệt</h6>
                            <h4 class="mb-0">{{ $stats['pending_requests'] ?? 0 }}</h4>
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
                                <i class="fas fa-thumbs-up text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Đã duyệt</h6>
                            <h4 class="mb-0">{{ $stats['approved_requests'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Group Conversations -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-comments text-primary me-2"></i>
                            Nhóm thảo luận của bạn
                        </h5>
                        <!-- Mobile Search -->
                        <div class="groups-search-mobile d-lg-none">
                            <div class="search-input-group">
                                <input type="text" class="form-control group-search-input" placeholder="Tìm kiếm nhóm...">
                                <button type="button" class="btn-search">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <select class="form-select filter-control" data-filter-type="role">
                                    <option value="">Tất cả vai trò</option>
                                    <option value="creator">Người tạo</option>
                                    <option value="admin">Quản trị viên</option>
                                    <option value="member">Thành viên</option>
                                </select>
                                <button type="button" class="btn btn-outline-primary btn-advanced-search">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Desktop Search -->
                        <div class="d-none d-lg-flex gap-2">
                            <input type="text" class="form-control form-control-sm group-search-input" placeholder="Tìm kiếm nhóm..." style="width: 200px;">
                            <select class="form-select form-select-sm filter-control" data-filter-type="role" style="width: 150px;">
                                <option value="">Tất cả</option>
                                <option value="creator">Người tạo</option>
                                <option value="admin">Quản trị viên</option>
                                <option value="member">Thành viên</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-advanced-search">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Suggestions -->
                <div class="search-suggestions position-absolute bg-white border rounded shadow-sm" style="display: none; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto;"></div>

                <!-- Active Filters -->
                <div class="active-filters mb-3" style="display: none;"></div>

                <!-- Advanced Search Panel -->
                <div class="advanced-search-panel card mb-3" style="display: none;">
                    <div class="card-body">
                        <h6 class="card-title">Tìm kiếm nâng cao</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Danh mục</label>
                                <select class="form-select filter-control" data-filter-type="category">
                                    <option value="">Tất cả danh mục</option>
                                    <option value="technical">Kỹ thuật</option>
                                    <option value="general">Tổng quát</option>
                                    <option value="project">Dự án</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quyền riêng tư</label>
                                <select class="form-select filter-control" data-filter-type="privacy">
                                    <option value="">Tất cả</option>
                                    <option value="public">Công khai</option>
                                    <option value="private">Riêng tư</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số thành viên</label>
                                <select class="form-select filter-control" data-filter-type="member_count">
                                    <option value="">Tất cả</option>
                                    <option value="small">1-10 thành viên</option>
                                    <option value="medium">11-50 thành viên</option>
                                    <option value="large">50+ thành viên</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary btn-apply-advanced">Áp dụng</button>
                            <button type="button" class="btn btn-outline-secondary btn-clear-advanced">Xóa</button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(isset($groupConversations) && $groupConversations->count() > 0)
                        <div class="list-group list-group-flush groups-list">
                            @foreach($groupConversations as $conversation)
                                <div class="list-group-item list-group-item-action border-0" data-group-id="{{ $conversation->id }}">
                                    <!-- Mobile Layout -->
                                    <div class="group-item-mobile d-lg-none">
                                        <div class="group-item-header">
                                            <div class="group-item-avatar bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fas fa-users text-primary"></i>
                                            </div>
                                            <div class="group-item-info">
                                                <h6 class="group-item-title mb-1">
                                                    <a href="{{ route('dashboard.messages.show', $conversation->id) }}" class="text-decoration-none">
                                                        {{ $conversation->title }}
                                                    </a>
                                                </h6>
                                                <div class="group-item-meta">
                                                    <span>
                                                        <i class="fas fa-users me-1"></i>
                                                        <span class="member-count">{{ $conversation->members_count ?? 0 }}</span>
                                                    </span>
                                                    <span class="badge bg-{{ $conversation->user_role === 'Người tạo' ? 'success' : ($conversation->user_role === 'Quản trị viên' ? 'warning' : 'secondary') }}">
                                                        {{ $conversation->user_role ?? 'Thành viên' }}
                                                    </span>
                                                    @if($conversation->unread_count > 0)
                                                        <span class="badge bg-danger rounded-pill">{{ $conversation->unread_count }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('dashboard.messages.show', $conversation->id) }}">
                                                            <i class="fas fa-eye me-2"></i>Xem chi tiết
                                                        </a>
                                                    </li>
                                                    @if(in_array($conversation->user_role, ['Người tạo', 'Quản trị viên']))
                                                        <li>
                                                            <a class="dropdown-item btn-manage-members" href="#" data-conversation-id="{{ $conversation->id }}">
                                                                <i class="fas fa-users-cog me-2"></i>Quản lý thành viên
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="leaveGroup({{ $conversation->id }})">
                                                            <i class="fas fa-sign-out-alt me-2"></i>Rời nhóm
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        @if($conversation->description)
                                        <p class="mb-0 text-muted small">{{ Str::limit($conversation->description, 80) }}</p>
                                        @endif
                                        <div class="group-item-actions">
                                            <a href="{{ route('dashboard.messages.show', $conversation->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-comments me-1"></i>Trò chuyện
                                            </a>
                                            @if(in_array($conversation->user_role, ['Người tạo', 'Quản trị viên']))
                                            <button class="btn btn-sm btn-outline-secondary btn-manage-members" data-conversation-id="{{ $conversation->id }}">
                                                <i class="fas fa-users-cog"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Desktop Layout -->
                                    <div class="d-none d-lg-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <i class="fas fa-users text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('dashboard.messages.show', $conversation->id) }}" class="text-decoration-none">
                                                            {{ $conversation->title }}
                                                        </a>
                                                    </h6>
                                                    <p class="text-muted mb-1 small">{{ Str::limit($conversation->description ?? '', 100) }}</p>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-users me-1"></i>
                                                            <span class="member-count">{{ $conversation->members_count ?? 0 }}</span> thành viên
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag me-1"></i>
                                                            {{ $conversation->conversation_type ?? 'Nhóm thảo luận' }}
                                                        </small>
                                                        <span class="badge bg-{{ $conversation->user_role === 'Người tạo' ? 'success' : ($conversation->user_role === 'Quản trị viên' ? 'warning' : 'secondary') }}">
                                                            {{ $conversation->user_role ?? 'Thành viên' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">{{ $conversation->last_activity ?? 'Chưa có hoạt động' }}</small>
                                                    @if($conversation->unread_count > 0)
                                                        <div class="mt-1">
                                                            <span class="badge bg-danger rounded-pill">{{ $conversation->unread_count }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('dashboard.messages.show', $conversation->id) }}">
                                                            <i class="fas fa-eye me-2"></i>Xem chi tiết
                                                        </a>
                                                    </li>
                                                    @if(in_array($conversation->user_role, ['Người tạo', 'Quản trị viên']))
                                                        <li>
                                                            <a class="dropdown-item btn-manage-members" href="#" data-conversation-id="{{ $conversation->id }}">
                                                                <i class="fas fa-users-cog me-2"></i>Quản lý thành viên
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item btn-group-settings" href="#" data-conversation-id="{{ $conversation->id }}">
                                                                <i class="fas fa-cog me-2"></i>Cài đặt nhóm
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="leaveGroup({{ $conversation->id }})">
                                                            <i class="fas fa-sign-out-alt me-2"></i>Rời nhóm
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(method_exists($groupConversations, 'links'))
                            <div class="card-footer bg-white border-top">
                                {{ $groupConversations->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">Chưa có nhóm thảo luận</h5>
                            <p class="text-muted mb-3">Bạn chưa tham gia nhóm thảo luận nào.</p>
                            <a href="{{ route('dashboard.messages.groups.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i>
                                Tạo nhóm đầu tiên
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Group Requests -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-paper-plane text-info me-2"></i>
                        Yêu cầu tạo nhóm
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($groupRequests) && $groupRequests->count() > 0)
                        @foreach($groupRequests as $request)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ $request['title'] }}</h6>
                                    <span class="badge bg-{{ $request['status_color'] ?? 'secondary' }}">
                                        {{ $request['status_label'] ?? 'Chưa xác định' }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">{{ Str::limit($request['description'] ?? '', 80) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $request['requested_at'] ? $request['requested_at']->diffForHumans() : 'Chưa xác định' }}
                                    </small>
                                    @if($request['can_edit'] ?? false)
                                        <a href="{{ route('dashboard.messages.groups.edit', $request['id']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-paper-plane text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0">Chưa có yêu cầu nào</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Thao tác nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard.messages.groups.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Tạo nhóm mới
                        </a>
                        <a href="{{ route('dashboard.messages.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-comments me-2"></i>
                            Xem tất cả tin nhắn
                        </a>
                        <a href="{{ route('dashboard.messages.create') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-envelope me-2"></i>
                            Tin nhắn riêng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Member Management Modal -->
<div class="modal fade" id="memberManagementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users-cog me-2"></i>
                    Quản lý thành viên
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="memberManagementContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Group Settings Modal -->
<div class="modal fade" id="groupSettingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cog me-2"></i>
                    Cài đặt nhóm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="groupSettingsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Member Management Modal -->
<div class="modal fade group-modal" id="memberManagementModal" tabindex="-1" aria-labelledby="memberManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="memberManagementModalLabel">
                    <i class="fas fa-users me-2"></i>Quản lý thành viên
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="member-search" placeholder="Tìm kiếm thành viên...">
                </div>
                <div id="member-list">
                    <!-- Members will be loaded here -->
                </div>
                <input type="hidden" id="current-conversation-id">
            </div>
        </div>
    </div>
</div>

<!-- Group Settings Modal -->
<div class="modal fade group-modal" id="groupSettingsModal" tabindex="-1" aria-labelledby="groupSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupSettingsModalLabel">
                    <i class="fas fa-cog me-2"></i>Cài đặt nhóm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="group-settings-content">
                    <!-- Settings form will be loaded here -->
                </div>
                <input type="hidden" id="settings-conversation-id">
            </div>
        </div>
    </div>
</div>

<!-- Invite Member Modal -->
<div class="modal fade group-modal" id="inviteMemberModal" tabindex="-1" aria-labelledby="inviteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inviteMemberModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Mời thành viên
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="user-search" class="form-label">Tìm kiếm người dùng</label>
                    <input type="text" class="form-control" id="user-search" placeholder="Nhập tên hoặc email...">
                    <small class="form-text text-muted">Nhập ít nhất 2 ký tự để tìm kiếm</small>
                </div>
                <div id="user-search-results">
                    <!-- Search results will be displayed here -->
                </div>
                <input type="hidden" id="invite-conversation-id">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/group-responsive.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/group-websocket.js') }}"></script>
<script src="{{ asset('js/group-management.js') }}"></script>
<script src="{{ asset('js/group-search-filter.js') }}"></script>
<script src="{{ asset('js/group-analytics.js') }}"></script>
<script src="{{ asset('js/group-mobile-enhancements.js') }}"></script>
<script src="{{ asset('js/group-features-integration.js') }}"></script>
<script>
function manageMembers(conversationId) {
    const modal = new bootstrap.Modal(document.getElementById('memberManagementModal'));
    const content = document.getElementById('memberManagementContent');

    content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
    modal.show();

    fetch(`/dashboard/messages/groups/${conversationId}/members`)
        .then(response => response.json())
        .then(data => {
            // Render member management interface
            let html = '<div class="row">';
            html += '<div class="col-12 mb-3">';
            html += '<button class="btn btn-primary btn-sm btn-invite-member" data-conversation-id="' + conversationId + '"><i class="fas fa-user-plus me-1"></i>Mời thành viên</button>';
            html += '</div>';

            data.members.forEach(member => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded">
                            <img src="${member.avatar || '/images/default-avatar.png'}" alt="${member.name}" class="rounded-circle me-2" width="40" height="40">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${member.name}</div>
                                <small class="text-muted">${member.group_role}</small>
                            </div>
                            ${member.can_manage ? `
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="changeMemberRole(${conversationId}, ${member.id})"><i class="fas fa-user-tag me-2"></i>Đổi vai trò</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="removeMember(${conversationId}, ${member.id})"><i class="fas fa-user-times me-2"></i>Xóa khỏi nhóm</a></li>
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi tải danh sách thành viên.</div>';
        });
}

function groupSettings(conversationId) {
    const modal = new bootstrap.Modal(document.getElementById('groupSettingsModal'));
    const content = document.getElementById('groupSettingsContent');

    content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
    modal.show();

    // Load group settings form
    content.innerHTML = `
        <form id="groupSettingsForm">
            <div class="mb-3">
                <label class="form-label">Tên nhóm</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_private" id="isPrivate">
                    <label class="form-check-label" for="isPrivate">
                        Nhóm riêng tư (chỉ thành viên mới thấy)
                    </label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    `;
}

function leaveGroup(conversationId) {
    if (confirm('Bạn có chắc chắn muốn rời khỏi nhóm này?')) {
        fetch(`/dashboard/messages/groups/${conversationId}/leave`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi rời nhóm.');
            }
        });
    }
}
</script>
@endpush
