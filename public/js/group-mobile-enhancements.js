/**
 * MechaMap Group Mobile Enhancements
 * Mobile-specific enhancements and responsive features for group functionality
 */

class GroupMobileEnhancements {
    constructor(options = {}) {
        this.options = {
            swipeThreshold: 50,
            touchTimeout: 300,
            enablePullToRefresh: true,
            enableSwipeActions: true,
            enableOfflineMode: true,
            ...options
        };

        this.touchStartX = 0;
        this.touchStartY = 0;
        this.touchStartTime = 0;
        this.isScrolling = false;
        this.offlineQueue = [];

        this.init();
    }

    init() {
        this.detectMobile();
        this.bindMobileEvents();
        this.setupSwipeGestures();
        this.setupPullToRefresh();
        this.setupOfflineMode();
        this.optimizeMobileUI();
        this.setupVirtualKeyboard();
    }

    /**
     * Detect mobile device
     */
    detectMobile() {
        this.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        this.isTablet = /iPad|Android(?=.*\b(tablet|pad)\b)/i.test(navigator.userAgent);
        this.isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        // Add mobile classes to body
        if (this.isMobile) {
            document.body.classList.add('mobile-device');
        }
        if (this.isTablet) {
            document.body.classList.add('tablet-device');
        }
        if (this.isTouchDevice) {
            document.body.classList.add('touch-device');
        }
    }

