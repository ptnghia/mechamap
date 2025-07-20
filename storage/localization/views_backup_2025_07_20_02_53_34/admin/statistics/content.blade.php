@extends('admin.layouts.dason')

@section('title', 'Thống kê nội dung')

@push('styles')
<!-- Page specific CSS -->
<style>
.chart-area {
    position: relative;
    height: 300px !important;
    width: 100% !important;
    min-height: 300px;
}
.chart-pie {
    position: relative;
    height: 300px !important;
    width: 100% !important;
    min-height: 300px;
}
canvas {
    width: 100% !important;
    height: 300px !important;
    min-height: 300px !important;
    display: block !important;
}
</style>
@endpush

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
                <h1 class="h3 mb-0 text-gray-800">Thống kê nội dung</h1>
                <div>
                    <a href="{{ route('admin.statistics.export', ['type' => 'content']) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-download fa-sm mr-1"></i> Xuất báo cáo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Thống kê bài đăng theo trạng thái -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Bài đăng theo trạng thái</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="threadStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($threadStatusStats as $stat)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ randomColor($loop->index) }}"></i> {{ ucfirst($stat->status) }}: {{ $stat->total }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê bài đăng theo diễn đàn -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Bài đăng theo diễn đàn (Top 15)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 70%">Tên diễn đàn</th>
                                    <th style="width: 25%" class="text-center">Số bài đăng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forumStats->take(15) as $index => $stat)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                @if($index < 3)
                                                    <i class="fas fa-trophy text-warning"></i>
                                                @elseif($index < 5)
                                                    <i class="fas fa-medal text-info"></i>
                                                @else
                                                    <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $stat->forum->name }}</div>
                                                @if($stat->forum->category)
                                                <small class="text-muted">{{ $stat->forum->category->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $stat->total }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($forumStats->count() > 15)
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Hiển thị top 15 trong tổng số {{ $forumStats->count() }} diễn đàn
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thống kê bài đăng theo chuyên mục -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Bài đăng theo chuyên mục</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($categoryStats as $stat)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ randomColor($loop->index) }}"></i> {{ $stat->category->name }}: {{ $stat->total }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê bài đăng theo thời gian -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Bài đăng theo thời gian (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="threadTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thống kê bình luận theo thời gian -->
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Bình luận theo thời gian (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="commentTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Initializing content charts...');

    // Hàm chuyển đổi tháng sang tên tháng
    function getMonthName(month) {
        const monthNames = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
        return monthNames[month - 1];
    }

    // Biểu đồ trạng thái bài đăng
    var threadStatusCtx = document.getElementById("threadStatusChart");
    if (threadStatusCtx) {
        var threadStatusData = @json($threadStatusStats);
        console.log('📊 Thread status data:', threadStatusData);
        var threadStatusChart = new Chart(threadStatusCtx, {
        type: 'doughnut',
        data: {
            labels: threadStatusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
            datasets: [{
                data: threadStatusData.map(item => item.total),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#60616f'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                }
            },
            cutout: '80%',
        },
    });
        console.log('✅ Thread status chart created successfully');
    }

    // Biểu đồ diễn đàn đã được thay thế bằng bảng
    console.log('📊 Forum stats displayed as table instead of chart');

    // Biểu đồ chuyên mục
    var categoryCtx = document.getElementById("categoryChart");
    if (categoryCtx) {
        var categoryData = @json($categoryStats);
        console.log('📂 Category data:', categoryData);
        var categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.category.name),
            datasets: [{
                data: categoryData.map(item => item.total),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796',
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#60616f',
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#60616f'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                }
            },
            cutout: '80%',
        },
    });
        console.log('✅ Category chart created successfully');
    }

    // Biểu đồ bài đăng theo thời gian
    var threadTimeCtx = document.getElementById("threadTimeChart");
    if (threadTimeCtx) {
        var threadTimeData = @json($threadTimeStats);
        console.log('📈 Thread time data:', threadTimeData);
        var threadTimeLabels = threadTimeData.map(item => getMonthName(item.month) + ' ' + item.year);
        var threadTimeValues = threadTimeData.map(item => item.total);

        var threadTimeChart = new Chart(threadTimeCtx, {
        type: 'line',
        data: {
            labels: threadTimeLabels,
            datasets: [{
                label: "Bài đăng",
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
                data: threadTimeValues,
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
                    time: {
                        unit: 'date'
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                },
                y: {
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        beginAtZero: true
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2]
                    }
                }
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
            }
        }
    });
        console.log('✅ Thread time chart created successfully');
    }

    // Biểu đồ bình luận theo thời gian
    var commentTimeCtx = document.getElementById("commentTimeChart");
    if (commentTimeCtx) {
        var commentTimeData = @json($commentTimeStats);
        console.log('💬 Comment time data:', commentTimeData);
        var commentTimeLabels = commentTimeData.map(item => getMonthName(item.month) + ' ' + item.year);
        var commentTimeValues = commentTimeData.map(item => item.total);

        var commentTimeChart = new Chart(commentTimeCtx, {
        type: 'line',
        data: {
            labels: commentTimeLabels,
            datasets: [{
                label: "Bình luận",
                lineTension: 0.3,
                backgroundColor: "rgba(28, 200, 138, 0.05)",
                borderColor: "rgba(28, 200, 138, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(28, 200, 138, 1)",
                pointBorderColor: "rgba(28, 200, 138, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
                pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: commentTimeValues,
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
                    time: {
                        unit: 'date'
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                },
                y: {
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        beginAtZero: true
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2]
                    }
                }
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
            }
        }
    });
        console.log('✅ Comment time chart created successfully');
    }

    console.log('🎉 All content charts initialized successfully!');
});
</script>
@endpush
