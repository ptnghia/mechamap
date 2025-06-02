@extends('admin.layouts.app')

@section('title', 'Thống kê tương tác')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @foreach($breadcrumbs as $breadcrumb)
                    @if($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
                    @else
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                    @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>

    <!-- Page Title -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Thống kê tương tác</h1>
                <div>
                    <a href="{{ route('admin.statistics.export', ['type' => 'interactions']) }}"
                        class="btn btn-sm btn-primary">
                        <i class="fas fa-download fa-sm mr-1"></i> Xuất báo cáo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Thống kê lượt xem -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lượt xem theo thời gian (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="viewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê lượt thích -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lượt thích theo thời gian (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="likesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thống kê lượt theo dõi -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lượt theo dõi theo thời gian (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="followsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê lượt đánh dấu -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lượt đánh dấu theo thời gian (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="bookmarksChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thống kê tương tác theo diễn đàn -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tương tác theo diễn đàn</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="forumInteractionsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($forumInteractions as $forum)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ randomColor($loop->index) }}"></i> {{ $forum->name
                            }}: {{ $forum->total_interactions }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê tương tác theo người dùng -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 người dùng tương tác nhiều nhất</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Người dùng</th>
                                    <th>Số bài viết</th>
                                    <th>Lượt thích</th>
                                    <th>Lượt bình luận</th>
                                    <th>Tổng tương tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $user)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}">
                                            {{ $user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $user->posts_count }}</td>
                                    <td>{{ $user->likes_count }}</td>
                                    <td>{{ $user->comments_count }}</td>
                                    <td>{{ $user->total_interactions }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thống kê tương tác theo thiết bị -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tương tác theo thiết bị</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="deviceChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Desktop
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Mobile
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Tablet
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê tương tác theo thời gian trong ngày -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tương tác theo thời gian trong ngày</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Hàm chuyển đổi tháng sang tên tháng
    function getMonthName(month) {
        const monthNames = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
        return monthNames[month - 1];
    }

    // Biểu đồ lượt xem
    var viewsCtx = document.getElementById("viewsChart");
    var viewsData = @json($viewsStats);

    var viewsChart = new Chart(viewsCtx, {
        type: 'line',
        data: {
            labels: viewsData.labels,
            datasets: [{
                label: "Lượt xem",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: viewsData.data,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2]
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    titleMarginBottom: 10,
                    titleColor: '#6e707e',
                    titleFont: {
                        size: 14
                    },
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10
                }
            }
        }
    });

    // Tương tự cho các biểu đồ khác: likesChart, followsChart, bookmarksChart, forumInteractionsChart, deviceChart, hourlyChart
    // Mã JavaScript cho các biểu đồ này sẽ tương tự như biểu đồ lượt xem, chỉ thay đổi dữ liệu và màu sắc
</script>
@endsection