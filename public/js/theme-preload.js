/**
 * Theme Pre-loader
 * Loads trước các cài đặt theme để tránh nhấp nháy khi tải trang
 */
(function() {
    'use strict';

    try {
        // Kiểm tra localStorage hoạt động không
        var testStorage = function() {
            var test = 'test';
            try {
                localStorage.setItem(test, test);
                localStorage.removeItem(test);
                return true;
            } catch (e) {
                return false;
            }
        };

        // Nếu localStorage khả dụng, load theme trước
        if (testStorage()) {
            var savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
            }
        }
    } catch (e) {
        console.warn('Theme pre-loader encountered an error:', e);
    }
})();
