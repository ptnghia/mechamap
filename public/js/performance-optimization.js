/**
 * Performance Optimization Script for MechaMap
 * Handles lazy loading, caching, and performance monitoring
 */

document.addEventListener('DOMContentLoaded', function() {

    // Initialize performance optimizations
    initializeLazyLoading();
    initializeImageOptimization();
    initializeMenuCaching();
    initializePerformanceMonitoring();
    initializeServiceWorker();

    /**
     * Initialize lazy loading for images and content
     */
    function initializeLazyLoading() {
        // Intersection Observer for lazy loading
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            // Observe all lazy images
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });

            // Lazy load content sections
            const contentObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        if (element.dataset.lazyLoad) {
                            loadLazyContent(element);
                        }
                    }
                });
            });

            document.querySelectorAll('[data-lazy-load]').forEach(element => {
                contentObserver.observe(element);
            });
        }
    }

    /**
     * Initialize image optimization
     */
    function initializeImageOptimization() {
        // WebP support detection
        const supportsWebP = (function() {
            const canvas = document.createElement('canvas');
            canvas.width = 1;
            canvas.height = 1;
            return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
        })();

        if (supportsWebP) {
            document.documentElement.classList.add('webp-support');

            // Replace image sources with WebP versions
            document.querySelectorAll('img[data-webp]').forEach(img => {
                img.src = img.dataset.webp;
            });
        }

        // Progressive image loading
        document.querySelectorAll('img.progressive').forEach(img => {
            const lowRes = img.dataset.lowres;
            const highRes = img.src;

            if (lowRes) {
                img.src = lowRes;
                img.classList.add('loading');

                const highResImg = new Image();
                highResImg.onload = function() {
                    img.src = highRes;
                    img.classList.remove('loading');
                    img.classList.add('loaded');
                };
                highResImg.src = highRes;
            }
        });
    }

    /**
     * Initialize menu caching
     */
    function initializeMenuCaching() {
        const CACHE_KEY = 'mechamap_menu_cache';
        const CACHE_DURATION = 30 * 60 * 1000; // 30 minutes

        // Cache menu data
        function cacheMenuData(data) {
            const cacheData = {
                data: data,
                timestamp: Date.now()
            };
            localStorage.setItem(CACHE_KEY, JSON.stringify(cacheData));
        }

        // Get cached menu data
        function getCachedMenuData() {
            try {
                const cached = localStorage.getItem(CACHE_KEY);
                if (cached) {
                    const cacheData = JSON.parse(cached);
                    if (Date.now() - cacheData.timestamp < CACHE_DURATION) {
                        return cacheData.data;
                    }
                }
            } catch (e) {
                console.warn('Failed to parse cached menu data');
            }
            return null;
        }

        // Preload menu content
        const menuDropdowns = document.querySelectorAll('.dropdown-menu[data-preload]');
        menuDropdowns.forEach(menu => {
            const url = menu.dataset.preload;
            const cached = getCachedMenuData();

            if (cached && cached[url]) {
                menu.innerHTML = cached[url];
            } else {
                // Fetch and cache menu content
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        menu.innerHTML = html;
                        const cacheData = getCachedMenuData() || {};
                        cacheData[url] = html;
                        cacheMenuData(cacheData);
                    })
                    .catch(error => {
                        console.warn('Failed to preload menu content:', error);
                    });
            }
        });
    }

    /**
     * Initialize performance monitoring
     */
    function initializePerformanceMonitoring() {
        // Performance metrics collection
        const performanceMetrics = {
            navigationStart: performance.timing.navigationStart,
            domContentLoaded: performance.timing.domContentLoadedEventEnd - performance.timing.navigationStart,
            windowLoaded: 0,
            firstPaint: 0,
            firstContentfulPaint: 0
        };

        // Capture window load time
        window.addEventListener('load', function() {
            performanceMetrics.windowLoaded = performance.timing.loadEventEnd - performance.timing.navigationStart;

            // Send metrics to analytics (if available)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'page_performance', {
                    'dom_content_loaded': performanceMetrics.domContentLoaded,
                    'window_loaded': performanceMetrics.windowLoaded
                });
            }
        });

        // Capture paint metrics
        if ('PerformanceObserver' in window) {
            const paintObserver = new PerformanceObserver((list) => {
                list.getEntries().forEach(entry => {
                    if (entry.name === 'first-paint') {
                        performanceMetrics.firstPaint = entry.startTime;
                    } else if (entry.name === 'first-contentful-paint') {
                        performanceMetrics.firstContentfulPaint = entry.startTime;
                    }
                });
            });

            paintObserver.observe({ entryTypes: ['paint'] });
        }

        // Monitor long tasks
        if ('PerformanceObserver' in window) {
            const longTaskObserver = new PerformanceObserver((list) => {
                list.getEntries().forEach(entry => {
                    if (entry.duration > 50) {
                        console.warn('Long task detected:', entry.duration + 'ms');

                        // Report to analytics
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'long_task', {
                                'duration': entry.duration,
                                'start_time': entry.startTime
                            });
                        }
                    }
                });
            });

            longTaskObserver.observe({ entryTypes: ['longtask'] });
        }
    }

    /**
     * Initialize Service Worker for caching
     */
    function initializeServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');

                        // Update available
                        registration.addEventListener('updatefound', function() {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', function() {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // Show update notification
                                    showUpdateNotification();
                                }
                            });
                        });
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed:', error);
                    });
            });
        }
    }

    /**
     * Load lazy content
     */
    function loadLazyContent(element) {
        const url = element.dataset.lazyLoad;
        const placeholder = element.querySelector('.lazy-placeholder');

        if (placeholder) {
            placeholder.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
        }

        fetch(url)
            .then(response => response.text())
            .then(html => {
                element.innerHTML = html;
                element.classList.add('lazy-loaded');

                // Trigger any scripts in the loaded content
                const scripts = element.querySelectorAll('script');
                scripts.forEach(script => {
                    const newScript = document.createElement('script');
                    newScript.textContent = script.textContent;
                    script.parentNode.replaceChild(newScript, script);
                });
            })
            .catch(error => {
                console.error('Failed to load lazy content:', error);
                if (placeholder) {
                    placeholder.innerHTML = '<p class="text-muted">Failed to load content</p>';
                }
            });
    }

    /**
     * Show update notification
     */
    function showUpdateNotification() {
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
        notification.innerHTML = `
            <strong>Update Available!</strong>
            <p class="mb-2">A new version of MechaMap is available.</p>
            <button type="button" class="btn btn-sm btn-primary me-2" onclick="location.reload()">
                Update Now
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-dismiss after 10 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 10000);
    }

    /**
     * Optimize scroll performance
     */
    let ticking = false;
    function optimizeScroll() {
        if (!ticking) {
            requestAnimationFrame(function() {
                // Scroll-based optimizations
                const scrollTop = window.pageYOffset;

                // Hide/show elements based on scroll
                const header = document.querySelector('.navbar');
                if (header) {
                    if (scrollTop > 100) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                }

                ticking = false;
            });
            ticking = true;
        }
    }

    window.addEventListener('scroll', optimizeScroll, { passive: true });

    /**
     * Preload critical resources
     */
    function preloadCriticalResources() {
        const criticalResources = [
            '/css/enhanced-menu.css',
            '/js/enhanced-menu.js',
            '/api/notifications'
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = resource;
            document.head.appendChild(link);
        });
    }

    // Initialize preloading
    preloadCriticalResources();

    /**
     * Memory management
     */
    function cleanupMemory() {
        // Remove unused event listeners
        const unusedElements = document.querySelectorAll('.removed, .hidden');
        unusedElements.forEach(element => {
            element.removeEventListener('click', null);
            element.removeEventListener('scroll', null);
        });

        // Clear old cache entries
        const cacheKeys = Object.keys(localStorage);
        cacheKeys.forEach(key => {
            if (key.startsWith('mechamap_') && key.includes('_cache')) {
                try {
                    const cached = JSON.parse(localStorage.getItem(key));
                    if (cached.timestamp && Date.now() - cached.timestamp > 24 * 60 * 60 * 1000) {
                        localStorage.removeItem(key);
                    }
                } catch (e) {
                    localStorage.removeItem(key);
                }
            }
        });
    }

    // Run cleanup every 5 minutes
    setInterval(cleanupMemory, 5 * 60 * 1000);

    // Cleanup on page unload
    window.addEventListener('beforeunload', cleanupMemory);
});
