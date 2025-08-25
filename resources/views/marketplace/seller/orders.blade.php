@extends('layouts.marketplace-dashboard')

@section('title', __('marketplace.my_orders'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('partials.dashboard.marketplace.seller-sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-shopping-cart me-2"></i>
                        {{ __('marketplace.my_orders') }}
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fa-solid fa-filter me-1"></i>
                            {{ __('ui.filter') }}
                        </button>
                        <a href="{{ route('dashboard.marketplace.seller.orders') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-refresh me-1"></i>
                            {{ __('ui.reset') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Summary -->
                    @if($currentStatus || $dateFrom || $dateTo || $search)
                    <div class="alert alert-info">
                        <strong>{{ __('ui.active_filters') }}:</strong>
                        @if($currentStatus)
                            <span class="badge bg-primary me-1">{{ __('marketplace.status') }}: {{ __('marketplace.fulfillment_status.' . $currentStatus) }}</span>
                        @endif
                        @if($dateFrom)
                            <span class="badge bg-primary me-1">{{ __('ui.from') }}: {{ $dateFrom }}</span>
                        @endif
                        @if($dateTo)
                            <span class="badge bg-primary me-1">{{ __('ui.to') }}: {{ $dateTo }}</span>
                        @endif
                        @if($search)
                            <span class="badge bg-primary me-1">{{ __('ui.search') }}: {{ $search }}</span>
                        @endif
                    </div>
                    @endif

                    <!-- Orders Table -->
                    @if($orderItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('marketplace.order_id') }}</th>
                                    <th>{{ __('marketplace.product') }}</th>
                                    <th>{{ __('marketplace.customer') }}</th>
                                    <th>{{ __('marketplace.quantity') }}</th>
                                    <th>{{ __('marketplace.amount') }}</th>
                                    <th>{{ __('marketplace.status') }}</th>
                                    <th>{{ __('marketplace.order_date') }}</th>
                                    <th>{{ __('ui.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $item)
                                <tr>
                                    <td>
                                        <strong>#{{ $item->order->order_number ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->images && count($item->product->images) > 0)
                                                <img src="{{ asset('images/' . $item->product->images[0]) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="rounded me-2"
                                                     width="40" height="40">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-2"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fa-solid fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $item->product->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $item->order->customer->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $item->order->customer->email ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        <strong>{{ number_format($item->total_amount) }} VND</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($item->fulfillment_status) {
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'shipped' => 'primary',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ __('marketplace.fulfillment_status.' . $item->fulfillment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $item->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#orderDetailModal"
                                                    data-order-id="{{ $item->order->id }}"
                                                    data-item-id="{{ $item->id }}">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            @if($item->fulfillment_status === 'pending')
                                            <button class="btn btn-outline-success"
                                                    onclick="updateOrderStatus({{ $item->id }}, 'processing')">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            @endif
                                            @if(in_array($item->fulfillment_status, ['pending', 'processing']))
                                            <button class="btn btn-outline-danger"
                                                    onclick="updateOrderStatus({{ $item->id }}, 'cancelled')">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            {{ __('ui.showing') }} {{ $orderItems->firstItem() ?? 0 }} - {{ $orderItems->lastItem() ?? 0 }}
                            {{ __('ui.of') }} {{ $orderItems->total() }} {{ __('ui.results') }}
                        </div>
                        {{ $orderItems->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fa-solid fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('marketplace.no_orders_found') }}</h5>
                        <p class="text-muted">{{ __('marketplace.no_orders_desc') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('dashboard.marketplace.seller.orders') }}">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('ui.filter_orders') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('marketplace.status') }}</label>
                            <select name="status" class="form-select">
                                <option value="">{{ __('ui.all_statuses') }}</option>
                                <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>
                                    {{ __('marketplace.fulfillment_status.pending') }}
                                </option>
                                <option value="processing" {{ $currentStatus === 'processing' ? 'selected' : '' }}>
                                    {{ __('marketplace.fulfillment_status.processing') }}
                                </option>
                                <option value="shipped" {{ $currentStatus === 'shipped' ? 'selected' : '' }}>
                                    {{ __('marketplace.fulfillment_status.shipped') }}
                                </option>
                                <option value="delivered" {{ $currentStatus === 'delivered' ? 'selected' : '' }}>
                                    {{ __('marketplace.fulfillment_status.delivered') }}
                                </option>
                                <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>
                                    {{ __('marketplace.fulfillment_status.cancelled') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('ui.search') }}</label>
                            <input type="text" name="search" class="form-control"
                                   value="{{ $search }}"
                                   placeholder="{{ __('ui.search_customer_product') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('ui.date_from') }}</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('ui.date_to') }}</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('ui.apply_filter') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('marketplace.order_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="orderDetailContent">
                    <div class="text-center py-3">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">{{ __('ui.loading') }}...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Update order status
function updateOrderStatus(itemId, status) {
    if (!confirm('{{ __("ui.confirm_action") }}')) {
        return;
    }

    fetch(`/dashboard/marketplace/seller/orders/${itemId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '{{ __("ui.error_occurred") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("ui.error_occurred") }}');
    });
}

// Load order details
document.getElementById('orderDetailModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const orderId = button.getAttribute('data-order-id');
    const itemId = button.getAttribute('data-item-id');

    fetch(`/dashboard/marketplace/seller/orders/${itemId}/details`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('orderDetailContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('orderDetailContent').innerHTML =
                '<div class="alert alert-danger">{{ __("ui.error_loading_data") }}</div>';
        });
});
</script>
@endpush
