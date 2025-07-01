@extends('admin.layouts.dason')

@section('title', 'Thông Báo')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Thông Báo</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Thông Báo</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Notification Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Tổng Thông Báo</p>
                                <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-bell font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Chưa Đọc</p>
                                <h4 class="mb-0">{{ $stats['unread'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-bell-alert font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Hôm Nay</p>
                                <h4 class="mb-0">{{ $stats['today'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-calendar-today font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Quan Trọng</p>
                                <h4 class="mb-0">{{ $notifications->where('priority', 'high')->count() }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-danger">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-alert-circle font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Danh Sách Thông Báo</h4>
                        <div class="card-title-desc">Quản lý tất cả thông báo hệ thống</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm" onclick="markAllAsRead()">
                                <i class="mdi mdi-check-all me-1"></i> Đánh Dấu Tất Cả Đã Đọc
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="mdi mdi-filter me-1"></i> Lọc
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=unread">Chưa đọc</a></li>
                                <li><a class="dropdown-item" href="?filter=read">Đã đọc</a></li>
                                <li><a class="dropdown-item" href="?filter=important">Quan trọng</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?">Tất cả</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($notifications->count() > 0)
                    <!-- Real Notifications -->
                    <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item list-group-item-action {{ $notification->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <div class="d-flex align-items-start">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-{{ $notification->color }} rounded-circle">
                                            <i class="fas fa-{{ $notification->icon }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                        <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                        <small class="text-muted">{{ $notification->time_ago }}</small>
                                    </div>
                                </div>
                                @if($notification->is_read)
                                    <span class="badge bg-success">Đã đọc</span>
                                @else
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="markAsRead({{ $notification->id }})">
                                                    <i class="fas fa-check me-2"></i>Đánh dấu đã đọc
                                                </a>
                                            </li>
                                            @if($notification->hasActionUrl())
                                                <li>
                                                    <a class="dropdown-item" href="{{ $notification->data['action_url'] }}">
                                                        <i class="fas fa-external-link-alt me-2"></i>Xem chi tiết
                                                    </a>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteNotification({{ $notification->id }})">
                                                    <i class="fas fa-trash me-2"></i>Xóa
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash font-size-48 text-muted mb-3"></i>
                        <h5 class="text-muted">Không có thông báo</h5>
                        <p class="text-muted">Tất cả thông báo đã được xử lý</p>
                    </div>
                @endif

                @if($notifications->count() > 0)
                    <!-- Pagination -->
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">
                                    Hiển thị {{ $notifications->firstItem() ?? 0 }} đến {{ $notifications->lastItem() ?? 0 }}
                                    của {{ $notifications->total() }} thông báo
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $notifications->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// CSRF Token for AJAX requests
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

/**
 * Mark single notification as read
 */
function markAsRead(notificationId) {
    fetch(`/admin/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to update UI
            window.location.reload();
        } else {
            alert('Có lỗi xảy ra khi đánh dấu thông báo đã đọc');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi đánh dấu thông báo đã đọc');
    });
}

/**
 * Delete notification
 */
function deleteNotification(notificationId) {
    if (confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
        fetch(`/admin/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to update UI
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa thông báo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa thông báo');
        });
    }
}

/**
 * Mark all notifications as read
 */
function markAllAsRead() {
    if (confirm('Bạn có chắc chắn muốn đánh dấu tất cả thông báo đã đọc?')) {
        fetch('/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert(data.message);
                // Reload page to update UI
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra khi đánh dấu tất cả thông báo đã đọc');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi đánh dấu tất cả thông báo đã đọc');
        });
    }
}

// Attach event listener to "Mark All as Read" button
document.addEventListener('DOMContentLoaded', function() {
    const markAllBtn = document.querySelector('button[onclick*="markAllAsRead"]');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllAsRead();
        });
    }
});

// Auto-refresh notifications every 2 minutes
setInterval(function() {
    // Check for new notifications count
    fetch('/admin/notifications/api/unread-count')
        .then(response => response.json())
        .then(data => {
            // Update notification badge in header if needed
            const badge = document.querySelector('.noti-icon .badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.formatted;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error checking notification count:', error);
        });
}, 120000); // 2 minutes

console.log('Notification system initialized');
</script>
@endsection
