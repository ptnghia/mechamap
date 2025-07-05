/**
 * MechaMap Simplified Menu System
 * Handles menu functionality without search (search moved to unified-search.js)
 * Uses Bootstrap 5 native functionality where possible
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize menu components
    initMenuActiveStates();
    initDarkModeToggle();
    initMobileMenuEnhancements();
    initMenuAnalytics();
});

/**
 * Menu Active States Management
 */
function initMenuActiveStates() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.navbar-nav .nav-link');

    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        
        if (href && currentPath.startsWith(href) && href !== '/') {
            item.classList.add('active');
            
            // Handle dropdown parent
            const dropdownParent = item.closest('.dropdown');
            if (dropdownParent) {
                const dropdownToggle = dropdownParent.querySelector('.dropdown-toggle');
                if (dropdownToggle) {
                    dropdownToggle.classList.add('active');
                }
            }
        }
    });

    // Special handling for homepage
    if (currentPath === '/') {
        const homeLink = document.querySelector('.navbar-nav .nav-link[href="/"]');
        if (homeLink) {
            homeLink.classList.add('active');
        }
    }
}

/**
 * Dark Mode Toggle
 */
function initDarkModeToggle() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (!darkModeToggle) return;

    // Load saved theme
    const savedTheme = localStorage.getItem('mechamap_theme') || 'light';
    applyTheme(savedTheme);

    // Toggle event
    darkModeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        applyTheme(newTheme);
        localStorage.setItem('mechamap_theme', newTheme);

        // Track theme change
        if (typeof gtag !== 'undefined') {
            gtag('event', 'theme_change', {
                'event_category': 'UI',
                'event_label': newTheme
            });
        }
    });

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        
        // Update toggle icon
        const icon = darkModeToggle.querySelector('i');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'fas fa-sun';
                darkModeToggle.setAttribute('title', 'Switch to Light Mode');
            } else {
                icon.className = 'fas fa-moon';
                darkModeToggle.setAttribute('title', 'Switch to Dark Mode');
            }
        }

        // Update Bootstrap theme if using theme-aware Bootstrap
        if (theme === 'dark') {
            document.body.setAttribute('data-bs-theme', 'dark');
        } else {
            document.body.removeAttribute('data-bs-theme');
        }
    }
}

/**
 * Mobile Menu Enhancements
 */
function initMobileMenuEnhancements() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (!navbarToggler || !navbarCollapse) return;

    // Close mobile menu when clicking on a link
    const mobileMenuLinks = navbarCollapse.querySelectorAll('.nav-link:not(.dropdown-toggle)');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Close mobile menu
            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        });
    });

    // Handle dropdown behavior on mobile
    const dropdownToggles = navbarCollapse.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            // On mobile, prevent default Bootstrap dropdown behavior
            if (window.innerWidth < 992) {
                e.preventDefault();
                e.stopPropagation();
                
                const dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    // Toggle dropdown menu visibility
                    if (dropdownMenu.style.display === 'block') {
                        dropdownMenu.style.display = 'none';
                        this.setAttribute('aria-expanded', 'false');
                    } else {
                        // Close other open dropdowns
                        navbarCollapse.querySelectorAll('.dropdown-menu').forEach(menu => {
                            menu.style.display = 'none';
                        });
                        navbarCollapse.querySelectorAll('.dropdown-toggle').forEach(t => {
                            t.setAttribute('aria-expanded', 'false');
                        });
                        
                        // Open this dropdown
                        dropdownMenu.style.display = 'block';
                        this.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        });
    });

    // Close dropdowns when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992) {
            if (!e.target.closest('.dropdown')) {
                navbarCollapse.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
                navbarCollapse.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        }
    });
}

/**
 * Menu Analytics & Performance
 */
function initMenuAnalytics() {
    // Track menu interactions
    document.addEventListener('click', function(e) {
        if (e.target.matches('.nav-link, .dropdown-item')) {
            const linkText = e.target.textContent.trim();
            const linkHref = e.target.getAttribute('href');
            
            // Track navigation clicks
            if (typeof gtag !== 'undefined') {
                gtag('event', 'navigation_click', {
                    'event_category': 'Navigation',
                    'event_label': linkText,
                    'value': linkHref
                });
            }
        }
    });

    // Track mobile menu usage
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'mobile_menu_toggle', {
                    'event_category': 'Mobile',
                    'event_label': 'Menu Toggle'
                });
            }
        });
    }
}

/**
 * Language Switcher Enhancement
 */
function initLanguageSwitcher() {
    const languageDropdown = document.querySelector('.language-switcher');
    if (!languageDropdown) return;

    const languageOptions = languageDropdown.querySelectorAll('.dropdown-item');
    languageOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedLang = this.dataset.lang;
            if (selectedLang) {
                // Track language change
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'language_change', {
                        'event_category': 'Localization',
                        'event_label': selectedLang
                    });
                }

                // Change language (implement your language switching logic here)
                changeLanguage(selectedLang);
            }
        });
    });

    function changeLanguage(lang) {
        // Add your language switching implementation
        // This could be a form submit, AJAX call, or URL redirect
        console.log('Changing language to:', lang);
        
        // Example: redirect with language parameter
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('lang', lang);
        window.location.href = currentUrl.toString();
    }
}

/**
 * Notification Badge Updates
 */
function initNotificationBadges() {
    // Update notification badges periodically
    updateNotificationBadges();
    
    // Set interval for updates (every 30 seconds)
    setInterval(updateNotificationBadges, 30000);

    function updateNotificationBadges() {
        // Only update if user is authenticated
        const isAuthenticated = document.querySelector('meta[name="user-authenticated"]');
        if (!isAuthenticated || isAuthenticated.content !== 'true') {
            return;
        }

        fetch('/api/notifications/count')
            .then(response => response.json())
            .then(data => {
                updateBadge('notifications-badge', data.notifications || 0);
                updateBadge('messages-badge', data.messages || 0);
            })
            .catch(error => {
                console.warn('Failed to update notification badges:', error);
            });
    }

    function updateBadge(badgeId, count) {
        const badge = document.getElementById(badgeId);
        if (!badge) return;

        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count.toString();
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Menu Performance Optimization
 */
function initMenuPerformanceOptimization() {
    // Preload critical navigation pages
    const criticalLinks = [
        '/threads',
        '/showcase',
        '/marketplace',
        '/community'
    ];

    // Preload on hover with delay
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        let preloadTimeout;
        
        link.addEventListener('mouseenter', function() {
            const href = this.getAttribute('href');
            if (href && criticalLinks.includes(href)) {
                preloadTimeout = setTimeout(() => {
                    preloadPage(href);
                }, 500); // 500ms delay
            }
        });

        link.addEventListener('mouseleave', function() {
            if (preloadTimeout) {
                clearTimeout(preloadTimeout);
            }
        });
    });

    function preloadPage(url) {
        // Check if already preloaded
        if (document.querySelector(`link[rel="prefetch"][href="${url}"]`)) {
            return;
        }

        // Create prefetch link
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        document.head.appendChild(link);
    }
}

// Initialize additional features
document.addEventListener('DOMContentLoaded', function() {
    initLanguageSwitcher();
    initNotificationBadges();
    initMenuPerformanceOptimization();
});
