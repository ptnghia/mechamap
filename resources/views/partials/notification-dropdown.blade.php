{{-- MechaMap Notification Dropdown Component --}}
@php
    $user = auth()->user();
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    $notifications = $user ? $user->userNotifications()
        ->orderByRaw('is_read ASC, created_at DESC')
        ->limit(20)
        ->get() : collect();
@endphp

<li class="nav-item dropdown notification-dropdown">
    <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown" role="button"
       data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
        <i class="fa-solid fa-bell fs-5"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  id="notificationBadge">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                <span class="visually-hidden">{{ __('notifications.unread_count') }}</span>
            </span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu shadow-lg"
         aria-labelledby="notificationDropdown" style="width: 380px; max-height: 600px;">

        {{-- Header --}}
        <div class="dropdown-header d-flex justify-content-between align-items-center py-3 px-3 border-bottom">
            <h6 class="mb-0 fw-bold">
                <i class="fa-solid fa-bell me-2 text-primary"></i>
                {{ __('notifications.ui.header') }}
            </h6>
            <div class="d-flex gap-2">
                @if($unreadCount > 0)
                    <button type="button" class="btn btn-sm btn-outline-primary" id="markAllReadBtn">
                        <i class="fa-solid fa-check-double me-1"></i>
                        {{ __('notifications.ui.mark_all_read') }}
                    </button>
                @endif
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-cog me-1"></i>
                    {{ __('notifications.ui.manage') }}
                </a>
            </div>
        </div>

        {{-- Quick Stats --}}
        @if($user)
        <div class="px-3 py-2 bg-light border-bottom">
            <div class="row text-center">
                <div class="col-4">
                    <small class="text-muted d-block">{{ __('notifications.total') }}</small>
                    <strong class="text-primary">{{ $user->userNotifications()->count() }}</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted d-block">{{ __('notifications.unread') }}</small>
                    <strong class="text-danger">{{ $unreadCount }}</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted d-block">{{ __('notifications.today') }}</small>
                    <strong class="text-success">{{ $user->userNotifications()->whereDate('created_at', today())->count() }}</strong>
                </div>
            </div>
        </div>
        @endif

        {{-- Notifications List --}}
        <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
            @forelse($notifications as $notification)
                <div class="dropdown-item notification-item {{ !$notification->is_read ? 'unread' : '' }}"
                     data-notification-id="{{ $notification->id }}"
                     style="border-left: 4px solid {{ $notification->is_read ? 'transparent' : 'var(--bs-primary)' }};">

                    <div class="d-flex align-items-start">
                        {{-- Icon --}}
                        <div class="notification-icon me-3 mt-1">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 40px; height: 40px; background-color: rgba(var(--bs-{{ $notification->color }}-rgb), 0.1);">
                                <i class="fa-solid fa-{{ $notification->icon }} text-{{ $notification->color }}"></i>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="notification-content flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="notification-title mb-1 fw-semibold">
                                    {{ $notification->localized_title }}
                                    @if(!$notification->is_read)
                                        <span class="badge bg-primary ms-1" style="font-size: 0.6em;">{{ __('notifications.new') }}</span>
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $notification->time_ago }}</small>
                            </div>

                            <p class="notification-message mb-2 text-muted small">
                                {{ Str::limit($notification->localized_message, 100) }}
                            </p>

                            {{-- Action Buttons --}}
                            <div class="notification-actions d-flex gap-2">
                                @if($notification->hasActionUrl())
                                    <a href="{{ $notification->getActionUrl() }}"
                                       class="btn btn-sm btn-outline-primary notification-action-btn">
                                        <i class="fa-solid fa-external-link-alt me-1"></i>
                                        {{ __('notifications.ui.view') }}
                                    </a>
                                @endif

                                @if(!$notification->is_read)
                                    <button type="button" class="btn btn-sm btn-outline-secondary mark-read-btn"
                                            data-notification-id="{{ $notification->id }}">
                                        <i class="fa-solid fa-check me-1"></i>
                                        {{ __('notifications.ui.mark_as_read') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fa-solid fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 text-muted">{{ __('notifications.no_notifications') }}</h6>
                    <p class="text-muted small">{{ __('notifications.no_notifications_desc') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->count() > 0)
        <div class="dropdown-footer border-top">
            <div class="row g-0">
                <div class="col-6">
                    <a href="{{ route('notifications.index') }}"
                       class="btn btn-link text-decoration-none w-100 py-3 text-center border-end">
                        <i class="fa-solid fa-list me-1"></i>
                        {{ __('notifications.view_all') }}
                    </a>
                </div>
                <div class="col-6">
                    <a href="https://mechamap.test/messages"
                       class="btn btn-link text-decoration-none w-100 py-3 text-center">
                        <i class="fa-solid fa-envelope me-1"></i>
                        {{ __('notifications.view_messages') }}
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</li>

{{-- Toast Container for Real-time Notifications --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="notificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <div class="rounded me-2 notification-toast-icon" style="width: 20px; height: 20px;"></div>
            <strong class="me-auto notification-toast-title">{{ __('notifications.new_notification') }}</strong>
            <small class="notification-toast-time"></small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body notification-toast-message">
            <!-- Dynamic content will be inserted here -->
        </div>
    </div>
</div>

<style>
.notification-dropdown-menu {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.notification-item {
    padding: 1rem;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.2s ease;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: rgba(var(--bs-primary-rgb), 0.02);
}

.notification-item.unread:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.notification-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

.notification-action-btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.mark-read-btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.notification-toast-icon {
    background-color: var(--bs-primary);
}

@media (max-width: 768px) {
    .notification-dropdown-menu {
        width: 320px !important;
        left: -280px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification dropdown functionality
    initializeNotificationDropdown();
});

function initializeNotificationDropdown() {
    // Mark single notification as read
    document.addEventListener('click', function(e) {
        if (e.target.closest('.mark-read-btn')) {
            e.preventDefault();
            e.stopPropagation();

            const btn = e.target.closest('.mark-read-btn');
            const notificationId = btn.dataset.notificationId;
            markNotificationAsRead(notificationId);
        }
    });

    // Mark all notifications as read
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            markAllNotificationsAsRead();
        });
    }

    // Handle notification item clicks
    document.addEventListener('click', function(e) {
        const notificationItem = e.target.closest('.notification-item');
        if (notificationItem && !e.target.closest('.notification-actions')) {
            const notificationId = notificationItem.dataset.notificationId;
            if (notificationItem.classList.contains('unread')) {
                markNotificationAsRead(notificationId);
            }
        }
    });
}

function markNotificationAsRead(notificationId) {
    fetch(`/ajax/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNotificationUI(notificationId);
            updateNotificationBadge();
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllNotificationsAsRead() {
    fetch('/ajax/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all notification items
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                item.style.borderLeftColor = 'transparent';
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
                const markReadBtn = item.querySelector('.mark-read-btn');
                if (markReadBtn) markReadBtn.remove();
            });

            updateNotificationBadge();

            // Hide mark all read button
            const markAllBtn = document.getElementById('markAllReadBtn');
            if (markAllBtn) markAllBtn.style.display = 'none';
        }
    })
    .catch(error => console.error('Error marking all notifications as read:', error));
}

function updateNotificationUI(notificationId) {
    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (notificationItem) {
        notificationItem.classList.remove('unread');
        notificationItem.style.borderLeftColor = 'transparent';

        const badge = notificationItem.querySelector('.badge');
        if (badge) badge.remove();

        const markReadBtn = notificationItem.querySelector('.mark-read-btn');
        if (markReadBtn) markReadBtn.remove();
    }
}

function updateNotificationBadge() {
    const badge = document.getElementById('notificationBadge');
    const unreadItems = document.querySelectorAll('.notification-item.unread').length;

    if (unreadItems === 0) {
        if (badge) badge.remove();
    } else {
        if (badge) {
            badge.textContent = unreadItems > 99 ? '99+' : unreadItems;
        }
    }
}

// WebSocket integration for real-time notifications
function handleNewNotification(notification) {
    // Update badge count
    updateNotificationBadge();

    // Show toast notification
    showNotificationToast(notification);

    // Add to dropdown if it's open
    addNotificationToDropdown(notification);
}

function showNotificationToast(notification) {
    const toast = document.getElementById('notificationToast');
    const toastIcon = toast.querySelector('.notification-toast-icon');
    const toastTitle = toast.querySelector('.notification-toast-title');
    const toastMessage = toast.querySelector('.notification-toast-message');
    const toastTime = toast.querySelector('.notification-toast-time');

    // Update toast content
    toastIcon.style.backgroundColor = `var(--bs-${notification.color})`;
    toastTitle.textContent = notification.title;
    toastMessage.textContent = notification.message;
    toastTime.textContent = 'Vừa xong';

    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

function addNotificationToDropdown(notification) {
    const notificationList = document.querySelector('.notification-list');
    if (notificationList) {
        // Create new notification element
        const notificationHTML = createNotificationHTML(notification);
        notificationList.insertAdjacentHTML('afterbegin', notificationHTML);

        // Remove last notification if more than 20
        const notifications = notificationList.querySelectorAll('.notification-item');
        if (notifications.length > 20) {
            notifications[notifications.length - 1].remove();
        }
    }
}

function createNotificationHTML(notification) {
    return `
        <div class="dropdown-item notification-item unread"
             data-notification-id="${notification.id}"
             style="border-left: 4px solid var(--bs-primary);">
            <div class="d-flex align-items-start">
                <div class="notification-icon me-3 mt-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 40px; height: 40px; background-color: rgba(var(--bs-${notification.color}-rgb), 0.1);">
                        <i class="fa-solid fa-${notification.icon} text-${notification.color}"></i>
                    </div>
                </div>
                <div class="notification-content flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="notification-title mb-1 fw-semibold">
                            ${notification.title}
                            <span class="badge bg-primary ms-1" style="font-size: 0.6em;">Mới</span>
                        </h6>
                        <small class="text-muted">Vừa xong</small>
                    </div>
                    <p class="notification-message mb-2 text-muted small">
                        ${notification.message.substring(0, 100)}${notification.message.length > 100 ? '...' : ''}
                    </p>
                    <div class="notification-actions d-flex gap-2">
                        ${notification.action_url ? `
                            <a href="${notification.action_url}"
                               class="btn btn-sm btn-outline-primary notification-action-btn">
                                <i class="fa-solid fa-external-link-alt me-1"></i>
                                Xem
                            </a>
                        ` : ''}
                        <button type="button" class="btn btn-sm btn-outline-secondary mark-read-btn"
                                data-notification-id="${notification.id}">
                            <i class="fa-solid fa-check me-1"></i>
                            Đánh dấu đã đọc
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}
</script>
