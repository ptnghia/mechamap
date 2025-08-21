@props(['position' => 'right'])

@push('styles')
<style>
.custom-notification-dropdown {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

.custom-notification-dropdown.dropdown-left {
    right: 0;
}

.custom-notification-dropdown.dropdown-right {
    left: 0;
}

.notification-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.notification-footer {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.notification-footer-link:hover {
    background: #e9ecef;
}

.notification-item {
    border-bottom: 1px solid #f1f3f4;
    transition: background-color 0.15s ease;
    cursor: pointer;
}

.notification-item:hover {
    background: #f8f9fa;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-bell:hover i {
    color: #0d6efd !important;
}

.notification-bell[aria-expanded="true"] i {
    color: #0d6efd !important;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .custom-notification-dropdown {
        width: 320px !important;
        max-width: calc(100vw - 20px);
        left: 50% !important;
        right: auto !important;
        transform: translateX(-50%);
    }
}

/* Loading animation */
.notification-loading .spinner-border {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Notification item animations */
.notification-item {
    animation: slideInDown 0.3s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes skeleton-loading {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}

.skeleton-line, .skeleton-avatar {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: skeleton-loading 1.5s infinite;
}
</style>
@endpush

<div class="notification-dropdown-wrapper position-relative">
    @auth
        <!-- Notification Bell Button -->
        <button type="button"
                class="btn btn-link position-relative notification-bell p-2"
                id="notificationBell"
                aria-expanded="false"
                title="Thông báo">
            <i class="fas fa-bell fs-5 text-muted"></i>
            <!-- Unread Count Badge -->
            <span class="notification-counter position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                0
                <span class="visually-hidden">thông báo chưa đọc</span>
            </span>
        </button>

        <!-- Custom Dropdown Menu -->
        <div class="custom-notification-dropdown {{ $position === 'left' ? 'dropdown-left' : 'dropdown-right' }}"
             id="notificationDropdown"
             aria-labelledby="notificationBell"
             style="display: none; position: absolute; top: 100%; z-index: 1050; width: 380px; max-height: 500px; background: white; border: 1px solid #dee2e6; border-radius: 0.375rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);"
             data-position="{{ $position }}">

            <!-- Header -->
            <div class="notification-header d-flex justify-content-between align-items-center py-3 px-3 border-bottom">
                <h6 class="mb-0 fw-bold" data-translate="notifications.ui.header">Thông báo</h6>
                <div class="d-flex gap-2">
                    <button type="button"
                            class="btn btn-sm btn-outline-primary btn-mark-all-read"
                            id="markAllRead"
                            data-translate-title="notifications.ui.mark_all_read"
                            title="Đánh dấu tất cả là đã đọc">
                        <i class="fas fa-check-double"></i>
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary btn-clear-all"
                            id="clearAll"
                            data-translate-title="notifications.ui.clear_all"
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

            <!-- Loading Skeleton -->
            <div class="notification-skeleton" id="notificationSkeleton" style="display: none;">
                <div class="notification-item p-3">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="skeleton-avatar rounded-circle bg-light" style="width: 40px; height: 40px;"></div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="skeleton-line mb-2 bg-light rounded" style="height: 16px; width: 70%;"></div>
                            <div class="skeleton-line mb-2 bg-light rounded" style="height: 14px; width: 90%;"></div>
                            <div class="skeleton-line bg-light rounded" style="height: 12px; width: 50%;"></div>
                        </div>
                    </div>
                </div>
                <div class="notification-item p-3">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="skeleton-avatar rounded-circle bg-light" style="width: 40px; height: 40px;"></div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="skeleton-line mb-2 bg-light rounded" style="height: 16px; width: 80%;"></div>
                            <div class="skeleton-line mb-2 bg-light rounded" style="height: 14px; width: 85%;"></div>
                            <div class="skeleton-line bg-light rounded" style="height: 12px; width: 45%;"></div>
                        </div>
                    </div>
                </div>
                <div class="notification-item p-3">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="skeleton-avatar rounded-circle bg-light" style="width: 40px; height: 40px;"></div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="skeleton-line mb-2 bg-light rounded" style="height: 16px; width: 75%;"></div>
                            <div class="skeleton-line mb-2 bg-light rounded" style="height: 14px; width: 95%;"></div>
                            <div class="skeleton-line bg-light rounded" style="height: 12px; width: 40%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="notification-list" id="notificationItems" style="max-height: 400px; overflow-y: auto;">
                <!-- Notifications will be loaded here by NotificationManager -->
            </div>

            <!-- Empty State -->
            <div class="notification-empty text-center py-4" id="notificationEmpty" style="display: none;">
                <i class="fas fa-bell-slash text-muted fs-1 mb-3"></i>
                <p class="text-muted mb-0" data-translate="notifications.ui.no_notifications">Không có thông báo nào</p>
            </div>

            <!-- Footer -->
            <div class="notification-footer border-top">
                <a href="{{ route('notifications.index') }}"
                   class="notification-footer-link text-center py-3 text-primary fw-medium d-block text-decoration-none"
                   data-translate="notifications.ui.view_all">
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
                title="{{ __('ui.auth.login_to_view_notifications') }}">
            <i class="fas fa-bell fs-5 text-muted"></i>
        </button>
    @endauth
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for NotificationUIManager to be ready
    document.addEventListener('notificationUI:notificationUIReady', function(e) {
        initNotificationDropdown(e.detail.manager);
    });

    // If NotificationUIManager is already ready
    if (window.NotificationUIManager && window.NotificationUIManager.isInitialized) {
        initNotificationDropdown(window.NotificationUIManager);
    }

    function initNotificationDropdown(uiManager) {
        const notificationDropdown = {
            // Reference to unified UI manager
            uiManager: uiManager,

            // State
            isLoaded: false,
            isLoading: false,
            notifications: [],
            cachedData: null,
            lastFetchTime: null,
            cacheTimeout: 60000, // 1 minute cache
            refreshInterval: null,
            translationsLoaded: false,

            // Initialize
            init() {
                if (!this.uiManager.elements.bell) return;

                // Load translations first
                this.loadTranslations();

                // Load notifications immediately on page load (with count)
                this.loadNotificationsWithCount(true);
                this.bindEvents();
                this.setupWebSocketHandlers();

                // Auto refresh every 30 seconds (count only, unless dropdown is open)
                this.refreshInterval = setInterval(() => {
                    const isDropdownOpen = this.isDropdownOpen();
                    this.loadNotificationsWithCount(!isDropdownOpen); // Load full data if dropdown is open
                }, 30000);

                console.log('NotificationDropdown: Initialized with NotificationUIManager');
            },

        // Load translations
        async loadTranslations() {
            try {
                if (window.translationService) {
                    await window.translationService.loadNotificationTranslations();
                    this.translationsLoaded = true;
                    this.updateTranslatedElements();
                    console.log('NotificationDropdown: Translations loaded');
                } else {
                    console.warn('NotificationDropdown: Translation service not available');
                }
            } catch (error) {
                console.error('NotificationDropdown: Failed to load translations:', error);
            }
        },

        // Update elements with translations
        updateTranslatedElements() {
            if (!this.translationsLoaded || !window.translationService) return;

            // Update elements with data-translate attribute
            document.querySelectorAll('[data-translate]').forEach(element => {
                const key = element.getAttribute('data-translate');
                const translation = window.translationService.trans(key);
                if (translation !== key) {
                    element.textContent = translation;
                }
            });

            // Update elements with data-translate-title attribute
            document.querySelectorAll('[data-translate-title]').forEach(element => {
                const key = element.getAttribute('data-translate-title');
                const translation = window.translationService.trans(key);
                if (translation !== key) {
                    element.setAttribute('title', translation);
                }
            });
        },

        // Bind events
        bindEvents() {
            // Preload notifications on hover (for faster UX)
            this.bellBtn.addEventListener('mouseenter', () => {
                if (!this.isLoaded && !this.isLoading) {
                    this.preloadNotifications();
                }
            });

            // Custom dropdown toggle
            this.bellBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown();
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.bellBtn.contains(e.target) && !this.dropdownMenu.contains(e.target)) {
                    this.closeDropdown();
                }
            });

            // Close dropdown on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isDropdownOpen()) {
                    this.closeDropdown();
                }
            });

            // Mark all as read
            this.markAllReadBtn?.addEventListener('click', () => {
                this.markAllAsRead();
            });

            // Prevent dropdown from closing when clicking inside
            this.dropdownMenu.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // Reposition dropdown on window resize
            window.addEventListener('resize', () => {
                if (this.isDropdownOpen()) {
                    this.positionDropdown();
                }
            });
        },

        // Dropdown control methods
        isDropdownOpen() {
            return this.dropdownMenu.style.display === 'block';
        },

        toggleDropdown() {
            if (this.isDropdownOpen()) {
                this.closeDropdown();
            } else {
                this.openDropdown();
            }
        },

        openDropdown() {
            // Position dropdown
            this.positionDropdown();

            // Show dropdown with animation
            this.dropdownMenu.style.display = 'block';
            this.dropdownMenu.style.opacity = '0';
            this.dropdownMenu.style.transform = 'translateY(-10px)';

            // Update aria attributes
            this.bellBtn.setAttribute('aria-expanded', 'true');

            // Show skeleton loading if no data yet
            if (!this.isLoaded && this.notifications.length === 0) {
                this.showSkeleton();
            }

            // Animate in
            requestAnimationFrame(() => {
                this.dropdownMenu.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
                this.dropdownMenu.style.opacity = '1';
                this.dropdownMenu.style.transform = 'translateY(0)';
            });

            // Force refresh notifications when dropdown opens (bypass cache)
            this.forceRefresh();
        },

        closeDropdown() {
            if (!this.isDropdownOpen()) return;

            // Animate out
            this.dropdownMenu.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
            this.dropdownMenu.style.opacity = '0';
            this.dropdownMenu.style.transform = 'translateY(-10px)';

            // Update aria attributes
            this.bellBtn.setAttribute('aria-expanded', 'false');

            // Hide after animation
            setTimeout(() => {
                this.dropdownMenu.style.display = 'none';
                this.dropdownMenu.style.transition = '';
            }, 150);
        },

        positionDropdown() {
            const position = this.dropdownMenu.getAttribute('data-position') || 'right';
            const rect = this.bellBtn.getBoundingClientRect();
            const dropdownWidth = window.innerWidth <= 576 ? 320 : 380;
            const viewportWidth = window.innerWidth;
            const margin = 10; // Margin from screen edge

            // Reset positioning
            this.dropdownMenu.style.left = '';
            this.dropdownMenu.style.right = '';
            this.dropdownMenu.style.transform = '';

            // Mobile responsive positioning
            if (window.innerWidth <= 576) {
                this.dropdownMenu.style.left = '50%';
                this.dropdownMenu.style.transform = 'translateX(-50%)';
                return;
            }

            if (position === 'left') {
                // Position to the left of the button
                this.dropdownMenu.style.right = '0';
            } else {
                // Position to the right of the button (default)
                // Check if dropdown would go off-screen
                const spaceOnRight = viewportWidth - rect.right;
                const spaceOnLeft = rect.left;

                if (spaceOnRight < dropdownWidth + margin && spaceOnLeft > dropdownWidth + margin) {
                    // Not enough space on right but enough on left, position to left
                    this.dropdownMenu.style.right = '0';
                } else if (spaceOnRight < dropdownWidth + margin && spaceOnLeft < dropdownWidth + margin) {
                    // Not enough space on either side, center it
                    this.dropdownMenu.style.left = '50%';
                    this.dropdownMenu.style.transform = 'translateX(-50%)';
                } else {
                    // Enough space on right
                    this.dropdownMenu.style.left = '0';
                }
            }
        },

        // Setup WebSocket handlers for real-time updates
        setupWebSocketHandlers() {
            console.log('🔗 Setting up WebSocket handlers for NotificationDropdown...');

            // Listen for WebSocket notification events from NotificationService
            if (window.NotificationService) {
                console.log('📡 NotificationService found, setting up listeners...');

                // Listen for new notifications
                window.NotificationService.on('onNotification', (notification) => {
                    console.log('🔔 NotificationDropdown received WebSocket notification:', notification);
                    this.handleNewNotification(notification);
                });

                // Listen for notification updates
                window.NotificationService.on('notificationUpdate', (data) => {
                    console.log('🔄 NotificationDropdown received notification update:', data);
                    if (data.type === 'read') {
                        this.handleNotificationRead(data.notificationId);
                    }
                });
            } else {
                console.warn('⚠️ NotificationService not available for real-time updates');
            }

            // Listen for global notification events (fallback)
            document.addEventListener('notification-received', (event) => {
                console.log('📨 NotificationDropdown received global notification event:', event.detail);
                this.handleNewNotification(event.detail);
            });

            document.addEventListener('notification-read', (event) => {
                console.log('✅ NotificationDropdown received notification read event:', event.detail);
                this.handleNotificationRead(event.detail.notificationId);
            });

            // Listen for NotificationManager events (bridge compatibility)
            document.addEventListener('notificationManager:newNotification', (event) => {
                console.log('🌉 NotificationDropdown received NotificationManager event:', event.detail);
                this.handleNewNotification(event.detail);
            });
        },

        // Load notifications with count using unified endpoint
        async loadNotificationsWithCount(countOnly = false) {
            try {
                // Check cache first - only for count-only requests
                if (countOnly && this.cachedData && this.lastFetchTime &&
                    (Date.now() - this.lastFetchTime) < this.cacheTimeout) {
                    this.updateUnreadCount(this.cachedData.unread_count);
                    return;
                }

                const params = new URLSearchParams({
                    load_notifications: countOnly ? 'false' : 'true',
                    limit: '10'
                });

                const response = await fetch(`/ajax/notifications?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Update cache
                    this.cachedData = data;
                    this.lastFetchTime = Date.now();

                    // Update UI
                    this.updateUnreadCount(data.unread_count);

                    if (data.loaded_notifications && data.notifications) {
                        this.notifications = data.notifications;
                        this.renderNotifications();
                        this.isLoaded = true;
                    }
                } else {
                    console.error('Failed to load notifications:', data.message);
                    if (!countOnly) {
                        this.showError('Không thể tải thông báo');
                    }
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
                if (!countOnly) {
                    this.showError('Có lỗi xảy ra khi tải thông báo');
                }
            }
        },

        // Handle new notification from WebSocket
        handleNewNotification(notification) {
            console.log('🔔 Processing new notification in dropdown:', notification);

            // Ensure notification has required properties
            if (!notification || !notification.id) {
                console.warn('⚠️ Invalid notification received:', notification);
                return;
            }

            // Format notification for dropdown display
            const formattedNotification = this.formatNotificationForDisplay(notification);

            // Update unread count
            if (this.cachedData) {
                this.cachedData.unread_count++;
                this.updateUnreadCount(this.cachedData.unread_count);
            } else {
                // If no cached data, just increment badge
                const currentCount = parseInt(this.unreadCount.textContent) || 0;
                this.updateUnreadCount(currentCount + 1);
            }

            // Add to notifications list if loaded
            if (this.isLoaded && this.notifications) {
                // Check if notification already exists (prevent duplicates)
                const existingIndex = this.notifications.findIndex(n => n.id === notification.id);
                if (existingIndex === -1) {
                    this.notifications.unshift(formattedNotification);
                    this.renderNotifications();
                    console.log('✅ Added new notification to dropdown list');
                } else {
                    console.log('ℹ️ Notification already exists in list, skipping');
                }
            }

            // Show visual feedback if dropdown is open
            if (this.isDropdownOpen()) {
                this.showNewNotificationAnimation();
            }

            // Invalidate cache to force refresh on next load
            this.clearCache();
        },

        // Clear cache and force refresh
        clearCache() {
            this.cachedData = null;
            this.lastFetchTime = null;
            this.isLoaded = false;
        },

        // Force refresh notifications (bypass cache)
        async forceRefresh() {
            this.clearCache();
            await this.loadNotificationsWithCount(false);
        },

        // Handle notification read event
        handleNotificationRead(notificationId) {
            // Update unread count
            if (this.cachedData) {
                this.cachedData.unread_count = Math.max(0, this.cachedData.unread_count - 1);
                this.updateUnreadCount(this.cachedData.unread_count);
            }

            // Update notification in list
            if (this.notifications) {
                const notification = this.notifications.find(n => n.id == notificationId);
                if (notification) {
                    notification.is_read = true;
                    this.renderNotifications();
                }
            }
        },

        // Render notifications - Use NotificationUIManager
        renderNotifications() {
            // Use unified UI manager for rendering
            this.uiManager.renderNotifications(this.notifications);

            // Hide skeleton loading
            this.hideSkeleton();
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
                                ${this.translateNotificationText(notification.title)}
                            </div>
                            <div class="notification-message text-muted small mb-2">
                                ${this.translateNotificationText(notification.message)}
                            </div>
                            <div class="notification-meta d-flex justify-content-between align-items-center">
                                <small class="text-muted">${notification.time_ago}</small>
                                <div class="notification-actions">
                                    ${isUnread ? `<span class="badge bg-primary">${this.getTranslation('notifications.ui.new_badge', 'Mới')}</span>` : ''}
                                    <button type="button"
                                            class="btn btn-sm btn-link text-muted p-0 ms-2 delete-notification-btn"
                                            title="${this.getTranslation('notifications.ui.delete_notification', 'Xóa thông báo')}">
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

        // Helper method to get translation
        getTranslation(key, fallback) {
            if (this.translationsLoaded && window.translationService) {
                return window.translationService.trans(key, {}, fallback);
            }
            return fallback;
        },

        // Helper method to translate notification text
        translateNotificationText(text) {
            if (!text) return '';

            // Check if text looks like a translation key
            if (text.includes('.') && text.match(/^[a-z_]+\.[a-z_]+/)) {
                return this.getTranslation(text, text);
            }

            return text;
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
                    // Trigger global event for other components
                    document.dispatchEvent(new CustomEvent('notification-read', {
                        detail: { notificationId }
                    }));

                    // Update UI immediately (will be handled by handleNotificationRead)
                    this.handleNotificationRead(notificationId);
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

        // Delete notification - Use NotificationUIManager
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
                    // Update notifications array
                    this.notifications = this.notifications.filter(n => n.id != notificationId);

                    // Use NotificationUIManager to handle UI updates
                    this.uiManager.handleNotificationDeleted(notificationId);

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
                this.badge.style.display = 'inline-block';
                this.bellBtn.querySelector('i').classList.add('text-primary');
                this.bellBtn.querySelector('i').classList.remove('text-muted');
            } else {
                this.badge.style.display = 'none';
                this.bellBtn.querySelector('i').classList.remove('text-primary');
                this.bellBtn.querySelector('i').classList.add('text-muted');
            }
        },

        // Show loading state - Use NotificationUIManager
        showLoading() {
            this.uiManager.showLoading();
        },

        // Hide loading state - Use NotificationUIManager
        hideLoading() {
            this.uiManager.hideLoading();
        },

        // Show empty state - Use NotificationUIManager
        showEmpty() {
            this.uiManager.showEmpty();
        },

        // Hide empty state - Use NotificationUIManager
        hideEmpty() {
            this.uiManager.hideEmpty();
        },

        // Show error
        showError(message) {
            this.notificationList.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                    <p class="text-muted mb-0">${message}</p>
                </div>
            `;
        },

        // Preload notifications (background loading)
        async preloadNotifications() {
            if (this.isLoading || this.isLoaded) return;

            this.isLoading = true;
            try {
                await this.loadNotificationsWithCount(false);
            } catch (error) {
                console.error('Failed to preload notifications:', error);
            } finally {
                this.isLoading = false;
            }
        },

        // Show skeleton loading state
        showSkeleton() {
            this.hideLoading();
            this.hideEmpty();
            this.notificationList.innerHTML = '';
            this.skeletonState.style.display = 'block';
        },

        // Hide skeleton loading state
        hideSkeleton() {
            this.skeletonState.style.display = 'none';
        },

        // Format notification for dropdown display
        formatNotificationForDisplay(notification) {
            // Ensure required properties exist
            const formatted = {
                id: notification.id,
                title: notification.title || 'Thông báo mới',
                message: notification.message || '',
                type: notification.type || 'system',
                icon: notification.icon || 'fas fa-bell',
                color: notification.color || 'primary',
                is_read: notification.is_read || false,
                created_at: notification.created_at || new Date().toISOString(),
                time_ago: notification.time_ago || 'Vừa xong',
                action_url: notification.action_url || '#',
                user_id: notification.user_id
            };

            console.log('📝 Formatted notification for display:', formatted);
            return formatted;
        },

        // Show animation for new notification
        showNewNotificationAnimation() {
            // Add a subtle animation to indicate new notification
            if (this.notificationList && this.notificationList.firstElementChild) {
                const firstItem = this.notificationList.firstElementChild;
                firstItem.style.animation = 'slideInDown 0.3s ease-out';

                // Add a temporary highlight
                firstItem.style.backgroundColor = '#e3f2fd';
                setTimeout(() => {
                    firstItem.style.backgroundColor = '';
                    firstItem.style.animation = '';
                }, 2000);
            }
        },

        // Cleanup method
        destroy() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
                this.refreshInterval = null;
            }

            // Clear cache
            this.cachedData = null;
            this.lastFetchTime = null;

            // Remove event listeners would be handled by browser when elements are removed
            console.log('NotificationDropdown: Cleaned up');
        }
        };

        // Initialize notification dropdown
        notificationDropdown.init();

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            notificationDropdown.destroy();
        });

        // Store reference for external access
        window.notificationDropdown = notificationDropdown;
    }
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
