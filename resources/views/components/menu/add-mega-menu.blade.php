{{-- Add Mega Menu Component - Optimized Version --}}
<div class="mega-menu-container">
    <div class="row g-0">
        {{-- Column 1: Tạo Nội Dung Mới --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-plus-circle me-2 text-primary"></i>
                    {{ t_navigation('add_menu.create_content.title') }}
                </h6>
                <ul class="mega-menu-list">
                    <li>
                        <a href="{{ route('threads.create') }}" class="mega-menu-item">
                            <i class="fa-solid fa-comments me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.create_content.new_thread') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.create_content.new_thread_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showcase.create') }}" class="mega-menu-item">
                            <i class="fa-solid fa-trophy me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.create_content.new_showcase') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.create_content.new_showcase_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('gallery.create') }}" class="mega-menu-item">
                            <i class="fa-solid fa-camera me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.create_content.upload_photo') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.create_content.upload_photo_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.seller.setup') }}" class="mega-menu-item">
                            <i class="fa-solid fa-store me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.create_content.become_seller') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.create_content.become_seller_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <!--
                    <li>
                        <a href="{{ route('events.create') }}" class="mega-menu-item">
                            <i class="fa-solid fa-calendar-plus me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.create_content.create_event') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.create_content.create_event_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('jobs.create') }}" class="mega-menu-item">
                            <i class="fa-solid fa-briefcase-plus me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.create_content.post_job') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.create_content.post_job_desc') }}</small>
                            </div>
                            <span class="badge bg-info">{{ t_navigation('add_menu.status.beta') }}</span>
                        </a>
                    </li> -->
                </ul>
            </div>
        </div>

        {{-- Column 2: Khám Phá & Kết Nối --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-compass me-2 text-info"></i>
                    {{ t_navigation('add_menu.discovery.title') }}
                </h6>
                <ul class="mega-menu-list">
                    <li>
                        <a href="{{ route('threads.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-search-plus me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.discovery.advanced_search') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.discovery.advanced_search_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('members.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-user-tie me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.community.find_experts') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.community.find_experts_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('companies.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-building me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.discovery.company_directory') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.discovery.company_directory_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <!--
                    <li>
                        <a href="{{ route('events.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-calendar-days me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.community.events') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.community.events_desc') }}</small>
                            </div>
                            <span class="badge bg-info">{{ t_navigation('add_menu.status.beta') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('jobs.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-briefcase me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.community.job_opportunities') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.community.job_opportunities_desc') }}</small>
                            </div>
                            <span class="badge bg-info">{{ t_navigation('add_menu.status.beta') }}</span>
                        </a>
                    </li> -->
                    <li>
                        <a href="{{ route('whats-new.popular') }}" class="mega-menu-item">
                            <i class="fa-solid fa-trending-up me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.discovery.tech_trends') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.discovery.tech_trends_desc') }}</small>
                            </div>
                            <span class="badge bg-success">{{ t_navigation('add_menu.status.ready') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Column 3: Công Cụ & Hỗ Trợ --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-tools me-2 text-warning"></i>
                    {{ t_navigation('add_menu.tools_support.title') }}
                </h6>
                <ul class="mega-menu-list">
                    <!--
                    <li>
                        <a href="{{ route('tools.materials') }}" class="mega-menu-item">
                            <i class="fa-solid fa-table me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.tools.material_lookup') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.tools.material_lookup_desc') }}</small>
                            </div>
                            <span class="badge bg-success">{{ t_navigation('add_menu.status.ready') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tools.standards') }}" class="mega-menu-item">
                            <i class="fa-solid fa-certificate me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.tools.standards_lookup') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.tools.standards_lookup_desc') }}</small>
                            </div>
                            <span class="badge bg-success">{{ t_navigation('add_menu.status.ready') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tools.cad-library') }}" class="mega-menu-item">
                            <i class="fa-solid fa-cube me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.tools.cad_library') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.tools.cad_library_desc') }}</small>
                            </div>
                            <span class="badge bg-success">{{ t_navigation('add_menu.status.ready') }}</span>
                        </a>
                    </li> -->
                    <li>
                        <a href="{{ route('contact') }}" class="mega-menu-item">
                            <i class="fa-solid fa-headset me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.support.contact') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.support.contact_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.support.about') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.support.about_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-file-contract me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.support.terms') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.support.terms_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-shield-alt me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.support.privacy') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.support.privacy_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rules') }}" class="mega-menu-item">
                            <i class="fa-solid fa-gavel me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.support.rules') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.support.rules_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-question me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ t_navigation('add_menu.support.faq') }}</span>
                                <small class="mega-menu-item-desc">{{ t_navigation('add_menu.support.faq_desc') }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    {{-- Bottom Action Bar --}}
    <!--div class="mega-menu-footer">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-4 text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="me-3">
                        <i class="fa-solid fa-moon me-1"></i>
                        <span class="small text-muted">{{ t_navigation('add_menu.footer.dark_mode') }}</span>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="darkModeToggle">
                        <label class="form-check-label" for="darkModeToggle"></label>
                    </div>
                </div>
            </div>
        </div>
    </div-->
</div>

{{-- JavaScript for functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dark mode toggle
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        // Check current theme
        const currentTheme = localStorage.getItem('theme') || 'light';
        darkModeToggle.checked = currentTheme === 'dark';

        darkModeToggle.addEventListener('change', function() {
            const theme = this.checked ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
            document.documentElement.setAttribute('data-theme', theme);
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            window.location.href = '{{ route("threads.create") }}';
        }
    });

    // Initialize badge animations
    initializeBadgeAnimations();
});

function initializeBadgeAnimations() {
    // Add subtle animations to status badges
    const badges = document.querySelectorAll('.mega-menu-item .badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
}
</script>
