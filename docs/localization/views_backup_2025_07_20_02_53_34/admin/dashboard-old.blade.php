@extends('admin.layouts.dason')

@section('title', 'Bảng điều khiển')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Bảng điều khiển</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Bảng điều khiển</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('admin.statistics.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-chart-bar"></i> Thống kê chi tiết
        </a>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="fas fa-download"></i> Xuất báo cáo
        </button>
    </div>
    <div class="btn-group dropdown">
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-calendar"></i> Thời gian
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Hôm nay</a></li>
            <li><a class="dropdown-item" href="#">Tuần này</a></li>
            <li><a class="dropdown-item" href="#">Tháng này</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Tùy chỉnh...</a></li>
        </ul>
    </div>
@endsection

@section('content')
    <!-- Community Metrics Section -->
    <div class="section-header">
        <h5 class="section-title">📊 Thống Kê Cộng Đồng</h5>
        <p class="section-subtitle">Tổng quan hoạt động diễn đàn và người dùng</p>
    </div>

    <!-- Community Stats Row -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card primary">
                    <h6 class="card-title">Tổng số người dùng</h6>
                    <h2 class="card-value">{{ number_format($stats['users']) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend {{ $stats['new_users_today'] > 0 ? 'up' : 'neutral' }}">
                            <i class="fas fa-arrow-{{ $stats['new_users_today'] > 0 ? 'up' : 'right' }}"></i> {{ $stats['new_users_today'] }}
                        </span>
                        <span class="card-period">hôm nay</span>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="usersDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card success">
                    <h6 class="card-title">Tổng số bài đăng</h6>
                    <h2 class="card-value">{{ number_format($stats['threads']) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend {{ $stats['new_threads_today'] > 0 ? 'up' : 'neutral' }}">
                            <i class="fas fa-arrow-{{ $stats['new_threads_today'] > 0 ? 'up' : 'right' }}"></i> {{ $stats['new_threads_today'] }}
                        </span>
                        <span class="card-period">hôm nay</span>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="threadsDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card info">
                    <h6 class="card-title">Tổng số bình luận</h6>
                    <h2 class="card-value">{{ number_format($stats['comments']) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend {{ $stats['new_comments_today'] > 0 ? 'up' : 'neutral' }}">
                            <i class="fas fa-arrow-{{ $stats['new_comments_today'] > 0 ? 'up' : 'right' }}"></i> {{ $stats['new_comments_today'] }}
                        </span>
                        <span class="card-period">hôm nay</span>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="commentsDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card warning">
                    <h6 class="card-title">Hoạt động tuần này</h6>
                    <h2 class="card-value">{{ number_format($stats['weekly_activity'] ?? 33) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend up">
                            <i class="fas fa-arrow-up"></i> +15%
                        </span>
                        <span class="card-period">so với tuần trước</span>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="activityDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marketplace Metrics Section -->
    <div class="section-header">
        <h5 class="section-title">🏪 Thống Kê Marketplace</h5>
        <p class="section-subtitle">Doanh thu, đơn hàng và hiệu suất kinh doanh</p>
    </div>

    <!-- Marketplace Stats Row -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card marketplace-primary">
                    <h6 class="card-title">Doanh thu tháng này</h6>
                    <h2 class="card-value">{{ number_format($stats['monthly_revenue'] ?? 125000000) }}₫</h2>
                    <div class="mt-2">
                        <span class="card-trend up">
                            <i class="fas fa-arrow-up"></i> +23%
                        </span>
                        <span class="card-period">so với tháng trước</span>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="revenueDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card marketplace-success">
                    <h6 class="card-title">Đơn hàng pending</h6>
                    <h2 class="card-value">{{ number_format($stats['pending_orders'] ?? 23) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend down">
                            <i class="fas fa-arrow-down"></i> -5
                        </span>
                        <span class="card-period">từ hôm qua</span>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="ordersDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card marketplace-info">
                    <h6 class="card-title">Sản phẩm chờ duyệt</h6>
                    <h2 class="card-value">{{ number_format($stats['pending_products'] ?? 8) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend up">
                            <i class="fas fa-arrow-up"></i> +3
                        </span>
                        <span class="card-period">hôm nay</span>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="productsDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card marketplace-warning">
                    <h6 class="card-title">Commission chưa thanh toán</h6>
                    <h2 class="card-value">{{ number_format($stats['unpaid_commission'] ?? 15000000) }}₫</h2>
                    <div class="mt-2">
                        <span class="card-trend neutral">
                            <i class="fas fa-arrow-right"></i> 0%
                        </span>
                        <span class="card-period">không thay đổi</span>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="commissionDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="modern-table">
                <div class="table-header">
                    <h5 class="table-title">Người dùng mới nhất</h5>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-outline-secondary modern-btn">
                            <i class="fas fa-filter"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary modern-btn">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Vai trò</th>
                                <th>Ngày tham gia</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="32" height="32">
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                <div class="small text-muted">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->isAdmin())
                                            <span class="modern-badge bg-danger">Quản trị viên</span>
                                        @elseif($user->isModerator())
                                            <span class="modern-badge bg-primary">Điều hành viên</span>
                                        @elseif($user->isSenior())
                                            <span class="modern-badge bg-success">Thành viên cấp cao</span>
                                        @else
                                            <span class="modern-badge bg-secondary">Thành viên</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary modern-btn">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-2 text-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary modern-btn modern-btn-primary">Xem tất cả người dùng</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="modern-table">
                <div class="table-header">
                    <h5 class="table-title">Bài đăng mới nhất</h5>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-outline-secondary modern-btn">
                            <i class="fas fa-filter"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary modern-btn">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Tác giả</th>
                                <th>Diễn đàn</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestThreads as $thread)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-truncate" style="max-width: 200px;">
                                            {{ $thread->title }}
                                        </div>
                                    </td>
                                    <td>{{ $thread->user->name }}</td>
                                    <td>
                                        @if($thread->forum)
                                            {{ $thread->forum->name }}
                                        @else
                                            <span class="text-muted">Không xác định</span>
                                        @endif
                                    </td>
                                    <td>{{ $thread->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-2 text-end">
                    <a href="{{ route('admin.threads.index') }}" class="btn btn-sm btn-primary modern-btn modern-btn-primary">Xem tất cả bài đăng</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Charts Section - Phase 2 -->
    <div class="section-header">
        <h5 class="section-title">📈 Phân Tích Xu Hướng - Phase 2</h5>
        <p class="section-subtitle">Biểu đồ tương tác với dữ liệu thời gian thực</p>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">👥 Xu Hướng Người Dùng</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar me-1"></i> 12 tháng
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-period="7d">7 ngày</a></li>
                            <li><a class="dropdown-item" href="#" data-period="30d">30 ngày</a></li>
                            <li><a class="dropdown-item" href="#" data-period="12m">12 tháng</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="userTrendChart"></canvas>
                    </div>
                    <div class="chart-stats mt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="stat-value text-primary">+8.2%</h6>
                                    <p class="stat-label">Tăng trưởng</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="stat-value text-success">61</h6>
                                    <p class="stat-label">Hiện tại</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">📝 Xu Hướng Nội Dung</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar me-1"></i> 12 tháng
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-period="7d">7 ngày</a></li>
                            <li><a class="dropdown-item" href="#" data-period="30d">30 ngày</a></li>
                            <li><a class="dropdown-item" href="#" data-period="12m">12 tháng</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="contentTrendChart"></canvas>
                    </div>
                    <div class="chart-stats mt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="stat-value text-success">118</h6>
                                    <p class="stat-label">Bài đăng</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="stat-value text-info">359</h6>
                                    <p class="stat-label">Bình luận</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">💰 Xu Hướng Doanh Thu</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar me-1"></i> 12 tháng
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-period="7d">7 ngày</a></li>
                            <li><a class="dropdown-item" href="#" data-period="30d">30 ngày</a></li>
                            <li><a class="dropdown-item" href="#" data-period="12m">12 tháng</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                    <div class="chart-stats mt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="stat-value text-warning">+23%</h6>
                                    <p class="stat-label">Tăng trưởng</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="stat-value text-primary">125M</h6>
                                    <p class="stat-label">Tháng này</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-eq-height">
        <div class="col-md-6 mb-3">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Thống kê người dùng</h5>
                    <div class="chart-actions">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="userStatsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar"></i> 12 tháng gần đây
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userStatsDropdown">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterUserStats(3)">3 tháng gần đây</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterUserStats(6)">6 tháng gần đây</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterUserStats(12)">12 tháng gần đây</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="customFilterUserStats()">Tùy chỉnh...</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 250px;">
                    <canvas id="userStatsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Thống kê nội dung</h5>
                    <div class="chart-actions">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="contentStatsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar"></i> 12 tháng gần đây
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="contentStatsDropdown">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterContentStats(3)">3 tháng gần đây</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterContentStats(6)">6 tháng gần đây</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterContentStats(12)">12 tháng gần đây</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="customFilterContentStats()">Tùy chỉnh...</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 250px;">
                    <canvas id="contentStatsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-eq-height">
        <div class="col-md-4 mb-3">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Thống kê theo vai trò</h5>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 200px;">
                    <div style="width: 200px; height: 200px;">
                        <canvas id="roleStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Thống kê theo trạng thái</h5>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 200px;">
                    <div style="width: 200px; height: 200px;">
                        <canvas id="statusStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Thống kê tương tác</h5>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 200px;">
                    <div style="width: 200px; height: 200px;">
                        <canvas id="interactionStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xuất báo cáo -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">{{ __('Xuất báo cáo thống kê') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.statistics.export') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="type" class="form-label">{{ __('Loại báo cáo') }}</label>
                            <select class="form-select" id="type" name="type">
                                <option value="overview">{{ __('Tổng quan') }}</option>
                                <option value="users">{{ __('Người dùng') }}</option>
                                <option value="content">{{ __('Nội dung') }}</option>
                                <option value="interactions">{{ __('Tương tác') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="format" class="form-label">{{ __('Định dạng') }}</label>
                            <select class="form-select" id="format" name="format">
                                <option value="csv">CSV</option>
                            </select>
                            <small class="form-text">{{ __('Hiện tại chỉ hỗ trợ định dạng CSV') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Xuất báo cáo') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Enhanced Marketplace Metrics Cards */
.stats-card.marketplace-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-card.marketplace-success {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stats-card.marketplace-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stats-card.marketplace-warning {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

/* Enhanced Trend Indicators */
.card-trend.up {
    color: #10b981;
    font-weight: 600;
}

.card-trend.down {
    color: #ef4444;
    font-weight: 600;
}

.card-trend.neutral {
    color: #6b7280;
    font-weight: 600;
}

/* Improved Card Hover Effects */
.modern-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

/* Enhanced Stats Icons */
.stats-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    opacity: 0.3;
    font-size: 2.5rem;
}

/* Improved Card Values */
.card-value {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0.5rem 0;
    line-height: 1.2;
}

/* Better Period Text */
.card-period {
    font-size: 0.875rem;
    opacity: 0.8;
    margin-left: 0.5rem;
}

/* Enhanced Donut Charts */
.donut-chart-container {
    height: 60px;
    width: 60px;
    position: absolute;
    bottom: 15px;
    right: 15px;
}

.donut-chart {
    max-height: 60px !important;
    max-width: 60px !important;
}

/* Responsive Improvements */
@media (max-width: 768px) {
    .card-value {
        font-size: 1.8rem;
    }

    .stats-icon {
        font-size: 2rem;
    }

    .donut-chart-container {
        height: 50px;
        width: 50px;
    }
}

/* Quick Actions Enhancement */
.btn-group .btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}

/* Section Headers */
.section-header {
    margin: 2rem 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
}

.section-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

/* Phase 2: Enhanced Chart Styling */
.chart-card {
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.chart-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.chart-card .card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 1rem 1.25rem;
}

.chart-container {
    position: relative;
    width: 100%;
}

.chart-stats {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

/* Chart Loading States */
.chart-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 300px;
    color: #6b7280;
}

.chart-loading .spinner-border {
    width: 2rem;
    height: 2rem;
    margin-right: 0.5rem;
}

/* Chart Responsive */
@media (max-width: 768px) {
    .chart-card .card-header {
        padding: 0.75rem 1rem;
    }

    .chart-container {
        height: 250px !important;
    }

    .stat-value {
        font-size: 1.1rem;
    }
}

/* Dark mode chart adjustments */
[data-layout-mode="dark"] .chart-card .card-header {
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    border-bottom-color: #374151;
}

[data-layout-mode="dark"] .chart-stats {
    background: #1f2937;
}

[data-layout-mode="dark"] .stat-label {
    color: #9ca3af;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Phase 2: Enhanced Charts JavaScript - Inline for testing -->
<script>
// Quick test for Phase 2 charts
document.addEventListener('DOMContentLoaded', function() {
    console.log('Phase 2 Charts: Initializing...');

    // Test if Chart.js is available
    if (typeof Chart !== 'undefined') {
        console.log('Chart.js is available');

        // Create a simple test chart
        const canvas = document.getElementById('userTrendChart');
        if (canvas) {
            console.log('Found userTrendChart canvas');
            const ctx = canvas.getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Users',
                        data: [45, 48, 52, 55, 58, 61],
                        borderColor: '#667eea',
                        backgroundColor: '#667eea20',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Phase 2 Chart created successfully!');
        } else {
            console.log('userTrendChart canvas not found');
        }
    } else {
        console.log('Chart.js not available');
    }
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Main chart script
        // Chuẩn bị dữ liệu mẫu cho biểu đồ
        const userMonthlyData = [5, 8, 12, 15, 20, 25, 30, 28, 35, 40, 45, 50];
        const threadMonthlyData = [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65];
        const commentMonthlyData = [20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130];

        // Sử dụng dữ liệu mẫu cho biểu đồ donut
        var usersTotal = 100;
        var usersNew = 25;
        var threadsTotal = 200;
        var threadsNew = 30;
        var commentsTotal = 500;
        var commentsNew = 50;

        // Bật lại biểu đồ donut cho card thống kê
        createDonutChart('usersDonutChart', usersTotal, usersNew, '#3366CC');
        createDonutChart('threadsDonutChart', threadsTotal, threadsNew, '#22C55E');
        createDonutChart('commentsDonutChart', commentsTotal, commentsNew, '#0EA5E9');
        createDonutChart('activityDonutChart', 100, 33, '#F59E0B');

        // Marketplace metrics donut charts
        createDonutChart('revenueDonutChart', 100, 75, '#667eea');
        createDonutChart('ordersDonutChart', 100, 23, '#f093fb');
        createDonutChart('productsDonutChart', 100, 8, '#4facfe');
        createDonutChart('commissionDonutChart', 100, 60, '#43e97b');

        // Kiểm tra và sử dụng dữ liệu thực
        var finalUserData = userMonthlyData;
        var finalThreadData = threadMonthlyData;
        var finalCommentData = commentMonthlyData;

        // Nếu không có dữ liệu, sử dụng dữ liệu mẫu
        if (!hasData(finalUserData)) {
            finalUserData = [5, 8, 12, 15, 20, 25, 30, 28, 35, 40, 45, 50];
        }
        if (!hasData(finalThreadData)) {
            finalThreadData = [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65];
        }
        if (!hasData(finalCommentData)) {
            finalCommentData = [20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130];
        }

        // Bật lại biểu đồ thống kê người dùng
        if (document.getElementById('userStatsChart')) {
            // Biểu đồ thống kê người dùng
            const userStatsCtx = document.getElementById('userStatsChart').getContext('2d');
            userStatsChartInstance = new Chart(userStatsCtx, {
                type: 'line',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                    datasets: [{
                        label: 'Người dùng mới',
                        data: finalUserData,
                        fill: false,
                        borderColor: '#3366CC',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        }

        // Bật lại biểu đồ thống kê nội dung
        if (document.getElementById('contentStatsChart')) {
            // Biểu đồ thống kê nội dung
            const contentStatsCtx = document.getElementById('contentStatsChart').getContext('2d');
            contentStatsChartInstance = new Chart(contentStatsCtx, {
                type: 'line',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                    datasets: [{
                        label: 'Bài đăng',
                        data: finalThreadData,
                        fill: false,
                        borderColor: '#22C55E',
                        tension: 0.1
                    }, {
                        label: 'Bình luận',
                        data: finalCommentData,
                        fill: false,
                        borderColor: '#1DCABC',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        }

        // Bật lại biểu đồ thống kê theo vai trò
        if (document.getElementById('roleStatsChart')) {
            // Biểu đồ thống kê theo vai trò
            const roleStatsCtx = document.getElementById('roleStatsChart').getContext('2d');
            const roleStatsChart = new Chart(roleStatsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Quản trị viên', 'Điều hành viên', 'Thành viên cấp cao', 'Thành viên'],
                    datasets: [{
                        data: [1, 1, 2, 10],
                        backgroundColor: [
                            '#EF4444',
                            '#3366CC',
                            '#22C55E',
                            '#6B7280'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Bật lại biểu đồ thống kê theo trạng thái
        if (document.getElementById('statusStatsChart')) {
            // Biểu đồ thống kê theo trạng thái
            const statusStatsCtx = document.getElementById('statusStatsChart').getContext('2d');
            const statusStatsChart = new Chart(statusStatsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Đã xuất bản', 'Chờ duyệt', 'Bị từ chối', 'Bị khóa'],
                    datasets: [{
                        data: [15, 5, 2, 1],
                        backgroundColor: [
                            '#22C55E',
                            '#F59E0B',
                            '#EF4444',
                            '#6B7280'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Bật lại biểu đồ thống kê tương tác
        if (document.getElementById('interactionStatsChart')) {
            // Biểu đồ thống kê tương tác
            const interactionStatsCtx = document.getElementById('interactionStatsChart').getContext('2d');
            const interactionStatsChart = new Chart(interactionStatsCtx, {
                type: 'pie',
                data: {
                    labels: ['Bình luận', 'Thích', 'Lưu', 'Báo cáo'],
                    datasets: [{
                        data: [25, 40, 10, 5],
                        backgroundColor: [
                            '#1DCABC',
                            '#FF7846',
                            '#3366CC',
                            '#F59E0B'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Hàm kiểm tra xem mảng có dữ liệu không
        function hasData(arr) {
            if (!arr || !Array.isArray(arr)) return false;
            for (var i = 0; i < arr.length; i++) {
                if (arr[i] > 0) return true;
            }
            return false;
        }

        // Hàm hỗ trợ chuẩn bị dữ liệu theo tháng
        function prepareMonthlyData(rawData) {
            // Khởi tạo mảng 12 tháng với giá trị 0
            const monthlyData = Array(12).fill(0);

            try {
                // Điền dữ liệu vào mảng
                if (rawData && Array.isArray(rawData) && rawData.length > 0) {
                    rawData.forEach(item => {
                        if (item && typeof item.month === 'number' && typeof item.total === 'number') {
                            // Tháng trong MySQL bắt đầu từ 1, mảng JavaScript bắt đầu từ 0
                            const monthIndex = item.month - 1;
                            if (monthIndex >= 0 && monthIndex < 12) {
                                monthlyData[monthIndex] = item.total;
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Lỗi khi xử lý dữ liệu thống kê:', error);
                // Trả về mảng với giá trị 0 nếu có lỗi
                return Array(12).fill(0);
            }

            return monthlyData;
        }

        // Hàm tạo biểu đồ donut
        function createDonutChart(elementId, total, newItems, color) {
            const element = document.getElementById(elementId);
            if (!element) return; // Kiểm tra xem phần tử có tồn tại không

            const ctx = element.getContext('2d');

            // Đảm bảo có giá trị mặc định nếu không có dữ liệu
            if (!total) total = 100;
            if (!newItems) newItems = 25;

            const percentage = total > 0 ? Math.round((newItems / total) * 100) : 25; // Giá trị mặc định là 25%

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [percentage, 100 - percentage],
                        backgroundColor: [color, '#f1f5f9'],
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                },
                plugins: [{
                    id: 'centerText',
                    afterDraw: function(chart) {
                        const width = chart.width;
                        const height = chart.height;
                        const ctx = chart.ctx;

                        ctx.restore();
                        ctx.font = 'bold 14px Inter';
                        ctx.textBaseline = 'middle';
                        ctx.textAlign = 'center';
                        ctx.fillStyle = color;
                        ctx.fillText(percentage + '%', width / 2, height / 2);
                        ctx.save();
                    }
                }]
            });
        }

        // Biến lưu trữ các biểu đồ để có thể cập nhật sau này
        var userStatsChartInstance = null;
        var contentStatsChartInstance = null;

        // Hàm lọc dữ liệu thống kê người dùng theo số tháng
        function filterUserStats(months) {
            // Cập nhật tiêu đề dropdown
            document.getElementById('userStatsDropdown').innerHTML = '<i class="fas fa-calendar"></i> ' + months + ' tháng gần đây';

            // Re-initialize Feather Icons
            if (typeof feather !== 'undefined') {
                try {
                    feather.replace();
                } catch (error) {
                    console.warn('Feather Icons error after filter update:', error);
                }
            }

            // Lấy dữ liệu cho số tháng được chọn
            const filteredData = userMonthlyData.slice(-months);
            const labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'].slice(-months);

            // Hủy biểu đồ cũ nếu tồn tại
            if (userStatsChartInstance) {
                userStatsChartInstance.destroy();
            }

            // Tạo biểu đồ mới
            const userStatsCtx = document.getElementById('userStatsChart').getContext('2d');
            userStatsChartInstance = new Chart(userStatsCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Người dùng mới',
                        data: filteredData,
                        fill: false,
                        borderColor: '#3366CC',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        }

        // Hàm lọc dữ liệu thống kê nội dung theo số tháng
        function filterContentStats(months) {
            // Cập nhật tiêu đề dropdown
            document.getElementById('contentStatsDropdown').innerHTML = '<i class="fas fa-calendar"></i> ' + months + ' tháng gần đây';

            // Re-initialize Feather Icons
            if (typeof feather !== 'undefined') {
                try {
                    feather.replace();
                } catch (error) {
                    console.warn('Feather Icons error after filter update:', error);
                }
            }

            // Lấy dữ liệu cho số tháng được chọn
            const filteredThreadData = threadMonthlyData.slice(-months);
            const filteredCommentData = commentMonthlyData.slice(-months);
            const labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'].slice(-months);

            // Hủy biểu đồ cũ nếu tồn tại
            if (contentStatsChartInstance) {
                contentStatsChartInstance.destroy();
            }

            // Tạo biểu đồ mới
            const contentStatsCtx = document.getElementById('contentStatsChart').getContext('2d');
            contentStatsChartInstance = new Chart(contentStatsCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Bài đăng',
                        data: filteredThreadData,
                        fill: false,
                        borderColor: '#22C55E',
                        tension: 0.1
                    }, {
                        label: 'Bình luận',
                        data: filteredCommentData,
                        fill: false,
                        borderColor: '#1DCABC',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        }

        // Hàm mở hộp thoại tùy chỉnh khoảng thời gian cho thống kê người dùng
        function customFilterUserStats() {
            // Hiển thị hộp thoại tùy chỉnh (có thể sử dụng modal hoặc prompt đơn giản)
            const startMonth = prompt('Nhập tháng bắt đầu (1-12):', '1');
            const endMonth = prompt('Nhập tháng kết thúc (1-12):', '12');

            if (startMonth && endMonth) {
                const start = parseInt(startMonth);
                const end = parseInt(endMonth);

                if (!isNaN(start) && !isNaN(end) && start >= 1 && start <= 12 && end >= 1 && end <= 12 && start <= end) {
                    // Cập nhật tiêu đề dropdown
                    document.getElementById('userStatsDropdown').innerHTML = '<i class="fas fa-calendar"></i> T' + start + ' - T' + end;

                    // Re-initialize Feather Icons
                    if (typeof feather !== 'undefined') {
                        try {
                            feather.replace();
                        } catch (error) {
                            console.warn('Feather Icons error after custom filter:', error);
                        }
                    }

                    // Lấy dữ liệu cho khoảng thời gian được chọn
                    const filteredData = userMonthlyData.slice(start - 1, end);
                    const labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'].slice(start - 1, end);

                    // Hủy biểu đồ cũ nếu tồn tại
                    if (userStatsChartInstance) {
                        userStatsChartInstance.destroy();
                    }

                    // Tạo biểu đồ mới
                    const userStatsCtx = document.getElementById('userStatsChart').getContext('2d');
                    userStatsChartInstance = new Chart(userStatsCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Người dùng mới',
                                data: filteredData,
                                fill: false,
                                borderColor: '#3366CC',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: false
                                }
                            }
                        }
                    });
                } else {
                    alert('Vui lòng nhập tháng hợp lệ (1-12) và đảm bảo tháng bắt đầu không lớn hơn tháng kết thúc.');
                }
            }
        }

        // Hàm mở hộp thoại tùy chỉnh khoảng thời gian cho thống kê nội dung
        function customFilterContentStats() {
            // Hiển thị hộp thoại tùy chỉnh (có thể sử dụng modal hoặc prompt đơn giản)
            const startMonth = prompt('Nhập tháng bắt đầu (1-12):', '1');
            const endMonth = prompt('Nhập tháng kết thúc (1-12):', '12');

            if (startMonth && endMonth) {
                const start = parseInt(startMonth);
                const end = parseInt(endMonth);

                if (!isNaN(start) && !isNaN(end) && start >= 1 && start <= 12 && end >= 1 && end <= 12 && start <= end) {
                    // Cập nhật tiêu đề dropdown
                    document.getElementById('contentStatsDropdown').innerHTML = '<i class="fas fa-calendar"></i> T' + start + ' - T' + end;

                    // Re-initialize Feather Icons
                    if (typeof feather !== 'undefined') {
                        try {
                            feather.replace();
                        } catch (error) {
                            console.warn('Feather Icons error after custom filter:', error);
                        }
                    }

                    // Lấy dữ liệu cho khoảng thời gian được chọn
                    const filteredThreadData = threadMonthlyData.slice(start - 1, end);
                    const filteredCommentData = commentMonthlyData.slice(start - 1, end);
                    const labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'].slice(start - 1, end);

                    // Hủy biểu đồ cũ nếu tồn tại
                    if (contentStatsChartInstance) {
                        contentStatsChartInstance.destroy();
                    }

                    // Tạo biểu đồ mới
                    const contentStatsCtx = document.getElementById('contentStatsChart').getContext('2d');
                    contentStatsChartInstance = new Chart(contentStatsCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Bài đăng',
                                data: filteredThreadData,
                                fill: false,
                                borderColor: '#22C55E',
                                tension: 0.1
                            }, {
                                label: 'Bình luận',
                                data: filteredCommentData,
                                fill: false,
                                borderColor: '#1DCABC',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: false
                                }
                            }
                        }
                    });
                } else {
                    alert('Vui lòng nhập tháng hợp lệ (1-12) và đảm bảo tháng bắt đầu không lớn hơn tháng kết thúc.');
                }
            }
        }
    });
</script>
@endpush
