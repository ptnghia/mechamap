@extends('layouts.app')

@section('title', 'Thông báo')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Thông báo
                    </h1>
                    <p class="text-muted mb-0">Quản lý tất cả thông báo của bạn</p>
                </div>

                <div class="d-flex gap-2">
                    <!-- Super Admin: System Notifications Toggle -->
                    @if(Auth::user()->role === 'super_admin')
                    <div class="btn-group" role="group">
                        <a href="{{ route('notifications.index') }}"
                           class="btn {{ !$showSystemNotifications ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-user me-1"></i>
                            User Notifications
                        </a>
                        <a href="{{ route('notifications.index', ['show_system' => 1]) }}"
                           class="btn {{ $showSystemNotifications ? 'btn-warning' : 'btn-outline-warning' }}">
                            <i class="fas fa-cog me-1"></i>
                            System Notifications
                        </a>
                    </div>
                    @endif

                    <!-- Mark all as read -->
                    @if($stats['unread'] > 0)
                    <button type="button" class="btn btn-outline-primary" id="markAllReadBtn">
                        <i class="fas fa-check-double me-1"></i>
                        Đánh dấu tất cả đã đọc
                    </button>
                    @endif

                    <!-- Clear all -->
                    @if(!$showSystemNotifications)
                    <button type="button" class="btn btn-outline-danger" id="clearAllBtn">
                        <i class="fas fa-trash me-1"></i>
                        Xóa tất cả
                    </button>
                    @endif
                </div>
            </div>

            <!-- System Notifications Warning -->
            @if($showSystemNotifications)
            <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>System Notifications</strong> - Đây là thông báo hệ thống chỉ dành cho Super Admin.
                    Chứa thông tin nhạy cảm về bảo mật, bảo trì và hoạt động hệ thống.
                </div>
            </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="fas fa-bell fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ number_format($stats['total']) }}</h4>
                            <small class="text-muted">Tổng thông báo</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ number_format($stats['unread']) }}</h4>
                            <small class="text-muted">Chưa đọc</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="fas fa-envelope-open fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ number_format($stats['read']) }}</h4>
                            <small class="text-muted">Đã đọc</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary mb-2">
                                <i class="fas fa-archive fa-2x"></i>
                            </div>
                            <h4 class="mb-1">{{ number_format($stats['archived']) }}</h4>
                            <small class="text-muted">Đã lưu trữ</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('notifications.index') }}" class="row g-3">
                        <!-- Search -->
                        <div class="col-md-4">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Tìm kiếm thông báo...">
                        </div>

                        <!-- Category Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Danh mục</label>
                            <select class="form-select" name="category">
                                <option value="">Tất cả</option>
                                @foreach($filters['categories'] as $key => $label)
                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="">Tất cả</option>
                                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Đã đọc</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Đã lưu trữ</option>
                            </select>
                        </div>

                        <!-- Priority Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Ưu tiên</label>
                            <select class="form-select" name="priority">
                                <option value="">Tất cả</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Bình thường</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                            </select>
                        </div>

                        <!-- Filter Actions -->
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                            <div class="list-group-item list-group-item-action notification-item {{ !$notification->is_read ? 'notification-unread' : '' }}"
                                 data-notification-id="{{ $notification->id }}">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <!-- Category Icon -->
                                            <div class="notification-icon me-3">
                                                <i class="fas fa-{{ $notification->getIconAttribute() }} text-{{ get_notification_category_color($notification->category) }}"></i>
                                            </div>

                                            <!-- Title and Category -->
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $notification->title }}</h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-{{ get_notification_category_color($notification->category) }}-subtle text-{{ get_notification_category_color($notification->category) }}">
                                                        {{ \App\Services\UnifiedNotificationManager::CATEGORIES[$notification->category] ?? $notification->category }}
                                                    </span>

                                                    @if($notification->priority !== 'normal')
                                                    <span class="badge bg-{{ $notification->priority === 'urgent' ? 'danger' : ($notification->priority === 'high' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($notification->priority) }}
                                                    </span>
                                                    @endif

                                                    @if(!$notification->is_read)
                                                    <span class="badge bg-primary">Mới</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="notification-actions">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if(!$notification->is_read)
                                                        <li><a class="dropdown-item mark-read-btn" href="#" data-id="{{ $notification->id }}">
                                                            <i class="fas fa-check me-2"></i>Đánh dấu đã đọc
                                                        </a></li>
                                                        @else
                                                        <li><a class="dropdown-item mark-unread-btn" href="#" data-id="{{ $notification->id }}">
                                                            <i class="fas fa-envelope me-2"></i>Đánh dấu chưa đọc
                                                        </a></li>
                                                        @endif

                                                        <li><a class="dropdown-item archive-btn" href="#" data-id="{{ $notification->id }}">
                                                            <i class="fas fa-archive me-2"></i>Lưu trữ
                                                        </a></li>

                                                        <li><hr class="dropdown-divider"></li>

                                                        <li><a class="dropdown-item text-danger delete-btn" href="#" data-id="{{ $notification->id }}">
                                                            <i class="fas fa-trash me-2"></i>Xóa
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Message -->
                                        <p class="mb-2 text-muted">{{ $notification->message }}</p>

                                        <!-- Action Button -->
                                        @if($notification->action_url && $notification->requires_action)
                                        <div class="mb-2">
                                            <a href="{{ $notification->action_url }}" class="btn btn-sm btn-primary">
                                                {{ $notification->action_text ?? 'Xem chi tiết' }}
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Metadata -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>

                                            @if($notification->view_count > 0 || $notification->click_count > 0)
                                            <small class="text-muted">
                                                @if($notification->view_count > 0)
                                                <i class="fas fa-eye me-1"></i>{{ $notification->view_count }}
                                                @endif
                                                @if($notification->click_count > 0)
                                                <i class="fas fa-mouse-pointer me-1 ms-2"></i>{{ $notification->click_count }}
                                                @endif
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer bg-transparent">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-bell-slash fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Không có thông báo nào</h5>
                            <p class="text-muted">Bạn chưa có thông báo nào phù hợp với bộ lọc hiện tại.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.notification-unread {
    background-color: rgba(13, 110, 253, 0.05);
    border-left: 4px solid #0d6efd;
}

.notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 50%;
}

.notification-actions {
    opacity: 0;
    transition: opacity 0.2s;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

.bg-blue-subtle { background-color: rgba(13, 110, 253, 0.1); }
.text-blue { color: #0d6efd; }
.bg-green-subtle { background-color: rgba(25, 135, 84, 0.1); }
.text-green { color: #198754; }
.bg-orange-subtle { background-color: rgba(253, 126, 20, 0.1); }
.text-orange { color: #fd7e14; }
.bg-purple-subtle { background-color: rgba(102, 16, 242, 0.1); }
.text-purple { color: #6610f2; }
.bg-red-subtle { background-color: rgba(220, 53, 69, 0.1); }
.text-red { color: #dc3545; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/frontend/components/unified-notifications.js') }}"></script>
@endpush
@endsection
