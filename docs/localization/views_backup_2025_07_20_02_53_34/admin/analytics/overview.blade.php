@extends('admin.layouts.dason')

@section('title', 'Tổng Quan Phân Tích')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tổng Quan Phân Tích</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Phân Tích</li>
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
                        <p class="text-muted fw-medium">Tổng Người Dùng</p>
                        <h4 class="mb-0">1,234</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="fas fa-user-group font-size-24"></i>
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
                        <p class="text-muted fw-medium">Bài Đăng</p>
                        <h4 class="mb-0">567</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                            <span class="avatar-title">
                                <i class="mdi mdi-post font-size-24"></i>
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
                        <p class="text-muted fw-medium">Sản Phẩm</p>
                        <h4 class="mb-0">89</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i class="mdi mdi-package-variant font-size-24"></i>
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
                        <h4 class="mb-0">$12,345</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                            <span class="avatar-title">
                                <i class="mdi mdi-currency-usd font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Traffic Chart -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Lưu Lượng Truy Cập</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle-outline me-2"></i>
                    Biểu đồ phân tích đang được phát triển. Sẽ sớm ra mắt!
                </div>
                <div id="traffic-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Top Pages -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Trang Phổ Biến</h4>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Trang Chủ</h6>
                            <small class="text-muted">/</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">2,345</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Diễn Đàn</h6>
                            <small class="text-muted">/forum</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">1,234</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Marketplace</h6>
                            <small class="text-muted">/marketplace</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">987</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Showcase</h6>
                            <small class="text-muted">/showcase</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">654</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- User Activity -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Hoạt Động Người Dùng</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle-outline me-2"></i>
                    Biểu đồ hoạt động đang được phát triển.
                </div>
                <div id="user-activity-chart" style="height: 250px;"></div>
            </div>
        </div>
    </div>

    <!-- Device Stats -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Thiết Bị Truy Cập</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <h5 class="font-size-20">65%</h5>
                            <p class="text-muted">Desktop</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h5 class="font-size-20">35%</h5>
                            <p class="text-muted">Mobile</p>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle-outline me-2"></i>
                    Biểu đồ thiết bị đang được phát triển.
                </div>
                <div id="device-chart" style="height: 200px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- ApexCharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
// Placeholder for charts - will be implemented with real data
document.addEventListener('DOMContentLoaded', function() {
    console.log('Analytics charts will be implemented here');
});
</script>
@endsection
