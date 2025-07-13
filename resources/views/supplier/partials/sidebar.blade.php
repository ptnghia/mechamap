{{-- Supplier Dashboard Sidebar --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">
        {{-- User Info --}}
        <div class="text-center mb-4">
            <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=7F9CF5&background=EBF4FF' }}" 
                 alt="{{ auth()->user()->name }}" 
                 class="rounded-circle mb-3" 
                 width="80" height="80"
                 style="object-fit: cover;">
            <h6 class="fw-bold mb-1">{{ auth()->user()->name }}</h6>
            <span class="badge bg-primary">{{ __('roles.supplier') }}</span>
        </div>

        {{-- Navigation Menu --}}
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}" 
               href="{{ route('supplier.dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i>
                {{ __('navigation.dashboard') }}
            </a>
            
            <a class="nav-link {{ request()->routeIs('supplier.products.*') ? 'active' : '' }}" 
               href="{{ route('supplier.products.index') }}">
                <i class="fas fa-box me-2"></i>
                {{ __('navigation.products') }}
            </a>
            
            <a class="nav-link {{ request()->routeIs('supplier.orders.*') ? 'active' : '' }}" 
               href="{{ route('supplier.orders.index') }}">
                <i class="fas fa-shopping-cart me-2"></i>
                {{ __('navigation.orders') }}
            </a>
            
            <a class="nav-link {{ request()->routeIs('supplier.analytics.*') ? 'active' : '' }}" 
               href="{{ route('supplier.analytics.index') }}">
                <i class="fas fa-chart-line me-2"></i>
                {{ __('navigation.analytics') }}
            </a>
            
            <a class="nav-link {{ request()->routeIs('supplier.settings.*') ? 'active' : '' }}" 
               href="{{ route('supplier.settings.index') }}">
                <i class="fas fa-cog me-2"></i>
                {{ __('navigation.settings') }}
            </a>
        </nav>

        {{-- Quick Stats --}}
        <div class="mt-4">
            <h6 class="text-muted mb-3">{{ __('dashboard.quick_stats') }}</h6>
            @php
                $seller = \App\Models\MarketplaceSeller::where('user_id', auth()->id())->first();
                $stats = [
                    'products' => $seller ? \App\Models\MarketplaceProduct::where('seller_id', $seller->id)->count() : 0,
                    'orders' => $seller ? \App\Models\MarketplaceOrderItem::where('seller_id', $seller->id)->count() : 0,
                    'revenue' => $seller ? \App\Models\MarketplaceOrderItem::where('seller_id', $seller->id)->sum('total_amount') : 0,
                ];
            @endphp
            
            <div class="row g-2 text-center">
                <div class="col-6">
                    <div class="p-2 bg-light rounded">
                        <div class="fw-bold text-primary">{{ number_format($stats['products']) }}</div>
                        <small class="text-muted">{{ __('dashboard.products') }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 bg-light rounded">
                        <div class="fw-bold text-success">{{ number_format($stats['orders']) }}</div>
                        <small class="text-muted">{{ __('dashboard.orders') }}</small>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-2 bg-light rounded">
                        <div class="fw-bold text-warning">{{ number_format($stats['revenue'], 0, ',', '.') }} VND</div>
                        <small class="text-muted">{{ __('dashboard.total_revenue') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-link {
    color: #6c757d;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin-bottom: 0.25rem;
    transition: all 0.2s ease;
}

.nav-link:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.nav-link.active {
    background-color: #0d6efd;
    color: white;
}

.nav-link.active:hover {
    background-color: #0b5ed7;
    color: white;
}
</style>
