@extends('layouts.user-dashboard')

@section('title', __('marketplace.products.title'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="fas fa-box me-2 text-primary"></i>
                    {{ __('marketplace.products.title') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('common.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('marketplace.products.title') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">{{ __('marketplace.products.total_products') }}</p>
                            <h4 class="mb-2">{{ number_format($stats['total_products']) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>
                                    {{ $stats['this_month_products'] }}
                                </span>
                                {{ __('marketplace.products.this_month') }}
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fas fa-box font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">{{ __('marketplace.products.active_products') }}</p>
                            <h4 class="mb-2">{{ number_format($stats['active_products']) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>
                                    {{ number_format(($stats['active_products'] / max($stats['total_products'], 1)) * 100, 1) }}%
                                </span>
                                {{ __('marketplace.products.of_total') }}
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="fas fa-check-circle font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">{{ __('marketplace.products.total_views') }}</p>
                            <h4 class="mb-2">{{ number_format($stats['total_views']) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-info fw-bold font-size-12 me-2">
                                    <i class="ri-eye-line me-1 align-middle"></i>
                                    {{ number_format($stats['total_views'] / max($stats['total_products'], 1), 1) }}
                                </span>
                                {{ __('marketplace.products.avg_per_product') }}
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="fas fa-eye font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">{{ __('marketplace.products.total_sales') }}</p>
                            <h4 class="mb-2">{{ number_format($stats['total_sales']) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-warning fw-bold font-size-12 me-2">
                                    <i class="ri-shopping-cart-line me-1 align-middle"></i>
                                    {{ number_format($stats['total_sales'] / max($stats['total_products'], 1), 1) }}
                                </span>
                                {{ __('marketplace.products.avg_per_product') }}
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="fas fa-shopping-cart font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Type Distribution -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('marketplace.products.product_types') }}</h4>
                </div>
                <div class="card-body">
                    @if(count($stats['product_types']) > 0)
                        @foreach($stats['product_types'] as $type)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-{{ $type->product_type === 'digital' ? 'primary' : 'success' }} text-white">
                                    <i class="fas fa-{{ $type->product_type === 'digital' ? 'download' : 'box' }}"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">{{ __('marketplace.products.type_' . $type->product_type) }}</p>
                                <h5 class="font-size-16 mb-0">{{ number_format($type->count) }}</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-light text-muted">
                                    {{ number_format(($type->count / max($stats['total_products'], 1)) * 100, 1) }}%
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open text-muted font-size-48 mb-3"></i>
                            <p class="text-muted">{{ __('marketplace.products.no_products_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('marketplace.products.quick_actions') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('dashboard.marketplace.seller.products.digital.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-download me-2"></i>
                                    {{ __('marketplace.digital_products.create_title') }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('dashboard.marketplace.seller.products.physical.create') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-box me-2"></i>
                                    {{ __('marketplace.physical_products.create_title') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Overview -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('marketplace.products.status_overview') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <h4 class="font-size-20 text-muted">{{ number_format($stats['draft_products']) }}</h4>
                                <p class="text-muted mb-0">{{ __('marketplace.products.draft') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <h4 class="font-size-20 text-warning">{{ number_format($stats['pending_products']) }}</h4>
                                <p class="text-muted mb-0">{{ __('marketplace.products.pending') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <h4 class="font-size-20 text-success">{{ number_format($stats['active_products']) }}</h4>
                                <p class="text-muted mb-0">{{ __('marketplace.products.active') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <h4 class="font-size-20 text-danger">{{ number_format($stats['rejected_products']) }}</h4>
                                <p class="text-muted mb-0">{{ __('marketplace.products.rejected') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">{{ __('marketplace.products.my_products') }}</h4>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-2"></i>{{ __('marketplace.products.filter') }}
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="?status=all">{{ __('marketplace.products.all_status') }}</a></li>
                                    <li><a class="dropdown-item" href="?status=active">{{ __('marketplace.products.active') }}</a></li>
                                    <li><a class="dropdown-item" href="?status=draft">{{ __('marketplace.products.draft') }}</a></li>
                                    <li><a class="dropdown-item" href="?status=pending">{{ __('marketplace.products.pending') }}</a></li>
                                    <li><a class="dropdown-item" href="?status=rejected">{{ __('marketplace.products.rejected') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="?type=digital">{{ __('marketplace.products.type_digital') }}</a></li>
                                    <li><a class="dropdown-item" href="?type=physical">{{ __('marketplace.products.type_physical') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('marketplace.products.product') }}</th>
                                        <th>{{ __('marketplace.products.type') }}</th>
                                        <th>{{ __('marketplace.products.category') }}</th>
                                        <th>{{ __('marketplace.products.price') }}</th>
                                        <th>{{ __('marketplace.products.status') }}</th>
                                        <th>{{ __('marketplace.products.stats') }}</th>
                                        <th>{{ __('marketplace.products.created') }}</th>
                                        <th>{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    @if($product->featured_image)
                                                        <img src="{{ Storage::url($product->featured_image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
                                                    @else
                                                        <span class="avatar-title bg-light text-primary rounded">
                                                            <i class="fas fa-{{ $product->product_type === 'digital' ? 'download' : 'box' }}"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h5 class="font-size-14 mb-1">
                                                        <a href="{{ route('marketplace.products.show', $product->slug) }}" class="text-dark">{{ $product->name }}</a>
                                                    </h5>
                                                    <p class="text-muted mb-0">{{ Str::limit($product->short_description, 50) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $product->product_type === 'digital' ? 'primary' : 'success' }}">
                                                {{ __('marketplace.products.type_' . $product->product_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $product->category->name ?? __('marketplace.products.uncategorized') }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                @if($product->sale_price && $product->is_on_sale)
                                                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price) }} VND</span><br>
                                                    <span class="text-success fw-bold">{{ number_format($product->sale_price) }} VND</span>
                                                @else
                                                    <span class="fw-bold">{{ number_format($product->price) }} VND</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $product->status === 'approved' ? 'success' : ($product->status === 'pending' ? 'warning' : ($product->status === 'draft' ? 'secondary' : 'danger')) }}">
                                                {{ __('marketplace.products.status_' . $product->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-muted small">
                                                <i class="fas fa-eye me-1"></i>{{ number_format($product->view_count) }}<br>
                                                <i class="fas fa-shopping-cart me-1"></i>{{ number_format($product->purchase_count) }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $product->created_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('marketplace.products.show', $product->slug) }}">
                                                            <i class="fas fa-eye me-2"></i>{{ __('common.view') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        @php
                                                            $editRoute = $product->product_type === 'digital'
                                                                ? 'dashboard.marketplace.seller.products.digital.edit'
                                                                : 'dashboard.marketplace.seller.products.physical.edit';
                                                        @endphp
                                                        <a class="dropdown-item" href="{{ route($editRoute, $product) }}">
                                                            <i class="fas fa-edit me-2"></i>{{ __('common.edit') }}
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteProduct({{ $product->id }})">
                                                            <i class="fas fa-trash me-2"></i>{{ __('common.delete') }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="pagination-wrap hstack gap-2 justify-content-center">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open text-muted font-size-48 mb-3"></i>
                            <h5 class="text-muted">{{ __('marketplace.products.no_products') }}</h5>
                            <p class="text-muted mb-4">{{ __('marketplace.products.no_products_description') }}</p>
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="{{ route('dashboard.marketplace.seller.products.digital.create') }}" class="btn btn-primary w-100">
                                                <i class="fas fa-download me-2"></i>
                                                {{ __('marketplace.digital_products.create') }}
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('dashboard.marketplace.seller.products.physical.create') }}" class="btn btn-success w-100">
                                                <i class="fas fa-box me-2"></i>
                                                {{ __('marketplace.physical_products.create') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('marketplace.products.confirm_delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('marketplace.products.confirm_delete_message') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">{{ __('common.delete') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let productToDelete = null;

function deleteProduct(productId) {
    productToDelete = productId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (productToDelete) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/dashboard/marketplace/seller/products/${productToDelete}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
});
</script>
@endpush
