{{-- Add Mega Menu Component --}}
<div class="mega-menu add-mega-menu">
    <div class="container-fluid">
        <div class="row">
            {{-- Column 1: Tạo Nội Dung Mới --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="mega-menu-section">
                    <h6 class="mega-menu-header">
                        <i class="fa-solid fa-plus-circle me-2 text-primary"></i>
                        {{ __('add_menu.create_content.title') }}
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('threads.create') }}" class="mega-menu-item">
                                <i class="fa-solid fa-comments me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.create_content.new_thread') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.create_content.new_thread_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('showcase.create') }}" class="mega-menu-item">
                                <i class="fa-solid fa-trophy me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.create_content.new_showcase') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.create_content.new_showcase_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'gallery']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-camera me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.create_content.upload_photo') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.create_content.upload_photo_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'marketplace_products']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-box me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.create_content.add_product') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.create_content.add_product_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'seller_setup']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-store me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.create_content.become_seller') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.create_content.become_seller_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'documents']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-file-alt me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.create_content.create_document') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.create_content.create_document_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 2: Tìm Kiếm & Khám Phá --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="mega-menu-section">
                    <h6 class="mega-menu-header">
                        <i class="fa-solid fa-search me-2 text-info"></i>
                        {{ __('add_menu.discovery.title') }}
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('forums.search.advanced') }}" class="mega-menu-item">
                                <i class="fa-solid fa-search-plus me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.discovery.advanced_search') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.discovery.advanced_search_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('forums.index') }}#tags" class="mega-menu-item">
                                <i class="fa-solid fa-tags me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.discovery.browse_tags') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.discovery.browse_tags_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('members.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-chart-bar me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.discovery.community_stats') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.discovery.community_stats_desc') }}</small>
                                </div>
                                <span class="activity-indicator" id="totalMembersCount">{{ number_format(96) }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('whats-new.popular') }}" class="mega-menu-item">
                                <i class="fa-solid fa-trending-up me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.discovery.tech_trends') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.discovery.tech_trends_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('whats-new') }}" class="mega-menu-item">
                                <i class="fa-solid fa-bullseye me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.discovery.recommendations') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.discovery.recommendations_desc') }}</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 3: Công Cụ & Tiện Ích --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="mega-menu-section">
                    <h6 class="mega-menu-header">
                        <i class="fa-solid fa-tools me-2 text-warning"></i>
                        {{ __('add_menu.tools.title') }}
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'calculator']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-calculator me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.tools.calculator') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.tools.calculator_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'unit_converter']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-exchange-alt me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.tools.unit_converter') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.tools.unit_converter_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'material_lookup']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-table me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.tools.material_lookup') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.tools.material_lookup_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'design_tools']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-drafting-compass me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.tools.design_tools') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.tools.design_tools_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'mobile_app']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-mobile-alt me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.tools.mobile_app') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.tools.mobile_app_desc') }}</small>
                                </div>
                                <span class="badge bg-info">{{ __('add_menu.status.beta') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'api_integration']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-code me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.tools.api_integration') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.tools.api_integration_desc') }}</small>
                                </div>
                                <span class="badge bg-success">{{ __('add_menu.status.new') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Column 4: Cộng Đồng & Hỗ Trợ --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="mega-menu-section">
                    <h6 class="mega-menu-header">
                        <i class="fa-solid fa-users me-2 text-success"></i>
                        {{ __('add_menu.community.title') }}
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('members.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-user-tie me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.community.find_experts') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.community.find_experts_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'business_connect']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-building me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.community.business_connect') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.community.business_connect_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'mentorship']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-graduation-cap me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.community.mentorship') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.community.mentorship_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'jobs']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-briefcase me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.community.job_opportunities') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.community.job_opportunities_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('forums.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-users-cog me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.community.professional_groups') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.community.professional_groups_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'events']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-calendar-alt me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.community.events') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.community.events_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Support Section --}}
                <div class="mega-menu-section mt-4">
                    <h6 class="mega-menu-header">
                        <i class="fa-solid fa-question-circle me-2 text-secondary"></i>
                        {{ __('add_menu.support.title') }}
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'faq']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-question me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.support.faq') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.support.faq_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'contact']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-headset me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.support.contact') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.support.contact_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coming-soon', ['feature' => 'about']) }}" class="mega-menu-item">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('add_menu.support.about') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('add_menu.support.about_desc') }}</small>
                                </div>
                                <span class="badge bg-warning text-dark">{{ __('add_menu.status.coming_soon') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Bottom Action Bar --}}
        <div class="mega-menu-footer">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="me-4">
                            <i class="fa-solid fa-lightbulb text-warning me-2"></i>
                            <span class="small text-muted">{{ __('add_menu.footer.quick_tip') }}</span>
                        </div>
                        <div class="me-4">
                            <i class="fa-solid fa-keyboard text-info me-2"></i>
                            <span class="small text-muted">{{ __('add_menu.footer.keyboard_shortcut') }}: <kbd>Ctrl + N</kbd></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-3">
                            <i class="fa-solid fa-moon me-1"></i>
                            <span class="small text-muted">{{ __('add_menu.footer.dark_mode') }}</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="darkModeToggle">
                            <label class="form-check-label" for="darkModeToggle"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    // Load community stats
    loadCommunityStats();
});

function loadCommunityStats() {
    // This would typically fetch from an API
    // For now, we'll use static data
    const stats = {
        totalMembers: 96,
        activeToday: 23,
        newThisWeek: 12
    };

    const membersCountEl = document.getElementById('totalMembersCount');
    if (membersCountEl) {
        membersCountEl.textContent = stats.totalMembers;
    }
}
</script>
