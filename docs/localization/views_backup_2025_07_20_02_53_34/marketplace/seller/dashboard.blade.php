@extends('layouts.app')

@section('title', __('marketplace.seller_dashboard'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-store me-2"></i>
                        {{ __('marketplace.seller_menu') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('marketplace.seller.dashboard') }}" class="list-group-item list-group-item-action active">
                            <i class="fa-solid fa-chart-line me-2"></i>
                            {{ __('marketplace.dashboard') }}
                        </a>
                        <a href="{{ route('marketplace.seller.products') }}" class="list-group-item list-group-item-action">
                            <i class="fa-solid fa-box me-2"></i>
                            {{ __('marketplace.my_products') }}
                        </a>
                        <a href="{{ route('marketplace.seller.orders') }}" class="list-group-item list-group-item-action">
                            <i class="fa-solid fa-shopping-cart me-2"></i>
                            {{ __('marketplace.my_orders') }}
                        </a>
                        <a href="{{ route('marketplace.seller.analytics') }}" class="list-group-item list-group-item-action">
                            <i class="fa-solid fa-chart-bar me-2"></i>
                            {{ __('marketplace.analytics') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Seller Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('marketplace.seller_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($seller->logo)
                            <img src="{{ asset('images/' . $seller->logo) }}" alt="{{ $seller->business_name }}" class="rounded-circle" width="80" height="80">
                        @else
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-store text-white fa-2x"></i>
                            </div>
                        @endif
                    </div>
                    <h6 class="text-center">{{ $seller->business_name }}</h6>
                    <p class="text-muted text-center small">{{ $seller->business_type }}</p>
                    <div class="d-flex justify-content-between small">
                        <span>{{ __('marketplace.status') }}:</span>
                        <span class="badge bg-{{ $seller->status === 'active' ? 'success' : 'warning' }}">
                            {{ __('marketplace.status.' . $seller->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Welcome Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>{{ __('marketplace.welcome_seller', ['name' => $seller->business_name]) }}</h2>
                    <p class="text-muted">{{ __('marketplace.seller_dashboard_desc') }}</p>
                </div>
                <div>
                    <a href="{{ route('marketplace.products.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-2"></i>
                        {{ __('marketplace.add_product') }}
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total_products'] }}</h4>
                                    <p class="mb-0">{{ __('marketplace.total_products') }}</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ number_format($stats['total_sales']) }} VND</h4>
                                    <p class="mb-0">{{ __('marketplace.total_sales') }}</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total_orders'] }}</h4>
                                    <p class="mb-0">{{ __('marketplace.total_orders') }}</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ number_format($stats['this_month_sales']) }} VND</h4>
                                    <p class="mb-0">{{ __('marketplace.this_month_sales') }}</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-calendar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('marketplace.quick_actions') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('marketplace.products.create') }}" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-plus me-2"></i>
                                    {{ __('marketplace.add_new_product') }}
                                </a>
                                <a href="{{ route('marketplace.seller.products') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-edit me-2"></i>
                                    {{ __('marketplace.manage_products') }}
                                </a>
                                <a href="{{ route('marketplace.seller.orders') }}" class="btn btn-outline-info">
                                    <i class="fa-solid fa-list me-2"></i>
                                    {{ __('marketplace.view_orders') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('marketplace.product_status') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="text-success">{{ $stats['active_products'] }}</h4>
                                    <small class="text-muted">{{ __('marketplace.active') }}</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-warning">{{ $stats['pending_products'] }}</h4>
                                    <small class="text-muted">{{ __('marketplace.pending') }}</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-info">{{ $stats['total_products'] }}</h4>
                                    <small class="text-muted">{{ __('marketplace.total') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Products & Orders -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ __('marketplace.recent_products') }}</h6>
                            <a href="{{ route('marketplace.seller.products') }}" class="btn btn-sm btn-outline-primary">
                                {{ __('marketplace.view_all') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($recentProducts as $product)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        @if($product->images && count($product->images) > 0)
                                            <img src="{{ asset('images/' . $product->images[0]) }}" alt="{{ $product->name }}" class="rounded" width="50" height="50">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fa-solid fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ Str::limit($product->name, 30) }}</h6>
                                        <small class="text-muted">{{ number_format($product->price) }} VND</small>
                                        <span class="badge bg-{{ $product->status === 'approved' ? 'success' : 'warning' }} ms-2">
                                            {{ __('marketplace.status.' . $product->status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">{{ __('marketplace.no_products_yet') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ __('marketplace.recent_orders') }}</h6>
                            <a href="{{ route('marketplace.seller.orders') }}" class="btn btn-sm btn-outline-primary">
                                {{ __('marketplace.view_all') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($recentOrders as $order)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1">#{{ $order->order_number }}</h6>
                                        <small class="text-muted">{{ $order->user->name ?? 'N/A' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">{{ number_format($order->total_amount) }} VND</div>
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ __('marketplace.order_status.' . $order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">{{ __('marketplace.no_orders_yet') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
