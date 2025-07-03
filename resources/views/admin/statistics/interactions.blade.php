@extends('admin.layouts.dason')

@section('title', 'Thống kê tương tác')

@push('styles')
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
.chart-bar {
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
                <h1 class="h3 mb-0">Thống kê tương tác</h1>
                <a href="{{ route('admin.statistics.export', ['type' => 'interactions']) }}" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Xuất báo cáo
                </a>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lượt xem theo thời gian (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="viewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
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

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lượt theo dõi theo thời gian (12 tháng gần nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="followsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
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

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tương tác theo diễn đàn</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="forumInteractionsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($forumInteractions->take(10) as $forum)
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> {{ $forum->name }}: {{ $forum->total_interactions }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
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
                                    <td><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></td>
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

    <!-- Additional Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tương tác theo thiết bị</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
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
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Initializing interaction charts...');

    // Number formatting function
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    // Biểu đồ lượt xem
    var viewsCtx = document.getElementById("viewsChart");
    if (viewsCtx) {
        console.log('🎯 Found viewsChart canvas:', viewsCtx);

        // Force canvas size
        viewsCtx.style.width = '100%';
        viewsCtx.style.height = '300px';
        viewsCtx.width = viewsCtx.offsetWidth;
        viewsCtx.height = 300;

        console.log('📏 Canvas dimensions after resize:', viewsCtx.width, 'x', viewsCtx.height);
        console.log('📐 Canvas style after resize:', viewsCtx.style.width, 'x', viewsCtx.style.height);

        var viewsData = @json($viewsStats);
        console.log('📊 Views data:', viewsData);

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
                            callback: function(value, index, values) {
                                return number_format(value);
                            }
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
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
        console.log('✅ Views chart created successfully');
    }

    // Biểu đồ lượt thích
    var likesCtx = document.getElementById("likesChart");
    if (likesCtx) {
        var likesData = @json($likesStats);
        console.log('👍 Likes data:', likesData);

        var likesChart = new Chart(likesCtx, {
            type: 'line',
            data: {
                labels: likesData.labels,
                datasets: [{
                    label: "Lượt thích",
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
                    data: likesData.data,
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
                            callback: function(value, index, values) {
                                return number_format(value);
                            }
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
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
        console.log('✅ Likes chart created successfully');
    }

    // Biểu đồ lượt theo dõi
    var followsCtx = document.getElementById("followsChart");
    if (followsCtx) {
        var followsData = @json($followsStats);
        console.log('👁️ Follows data:', followsData);

        var followsChart = new Chart(followsCtx, {
            type: 'line',
            data: {
                labels: followsData.labels,
                datasets: [{
                    label: "Lượt theo dõi",
                    lineTension: 0.3,
                    backgroundColor: "rgba(54, 185, 204, 0.05)",
                    borderColor: "rgba(54, 185, 204, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(54, 185, 204, 1)",
                    pointBorderColor: "rgba(54, 185, 204, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(54, 185, 204, 1)",
                    pointHoverBorderColor: "rgba(54, 185, 204, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: followsData.data,
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
                            callback: function(value, index, values) {
                                return number_format(value);
                            }
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
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
        console.log('✅ Follows chart created successfully');
    }

    // Biểu đồ lượt đánh dấu
    var bookmarksCtx = document.getElementById("bookmarksChart");
    if (bookmarksCtx) {
        var bookmarksData = @json($bookmarksStats);
        console.log('🔖 Bookmarks data:', bookmarksData);

        var bookmarksChart = new Chart(bookmarksCtx, {
            type: 'line',
            data: {
                labels: bookmarksData.labels,
                datasets: [{
                    label: "Lượt đánh dấu",
                    lineTension: 0.3,
                    backgroundColor: "rgba(231, 74, 59, 0.05)",
                    borderColor: "rgba(231, 74, 59, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(231, 74, 59, 1)",
                    pointBorderColor: "rgba(231, 74, 59, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(231, 74, 59, 1)",
                    pointHoverBorderColor: "rgba(231, 74, 59, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: bookmarksData.data,
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
                            callback: function(value, index, values) {
                                return number_format(value);
                            }
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
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
        console.log('✅ Bookmarks chart created successfully');
    }

    // Biểu đồ tương tác theo diễn đàn
    var forumCtx = document.getElementById("forumInteractionsChart");
    if (forumCtx) {
        var forumLabels = [];
        var forumData = [];
        var forumColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1', '#e83e8c', '#fd7e14'];

        @foreach($forumInteractions->take(10) as $index => $forum)
            forumLabels.push('{{ $forum->name }}');
            forumData.push({{ $forum->total_interactions }});
        @endforeach

        var forumChart = new Chart(forumCtx, {
            type: 'doughnut',
            data: {
                labels: forumLabels,
                datasets: [{
                    data: forumData,
                    backgroundColor: forumColors,
                    hoverBackgroundColor: forumColors.map(color => color + 'CC'),
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
        console.log('✅ Forum interactions chart created successfully');
    }

    // Biểu đồ tương tác theo thiết bị
    var deviceCtx = document.getElementById("deviceChart");
    if (deviceCtx) {
        var deviceChart = new Chart(deviceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Desktop', 'Mobile', 'Tablet'],
                datasets: [{
                    data: [65, 30, 5],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
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
        console.log('✅ Device chart created successfully');
    }

    // Biểu đồ tương tác theo giờ trong ngày
    var hourlyCtx = document.getElementById("hourlyChart");
    if (hourlyCtx) {
        var hourlyChart = new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: ['0h', '2h', '4h', '6h', '8h', '10h', '12h', '14h', '16h', '18h', '20h', '22h'],
                datasets: [{
                    label: "Tương tác",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: [12, 8, 15, 25, 45, 65, 80, 95, 85, 70, 55, 30],
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
                            unit: 'hour'
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 12
                        },
                        maxBarThickness: 25
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return number_format(value);
                            }
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
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
        console.log('✅ Hourly chart created successfully');
    }

    console.log('🎉 All interaction charts initialized successfully!');
});
</script>
@endpush
