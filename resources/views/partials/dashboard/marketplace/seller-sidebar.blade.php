<!-- Seller Sidebar -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fa-solid fa-store me-2"></i>
            {{ __('marketplace.seller_dashboard') }}
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <!-- Dashboard -->
            <a href="{{ route('dashboard.marketplace.seller.dashboard') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('dashboard.marketplace.seller.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line me-2"></i>
                {{ __('marketplace.dashboard') }}
            </a>

            <!-- Products -->
            <a href="{{ route('dashboard.marketplace.seller.products.index') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('dashboard.marketplace.seller.products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box me-2"></i>
                {{ __('marketplace.my_products') }}
            </a>

            <!-- Orders -->
            <a href="{{ route('dashboard.marketplace.seller.orders') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('dashboard.marketplace.seller.orders*') ? 'active' : '' }}">
                <i class="fa-solid fa-shopping-cart me-2"></i>
                {{ __('marketplace.my_orders') }}
            </a>

            <!-- Analytics -->
            <a href="{{ route('dashboard.marketplace.seller.analytics.index') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('dashboard.marketplace.seller.analytics.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar me-2"></i>
                {{ __('marketplace.analytics') }}
            </a>

            <!-- Settings -->
            <a href="#"
               class="list-group-item list-group-item-action disabled">
                <i class="fa-solid fa-cog me-2"></i>
                {{ __('marketplace.seller_settings') }} <small class="text-muted">({{ __('ui.coming_soon') }})</small>
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mt-3">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fa-solid fa-bolt me-2"></i>
            {{ __('ui.quick_actions') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="d-grid gap-2">
            <a href="{{ route('dashboard.marketplace.seller.products.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>
                {{ __('marketplace.add_product') }}
            </a>
            <a href="{{ route('dashboard.marketplace.seller.orders') }}?status=pending" class="btn btn-warning btn-sm">
                <i class="fa-solid fa-clock me-1"></i>
                {{ __('marketplace.pending_orders') }}
            </a>
            <a href="{{ route('dashboard.marketplace.seller.analytics.index') }}" class="btn btn-info btn-sm">
                <i class="fa-solid fa-chart-line me-1"></i>
                {{ __('marketplace.view_analytics') }}
            </a>
        </div>
    </div>
</div>

<!-- Seller Info -->
@if(isset($seller) && $seller)
<div class="card mt-3">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fa-solid fa-user-tie me-2"></i>
            {{ __('marketplace.seller_info') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="text-center mb-3">
            @if($seller->logo)
                <img src="{{ asset('images/' . $seller->logo) }}"
                     alt="{{ $seller->business_name }}"
                     class="rounded-circle mb-2"
                     width="60" height="60">
            @else
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-store text-white fa-lg"></i>
                </div>
            @endif
            <h6 class="mb-1">{{ $seller->business_name }}</h6>
            <small class="text-muted">{{ $seller->business_type }}</small>
        </div>

        <div class="row text-center">
            <div class="col-6">
                <div class="border-end">
                    <div class="fw-bold text-primary">{{ $seller->products()->count() }}</div>
                    <small class="text-muted">{{ __('marketplace.products') }}</small>
                </div>
            </div>
            <div class="col-6">
                <div class="fw-bold text-success">{{ $seller->orderItems()->count() }}</div>
                <small class="text-muted">{{ __('marketplace.orders') }}</small>
            </div>
        </div>

        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted">{{ __('marketplace.rating') }}</small>
                <div>
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star {{ $i <= ($seller->rating ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    <small class="text-muted ms-1">({{ $seller->rating ?? 0 }})</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
