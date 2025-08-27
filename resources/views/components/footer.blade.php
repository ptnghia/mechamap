{{--
    MechaMap Unified Footer Component - FIXED VERSION
    Footer thống nhất cho tất cả trang frontend user
--}}
<!-- Footer -->
<footer class=" bg-primary text-white py-2 mt-auto">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="small mb-0">
                    © {{ date('Y') }} {{ get_site_name() }}. {{ t_footer('copyright.all_rights_reserved') }}
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end align-items-center mxh-f">
                    <!-- Social Media Links -->
                    @php
                    $socialLinks = get_social_links();
                    @endphp

                    @if(!empty($socialLinks['facebook'] ?? ''))
                    <a href="{{ $socialLinks['facebook'] ?? '' }}" target="_blank"  class="btn btn-primary" data-bs-toggle="tooltip" title="{{ t_footer('social.facebook') }}">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    @endif

                    @if(!empty($socialLinks['twitter'] ?? ''))
                    <a href="{{ $socialLinks['twitter'] ?? '' }}" target="_blank" class="btn btn-primary" data-bs-toggle="tooltip" title="{{ t_footer('social.twitter') }}">
                        <i class="fab fa-twitter"></i>
                    </a>
                    @endif

                    @if(!empty($socialLinks['instagram'] ?? ''))
                    <a href="{{ $socialLinks['instagram'] ?? '' }}" target="_blank" class="btn btn-primary" data-bs-toggle="tooltip" title="{{ t_footer('social.instagram') }}">
                        <i class="fab fa-instagram"></i>
                    </a>
                    @endif

                    @if(!empty($socialLinks['linkedin'] ?? ''))
                    <a href="{{ $socialLinks['linkedin'] ?? '' }}" target="_blank" class="btn btn-primary" data-bs-toggle="tooltip" title="{{ t_footer('social.linkedin') }}">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    @endif

                    @if(!empty($socialLinks['youtube'] ?? ''))
                    <a href="{{ $socialLinks['youtube'] ?? '' }}" target="_blank" class="btn btn-primary" data-bs-toggle="tooltip" title="{{ t_footer('social.youtube') }}">
                        <i class="fab fa-youtube"></i>
                    </a>
                    @endif

                    <!-- Dark Mode Toggle -->
                    <button type="button" class="btn darkModeToggle btn-primary" id="darkModeToggle" data-bs-toggle="tooltip" title="{{ t_footer('tools.toggle_theme') }}">
                        <img src="{{ asset('images/moon.svg') }}" alt="{{ t_footer('tools.dark_mode') }}" width="16" height="16" id="darkModeIcon">
                        <span class="visually-hidden">{{ t_footer('accessibility.toggle_navigation') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
(function() {
    'use strict';

    const DEBUG = false;
    function log(msg) { if (DEBUG) console.log('[Theme] ' + msg); }

    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon   = document.getElementById('darkModeIcon');
    const darkModeCSS = document.getElementById('darkModeCSS');

    // --- Khởi tạo theme khi tải trang ---
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
        setupButton();
        setupKeyboardShortcut();

        // Bootstrap tooltip
        if (window.bootstrap) {
            new bootstrap.Tooltip(darkModeToggle);
        }
    });

    // --- Lấy theme hiện tại ---
    function getCurrentTheme() {
        return document.documentElement.getAttribute('data-bs-theme') || 'light';
    }

    // --- Áp dụng theme ---
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);

        if (theme === 'dark') {
            if (darkModeCSS) darkModeCSS.disabled = false; // bật dark.css
            document.body.classList.add('dark');
            if (darkModeIcon) {
                darkModeIcon.src = "{{ asset('images/sun.svg') }}";
                darkModeIcon.alt = "{{ t_footer('tools.light_mode') }}";
            }
        } else {
            if (darkModeCSS) darkModeCSS.disabled = false; // bật dark.css
            document.body.classList.remove('dark');
            if (darkModeIcon) {
                darkModeIcon.src = "{{ asset('images/moon.svg') }}";
                darkModeIcon.alt = "{{ t_footer('tools.dark_mode') }}";
            }
        }

        localStorage.setItem('theme', theme);
        document.cookie = "dark_mode=" + theme + "; path=/; max-age=31536000"; // 1 năm
    }

    // --- Khởi tạo từ localStorage ---
    function initializeTheme() {
        let savedTheme = 'light';
        try {
            savedTheme = localStorage.getItem('theme') || 'light';
        } catch(e) {
            log('localStorage not available');
        }
        log('Init theme: ' + savedTheme);
        applyTheme(savedTheme);
    }

    // --- Setup nút bấm ---
    function setupButton() {
        if (!darkModeToggle) return;

        darkModeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const currentTheme = getCurrentTheme();
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);

            // Dispatch sự kiện custom để các script khác có thể nghe
            document.dispatchEvent(new CustomEvent('themeToggled', {
                detail: { theme: newTheme }
            }));
        });
    }

    // --- Setup phím tắt Ctrl+Shift+D ---
    function setupKeyboardShortcut() {
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'd') {
                e.preventDefault();
                const currentTheme = getCurrentTheme();
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                applyTheme(newTheme);

                // Feedback nhỏ
                const feedback = document.createElement('div');
                feedback.textContent = (newTheme === 'dark') ? 'Đã bật chế độ tối' : 'Đã bật chế độ sáng';
                feedback.style.cssText = `
                    position:fixed;top:10px;left:50%;transform:translateX(-50%);
                    background:rgba(0,0,0,0.7);color:#fff;padding:6px 12px;
                    border-radius:4px;z-index:9999;font-size:14px;
                `;
                document.body.appendChild(feedback);
                setTimeout(() => {
                    feedback.style.opacity = '0';
                    feedback.style.transition = 'opacity 0.5s';
                    setTimeout(() => feedback.remove(), 500);
                }, 1200);
            }
        });
    }

})();

</script>
