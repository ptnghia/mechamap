<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('home') }}">
            @if(get_logo_url() && get_logo_url() != '/images/logo.png')
                <img src="{{ get_logo_url() }}" alt="{{ setting('site_name', config('app.name')) }}" height="40">
            @else
                <x-application-logo style="width: 40px; height: 40px;" class="fill-current text-primary" />
            @endif
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door me-1"></i> {{ __('Home') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('threads.index') ? 'active' : '' }}" href="{{ route('threads.index') }}">
                        <i class="bi bi-layout-text-window me-1"></i> {{ __('Forums') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('threads.create') ? 'active' : '' }}" href="{{ route('threads.create') }}">
                        <i class="bi bi-plus-circle me-1"></i> {{ __('New Thread') }}
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs(['whats-new', 'forums.listing', 'showcase.public', 'gallery.*', 'search.*', 'members.*', 'faq.*']) ? 'active' : '' }}" href="#" id="moreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots me-1"></i> {{ __('More') }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="moreDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('whats-new') ? 'active' : '' }}" href="{{ route('whats-new') }}">
                                {{ __('What\'s New') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('forums.listing') ? 'active' : '' }}" href="{{ route('forums.listing') }}">
                                {{ __('Forum Listing') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('showcase.public') ? 'active' : '' }}" href="{{ route('showcase.public') }}">
                                {{ __('Showcase') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">
                                {{ __('Gallery') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('search.advanced') ? 'active' : '' }}" href="{{ route('search.advanced') }}">
                                {{ __('Advanced Search') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('members.*') ? 'active' : '' }}" href="{{ route('members.index') }}">
                                {{ __('Members') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('faq.*') ? 'active' : '' }}" href="{{ route('faq.index') }}">
                                {{ __('FAQ') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <div class="dropdown-item d-flex justify-content-between align-items-center">
                                <span>{{ __('Original view') }}</span>
                                <div class="form-check form-switch">
                                    <form action="{{ route('theme.original-view') }}" method="POST" id="originalViewForm">
                                        @csrf
                                        <input class="form-check-input" type="checkbox" role="switch" id="originalViewSwitch" onchange="document.getElementById('originalViewForm').submit()" {{ request()->cookie('original_view') == 'original' ? 'checked' : '' }}>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-item d-flex justify-content-between align-items-center">
                                <span>{{ __('Dark Mode') }}</span>
                                <div class="form-check form-switch">
                                    <form action="{{ route('theme.dark-mode') }}" method="POST" id="darkModeForm">
                                        @csrf
                                        <input class="form-check-input" type="checkbox" role="switch" id="darkModeSwitch" onchange="document.getElementById('darkModeForm').submit()" {{ request()->cookie('dark_mode') == 'dark' ? 'checked' : '' }}>
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>

                @if (Auth::check() && Auth::user()->canAccessAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        {{ __('Admin') }}
                    </a>
                </li>
                @endif
            </ul>

            <!-- Right Side Menu -->
            @auth
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show', Auth::user()->username) }}">
                            <i class="bi bi-person me-2"></i> {{ __('My Profile') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('following.index') }}">
                            <i class="bi bi-people me-2"></i> {{ __('Following') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('alerts.index') }}">
                            <i class="bi bi-bell me-2"></i> {{ __('Alerts') }}
                            @php
                                $unreadAlertsCount = Auth::user()->alerts()->whereNull('read_at')->count();
                            @endphp
                            @if($unreadAlertsCount > 0)
                                <span class="badge bg-danger rounded-pill ms-2">{{ $unreadAlertsCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('conversations.index') }}">
                            <i class="bi bi-chat-dots me-2"></i> {{ __('Conversations') }}
                            @php
                                $unreadConversationsCount = 0;
                                foreach(Auth::user()->conversations as $conversation) {
                                    if($conversation->hasUnreadMessages(Auth::id())) {
                                        $unreadConversationsCount++;
                                    }
                                }
                            @endphp
                            @if($unreadConversationsCount > 0)
                                <span class="badge bg-danger rounded-pill ms-2">{{ $unreadConversationsCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('threads.saved') }}">
                            <i class="bi bi-bookmark me-2"></i> {{ __('Saved Threads') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('showcase.index') }}">
                            <i class="bi bi-stars me-2"></i> {{ __('My Showcase') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-gear me-2"></i> {{ __('Account Settings') }}
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('business.index') }}">
                            <i class="bi bi-graph-up me-2"></i> {{ __('Grow Your Business') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('subscription.index') }}">
                            <i class="bi bi-star me-2"></i> {{ __('Upgrade your account') }}
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right me-2"></i> {{ __('Log out') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @else
            <div class="d-flex">
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">{{ __('Log in') }}</a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Register') }}</a>
                @endif
            </div>
            @endauth
        </div>
    </div>
</nav>
