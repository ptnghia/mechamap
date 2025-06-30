/**
 * MechaMap Admin Service Worker
 * Provides offline functionality and background sync for admin panel
 */

const CACHE_NAME = 'mechamap-admin-v1.0.0';
const OFFLINE_URL = '/admin/offline';
const API_CACHE_NAME = 'mechamap-admin-api-v1.0.0';

// Resources to cache immediately
const STATIC_CACHE_URLS = [
    '/admin/dashboard',
    '/admin/offline',
    '/assets/css/bootstrap.min.css',
    '/assets/css/app.min.css',
    '/assets/css/admin-mobile.css',
    '/assets/js/bootstrap.bundle.min.js',
    '/assets/js/app.js',
    '/assets/js/admin-mobile.js',
    '/assets/libs/apexcharts/apexcharts.min.js',
    '/assets/images/logo-dark.png',
    '/assets/images/logo-light.png',
    '/admin-manifest.json'
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
    /\/admin\/api\/dashboard-stats/,
    /\/admin\/api\/notifications/,
    /\/admin\/analytics\/realtime\/metrics/,
    /\/admin\/api\/user-activity/
];

// Install event - cache static resources
self.addEventListener('install', event => {
    console.log('[SW] Installing service worker...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('[SW] Caching static resources');
                return cache.addAll(STATIC_CACHE_URLS);
            })
            .then(() => {
                console.log('[SW] Static resources cached successfully');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('[SW] Failed to cache static resources:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('[SW] Activating service worker...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== CACHE_NAME && cacheName !== API_CACHE_NAME) {
                            console.log('[SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('[SW] Service worker activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - handle requests with caching strategy
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip external requests
    if (url.origin !== location.origin) {
        return;
    }
    
    // Handle admin routes
    if (url.pathname.startsWith('/admin/')) {
        event.respondWith(handleAdminRequest(request));
    }
});

// Handle admin requests with appropriate caching strategy
async function handleAdminRequest(request) {
    const url = new URL(request.url);
    
    try {
        // API requests - Network First with cache fallback
        if (isApiRequest(url)) {
            return await networkFirstStrategy(request, API_CACHE_NAME);
        }
        
        // Static assets - Cache First
        if (isStaticAsset(url)) {
            return await cacheFirstStrategy(request, CACHE_NAME);
        }
        
        // Admin pages - Network First with offline fallback
        if (isAdminPage(url)) {
            return await networkFirstWithOfflineFallback(request);
        }
        
        // Default: Network First
        return await networkFirstStrategy(request, CACHE_NAME);
        
    } catch (error) {
        console.error('[SW] Request failed:', error);
        return await getOfflineResponse(request);
    }
}

// Network First strategy - try network, fallback to cache
async function networkFirstStrategy(request, cacheName) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache successful responses
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('[SW] Network failed, trying cache:', request.url);
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        throw error;
    }
}

// Cache First strategy - try cache, fallback to network
async function cacheFirstStrategy(request, cacheName) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.error('[SW] Cache and network both failed:', error);
        throw error;
    }
}

