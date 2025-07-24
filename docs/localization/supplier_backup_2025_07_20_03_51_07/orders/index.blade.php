@extends('layouts.app')

@section('title', 'Quản lý đơn hàng - Supplier Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('supplier.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">📦 Quản lý đơn hàng</h1>
                    <p class="text-muted">Theo dõi và xử lý đơn hàng của bạn</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="exportOrders()">
                        <i class="fas fa-download"></i> Xuất danh sách
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Tổng đơn hàng</h6>
                                    <h4 class="mb-0">{{ number_format($stats['total_orders']) }}</h4>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Chờ xử lý</h6>
                                    <h4 class="mb-0">{{ number_format($stats['pending_orders']) }}</h4>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Đang giao</h6>
                                    <h4 class="mb-0">{{ number_format($stats['shipped_orders']) }}</h4>
                                </div>
                                <div class="text-info">
                                    <i class="fas fa-truck fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">Hoàn thành</h6>
                                    <h4 class="mb-0">{{ number_format($stats['delivered_orders']) }}</h4>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('supplier.orders.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="{{ route('supplier.orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Xóa
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($orderItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th>Khách hàng</th>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Số lượng</th>
                                        <th class="text-end">Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đặt</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderItems as $orderItem)
                                    <tr>
                                        <td>
                                            <strong>#{{ $orderItem->order->order_number }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $orderItem->order->customer->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $orderItem->order->customer->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ Str::limit($orderItem->product_name, 30) }}</strong>
                                                <br>
                                                <small class="text-muted">SKU: {{ $orderItem->product_sku ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ number_format($orderItem->quantity) }}</td>
                                        <td class="text-end">{{ number_format($orderItem->total_amount, 0, ',', '.') }} VND</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Chờ xử lý',
                                                    'processing' => 'Đang xử lý',
                                                    'shipped' => 'Đã giao',
                                                    'delivered' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$orderItem->fulfillment_status] ?? 'secondary' }}">
                                                {{ $statusLabels[$orderItem->fulfillment_status] ?? $orderItem->fulfillment_status }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $orderItem->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('supplier.orders.show', $orderItem->id) }}">
                                                            <i class="fas fa-eye"></i> Xem chi tiết
                                                        </a>
                                                    </li>
                                                    @if($orderItem->fulfillment_status == 'pending')
                                                    <li>
                                                        <button class="dropdown-item" onclick="updateOrderStatus({{ $orderItem->id }}, 'processing')">
                                                            <i class="fas fa-play"></i> Bắt đầu xử lý
                                                        </button>
                                                    </li>
                                                    @endif
                                                    @if($orderItem->fulfillment_status == 'processing')
                                                    <li>
                                                        <button class="dropdown-item" onclick="updateOrderStatus({{ $orderItem->id }}, 'shipped')">
                                                            <i class="fas fa-truck"></i> Đánh dấu đã giao
                                                        </button>
                                                    </li>
                                                    @endif
                                                    @if(in_array($orderItem->fulfillment_status, ['pending', 'processing']))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="updateOrderStatus({{ $orderItem->id }}, 'cancelled')">
                                                            <i class="fas fa-times"></i> Hủy đơn hàng
                                                        </button>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <small class="text-muted">
                                    Hiển thị {{ $orderItems->firstItem() }} - {{ $orderItems->lastItem() }}
                                    trong tổng số {{ number_format($orderItems->total()) }} đơn hàng
                                </small>
                            </div>
                            <div>
                                {{ $orderItems->withQueryString()->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có đơn hàng nào</h5>
                            <p class="text-muted">Đơn hàng của bạn sẽ xuất hiện ở đây khi có khách hàng mua sản phẩm.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateOrderStatus(orderItemId, status) {
    if (confirm('Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này?')) {
        fetch(`/supplier/orders/${orderItemId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ fulfillment_status: status })
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.');
        });
    }
}

function exportOrders() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/supplier/orders/export?${params.toString()}`;
}
</script>
@endpush
@endsection
