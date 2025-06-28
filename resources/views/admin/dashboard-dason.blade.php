@extends('admin.layouts.dason')

@section('title', 'Admin Dashboard')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- ApexCharts CSS -->
<link href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="row">
    <!-- Marketplace Overview Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Users</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="{{ $stats['total_users'] ?? 0 }}">0</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-success text-success">+{{ $stats['new_users_this_month'] ?? 0 }}</span>
                            <span class="ms-1 text-muted font-size-13">this month</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div id="mini-chart1" data-colors='["#1c84ee", "#33c38e"]' class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Products</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="{{ $stats['total_products'] ?? 0 }}">0</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-info text-info">{{ $stats['approved_products'] ?? 0 }}</span>
                            <span class="ms-1 text-muted font-size-13">approved</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div id="mini-chart2" data-colors='["#1c84ee", "#33c38e"]' class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Orders</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="{{ $stats['total_orders'] ?? 0 }}">0</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-warning text-warning">{{ $stats['pending_orders'] ?? 0 }}</span>
                            <span class="ms-1 text-muted font-size-13">pending</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div id="mini-chart3" data-colors='["#1c84ee", "#33c38e"]' class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Revenue</span>
                        <h4 class="mb-3">
                            ₫<span class="counter-value" data-target="{{ $stats['total_revenue'] ?? 0 }}">0</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-success text-success">+{{ number_format($stats['revenue_growth'] ?? 0, 1) }}%</span>
                            <span class="ms-1 text-muted font-size-13">vs last month</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div id="mini-chart4" data-colors='["#1c84ee", "#33c38e"]' class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Revenue Analytics</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold">Sort By:</span> <span class="text-muted">Monthly<i class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Monthly</a>
                            <a class="dropdown-item" href="#">Yearly</a>
                            <a class="dropdown-item" href="#">Weekly</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pb-2">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                            <i data-feather="trending-up" class="text-primary"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 text-muted">Total Revenue</h6>
                        <b>₫{{ number_format($stats['total_revenue'] ?? 0) }}</b>
                    </div>
                </div>

                <div id="revenue-chart" data-colors='["#1c84ee", "#33c38e"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>

    <!-- User Activity -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">User Activity</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted font-size-12">Sort By</span> <span class="text-muted">Today<i class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Today</a>
                            <a class="dropdown-item" href="#">Yesterday</a>
                            <a class="dropdown-item" href="#">Last 7 Days</a>
                            <a class="dropdown-item" href="#">Last 30 Days</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-2">
                <div id="user-activity-chart" data-colors='["#1c84ee", "#33c38e", "#f1b44c"]' class="apex-charts" dir="ltr"></div>
                <div class="px-3">
                    <div class="row text-center mt-4">
                        <div class="col-4">
                            <h5 class="mb-0">{{ $stats['active_users'] ?? 0 }}</h5>
                            <p class="text-muted text-truncate">Active</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">{{ $stats['new_registrations'] ?? 0 }}</h5>
                            <p class="text-muted text-truncate">New</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">{{ $stats['returning_users'] ?? 0 }}</h5>
                            <p class="text-muted text-truncate">Returning</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Recent Orders</h4>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-soft-primary btn-sm">
                        View All <i class="mdi mdi-arrow-right align-middle ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Order ID</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Product</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_orders ?? [] as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-body">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $order->user->avatar_url ?? asset('images/users/avatar-1.jpg') }}" alt="" class="avatar-sm rounded-circle me-2">
                                        <div class="flex-grow-1">{{ $order->user->name }}</div>
                                    </div>
                                </td>
                                <td>{{ $order->items->first()->product_title ?? 'N/A' }}</td>
                                <td>₫{{ number_format($order->total_amount) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">No recent orders found</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Sellers -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Top Sellers</h4>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.sellers.index') }}" class="btn btn-soft-primary btn-sm">
                        View All <i class="mdi mdi-arrow-right align-middle ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                @forelse($top_sellers ?? [] as $seller)
                <div class="d-flex align-items-center pb-3">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <img src="{{ $seller->avatar_url ?? asset('images/users/avatar-1.jpg') }}" alt="" class="img-thumbnail rounded-circle">
                    </div>
                    <div class="flex-grow-1">
                        <div>
                            <h5 class="font-size-14 mb-1">{{ $seller->name }}</h5>
                            <p class="font-size-13 text-muted mb-0">{{ ucfirst($seller->role) }}</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <h6 class="mb-0">₫{{ number_format($seller->total_earnings ?? 0) }}</h6>
                        <p class="text-muted font-size-12 mb-0">{{ $seller->total_sales ?? 0 }} sales</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <div class="text-muted">No seller data available</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Forum Activity -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Forum Activity</h4>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.forum.threads') }}" class="btn btn-soft-primary btn-sm">
                        Manage Forum <i class="mdi mdi-arrow-right align-middle ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="mt-0">
                            <p class="text-muted mb-1">Total Threads</p>
                            <h4>{{ $stats['total_threads'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mt-0">
                            <p class="text-muted mb-1">Total Posts</p>
                            <h4>{{ $stats['total_posts'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div id="forum-activity-chart" data-colors='["#1c84ee"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">System Status</h4>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.settings.maintenance') }}" class="btn btn-soft-primary btn-sm">
                        Settings <i class="mdi mdi-cog align-middle ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-success text-success rounded">
                                        <i class="mdi mdi-server"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Server Status</p>
                                <h6 class="mb-0">Online</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-info text-info rounded">
                                        <i class="mdi mdi-database"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Database</p>
                                <h6 class="mb-0">Connected</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-warning text-warning rounded">
                                        <i class="mdi mdi-backup-restore"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Last Backup</p>
                                <h6 class="mb-0">2 hours ago</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-primary text-primary rounded">
                                        <i class="mdi mdi-update"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Version</p>
                                <h6 class="mb-0">v2.0.0</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- ApexCharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- Dashboard init -->
<script>
// Counter animation
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation
    const counters = document.querySelectorAll('.counter-value');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const increment = target / 100;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 20);
    });

    // Revenue Chart
    const revenueOptions = {
        series: [{
            name: 'Revenue',
            data: @json($revenue_chart_data ?? [])
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false }
        },
        colors: ['#1c84ee'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        },
        xaxis: {
            categories: @json($revenue_chart_labels ?? [])
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return '₫' + val.toLocaleString();
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#revenue-chart"), revenueOptions).render();

    // User Activity Chart
    const userActivityOptions = {
        series: @json($user_activity_data ?? []),
        chart: {
            type: 'donut',
            height: 200
        },
        colors: ['#1c84ee', '#33c38e', '#f1b44c'],
        labels: ['Active', 'New', 'Returning'],
        legend: { show: false }
    };
    new ApexCharts(document.querySelector("#user-activity-chart"), userActivityOptions).render();

    // Forum Activity Chart
    const forumActivityOptions = {
        series: [{
            name: 'Posts',
            data: @json($forum_activity_data ?? [])
        }],
        chart: {
            type: 'line',
            height: 100,
            sparkline: { enabled: true }
        },
        colors: ['#1c84ee'],
        stroke: { width: 2 }
    };
    new ApexCharts(document.querySelector("#forum-activity-chart"), forumActivityOptions).render();
});
</script>
@endpush
