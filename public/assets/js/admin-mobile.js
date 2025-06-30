/**
 * MechaMap Admin Mobile JavaScript
 * Touch interactions and mobile-specific functionality
 */

class AdminMobile {
    constructor() {
        this.isMobile = window.innerWidth <= 768;
        this.isTouch = 'ontouchstart' in window;
        this.sidebar = null;
        this.sidebarOverlay = null;
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.isScrolling = false;
        
        this.init();
    }

    init() {
        this.setupMobileDetection();
        this.setupTouchInteractions();
        this.setupMobileSidebar();
        this.setupMobileTables();
        this.setupMobileModals();
        this.setupSwipeGestures();
        this.setupMobileSearch();
        this.setupMobileNotifications();
        this.setupMobileKeyboard();
        
        // Listen for orientation changes
        window.addEventListener('orientationchange', () => {
            setTimeout(() => this.handleOrientationChange(), 100);
        });
        
        // Listen for resize
        window.addEventListener('resize', () => this.handleResize());
    }

    setupMobileDetection() {
        // Add mobile class to body
        if (this.isMobile) {
            document.body.classList.add('mobile-device');
        }
        
        if (this.isTouch) {
            document.body.classList.add('touch-device');
        }
        
        // Detect iOS
        if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
            document.body.classList.add('ios-device');
        }
        