// Network First with offline fallback for admin pages
async function networkFirstWithOfflineFallback(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('[SW] Network failed for admin page, checking cache:', request.url);
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page
        return await caches.match(OFFLINE_URL) || new Response(
            getOfflineHTML(),
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

// Get offline response based on request type
async function getOfflineResponse(request) {
    const url = new URL(request.url);
    
    if (isApiRequest(url)) {
        return new Response(
            JSON.stringify({
                success: false,
                message: 'Offline - data not available',
                offline: true
            }),
            {
                headers: { 'Content-Type': 'application/json' },
                status: 503
            }
        );
    }
    
    if (isAdminPage(url)) {
        return await caches.match(OFFLINE_URL) || new Response(
            getOfflineHTML(),
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
    
    return new Response('Offline', { status: 503 });
}

// Helper functions
function isApiRequest(url) {
    return url.pathname.includes('/api/') || 
           API_CACHE_PATTERNS.some(pattern => pattern.test(url.pathname));
}

function isStaticAsset(url) {
    return url.pathname.includes('/assets/') ||
           url.pathname.endsWith('.css') ||
           url.pathname.endsWith('.js') ||
           url.pathname.endsWith('.png') ||
           url.pathname.endsWith('.jpg') ||
           url.pathname.endsWith('.svg') ||
           url.pathname.endsWith('.ico');
}

function isAdminPage(url) {
    return url.pathname.startsWith('/admin/') && 
           !isApiRequest(url) && 
           !isStaticAsset(url);
}

function getOfflineHTML() {
    return `
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Offline - MechaMap Admin</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background: #f8f9fa;
                    color: #333;
                    text-align: center;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .offline-container {
                    max-width: 400px;
                    background: white;
                    padding: 40px 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                }
                .offline-icon {
                    font-size: 64px;
                    margin-bottom: 20px;
                    opacity: 0.6;
                }
                h1 {
                    color: #1c84ee;
                    margin-bottom: 16px;
                    font-size: 24px;
                }
                p {
                    color: #6c757d;
                    line-height: 1.5;
                    margin-bottom: 24px;
                }
                .retry-btn {
                    background: #1c84ee;
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-size: 16px;
                    cursor: pointer;
                    transition: background 0.3s;
                }
                .retry-btn:hover {
                    background: #1a73d1;
                }
            </style>
        </head>
        <body>
            <div class="offline-container">
                <div class="offline-icon">📡</div>
                <h1>Bạn đang offline</h1>
                <p>Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối internet và thử lại.</p>
                <button class="retry-btn" onclick="window.location.reload()">
                    Thử lại
                </button>
            </div>
        </body>
        </html>
    `;
}

// Background sync for failed requests
self.addEventListener('sync', event => {
    console.log('[SW] Background sync triggered:', event.tag);
    
    if (event.tag === 'admin-data-sync') {
        event.waitUntil(syncAdminData());
    }
});

// Sync admin data when back online
async function syncAdminData() {
    try {
        console.log('[SW] Syncing admin data...');
        
        // Sync critical admin data
        const syncPromises = [
            fetch('/admin/api/dashboard-stats'),
            fetch('/admin/api/notifications'),
            fetch('/admin/analytics/realtime/metrics')
        ];
        
        await Promise.allSettled(syncPromises);
        console.log('[SW] Admin data sync completed');
        
        // Notify clients about sync completion
        const clients = await self.clients.matchAll();
        clients.forEach(client => {
            client.postMessage({
                type: 'SYNC_COMPLETE',
                data: { timestamp: Date.now() }
            });
        });
        
    } catch (error) {
        console.error('[SW] Admin data sync failed:', error);
    }
}

// Push notification handling
self.addEventListener('push', event => {
    console.log('[SW] Push notification received');
    
    const options = {
        body: 'You have new admin notifications',
        icon: '/assets/images/icons/admin-icon-192x192.png',
        badge: '/assets/images/icons/admin-badge.png',
        tag: 'admin-notification',
        requireInteraction: true,
        actions: [
            {
                action: 'view',
                title: 'View Dashboard',
                icon: '/assets/images/icons/view-action.png'
            },
            {
                action: 'dismiss',
                title: 'Dismiss',
                icon: '/assets/images/icons/dismiss-action.png'
            }
        ]
    };
    
    if (event.data) {
        const data = event.data.json();
        options.body = data.message || options.body;
        options.title = data.title || 'MechaMap Admin';
        options.data = data;
    }
    
    event.waitUntil(
        self.registration.showNotification('MechaMap Admin', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
    console.log('[SW] Notification clicked:', event.action);
    
    event.notification.close();
    
    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow('/admin/dashboard')
        );
    } else if (event.action === 'dismiss') {
        // Just close the notification
        return;
    } else {
        // Default action - open admin dashboard
        event.waitUntil(
            clients.openWindow('/admin/dashboard')
        );
    }
});

// Message handling from clients
self.addEventListener('message', event => {
    console.log('[SW] Message received:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});
