@extends('admin.layouts.dason')

@section('title', 'Dashboard - ' . auth()->user()->getRoleDisplayName())

@section('css')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Dashboard CSS -->
    <style>
        .dashboard-card {
            transition: transform 0.2s ease-in-out;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
        }
        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title & Role Info -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                        <div class="d-flex align-items-center mt-1">
                            <span class="badge bg-{{ auth()->user()->getRoleColor() }} role-badge me-2">
                                {{ $dashboardData['role_info']['role'] }}
                            </span>
                            <small class="text-muted">
                                {{ $dashboardData['role_info']['role_group'] }} •
                                {{ $dashboardData['role_info']['permissions_count'] }} quyền hạn
                            </small>
                        </div>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        @if(isset($dashboardData['system']) || isset($dashboardData['marketplace']) || isset($dashboardData['community']))
        <div class="row">
            @if(isset($dashboardData['system']['users']))
                <div class="col-xl-3 col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-1">
                                    <p class="text-truncate font-size-14 mb-2">Tổng người dùng</p>
                                    <h4 class="mb-2">{{ number_format($dashboardData['system']['users']['total']) }}</h4>
                                    <p class="text-muted mb-0">
                                        <span class="text-success fw-bold font-size-12 me-2">
                                            <i class="ri-arrow-right-up-line me-1 align-middle"></i>
                                            +{{ $dashboardData['system']['users']['new_today'] }}
                                        </span>
                                        hôm nay
                                    </p>
                                </div>
                                <div class="stat-icon bg-primary bg-soft text-primary">
                                    <i class="fas fa-users font-size-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($dashboardData['marketplace']['products']))
                <div class="col-xl-3 col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-1">
                                    <p class="text-truncate font-size-14 mb-2">Sản phẩm chờ duyệt</p>
                                    <h4 class="mb-2">{{ $dashboardData['marketplace']['products']['pending'] }}</h4>
                                    <p class="text-muted mb-0">
                                        <span class="text-warning fw-bold font-size-12 me-2">
                                            <i class="ri-time-line me-1 align-middle"></i>
                                            Cần xử lý
                                        </span>
                                    </p>
                                </div>
                                <div class="stat-icon bg-warning bg-soft text-warning">
                                    <i class="fas fa-clock font-size-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($dashboardData['community']['moderation']))
                <div class="col-xl-3 col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-1">
                                    <p class="text-truncate font-size-14 mb-2">Báo cáo vi phạm</p>
                                    <h4 class="mb-2">{{ $dashboardData['community']['moderation']['pending_reports'] }}</h4>
                                    <p class="text-muted mb-0">
                                        <span class="text-danger fw-bold font-size-12 me-2">
                                            <i class="ri-alert-line me-1 align-middle"></i>
                                            {{ $dashboardData['community']['moderation']['urgent_reports'] }} khẩn cấp
                                        </span>
                                    </p>
                                </div>
                                <div class="stat-icon bg-danger bg-soft text-danger">
                                    <i class="fas fa-flag font-size-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($dashboardData['marketplace']['products']['revenue_today']))
                <div class="col-xl-3 col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-1">
                                    <p class="text-truncate font-size-14 mb-2">Doanh thu hôm nay</p>
                                    <h4 class="mb-2">{{ number_format($dashboardData['marketplace']['products']['revenue_today']) }}đ</h4>
                                    <p class="text-muted mb-0">
                                        <span class="text-success fw-bold font-size-12 me-2">
                                            <i class="ri-shopping-cart-line me-1 align-middle"></i>
                                            {{ $dashboardData['marketplace']['products']['orders_today'] }} đơn hàng
                                        </span>
                                    </p>
                                </div>
                                <div class="stat-icon bg-success bg-soft text-success">
                                    <i class="fas fa-dollar-sign font-size-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endif

        <!-- Main Content Row -->
        <div class="row">
            <!-- Left Column -->
            <div class="col-xl-8">

                @if(isset($dashboardData['analytics']['charts']))
                <!-- Analytics Chart -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Tăng trưởng người dùng (30 ngày qua)</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="userGrowthChart" height="300"></canvas>
                    </div>
                </div>
                @endif

                @if(isset($dashboardData['community']['moderation']['recent_reports']))
                <!-- Recent Reports -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title mb-0">Báo cáo vi phạm gần đây</h4>
                            <div class="ms-auto">
                                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i> Xem tất cả
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($dashboardData['community']['moderation']['recent_reports']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Người báo cáo</th>
                                            <th>Nội dung</th>
                                            <th>Mức độ</th>
                                            <th>Thời gian</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dashboardData['community']['moderation']['recent_reports'] as $report)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $report->user->getAvatarUrl() }}" alt="" class="avatar-xs rounded-circle me-2"
                                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($report->user->name, 0, 1))) }}&background=6366f1&color=fff&size=32'">
                                                    <span>{{ $report->user->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 200px;">{{ $report->reason }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $report->priority === 'high' ? 'danger' : ($report->priority === 'medium' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($report->priority) }}
                                                </span>
                                            </td>
                                            <td>{{ $report->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-success font-size-48 mb-3"></i>
                                <h5>Không có báo cáo nào</h5>
                                <p class="text-muted">Tất cả báo cáo đã được xử lý</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            <!-- Right Column -->
            <div class="col-xl-4">

                @if(isset($dashboardData['system']['users']['by_role_group']))
                <!-- Role Distribution -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Phân bố theo nhóm</h4>
                    </div>
                    <div class="card-body">
                        @foreach($dashboardData['system']['users']['by_role_group'] as $group => $count)
                            @php
                                $groupConfig = config('mechamap_permissions.role_groups.' . $group, []);
                                $percentage = ($count / $dashboardData['system']['users']['total']) * 100;
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-1">
                                    <h6 class="mb-1">{{ $groupConfig['name'] ?? $group }}</h6>
                                    <p class="text-muted mb-0">{{ $count }} người dùng</p>
                                </div>
                                <div class="ms-3">
                                    <span class="badge bg-{{ $groupConfig['color'] ?? 'secondary' }}">
                                        {{ number_format($percentage, 1) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-{{ $groupConfig['color'] ?? 'secondary' }}"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($dashboardData['system']['system']))
                <!-- System Status -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Trạng thái hệ thống</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-1">
                                <h6 class="mb-1">Server</h6>
                                <p class="text-muted mb-0">{{ ucfirst($dashboardData['system']['system']['server_status']) }}</p>
                            </div>
                            <div class="ms-3">
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-1">
                                <h6 class="mb-1">Database</h6>
                                <p class="text-muted mb-0">{{ ucfirst($dashboardData['system']['system']['database_status']) }}</p>
                            </div>
                            <div class="ms-3">
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="mb-0">Storage</h6>
                                <span class="text-muted">{{ $dashboardData['system']['system']['storage_usage']['percentage'] }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" style="width: {{ $dashboardData['system']['system']['storage_usage']['percentage'] }}%"></div>
                            </div>
                            <small class="text-muted">
                                {{ $dashboardData['system']['system']['storage_usage']['used'] }} /
                                {{ $dashboardData['system']['system']['storage_usage']['total'] }}
                            </small>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Hành động nhanh</h4>
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->hasPermissionTo('create-users'))
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm w-100 mb-2">
                                <i class="fas fa-user-plus me-2"></i> Thêm người dùng
                            </a>
                        @endif

                        @if(auth()->user()->hasPermissionTo('moderate-content'))
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-warning btn-sm w-100 mb-2">
                                <i class="fas fa-flag me-2"></i> Xem báo cáo
                            </a>
                        @endif

                        @if(auth()->user()->hasPermissionTo('manage-marketplace'))
                            <a href="{{ route('admin.products.pending') }}" class="btn btn-info btn-sm w-100 mb-2">
                                <i class="fas fa-clock me-2"></i> Duyệt sản phẩm
                            </a>
                        @endif

                        @if(auth()->user()->hasPermissionTo('view-analytics'))
                            <a href="{{ route('admin.analytics.dashboard') }}" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-chart-bar me-2"></i> Xem báo cáo
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($dashboardData['analytics']['charts']['user_growth']))
    // User Growth Chart
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($dashboardData['analytics']['charts']['user_growth']['labels']),
            datasets: [{
                label: 'Người dùng mới',
                data: @json($dashboardData['analytics']['charts']['user_growth']['data']),
                borderColor: '#556ee6',
                backgroundColor: 'rgba(85, 110, 230, 0.1)',
                borderWidth: 2,
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
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    @endif

    // Auto refresh dashboard data every 5 minutes
    setInterval(function() {
        fetch('{{ route("admin.dashboard.realtime") }}')
            .then(response => response.json())
            .then(data => {
                // Update notification counts
                console.log('Dashboard data refreshed:', data);
            })
            .catch(error => console.log('Refresh error:', error));
    }, 300000); // 5 minutes
});
</script>
@endsection
