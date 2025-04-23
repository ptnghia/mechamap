<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> {{ __('Users') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i> {{ __('Chuyên mục') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.forums.*') ? 'active' : '' }}" href="{{ route('admin.forums.index') }}">
                    <i class="bi bi-chat-left-text"></i> {{ __('Diễn đàn') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.threads.*') ? 'active' : '' }}" href="{{ route('admin.threads.index') }}">
                    <i class="bi bi-file-earmark-text"></i> {{ __('Bài đăng') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">
                    <i class="bi bi-chat-dots"></i> {{ __('Bình luận') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pages.*') || request()->routeIs('admin.page-categories.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
                    <i class="bi bi-file-earmark-text"></i> {{ __('Bài viết') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}">
                    <i class="bi bi-question-circle"></i> {{ __('Hỏi đáp') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">
                    <i class="bi bi-image"></i> {{ __('Media') }}
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>{{ __('Account') }}</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.index') }}">
                    <i class="bi bi-person"></i> {{ __('My Profile') }}
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>{{ __('Thống kê') }}</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.statistics.index') ? 'active' : '' }}" href="{{ route('admin.statistics.index') }}">
                    <i class="bi bi-bar-chart"></i> {{ __('Tổng quan') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.statistics.users') ? 'active' : '' }}" href="{{ route('admin.statistics.users') }}">
                    <i class="bi bi-people"></i> {{ __('Người dùng') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.statistics.content') ? 'active' : '' }}" href="{{ route('admin.statistics.content') }}">
                    <i class="bi bi-file-text"></i> {{ __('Nội dung') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.statistics.interactions') ? 'active' : '' }}" href="{{ route('admin.statistics.interactions') }}">
                    <i class="bi bi-graph-up"></i> {{ __('Tương tác') }}
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>{{ __('System') }}</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}" href="{{ route('admin.seo.index') }}">
                    <i class="bi bi-search"></i> {{ __('SEO') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.general') }}">
                    <i class="bi bi-gear"></i> {{ __('Settings') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-file-earmark-code"></i> {{ __('Logs') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> {{ __('View Site') }}
                </a>
            </li>
        </ul>
    </div>
</nav>
