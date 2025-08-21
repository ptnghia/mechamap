{{--
    MechaMap Unified Footer Component - FIXED VERSION
    Footer thống nhất cho tất cả trang frontend user
--}}
<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-auto">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="small mb-0">
                    © {{ date('Y') }} {{ get_site_name() }}. {{ t_footer('copyright.all_rights_reserved') }}
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end align-items-center">
                    <!-- Social Media Links -->
                    @php
                    $socialLinks = get_social_links();
                    @endphp

                    @if(!empty($socialLinks['facebook'] ?? ''))
                    <a href="{{ $socialLinks['facebook'] ?? '' }}" target="_blank"
                        class="btn btn-sm btn-outline-light rounded-circle p-2 me-2"
                        data-bs-toggle="tooltip" title="{{ t_footer('social.facebook') }}">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    @endif

                    @if(!empty($socialLinks['twitter'] ?? ''))
                    <a href="{{ $socialLinks['twitter'] ?? '' }}" target="_blank"
                        class="btn btn-sm btn-outline-light rounded-circle p-2 me-2"
                        data-bs-toggle="tooltip" title="{{ t_footer('social.twitter') }}">
                        <i class="fab fa-twitter"></i>
                    </a>
                    @endif

                    @if(!empty($socialLinks['instagram'] ?? ''))
                    <a href="{{ $socialLinks['instagram'] ?? '' }}" target="_blank"
                        class="btn btn-sm btn-outline-light rounded-circle p-2 me-2"
                        data-bs-toggle="tooltip" title="{{ t_footer('social.instagram') }}">
                        <i class="fab fa-instagram"></i>
                    </a>
                    @endif

                    @if(!empty($socialLinks['linkedin'] ?? ''))
                    <a href="{{ $socialLinks['linkedin'] ?? '' }}" target="_blank"
                        class="btn btn-sm btn-outline-light rounded-circle p-2 me-2"
                        data-bs-toggle="tooltip" title="{{ t_footer('social.linkedin') }}">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    @endif

                    @if(!empty($socialLinks['youtube'] ?? ''))
                    <a href="{{ $socialLinks['youtube'] ?? '' }}" target="_blank"
                        class="btn btn-sm btn-outline-light rounded-circle p-2 me-2"
                        data-bs-toggle="tooltip" title="{{ t_footer('social.youtube') }}">
                        <i class="fab fa-youtube"></i>
                    </a>
                    @endif

                    <!-- Dark Mode Toggle -->
                    <button type="button"
                            class="btn btn-sm btn-outline-light rounded-circle p-2 ms-2"
                            id="darkModeToggleFixed"
                            data-bs-toggle="tooltip"
                            title="{{ t_footer('tools.toggle_theme') }}">
                        <img src="{{ asset('images/moon.svg') }}" alt="{{ t_footer('tools.dark_mode') }}" width="16" height="16" id="darkModeIconFixed">
                        <span class="visually-hidden">{{ t_footer('accessibility.toggle_navigation') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
// Dark mode toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggleFixed');
    const darkModeIcon = document.getElementById('darkModeIconFixed');

    if (darkModeToggle && darkModeIcon) {
        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';

        // Apply the current theme
        if (currentTheme === 'dark') {
            document.body.classList.add('dark-mode');
            darkModeIcon.src = '{{ asset("images/sun.svg") }}';
            darkModeIcon.alt = '{{ t_footer("tools.light_mode") }}';
        }

        // Toggle theme on button click
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');

            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                darkModeIcon.src = '{{ asset("images/sun.svg") }}';
                darkModeIcon.alt = '{{ t_footer("tools.light_mode") }}';
            } else {
                localStorage.setItem('theme', 'light');
                darkModeIcon.src = '{{ asset("images/moon.svg") }}';
                darkModeIcon.alt = '{{ t_footer("tools.dark_mode") }}';
            }
        });
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
