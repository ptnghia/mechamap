@extends('admin.layouts.dason')

@section('title', 'Quản Lý Đơn Hàng')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản Lý Đơn Hàng</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketplace.index') }}">Marketplace</a></li>
                    <li class="breadcrumb-item active">Đơn Hàng</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Tổng Đơn Hàng</p>
                                <h4 class="mb-0">{{ $stats['total_orders'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i data-feather="shopping-cart"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Chờ Xử Lý</p>
                                <h4 class="mb-0">{{ $stats['pending_orders'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Hoàn Thành</p>
                                <h4 class="mb-0">{{ $stats['completed_orders'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Doanh Thu</p>
                                <h4 class="mb-0">{{ number_format($stats['total_revenue'] ?? 0) }} VND</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i data-feather="dollar-sign"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Danh Sách Đơn Hàng</h4>
                        <div class="card-title-desc">Quản lý tất cả đơn hàng trong marketplace</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm" onclick="exportOrders()">
                                <i class="fas fa-download" class="me-1"></i> Xuất Excel
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="refreshOrders()">
                                <i class="fas fa-sync" class="me-1"></i> Làm Mới
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tìm mã đơn hàng, email...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Đã gửi hàng</option>
                                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="payment_status">
                                <option value="">Trạng thái thanh toán</option>
                                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Thất bại</option>
                                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="Từ ngày">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="Đến ngày">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Orders Table -->
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã Đơn Hàng</th>
                                <th>Khách Hàng</th>
                                <th>Sản Phẩm</th>
                                <th>Tổng Tiền</th>
                                <th>Trạng Thái</th>
                                <th>Thanh Toán</th>
                                <th>Ngày Đặt</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders ?? [] as $order)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $order->order_number }}</h6>
                                            <p class="text-muted mb-0 small">{{ $order->order_type }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $order->customer->name ?? 'N/A' }}</h6>
                                            <p class="text-muted mb-0 small">{{ $order->customer_email }}</p>
                                            @if($order->customer_phone)
                                                <p class="text-muted mb-0 small">{{ $order->customer_phone }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="badge bg-info">{{ $order->items->count() }} sản phẩm</span>
                                            @if($order->items->count() > 0)
                                                <p class="text-muted mb-0 small mt-1">{{ $order->items->first()->product_name }}</p>
                                                @if($order->items->count() > 1)
                                                    <p class="text-muted mb-0 small">và {{ $order->items->count() - 1 }} sản phẩm khác</p>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ number_format($order->total_amount) }} {{ $order->currency }}</h6>
                                            @if($order->discount_amount > 0)
                                                <p class="text-success mb-0 small">Giảm: {{ number_format($order->discount_amount) }} {{ $order->currency }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'processing' => 'primary',
                                                'shipped' => 'secondary',
                                                'delivered' => 'success',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                'refunded' => 'dark'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $paymentColors = [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'paid' => 'success',
                                                'failed' => 'danger',
                                                'refunded' => 'dark',
                                                'partially_refunded' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $paymentColors[$order->payment_status] ?? 'secondary' }}">
                                            {{ $order->payment_status_label }}
                                        </span>
                                        @if($order->payment_method)
                                            <p class="text-muted mb-0 small mt-1">{{ $order->payment_method }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.marketplace.orders.show', $order) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($order->canBeCancelled())
                                                <button type="button" class="btn btn-outline-danger" onclick="cancelOrder({{ $order->id }})" title="Hủy đơn">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-info" onclick="generateInvoice({{ $order->id }})" title="Tạo hóa đơn">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i data-feather="shopping-cart" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                                            <h5 class="text-muted">Chưa có đơn hàng nào</h5>
                                            <p class="text-muted mb-0">Đơn hàng sẽ xuất hiện ở đây khi có khách hàng đặt mua</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($orders) && $orders->hasPages())
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">
                                    Hiển thị {{ $orders->firstItem() }} đến {{ $orders->lastItem() }}
                                    của {{ $orders->total() }} đơn hàng
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Export orders
function exportOrders() {
    alert('Chức năng xuất Excel sẽ được triển khai');
}

// Refresh orders
function refreshOrders() {
    window.location.reload();
}

// Cancel order
function cancelOrder(orderId) {
    const reason = prompt('Lý do hủy đơn hàng:');
    if (reason && confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
        // TODO: Implement cancel order
        alert('Chức năng hủy đơn hàng sẽ được triển khai');
    }
}

// Generate invoice
function generateInvoice(orderId) {
    alert('Chức năng tạo hóa đơn sẽ được triển khai');
}

// Initialize Feather Icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        try {
            feather.replace();
        } catch (error) {
            console.warn('Feather Icons error in orders page:', error);
        }
    }
});
</script>
@endsection
