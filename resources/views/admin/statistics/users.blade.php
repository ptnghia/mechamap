@extends('admin.layouts.dason')

@section('title', 'Thống kê người dùng')

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
                <h1 class="h3 mb-0 text-gray-800">Thống kê người dùng</h1>
                <div>
                    <a href="{{ route('admin.statistics.export', ['type' => 'users']) }}"
                        class="btn btn-sm btn-primary">
                        <i class="fas fa-download fa-sm mr-1"></i> Xuất báo cáo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Thống kê người dùng theo vai trò -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Người dùng theo vai trò</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="userRoleChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($userRoleStats as $stat)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ randomColor($loop->index) }}"></i> {{
                            ucfirst($stat->role) }}: {{ $stat->total }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê người dùng theo trạng thái -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Người dùng theo trạng thái</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="userStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($userStatusStats as $stat)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ randomColor($loop->index) }}"></i> {{
                            ucfirst($stat->status) }}: {{ $stat->total }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thống kê người dùng theo thời gian đăng ký -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Người dùng đăng ký theo thời gian (12 tháng gần nhất)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userRegistrationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê người dùng theo phương thức đăng nhập -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Người dùng theo phương thức đăng nhập</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="userLoginMethodChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @php
                        $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
                        @endphp
                        @foreach($userLoginMethodStats as $stat)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ $colors[$loop->index % count($colors)] }}"></i> {{
                            ucfirst($stat->method) }}: {{ $stat->total }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thống kê người dùng hoạt động -->
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Người dùng hoạt động theo thời gian (12 tháng gần
                        nhất)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top 10 người dùng hoạt động nhiều nhất -->
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 người dùng hoạt động nhiều nhất</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Người dùng</th>
                                    <th>Bài đăng</th>
                                    <th>Bình luận</th>
                                    <th>Lượt thích</th>
                                    <th>Lượt theo dõi</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Đăng nhập cuối</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topActiveUsers as $user)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}">
                                            {{ $user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $user->threads_count }}</td>
                                    <td>{{ $user->comments_count }}</td>
                                    <td>{{ $user->likes_count }}</td>
                                    <td>{{ $user->follows_count }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $user->last_login_at ?
                                        \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

    // Biểu đồ người dùng theo vai trò
    var userRoleCtx = document.getElementById("userRoleChart");
    var userRoleData = @json($userRoleStats);
    var userRoleChart = new Chart(userRoleCtx, {
        type: 'doughnut',
        data: {
            labels: userRoleData.map(item => item.role.charAt(0).toUpperCase() + item.role.slice(1)),
            datasets: [{
                data: userRoleData.map(item => item.total),
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
                    titleColor: "#858796",
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

    // Biểu đồ người dùng theo trạng thái
    var userStatusCtx = document.getElementById("userStatusChart");
    var userStatusData = @json($userStatusStats);
    var userStatusChart = new Chart(userStatusCtx, {
        type: 'pie',
        data: {
            labels: userStatusData.map(item => item.status === 'active' ? 'Hoạt động' : 'Không hoạt động'),
            datasets: [{
                data: userStatusData.map(item => item.total),
                backgroundColor: ['#1cc88a', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#be2617'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    titleColor: "#858796",
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
        },
    });

    // Biểu đồ người dùng đăng ký theo thời gian (giả định dữ liệu từ controller)
    var userRegistrationCtx = document.getElementById("userRegistrationChart");
    // Tạo dữ liệu mẫu cho 12 tháng gần nhất - controller cần cung cấp dữ liệu này
    var registrationData = [15, 25, 30, 45, 50, 35, 40, 55, 48, 62, 58, 70]; // Dữ liệu mẫu
    var registrationLabels = [];
    for (let i = 11; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        registrationLabels.push(getMonthName(date.getMonth() + 1));
    }

    var userRegistrationChart = new Chart(userRegistrationCtx, {
        type: 'line',
        data: {
            labels: registrationLabels,
            datasets: [{
                label: "Số người đăng ký",
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
                data: registrationData,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    time: {
                        unit: 'month'
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
                        callback: function(value) {
                            return value + ' người';
                        }
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                },
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    titleColor: "#858796",
                    bodyColor: "#858796",
                    titleMarginBottom: 10,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    },
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                }
            }
        }
    });

    // Biểu đồ người dùng theo phương thức đăng nhập
    var userLoginMethodCtx = document.getElementById("userLoginMethodChart");
    var userLoginMethodData = @json($userLoginMethodStats);
    var userLoginMethodChart = new Chart(userLoginMethodCtx, {
        type: 'doughnut',
        data: {
            labels: userLoginMethodData.map(item => item.method.charAt(0).toUpperCase() + item.method.slice(1)),
            datasets: [{
                data: userLoginMethodData.map(item => item.total),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    titleColor: "#858796",
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

    // Biểu đồ hoạt động người dùng theo thời gian
    var userActivityCtx = document.getElementById("userActivityChart");
    // Tạo dữ liệu mẫu cho hoạt động - controller cần cung cấp dữ liệu này
    var activityData = [120, 135, 150, 165, 180, 145, 160, 175, 158, 192, 178, 210]; // Dữ liệu mẫu
    var activityLabels = [];
    for (let i = 11; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        activityLabels.push(getMonthName(date.getMonth() + 1));
    }

    var userActivityChart = new Chart(userActivityCtx, {
        type: 'bar',
        data: {
            labels: activityLabels,
            datasets: [{
                label: "Số hoạt động",
                backgroundColor: "rgba(78, 115, 223, 0.8)",
                hoverBackgroundColor: "rgba(78, 115, 223, 1)",
                borderColor: "rgba(78, 115, 223, 1)",
                data: activityData,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    }
                },
                y: {
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return value + ' hoạt động';
                        }
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                },
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    titleColor: "#858796",
                    bodyColor: "#858796",
                    titleMarginBottom: 10,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    },
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                }
            }
        }
    });
</script>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection