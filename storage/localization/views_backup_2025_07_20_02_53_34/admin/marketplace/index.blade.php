@extends('admin.layouts.dason')

@section('title', 'Tổng Quan Marketplace')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tổng Quan Marketplace</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Marketplace</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Key Metrics -->
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Tổng Sản Phẩm</p>
                        <h4 class="mb-0">0</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i data-feather="package"></i>
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
                        <p class="text-muted fw-medium">Tổng Đơn Hàng</p>
                        <h4 class="mb-0">0</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
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
                        <p class="text-muted fw-medium">Nhà Bán Hàng</p>
                        <h4 class="mb-0">0</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i class="fas fa-users"></i>
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
                        <h4 class="mb-0">0 VND</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
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

<div class="row">
    <!-- Quick Actions -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Thao Tác Nhanh</h4>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.marketplace.products.index') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-primary rounded-circle">
                                    <i data-feather="package"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Quản Lý Sản Phẩm</h6>
                                <p class="text-muted mb-0 small">Xem và quản lý tất cả sản phẩm</p>
                            </div>
                            <div>
                                <i data-feather="chevron-right" class="text-muted"></i>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.marketplace.orders.index') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-success rounded-circle">
                                    <i data-feather="shopping-cart"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Quản Lý Đơn Hàng</h6>
                                <p class="text-muted mb-0 small">Xử lý và theo dõi đơn hàng</p>
                            </div>
                            <div>
                                <i data-feather="chevron-right" class="text-muted"></i>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.marketplace.sellers.index') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-info rounded-circle">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Quản Lý Nhà Bán Hàng</h6>
                                <p class="text-muted mb-0 small">Xác minh và quản lý nhà bán hàng</p>
                            </div>
                            <div>
                                <i data-feather="chevron-right" class="text-muted"></i>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.payments.index') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-warning rounded-circle">
                                    <i data-feather="credit-card"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Quản Lý Thanh Toán</h6>
                                <p class="text-muted mb-0 small">Theo dõi giao dịch thanh toán</p>
                            </div>
                            <div>
                                <i data-feather="chevron-right" class="text-muted"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Hoạt Động Gần Đây</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle" class="me-2"></i>
                    Marketplace đang được phát triển. Các tính năng sẽ sớm ra mắt!
                </div>

                <!-- Sample Activities -->
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-success rounded-circle">
                                    <i class="fas fa-plus-circle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Sản phẩm mới được thêm</h6>
                                <p class="text-muted mb-1">Máy tiện CNC XYZ-2000 đã được thêm bởi Công ty ABC</p>
                                <small class="text-muted">2 giờ trước</small>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-primary rounded-circle">
                                    <i data-feather="shopping-cart"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Đơn hàng mới</h6>
                                <p class="text-muted mb-1">Đơn hàng #MO-20241225-ABC123 đã được đặt</p>
                                <small class="text-muted">5 giờ trước</small>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-warning rounded-circle">
                                    <i data-feather="user-check"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Nhà bán hàng được xác minh</h6>
                                <p class="text-muted mb-1">Công ty Cơ Khí DEF đã được xác minh thành công</p>
                                <small class="text-muted">1 ngày trước</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pending Approvals -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Chờ Phê Duyệt</h4>
                    <a href="{{ route('admin.marketplace.products.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">
                        Xem Tất Cả
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="fas fa-clock" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                    <h5 class="text-muted">Không có mục nào chờ phê duyệt</h5>
                    <p class="text-muted mb-0">Tất cả sản phẩm và nhà bán hàng đã được xử lý</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Sellers -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Nhà Bán Hàng Hàng Đầu</h4>
                    <a href="{{ route('admin.marketplace.sellers.index') }}" class="btn btn-sm btn-outline-primary">
                        Xem Tất Cả
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i data-feather="award" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                    <h5 class="text-muted">Chưa có dữ liệu</h5>
                    <p class="text-muted mb-0">Thống kê nhà bán hàng sẽ xuất hiện khi có giao dịch</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Biểu Đồ Doanh Thu</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i data-feather="bar-chart-2" class="me-2"></i>
                    Biểu đồ doanh thu sẽ được hiển thị khi có dữ liệu giao dịch
                </div>
                <div id="revenue-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- ApexCharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Initialize Revenue Chart (placeholder)
    var options = {
        series: [{
            name: 'Doanh Thu',
            data: [0, 0, 0, 0, 0, 0, 0]
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return val.toLocaleString() + ' VND';
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        },
        colors: ['#556ee6'],
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toLocaleString() + ' VND';
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
    chart.render();
});
</script>
@endsection
