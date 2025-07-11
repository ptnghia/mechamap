/**
 * Manual Dropdown Toggle - Tối ưu cho mobile và desktop
 */
document.addEventListener('DOMContentLoaded', function() {
    // Tìm tất cả dropdown toggles
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    let activeDropdown = null;

    dropdownToggles.forEach(function(toggle) {
        // Gỡ bỏ event listener Bootstrap nếu có
        toggle.removeAttribute('data-bs-toggle');

        // Touch và click events
        const events = ['click', 'touchstart'];

        events.forEach(eventType => {
            toggle.addEventListener(eventType, function(e) {
                // Prevent default và stop propagation
                e.preventDefault();
                e.stopPropagation();

                // Tránh double trigger trên touch devices
                if (eventType === 'touchstart' && e.touches.length > 1) return;

                // Tìm dropdown menu tương ứng
                const menu = this.nextElementSibling;

                if (menu && menu.classList.contains('dropdown-menu')) {
                    const isCurrentlyOpen = menu.classList.contains('show');

                    // Đóng dropdown đang mở (nếu khác với dropdown hiện tại)
                    if (activeDropdown && activeDropdown !== menu) {
                        activeDropdown.classList.remove('show');
                        const activeToggle = activeDropdown.previousElementSibling;
                        if (activeToggle) {
                            activeToggle.setAttribute('aria-expanded', 'false');
                        }
                    }

                    // Toggle dropdown hiện tại
                    if (isCurrentlyOpen) {
                        menu.classList.remove('show');
                        this.setAttribute('aria-expanded', 'false');
                        activeDropdown = null;
                    } else {
                        menu.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                        activeDropdown = menu;

                        // Focus management cho accessibility
                        setTimeout(() => {
                            const firstItem = menu.querySelector('.dropdown-item');
                            if (firstItem && window.innerWidth <= 768) {
                                firstItem.focus();
                            }
                        }, 100);
                    }
                }
            }, { passive: false });
        });

        // Keyboard navigation
        toggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }

            if (e.key === 'Escape') {
                const menu = this.nextElementSibling;
                if (menu && menu.classList.contains('show')) {
                    menu.classList.remove('show');
                    this.setAttribute('aria-expanded', 'false');
                    this.focus();
                    activeDropdown = null;
                }
            }
        });
    });

    // Đóng dropdown khi click/touch bên ngoài
    document.addEventListener('click', closeDropdownsOnOutsideClick);
    document.addEventListener('touchstart', closeDropdownsOnOutsideClick);

    function closeDropdownsOnOutsideClick(e) {
        if (!e.target.closest('.dropdown') && activeDropdown) {
            activeDropdown.classList.remove('show');
            const toggle = activeDropdown.previousElementSibling;
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
            activeDropdown = null;
        }
    }

    // Đóng dropdown khi escape được nhấn
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && activeDropdown) {
            activeDropdown.classList.remove('show');
            const toggle = activeDropdown.previousElementSibling;
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
            }
            activeDropdown = null;
        }
    });

    // Handle orientation change trên mobile
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            if (activeDropdown) {
                activeDropdown.classList.remove('show');
                const toggle = activeDropdown.previousElementSibling;
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
                activeDropdown = null;
            }
        }, 100);
    });
});
