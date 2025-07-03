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
        <button type="button" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-sync-alt"></i> Làm mới
        </button>
    </div>
@endsection

@push('styles')
<style>
.metric-card {
    transition: transform 0.2s ease-in-out;
    border-left: 4px solid transparent;
}
.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.metric-card.primary { border-left-color: #4e73df; }
.metric-card.success { border-left-color: #1cc88a; }
.metric-card.info { border-left-color: #36b9cc; }
.metric-card.warning { border-left-color: #f6c23e; }

.growth-positive { color: #1cc88a; }
.growth-negative { color: #e74a3b; }
.growth-neutral { color: #858796; }

.section-divider {
    border-top: 2px solid #e3e6f0;
    margin: 2rem 0;
    position: relative;
}
.section-divider::before {
    content: '';
    position: absolute;
    top: -2px;
    left: 0;
    width: 60px;
    height: 2px;
    background: #4e73df;
}

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}
</style>
@endpush

@section('content')
<!-- 📊 CORE METRICS -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-primary mb-3">
            <i class="fas fa-tachometer-alt"></i> Số liệu cốt lõi
        </h5>
    </div>
</div>

<div class="row mb-4">
    <!-- Tổng số người dùng -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card metric-card primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng số người dùng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($coreStats['total_users']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-plus-circle"></i> {{ $coreStats['users_today'] }} hôm nay
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tổng số bài đăng -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card metric-card success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tổng số bài đăng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($coreStats['total_threads']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-plus-circle"></i> {{ $coreStats['threads_today'] }} hôm nay
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tổng số bình luận -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card metric-card info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Tổng số bình luận
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($coreStats['total_comments']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-plus-circle"></i> {{ $coreStats['comments_today'] }} hôm nay
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comment-dots fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Người dùng online -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card metric-card warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Người dùng online
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($coreStats['online_users']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-circle text-success"></i> Đang hoạt động
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-divider"></div>

<!-- 📈 GROWTH METRICS -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-primary mb-3">
            <i class="fas fa-chart-line"></i> Tăng trưởng & Xu hướng
        </h5>
    </div>
</div>

<div class="row mb-4">
    <!-- Hoạt động tuần này -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hoạt động tuần này</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="h2 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($growthStats['weekly_activity']) }}
                        </div>
                        <div class="text-xs text-muted">
                            Tổng hoạt động (bài đăng + bình luận + người dùng mới)
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-right">
                            <div class="h5 mb-0 {{ $growthStats['weekly_growth'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                <i class="fas fa-arrow-{{ $growthStats['weekly_growth'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($growthStats['weekly_growth']) }}%
                            </div>
                            <div class="text-xs text-muted">so với tuần trước</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hoạt động tháng này -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hoạt động tháng này</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="h2 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($growthStats['monthly_activity']) }}
                        </div>
                        <div class="text-xs text-muted">
                            Tổng hoạt động trong tháng {{ now()->format('m/Y') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-right">
                            <div class="h5 mb-0 {{ $growthStats['monthly_growth'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                <i class="fas fa-arrow-{{ $growthStats['monthly_growth'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($growthStats['monthly_growth']) }}%
                            </div>
                            <div class="text-xs text-muted">so với tháng trước</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-divider"></div>

<!-- 🏪 MARKETPLACE METRICS -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-primary mb-3">
            <i class="fas fa-store"></i> Thương mại điện tử
            <small class="text-muted">(Dữ liệu mô phỏng - chưa có hệ thống thực)</small>
        </h5>
    </div>
</div>

<div class="row mb-4">
    <!-- Đơn hàng chờ xử lý -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card metric-card warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Đơn hàng chờ xử lý
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($marketplaceStats['pending_orders']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-clock"></i> Cần xử lý
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sản phẩm chờ duyệt -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card metric-card info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Sản phẩm chờ duyệt
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($marketplaceStats['pending_products']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-eye"></i> Cần kiểm duyệt
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu tháng này -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card metric-card success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Doanh thu tháng này
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($marketplaceStats['monthly_revenue']) }}₫
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-info-circle"></i> Chưa có giao dịch
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-divider"></div>

<!-- 📊 CHARTS & ANALYTICS -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-primary mb-3">
            <i class="fas fa-chart-pie"></i> Phân tích dữ liệu
        </h5>
    </div>
</div>

<div class="row mb-4">
    <!-- Thống kê vai trò người dùng -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Người dùng theo vai trò</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="userRoleChart"></canvas>
                </div>
                <div class="mt-3">
                    @foreach($chartData['user_roles'] as $role => $count)
                    <span class="badge badge-primary mr-2 mb-1">
                        {{ ucfirst($role) }}: {{ $count }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê trạng thái bài đăng -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Bài đăng theo trạng thái</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="threadStatusChart"></canvas>
                </div>
                <div class="mt-3">
                    @foreach($chartData['thread_status'] as $status => $count)
                    <span class="badge badge-secondary mr-2 mb-1">
                        {{ ucfirst($status) }}: {{ $count }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-divider"></div>

<!-- 📋 RECENT ACTIVITY -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-primary mb-3">
            <i class="fas fa-clock"></i> Hoạt động gần đây
        </h5>
    </div>
</div>

<div class="row">
    <!-- Người dùng mới nhất -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Người dùng mới nhất</h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tbody>
                            @forelse($recentActivity['latest_users'] as $user)
                            <tr>
                                <td style="width: 50px;">
                                    <img src="{{ $user->getAvatarUrl() }}"
                                         alt="{{ $user->name }}"
                                         class="rounded-circle"
                                         style="width: 40px; height: 40px; object-fit: cover;"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($user->name, 0, 1))) }}&background=6366f1&color=fff&size=40'">
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </td>
                                <td class="text-right">
                                    <div class="badge badge-primary">{{ ucfirst($user->role ?? 'member') }}</div>
                                    <div class="text-xs text-muted">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <div>Chưa có người dùng nào</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bài đăng mới nhất -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Bài đăng mới nhất</h6>
                <a href="{{ route('admin.threads.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tbody>
                            @forelse($recentActivity['latest_threads'] as $thread)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">
                                        {{ Str::limit($thread->title, 40) }}
                                    </div>
                                    <small class="text-muted">
                                        bởi {{ $thread->user->name ?? 'Unknown' }}
                                        @if($thread->forum)
                                        trong {{ $thread->forum->name }}
                                        @endif
                                    </small>
                                </td>
                                <td class="text-right" style="width: 100px;">
                                    <div class="badge badge-{{ $thread->status === 'published' ? 'success' : 'warning' }}">
                                        {{ ucfirst($thread->status ?? 'draft') }}
                                    </div>
                                    <div class="text-xs text-muted">{{ $thread->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">
                                    <i class="fas fa-comments fa-2x mb-2"></i>
                                    <div>Chưa có bài đăng nào</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Initializing dashboard charts...');

    // Biểu đồ vai trò người dùng
    const userRoleCtx = document.getElementById('userRoleChart');
    if (userRoleCtx) {
        const userRoleData = @json($chartData['user_roles']);
        console.log('👥 User role data:', userRoleData);

        new Chart(userRoleCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(userRoleData).map(role => role.charAt(0).toUpperCase() + role.slice(1)),
                datasets: [{
                    data: Object.values(userRoleData),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                        '#e74a3b', '#858796', '#5a5c69', '#6f42c1'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9', '#17a673', '#2c9faf', '#dda20a',
                        '#be2617', '#60616f', '#484848', '#5a2d91'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255,255,255,0.95)',
                        titleColor: '#333',
                        bodyColor: '#666',
                        borderColor: '#ddd',
                        borderWidth: 1
                    }
                },
                cutout: '60%'
            }
        });
        console.log('✅ User role chart created');
    }

    // Biểu đồ trạng thái bài đăng
    const threadStatusCtx = document.getElementById('threadStatusChart');
    if (threadStatusCtx) {
        const threadStatusData = @json($chartData['thread_status']);
        console.log('📝 Thread status data:', threadStatusData);

        new Chart(threadStatusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(threadStatusData).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
                datasets: [{
                    data: Object.values(threadStatusData),
                    backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#858796'],
                    hoverBackgroundColor: ['#17a673', '#dda20a', '#be2617', '#60616f'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255,255,255,0.95)',
                        titleColor: '#333',
                        bodyColor: '#666',
                        borderColor: '#ddd',
                        borderWidth: 1
                    }
                },
                cutout: '60%'
            }
        });
        console.log('✅ Thread status chart created');
    }

    console.log('🎉 Dashboard charts initialized successfully!');
});
</script>
@endpush
