/**
 * Typing Indicator Component
 * Manages typing indicators for real-time feedback
 */
class TypingIndicator {
    constructor(contextType, contextId, typingType = 'comment') {
        this.contextType = contextType;
        this.contextId = contextId;
        this.typingType = typingType;
        this.isTyping = false;
        this.typingTimer = null;
        this.heartbeatTimer = null;
        this.typingDelay = 1000; // 1 second delay before stopping
        this.heartbeatInterval = 15000; // 15 seconds heartbeat
        this.indicators = new Map(); // Store active indicators
        
        this.init();
    }

    /**
     * Initialize typing indicator
     */
    init() {
        this.setupEventListeners();
        this.setupRealTimeCallbacks();
        console.log(`TypingIndicator: Initialized for ${this.contextType}:${this.contextId}`);
    }

    /**
     * Setup event listeners for input fields
     */
    setupEventListeners() {
        // Find input fields in the context
        const contextSelector = `[data-context-type="${this.contextType}"][data-context-id="${this.contextId}"]`;
        const contextElement = document.querySelector(contextSelector);
        
        if (contextElement) {
            const inputFields = contextElement.querySelectorAll('input[type="text"], textarea, [contenteditable="true"]');
            
            inputFields.forEach(field => {
                field.addEventListener('input', () => this.handleTyping());
                field.addEventListener('keydown', () => this.handleTyping());
                field.addEventListener('blur', () => this.stopTyping());
                field.addEventListener('focus', () => this.handleTyping());
            });
        }

        // Global setup for comment forms
        this.setupCommentFormListeners();
    }

    /**
     * Setup comment form listeners
     */
    setupCommentFormListeners() {
        // Thread comment forms
        const threadCommentForms = document.querySelectorAll('.thread-comment-form, .comment-form');
        threadCommentForms.forEach(form => {
            const textarea = form.querySelector('textarea, [contenteditable="true"]');
            if (textarea) {
                textarea.addEventListener('input', () => {
                    const threadId = form.getAttribute('data-thread-id') || this.contextId;
                    this.handleTypingForContext('thread', threadId, 'comment');
                });
                
                textarea.addEventListener('blur', () => {
                    const threadId = form.getAttribute('data-thread-id') || this.contextId;
                    this.stopTypingForContext('thread', threadId, 'comment');
                });
            }
        });

        // Reply forms
        const replyForms = document.querySelectorAll('.reply-form');
        replyForms.forEach(form => {
            const textarea = form.querySelector('textarea, [contenteditable="true"]');
            if (textarea) {
                textarea.addEventListener('input', () => {
                    const commentId = form.getAttribute('data-comment-id');
                    this.handleTypingForContext('comment', commentId, 'reply');
                });
                
                textarea.addEventListener('blur', () => {
                    const commentId = form.getAttribute('data-comment-id');
                    this.stopTypingForContext('comment', commentId, 'reply');
                });
            }
        });
    }

    /**
     * Setup real-time callbacks
     */
    setupRealTimeCallbacks() {
        if (window.NotificationService) {
            // Listen for typing events
            window.NotificationService.on('onTypingStarted', (data) => {
                this.showTypingIndicator(data.indicator);
            });

            window.NotificationService.on('onTypingStopped', (data) => {
                this.hideTypingIndicator(data.indicator);
            });

            window.NotificationService.on('onTypingUpdated', (data) => {
                this.updateTypingIndicator(data.indicator);
            });
        }
    }

    /**
     * Handle typing for specific context
     */
    handleTypingForContext(contextType, contextId, typingType) {
        const key = `${contextType}:${contextId}:${typingType}`;
        
        // Clear existing timer for this context
        if (this.typingTimers && this.typingTimers[key]) {
            clearTimeout(this.typingTimers[key]);
        }

        // Start typing if not already
        if (!this.activeTyping || !this.activeTyping[key]) {
            this.startTypingForContext(contextType, contextId, typingType);
        }

        // Set timer to stop typing
        if (!this.typingTimers) this.typingTimers = {};
        this.typingTimers[key] = setTimeout(() => {
            this.stopTypingForContext(contextType, contextId, typingType);
        }, this.typingDelay);
    }

    /**
     * Handle typing (legacy method)
     */
    handleTyping() {
        // Clear existing timer
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
        }

        // Start typing if not already
        if (!this.isTyping) {
            this.startTyping();
        }

