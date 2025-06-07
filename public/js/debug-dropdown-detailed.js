/**
 * Debug Dropdown - Kiểm tra class 'show' được thêm vào đúng cách
 */
console.log('🔍 Debug dropdown script starting...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 DOM loaded, checking dropdown elements...');

    // Tìm tất cả dropdown elements
    const dropdowns = document.querySelectorAll('.dropdown');
    const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"], .dropdown-toggle');
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');

    console.log(`Found ${dropdowns.length} dropdown containers`);
    console.log(`Found ${dropdownToggles.length} dropdown toggles`);
    console.log(`Found ${dropdownMenus.length} dropdown menus`);

    // Log chi tiết từng dropdown
    dropdowns.forEach((dropdown, index) => {
        const toggle = dropdown.querySelector('.dropdown-toggle, [data-bs-toggle="dropdown"]');
        const menu = dropdown.querySelector('.dropdown-menu');

        console.log(`📋 Dropdown ${index + 1}:`, {
            id: toggle?.id || 'no-id',
            toggleClasses: toggle?.classList.toString() || 'no-toggle',
            menuClasses: menu?.classList.toString() || 'no-menu',
            hasShow: menu?.classList.contains('show') || false
        });

        if (toggle) {
            // Thêm event listener với debug
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log(`🖱️ Dropdown ${index + 1} clicked - ID: ${this.id}`);
                console.log('📊 Before click:', {
                    toggleClasses: this.classList.toString(),
                    menuClasses: menu?.classList.toString(),
                    menuVisible: menu?.classList.contains('show')
                });

                if (menu) {
                    // Đóng tất cả dropdown khác
                    document.querySelectorAll('.dropdown-menu.show').forEach(otherMenu => {
                        if (otherMenu !== menu) {
                            otherMenu.classList.remove('show');
                            console.log('🔒 Closed other dropdown');
                        }
                    });

                    document.querySelectorAll('.dropdown-toggle.show, [data-bs-toggle="dropdown"].show').forEach(otherToggle => {
                        if (otherToggle !== this) {
                            otherToggle.classList.remove('show');
                            otherToggle.setAttribute('aria-expanded', 'false');
                        }
                    });

                    // Toggle current dropdown
                    const isOpen = menu.classList.contains('show');

                    if (isOpen) {
                        // Đóng
                        menu.classList.remove('show');
                        this.classList.remove('show');
                        this.setAttribute('aria-expanded', 'false');
                        console.log('🔒 Closed current dropdown');
                    } else {
                        // Mở
                        menu.classList.add('show');
                        this.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                        console.log('🔓 Opened current dropdown');
                    }

                    console.log('📊 After click:', {
                        toggleClasses: this.classList.toString(),
                        menuClasses: menu.classList.toString(),
                        menuVisible: menu.classList.contains('show')
                    });

                    // Kiểm tra CSS computed styles
                    const menuStyles = window.getComputedStyle(menu);
                    console.log('🎨 Menu computed styles:', {
                        display: menuStyles.display,
                        visibility: menuStyles.visibility,
                        opacity: menuStyles.opacity,
                        position: menuStyles.position,
                        zIndex: menuStyles.zIndex
                    });
                }
            });
        }
    });

    // Observer để theo dõi class changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const target = mutation.target;
                if (target.classList.contains('dropdown-menu') || target.classList.contains('dropdown-toggle')) {
                    console.log('🔄 Class changed on element:', {
                        element: target.tagName,
                        id: target.id,
                        classes: target.classList.toString(),
                        hasShow: target.classList.contains('show')
                    });
                }
            }
        });
    });

    // Observe all dropdown elements
    document.querySelectorAll('.dropdown-menu, .dropdown-toggle, [data-bs-toggle="dropdown"]').forEach(element => {
        observer.observe(element, { attributes: true, attributeFilter: ['class'] });
    });

    // Outside click handler
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            const openMenus = document.querySelectorAll('.dropdown-menu.show');
            const openToggles = document.querySelectorAll('.dropdown-toggle.show, [data-bs-toggle="dropdown"].show');

            if (openMenus.length > 0 || openToggles.length > 0) {
                console.log('🔒 Closing dropdowns due to outside click');

                openMenus.forEach(menu => menu.classList.remove('show'));
                openToggles.forEach(toggle => {
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        }
    });

    console.log('✅ Debug dropdown script setup complete');

    // Test function để trigger dropdown programmatically
    window.testDropdown = function(dropdownId) {
        const toggle = document.getElementById(dropdownId);
        if (toggle) {
            console.log(`🧪 Testing dropdown: ${dropdownId}`);
            toggle.click();
        } else {
            console.error(`❌ Dropdown not found: ${dropdownId}`);
        }
    };

    console.log('💡 Use testDropdown("moreDropdown") or testDropdown("userDropdown") to test programmatically');
});
