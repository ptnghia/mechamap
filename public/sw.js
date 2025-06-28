/**
 * Service Worker for MechaMap
 * Handles caching, offline functionality, and performance optimization
 */

const CACHE_NAME = 'mechamap-v1.0.0';
const STATIC_CACHE = 'mechamap-static-v1.0.0';
const DYNAMIC_CACHE = 'mechamap-dynamic-v1.0.0';

// Static assets to cache
const STATIC_ASSETS = [
    '/',
    '/css/app.css',
    '/css/enhanced-menu.css',
    '/js/app.js',
    '/js/enhanced-menu.js',
    '/js/performance-optimization.js',
    '/images/logo.svg',
    '/images/favicon.svg',
    '/offline.html'
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
    /\/api\/materials/,
    /\/api\/standards/,
    /\/api\/search/,
    /\/api\/user\/notifications/
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('Service Worker installing...');

    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('Caching static assets...');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('Static assets cached successfully');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('Failed to cache static assets:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker activating...');

    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== STATIC_CACHE &&
                            cacheName !== DYNAMIC_CACHE &&
                            cacheName !== CACHE_NAME) {
                            console.log('Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Old caches cleaned up');
                return self.clients.claim();
            })
    );
});

// Fetch event - handle requests with caching strategies
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip chrome-extension and other non-http requests
    if (!request.url.startsWith('http')) {
        return;
    }

    // Handle different types of requests
    if (isStaticAsset(request)) {
        event.respondWith(handleStaticAsset(request));
    } else if (isAPIRequest(request)) {
        event.respondWith(handleAPIRequest(request));
    } else if (isPageRequest(request)) {
        event.respondWith(handlePageRequest(request));
    } else {
        event.respondWith(handleOtherRequest(request));
    }
});

/**
 * Check if request is for static asset
 */
function isStaticAsset(request) {
    const url = new URL(request.url);
    return url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/);
}

/**
 * Check if request is for API
 */
function isAPIRequest(request) {
    const url = new URL(request.url);
    return url.pathname.startsWith('/api/') ||
           API_CACHE_PATTERNS.some(pattern => pattern.test(url.pathname));
}

/**
 * Check if request is for page
 */
function isPageRequest(request) {
    return request.headers.get('accept').includes('text/html');
}

/**
 * Handle static asset requests - Cache First strategy
 */
function handleStaticAsset(request) {
    return caches.match(request)
        .then(cachedResponse => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(request)
                .then(response => {
                    // Cache successful responses
                    if (response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(STATIC_CACHE)
                            .then(cache => {
                                cache.put(request, responseClone);
                            });
                    }
                    return response;
                })
                .catch(() => {
                    // Return offline fallback for images
                    if (request.url.match(/\.(png|jpg|jpeg|gif|svg)$/)) {
                        return new Response(
                            '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f0f0f0"/><text x="100" y="100" text-anchor="middle" fill="#999">Image Offline</text></svg>',
                            { headers: { 'Content-Type': 'image/svg+xml' } }
                        );
                    }
                });
        });
}

/**
 * Handle API requests - Network First with cache fallback
 */
function handleAPIRequest(request) {
    return fetch(request)
        .then(response => {
            // Cache successful API responses
            if (response.status === 200) {
                const responseClone = response.clone();
                caches.open(DYNAMIC_CACHE)
                    .then(cache => {
                        cache.put(request, responseClone);
                    });
            }
            return response;
        })
        .catch(() => {
            // Fallback to cache
            return caches.match(request)
                .then(cachedResponse => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // Return offline API response
                    return new Response(
                        JSON.stringify({
                            error: 'Offline',
                            message: 'This content is not available offline'
                        }),
                        {
                            status: 503,
                            headers: { 'Content-Type': 'application/json' }
                        }
                    );
                });
        });
}

/**
 * Handle page requests - Network First with cache fallback
 */
function handlePageRequest(request) {
    return fetch(request)
        .then(response => {
            // Cache successful page responses
            if (response.status === 200) {
                const responseClone = response.clone();
                caches.open(DYNAMIC_CACHE)
                    .then(cache => {
                        cache.put(request, responseClone);
                    });
            }
            return response;
        })
        .catch(() => {
            // Fallback to cache
            return caches.match(request)
                .then(cachedResponse => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // Return offline page
                    return caches.match('/offline.html');
                });
        });
}

/**
 * Handle other requests - Network only
 */
function handleOtherRequest(request) {
    return fetch(request);
}

// Background sync for offline actions
self.addEventListener('sync', event => {
    console.log('Background sync triggered:', event.tag);

    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

/**
 * Perform background sync
 */
function doBackgroundSync() {
    return new Promise((resolve, reject) => {
        // Get offline actions from IndexedDB
        // Process queued actions when online
        // This would integrate with your offline action queue
        console.log('Performing background sync...');
        resolve();
    });
}

// Push notifications
self.addEventListener('push', event => {
    console.log('Push notification received:', event);

    const options = {
        body: event.data ? event.data.text() : 'New notification from MechaMap',
        icon: '/images/icon-192x192.png',
        badge: '/images/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/images/checkmark.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/images/xmark.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('MechaMap', options)
    );
});

// Notification click handling
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event);

    event.notification.close();

    if (event.action === 'explore') {
        // Open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message handling from main thread
self.addEventListener('message', event => {
    console.log('Message received:', event.data);

    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'CACHE_URLS') {
        event.waitUntil(
            caches.open(DYNAMIC_CACHE)
                .then(cache => {
                    return cache.addAll(event.data.urls);
                })
        );
    }
});

// Periodic background sync (if supported)
self.addEventListener('periodicsync', event => {
    console.log('Periodic sync triggered:', event.tag);

    if (event.tag === 'content-sync') {
        event.waitUntil(syncContent());
    }
});

/**
 * Sync content in background
 */
function syncContent() {
    return fetch('/api/sync/content')
        .then(response => response.json())
        .then(data => {
            // Update cached content
            return caches.open(DYNAMIC_CACHE)
                .then(cache => {
                    // Cache updated content
                    return Promise.all(
                        data.urls.map(url => {
                            return fetch(url).then(response => {
                                if (response.status === 200) {
                                    return cache.put(url, response);
                                }
                            });
                        })
                    );
                });
        })
        .catch(error => {
            console.error('Content sync failed:', error);
        });
}

// Error handling
self.addEventListener('error', event => {
    console.error('Service Worker error:', event.error);
});

self.addEventListener('unhandledrejection', event => {
    console.error('Service Worker unhandled rejection:', event.reason);
});

console.log('Service Worker loaded successfully');
