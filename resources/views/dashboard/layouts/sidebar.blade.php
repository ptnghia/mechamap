<div class="dashboard-sidebar">
    <!-- Logo -->
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-logo">
            <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="logo-img">
            <span class="logo-text">MechaMap</span>
        </a>
        <button class="sidebar-toggle d-lg-none">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- User Info -->
    <div class="sidebar-user">
        <div class="user-avatar">
            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=007bff&color=fff' }}"
                 alt="{{ Auth::user()->name }}" class="avatar-img">
        </div>
        <div class="user-info">
            <h6 class="user-name">{{ Auth::user()->name }}</h6>
            <span class="user-role">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <!-- Common Section -->
        <div class="nav-section">
            <h6 class="nav-section-title">{{ __('sidebar.user_dashboard.dashboard') }}</h6>
            <ul class="nav-menu">
                @foreach($menuItems['common'] as $item)
                    <li class="nav-item">
                        <a href="{{ route($item['route']) }}"
                           class="nav-link {{ $item['active'] ? 'active' : '' }}">
                            <i class="{{ $item['icon'] }}"></i>
                            <span class="nav-text">{{ $item['name'] }}</span>
                            @if(isset($item['badge']) && $item['badge'] > 0)
                                <span class="badge bg-danger ms-auto">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Community Section -->
        <div class="nav-section">
            <h6 class="nav-section-title">Cộng đồng</h6>
            <ul class="nav-menu">
                @foreach($menuItems['community'] as $item)
                    <li class="nav-item">
                        <a href="{{ route($item['route']) }}"
                           class="nav-link {{ $item['active'] ? 'active' : '' }}">
                            <i class="{{ $item['icon'] }}"></i>
                            <span class="nav-text">{{ $item['name'] }}</span>
                            @if(isset($item['badge']) && $item['badge'] > 0)
                                <span class="badge bg-primary ms-auto">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Messages Section -->
        @if(isset($menuItems['messages']) && count($menuItems['messages']) > 0)
            <div class="nav-section">
                <h6 class="nav-section-title">{{ __('sidebar.user_dashboard.messages') }}</h6>
                <ul class="nav-menu">
                    @foreach($menuItems['messages'] as $item)
                        <li class="nav-item">
                            <a href="{{ route($item['route']) }}"
                               class="nav-link {{ $item['active'] ? 'active' : '' }}">
                                <i class="{{ $item['icon'] }}"></i>
                                <span class="nav-text">{{ $item['name'] }}</span>
                                @if(isset($item['badge']) && $item['badge'] > 0)
                                    <span class="badge bg-primary ms-auto">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Marketplace Section -->
        @if(isset($menuItems['marketplace']) && count($menuItems['marketplace']) > 0)
            <div class="nav-section">
                <h6 class="nav-section-title">{{ __('sidebar.marketplace') }}</h6>
                <ul class="nav-menu">
                    @foreach($menuItems['marketplace'] as $item)
                        <li class="nav-item">
                            <a href="{{ route($item['route']) }}"
                               class="nav-link {{ $item['active'] ? 'active' : '' }}">
                                <i class="{{ $item['icon'] }}"></i>
                                <span class="nav-text">{{ $item['name'] }}</span>
                                @if(isset($item['badge']) && $item['badge'] > 0)
                                    <span class="badge bg-success ms-auto">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="nav-section">
            <h6 class="nav-section-title">{{ __('sidebar.quick_actions') }}</h6>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('threads.create') }}" class="nav-link">
                        <i class="fas fa-plus-circle"></i>
                        <span class="nav-text">{{ __('sidebar.new_thread') }}</span>
                    </a>
                </li>
                @if($currentUser->canAccessMarketplace())
                    <li class="nav-item">
                        <a href="{{ route('marketplace.index') }}" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="nav-text">{{ __('sidebar.browse_products') }}</span>
                        </a>
                    </li>
                @endif
                @if($currentUser->canSell())
                    <li class="nav-item">
                        <a href="{{ route('dashboard.marketplace.seller.products.create') }}" class="nav-link">
                            <i class="fas fa-box"></i>
                            <span class="nav-text">{{ __('sidebar.add_product') }}</span>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('showcase.create') }}" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span class="nav-text">{{ __('sidebar.create_showcase') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="footer-links">
            <a href="{{ route('home') }}" class="footer-link" title="{{ __('ui.back_to_site') }}">
                <i class="fas fa-home"></i>
            </a>
            {{-- TODO: Implement help page --}}
            {{-- <a href="{{ route('help') }}" class="footer-link" title="{{ __('ui.help') }}">
                <i class="fas fa-question-circle"></i>
            </a> --}}
            <a href="{{ route('dashboard.settings.index') }}" class="footer-link" title="{{ __('ui.settings') }}">
                <i class="fas fa-cog"></i>
            </a>
        </div>
        <div class="footer-text">
            <small>&copy; {{ date('Y') }} MechaMap</small>
        </div>
    </div>
</div>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay d-lg-none"></div>

<style>
.dashboard-sidebar {
    width: 280px;
    height: 100vh;
    background: #fff;
    border-right: 1px solid #e9ecef;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    overflow-y: auto;
    transition: transform 0.3s ease;
}

.sidebar-header {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #333;
}

.logo-img {
    width: 32px;
    height: 32px;
    margin-right: 0.5rem;
}

.logo-text {
    font-size: 1.25rem;
    font-weight: 600;
}

.sidebar-toggle {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #6c757d;
}

.sidebar-user {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
}

.user-avatar {
    margin-right: 0.75rem;
}

.avatar-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.user-info {
    flex: 1;
}

.user-name {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
}

.user-role {
    font-size: 0.8rem;
    color: #6c757d;
}

.sidebar-nav {
    padding: 1rem 0;
    flex: 1;
}

.nav-section {
    margin-bottom: 1.5rem;
}

.nav-section-title {
    padding: 0 1rem;
    margin-bottom: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: 0.5px;
}

.nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 0.25rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s ease;
    border-radius: 0;
}

.nav-link:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.nav-link.active {
    background-color: #007bff;
    color: #fff;
    border-right: 3px solid #0056b3;
}

.nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    text-align: center;
}

.nav-text {
    flex: 1;
    font-size: 0.9rem;
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid #e9ecef;
    margin-top: auto;
}

.footer-links {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.footer-link {
    color: #6c757d;
    text-decoration: none;
    font-size: 1.1rem;
    transition: color 0.2s ease;
}

.footer-link:hover {
    color: #007bff;
}

.footer-text {
    text-align: center;
    color: #6c757d;
}

.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    display: none;
}

/* Mobile Responsive */
@media (max-width: 991.98px) {
    .dashboard-sidebar {
        transform: translateX(-100%);
    }

    .dashboard-wrapper.sidebar-open .dashboard-sidebar {
        transform: translateX(0);
    }

    .dashboard-wrapper.sidebar-open .sidebar-overlay {
        display: block;
    }
}
</style>
