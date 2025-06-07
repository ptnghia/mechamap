<!-- Header with Banner and Navigation -->
<header class="site-header">
    <!-- Banner Image -->
    <div class="header-banner">
        <img src="{{ get_banner_url() }}" alt="Banner" class="banner-img">
    </div>

    <!-- Header Content with Navigation -->
    <div class="header-content">
        <div class="container header-container">
            <!-- Logo -->
            <div class="logo-container">
                <a href="{{ route('home') }}">
                    <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="img-fluid">
                </a>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <div class="input-group">
                    <input type="text" class="form-control" id="header-search" name="query"
                        placeholder="Search Community" aria-label="Search">
                    <button class="btn" type="button" id="header-search-btn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <!-- Search Results Dropdown -->
                <div class="search-results-dropdown" id="search-results-dropdown">
                    <div class="search-scope-options">
                        <div class="search-scope-option active" data-scope="site">in the entire site</div>
                        <div class="search-scope-option" data-scope="forum">in this sub-forum</div>
                        <div class="search-scope-option" data-scope="thread">in this thread</div>
                    </div>
                    <div class="search-results-content" id="search-results-content">
                        <!-- Results will be loaded here via AJAX -->
                    </div>
                    <div class="search-results-footer">
                        <a href="{{ route('search.advanced') }}" class="advanced-search-link">
                            <i class="bi bi-sliders"></i> Advanced Search
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navigation Actions -->
            <div class="nav-actions">
                <!-- New Link -->
                <a href="{{ route('whats-new') }}" class="nav-link" title="What's New">
                    <i class="bi bi-clock-history"></i>
                    <span class="nav-text">New</span>
                </a>

                <!-- Forums Link -->
                <a href="{{ route('forums.index') }}" class="nav-link" title="Forums">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span class="nav-text">Forums</span>
                </a>

                <!-- More Dropdown -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="true" title="More Options">
                        <i class="bi bi-three-dots"></i>
                        <span class="nav-text">More</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="moreDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('whats-new') }}">
                                <i class="bi bi-clock-history me-2"></i>What's New
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('forums.listing') }}">
                                <i class="bi bi-list me-2"></i>Forum Listing
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('showcase.public') }}">
                                <i class="bi bi-image me-2"></i>Showcase
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('gallery.index') }}">
                                <i class="bi bi-images me-2"></i>Gallery
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('search.advanced') }}">
                                <i class="bi bi-search me-2"></i>Advanced Search
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('members.index') }}">
                                <i class="bi bi-people me-2"></i>Members
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('faq.index') }}">
                                <i class="bi bi-question-circle me-2"></i>FAQ
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <div class="dropdown-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-moon me-2"></i>Dark Mode</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="darkModeSwitch"
                                        data-toggle-theme="dark" {{ request()->cookie('dark_mode') == 'dark' ? 'checked'
                                    : '' }}>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- User Menu or Login/Register -->
                @auth
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="true"
                        title="{{ Auth::user()->name }}">
                        <img src="{{ Auth::user()->getAvatarUrl() }}" alt="{{ Auth::user()->name }}"
                            class="rounded-circle me-1" width="24" height="24">
                        <span class="nav-text">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show', Auth::user()->username) }}">
                                <i class="bi bi-person me-2"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('following.index') }}">
                                <i class="bi bi-people me-2"></i> Following
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('alerts.index') }}">
                                <i class="bi bi-bell me-2"></i> Alerts
                                @php
                                $unreadAlertsCount = Auth::user()->alerts()->whereNull('read_at')->count();
                                @endphp
                                @if($unreadAlertsCount > 0)
                                <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadAlertsCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('conversations.index') }}">
                                <i class="bi bi-chat-dots me-2"></i> Conversations
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('bookmarks.index') }}">
                                <i class="bi bi-bookmark me-2"></i> Bookmarks
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('showcase.index') }}">
                                <i class="bi bi-image me-2"></i> My Showcase
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-gear me-2"></i> Account Settings
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('business.index') }}">
                                <i class="bi bi-briefcase me-2"></i> Grow Your Business
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('subscription.index') }}">
                                <i class="bi bi-star me-2"></i> Upgrade your account
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Log out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <div class="auth-buttons">
                    <a href="#" class="nav-link login-link" title="Login / Join">
                        <i class="bi bi-person-circle"></i>
                        <span class="nav-text">Login / Join</span>
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>
</header>

<!-- Include custom CSS for header -->
<link rel="stylesheet" href="{{ asset('css/custom-header.css') }}">
<link rel="stylesheet" href="{{ asset('css/mobile-nav.css') }}">

<!-- Manual Dropdown Script -->
<script src="{{ asset('js/manual-dropdown.js') }}"></script>