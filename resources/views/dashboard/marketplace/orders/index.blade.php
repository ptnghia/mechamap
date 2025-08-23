@extends('dashboard.layouts.app')

@section('title', __('orders.index.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2">
                        <i class="fas fa-shopping-cart"></i>
                        {{ __('orders.index.heading') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('orders.index.description') }}</p>
                </div>
                <div>
                    <a href="{{ route('marketplace.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i>
                        {{ __('orders.index.browse_products') }}
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ $stats['total'] }}</div>
                                    <div class="small">{{ __('orders.index.total_orders') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ $stats['completed'] }}</div>
                                    <div class="small">{{ __('orders.index.completed') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ $stats['pending'] }}</div>
                                    <div class="small">{{ __('orders.index.pending') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ number_format($stats['total_spent']) }}₫</div>
                                    <div class="small">{{ __('orders.index.total_spent') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.marketplace.orders.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search"
                                       value="{{ $search }}" placeholder="{{ __('orders.index.search_placeholder') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">{{ __('orders.index.all_status') }}</option>
                                <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>{{ __('orders.index.pending') }}</option>
                                <option value="confirmed" {{ $currentStatus == 'confirmed' ? 'selected' : '' }}>{{ __('orders.index.confirmed') }}</option>
                                <option value="processing" {{ $currentStatus == 'processing' ? 'selected' : '' }}>{{ __('orders.index.processing') }}</option>
                                <option value="shipped" {{ $currentStatus == 'shipped' ? 'selected' : '' }}>{{ __('orders.index.shipped') }}</option>
                                <option value="delivered" {{ $currentStatus == 'delivered' ? 'selected' : '' }}>{{ __('orders.index.delivered') }}</option>
                                <option value="completed" {{ $currentStatus == 'completed' ? 'selected' : '' }}>{{ __('orders.index.completed') }}</option>
                                <option value="cancelled" {{ $currentStatus == 'cancelled' ? 'selected' : '' }}>{{ __('orders.index.cancelled') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_from"
                                   value="{{ $dateFrom }}" placeholder="{{ __('orders.index.date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_to"
                                   value="{{ $dateTo }}" placeholder="{{ __('orders.index.date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                    {{ __('orders.index.filter') }}
                                </button>
                                <a href="{{ route('dashboard.marketplace.orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                    {{ __('orders.index.clear') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('orders.index.orders_list') }}</h5>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($orders as $order)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <!-- Order Header -->
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <strong>{{ __('orders.index.order_number') }}:</strong> #{{ $order->order_number }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar"></i>
                                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="h5 mb-1">{{ number_format($order->total_amount) }}₫</div>
                                                    <div class="d-flex gap-2">
                                                        @if($order->status == 'pending')
                                                            <span class="badge bg-warning">{{ __('orders.index.pending') }}</span>
                                                        @elseif($order->status == 'confirmed')
                                                            <span class="badge bg-info">{{ __('orders.index.confirmed') }}</span>
                                                        @elseif($order->status == 'processing')
                                                            <span class="badge bg-primary">{{ __('orders.index.processing') }}</span>
                                                        @elseif($order->status == 'shipped')
                                                            <span class="badge bg-secondary">{{ __('orders.index.shipped') }}</span>
                                                        @elseif($order->status == 'delivered')
                                                            <span class="badge bg-success">{{ __('orders.index.delivered') }}</span>
                                                        @elseif($order->status == 'completed')
                                                            <span class="badge bg-success">{{ __('orders.index.completed') }}</span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="badge bg-danger">{{ __('orders.index.cancelled') }}</span>
                                                        @endif

                                                        @if($order->payment_status == 'paid')
                                                            <span class="badge bg-success">{{ __('orders.index.paid') }}</span>
                                                        @elseif($order->payment_status == 'pending')
                                                            <span class="badge bg-warning">{{ __('orders.index.payment_pending') }}</span>
                                                        @elseif($order->payment_status == 'failed')
                                                            <span class="badge bg-danger">{{ __('orders.index.payment_failed') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Order Items -->
                                            <div class="border rounded p-3 mb-3">
                                                <h6 class="mb-2">{{ __('orders.index.order_items') }}:</h6>
                                                @foreach($order->items as $item)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-medium">{{ $item->product->name }}</div>
                                                            <small class="text-muted">
                                                                {{ __('orders.index.seller') }}: {{ $item->product->seller->user->name ?? 'Unknown' }}
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <div>{{ $item->quantity }} x {{ number_format($item->price) }}₫</div>
                                                            <small class="text-muted">= {{ number_format($item->quantity * $item->price) }}₫</small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Order Summary -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>{{ __('orders.index.payment_method') }}:</strong>
                                                        {{ ucfirst($order->payment_method) }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    @if($order->notes)
                                                        <small class="text-muted">
                                                            <strong>{{ __('orders.index.notes') }}:</strong>
                                                            {{ Str::limit($order->notes, 50) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex-shrink-0 ms-3">
                                            <div class="btn-group-vertical btn-group-sm">
                                                <a href="{{ route('dashboard.marketplace.orders.show', $order->id) }}"
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                    {{ __('orders.index.view') }}
                                                </a>
                                                @if($order->status == 'pending' && $order->payment_status != 'paid')
                                                    <a href="{{ route('marketplace.orders.payment', $order->id) }}"
                                                       class="btn btn-outline-success">
                                                        <i class="fas fa-credit-card"></i>
                                                        {{ __('orders.index.pay_now') }}
                                                    </a>
                                                @endif
                                                @if(in_array($order->status, ['pending', 'confirmed']) && $order->payment_status != 'paid')
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="cancelOrder({{ $order->id }})">
                                                        <i class="fas fa-times"></i>
                                                        {{ __('orders.index.cancel') }}
                                                    </button>
                                                @endif
                                                @if($order->status == 'completed')
                                                    <a href="{{ route('dashboard.marketplace.orders.download', $order->id) }}"
                                                       class="btn btn-outline-info">
                                                        <i class="fas fa-download"></i>
                                                        {{ __('orders.index.download') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($orders->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $orders->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('orders.index.no_orders') }}</h5>
                            <p class="text-muted">{{ __('orders.index.no_orders_desc') }}</p>
                            <a href="{{ route('marketplace.index') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i>
                                {{ __('orders.index.start_shopping') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        @include('dashboard.layouts.sidebar')
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('orders.index.cancel_order') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('orders.index.cancel_confirm') }}</p>
                <div class="mb-3">
                    <label for="cancelReason" class="form-label">{{ __('orders.index.cancel_reason') }}</label>
                    <textarea class="form-control" id="cancelReason" rows="3" placeholder="{{ __('orders.index.cancel_reason_placeholder') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('orders.index.close') }}</button>
                <button type="button" class="btn btn-danger" id="confirmCancelOrder">{{ __('orders.index.confirm_cancel') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let orderToCancel = null;

function cancelOrder(orderId) {
    orderToCancel = orderId;
    const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    modal.show();
}

document.getElementById('confirmCancelOrder').addEventListener('click', function() {
    if (orderToCancel) {
        const reason = document.getElementById('cancelReason').value;

        fetch(`/dashboard/marketplace/orders/${orderToCancel}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error cancelling order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling order');
        });
    }
});
</script>
@endpush
