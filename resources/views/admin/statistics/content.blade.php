@extends('admin.layouts.dason')

@section('title', 'Thống kê nội dung')

@push('styles')
<!-- Page specific CSS -->
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
                    <h6 class="m-0 font-weight-bold text-primary">Bài đăng theo diễn đàn</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="forumChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($forumStats as $stat)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ randomColor($loop->index) }}"></i> {{ $stat->forum->name }}: {{ $stat->total }}
                            </span>
                        @endforeach
                    </div>
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
<script>
    // Hàm chuyển đổi tháng sang tên tháng
    function getMonthName(month) {
        const monthNames = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
        return monthNames[month - 1];
    }

    // Biểu đồ trạng thái bài đăng
    var threadStatusCtx = document.getElementById("threadStatusChart");
    var threadStatusData = @json($threadStatusStats);
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
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });

    // Biểu đồ diễn đàn
    var forumCtx = document.getElementById("forumChart");
    var forumData = @json($forumStats);
    var forumChart = new Chart(forumCtx, {
        type: 'doughnut',
        data: {
            labels: forumData.map(item => item.forum.name),
            datasets: [{
                data: forumData.map(item => item.total),
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
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });

    // Biểu đồ chuyên mục
    var categoryCtx = document.getElementById("categoryChart");
    var categoryData = @json($categoryStats);
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
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });

    // Biểu đồ bài đăng theo thời gian
    var threadTimeCtx = document.getElementById("threadTimeChart");
    var threadTimeData = @json($threadTimeStats);
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
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        beginAtZero: true
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
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

    // Biểu đồ bình luận theo thời gian
    var commentTimeCtx = document.getElementById("commentTimeChart");
    var commentTimeData = @json($commentTimeStats);
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
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        beginAtZero: true
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
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
</script>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection