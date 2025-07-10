@props(['position' => 'right'])

<div class="notification-dropdown-wrapper position-relative">
    @auth
        <!-- Notification Bell Button -->
        <button type="button"
                class="btn btn-link position-relative notification-bell p-2"
                id="notificationBell"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                title="Thông báo">
            <i class="fas fa-bell fs-5 text-muted"></i>
            <!-- Unread Count Badge -->
            <span class="notification-counter position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                0
                <span class="visually-hidden">thông báo chưa đọc</span>
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div class="dropdown-menu dropdown-menu-{{ $position }} notification-dropdown show"
             id="notificationDropdown"
             aria-labelledby="notificationBell"
             style="width: 380px; max-height: 500px;">

            <!-- Header -->
            <div class="dropdown-header d-flex justify-content-between align-items-center py-3 px-3 border-bottom">
                <h6 class="mb-0 fw-bold">Thông báo</h6>
                <div class="d-flex gap-2">
                    <button type="button"
                            class="btn btn-sm btn-outline-primary btn-mark-all-read"
                            id="markAllRead"
                            title="Đánh dấu tất cả là đã đọc">
                        <i class="fas fa-check-double"></i>
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary btn-clear-all"
                            id="clearAll"
                            title="Xóa tất cả">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div class="notification-loading text-center py-4 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <div class="mt-2 text-muted small">Đang tải thông báo...</div>
            </div>

            <!-- Notifications List -->
            <div class="notification-list" id="notificationItems" style="max-height: 400px; overflow-y: auto;">
                <!-- Notifications will be loaded here by NotificationManager -->
            </div>

            <!-- Empty State -->
            <div class="notification-empty text-center py-4" id="notificationEmpty" style="display: none;">
                <i class="fas fa-bell-slash text-muted fs-1 mb-3"></i>
                <p class="text-muted mb-0">Không có thông báo nào</p>
            </div>

            <!-- Footer -->
            <div class="dropdown-footer border-top">
                <a href="{{ route('notifications.index') }}"
                   class="dropdown-item text-center py-3 text-primary fw-medium">
                    <i class="fas fa-list me-1"></i>
                    Xem tất cả thông báo
                </a>
            </div>
        </div>
    @else
        <!-- Guest State -->
        <button type="button"
                class="btn btn-link p-2"
                onclick="showLoginModal()"
                title="Đăng nhập để xem thông báo">
            <i class="fas fa-bell fs-5 text-muted"></i>
        </button>
    @endauth
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdown = {
        // Elements
        bellBtn: document.getElementById('notificationDropdownBtn'),
        badge: document.querySelector('.notification-badge'),
        unreadCount: document.querySelector('.unread-count'),
        dropdownMenu: document.querySelector('.notification-dropdown-menu'),
        notificationList: document.querySelector('.notification-list'),
        loadingState: document.querySelector('.notification-loading'),
        emptyState: document.querySelector('.notification-empty'),
        markAllReadBtn: document.querySelector('.mark-all-read-btn'),

        // State
        isLoaded: false,
        notifications: [],

        // Initialize
        init() {
            if (!this.bellBtn) return;

            this.loadUnreadCount();
            this.bindEvents();

            // Auto refresh every 30 seconds
            setInterval(() => {
                this.loadUnreadCount();
                if (this.isLoaded) {
                    this.loadNotifications();
                }
            }, 30000);
        },

        // Bind events
        bindEvents() {
            // Load notifications when dropdown is shown
            this.bellBtn.addEventListener('click', () => {
                if (!this.isLoaded) {
                    this.loadNotifications();
                }
            });

            // Mark all as read
            this.markAllReadBtn?.addEventListener('click', () => {
                this.markAllAsRead();
            });
        },

        // Load unread count
        async loadUnreadCount() {
            try {
                const response = await fetch('/ajax/notifications/unread-count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.updateUnreadCount(data.unread_count);
                }
            } catch (error) {
                console.error('Failed to load unread count:', error);
            }
        },

        // Load notifications
        async loadNotifications() {
            this.showLoading();

            try {
                const response = await fetch('/ajax/notifications/dropdown', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.notifications = data.notifications;
                    this.renderNotifications();
                    this.updateUnreadCount(data.unread_count);
                    this.isLoaded = true;
                } else {
                    this.showError('Không thể tải thông báo');
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
                this.showError('Có lỗi xảy ra khi tải thông báo');
            } finally {
                this.hideLoading();
            }
        },

        // Render notifications
        renderNotifications() {
            if (this.notifications.length === 0) {
                this.showEmpty();
                return;
            }

            const html = this.notifications.map(notification => this.renderNotification(notification)).join('');
            this.notificationList.innerHTML = html;

            // Bind notification events
            this.bindNotificationEvents();

            this.hideEmpty();
        },

        // Render single notification
        renderNotification(notification) {
            const isUnread = !notification.is_read;
            const actionUrl = notification.action_url || '#';

            return `
                <div class="notification-item dropdown-item-text p-3 border-bottom ${isUnread ? 'bg-light' : ''}"
                     data-notification-id="${notification.id}">
                    <div class="d-flex">
                        <div class="notification-icon me-3">
                            <i class="${notification.icon} text-${notification.color}"></i>
                        </div>
                        <div class="notification-content flex-grow-1">
                            <div class="notification-title fw-medium mb-1">
                                ${notification.title}
                            </div>
                            <div class="notification-message text-muted small mb-2">
                                ${notification.message}
                            </div>
                            <div class="notification-meta d-flex justify-content-between align-items-center">
                                <small class="text-muted">${notification.time_ago}</small>
                                <div class="notification-actions">
                                    ${isUnread ? '<span class="badge bg-primary">Mới</span>' : ''}
                                    <button type="button"
                                            class="btn btn-sm btn-link text-muted p-0 ms-2 delete-notification-btn"
                                            title="Xóa thông báo">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${actionUrl !== '#' ? `<a href="${actionUrl}" class="stretched-link"></a>` : ''}
                </div>
            `;
        },

        // Bind notification events
        bindNotificationEvents() {
            // Mark as read on click
            this.notificationList.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', (e) => {
                    if (e.target.closest('.delete-notification-btn')) return;

                    const notificationId = item.dataset.notificationId;
                    this.markAsRead(notificationId);
                });
            });

            // Delete notification
            this.notificationList.querySelectorAll('.delete-notification-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const notificationId = btn.closest('.notification-item').dataset.notificationId;
                    this.deleteNotification(notificationId);
                });
            });
        },

        // Mark notification as read
        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/ajax/notifications/${notificationId}/read`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Update UI
                    const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (item) {
                        item.classList.remove('bg-light');
                        const badge = item.querySelector('.badge');
                        if (badge) badge.remove();
                    }

                    // Update unread count
                    this.loadUnreadCount();
                }
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },

        // Mark all as read
        async markAllAsRead() {
            try {
                const response = await fetch('/ajax/notifications/mark-all-read', {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Update UI
                    this.notificationList.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('bg-light');
                        const badge = item.querySelector('.badge');
                        if (badge) badge.remove();
                    });

                    // Update unread count
                    this.updateUnreadCount(0);

                    showToast(data.message, 'success');
                }
            } catch (error) {
                console.error('Failed to mark all as read:', error);
                showToast('Có lỗi xảy ra', 'error');
            }
        },

        // Delete notification
        async deleteNotification(notificationId) {
            try {
                const response = await fetch(`/ajax/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Remove from UI
                    const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (item) {
                        item.remove();
                    }

                    // Update notifications array
                    this.notifications = this.notifications.filter(n => n.id != notificationId);

                    // Check if empty
                    if (this.notifications.length === 0) {
                        this.showEmpty();
                    }

                    // Update unread count
                    this.loadUnreadCount();

                    showToast(data.message, 'success');
                }
            } catch (error) {
                console.error('Failed to delete notification:', error);
                showToast('Có lỗi xảy ra khi xóa thông báo', 'error');
            }
        },

        // Update unread count
        updateUnreadCount(count) {
            if (count > 0) {
                this.unreadCount.textContent = count > 99 ? '99+' : count;
                this.badge.classList.remove('d-none');
                this.bellBtn.classList.add('text-primary');
                this.bellBtn.classList.remove('text-muted');
            } else {
                this.badge.classList.add('d-none');
                this.bellBtn.classList.remove('text-primary');
                this.bellBtn.classList.add('text-muted');
            }
        },

        // Show loading state
        showLoading() {
            this.loadingState.classList.remove('d-none');
            this.notificationList.innerHTML = '';
            this.hideEmpty();
        },

        // Hide loading state
        hideLoading() {
            this.loadingState.classList.add('d-none');
        },

        // Show empty state
        showEmpty() {
            this.emptyState.classList.remove('d-none');
            this.notificationList.innerHTML = '';
        },

        // Hide empty state
        hideEmpty() {
            this.emptyState.classList.add('d-none');
        },

        // Show error
        showError(message) {
            this.notificationList.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                    <p class="text-muted mb-0">${message}</p>
                </div>
            `;
        }
    };

    // Initialize notification dropdown
    notificationDropdown.init();
});
</script>
@endpush

@push('styles')
<style>
.notification-dropdown-wrapper .notification-bell-btn {
    border: none !important;
    background: none !important;
    transition: all 0.3s ease;
}

.notification-dropdown-wrapper .notification-bell-btn:hover {
    transform: scale(1.1);
}

.notification-dropdown-wrapper .notification-badge {
    font-size: 0.7rem;
    min-width: 18px;
    height: 18px;
    line-height: 18px;
}

.notification-dropdown-menu {
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1) !important;
}

.notification-dropdown-menu .dropdown-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0;
    margin: -1px -1px 0 -1px;
}

.notification-dropdown-menu .dropdown-header .btn-outline-primary {
    border-color: rgba(255,255,255,0.3);
    color: white;
}

.notification-dropdown-menu .dropdown-header .btn-outline-primary:hover {
    background-color: rgba(255,255,255,0.1);
    border-color: white;
}

.notification-item {
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
}

.notification-item .notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.05);
    border-radius: 50%;
}

.notification-item .delete-notification-btn {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-item:hover .delete-notification-btn {
    opacity: 1;
}

.notification-item .stretched-link::after {
    z-index: 1;
}

.notification-item .delete-notification-btn {
    z-index: 2;
    position: relative;
}

.dropdown-footer {
    border-radius: 0 0 12px 12px;
    background-color: #f8f9fa;
}

.dropdown-footer .dropdown-item:hover {
    background-color: #e9ecef;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .notification-dropdown-menu {
        width: 320px !important;
        margin-right: -20px;
    }
}
</style>
@endpush
