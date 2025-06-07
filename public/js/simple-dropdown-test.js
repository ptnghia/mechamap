/**
 * Simple Dropdown Test - Kiểm tra dropdown trong navigation
 */
(function() {
    console.log('🔍 Starting dropdown test...');

    // Kiểm tra sau khi DOM load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            console.log('🧪 Testing dropdown functionality...');

            // Tìm dropdown trong navigation
            const moreDropdown = document.getElementById('moreDropdown');
            const userDropdown = document.getElementById('userDropdown');

            if (moreDropdown) {
                console.log('✅ More dropdown found');
                testDropdown(moreDropdown, 'More');
            } else {
                console.log('❌ More dropdown not found');
            }

            if (userDropdown) {
                console.log('✅ User dropdown found');
                testDropdown(userDropdown, 'User');
            } else {
                console.log('❌ User dropdown not found');
            }
        }, 1000);
    });

    function testDropdown(toggle, name) {
        const menu = toggle.nextElementSibling;

        if (!menu || !menu.classList.contains('dropdown-menu')) {
            console.log(`❌ ${name} dropdown menu not found`);
            return;
        }

        console.log(`🔧 Testing ${name} dropdown...`);

        // Test click
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log(`🖱️ ${name} dropdown clicked`);

            // Log current state
            console.log(`Current state - Toggle classes: ${toggle.classList.toString()}`);
            console.log(`Current state - Menu classes: ${menu.classList.toString()}`);

            // Manual toggle
            if (menu.classList.contains('show')) {
                menu.classList.remove('show');
                toggle.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
                console.log(`🔒 ${name} dropdown closed manually`);
                console.log(`After close - Toggle classes: ${toggle.classList.toString()}`);
                console.log(`After close - Menu classes: ${menu.classList.toString()}`);
            } else {
                // Close other dropdowns first
                document.querySelectorAll('.dropdown-menu.show').forEach(function(otherMenu) {
                    otherMenu.classList.remove('show');
                    const otherToggle = otherMenu.previousElementSibling;
                    if (otherToggle) {
                        otherToggle.classList.remove('show');
                        otherToggle.setAttribute('aria-expanded', 'false');
                    }
                });

                menu.classList.add('show');
                toggle.classList.add('show');
                toggle.setAttribute('aria-expanded', 'true');
                console.log(`🔓 ${name} dropdown opened manually`);
                console.log(`After open - Toggle classes: ${toggle.classList.toString()}`);
                console.log(`After open - Menu classes: ${menu.classList.toString()}`);
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                if (menu.classList.contains('show')) {
                    menu.classList.remove('show');
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                    console.log(`🔒 ${name} dropdown closed by outside click`);
                }
            }
        });

        console.log(`✅ ${name} dropdown test setup complete`);
    }
})();
