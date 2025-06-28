@extends('admin.layouts.dason')

@section('title', 'Quản lý Đơn hàng Marketplace')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản lý Đơn hàng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Marketplace</a></li>
                        <li class="breadcrumb-item active">Đơn hàng</li>
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
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng đơn hàng</p>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-receipt font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Chờ xử lý</p>
                            <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-time font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Hoàn thành</p>
                            <h4 class="mb-0">{{ $stats['completed'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Doanh thu</p>
                            <h4 class="mb-0">${{ number_format($stats['revenue'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-dollar font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Danh sách đơn hàng</h4>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary" onclick="exportOrders()">
                                <i class="bx bx-export me-1"></i> Xuất Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang xử lý</option>
                                <option value="shipped">Đã gửi</option>
                                <option value="delivered">Đã giao</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFrom" placeholder="Từ ngày">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateTo" placeholder="Đến ngày">
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm đơn hàng..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0" id="ordersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders ?? [] as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.marketplace.orders.show', $order) }}" class="text-body fw-bold">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $order->customer->name ?? 'N/A' }}</h6>
                                            <p class="text-muted mb-0">{{ $order->customer->email ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ $order->items_count ?? 0 }} sản phẩm</span>
                                            @if($order->items && $order->items->count() > 0)
                                                <p class="text-muted mb-0">{{ $order->items->first()->product->name ?? 'N/A' }}
                                                @if($order->items->count() > 1)
                                                    và {{ $order->items->count() - 1 }} sản phẩm khác
                                                @endif
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">${{ number_format($order->total_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Chờ xử lý</span>
                                                @break
                                            @case('processing')
                                                <span class="badge bg-info">Đang xử lý</span>
                                                @break
                                            @case('shipped')
                                                <span class="badge bg-primary">Đã gửi</span>
                                                @break
                                            @case('delivered')
                                                <span class="badge bg-success">Đã giao</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Đã hủy</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.marketplace.orders.show', $order) }}">
                                                    <i class="mdi mdi-eye font-size-16 text-success me-1"></i> Xem chi tiết
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.marketplace.orders.edit', $order) }}">
                                                    <i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Cập nhật
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="#" onclick="printInvoice({{ $order->id }})">
                                                    <i class="mdi mdi-printer font-size-16 text-info me-1"></i> In hóa đơn
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-receipt font-size-48 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có đơn hàng nào</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($orders) && $orders->hasPages())
                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="dataTables_info">
                                Hiển thị {{ $orders->firstItem() }} đến {{ $orders->lastItem() }}
                                trong tổng số {{ $orders->total() }} đơn hàng
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        // Implement search logic
    });

    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    document.getElementById('dateFrom').addEventListener('change', function() {
        // Implement date filter logic
    });

    document.getElementById('dateTo').addEventListener('change', function() {
        // Implement date filter logic
    });
});

function exportOrders() {
    // Implement export logic
    console.log('Export orders');
}

function printInvoice(orderId) {
    // Implement print invoice logic
    console.log('Print invoice for order:', orderId);
}
</script>
@endsection
