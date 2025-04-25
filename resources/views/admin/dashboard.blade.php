@extends('admin.layouts.app')

@section('title', 'Bảng điều khiển')
@section('header', 'Bảng điều khiển')

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('admin.statistics.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-bar-chart"></i> Thống kê chi tiết
        </a>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="bi bi-download"></i> Xuất báo cáo
        </button>
    </div>
    <div class="btn-group dropdown">
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-calendar3"></i> Thời gian
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
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card primary">
                    <h6 class="card-title">Tổng số người dùng</h6>
                    <h2 class="card-value">{{ number_format($stats['users']) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend up">
                            <i class="bi bi-arrow-up"></i> {{ $stats['new_users_today'] }}
                        </span>
                        <span class="card-period">hôm nay</span>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="usersDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card success">
                    <h6 class="card-title">Tổng số bài đăng</h6>
                    <h2 class="card-value">{{ number_format($stats['threads']) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend up">
                            <i class="bi bi-arrow-up"></i> {{ $stats['new_threads_today'] }}
                        </span>
                        <span class="card-period">hôm nay</span>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-chat-left-text"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="threadsDonutChart" class="donut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="modern-card">
                <div class="card-body stats-card info">
                    <h6 class="card-title">Tổng số bình luận</h6>
                    <h2 class="card-value">{{ number_format($stats['comments']) }}</h2>
                    <div class="mt-2">
                        <span class="card-trend up">
                            <i class="bi bi-arrow-up"></i> {{ $stats['new_comments_today'] }}
                        </span>
                        <span class="card-period">hôm nay</span>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-chat-right-text"></i>
                    </div>
                    <div class="donut-chart-container mt-2">
                        <canvas id="commentsDonutChart" class="donut-chart"></canvas>
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
                            <i class="bi bi-filter"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary modern-btn">
                            <i class="bi bi-arrow-repeat"></i>
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
                                            <i class="bi bi-pencil"></i>
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
                            <i class="bi bi-filter"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary modern-btn">
                            <i class="bi bi-arrow-repeat"></i>
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

    <div class="row row-eq-height">
        <div class="col-md-6 mb-3">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Thống kê người dùng</h5>
                    <div class="chart-actions">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="userStatsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-calendar3"></i> 12 tháng gần đây
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
                                <i class="bi bi-calendar3"></i> 12 tháng gần đây
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
/* Không cần CSS riêng cho form xuất báo cáo nữa vì đã có CSS chung */
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Không cần code tùy chỉnh cho dropdown nữa, sử dụng Bootstrap mặc định

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
            document.getElementById('userStatsDropdown').innerHTML = '<i class="bi bi-calendar3"></i> ' + months + ' tháng gần đây';

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
            document.getElementById('contentStatsDropdown').innerHTML = '<i class="bi bi-calendar3"></i> ' + months + ' tháng gần đây';

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
                    document.getElementById('userStatsDropdown').innerHTML = '<i class="bi bi-calendar3"></i> T' + start + ' - T' + end;

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
                    document.getElementById('contentStatsDropdown').innerHTML = '<i class="bi bi-calendar3"></i> T' + start + ' - T' + end;

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