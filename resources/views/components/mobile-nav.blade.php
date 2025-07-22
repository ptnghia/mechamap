{{--
    MechaMap Mobile Navigation Component
    Using HC-MobileNav library for better mobile UX
    Only visible on mobile devices
--}}

<!-- Mobile Navigation Menu (Hidden by default, controlled by HC-MobileNav) -->
<nav id="mobile-nav" class="hc-mobile-nav" style="display: none;">
    <ul>
        <!-- Home -->
        <li>
            <a href="{{ url('/') }}">
                <i class="fa-solid fa-home me-2"></i>
                {{ t_common("home") }}
            </a>
        </li>

        <!-- Community/Forum -->
        <li>
            <a href="{{ route('forums.index') }}">
                <i class="fa-solid fa-users me-2"></i>
                {{ t_common("community") }}
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

        <!-- Showcase/Projects -->
        <li>
            <a href="{{ route('showcase.index') }}">
                <i class="fa-solid fa-trophy me-2"></i>
                {{ t_common("showcase") }}
            </a>
        </li>

        <!-- Marketplace -->
        <li>
            <a href="{{ route('marketplace.index') }}">
                <i class="fa-solid fa-store me-2"></i>
                {{ t_common("marketplace_title") }}
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

        <!-- Search & Discovery -->
        <li>
            <a href="/search">
                <i class="fa-solid fa-search me-2"></i>
                {{ t_common("search_discovery") }}
            </a>
            <ul>
                <li>
                    <a href="/search/advanced">
                        <i class="fa-brands fa-searchengin me-2"></i>
                        {{ t_common("advanced_search") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('gallery.index') }}">
                        <i class="fa-regular fa-images me-2"></i>
                        {{ t_common("photo_gallery") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('tags.index') }}">
                        <i class="fa-solid fa-tags me-2"></i>
                        {{ t_common("browse_by_tags") }}
                    </a>
                </li>
            </ul>
        </li>

        <!-- Help & Support -->
        <li>
            <a href="{{ route('help.index') }}">
                <i class="fa-solid fa-life-ring me-2"></i>
                {{ t_common("help_support") }}
            </a>
            <ul>
                <li>
                    <a href="{{ route('faq.index') }}">
                        <i class="fa-solid fa-question me-2"></i>
                        {{ t_common("faq") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('help.index') }}">
                        <i class="fa-solid fa-life-ring me-2"></i>
                        {{ t_common("help_center") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('contact') }}">
                        <i class="fa-solid fa-envelope me-2"></i>
                        {{ t_common("contact_support") }}
                    </a>
                </li>
            </ul>
        </li>

        <!-- About -->
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
                <li>
                    <a href="{{ route('terms.index') }}">
                        <i class="fa-solid fa-file-contract me-2"></i>
                        {{ t_common("terms_of_service") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('privacy.index') }}">
                        <i class="fa-solid fa-shield-halved me-2"></i>
                        {{ t_common("privacy_policy") }}
                    </a>
                </li>
            </ul>
        </li>

        @auth
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
                        {{ t_common("my_profile") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.edit') }}">
                        <i class="fa-solid fa-cog me-2"></i>
                        {{ t_common("account_settings") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('notifications.index') }}">
                        <i class="fa-solid fa-bell me-2"></i>
                        {{ t_common("notifications") }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-sign-out-alt me-2"></i>
                        {{ t_common("logout") }}
                    </a>
                </li>
            </ul>
        </li>
        @else
        <!-- Login/Register -->
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
