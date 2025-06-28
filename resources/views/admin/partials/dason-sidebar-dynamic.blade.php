{{-- 
    Dynamic Admin Sidebar - Phase 2
    Sidebar động theo permissions của user
--}}

@php
    use App\Services\AdminMenuService;
    $adminMenu = AdminMenuService::getAdminMenu(auth()->user());
    $currentRoute = request()->route()->getName();
@endphp

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">{{ __('Menu Chính') }}</li>

                @foreach($adminMenu as $menuItem)
                    @if(isset($menuItem['children']))
                        {{-- Menu có submenu --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i class="{{ $menuItem['icon'] ?? 'fas fa-circle' }}"></i>
                                <span data-key="t-{{ Str::slug($menuItem['title']) }}">{{ $menuItem['title'] }}</span>
                                @if(isset($menuItem['badge']))
                                    <span class="badge rounded-pill bg-{{ $menuItem['badge'] }} float-end">!</span>
                                @endif
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @foreach($menuItem['children'] as $child)
                                    <li>
                                        <a href="{{ isset($child['route']) ? route($child['route']) : '#' }}"
                                           class="{{ request()->routeIs($child['route'] ?? '') ? 'active' : '' }}">
                                            <i class="{{ $child['icon'] ?? 'fas fa-circle' }}"></i>
                                            <span data-key="t-{{ Str::slug($child['title']) }}">{{ $child['title'] }}</span>
                                            @if(isset($child['badge']))
                                                <span class="badge rounded-pill bg-{{ $child['badge'] }} float-end">
                                                    @if($child['badge'] === 'danger')
                                                        {{ \App\Models\Report::where('status', 'pending')->count() }}
                                                    @elseif($child['badge'] === 'info')
                                                        {{ auth()->user()->unreadMessages()->count() }}
                                                    @else
                                                        !
                                                    @endif
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        {{-- Menu đơn --}}
                        <li>
                            <a href="{{ isset($menuItem['route']) ? route($menuItem['route']) : '#' }}"
                               class="{{ request()->routeIs($menuItem['route'] ?? '') ? 'active' : '' }}">
                                <i class="{{ $menuItem['icon'] ?? 'fas fa-circle' }}"></i>
                                <span data-key="t-{{ Str::slug($menuItem['title']) }}">{{ $menuItem['title'] }}</span>
                                @if(isset($menuItem['badge']))
                                    <span class="badge rounded-pill bg-{{ $menuItem['badge'] }} float-end">
                                        @if($menuItem['badge'] === 'info')
                                            {{ auth()->user()->unreadMessages()->count() }}
                                        @else
                                            !
                                        @endif
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach

                {{-- Separator --}}
                <li class="menu-title mt-2" data-key="t-components">{{ __('Công cụ') }}</li>

                {{-- Quick Actions dựa trên role --}}
                @if(auth()->user()->isSystemManagement())
                    <li>
                        <a href="{{ route('admin.users.create') }}">
                            <i class="fas fa-user-plus"></i>
                            <span data-key="t-add-user">{{ __('Thêm người dùng') }}</span>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->hasPermissionTo('manage-marketplace'))
                    <li>
                        <a href="{{ route('admin.products.pending') }}">
                            <i class="fas fa-clock"></i>
                            <span data-key="t-pending-products">{{ __('Sản phẩm chờ duyệt') }}</span>
                            <span class="badge rounded-pill bg-warning float-end">
                                {{ \App\Models\Product::where('status', 'pending')->count() }}
                            </span>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->hasPermissionTo('moderate-content'))
                    <li>
                        <a href="{{ route('admin.reports.urgent') }}">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span data-key="t-urgent-reports">{{ __('Báo cáo khẩn cấp') }}</span>
                            <span class="badge rounded-pill bg-danger float-end">
                                {{ \App\Models\Report::where('priority', 'high')->where('status', 'pending')->count() }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Role Info --}}
                <li class="menu-title mt-2" data-key="t-role-info">{{ __('Thông tin vai trò') }}</li>
                <li>
                    <div class="card border-0 shadow-none mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <span class="avatar-title rounded-circle bg-{{ auth()->user()->getRoleColor() }} text-white">
                                        <i class="{{ config('mechamap_permissions.role_groups.' . auth()->user()->role_group . '.icon', 'fas fa-user') }}"></i>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h6 class="mb-1 font-size-14">{{ auth()->user()->getRoleDisplayName() }}</h6>
                                    <p class="text-muted mb-0 font-size-12">{{ auth()->user()->getRoleGroupDisplayName() }}</p>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    {{ auth()->user()->getAllPermissions()->count() }} quyền hạn
                                </small>
                            </div>
                        </div>
                    </div>
                </li>

                {{-- System Status (chỉ cho System Management) --}}
                @if(auth()->user()->isSystemManagement())
                    <li class="menu-title mt-2" data-key="t-system">{{ __('Trạng thái hệ thống') }}</li>
                    <li>
                        <div class="card border-0 shadow-none mb-0">
                            <div class="card-body p-3">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h5 class="mb-1 text-primary">{{ \App\Models\User::count() }}</h5>
                                        <p class="text-muted mb-0 font-size-11">Users</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="mb-1 text-success">{{ \App\Models\User::where('is_active', true)->count() }}</h5>
                                        <p class="text-muted mb-0 font-size-11">Online</p>
                                    </div>
                                </div>
                                @if(auth()->user()->hasPermissionTo('view-system-logs'))
                                    <div class="mt-2">
                                        <a href="{{ route('admin.system.status') }}" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-heartbeat me-1"></i> Chi tiết
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>

{{-- Custom CSS cho dynamic sidebar --}}
<style>
.vertical-menu .metismenu .active {
    color: #556ee6 !important;
    background-color: rgba(85, 110, 230, 0.1);
    border-radius: 4px;
}

.vertical-menu .metismenu .active i {
    color: #556ee6 !important;
}

.badge.float-end {
    margin-top: 2px;
}

.card.border-0.shadow-none {
    background-color: rgba(0, 0, 0, 0.02);
}

.menu-title {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.5px;
}
</style>

{{-- JavaScript cho dynamic features --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto refresh badges every 30 seconds
    setInterval(function() {
        // Refresh notification badges
        fetch('{{ route("admin.notifications.count") }}')
            .then(response => response.json())
            .then(data => {
                // Update badge counts
                document.querySelectorAll('.badge').forEach(badge => {
                    if (badge.dataset.type) {
                        badge.textContent = data[badge.dataset.type] || 0;
                    }
                });
            })
            .catch(error => console.log('Badge refresh error:', error));
    }, 30000);

    // Highlight active menu based on current route
    const currentPath = window.location.pathname;
    document.querySelectorAll('#side-menu a').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
            // Expand parent menu if needed
            const parentLi = link.closest('li');
            if (parentLi && parentLi.querySelector('.has-arrow')) {
                parentLi.classList.add('mm-active');
                const submenu = parentLi.querySelector('.sub-menu');
                if (submenu) {
                    submenu.style.display = 'block';
                }
            }
        }
    });
});
</script>
