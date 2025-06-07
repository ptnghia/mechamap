/* Test Dropdown Functionality Script */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, testing dropdowns...');

    // Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap JavaScript chưa được load!');
        return;
    } else {
        console.log('✅ Bootstrap JavaScript đã được load');
    }

    // Find all dropdown toggles
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    console.log(`🔍 Tìm thấy ${dropdownToggles.length} dropdown toggles`);

    // Check each dropdown
    dropdownToggles.forEach((toggle, index) => {
        console.log(`📋 Dropdown ${index + 1}:`, {
            id: toggle.id,
            classes: toggle.classList.toString(),
            'data-bs-toggle': toggle.getAttribute('data-bs-toggle'),
            'aria-expanded': toggle.getAttribute('aria-expanded')
        });

        // Try to initialize Bootstrap dropdown
        try {
            const dropdown = new bootstrap.Dropdown(toggle);
            console.log(`✅ Dropdown ${index + 1} initialized successfully`);

            // Test dropdown menu exists
            const menu = toggle.nextElementSibling;
            if (menu && menu.classList.contains('dropdown-menu')) {
                console.log(`✅ Dropdown menu ${index + 1} found`);
            } else {
                console.warn(`⚠️ Dropdown menu ${index + 1} not found or invalid`);
            }
        } catch (error) {
            console.error(`❌ Error initializing dropdown ${index + 1}:`, error);
        }
    });

    // Add manual click test
    dropdownToggles.forEach((toggle, index) => {
        toggle.addEventListener('click', function(e) {
            console.log(`🖱️ Dropdown ${index + 1} clicked`);

            // Debug dropdown state
            const menu = this.nextElementSibling;
            if (menu) {
                console.log('Dropdown menu classes:', menu.classList.toString());
                console.log('Dropdown menu display:', window.getComputedStyle(menu).display);
                console.log('Dropdown menu visibility:', window.getComputedStyle(menu).visibility);
            }
        });
    });
});

// Add some CSS for debugging
const debugStyle = document.createElement('style');
debugStyle.textContent = `
    .dropdown-menu.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* Debug border for dropdown menus */
    .dropdown-menu {
        border: 2px solid red !important;
    }
`;
document.head.appendChild(debugStyle);

console.log('🔧 Dropdown debug script loaded');
