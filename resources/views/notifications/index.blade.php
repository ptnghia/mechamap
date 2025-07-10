@extends('layouts.app')

@section('title', 'Quản lý thông báo')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-bell me-2 text-primary"></i>
                        Quản lý thông báo
                    </h1>
                    <p class="text-muted mb-0">Xem và quản lý tất cả thông báo của bạn</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="markAllReadBtn">
                        <i class="fas fa-check-double me-1"></i>
                        Đánh dấu tất cả đã đọc
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="deleteAllReadBtn">
                        <i class="fas fa-trash me-1"></i>
                        Xóa đã đọc
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-bell fs-4 text-primary"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ number_format($stats['total']) }}</h3>
                    <p class="text-muted mb-0">Tổng thông báo</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-bell-slash fs-4 text-warning"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ number_format($stats['unread']) }}</h3>
                    <p class="text-muted mb-0">Chưa đọc</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check fs-4 text-success"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ number_format($stats['read']) }}</h3>
                    <p class="text-muted mb-0">Đã đọc</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('notifications.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="type" class="form-label">Loại thông báo</label>
                    <select name="type" id="type" class="form-select">
                        <option value="all" {{ $type === 'all' || !$type ? 'selected' : '' }}>Tất cả loại</option>
                        @foreach($notificationTypes as $notificationType)
                            <option value="{{ $notificationType['value'] }}" {{ $type === $notificationType['value'] ? 'selected' : '' }}>
                                {{ $notificationType['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="all" {{ $status === 'all' || !$status ? 'selected' : '' }}>Tất cả</option>
                        <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Đã đọc</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="per_page" class="form-label">Hiển thị</label>
                    <div class="d-flex gap-2">
                        <select name="per_page" id="per_page" class="form-select">
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 / trang</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / trang</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / trang</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i>
                            Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Danh sách thông báo
                    @if($notifications->total() > 0)
                        <span class="badge bg-primary ms-2">{{ $notifications->total() }}</span>
                    @endif
                </h5>
                @if($notifications->hasPages())
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">
                            Hiển thị {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} 
                            trong {{ $notifications->total() }} kết quả
                        </small>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="notification-list">
                    @foreach($notifications as $notification)
                        <div class="notification-item border-bottom p-3 {{ !$notification->is_read ? 'bg-light' : '' }}" 
                             data-notification-id="{{ $notification->id }}">
                            <div class="d-flex">
                                <!-- Icon -->
                                <div class="notification-icon me-3">
                                    <div class="bg-{{ $notification->color }} bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="{{ $notification->icon }} text-{{ $notification->color }}"></i>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="notification-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="notification-title mb-0 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                            {{ $notification->title }}
                                        </h6>
                                        <div class="notification-actions d-flex gap-1">
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary">Mới</span>
                                            @endif
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary mark-read-btn"
                                                    data-notification-id="{{ $notification->id }}"
                                                    title="{{ $notification->is_read ? 'Đánh dấu chưa đọc' : 'Đánh dấu đã đọc' }}">
                                                <i class="fas {{ $notification->is_read ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-notification-id="{{ $notification->id }}"
                                                    title="Xóa thông báo">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <p class="notification-message text-muted mb-2">
                                        {{ $notification->message }}
                                    </p>
                                    
                                    <div class="notification-meta d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $notification->getTypeLabel() }}
                                            </small>
                                        </div>
                                        
                                        @if($notification->getActionUrl())
                                            <a href="{{ $notification->getActionUrl() }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>
                                                Xem chi tiết
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-bell-slash text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-2">Không có thông báo nào</h5>
                    <p class="text-muted mb-4">
                        @if($type && $type !== 'all')
                            Không tìm thấy thông báo loại "{{ collect($notificationTypes)->firstWhere('value', $type)['label'] ?? $type }}"
                        @elseif($status && $status !== 'all')
                            Không có thông báo {{ $status === 'read' ? 'đã đọc' : 'chưa đọc' }}
                        @else
                            Bạn chưa có thông báo nào. Thông báo sẽ xuất hiện ở đây khi có hoạt động mới.
                        @endif
                    </p>
                    <a href="{{ route('notifications.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-1"></i>
                        Xem tất cả thông báo
                    </a>
                </div>
            @endif
        </div>
        
        @if($notifications->hasPages())
            <div class="card-footer bg-white">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark all as read
    document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
        if (confirm('Đánh dấu tất cả thông báo là đã đọc?')) {
            fetch('/ajax/notifications/mark-all-read', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            });
        }
    });
    
    // Individual mark as read/unread
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            
            fetch(`/ajax/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            });
        });
    });
    
    // Delete notification
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Xóa thông báo này?')) {
                const notificationId = this.dataset.notificationId;
                
                fetch(`/ajax/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra');
                });
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
}

.notification-item .notification-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

.notification-icon {
    flex-shrink: 0;
}

.notification-content {
    min-width: 0;
}

.notification-title {
    word-break: break-word;
}

.notification-message {
    word-break: break-word;
    line-height: 1.5;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .notification-item .notification-actions {
        opacity: 1;
    }
    
    .notification-meta {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }
}
</style>
@endpush
