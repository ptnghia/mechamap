/**
 * NotificationEventSystem - Unified Event Management for Notification Components
 * Provides centralized event handling and component synchronization
 */
class NotificationEventSystem {
    constructor() {
        this.eventListeners = new Map();
        this.components = new Map();
        this.isInitialized = false;
        
        this.init();
    }

    /**
     * Initialize the event system
     */
    init() {
        if (this.isInitialized) return;
        
        this.setupGlobalEventListeners();
        this.isInitialized = true;
        
        console.log('NotificationEventSystem: Initialized');
    }

    /**
     * Setup global event listeners for component coordination
     */
    setupGlobalEventListeners() {
        // Listen for NotificationUIManager events
        document.addEventListener('notificationUI:notificationUIReady', (e) => {
            this.handleUIManagerReady(e.detail.manager);
        });

        document.addEventListener('notificationUI:emptyStateShown', (e) => {
            this.broadcastEvent('emptyStateChanged', { isEmpty: true });
        });

        document.addEventListener('notificationUI:emptyStateHidden', (e) => {
            this.broadcastEvent('emptyStateChanged', { isEmpty: false });
        });

        document.addEventListener('notificationUI:counterUpdated', (e) => {
            this.broadcastEvent('unreadCountChanged', { count: e.detail.count });
        });

        document.addEventListener('notificationUI:notificationsRendered', (e) => {
            this.broadcastEvent('notificationsUpdated', { 
                notifications: e.detail.notifications,
                count: e.detail.notifications.length 
            });
        });

        document.addEventListener('notificationUI:dropdownOpened', (e) => {
            this.broadcastEvent('dropdownStateChanged', { isOpen: true });
        });

        document.addEventListener('notificationUI:dropdownClosed', (e) => {
            this.broadcastEvent('dropdownStateChanged', { isOpen: false });
        });

        // Listen for component-specific events
        document.addEventListener('notificationManager:newNotification', (e) => {
            this.handleNewNotification(e.detail);
        });

        document.addEventListener('notificationDropdown:loadRequested', (e) => {
            this.handleLoadRequest(e.detail);
        });
    }

    /**
     * Register a component with the event system
     */
    registerComponent(name, component) {
        this.components.set(name, component);
        console.log(`NotificationEventSystem: Registered component '${name}'`);
        
        // Notify other components about new registration
        this.broadcastEvent('componentRegistered', { name, component });
    }

    /**
     * Unregister a component
     */
    unregisterComponent(name) {
        if (this.components.has(name)) {
            this.components.delete(name);
            console.log(`NotificationEventSystem: Unregistered component '${name}'`);
            
            this.broadcastEvent('componentUnregistered', { name });
        }
    }

    /**
     * Broadcast event to all registered components
     */
    broadcastEvent(eventName, data = {}) {
        const event = new CustomEvent(`notificationSystem:${eventName}`, {
            detail: { ...data, timestamp: Date.now() }
        });
        
        document.dispatchEvent(event);
        
        // Also notify components directly if they have event handlers
        this.components.forEach((component, name) => {
            if (component && typeof component.handleSystemEvent === 'function') {
                try {
                    component.handleSystemEvent(eventName, data);
                } catch (error) {
                    console.error(`NotificationEventSystem: Error in component '${name}' event handler:`, error);
                }
            }
        });
    }

    /**
     * Handle UIManager ready event
     */
    handleUIManagerReady(uiManager) {
        console.log('NotificationEventSystem: UIManager ready');
        
        // Setup event bridges between UIManager and other components
        this.setupUIManagerBridges(uiManager);
        
        this.broadcastEvent('uiManagerReady', { uiManager });
    }

    /**
     * Setup event bridges between UIManager and other components
     */
    setupUIManagerBridges(uiManager) {
        // Bridge NotificationManager events to UIManager
        document.addEventListener('notificationManager:notificationsLoaded', (e) => {
            uiManager.renderNotifications(e.detail.notifications);
            uiManager.updateCounter(e.detail.unreadCount || 0);
        });

        document.addEventListener('notificationManager:counterUpdate', (e) => {
            uiManager.updateCounter(e.detail.count);
        });

        document.addEventListener('notificationManager:newNotification', (e) => {
            // Trigger UI update for new notification
            this.broadcastEvent('refreshRequired', { reason: 'newNotification' });
        });

        // Bridge UIManager events to NotificationManager
        document.addEventListener('notificationUI:markAllAsRead', (e) => {
            this.broadcastEvent('markAllAsReadRequested');
        });

        document.addEventListener('notificationUI:clearAllNotifications', (e) => {
            this.broadcastEvent('clearAllNotificationsRequested');
        });

        document.addEventListener('notificationUI:deleteNotification', (e) => {
            this.broadcastEvent('deleteNotificationRequested', { id: e.detail.id });
        });

        document.addEventListener('notificationUI:notificationClicked', (e) => {
            this.broadcastEvent('notificationClicked', { 
                id: e.detail.id, 
                actionUrl: e.detail.actionUrl 
            });
        });
    }

    /**
     * Handle new notification from NotificationManager
     */
    handleNewNotification(notification) {
        console.log('NotificationEventSystem: New notification received', notification);
        
        // Broadcast to all components
        this.broadcastEvent('newNotificationReceived', { notification });
        
        // Trigger refresh if needed
        this.broadcastEvent('refreshRequired', { reason: 'newNotification' });
    }

    /**
     * Handle load request from dropdown
     */
    handleLoadRequest(request) {
        console.log('NotificationEventSystem: Load request received', request);
        
        // Broadcast to components that can handle loading
        this.broadcastEvent('loadNotificationsRequested', request);
    }

    /**
     * Synchronize state across all components
     */
    synchronizeState(state) {
        console.log('NotificationEventSystem: Synchronizing state', state);
        
        this.broadcastEvent('stateSynchronization', { state });
        
        // Update each component with new state
        this.components.forEach((component, name) => {
            if (component && typeof component.updateState === 'function') {
                try {
                    component.updateState(state);
                } catch (error) {
                    console.error(`NotificationEventSystem: Error updating state for '${name}':`, error);
                }
            }
        });
    }

    /**
     * Get current system state
     */
    getSystemState() {
        const state = {
            components: Array.from(this.components.keys()),
            timestamp: Date.now()
        };
        
        // Collect state from UIManager if available
        const uiManager = this.components.get('NotificationUIManager') || window.NotificationUIManager;
        if (uiManager && typeof uiManager.getState === 'function') {
            state.ui = uiManager.getState();
        }
        
        // Collect state from NotificationManager if available
        const notificationManager = this.components.get('NotificationManager');
        if (notificationManager) {
            state.notifications = {
                count: notificationManager.currentNotifications?.length || 0,
                unreadCount: notificationManager.unreadCount || 0,
                isInitialized: notificationManager.isInitialized || false
            };
        }
        
        return state;
    }

    /**
     * Debug method to log current system state
     */
    debugState() {
        console.log('NotificationEventSystem State:', this.getSystemState());
    }

    /**
     * Cleanup event system
     */
    destroy() {
        // Clear all event listeners
        this.eventListeners.clear();
        
        // Clear all components
        this.components.clear();
        
        this.isInitialized = false;
        
        console.log('NotificationEventSystem: Destroyed');
    }
}

// Create global instance
window.NotificationEventSystem = window.NotificationEventSystem || new NotificationEventSystem();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationEventSystem;
}

console.log('NotificationEventSystem loaded successfully');
