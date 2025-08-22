@extends('layouts.app')

@section('title', __('marketplace.suppliers.title'))
@section('meta_description', __('marketplace.suppliers.meta_description'))

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-store text-primary me-2"></i>
                        {{ __('marketplace.suppliers.title') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('marketplace.suppliers.subtitle') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('common.back_to_marketplace') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-store fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ number_format($stats['total_suppliers']) }}</h4>
                    <small class="text-muted">{{ __('marketplace.suppliers.total_suppliers') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-certificate fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ number_format($stats['verified_suppliers']) }}</h4>
                    <small class="text-muted">{{ __('marketplace.suppliers.verified_suppliers') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ number_format($stats['total_products']) }}</h4>
                    <small class="text-muted">{{ __('marketplace.suppliers.total_products') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('marketplace.suppliers.index') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}"
                           placeholder="{{ __('marketplace.suppliers.search_placeholder') }}">
                </div>

                <!-- Location -->
                <div class="col-md-3">
                    <label for="location" class="form-label">{{ __('marketplace.suppliers.location') }}</label>
                    <input type="text" class="form-control" id="location" name="location"
                           value="{{ request('location') }}"
                           placeholder="{{ __('marketplace.suppliers.location_placeholder') }}">
                </div>

                <!-- Verification Status -->
                <div class="col-md-2">
                    <label for="verified" class="form-label">{{ __('marketplace.suppliers.verification') }}</label>
                    <select class="form-select" id="verified" name="verified">
                        <option value="">{{ __('common.all') }}</option>
                        <option value="1" {{ request('verified') == '1' ? 'selected' : '' }}>
                            {{ __('marketplace.suppliers.verified_only') }}
                        </option>
                    </select>
                </div>

                <!-- Sort -->
                <div class="col-md-2">
                    <label for="sort" class="form-label">{{ __('common.sort_by') }}</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                            {{ __('common.newest') }}
                        </option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                            {{ __('common.oldest') }}
                        </option>
                        <option value="top_rated" {{ request('sort') == 'top_rated' ? 'selected' : '' }}>
                            {{ __('marketplace.suppliers.top_rated') }}
                        </option>
                        <option value="most_sales" {{ request('sort') == 'most_sales' ? 'selected' : '' }}>
                            {{ __('marketplace.suppliers.most_sales') }}
                        </option>
                        <option value="most_products" {{ request('sort') == 'most_products' ? 'selected' : '' }}>
                            {{ __('marketplace.suppliers.most_products') }}
                        </option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers Grid -->
    <div class="row">
        @forelse($suppliers as $supplier)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <!-- Supplier Header -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                @if($supplier->user->avatar)
                                    <img src="{{ asset('images/avatars/' . $supplier->user->avatar) }}"
                                         alt="{{ $supplier->user->name }}"
                                         class="rounded-circle" width="50" height="50">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">
                                    @if($supplier->store_slug)
                                        <a href="{{ route('marketplace.sellers.show', $supplier->store_slug) }}"
                                           class="text-decoration-none">
                                            {{ $supplier->store_name }}
                                        </a>
                                    @else
                                        {{ $supplier->store_name }}
                                    @endif
                                    @if($supplier->verification_status === 'verified')
                                        <i class="fas fa-certificate text-success ms-1"
                                           title="{{ __('marketplace.suppliers.verified') }}"></i>
                                    @endif
                                </h5>
                                <small class="text-muted">{{ $supplier->user->name }}</small>
                            </div>
                        </div>

                        <!-- Supplier Info -->
                        <div class="mb-3">
                            @if($supplier->store_description)
                                <p class="text-muted small mb-2">
                                    {{ Str::limit($supplier->store_description, 100) }}
                                </p>
                            @endif

                            @if($supplier->location)
                                <div class="d-flex align-items-center text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $supplier->location }}
                                </div>
                            @endif

                            <div class="d-flex align-items-center text-muted small mb-1">
                                <i class="fas fa-calendar me-2"></i>
                                {{ __('marketplace.suppliers.joined') }} {{ $supplier->created_at->format('M Y') }}
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fw-bold">{{ number_format($supplier->products_count ?? 0) }}</div>
                                    <small class="text-muted">{{ __('marketplace.suppliers.products') }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fw-bold">{{ number_format($supplier->total_sales ?? 0) }}</div>
                                    <small class="text-muted">{{ __('marketplace.suppliers.sales') }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">
                                    @if($supplier->average_rating)
                                        {{ number_format($supplier->average_rating, 1) }}
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ __('marketplace.suppliers.rating') }}</small>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="d-grid">
                            @if($supplier->store_slug)
                                <a href="{{ route('marketplace.sellers.show', $supplier->store_slug) }}"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-2"></i>{{ __('marketplace.suppliers.view_store') }}
                                </a>
                            @else
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('marketplace.suppliers.store_not_ready') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('marketplace.suppliers.no_suppliers') }}</h4>
                    <p class="text-muted">{{ __('marketplace.suppliers.no_suppliers_desc') }}</p>
                    <a href="{{ route('marketplace.index') }}" class="btn btn-primary">
                        {{ __('marketplace.suppliers.browse_products') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($suppliers->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $suppliers->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}
</style>
@endpush