    /**
     * Bind mobile-specific events
     */
    bindMobileEvents() {
        // Touch events for swipe gestures
        if (this.isTouchDevice) {
            document.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: true });
            document.addEventListener('touchmove', this.handleTouchMove.bind(this), { passive: false });
            document.addEventListener('touchend', this.handleTouchEnd.bind(this), { passive: true });
        }

        // Orientation change
        window.addEventListener('orientationchange', this.handleOrientationChange.bind(this));
        window.addEventListener('resize', this.handleResize.bind(this));

        // Visibility change (app backgrounding)
        document.addEventListener('visibilitychange', this.handleVisibilityChange.bind(this));

        // Online/offline events
        window.addEventListener('online', this.handleOnline.bind(this));
        window.addEventListener('offline', this.handleOffline.bind(this));

        // Mobile-specific UI events
        $(document).on('click', '.mobile-menu-toggle', this.toggleMobileMenu.bind(this));
        $(document).on('click', '.mobile-back-btn', this.handleMobileBack.bind(this));
        $(document).on('click', '.mobile-action-btn', this.handleMobileAction.bind(this));
    }

    /**
     * Setup swipe gestures
     */
    setupSwipeGestures() {
        if (!this.options.enableSwipeActions || !this.isTouchDevice) return;

        // Swipe to delete/archive messages
        this.setupMessageSwipeActions();

        // Swipe navigation between group tabs
        this.setupTabSwipeNavigation();

        // Swipe to reveal member actions
        this.setupMemberSwipeActions();
    }

    /**
     * Setup message swipe actions
     */
    setupMessageSwipeActions() {
        $('.message-item').each((index, element) => {
            const $message = $(element);
            let startX = 0;
            let currentX = 0;
            let isSwipeActive = false;

            $message.on('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isSwipeActive = true;
            });

            $message.on('touchmove', (e) => {
                if (!isSwipeActive) return;

                currentX = e.touches[0].clientX;
                const diffX = currentX - startX;

                if (Math.abs(diffX) > 10) {
                    e.preventDefault();
                    $message.css('transform', `translateX(${diffX}px)`);

                    // Show action buttons when swiped enough
                    if (Math.abs(diffX) > this.options.swipeThreshold) {
                        $message.addClass('swipe-active');
                        this.showSwipeActions($message, diffX > 0 ? 'right' : 'left');
                    } else {
                        $message.removeClass('swipe-active');
                        this.hideSwipeActions($message);
                    }
                }
            });

            $message.on('touchend', () => {
                isSwipeActive = false;

                // Reset position if not swiped enough
                if (!$message.hasClass('swipe-active')) {
                    $message.css('transform', '');
                }
            });
        });
    }

    /**
     * Setup tab swipe navigation
     */
    setupTabSwipeNavigation() {
        const $tabContainer = $('.group-tabs-container');
        if (!$tabContainer.length) return;

        let startX = 0;
        let isSwipeActive = false;

        $tabContainer.on('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isSwipeActive = true;
        });

        $tabContainer.on('touchmove', (e) => {
            if (!isSwipeActive) return;

            const currentX = e.touches[0].clientX;
            const diffX = currentX - startX;

            if (Math.abs(diffX) > this.options.swipeThreshold) {
                e.preventDefault();

                if (diffX > 0) {
                    this.navigateToPreviousTab();
                } else {
                    this.navigateToNextTab();
                }

                isSwipeActive = false;
            }
        });

        $tabContainer.on('touchend', () => {
            isSwipeActive = false;
        });
    }

    /**
     * Setup member swipe actions
     */
    setupMemberSwipeActions() {
        $('.member-item').each((index, element) => {
            const $member = $(element);

            // Add swipe-to-reveal actions for member management
            this.addSwipeToReveal($member, {
                leftActions: [
                    { icon: 'fa-comment', label: 'Nhắn tin', action: 'message' },
                    { icon: 'fa-user-plus', label: 'Kết bạn', action: 'friend' }
                ],
                rightActions: [
                    { icon: 'fa-user-cog', label: 'Quản lý', action: 'manage' },
                    { icon: 'fa-user-minus', label: 'Xóa', action: 'remove', danger: true }
                ]
            });
        });
    }

    /**
     * Setup pull-to-refresh
     */
    setupPullToRefresh() {
        if (!this.options.enablePullToRefresh || !this.isTouchDevice) return;

        const $refreshContainer = $('.pull-to-refresh-container');
        if (!$refreshContainer.length) return;

        let startY = 0;
        let currentY = 0;
        let isRefreshActive = false;
        let isAtTop = false;

        $refreshContainer.on('touchstart', (e) => {
            startY = e.touches[0].clientY;
            isAtTop = $refreshContainer.scrollTop() === 0;
        });

        $refreshContainer.on('touchmove', (e) => {
            if (!isAtTop) return;

            currentY = e.touches[0].clientY;
            const diffY = currentY - startY;

            if (diffY > 0 && diffY < 100) {
                e.preventDefault();

                const pullDistance = Math.min(diffY, 80);
                const opacity = pullDistance / 80;

                $('.pull-to-refresh-indicator')
                    .css({
                        'transform': `translateY(${pullDistance}px)`,
                        'opacity': opacity
                    })
                    .toggleClass('active', pullDistance > 50);

                if (pullDistance > 50 && !isRefreshActive) {
                    isRefreshActive = true;
                    this.triggerRefresh();
                }
            }
        });

        $refreshContainer.on('touchend', () => {
            $('.pull-to-refresh-indicator').css({
                'transform': '',
                'opacity': ''
            }).removeClass('active');

            isRefreshActive = false;
        });
    }

    /**
     * Setup offline mode
     */
    setupOfflineMode() {
        if (!this.options.enableOfflineMode) return;

        // Cache important data
        this.cacheGroupData();

        // Setup service worker for offline functionality
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw-groups.js')
                .then(registration => {
                    console.log('Group SW registered:', registration);
                })
                .catch(error => {
                    console.log('Group SW registration failed:', error);
                });
        }
    }

    /**
     * Optimize mobile UI
     */
    optimizeMobileUI() {
        // Adjust font sizes for mobile
        if (this.isMobile) {
            this.adjustMobileFontSizes();
        }

        // Optimize touch targets
        this.optimizeTouchTargets();

        // Setup mobile-specific modals
        this.setupMobileModals();

        // Optimize scrolling performance
        this.optimizeScrolling();
    }

    /**
     * Setup virtual keyboard handling
     */
    setupVirtualKeyboard() {
        if (!this.isMobile) return;

        let initialViewportHeight = window.innerHeight;

        window.addEventListener('resize', () => {
            const currentHeight = window.innerHeight;
            const heightDiff = initialViewportHeight - currentHeight;

            // Virtual keyboard is likely open if height decreased significantly
            if (heightDiff > 150) {
                document.body.classList.add('keyboard-open');
                this.adjustForKeyboard(true);
            } else {
                document.body.classList.remove('keyboard-open');
                this.adjustForKeyboard(false);
            }
        });

        // Focus events for input fields
        $('input, textarea').on('focus', () => {
            setTimeout(() => {
                this.scrollToActiveInput();
            }, 300);
        });
    }

    /**
     * Handle touch events
     */
    handleTouchStart(e) {
        this.touchStartX = e.touches[0].clientX;
        this.touchStartY = e.touches[0].clientY;
        this.touchStartTime = Date.now();
        this.isScrolling = false;
    }

    handleTouchMove(e) {
        if (!this.touchStartX || !this.touchStartY) return;

        const touchX = e.touches[0].clientX;
        const touchY = e.touches[0].clientY;
        const diffX = this.touchStartX - touchX;
        const diffY = this.touchStartY - touchY;

        // Determine if user is scrolling
        if (Math.abs(diffY) > Math.abs(diffX)) {
            this.isScrolling = true;
        }
    }

    handleTouchEnd(e) {
        if (!this.touchStartX || !this.touchStartY || this.isScrolling) return;

        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        const diffX = this.touchStartX - touchEndX;
        const diffY = this.touchStartY - touchEndY;
        const touchDuration = Date.now() - this.touchStartTime;

        // Check for swipe gestures
        if (Math.abs(diffX) > this.options.swipeThreshold && touchDuration < this.options.touchTimeout) {
            if (diffX > 0) {
                this.handleSwipeLeft(e);
            } else {
                this.handleSwipeRight(e);
            }
        }

        // Reset touch tracking
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.touchStartTime = 0;
        this.isScrolling = false;
    }

    /**
     * Handle orientation change
     */
    handleOrientationChange() {
        setTimeout(() => {
            this.adjustLayoutForOrientation();
            this.recalculateViewport();
        }, 100);
    }

    /**
     * Handle resize
     */
    handleResize() {
        this.optimizeMobileUI();
        this.adjustLayoutForOrientation();
    }

    /**
     * Handle visibility change
     */
    handleVisibilityChange() {
        if (document.hidden) {
            // App is backgrounded
            this.pauseRealTimeUpdates();
        } else {
            // App is foregrounded
            this.resumeRealTimeUpdates();
            this.syncOfflineActions();
        }
    }

    /**
     * Handle online/offline events
     */
    handleOnline() {
        this.showConnectionStatus('online');
        this.syncOfflineActions();
    }

    handleOffline() {
        this.showConnectionStatus('offline');
    }

    /**
     * Mobile-specific UI handlers
     */
    toggleMobileMenu() {
        $('.mobile-menu').toggleClass('active');
        $('body').toggleClass('menu-open');
    }

    handleMobileBack() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/dashboard/messages/groups';
        }
    }

    handleMobileAction(event) {
        const action = $(event.target).data('action');
        const groupId = $(event.target).data('group-id');

        switch (action) {
            case 'quick-reply':
                this.showQuickReply(groupId);
                break;
            case 'voice-message':
                this.startVoiceRecording(groupId);
                break;
            case 'share-location':
                this.shareLocation(groupId);
                break;
            case 'camera':
                this.openCamera(groupId);
                break;
        }
    }

    /**
     * Swipe gesture handlers
     */
    handleSwipeLeft(e) {
        // Implement swipe left actions
        console.log('Swipe left detected');
    }

    handleSwipeRight(e) {
        // Implement swipe right actions
        console.log('Swipe right detected');
    }

    /**
     * Navigation helpers
     */
    navigateToPreviousTab() {
        const $activeTab = $('.nav-tabs .nav-link.active');
        const $prevTab = $activeTab.parent().prev().find('.nav-link');

        if ($prevTab.length) {
            $prevTab.click();
        }
    }

    navigateToNextTab() {
        const $activeTab = $('.nav-tabs .nav-link.active');
        const $nextTab = $activeTab.parent().next().find('.nav-link');

        if ($nextTab.length) {
            $nextTab.click();
        }
    }

    /**
     * Utility methods
     */
    adjustMobileFontSizes() {
        // Adjust font sizes for better mobile readability
        $('body').addClass('mobile-optimized');
    }

    optimizeTouchTargets() {
        // Ensure touch targets are at least 44px
        $('.btn, .nav-link, .list-group-item').each(function() {
            const $element = $(this);
            if ($element.outerHeight() < 44) {
                $element.css('min-height', '44px');
            }
        });
    }

    setupMobileModals() {
        // Make modals mobile-friendly
        $('.modal').addClass('mobile-optimized');
    }

    optimizeScrolling() {
        // Enable momentum scrolling on iOS
        $('.scrollable').css('-webkit-overflow-scrolling', 'touch');
    }

    adjustForKeyboard(isOpen) {
        if (isOpen) {
            $('.chat-input-container').addClass('keyboard-active');
        } else {
            $('.chat-input-container').removeClass('keyboard-active');
        }
    }

    scrollToActiveInput() {
        const $activeInput = $('input:focus, textarea:focus');
        if ($activeInput.length) {
            $activeInput[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    adjustLayoutForOrientation() {
        const orientation = window.orientation || 0;

        if (Math.abs(orientation) === 90) {
            // Landscape
            $('body').addClass('landscape').removeClass('portrait');
        } else {
            // Portrait
            $('body').addClass('portrait').removeClass('landscape');
        }
    }

    recalculateViewport() {
        // Force viewport recalculation
        const viewport = document.querySelector('meta[name=viewport]');
        if (viewport) {
            viewport.setAttribute('content', viewport.getAttribute('content'));
        }
    }

    triggerRefresh() {
        $('.pull-to-refresh-indicator').addClass('refreshing');

        // Trigger actual refresh
        if (window.groupChat && window.groupChat.refreshMessages) {
            window.groupChat.refreshMessages();
        }

        setTimeout(() => {
            $('.pull-to-refresh-indicator').removeClass('refreshing');
        }, 1000);
    }

    showConnectionStatus(status) {
        const message = status === 'online' ? 'Đã kết nối' : 'Mất kết nối';
        const className = status === 'online' ? 'success' : 'warning';

        this.showToast(message, className);
    }

    showToast(message, className = 'info') {
        const toast = $(`
            <div class="toast mobile-toast align-items-center text-white bg-${className} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);

        $('.toast-container').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
    }

    cacheGroupData() {
        // Cache important group data for offline use
        if ('localStorage' in window) {
            // Implementation for caching
        }
    }

    syncOfflineActions() {
        // Sync actions performed while offline
        if (this.offlineQueue.length > 0) {
            // Process offline queue
        }
    }

    pauseRealTimeUpdates() {
        if (window.GroupWebSocket && typeof window.GroupWebSocket.pause === 'function') {
            window.GroupWebSocket.pause();
        }
    }

    resumeRealTimeUpdates() {
        if (window.GroupWebSocket && typeof window.GroupWebSocket.resume === 'function') {
            window.GroupWebSocket.resume();
        }
    }

    // Additional mobile-specific methods would be added here
    showQuickReply(groupId) {
        // Implementation for quick reply
    }

    startVoiceRecording(groupId) {
        // Implementation for voice recording
    }

    shareLocation(groupId) {
        // Implementation for location sharing
    }

    openCamera(groupId) {
        // Implementation for camera access
    }

    addSwipeToReveal($element, actions) {
        // Implementation for swipe-to-reveal actions
    }

    showSwipeActions($element, direction) {
        // Show swipe action buttons
    }

    hideSwipeActions($element) {
        // Hide swipe action buttons
    }
}

// Auto-initialize on mobile devices
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.group-container') || document.querySelector('.groups-list')) {
        window.groupMobileEnhancements = new GroupMobileEnhancements();
        console.log('✅ GroupMobileEnhancements initialized');
    }
});

// Export for manual initialization
window.GroupMobileEnhancements = GroupMobileEnhancements;
