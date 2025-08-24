/**
 * MechaMap Group Features Integration
 * Central integration and coordination for all group JavaScript features
 */

class GroupFeaturesIntegration {
    constructor() {
        this.components = {};
        this.eventBus = new EventTarget();
        this.config = {
            debug: false,
            autoInit: true,
            enableAnalytics: true,
            enableMobileEnhancements: true,
            enableAdvancedSearch: true,
            enableCreationWizard: true
        };

        this.init();
    }

    init() {
        console.log('🚀 Initializing MechaMap Group Features Integration...');

        this.loadConfiguration();
        this.initializeComponents();
        this.setupEventListeners();
        this.setupGlobalErrorHandling();
        this.setupPerformanceMonitoring();

        console.log('✅ Group Features Integration initialized successfully');
    }

    /**
     * Load configuration from meta tags or global config
     */
    loadConfiguration() {
        // Load from meta tags
        const configMeta = document.querySelector('meta[name="group-features-config"]');
        if (configMeta) {
            try {
                const metaConfig = JSON.parse(configMeta.getAttribute('content'));
                this.config = { ...this.config, ...metaConfig };
            } catch (error) {
                console.warn('Failed to parse group features config from meta tag:', error);
            }
        }

        // Load from global window object
        if (window.MechaMapGroupConfig) {
            this.config = { ...this.config, ...window.MechaMapGroupConfig };
        }

        if (this.config.debug) {
            console.log('Group Features Config:', this.config);
        }
    }

    /**
     * Initialize all group feature components
     */
    initializeComponents() {
        // Initialize Group Management (already exists)
        if (window.GroupManagement && document.querySelector('.group-management-container')) {
            this.components.management = new window.GroupManagement();
            this.log('GroupManagement initialized');
        }

        // Initialize Group WebSocket (already exists)
        if (window.GroupWebSocket && this.shouldInitializeWebSocket()) {
            // GroupWebSocket is already initialized globally, just reference it
            this.components.websocket = window.GroupWebSocket;
            this.log('GroupWebSocket referenced');
        }

        // Initialize Group Chat (already exists)
        if (window.GroupChat && document.querySelector('[data-group-id]')) {
            const groupId = document.querySelector('[data-group-id]').dataset.groupId;
            this.components.chat = new window.GroupChat(groupId);
            this.log('GroupChat initialized');
        }

        // Initialize Creation Wizard
        if (this.config.enableCreationWizard && window.GroupCreationWizard && document.querySelector('.group-creation-wizard')) {
            this.components.creationWizard = new window.GroupCreationWizard();
            this.log('GroupCreationWizard initialized');
        }

        // Initialize Analytics
        if (this.config.enableAnalytics && window.GroupAnalytics && document.querySelector('.group-analytics-container')) {
            this.components.analytics = new window.GroupAnalytics();
            this.log('GroupAnalytics initialized');
        }

        // Initialize Search & Filter
        if (this.config.enableAdvancedSearch && window.GroupSearchFilter && document.querySelector('.groups-search-container')) {
            this.components.searchFilter = new window.GroupSearchFilter();
            this.log('GroupSearchFilter initialized');
        }

        // Initialize Mobile Enhancements
        if (this.config.enableMobileEnhancements && window.GroupMobileEnhancements) {
            this.components.mobileEnhancements = new window.GroupMobileEnhancements();
            this.log('GroupMobileEnhancements initialized');
        }
    }

    /**
     * Setup cross-component event listeners
     */
    setupEventListeners() {
        // Group creation events
        this.eventBus.addEventListener('group:created', this.handleGroupCreated.bind(this));
        this.eventBus.addEventListener('group:updated', this.handleGroupUpdated.bind(this));
        this.eventBus.addEventListener('group:deleted', this.handleGroupDeleted.bind(this));

        // Member events
        this.eventBus.addEventListener('member:joined', this.handleMemberJoined.bind(this));
        this.eventBus.addEventListener('member:left', this.handleMemberLeft.bind(this));
        this.eventBus.addEventListener('member:updated', this.handleMemberUpdated.bind(this));

        // Message events
        this.eventBus.addEventListener('message:sent', this.handleMessageSent.bind(this));
        this.eventBus.addEventListener('message:received', this.handleMessageReceived.bind(this));

        // Search events
        this.eventBus.addEventListener('search:performed', this.handleSearchPerformed.bind(this));
        this.eventBus.addEventListener('filter:applied', this.handleFilterApplied.bind(this));

        // Analytics events
        this.eventBus.addEventListener('analytics:updated', this.handleAnalyticsUpdated.bind(this));

        // Mobile events
        this.eventBus.addEventListener('mobile:swipe', this.handleMobileSwipe.bind(this));
        this.eventBus.addEventListener('mobile:orientation', this.handleOrientationChange.bind(this));

        // Global document events
        document.addEventListener('DOMContentLoaded', this.handleDOMReady.bind(this));
        window.addEventListener('beforeunload', this.handleBeforeUnload.bind(this));
    }