        // Detect Android
        if (/Android/.test(navigator.userAgent)) {
            document.body.classList.add('android-device');
        }
    }

    setupTouchInteractions() {
        if (!this.isTouch) return;

        // Add ripple effect to buttons
        document.addEventListener('touchstart', (e) => {
            if (e.target.classList.contains('btn')) {
                this.createRipple(e);
            }
        });

        // Improve touch feedback
        document.addEventListener('touchstart', (e) => {
            e.target.classList.add('touch-active');
        });

        document.addEventListener('touchend', (e) => {
            setTimeout(() => {
                e.target.classList.remove('touch-active');
            }, 150);
        });

        // Prevent double-tap zoom on buttons
        document.addEventListener('touchend', (e) => {
            if (e.target.classList.contains('btn') || e.target.closest('.btn')) {
                e.preventDefault();
            }
        });
    }

    createRipple(e) {
        const button = e.target;
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.touches[0].clientX - rect.left - size / 2;
        const y = e.touches[0].clientY - rect.top - size / 2;
        
        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    setupMobileSidebar() {
        this.sidebar = document.querySelector('.sidebar');
        this.sidebarOverlay = document.querySelector('.sidebar-overlay');
        
        if (!this.sidebar) return;

        // Create overlay if it doesn't exist
        if (!this.sidebarOverlay) {
            this.sidebarOverlay = document.createElement('div');
            this.sidebarOverlay.className = 'sidebar-overlay';
            document.body.appendChild(this.sidebarOverlay);
        }

        // Mobile sidebar toggle
        const toggleBtn = document.querySelector('.navbar-toggler');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleSidebar());
        }

        // Close sidebar when clicking overlay
        this.sidebarOverlay.addEventListener('click', () => this.closeSidebar());

        // Close sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.sidebar.classList.contains('show')) {
                this.closeSidebar();
            }
        });

        // Handle submenu toggles
        const submenuToggles = this.sidebar.querySelectorAll('.has-arrow');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSubmenu(toggle);
            });
        });
    }

    toggleSidebar() {
        this.sidebar.classList.toggle('show');
        this.sidebarOverlay.classList.toggle('show');
        document.body.classList.toggle('sidebar-open');
    }

    closeSidebar() {
        this.sidebar.classList.remove('show');
        this.sidebarOverlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }

    toggleSubmenu(toggle) {
        const submenu = toggle.nextElementSibling;
        const isOpen = submenu.style.display === 'block';
        
        // Close all other submenus
        this.sidebar.querySelectorAll('.sub-menu').forEach(menu => {
            menu.style.display = 'none';
        });
        
        // Toggle current submenu
        submenu.style.display = isOpen ? 'none' : 'block';
        toggle.classList.toggle('active', !isOpen);
    }

    setupMobileTables() {
        if (!this.isMobile) return;

        const tables = document.querySelectorAll('.table-responsive');
        tables.forEach(tableContainer => {
            this.convertTableToCards(tableContainer);
        });
    }

    convertTableToCards(tableContainer) {
        const table = tableContainer.querySelector('table');
        if (!table) return;

        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        const rows = table.querySelectorAll('tbody tr');
        
        const cardsContainer = document.createElement('div');
        cardsContainer.className = 'mobile-table-cards';
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const card = this.createMobileCard(headers, cells);
            cardsContainer.appendChild(card);
        });
        
        tableContainer.appendChild(cardsContainer);
    }

    createMobileCard(headers, cells) {
        const card = document.createElement('div');
        card.className = 'mobile-table-card';
        
        let cardHTML = '<div class="card-body">';
        
        cells.forEach((cell, index) => {
            if (headers[index] && cell.textContent.trim()) {
                cardHTML += `
                    <div class="field">
                        <span class="field-label">${headers[index]}</span>
                        <span class="field-value">${cell.innerHTML}</span>
                    </div>
                `;
            }
        });
        
        // Extract action buttons
        const actionCell = Array.from(cells).find(cell => 
            cell.querySelector('.btn') || cell.classList.contains('actions')
        );
        
        if (actionCell) {
            cardHTML += `<div class="actions">${actionCell.innerHTML}</div>`;
        }
        
        cardHTML += '</div>';
        card.innerHTML = cardHTML;
        
        return card;
    }

    setupMobileModals() {
        if (!this.isMobile) return;

        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            // Add mobile class for styling
            modal.classList.add('mobile-modal');
            
            // Make small modals fullscreen on mobile
            const dialog = modal.querySelector('.modal-dialog');
            if (dialog && !dialog.classList.contains('modal-lg') && !dialog.classList.contains('modal-xl')) {
                dialog.classList.add('modal-fullscreen-sm-down');
            }
        });
    }

    setupSwipeGestures() {
        if (!this.isTouch) return;

        document.addEventListener('touchstart', (e) => {
            this.touchStartX = e.touches[0].clientX;
            this.touchStartY = e.touches[0].clientY;
            this.isScrolling = false;
        });

        document.addEventListener('touchmove', (e) => {
            if (!this.touchStartX || !this.touchStartY) return;

            const touchX = e.touches[0].clientX;
            const touchY = e.touches[0].clientY;
            const diffX = this.touchStartX - touchX;
            const diffY = this.touchStartY - touchY;

            if (Math.abs(diffX) > Math.abs(diffY)) {
                // Horizontal swipe
                if (Math.abs(diffX) > 50) {
                    if (diffX > 0) {
                        // Swipe left - close sidebar
                        if (this.sidebar && this.sidebar.classList.contains('show')) {
                            this.closeSidebar();
                        }
                    } else {
                        // Swipe right - open sidebar
                        if (this.sidebar && !this.sidebar.classList.contains('show') && this.touchStartX < 50) {
                            this.toggleSidebar();
                        }
                    }
                }
            } else {
                // Vertical swipe - allow normal scrolling
                this.isScrolling = true;
            }
        });

        document.addEventListener('touchend', () => {
            this.touchStartX = 0;
            this.touchStartY = 0;
            this.isScrolling = false;
        });
    }

    setupMobileSearch() {
        const searchInputs = document.querySelectorAll('input[type="search"], .search-input');
        
        searchInputs.forEach(input => {
            // Prevent zoom on focus for iOS
            if (this.isMobile) {
                input.style.fontSize = '16px';
            }
            
            // Add mobile search enhancements
            input.addEventListener('focus', () => {
                if (this.isMobile) {
                    // Scroll to input
                    setTimeout(() => {
                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            });
        });
    }

    setupMobileNotifications() {
        // Enhanced mobile notifications
        const showMobileNotification = (message, type = 'info', duration = 3000) => {
            const notification = document.createElement('div');
            notification.className = `mobile-notification mobile-notification-${type}`;
            notification.innerHTML = `
                <div class="mobile-notification-content">
                    <span class="mobile-notification-message">${message}</span>
                    <button class="mobile-notification-close">&times;</button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => notification.classList.add('show'), 100);
            
            // Auto hide
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, duration);
            
            // Manual close
            notification.querySelector('.mobile-notification-close').addEventListener('click', () => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            });
        };
        
        // Make it globally available
        window.showMobileNotification = showMobileNotification;
    }

    setupMobileKeyboard() {
        if (!this.isMobile) return;

        // Handle virtual keyboard
        let initialViewportHeight = window.innerHeight;
        
        window.addEventListener('resize', () => {
            const currentHeight = window.innerHeight;
            const heightDifference = initialViewportHeight - currentHeight;
            
            if (heightDifference > 150) {
                // Keyboard is likely open
                document.body.classList.add('keyboard-open');
            } else {
                // Keyboard is likely closed
                document.body.classList.remove('keyboard-open');
            }
        });
    }

    handleOrientationChange() {
        // Update mobile detection
        this.isMobile = window.innerWidth <= 768;
        
        // Close sidebar on orientation change
        if (this.sidebar && this.sidebar.classList.contains('show')) {
            this.closeSidebar();
        }
        
        // Refresh table layouts
        this.setupMobileTables();
    }

    handleResize() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 768;
        
        if (wasMobile !== this.isMobile) {
            // Mobile state changed
            if (this.isMobile) {
                document.body.classList.add('mobile-device');
            } else {
                document.body.classList.remove('mobile-device');
                this.closeSidebar();
            }
        }
    }

    // Public methods for external use
    static showNotification(message, type = 'info', duration = 3000) {
        if (window.showMobileNotification) {
            window.showMobileNotification(message, type, duration);
        }
    }

    static toggleSidebar() {
        if (window.adminMobile) {
            window.adminMobile.toggleSidebar();
        }
    }
}

// CSS for mobile notifications
const mobileNotificationCSS = `
.mobile-notification {
    position: fixed;
    top: 20px;
    left: 20px;
    right: 20px;
    z-index: 9999;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transform: translateY(-100px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-notification.show {
    transform: translateY(0);
    opacity: 1;
}

.mobile-notification-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
}

.mobile-notification-message {
    flex: 1;
    font-size: 16px;
    font-weight: 500;
}

.mobile-notification-close {
    background: none;
    border: none;
    font-size: 20px;
    font-weight: bold;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    margin-left: 12px;
}

.mobile-notification-info {
    border-left: 4px solid #0d6efd;
}

.mobile-notification-success {
    border-left: 4px solid #198754;
}

.mobile-notification-warning {
    border-left: 4px solid #ffc107;
}

.mobile-notification-error {
    border-left: 4px solid #dc3545;
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
`;

// Inject CSS
const style = document.createElement('style');
style.textContent = mobileNotificationCSS;
document.head.appendChild(style);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.adminMobile = new AdminMobile();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminMobile;
}
