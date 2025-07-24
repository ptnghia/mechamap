@extends('layouts.app')

@section('title', 'Bảng điều khiển Nhà sản xuất')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Bảng điều khiển Nhà sản xuất</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng thiết kế</p>
                            <h4 class="mb-0">{{ number_format($stats['total_designs']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-cube font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Tổng lượt tải</p>
                            <h4 class="mb-0">{{ number_format($stats['total_downloads']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-download font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Doanh thu tháng</p>
                            <h4 class="mb-0">${{ number_format($stats['month_revenue'], 2) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-dollar font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Đơn hàng hoàn thành</p>
                            <h4 class="mb-0">{{ number_format($stats['completed_orders']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Top Designs -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Đơn hàng gần đây</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Thiết kế</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $orderItem)
                                <tr>
                                    <td>#{{ $orderItem->order->order_number }}</td>
                                    <td>{{ $orderItem->order->customer->name }}</td>
                                    <td>{{ $orderItem->product_name }}</td>
                                    <td>${{ number_format($orderItem->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ $orderItem->fulfillment_status === 'downloaded' ? 'success' : ($orderItem->fulfillment_status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($orderItem->fulfillment_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $orderItem->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có đơn hàng nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Designs -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thiết kế phổ biến</h4>
                </div>
                <div class="card-body">
                    @forelse($topProducts as $product)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-1">
                            <h6 class="mb-1">{{ $product->product_name }}</h6>
                            <p class="text-muted mb-0">Đã tải: {{ $product->total_sold }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="text-success">${{ number_format($product->total_revenue, 2) }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">Chưa có dữ liệu bán hàng</p>
                    @endforelse
                </div>
            </div>

            <!-- File Format Distribution -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Định dạng file phổ biến</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-primary">CAD</h5>
                                <p class="text-muted mb-0">DWG, STEP</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-info">3D</h5>
                                <p class="text-muted mb-0">STL, OBJ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thao tác nhanh</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('manufacturer.designs.index') }}" class="btn btn-primary btn-block">
                                <i class="bx bx-cube me-2"></i>Quản lý thiết kế
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('manufacturer.orders.index') }}" class="btn btn-warning btn-block">
                                <i class="bx bx-download me-2"></i>Quản lý đơn hàng
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('manufacturer.analytics.index') }}" class="btn btn-info btn-block">
                                <i class="bx bx-bar-chart me-2"></i>Báo cáo & Thống kê
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('manufacturer.settings.index') }}" class="btn btn-secondary btn-block">
                                <i class="bx bx-cog me-2"></i>Cài đặt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Standards Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Tiêu chuẩn kỹ thuật</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bx bx-certification text-primary font-size-24"></i>
                                <h6 class="mt-2">ISO Standards</h6>
                                <p class="text-muted">Tuân thủ tiêu chuẩn quốc tế</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bx bx-shield-check text-success font-size-24"></i>
                                <h6 class="mt-2">Quality Assurance</h6>
                                <p class="text-muted">Đảm bảo chất lượng thiết kế</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bx bx-file-find text-info font-size-24"></i>
                                <h6 class="mt-2">Documentation</h6>
                                <p class="text-muted">Tài liệu kỹ thuật đầy đủ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.mini-stat-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-block {
    width: 100%;
    margin-bottom: 10px;
}

.badge-soft-success {
    background-color: rgba(52, 195, 143, 0.1);
    color: #34c38f;
}

.badge-soft-warning {
    background-color: rgba(248, 183, 77, 0.1);
    color: #f8b74f;
}

.badge-soft-secondary {
    background-color: rgba(116, 120, 141, 0.1);
    color: #74788d;
}
</style>
@endpush
