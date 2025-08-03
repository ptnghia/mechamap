{{--
    MechaMap Mobile Navigation Component
    Using HC-MobileNav library for better mobile UX
    Only visible on mobile devices
    Synchronized with desktop menu structure
--}}

@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<!-- Mobile Navigation Menu (Hidden by default, controlled by HC-MobileNav) -->
<nav id="mobile-nav" class="hc-mobile-nav" style="display: none;">
    <ul>
        <!-- Home -->
        <li>
            <a href="{{ url('/') }}">
                <i class="fa-solid fa-home me-2"></i>
                {{ __('nav.home') }}
            </a>
        </li>

        <!-- Forums/Community -->
        <li>
            <a href="{{ route('forums.index') }}">
                <i class="fa-solid fa-comments me-2"></i>
                {{ __('nav.forums') }}
            </a>
            <ul>
                <!-- Quick Access -->
                <li>
                    <a href="{{ route('forums.index') }}">
                        <i class="fa-solid fa-home me-2"></i>
                        {{ __('forum.threads.title') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('whats-new.popular') }}">
                        <i class="fa-solid fa-star me-2"></i>
                        {{ t_common("popular_topics") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('forums.index') }}#categories">
                        <i class="fa-solid fa-folder-tree me-2"></i>
                        {{ __('ui.community.browse_categories') }}
                    </a>
                </li>

                <!-- Discover -->
                <li>
                    <a href="{{ route('whats-new') }}">
                        <i class="fa-solid fa-clock me-2"></i>
                        {{ t_common("recent_discussions") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('whats-new.trending') }}">
                        <i class="fa-solid fa-chart-line me-2"></i>
                        {{ t_common("trending") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('whats-new.most-viewed') }}">
                        <i class="fa-solid fa-eye me-2"></i>
                        {{ t_common("most_viewed") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('whats-new.hot-topics') }}">
                        <i class="fa-solid fa-flame me-2"></i>
                        {{ t_common("hot_topics") }}
                    </a>
                </li>

                <!-- Tools & Connect -->
                <li>
                    <a href="{{ route('forums.search.advanced') }}">
                        <i class="fa-solid fa-search-plus me-2"></i>
                        {{ __('ui.search.advanced_search') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('members.index') }}">
                        <i class="fa-solid fa-users-gear me-2"></i>
                        {{ t_common("member_directory") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('events.index') }}">
                        <i class="fa-solid fa-calendar-days me-2"></i>
                        {{ t_common("events_webinars") }}
                        <span class="badge badge-coming-soon">{{ t_common("coming_soon") }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jobs.index') }}">
                        <i class="fa-solid fa-briefcase me-2"></i>
                        {{ t_common("job_board") }}
                        <span class="badge badge-coming-soon">{{ t_common("coming_soon") }}</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Showcases -->
        <li>
            <a href="{{ route('showcase.index') }}">
                <i class="fa-solid fa-star me-2"></i>
                {{ __('nav.showcases') }}
            </a>
        </li>

        <!-- Marketplace -->
        <li>
            <a href="{{ route('marketplace.index') }}">
                <i class="fa-solid fa-store me-2"></i>
                {{ __('nav.marketplace') }}
            </a>
            <ul>
                <!-- Shop -->
                <li>
                    <a href="{{ route('marketplace.products.index') }}">
                        <i class="fa-solid fa-box me-2"></i>
                        {{ __('marketplace.products.all') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('marketplace.categories.index') }}">
                        <i class="fa-solid fa-grid-2 me-2"></i>
                        {{ __('marketplace.categories.title') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('marketplace.suppliers.index') }}">
                        <i class="fa-solid fa-building me-2"></i>
                        {{ __('marketplace.suppliers.title') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('companies.index') }}">
                        <i class="fa-solid fa-building-user me-2"></i>
                        {{ t_common("company_profiles") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('marketplace.index') }}#featured">
                        <i class="fa-solid fa-star me-2"></i>
                        {{ __('marketplace.products.featured') }}
                    </a>
                </li>

                <!-- Business Tools -->
                <li>
                    <a href="{{ route('marketplace.rfq.index') }}">
                        <i class="fa-solid fa-file-invoice me-2"></i>
                        {{ __('marketplace.rfq.title') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('marketplace.bulk-orders') }}">
                        <i class="fa-solid fa-boxes-stacked me-2"></i>
                        {{ __('marketplace.bulk_orders') }}
                    </a>
                </li>

                @auth
                <!-- My Account -->
                <li>
                    <a href="{{ route('marketplace.orders.index') }}">
                        <i class="fa-solid fa-list-check me-2"></i>
                        {{ __('marketplace.my_orders') }}
                    </a>
                </li>
                @if(auth()->user()->canBuyAnyProduct())
                <li>
                    <a href="{{ route('marketplace.cart.index') }}">
                        <i class="fa-solid fa-shopping-cart me-2"></i>
                        {{ __('marketplace.cart.title') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('marketplace.downloads.index') }}">
                        <i class="fa-solid fa-download me-2"></i>
                        {{ __('marketplace.downloads') }}
                    </a>
                </li>
                @endif
                @endauth
            </ul>
        </li>

        <!-- Docs (Member menu only) -->
        @auth
        @if(in_array(auth()->user()->role, ['senior_member', 'member', 'guest']))
        <li>
            <a href="{{ route('docs.index') }}">
                <i class="fa-solid fa-book me-2"></i>
                {{ __('nav.docs') }}
            </a>
        </li>
        @endif
        @endauth

        <!-- Quick Create/Add Content - Matching Desktop Menu -->
        @auth
        @if(auth()->user()->role !== 'guest')
        <li>
            <a href="#">
                <i class="fa-solid fa-plus me-2"></i>
                {{ __('nav.create.title') }}
            </a>
            <ul>
                <!-- Content Creation -->
                @if(Route::has('threads.create'))
                <li>
                    <a href="{{ route('threads.create') }}">
                        <i class="fa-solid fa-comment-dots me-2"></i>
                        {{ __('forum.threads.create_new') }}
                    </a>
                </li>
                @endif
                @if(Route::has('showcase.create'))
                <li>
                    <a href="{{ route('showcase.create') }}">
                        <i class="fa-solid fa-star me-2"></i>
                        {{ __('showcase.create.title') }}
                    </a>
                </li>
                @endif
                @if(Route::has('gallery.upload'))
                <li>
                    <a href="{{ route('gallery.upload') }}">
                        <i class="fa-solid fa-image me-2"></i>
                        {{ __('gallery.upload.title') }}
                    </a>
                </li>
                @endif

                <!-- Business Content -->
                @if(auth()->user()->canSellAnyProduct() && Route::has('marketplace.products.create'))
                <li>
                    <a href="{{ route('marketplace.products.create') }}">
                        <i class="fa-solid fa-box me-2"></i>
                        {{ __('marketplace.products.create') }}
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole(['supplier', 'manufacturer', 'verified_partner']) && Route::has('companies.create'))
                <li>
                    <a href="{{ route('companies.create') }}">
                        <i class="fa-solid fa-building me-2"></i>
                        {{ __('companies.create.title') }}
                    </a>
                </li>
                @endif

                @if(in_array(auth()->user()->role, ['verified_partner', 'manufacturer', 'supplier']) && Route::has('marketplace.rfq.create'))
                <li>
                    <a href="{{ route('marketplace.rfq.create') }}">
                        <i class="fa-solid fa-file-invoice me-2"></i>
                        {{ __('marketplace.rfq.create') }}
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif
        @endauth

        <!-- Search & Discovery -->
        <li>
            <a href="{{ route('search.index') }}">
                <i class="fa-solid fa-search me-2"></i>
                {{ t_common("search_discovery") }}
            </a>
            <ul>
                @if(Route::has('forums.search.advanced'))
                <li>
                    <a href="{{ route('forums.search.advanced') }}">
                        <i class="fa-brands fa-searchengin me-2"></i>
                        {{ t_common("advanced_search") }}
                    </a>
                </li>
                @endif
                @if(Route::has('gallery.index'))
                <li>
                    <a href="{{ route('gallery.index') }}">
                        <i class="fa-regular fa-images me-2"></i>
                        {{ t_common("photo_gallery") }}
                    </a>
                </li>
                @endif
                @if(Route::has('tags.index'))
                <li>
                    <a href="{{ route('tags.index') }}">
                        <i class="fa-solid fa-tags me-2"></i>
                        {{ t_common("browse_by_tags") }}
                    </a>
                </li>
                @endif
            </ul>
        </li>

        <!-- Help & Support -->
        @if(Route::has('help.index'))
        <li>
            <a href="{{ route('help.index') }}">
                <i class="fa-solid fa-life-ring me-2"></i>
                {{ t_common("help_support") }}
            </a>
            <ul>
                @if(Route::has('faq.index'))
                <li>
                    <a href="{{ route('faq.index') }}">
                        <i class="fa-solid fa-question me-2"></i>
                        {{ t_common("faq") }}
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('help.index') }}">
                        <i class="fa-solid fa-life-ring me-2"></i>
                        {{ t_common("help_center") }}
                    </a>
                </li>
                @if(Route::has('contact'))
                <li>
                    <a href="{{ route('contact') }}">
                        <i class="fa-solid fa-envelope me-2"></i>
                        {{ t_common("contact_support") }}
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- About -->
        @if(Route::has('about.index'))
        <li>
            <a href="{{ route('about.index') }}">
                <i class="fa-solid fa-info me-2"></i>
                {{ t_common("about_mechamap") }}
            </a>
            <ul>
                <li>
                    <a href="{{ route('about.index') }}">
                        <i class="fa-solid fa-building me-2"></i>
                        {{ t_common("about_us") }}
                    </a>
                </li>
                @if(Route::has('terms.index'))
                <li>
                    <a href="{{ route('terms.index') }}">
                        <i class="fa-solid fa-file-contract me-2"></i>
                        {{ t_common("terms_of_service") }}
                    </a>
                </li>
                @endif
                @if(Route::has('privacy.index'))
                <li>
                    <a href="{{ route('privacy.index') }}">
                        <i class="fa-solid fa-shield-halved me-2"></i>
                        {{ t_common("privacy_policy") }}
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Language Switcher -->
        <li>
            <a href="#">
                <i class="fa-solid fa-globe me-2"></i>
                {{ __('common.language.switch') }}
            </a>
            <ul>
                <li>
                    <a href="{{ route('language.switch', 'vi') }}" class="{{ app()->getLocale() == 'vi' ? 'active' : '' }}">
                        <i class="flag-icon flag-icon-vn me-2"></i>
                        Tiếng Việt
                    </a>
                </li>
                <li>
                    <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">
                        <i class="flag-icon flag-icon-us me-2"></i>
                        English
                    </a>
                </li>
            </ul>
        </li>

        @auth
        <!-- Shopping Cart - Only show if user can buy products -->
        @if(auth()->user()->canBuyAnyProduct() && Route::has('marketplace.cart.index'))
        <li>
            <a href="{{ route('marketplace.cart.index') }}">
                <i class="fa-solid fa-shopping-cart me-2"></i>
                {{ __('marketplace.cart.title') }}
                <span class="badge bg-primary ms-2" id="mobileCartBadge" style="display: none;">0</span>
            </a>
        </li>
        @endif

        <!-- Business Dashboard - For Business Users -->
        @if(in_array(auth()->user()->role, ['verified_partner', 'manufacturer', 'supplier', 'brand']))
        <li>
            <a href="#">
                <i class="fa-solid fa-briefcase me-2"></i>
                {{ __('nav.business.dashboard') }}
            </a>
            <ul>
                @switch(auth()->user()->role)
                    @case('verified_partner')
                        @if(Route::has('partner.dashboard'))
                        <li>
                            <a href="{{ route('partner.dashboard') }}">
                                <i class="fa-solid fa-tachometer-alt me-2"></i>
                                {{ __('nav.business.partner_dashboard') }}
                            </a>
                        </li>
                        @endif
                        @break
                    @case('manufacturer')
                        @if(Route::has('manufacturer.dashboard'))
                        <li>
                            <a href="{{ route('manufacturer.dashboard') }}">
                                <i class="fa-solid fa-industry me-2"></i>
                                {{ __('nav.business.manufacturer_dashboard') }}
                            </a>
                        </li>
                        @endif
                        @break
                    @case('supplier')
                        @if(Route::has('supplier.dashboard'))
                        <li>
                            <a href="{{ route('supplier.dashboard') }}">
                                <i class="fa-solid fa-truck me-2"></i>
                                {{ __('nav.business.supplier_dashboard') }}
                            </a>
                        </li>
                        @endif
                        @break
                    @case('brand')
                        @if(Route::has('brand.dashboard'))
                        <li>
                            <a href="{{ route('brand.dashboard') }}">
                                <i class="fa-solid fa-bullhorn me-2"></i>
                                {{ __('nav.business.brand_dashboard') }}
                            </a>
                        </li>
                        @endif
                        @break
                @endswitch

                <!-- Business Menu Items -->
                @if(auth()->user()->role !== 'brand')
                    @if(Route::has(auth()->user()->role . '.products.index'))
                    <li>
                        <a href="{{ route(auth()->user()->role . '.products.index') }}">
                            <i class="fa-solid fa-box me-2"></i>
                            {{ __('nav.business.my_products') }}
                        </a>
                    </li>
                    @endif
                    @if(Route::has(auth()->user()->role . '.orders.index'))
                    <li>
                        <a href="{{ route(auth()->user()->role . '.orders.index') }}">
                            <i class="fa-solid fa-shopping-cart me-2"></i>
                            {{ __('nav.business.orders') }}
                        </a>
                    </li>
                    @endif
                @endif

                @if(Route::has(auth()->user()->role . '.analytics.index'))
                <li>
                    <a href="{{ route(auth()->user()->role . '.analytics.index') }}">
                        <i class="fa-solid fa-chart-line me-2"></i>
                        {{ __('nav.business.analytics') }}
                    </a>
                </li>
                @endif

                @if(Route::has('business.verification.status'))
                <li>
                    <a href="{{ route('business.verification.status') }}">
                        <i class="fa-solid fa-{{ auth()->user()->business_verified ? 'check-circle text-success' : 'clock text-warning' }} me-2"></i>
                        {{ __('nav.business.verification_status') }}
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Admin Dashboard - For Admin/Moderator Users -->
        @if(in_array(auth()->user()->role, ['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator']))
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-shield-halved me-2"></i>
                {{ __('nav.admin.dashboard') }}
            </a>
            <ul>
                @if(Route::has('admin.dashboard'))
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-tachometer-alt me-2"></i>
                        {{ __('nav.admin.dashboard') }}
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasPermission('view-users') && Route::has('admin.users.index'))
                <li>
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fa-solid fa-users me-2"></i>
                        {{ __('nav.admin.users') }}
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasPermission('manage-content') && Route::has('admin.content.index'))
                <li>
                    <a href="{{ route('admin.content.index') }}">
                        <i class="fa-solid fa-file-alt me-2"></i>
                        {{ __('nav.admin.content') }}
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasPermission('manage-marketplace') && Route::has('admin.marketplace.index'))
                <li>
                    <a href="{{ route('admin.marketplace.index') }}">
                        <i class="fa-solid fa-store-alt me-2"></i>
                        {{ __('nav.admin.marketplace') }}
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- User Account -->
        <li>
            <a href="{{ route('profile.show', auth()->user()->username) }}">
                <i class="fa-solid fa-user me-2"></i>
                {{ t_common("my_account") }}
            </a>
            <ul>
                <li>
                    <a href="{{ route('profile.show', auth()->user()->username) }}">
                        <i class="fa-solid fa-user me-2"></i>
                        {{ __('nav.user.profile') }}
                    </a>
                </li>
                @if(Route::has('user.dashboard'))
                <li>
                    <a href="{{ route('user.dashboard') }}">
                        <i class="fa-solid fa-tachometer-alt me-2"></i>
                        {{ __('nav.user.dashboard') }}
                    </a>
                </li>
                @endif
                @if(Route::has('user.my-threads'))
                <li>
                    <a href="{{ route('user.my-threads') }}">
                        <i class="fa-solid fa-comments me-2"></i>
                        {{ __('nav.user.my_threads') }}
                        @if(auth()->user()->threads()->count() > 0)
                        <span class="badge bg-secondary ms-2">{{ auth()->user()->threads()->count() }}</span>
                        @endif
                    </a>
                </li>
                @endif
                @if(Route::has('user.bookmarks'))
                <li>
                    <a href="{{ route('user.bookmarks') }}">
                        <i class="fa-solid fa-bookmark me-2"></i>
                        {{ __('nav.user.bookmarks') }}
                        @if(auth()->user()->bookmarks()->count() > 0)
                        <span class="badge bg-secondary ms-2">{{ auth()->user()->bookmarks()->count() }}</span>
                        @endif
                    </a>
                </li>
                @endif
                @if(Route::has('user.following'))
                <li>
                    <a href="{{ route('user.following') }}">
                        <i class="fa-solid fa-heart me-2"></i>
                        {{ __('nav.user.following') }}
                        @if(auth()->user()->following()->count() > 0)
                        <span class="badge bg-secondary ms-2">{{ auth()->user()->following()->count() }}</span>
                        @endif
                    </a>
                </li>
                @endif
                @if(auth()->user()->role !== 'guest' && Route::has('user.ratings'))
                <li>
                    <a href="{{ route('user.ratings') }}">
                        <i class="fa-solid fa-star-half-alt me-2"></i>
                        {{ __('nav.user.ratings') }}
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('profile.edit') }}">
                        <i class="fa-solid fa-cog me-2"></i>
                        {{ __('nav.user.account_settings') }}
                    </a>
                </li>
                @if(Route::has('notifications.index'))
                <li>
                    <a href="{{ route('notifications.index') }}">
                        <i class="fa-solid fa-bell me-2"></i>
                        {{ t_common("notifications") }}
                        <span class="badge bg-danger ms-2" id="mobileNotificationBadge" style="display: none;">0</span>
                    </a>
                </li>
                @endif

                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-sign-out-alt me-2"></i>
                        {{ t_auth('logout.title') }}
                    </a>
                </li>
            </ul>
        </li>
        @else
        <!-- Login/Register for Guests -->
        <li>
            <a href="{{ route('login') }}">
                <i class="fa-solid fa-sign-in-alt me-2"></i>
                {{ t_common("login") }}
            </a>
        </li>
        <li>
            <a href="{{ route('register') }}">
                <i class="fa-solid fa-user-plus me-2"></i>
                {{ t_common("register") }}
            </a>
        </li>
        @endauth
    </ul>
</nav>

@auth
<!-- Hidden logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
@endauth

{{-- JavaScript to sync badges between desktop and mobile --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sync cart badge
    function syncCartBadge() {
        const desktopCartCount = document.getElementById('cartCount');
        const mobileCartBadge = document.getElementById('mobileCartBadge');

        if (desktopCartCount && mobileCartBadge) {
            const count = desktopCartCount.textContent.trim();
            if (count && count !== '0') {
                mobileCartBadge.textContent = count;
                mobileCartBadge.style.display = 'inline-block';
            } else {
                mobileCartBadge.style.display = 'none';
            }
        }
    }

    // Sync notification badge
    function syncNotificationBadge() {
        const desktopNotificationBadge = document.querySelector('#notificationDropdown .badge');
        const mobileNotificationBadge = document.getElementById('mobileNotificationBadge');

        if (desktopNotificationBadge && mobileNotificationBadge) {
            const count = desktopNotificationBadge.textContent.trim();
            if (count && count !== '0') {
                mobileNotificationBadge.textContent = count;
                mobileNotificationBadge.style.display = 'inline-block';
            } else {
                mobileNotificationBadge.style.display = 'none';
            }
        }
    }

    // Initial sync
    syncCartBadge();
    syncNotificationBadge();

    // Observe changes in desktop badges
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'characterData') {
                syncCartBadge();
                syncNotificationBadge();
            }
        });
    });

    // Start observing
    const desktopCartCount = document.getElementById('cartCount');
    const desktopNotificationBadge = document.querySelector('#notificationDropdown .badge');

    if (desktopCartCount) {
        observer.observe(desktopCartCount, { childList: true, characterData: true, subtree: true });
    }

    if (desktopNotificationBadge) {
        observer.observe(desktopNotificationBadge, { childList: true, characterData: true, subtree: true });
    }

    // Also sync when cart is updated via AJAX
    document.addEventListener('cartUpdated', function() {
        setTimeout(syncCartBadge, 100);
    });

    // Sync when notifications are updated
    document.addEventListener('notificationsUpdated', function() {
        setTimeout(syncNotificationBadge, 100);
    });
});
</script>