    /**
     * Setup global error handling for group features
     */
    setupGlobalErrorHandling() {
        window.addEventListener('error', (event) => {
            if (event.filename && event.filename.includes('group-')) {
                this.handleComponentError('JavaScript Error', event.error, {
                    filename: event.filename,
                    lineno: event.lineno,
                    colno: event.colno
                });
            }
        });

        window.addEventListener('unhandledrejection', (event) => {
            if (event.reason && event.reason.stack && event.reason.stack.includes('group-')) {
                this.handleComponentError('Unhandled Promise Rejection', event.reason);
            }
        });
    }

    /**
     * Setup performance monitoring
     */
    setupPerformanceMonitoring() {
        if (!this.config.enableAnalytics) return;

        // Monitor component initialization times
        this.performanceMetrics = {
            initStart: performance.now(),
            componentTimes: {},
            eventCounts: {},
            errorCounts: {}
        };

        // Track component performance
        Object.keys(this.components).forEach(componentName => {
            this.performanceMetrics.componentTimes[componentName] = performance.now();
        });
    }

    /**
     * Event handlers
     */
    handleGroupCreated(event) {
        this.log('Group created:', event.detail);

        // Refresh search results if search component is active
        if (this.components.searchFilter) {
            this.components.searchFilter.refreshResults();
        }

        // Update analytics if available
        if (this.components.analytics) {
            this.components.analytics.refreshData();
        }

        // Show success notification
        this.showNotification('Nhóm đã được tạo thành công!', 'success');
    }

    handleGroupUpdated(event) {
        this.log('Group updated:', event.detail);

        // Refresh relevant components
        this.refreshComponents(['searchFilter', 'analytics']);
    }

    handleGroupDeleted(event) {
        this.log('Group deleted:', event.detail);

        // Refresh components and redirect if necessary
        this.refreshComponents(['searchFilter', 'analytics']);

        // Redirect if currently viewing deleted group
        if (window.location.pathname.includes(`/groups/${event.detail.groupId}`)) {
            window.location.href = '/dashboard/messages/groups';
        }
    }

    handleMemberJoined(event) {
        this.log('Member joined:', event.detail);

        // Update member count in UI
        this.updateMemberCount(event.detail.groupId, event.detail.memberCount);

        // Refresh analytics
        if (this.components.analytics) {
            this.components.analytics.refreshMemberStats();
        }
    }

    handleMemberLeft(event) {
        this.log('Member left:', event.detail);

        // Update member count in UI
        this.updateMemberCount(event.detail.groupId, event.detail.memberCount);
    }

    handleMemberUpdated(event) {
        this.log('Member updated:', event.detail);

        // Refresh member lists in relevant components
        if (this.components.management) {
            this.components.management.refreshMemberList();
        }
    }

    handleMessageSent(event) {
        this.log('Message sent:', event.detail);

        // Update analytics
        if (this.components.analytics) {
            this.components.analytics.incrementMessageCount();
        }
    }

    handleMessageReceived(event) {
        this.log('Message received:', event.detail);

        // Handle mobile notifications
        if (this.components.mobileEnhancements) {
            this.components.mobileEnhancements.handleNewMessage(event.detail);
        }
    }

    handleSearchPerformed(event) {
        this.log('Search performed:', event.detail);

        // Track search analytics
        this.trackEvent('search', 'performed', event.detail.query);
    }

    handleFilterApplied(event) {
        this.log('Filter applied:', event.detail);

        // Track filter usage
        this.trackEvent('filter', 'applied', event.detail.filterType);
    }

    handleAnalyticsUpdated(event) {
        this.log('Analytics updated:', event.detail);

        // Sync analytics data across components
        this.syncAnalyticsData(event.detail);
    }

    handleMobileSwipe(event) {
        this.log('Mobile swipe:', event.detail);

        // Handle swipe actions across components
        this.processMobileSwipe(event.detail);
    }

    handleOrientationChange(event) {
        this.log('Orientation changed:', event.detail);

        // Adjust all components for new orientation
        this.adjustForOrientation(event.detail.orientation);
    }

    handleDOMReady() {
        this.log('DOM ready - performing final initialization');

        // Perform any final setup after DOM is ready
        this.finalizeInitialization();
    }

    handleBeforeUnload() {
        this.log('Page unloading - cleaning up');

        // Clean up resources
        this.cleanup();
    }

    /**
     * Utility methods
     */
    shouldInitializeWebSocket() {
        // Check if WebSocket should be initialized based on page context
        return document.querySelector('[data-group-id]') ||
               document.querySelector('.group-chat-container') ||
               document.querySelector('.groups-list');
    }

