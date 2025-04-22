<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Cài đặt') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.settings.general') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                <i class="bi bi-gear-fill me-2"></i> {{ __('Cấu hình chung') }}
            </a>
            <a href="{{ route('admin.settings.company') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.company') ? 'active' : '' }}">
                <i class="bi bi-building me-2"></i> {{ __('Thông tin công ty') }}
            </a>
            <a href="{{ route('admin.settings.contact') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.contact') ? 'active' : '' }}">
                <i class="bi bi-envelope me-2"></i> {{ __('Thông tin liên hệ') }}
            </a>
            <a href="{{ route('admin.settings.social') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.social') ? 'active' : '' }}">
                <i class="bi bi-share me-2"></i> {{ __('Mạng xã hội') }}
            </a>
            <a href="{{ route('admin.settings.api') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.api') ? 'active' : '' }}">
                <i class="bi bi-key me-2"></i> {{ __('API Keys') }}
            </a>
            <a href="{{ route('admin.settings.copyright') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.copyright') ? 'active' : '' }}">
                <i class="bi bi-c-circle me-2"></i> {{ __('Bản quyền') }}
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
            {{ __('Cài đặt các thông tin cơ bản của trang web. Các thông tin này sẽ được sử dụng ở nhiều nơi trên trang web.') }}
        </p>
        <p class="mb-0">
            {{ __('Sau khi thay đổi cài đặt, hãy nhấn nút "Lưu cấu hình" để lưu lại.') }}
        </p>
    </div>
</div>
