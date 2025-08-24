@extends('layouts.app')

@section('title', __('notifications.index.title'))

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-bell me-2 text-primary"></i>
                        {{ __('notifications.index.heading') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('notifications.index.description') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="markAllReadBtn">
                        <i class="fas fa-check-double me-1"></i>
                        {{ __('notifications.index.mark_all_read') }}
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="deleteAllReadBtn">
                        <i class="fas fa-trash me-1"></i>
                        {{ __('notifications.index.delete_read') }}
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
                    <p class="text-muted mb-0">{{ __('notifications.index.total_notifications') }}</p>
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
                    <p class="text-muted mb-0">{{ __('notifications.index.unread_count') }}</p>
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
                    <p class="text-muted mb-0">{{ __('notifications.index.read_count') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.notifications.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="type" class="form-label">{{ __('notifications.index.notification_type') }}</label>
                    <select name="type" id="type" class="form-select">
                        <option value="all" {{ $type === 'all' || !$type ? 'selected' : '' }}>{{ __('notifications.index.all_types') }}</option>
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
                        <option value="all" {{ $status === 'all' || !$status ? 'selected' : '' }}>{{ __('notifications.index.status_all') }}</option>
                        <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>{{ __('notifications.index.status_unread') }}</option>
                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>{{ __('notifications.index.status_read') }}</option>
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
                    {{ __('notifications.index.notification_list') }}
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
                                            {{ $notification->localized_title }}
                                        </h6>
                                        <div class="notification-actions d-flex gap-1">
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary">Mới</span>
                                            @endif
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary mark-read-btn"
                                                    data-notification-id="{{ $notification->id }}"
                                                    title="{{ $notification->is_read ? __('notifications.index.mark_unread') : __('notifications.index.mark_read') }}">
                                                <i class="fas {{ $notification->is_read ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-notification-id="{{ $notification->id }}"
                                                    title="{{ __('notifications.index.delete_notification') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <p class="notification-message text-muted mb-2">
                                        {{ $notification->localized_message }}
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
                    <h5 class="text-muted mb-2">{{ __('notifications.index.no_notifications') }}</h5>
                    <p class="text-muted mb-4">
                        @if($type && $type !== 'all')
                            {{ __('notifications.index.no_notifications_type') }} "{{ collect($notificationTypes)->firstWhere('value', $type)['label'] ?? $type }}"
                        @elseif($status && $status !== 'all')
                            {{ $status === 'read' ? __('notifications.index.no_read_notifications') : __('notifications.index.no_unread_notifications') }}
                        @else
                            {{ __('notifications.index.no_notifications_desc') }}
                        @endif
                    </p>
                    <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-1"></i>
                        {{ __('notifications.index.view_all') }}
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
        if (confirm('{!! addslashes(__('notifications.index.confirm_mark_all')) !!}')) {
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
            if (confirm('{!! addslashes(__('notifications.index.confirm_delete')) !!}')) {
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
