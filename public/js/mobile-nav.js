/**
 * MechaMap Mobile Navigation
 * Using HC-MobileNav library for enhanced mobile UX
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if HC-MobileNav library is loaded
    if (typeof hcOffcanvasNav === 'undefined') {
        console.warn('HC-MobileNav library not loaded');
        return;
    }

    // Initialize HC-MobileNav only on mobile devices
    const mobileNav = document.getElementById('mobile-nav');
    const toggleButton = document.querySelector('.hc-mobile-nav-toggle');
    
    if (!mobileNav || !toggleButton) {
        console.warn('Mobile navigation elements not found');
        return;
    }

    // Initialize HC-MobileNav
    const nav = new hcOffcanvasNav('#mobile-nav', {
        // Basic settings
        disableAt: 992, // Disable on desktop (Bootstrap lg breakpoint)
        customToggle: '.hc-mobile-nav-toggle',
        
        // Navigation appearance
        navTitle: 'MechaMap',
        levelTitles: true,
        levelTitleAsBack: true,
        
        // Position and behavior
        position: 'left',
        width: 280,
        swipeGestures: true,
        
        // Level behavior
        levelOpen: 'overlap',
        levelSpacing: 40,
        
        // Body interaction
        disableBody: true,
        closeOnClick: true,
        closeOnEsc: true,
        
        // Buttons
        insertClose: true,
        insertBack: true,
        labelClose: 'Đóng',
        labelBack: 'Quay lại',
        
        // Classes and styling
        navClass: 'mechamap-mobile-nav',
        activeToggleClass: 'active',
        
        // Content management
        closeOpenLevels: true,
        closeActiveLevel: false,
        keepClasses: true,
        removeOriginalNav: false,
        
        // Accessibility
        ariaLabels: {
            open: 'Mở menu',
            close: 'Đóng menu',
            submenu: 'Menu con'
        }
    });

    // Add event listeners for enhanced functionality
    nav.on('open', function(e, settings) {
        console.log('Mobile navigation opened');
        
        // Add body class for additional styling
        document.body.classList.add('mobile-nav-open');
        
        // Track analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'mobile_nav_open', {
                'event_category': 'navigation',
                'event_label': 'mobile_menu'
            });
        }
    });

    nav.on('close', function(e, settings) {
        console.log('Mobile navigation closed');
        
        // Remove body class
        document.body.classList.remove('mobile-nav-open');
        
        // Track analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'mobile_nav_close', {
                'event_category': 'navigation',
                'event_label': 'mobile_menu'
            });
        }
    });

    nav.on('open.level', function(e, settings) {
        console.log('Mobile navigation level opened:', e.data);
        
        // Track submenu usage
        if (typeof gtag !== 'undefined') {
            gtag('event', 'mobile_nav_submenu', {
                'event_category': 'navigation',
                'event_label': 'level_' + e.data.currentLevel
            });
        }
    });

    // Handle swipe gestures for better UX
    let touchStartX = 0;
    let touchStartY = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });
    
    document.addEventListener('touchend', function(e) {
        if (!e.changedTouches || e.changedTouches.length === 0) return;
        
        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        
        const deltaX = touchEndX - touchStartX;
        const deltaY = touchEndY - touchStartY;
        
        // Only trigger if horizontal swipe is dominant
        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
            // Swipe from left edge to open
            if (touchStartX < 20 && deltaX > 50 && !nav.isOpen()) {
                nav.open();
            }
            // Swipe right to close when nav is open
            else if (nav.isOpen() && deltaX > 50) {
                nav.close();
            }
        }
    }, { passive: true });

    // Update cart count in mobile nav if cart exists
    function updateMobileCartCount() {
        const cartCount = document.querySelector('#cartCount');
        const mobileCartBadge = document.querySelector('#mobile-nav .fa-shopping-cart').closest('a').querySelector('.badge');
        
        if (cartCount && mobileCartBadge) {
            const count = cartCount.textContent.trim();
            if (count && count !== '0') {
                mobileCartBadge.textContent = count;
                mobileCartBadge.style.display = 'inline-block';
            } else {
                mobileCartBadge.style.display = 'none';
            }
        }
    }

    // Update cart count on page load and when cart changes
    updateMobileCartCount();
    
    // Listen for cart updates
    document.addEventListener('cartUpdated', updateMobileCartCount);
    
    // Observe cart count changes
    const cartCountElement = document.querySelector('#cartCount');
    if (cartCountElement) {
        const observer = new MutationObserver(updateMobileCartCount);
        observer.observe(cartCountElement, { childList: true, subtree: true });
    }

    // Handle notification count updates in mobile nav
    function updateMobileNotificationCount() {
        const notificationCount = document.querySelector('.notification-count');
        const mobileNotificationLink = document.querySelector('#mobile-nav .fa-bell').closest('a');
        
        if (notificationCount && mobileNotificationLink) {
            const count = notificationCount.textContent.trim();
            let badge = mobileNotificationLink.querySelector('.badge');
            
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'badge bg-danger ms-auto';
                mobileNotificationLink.appendChild(badge);
            }
            
            if (count && count !== '0') {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // Update notification count
    updateMobileNotificationCount();
    
    // Listen for notification updates
    document.addEventListener('notificationsUpdated', updateMobileNotificationCount);

    // Expose nav instance globally for debugging
    window.MechaMapMobileNav = nav;
    
    console.log('MechaMap Mobile Navigation initialized successfully');
});
