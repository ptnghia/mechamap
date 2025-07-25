@extends('admin.layouts.dason')

@section('title', 'Thống kê tài liệu')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">📊 Thống kê tài liệu</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.documentation.index') }}">Tài liệu</a></li>
                        <li class="breadcrumb-item active">Thống kê</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Range Filter -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="range" class="form-label">Khoảng thời gian</label>
                            <select name="range" id="range" class="form-select">
                                <option value="7" {{ $timeRange == '7' ? 'selected' : '' }}>7 ngày qua</option>
                                <option value="30" {{ $timeRange == '30' ? 'selected' : '' }}>30 ngày qua</option>
                                <option value="90" {{ $timeRange == '90' ? 'selected' : '' }}>90 ngày qua</option>
                                <option value="365" {{ $timeRange == '365' ? 'selected' : '' }}>1 năm qua</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                                <a href="{{ route('admin.documentation.analytics.export', ['range' => $timeRange, 'format' => 'csv']) }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Export CSV
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng tài liệu</p>
                            <h4 class="mb-0">{{ number_format($overviewStats['total_documents']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="fas fa-file-alt font-size-16"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Đã xuất bản</p>
                            <h4 class="mb-0">{{ number_format($overviewStats['published_documents']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="fas fa-check-circle font-size-16"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng lượt xem</p>
                            <h4 class="mb-0">{{ number_format($overviewStats['total_views']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="fas fa-eye font-size-16"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Đánh giá trung bình</p>
                            <h4 class="mb-0">{{ number_format($overviewStats['average_rating'], 1) }}/5</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="fas fa-star font-size-16"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Views Chart -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Lượt xem theo thời gian</h4>
                </div>
                <div class="card-body">
                    <div id="viewsChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Content Type Stats -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Lượt xem theo loại nội dung</h4>
                </div>
                <div class="card-body">
                    <div id="contentTypeChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Documents -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Tài liệu phổ biến</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tài liệu</th>
                                    <th>Danh mục</th>
                                    <th>Lượt xem</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularDocs as $doc)
                                <tr>
                                    <td>
                                        <h6 class="mb-0">{{ $doc->title }}</h6>
                                        <p class="text-muted mb-0">{{ Str::limit($doc->excerpt, 50) }}</p>
                                    </td>
                                    <td>{{ $doc->category->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ number_format($doc->views_count) }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.documentation.show', $doc) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Hiệu suất theo danh mục</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Danh mục</th>
                                    <th>Tài liệu</th>
                                    <th>Lượt xem</th>
                                    <th>TB/Tài liệu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryStats as $category)
                                <tr>
                                    <td>{{ $category['name'] }}</td>
                                    <td>{{ $category['documents_count'] }}</td>
                                    <td>{{ number_format($category['total_views']) }}</td>
                                    <td>{{ number_format($category['avg_views_per_doc'], 1) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement Stats -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thống kê tương tác người dùng</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5 class="font-size-20">{{ number_format($engagementStats['avg_time_spent'] ?? 0) }}s</h5>
                                <p class="text-muted">Thời gian đọc TB</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5 class="font-size-20">{{ number_format($engagementStats['avg_scroll_percentage'] ?? 0, 1) }}%</h5>
                                <p class="text-muted">Tỷ lệ cuộn TB</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5 class="font-size-20">{{ number_format($engagementStats['bounce_rate'] ?? 0, 1) }}%</h5>
                                <p class="text-muted">Tỷ lệ thoát</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5 class="font-size-20">{{ number_format($engagementStats['return_visitor_rate'] ?? 0, 1) }}%</h5>
                                <p class="text-muted">Người dùng quay lại</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
// Views Chart
var viewsOptions = {
    series: [{
        name: __("common.views"),
        data: @json(array_values($viewsData['daily_views']->toArray()))
    }, {
        name: 'Lượt xem duy nhất',
        data: @json(array_values($viewsData['daily_unique_views']->toArray()))
    }],
    chart: {
        type: 'area',
        height: 350,
        zoom: {
            enabled: false
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth'
    },
    xaxis: {
        categories: @json(array_keys($viewsData['daily_views']->toArray()))
    },
    tooltip: {
        x: {
            format: 'dd/MM/yy'
        },
    },
};

var viewsChart = new ApexCharts(document.querySelector("#viewsChart"), viewsOptions);
viewsChart.render();

// Content Type Chart
var contentTypeOptions = {
    series: @json(array_values($contentTypeStats->toArray())),
    chart: {
        type: 'donut',
        height: 350
    },
    labels: @json(array_keys($contentTypeStats->toArray())),
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

var contentTypeChart = new ApexCharts(document.querySelector("#contentTypeChart"), contentTypeOptions);
contentTypeChart.render();
</script>
@endsection
