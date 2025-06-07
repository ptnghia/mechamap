/**
 * Kiểm tra dropdown toggle đơn giản - Thêm class 'show' khi click
 */
console.log('🚀 Simple Toggle Script Loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded');

    // Tìm tất cả dropdown toggles
    const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    console.log(`🔍 Found ${dropdownToggles.length} dropdown toggles`);

    dropdownToggles.forEach(function(toggle, index) {
        console.log(`📋 Setting up dropdown ${index + 1}:`, {
            id: toggle.id,
            classes: toggle.classList.toString()
        });

        // Tìm dropdown menu tương ứng
        const menu = toggle.nextElementSibling;

        if (menu && menu.classList.contains('dropdown-menu')) {
            console.log(`✅ Found dropdown menu for toggle ${index + 1}`);

            // Thêm event listener
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log(`🖱️ Dropdown ${index + 1} clicked`);
                console.log('📊 Before toggle:', {
                    toggleClasses: toggle.classList.toString(),
                    menuClasses: menu.classList.toString(),
                    menuHasShow: menu.classList.contains('show')
                });

                // Đóng tất cả dropdown khác
                document.querySelectorAll('.dropdown-menu.show').forEach(function(otherMenu) {
                    if (otherMenu !== menu) {
                        otherMenu.classList.remove('show');
                        console.log('🔒 Closed other dropdown');
                    }
                });

                document.querySelectorAll('[data-bs-toggle="dropdown"].show').forEach(function(otherToggle) {
                    if (otherToggle !== toggle) {
                        otherToggle.classList.remove('show');
                        otherToggle.setAttribute('aria-expanded', 'false');
                    }
                });

                // Toggle dropdown hiện tại
                if (menu.classList.contains('show')) {
                    // Đóng dropdown
                    menu.classList.remove('show');
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                    console.log('🔒 Dropdown closed');
                } else {
                    // Mở dropdown
                    menu.classList.add('show');
                    toggle.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');
                    console.log('🔓 Dropdown opened');
                }

                console.log('📊 After toggle:', {
                    toggleClasses: toggle.classList.toString(),
                    menuClasses: menu.classList.toString(),
                    menuHasShow: menu.classList.contains('show')
                });
            });

        } else {
            console.log(`❌ No dropdown menu found for toggle ${index + 1}`);
        }
    });

    // Click outside để đóng dropdown
    document.addEventListener('click', function(e) {
        // Kiểm tra nếu click không phải vào dropdown
        if (!e.target.closest('.dropdown')) {
            const openMenus = document.querySelectorAll('.dropdown-menu.show');
            const openToggles = document.querySelectorAll('[data-bs-toggle="dropdown"].show');

            if (openMenus.length > 0 || openToggles.length > 0) {
                console.log('🔒 Closing dropdowns due to outside click');

                openMenus.forEach(function(menu) {
                    menu.classList.remove('show');
                });

                openToggles.forEach(function(toggle) {
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        }
    });

    console.log('✅ Simple toggle script setup complete');
});
