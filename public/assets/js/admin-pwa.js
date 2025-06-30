/**
 * MechaMap Admin PWA Manager
 * Handles PWA installation, updates, and offline functionality
 */

class AdminPWA {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.isOnline = navigator.onLine;
        this.swRegistration = null;
        this.updateAvailable = false;
        
        this.init();
    }

    async init() {
        this.checkInstallation();
        this.setupServiceWorker();
        this.setupInstallPrompt();
        this.setupOnlineOfflineHandlers();
        this.setupUpdateHandlers();
        this.setupNotifications();
        this.createInstallBanner();
        this.setupKeyboardShortcuts();
    }

    checkInstallation() {
        // Check if app is installed
        if (window.matchMedia('(display-mode: standalone)').matches || 
            window.navigator.standalone === true) {
            this.isInstalled = true;
            document.body.classList.add('pwa-installed');
            console.log('[PWA] App is installed');
        }
    }

    async setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                this.swRegistration = await navigator.serviceWorker.register('/admin-sw.js', {
                    scope: '/admin/'
                });
                
                console.log('[PWA] Service Worker registered:', this.swRegistration);
                
                // Listen for service worker messages
                navigator.serviceWorker.addEventListener('message', (event) => {
                    this.handleServiceWorkerMessage(event);
                });
                
                // Check for updates
                this.swRegistration.addEventListener('updatefound', () => {
                    this.handleServiceWorkerUpdate();
                });
                
            } catch (error) {
                console.error('[PWA] Service Worker registration failed:', error);
            }
        }
    }

    setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('[PWA] Install prompt available');
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallBanner();
        });

        window.addEventListener('appinstalled', () => {
            console.log('[PWA] App installed successfully');
            this.isInstalled = true;
            this.hideInstallBanner();
            this.showNotification('App installed successfully!', 'success');
            
            // Track installation
            this.trackEvent('pwa_installed');
        });
    }

    setupOnlineOfflineHandlers() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            document.body.classList.remove('offline');
            document.body.classList.add('online');
            this.showNotification('Back online!', 'success');
            this.syncWhenOnline();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            document.body.classList.remove('online');
            document.body.classList.add('offline');
            this.showNotification('You are offline. Some features may be limited.', 'warning');
        });

        // Set initial state
        if (this.isOnline) {
            document.body.classList.add('online');
        } else {
            document.body.classList.add('offline');
        }
    }

    setupUpdateHandlers() {
        // Check for updates periodically
        setInterval(() => {
            if (this.swRegistration) {
                this.swRegistration.update();
            }
        }, 60000); // Check every minute
    }

    setupNotifications() {
        if ('Notification' in window && 'serviceWorker' in navigator) {
            // Request notification permission for admin alerts
            if (Notification.permission === 'default') {
                this.requestNotificationPermission();
            }
        }
    }

    async requestNotificationPermission() {
        try {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                console.log('[PWA] Notification permission granted');
                this.setupPushNotifications();
            }
        } catch (error) {
            console.error('[PWA] Notification permission error:', error);
        }
    }

    async setupPushNotifications() {
        if (this.swRegistration && 'pushManager' in this.swRegistration) {
            try {
                const subscription = await this.swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(
                        'YOUR_VAPID_PUBLIC_KEY' // Replace with actual VAPID key
                    )
                });
                
                // Send subscription to server
                await this.sendSubscriptionToServer(subscription);
                
            } catch (error) {
                console.error('[PWA] Push subscription failed:', error);
            }
        }
    }

    createInstallBanner() {
        const banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.className = 'pwa-install-banner hidden';
        banner.innerHTML = `
            <div class="pwa-banner-content">
                <div class="pwa-banner-icon">📱</div>
                <div class="pwa-banner-text">
                    <strong>Install MechaMap Admin</strong>
                    <p>Get faster access and offline functionality</p>
                </div>
                <div class="pwa-banner-actions">
                    <button class="btn btn-primary btn-sm" onclick="adminPWA.installApp()">
                        Install
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="adminPWA.hideInstallBanner()">
                        Later
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(banner);
    }

    showInstallBanner() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner && !this.isInstalled) {
            banner.classList.remove('hidden');
            
            // Auto-hide after 10 seconds
            setTimeout(() => {
                this.hideInstallBanner();
            }, 10000);
        }
    }

    hideInstallBanner() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            banner.classList.add('hidden');
        }
    }

    async installApp() {
        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('[PWA] User accepted install prompt');
                this.trackEvent('pwa_install_accepted');
            } else {
                console.log('[PWA] User dismissed install prompt');
                this.trackEvent('pwa_install_dismissed');
            }
            
            this.deferredPrompt = null;
            this.hideInstallBanner();
        }
    }

    handleServiceWorkerMessage(event) {
        const { data } = event;
        
        if (data.type === 'SYNC_COMPLETE') {
            console.log('[PWA] Background sync completed');
            this.showNotification('Data synchronized', 'info');
        }
        
        if (data.type === 'CACHE_UPDATED') {
            console.log('[PWA] Cache updated');
        }
    }

    handleServiceWorkerUpdate() {
        const newWorker = this.swRegistration.installing;
        
        newWorker.addEventListener('statechange', () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                this.updateAvailable = true;
                this.showUpdateNotification();
            }
        });
    }

    showUpdateNotification() {
        const notification = document.createElement('div');
        notification.className = 'pwa-update-notification';
        notification.innerHTML = `
            <div class="update-content">
                <strong>Update Available</strong>
                <p>A new version of the admin panel is available.</p>
                <div class="update-actions">
                    <button class="btn btn-primary btn-sm" onclick="adminPWA.applyUpdate()">
                        Update Now
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="this.parentElement.parentElement.parentElement.remove()">
                        Later
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
    }

    applyUpdate() {
        if (this.swRegistration && this.swRegistration.waiting) {
            this.swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
            window.location.reload();
        }
    }

    syncWhenOnline() {
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            navigator.serviceWorker.ready.then((registration) => {
                return registration.sync.register('admin-data-sync');
            }).catch((error) => {
                console.error('[PWA] Background sync registration failed:', error);
            });
        }
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Shift + I - Install app
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'I') {
                e.preventDefault();
                if (this.deferredPrompt) {
                    this.installApp();
                }
            }
            
            // Ctrl/Cmd + Shift + U - Check for updates
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'U') {
                e.preventDefault();
                if (this.swRegistration) {
                    this.swRegistration.update();
                    this.showNotification('Checking for updates...', 'info');
                }
            }
        });
    }

    showNotification(message, type = 'info') {
        // Use existing mobile notification system if available
        if (window.showMobileNotification) {
            window.showMobileNotification(message, type);
            return;
        }
        
        // Fallback notification
        const notification = document.createElement('div');
        notification.className = `pwa-notification pwa-notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    trackEvent(eventName, data = {}) {
        // Track PWA events for analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, {
                event_category: 'PWA',
                ...data
            });
        }
        
        console.log('[PWA] Event tracked:', eventName, data);
    }

    // Utility functions
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        
        return outputArray;
    }

    async sendSubscriptionToServer(subscription) {
        try {
            const response = await fetch('/admin/api/push-subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(subscription)
            });
            
            if (response.ok) {
                console.log('[PWA] Push subscription sent to server');
            }
        } catch (error) {
            console.error('[PWA] Failed to send subscription to server:', error);
        }
    }

    // Public API
    getInstallationStatus() {
        return {
            isInstalled: this.isInstalled,
            canInstall: !!this.deferredPrompt,
            isOnline: this.isOnline,
            updateAvailable: this.updateAvailable
        };
    }

    forceUpdate() {
        if (this.swRegistration) {
            this.swRegistration.update();
        }
    }

    clearCache() {
        if ('caches' in window) {
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        return caches.delete(cacheName);
                    })
                );
            }).then(() => {
                this.showNotification('Cache cleared successfully', 'success');
                window.location.reload();
            });
        }
    }
}

// CSS for PWA components
const pwaCSS = `
.pwa-install-banner {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #1c84ee, #1a73d1);
    color: white;
    z-index: 9999;
    transform: translateY(-100%);
    transition: transform 0.3s ease;
}

.pwa-install-banner:not(.hidden) {
    transform: translateY(0);
}

.pwa-banner-content {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    gap: 16px;
}

.pwa-banner-icon {
    font-size: 24px;
}

.pwa-banner-text {
    flex: 1;
}

.pwa-banner-text strong {
    display: block;
    font-size: 16px;
    margin-bottom: 2px;
}

.pwa-banner-text p {
    margin: 0;
    font-size: 14px;
    opacity: 0.9;
}

.pwa-banner-actions {
    display: flex;
    gap: 8px;
}

.pwa-update-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 20px;
    max-width: 300px;
    z-index: 9999;
    animation: slideInUp 0.3s ease;
}

.pwa-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    padding: 16px 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.pwa-notification.show {
    transform: translateX(0);
}

.pwa-notification-success {
    border-left: 4px solid #198754;
}

.pwa-notification-warning {
    border-left: 4px solid #ffc107;
}

.pwa-notification-error {
    border-left: 4px solid #dc3545;
}

.pwa-notification-info {
    border-left: 4px solid #0d6efd;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Offline styles */
.offline .online-only {
    opacity: 0.5;
    pointer-events: none;
}

.offline::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: #ffc107;
    z-index: 9999;
}

/* PWA installed styles */
.pwa-installed .install-prompt {
    display: none;
}
`;

// Inject CSS
const style = document.createElement('style');
style.textContent = pwaCSS;
document.head.appendChild(style);

// Initialize PWA when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.adminPWA = new AdminPWA();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminPWA;
}