        // Set timer to stop typing
        this.typingTimer = setTimeout(() => {
            this.stopTyping();
        }, this.typingDelay);
    }

    /**
     * Start typing for specific context
     */
    async startTypingForContext(contextType, contextId, typingType) {
        try {
            const response = await fetch('/api/typing/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    context_type: contextType,
                    context_id: parseInt(contextId),
                    typing_type: typingType
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    const key = `${contextType}:${contextId}:${typingType}`;
                    if (!this.activeTyping) this.activeTyping = {};
                    this.activeTyping[key] = true;
                    
                    // Start heartbeat for this context
                    this.startHeartbeatForContext(contextType, contextId, typingType);
                    
                    console.log(`TypingIndicator: Started typing for ${key}`);
                }
            }
        } catch (error) {
            console.error('TypingIndicator: Failed to start typing', error);
        }
    }

    /**
     * Start typing (legacy method)
     */
    async startTyping() {
        return this.startTypingForContext(this.contextType, this.contextId, this.typingType);
    }

    /**
     * Stop typing for specific context
     */
    async stopTypingForContext(contextType, contextId, typingType) {
        try {
            const response = await fetch('/api/typing/stop', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    context_type: contextType,
                    context_id: parseInt(contextId),
                    typing_type: typingType
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    const key = `${contextType}:${contextId}:${typingType}`;
                    if (this.activeTyping) {
                        delete this.activeTyping[key];
                    }
                    
                    // Stop heartbeat for this context
                    this.stopHeartbeatForContext(key);
                    
                    console.log(`TypingIndicator: Stopped typing for ${key}`);
                }
            }
        } catch (error) {
            console.error('TypingIndicator: Failed to stop typing', error);
        }
    }

    /**
     * Stop typing (legacy method)
     */
    async stopTyping() {
        return this.stopTypingForContext(this.contextType, this.contextId, this.typingType);
    }

    /**
     * Start heartbeat for context
     */
    startHeartbeatForContext(contextType, contextId, typingType) {
        const key = `${contextType}:${contextId}:${typingType}`;
        
        if (!this.heartbeatTimers) this.heartbeatTimers = {};
        
        this.heartbeatTimers[key] = setInterval(async () => {
            try {
                await fetch('/api/typing/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        context_type: contextType,
                        context_id: parseInt(contextId),
                        typing_type: typingType
                    })
                });
            } catch (error) {
                console.error('TypingIndicator: Heartbeat failed', error);
                this.stopHeartbeatForContext(key);
            }
        }, this.heartbeatInterval);
    }

    /**
     * Stop heartbeat for context
     */
    stopHeartbeatForContext(key) {
        if (this.heartbeatTimers && this.heartbeatTimers[key]) {
            clearInterval(this.heartbeatTimers[key]);
            delete this.heartbeatTimers[key];
        }
    }

    /**
     * Show typing indicator
     */
    showTypingIndicator(indicator) {
        const contextSelector = `[data-context-type="${indicator.context_type}"][data-context-id="${indicator.context_id}"]`;
        const contextElement = document.querySelector(contextSelector);
        
        if (!contextElement) return;

        let typingContainer = contextElement.querySelector('.typing-indicators');
        if (!typingContainer) {
            typingContainer = document.createElement('div');
            typingContainer.className = 'typing-indicators';
            contextElement.appendChild(typingContainer);
        }

        // Check if indicator already exists
        const existingIndicator = typingContainer.querySelector(`[data-user-id="${indicator.user.id}"]`);
        if (existingIndicator) {
            return; // Already showing
        }

        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'typing-indicator';
        typingIndicator.setAttribute('data-user-id', indicator.user.id);
        typingIndicator.innerHTML = `
            <span class="typing-user">${this.escapeHtml(indicator.user.name)}</span>
            <span class="typing-text">đang gõ...</span>
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;

        typingContainer.appendChild(typingIndicator);
        
        // Store indicator
        this.indicators.set(indicator.user.id, {
            element: typingIndicator,
            indicator: indicator
        });

        console.log(`TypingIndicator: Showing indicator for ${indicator.user.name}`);
    }

    /**
     * Hide typing indicator
     */
    hideTypingIndicator(indicator) {
        const stored = this.indicators.get(indicator.user.id);
        if (stored && stored.element) {
            stored.element.classList.add('removing');
            setTimeout(() => {
                if (stored.element.parentNode) {
                    stored.element.parentNode.removeChild(stored.element);
                }
            }, 300);
            
            this.indicators.delete(indicator.user.id);
            console.log(`TypingIndicator: Hidden indicator for ${indicator.user.name}`);
        }
    }

    /**
     * Update typing indicator
     */
    updateTypingIndicator(indicator) {
        const stored = this.indicators.get(indicator.user.id);
        if (stored) {
            // Update stored indicator data
            stored.indicator = indicator;
            console.log(`TypingIndicator: Updated indicator for ${indicator.user.name}`);
        }
    }

    /**
     * Load active indicators for context
     */
    async loadActiveIndicators() {
        try {
            const response = await fetch(`/api/typing/active?context_type=${this.contextType}&context_id=${this.contextId}&typing_type=${this.typingType}&exclude_self=true`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    data.data.indicators.forEach(indicator => {
                        this.showTypingIndicator(indicator);
                    });
                }
            }
        } catch (error) {
            console.error('TypingIndicator: Failed to load active indicators', error);
        }
    }

    /**
     * Clear all typing indicators
     */
    clearAllIndicators() {
        this.indicators.forEach((stored, userId) => {
            if (stored.element && stored.element.parentNode) {
                stored.element.parentNode.removeChild(stored.element);
            }
        });
        this.indicators.clear();
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Destroy typing indicator
     */
    destroy() {
        // Clear all timers
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
        }
        
        if (this.typingTimers) {
            Object.values(this.typingTimers).forEach(timer => clearTimeout(timer));
        }
        
        if (this.heartbeatTimers) {
            Object.values(this.heartbeatTimers).forEach(timer => clearInterval(timer));
        }

        // Stop all active typing
        if (this.activeTyping) {
            Object.keys(this.activeTyping).forEach(key => {
                const [contextType, contextId, typingType] = key.split(':');
                this.stopTypingForContext(contextType, contextId, typingType);
            });
        }

        // Clear indicators
        this.clearAllIndicators();
        
        console.log(`TypingIndicator: Destroyed for ${this.contextType}:${this.contextId}`);
    }
}

/**
 * Global Typing Indicator Manager
 */
class TypingIndicatorManager {
    constructor() {
        this.indicators = new Map();
        this.init();
    }

    init() {
        // Auto-initialize typing indicators for existing contexts
        this.initializeExistingContexts();
        
        // Setup global event listeners
        this.setupGlobalListeners();
        
        console.log('TypingIndicatorManager: Initialized');
    }

    /**
     * Initialize existing contexts
     */
    initializeExistingContexts() {
        // Find all elements with context data
        const contextElements = document.querySelectorAll('[data-context-type][data-context-id]');
        
        contextElements.forEach(element => {
            const contextType = element.getAttribute('data-context-type');
            const contextId = element.getAttribute('data-context-id');
            const typingType = element.getAttribute('data-typing-type') || 'comment';
            
            this.createIndicator(contextType, contextId, typingType);
        });
    }

    /**
     * Setup global listeners
     */
    setupGlobalListeners() {
        // Listen for dynamically added content
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const contextElements = node.querySelectorAll ? 
                            node.querySelectorAll('[data-context-type][data-context-id]') : [];
                        
                        contextElements.forEach(element => {
                            const contextType = element.getAttribute('data-context-type');
                            const contextId = element.getAttribute('data-context-id');
                            const typingType = element.getAttribute('data-typing-type') || 'comment';
                            
                            this.createIndicator(contextType, contextId, typingType);
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Create typing indicator for context
     */
    createIndicator(contextType, contextId, typingType = 'comment') {
        const key = `${contextType}:${contextId}:${typingType}`;
        
        if (!this.indicators.has(key)) {
            const indicator = new TypingIndicator(contextType, contextId, typingType);
            this.indicators.set(key, indicator);
            
            // Load existing active indicators
            indicator.loadActiveIndicators();
        }
        
        return this.indicators.get(key);
    }

    /**
     * Get typing indicator for context
     */
    getIndicator(contextType, contextId, typingType = 'comment') {
        const key = `${contextType}:${contextId}:${typingType}`;
        return this.indicators.get(key);
    }

    /**
     * Remove typing indicator
     */
    removeIndicator(contextType, contextId, typingType = 'comment') {
        const key = `${contextType}:${contextId}:${typingType}`;
        const indicator = this.indicators.get(key);
        
        if (indicator) {
            indicator.destroy();
            this.indicators.delete(key);
        }
    }

    /**
     * Destroy all indicators
     */
    destroy() {
        this.indicators.forEach(indicator => indicator.destroy());
        this.indicators.clear();
    }
}

// Initialize global typing indicator manager
document.addEventListener('DOMContentLoaded', () => {
    window.TypingIndicatorManager = new TypingIndicatorManager();
});
