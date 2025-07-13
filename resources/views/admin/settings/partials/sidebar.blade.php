<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Cài đặt') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.settings.general') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                <i class="fas fa-cog me-2"></i> {{ __('Cấu hình chung') }}
            </a>
            <a href="{{ route('admin.settings.company') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.company') ? 'active' : '' }}">
                <i class="fas fa-building me-2"></i> {{ __('Thông tin công ty') }}
            </a>
            <a href="{{ route('admin.settings.contact') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.contact') ? 'active' : '' }}">
                <i class="fas fa-envelope me-2"></i> {{ __('Thông tin liên hệ') }}
            </a>
            <a href="{{ route('admin.settings.social') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.social') ? 'active' : '' }}">
                <i class="fas fa-share me-2"></i> {{ __('Mạng xã hội') }}
            </a>
            <a href="{{ route('admin.settings.api') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.api') ? 'active' : '' }}">
                <i class="fas fa-key me-2"></i> {{ __('API Keys') }}
            </a>
            <a href="{{ route('admin.settings.copyright') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.copyright') ? 'active' : '' }}">
                <i class="fas fa-copyright me-2"></i> {{ __('Bản quyền') }}
            </a>
            <a href="{{ route('admin.settings.forum') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.forum') ? 'active' : '' }}">
                <i class="fas fa-comment me-2"></i> {{ __(__('forum.forums.title')) }}
            </a>
            <a href="{{ route('admin.settings.user') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.user') ? 'active' : '' }}">
                <i class="fas fa-users me-2"></i> {{ __('Người dùng') }}
            </a>
            <a href="{{ route('admin.settings.email') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                <i class="fas fa-envelope-at me-2"></i> {{ __('Email') }}
            </a>
            <a href="{{ route('admin.settings.security') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.security') ? 'active' : '' }}">
                <i class="fas fa-shield-alt me-2"></i> {{ __('Bảo mật') }}
            </a>
            <a href="{{ route('admin.settings.wiki') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.wiki') ? 'active' : '' }}">
                <i class="fas fa-book me-2"></i> {{ __('Wiki') }}
            </a>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Hướng dẫn') }}</h5>
    </div>
    <div class="card-body">
        <p class="card-text">
            {{ __('Cài đặt các thông tin cơ bản của trang web. Các thông tin này sẽ được sử dụng ở nhiều nơi trên trang
            web.') }}
        </p>
        <p class="mb-0">
            {{ __('Sau khi thay đổi cài đặt, hãy nhấn nút "Lưu cấu hình" để lưu lại.') }}
        </p>
    </div>
</div>