    refreshComponents(componentNames) {
        componentNames.forEach(name => {
            if (this.components[name] && typeof this.components[name].refresh === 'function') {
                this.components[name].refresh();
            }
        });
    }

    updateMemberCount(groupId, count) {
        // Update member count in all relevant UI elements
        $(`.group-member-count[data-group-id="${groupId}"]`).text(count);
        $(`.member-count-${groupId}`).text(count);
    }

    trackEvent(category, action, label) {
        if (this.config.enableAnalytics && window.gtag) {
            window.gtag('event', action, {
                event_category: category,
                event_label: label
            });
        }

        // Internal tracking
        const eventKey = `${category}:${action}`;
        this.performanceMetrics.eventCounts[eventKey] =
            (this.performanceMetrics.eventCounts[eventKey] || 0) + 1;
    }

    syncAnalyticsData(data) {
        // Sync analytics data across components that need it
        Object.values(this.components).forEach(component => {
            if (component && typeof component.updateAnalyticsData === 'function') {
                component.updateAnalyticsData(data);
            }
        });
    }

    processMobileSwipe(swipeData) {
        // Process mobile swipe actions
        switch (swipeData.direction) {
            case 'left':
                this.handleSwipeLeft(swipeData);
                break;
            case 'right':
                this.handleSwipeRight(swipeData);
                break;
        }
    }

    handleSwipeLeft(swipeData) {
        // Handle left swipe actions
        if (swipeData.target.classList.contains('message-item')) {
            // Show message actions
            this.showMessageActions(swipeData.target);
        }
    }

    handleSwipeRight(swipeData) {
        // Handle right swipe actions
        if (swipeData.target.classList.contains('group-item')) {
            // Show group actions
            this.showGroupActions(swipeData.target);
        }
    }

    adjustForOrientation(orientation) {
        // Adjust all components for orientation change
        Object.values(this.components).forEach(component => {
            if (component && typeof component.adjustForOrientation === 'function') {
                component.adjustForOrientation(orientation);
            }
        });
    }

    finalizeInitialization() {
        // Perform final setup
        this.performanceMetrics.initEnd = performance.now();
        this.performanceMetrics.totalInitTime =
            this.performanceMetrics.initEnd - this.performanceMetrics.initStart;

        this.log(`Initialization completed in ${this.performanceMetrics.totalInitTime.toFixed(2)}ms`);

        // Emit ready event
        this.eventBus.dispatchEvent(new CustomEvent('groupFeatures:ready', {
            detail: {
                components: Object.keys(this.components),
                performance: this.performanceMetrics
            }
        }));
    }

    cleanup() {
        // Clean up all components
        Object.values(this.components).forEach(component => {
            if (component && typeof component.destroy === 'function') {
                component.destroy();
            }
        });

        // Clear event listeners
        this.eventBus.removeEventListener('*');
    }

    handleComponentError(type, error, context = {}) {
        console.error(`Group Features ${type}:`, error, context);

        // Track error
        const errorKey = `${type}:${error.name || 'Unknown'}`;
        this.performanceMetrics.errorCounts[errorKey] =
            (this.performanceMetrics.errorCounts[errorKey] || 0) + 1;

        // Show user-friendly error message
        this.showNotification('Đã xảy ra lỗi. Vui lòng thử lại.', 'error');
    }

    showNotification(message, type = 'info') {
        // Use existing notification system or create simple toast
        if (window.showNotification) {
            window.showNotification(message, type);
        } else if (this.components.mobileEnhancements && this.components.mobileEnhancements.showToast) {
            this.components.mobileEnhancements.showToast(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    log(message, ...args) {
        if (this.config.debug) {
            console.log(`[GroupFeatures] ${message}`, ...args);
        }
    }

    /**
     * Public API methods
     */
    getComponent(name) {
        return this.components[name];
    }

    getAllComponents() {
        return { ...this.components };
    }

    getPerformanceMetrics() {
        return { ...this.performanceMetrics };
    }

    emit(eventName, data) {
        this.eventBus.dispatchEvent(new CustomEvent(eventName, { detail: data }));
    }

    on(eventName, callback) {
        this.eventBus.addEventListener(eventName, callback);
    }

    off(eventName, callback) {
        this.eventBus.removeEventListener(eventName, callback);
    }

    // Additional utility methods would be added here
    showMessageActions(messageElement) {
        // Implementation for showing message actions
    }

    showGroupActions(groupElement) {
        // Implementation for showing group actions
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on a group-related page
    if (document.querySelector('.group-container') ||
        document.querySelector('.groups-list') ||
        document.querySelector('.group-creation-wizard') ||
        document.querySelector('.group-analytics-container')) {

        window.groupFeaturesIntegration = new GroupFeaturesIntegration();
        console.log('✅ Group Features Integration initialized');
    }
});

// Export for manual initialization and external access
window.GroupFeaturesIntegration = GroupFeaturesIntegration;
