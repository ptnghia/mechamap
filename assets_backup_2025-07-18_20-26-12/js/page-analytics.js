/**
 * MechaMap Page Analytics & Tracking
 * Advanced analytics cho dynamic pages
 */

class PageAnalytics {
    constructor() {
        this.pageData = {
            url: window.location.href,
            title: document.title,
            referrer: document.referrer,
            userAgent: navigator.userAgent,
            timestamp: new Date().toISOString(),
            sessionId: this.getSessionId(),
            userId: this.getUserId(),
            pageId: this.getPageId(),
            category: this.getPageCategory()
        };
        
        this.startTime = Date.now();
        this.scrollDepth = 0;
        this.maxScrollDepth = 0;
        this.interactions = [];
        this.readingTime = 0;
        
        this.init();
    }

    init() {
        this.trackPageView();
        this.setupScrollTracking();
        this.setupTimeTracking();
        this.setupInteractionTracking();
        this.setupVisibilityTracking();
        this.setupPerformanceTracking();
        
        // Send data before page unload
        window.addEventListener('beforeunload', () => {
            this.trackPageExit();
        });
    }

    /**
     * Track page view
     */
    trackPageView() {
        const data = {
            event: 'page_view',
            ...this.pageData,
            viewport: {
                width: window.innerWidth,
                height: window.innerHeight
            },
            screen: {
                width: screen.width,
                height: screen.height
            },
            device: this.getDeviceInfo()
        };

        this.sendAnalytics(data);
        
        // Update view count in UI
        this.updateViewCount();
    }

    /**
     * Setup scroll tracking
     */
    setupScrollTracking() {
        let ticking = false;
        
        const trackScroll = () => {
            const scrollTop = window.pageYOffset;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = Math.round((scrollTop / docHeight) * 100);
            
            this.scrollDepth = scrollPercent;
            this.maxScrollDepth = Math.max(this.maxScrollDepth, scrollPercent);
            
            // Track milestone scrolls
            if (scrollPercent >= 25 && !this.scrollMilestones?.['25']) {
                this.trackScrollMilestone(25);
            }
            if (scrollPercent >= 50 && !this.scrollMilestones?.['50']) {
                this.trackScrollMilestone(50);
            }
            if (scrollPercent >= 75 && !this.scrollMilestones?.['75']) {
                this.trackScrollMilestone(75);
            }
            if (scrollPercent >= 90 && !this.scrollMilestones?.['90']) {
                this.trackScrollMilestone(90);
            }
            
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(trackScroll);
                ticking = true;
            }
        });

        this.scrollMilestones = {};
    }

    /**
     * Track scroll milestones
     */
    trackScrollMilestone(percent) {
        this.scrollMilestones[percent] = true;
        
        this.sendAnalytics({
            event: 'scroll_milestone',
            pageId: this.pageData.pageId,
            milestone: percent,
            timestamp: new Date().toISOString()
        });
    }

    /**
     * Setup time tracking
     */
    setupTimeTracking() {
        // Track reading time every 15 seconds
        setInterval(() => {
            if (this.isPageVisible) {
                this.readingTime += 15;
                
                // Track reading milestones
                if (this.readingTime === 30) {
                    this.trackReadingMilestone('30_seconds');
                } else if (this.readingTime === 60) {
                    this.trackReadingMilestone('1_minute');
                } else if (this.readingTime === 180) {
                    this.trackReadingMilestone('3_minutes');
                } else if (this.readingTime === 300) {
                    this.trackReadingMilestone('5_minutes');
                }
            }
        }, 15000);
    }

    /**
     * Track reading milestones
     */
    trackReadingMilestone(milestone) {
        this.sendAnalytics({
            event: 'reading_milestone',
            pageId: this.pageData.pageId,
            milestone: milestone,
            readingTime: this.readingTime,
            scrollDepth: this.maxScrollDepth,
            timestamp: new Date().toISOString()
        });
    }

    /**
     * Setup interaction tracking
     */
    setupInteractionTracking() {
        // Track clicks on important elements
        document.addEventListener('click', (e) => {
            const target = e.target.closest('a, button, .trackable');
            if (target) {
                this.trackInteraction('click', target);
            }
        });

        // Track social shares
        document.addEventListener('click', (e) => {
            if (e.target.closest('[href*="facebook.com"], [href*="twitter.com"], [href*="linkedin.com"]')) {
                const platform = this.getSocialPlatform(e.target.href);
                this.trackSocialShare(platform);
            }
        });

        // Track copy link
        document.addEventListener('click', (e) => {
            if (e.target.closest('[onclick*="copyToClipboard"]')) {
                this.trackInteraction('copy_link');
            }
        });
    }

    /**
     * Track interactions
     */
    trackInteraction(type, element = null) {
        const interaction = {
            type: type,
            timestamp: new Date().toISOString(),
            element: element ? {
                tag: element.tagName,
                text: element.textContent?.substring(0, 100),
                href: element.href,
                id: element.id,
                className: element.className
            } : null
        };

        this.interactions.push(interaction);

        this.sendAnalytics({
            event: 'interaction',
            pageId: this.pageData.pageId,
            interaction: interaction
        });
    }

    /**
     * Track social shares
     */
    trackSocialShare(platform) {
        this.sendAnalytics({
            event: 'social_share',
            pageId: this.pageData.pageId,
            platform: platform,
            url: this.pageData.url,
            title: this.pageData.title,
            timestamp: new Date().toISOString()
        });
    }

    /**
     * Setup visibility tracking
     */
    setupVisibilityTracking() {
        this.isPageVisible = !document.hidden;
        
        document.addEventListener('visibilitychange', () => {
            this.isPageVisible = !document.hidden;
            
            this.sendAnalytics({
                event: 'visibility_change',
                pageId: this.pageData.pageId,
                visible: this.isPageVisible,
                timestamp: new Date().toISOString()
            });
        });
    }

    /**
     * Setup performance tracking
     */
    setupPerformanceTracking() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                
                this.sendAnalytics({
                    event: 'performance',
                    pageId: this.pageData.pageId,
                    metrics: {
                        loadTime: perfData.loadEventEnd - perfData.loadEventStart,
                        domContentLoaded: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart,
                        firstPaint: this.getFirstPaint(),
                        firstContentfulPaint: this.getFirstContentfulPaint()
                    },
                    timestamp: new Date().toISOString()
                });
            }, 1000);
        });
    }

    /**
     * Track page exit
     */
    trackPageExit() {
        const timeOnPage = Date.now() - this.startTime;
        
        const exitData = {
            event: 'page_exit',
            pageId: this.pageData.pageId,
            timeOnPage: timeOnPage,
            readingTime: this.readingTime,
            maxScrollDepth: this.maxScrollDepth,
            interactions: this.interactions.length,
            timestamp: new Date().toISOString()
        };

        // Use sendBeacon for reliable sending
        if (navigator.sendBeacon) {
            navigator.sendBeacon('/api/analytics', JSON.stringify(exitData));
        } else {
            this.sendAnalytics(exitData);
        }
    }

    /**
     * Send analytics data
     */
    sendAnalytics(data) {
        fetch('/api/analytics', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify(data)
        }).catch(error => {
            console.warn('Analytics tracking failed:', error);
        });
    }

    /**
     * Update view count in UI
     */
    updateViewCount() {
        const viewCountElement = document.querySelector('.view-count, [data-view-count]');
        if (viewCountElement && this.pageData.pageId) {
            fetch(`/api/pages/${this.pageData.pageId}/view-count`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        viewCountElement.textContent = `${data.count.toLocaleString()} lượt xem`;
                    }
                })
                .catch(error => console.warn('Failed to update view count:', error));
        }
    }

    /**
     * Helper methods
     */
    getSessionId() {
        let sessionId = sessionStorage.getItem('mechamap_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('mechamap_session_id', sessionId);
        }
        return sessionId;
    }

    getUserId() {
        return document.querySelector('meta[name="user-id"]')?.getAttribute('content') || null;
    }

    getPageId() {
        return document.querySelector('meta[name="page-id"]')?.getAttribute('content') || 
               window.location.pathname.split('/').pop();
    }

    getPageCategory() {
        return document.querySelector('meta[name="page-category"]')?.getAttribute('content') || 'general';
    }

    getDeviceInfo() {
        const ua = navigator.userAgent;
        return {
            isMobile: /Mobile|Android|iPhone|iPad/.test(ua),
            isTablet: /iPad|Android(?!.*Mobile)/.test(ua),
            browser: this.getBrowserName(ua),
            os: this.getOSName(ua)
        };
    }

    getBrowserName(ua) {
        if (ua.includes('Chrome')) return 'Chrome';
        if (ua.includes('Firefox')) return 'Firefox';
        if (ua.includes('Safari')) return 'Safari';
        if (ua.includes('Edge')) return 'Edge';
        return 'Other';
    }

    getOSName(ua) {
        if (ua.includes('Windows')) return 'Windows';
        if (ua.includes('Mac')) return 'macOS';
        if (ua.includes('Linux')) return 'Linux';
        if (ua.includes('Android')) return 'Android';
        if (ua.includes('iOS')) return 'iOS';
        return 'Other';
    }

    getSocialPlatform(url) {
        if (url.includes('facebook.com')) return 'facebook';
        if (url.includes('twitter.com')) return 'twitter';
        if (url.includes('linkedin.com')) return 'linkedin';
        return 'other';
    }

    getFirstPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const firstPaint = paintEntries.find(entry => entry.name === 'first-paint');
        return firstPaint ? firstPaint.startTime : null;
    }

    getFirstContentfulPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const fcp = paintEntries.find(entry => entry.name === 'first-contentful-paint');
        return fcp ? fcp.startTime : null;
    }
}

// Initialize analytics when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.pageAnalytics = new PageAnalytics();
});
