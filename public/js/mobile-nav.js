/**
 * MechaMap Mobile Navigation - Additional Features
 * Note: Main navigation initialization is now handled in mobile-nav.blade.php
 * This file provides additional mobile-specific features and utilities
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Mobile navigation additional features loaded');

    // Additional mobile-specific features can be added here
    // Main HC-MobileNav initialization is handled in mobile-nav.blade.php

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
            if (touchStartX < 20 && deltaX > 50) {
                if (window.MechaMapMobileNav && typeof window.MechaMapMobileNav.open === 'function') {
                    window.MechaMapMobileNav.open();
                }
            }
            // Swipe right to close when nav is open
            else if (deltaX > 50) {
                if (window.MechaMapMobileNav && typeof window.MechaMapMobileNav.close === 'function') {
                    window.MechaMapMobileNav.close();
                }
            }
        }
    }, { passive: true });

    // Update cart count in mobile nav if cart exists
    function updateMobileCartCount() {
        const cartCount = document.querySelector('#cartCount');
        const mobileCartCount = document.querySelector('#mobileCartCount');

        if (cartCount && mobileCartCount) {
            const count = cartCount.textContent.trim();
            if (count && count !== '0') {
                mobileCartCount.textContent = count;
                mobileCartCount.style.display = 'inline';
            } else {
                mobileCartCount.style.display = 'none';
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
        const mobileNotificationIcon = document.querySelector('#mobile-nav .fa-bell');

        if (notificationCount && mobileNotificationIcon) {
            const mobileNotificationLink = mobileNotificationIcon.closest('a');
            if (mobileNotificationLink) {
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
    }

    // Update notification count
    updateMobileNotificationCount();

    // Listen for notification updates
    document.addEventListener('notificationsUpdated', updateMobileNotificationCount);

    console.log('Mobile navigation additional features initialized successfully');
});
