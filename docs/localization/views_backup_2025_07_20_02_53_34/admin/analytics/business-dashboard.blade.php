@extends('admin.layouts.dason')

@section('title', 'Business Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">📊 Business Analytics Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Analytics</a></li>
                        <li class="breadcrumb-item active">Business Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Range Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 me-3">📅 Thời gian phân tích:</h5>
                        <div class="btn-group" role="group">
                            <a href="?range=7" class="btn btn-outline-primary {{ $timeRange == 7 ? 'active' : '' }}">7 ngày</a>
                            <a href="?range=30" class="btn btn-outline-primary {{ $timeRange == 30 ? 'active' : '' }}">30 ngày</a>
                            <a href="?range=90" class="btn btn-outline-primary {{ $timeRange == 90 ? 'active' : '' }}">90 ngày</a>
                            <a href="?range=365" class="btn btn-outline-primary {{ $timeRange == 365 ? 'active' : '' }}">1 năm</a>
                        </div>
                        <div class="ms-auto">
                            <span class="badge badge-soft-info">
                                <i class="fas fa-user me-1"></i>{{ ucfirst($analytics['user_role']) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Permissions Info -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">🔐 Quyền truy cập của bạn</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($analytics['permissions'] as $permission)
                            <span class="badge badge-soft-success">
                                <i class="fas fa-check me-1"></i>{{ $permission }}
                            </span>
                        @empty
                            <span class="text-muted">Không có quyền đặc biệt</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($analytics['system']))
    <!-- System Management Analytics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">🏢 Quản Lý Hệ Thống</h5>
        </div>
        
        <!-- System Stats Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng Người Dùng</p>
                            <h4 class="mb-0">{{ number_format($analytics['system']['total_users']) }}</h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-users font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Người Dùng Mới</p>
                            <h4 class="mb-0">{{ number_format($analytics['system']['new_users']) }}</h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-user-plus font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Người Dùng Hoạt Động</p>
                            <h4 class="mb-0">{{ number_format($analytics['system']['active_users']) }}</h4>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-user-check font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Tăng Trưởng</p>
                            <h4 class="mb-0">
                                @if(isset($analytics['system']['user_growth']['growth_rate']))
                                    {{ number_format($analytics['system']['user_growth']['growth_rate'], 1) }}%
                                @else
                                    N/A
                                @endif
                            </h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-chart-line font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($analytics['marketplace']))
    <!-- Marketplace Analytics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">🛒 Phân Tích Marketplace</h5>
        </div>
        
        <!-- Marketplace Stats Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng Sản Phẩm</p>
                            <h4 class="mb-0">{{ number_format($analytics['marketplace']['total_products']) }}</h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-box font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Sản Phẩm Hoạt Động</p>
                            <h4 class="mb-0">{{ number_format($analytics['marketplace']['active_products']) }}</h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Đơn Hàng</p>
                            <h4 class="mb-0">{{ number_format($analytics['marketplace']['total_orders']) }}</h4>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-shopping-cart font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Doanh Thu</p>
                            <h4 class="mb-0">{{ number_format($analytics['marketplace']['total_revenue']) }} VND</h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-dollar-sign font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($analytics['business']))
    <!-- Business Partner Analytics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">🤝 Hiệu Suất Kinh Doanh</h5>
        </div>
        
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">📈 Thống Kê Cá Nhân</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-muted">Tổng Doanh Số</h5>
                                <h3 class="text-primary">
                                    {{ number_format($analytics['business']['total_sales'] ?? 0) }}
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-muted">Tổng Thu Nhập</h5>
                                <h3 class="text-success">
                                    {{ number_format($analytics['business']['total_earnings'] ?? 0) }} VND
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-muted">Đánh Giá Trung Bình</h5>
                                <h3 class="text-warning">
                                    {{ number_format($analytics['business']['avg_rating'] ?? 0, 1) }}/5
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- No Data Message -->
    @if(!isset($analytics['system']) && !isset($analytics['marketplace']) && !isset($analytics['business']))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chart-bar font-size-48 text-muted mb-3"></i>
                    <h4 class="text-muted">Không có dữ liệu analytics</h4>
                    <p class="text-muted">
                        Tài khoản của bạn chưa có quyền truy cập vào các phân tích kinh doanh.<br>
                        Vui lòng liên hệ quản trị viên để được cấp quyền.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">⚡ Hành Động Nhanh</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @if(isset($analytics['marketplace']))
                            <a href="{{ route('admin.analytics.marketplace') }}" class="btn btn-outline-primary">
                                <i class="fas fa-store me-1"></i>Chi Tiết Marketplace
                            </a>
                        @endif
                        
                        @if(isset($analytics['system']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-users me-1"></i>Quản Lý Người Dùng
                            </a>
                        @endif
                        
                        <a href="{{ route('admin.analytics.export') }}" class="btn btn-outline-info">
                            <i class="fas fa-download me-1"></i>Xuất Báo Cáo
                        </a>
                        
                        <a href="{{ route('admin.analytics.realtime') }}" class="btn btn-outline-warning">
                            <i class="fas fa-chart-line me-1"></i>Real-time Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto refresh every 5 minutes
    setTimeout(function() {
        window.location.reload();
    }, 300000);
    
    // Add tooltips to cards
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
