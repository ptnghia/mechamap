@extends('admin.layouts.dason')

@section('title', 'Thống kê tìm kiếm')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Thống kê tìm kiếm</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Thống kê tìm kiếm</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.search.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Quay lại cấu hình') }}
    </a>
    <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportAnalytics()">
        <i class="fas fa-download me-1"></i> {{ __('Xuất báo cáo') }}
    </button>
</div>
@endsection

@section('content')
<!-- Overview Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 text-primary">{{ number_format($stats['total_searches'] ?? 0) }}</div>
                <div class="text-muted">{{ __('Tổng số tìm kiếm') }}</div>
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> {{ $stats['searches_growth'] ?? 0 }}% {{ __('so với tháng trước') }}
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 text-success">{{ number_format($stats['unique_queries'] ?? 0) }}</div>
                <div class="text-muted">{{ __('Từ khóa duy nhất') }}</div>
                <small class="text-info">
                    {{ __('Trung bình') }} {{ $stats['avg_query_length'] ?? 0 }} {{ __('ký tự') }}
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 text-info">{{ $stats['avg_results'] ?? 0 }}</div>
                <div class="text-muted">{{ __('Kết quả TB/tìm kiếm') }}</div>
                <small class="text-warning">
                    {{ $stats['zero_results_rate'] ?? 0 }}% {{ __('không có kết quả') }}
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 text-warning">{{ $stats['avg_response_time'] ?? 0 }}ms</div>
                <div class="text-muted">{{ __('Thời gian phản hồi TB') }}</div>
                <small class="text-{{ $stats['response_time_trend'] === 'up' ? 'danger' : 'success' }}">
                    <i class="fas fa-arrow-{{ $stats['response_time_trend'] === 'up' ? 'up' : 'down' }}"></i>
                    {{ $stats['response_time_change'] ?? 0 }}ms
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Search Trends Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Xu hướng tìm kiếm') }}</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="timeRange" id="day7" value="7" checked>
                    <label class="btn btn-outline-primary" for="day7">7 {{ __('ngày') }}</label>

                    <input type="radio" class="btn-check" name="timeRange" id="day30" value="30">
                    <label class="btn btn-outline-primary" for="day30">30 {{ __('ngày') }}</label>

                    <input type="radio" class="btn-check" name="timeRange" id="day90" value="90">
                    <label class="btn btn-outline-primary" for="day90">90 {{ __('ngày') }}</label>
                </div>
            </div>
            <div class="card-body">
                <canvas id="searchTrendsChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Search Terms -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Từ khóa phổ biến') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($topQueries ?? [] as $index => $query)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">{{ $query->term }}</div>
                            <small class="text-muted">{{ $query->avg_results }} {{ __('kết quả TB') }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary rounded-pill">{{ $query->count }}</span>
                            <div class="text-muted small">{{ $query->percentage }}%</div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted">
                        {{ __('Chưa có dữ liệu') }}
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Search Categories -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Phân loại tìm kiếm') }}</h5>
            </div>
            <div class="card-body">
                <canvas id="categoriesChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- User Behavior -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Hành vi người dùng') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="h4 text-primary">{{ $userBehavior['click_through_rate'] ?? 0 }}%</div>
                            <div class="text-muted small">{{ __('Tỷ lệ click-through') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="h4 text-success">{{ $userBehavior['bounce_rate'] ?? 0 }}%</div>
                            <div class="text-muted small">{{ __('Tỷ lệ thoát') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="h4 text-info">{{ $userBehavior['avg_session_searches'] ?? 0 }}</div>
                            <div class="text-muted small">{{ __('Tìm kiếm/phiên') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="h4 text-warning">{{ $userBehavior['refinement_rate'] ?? 0 }}%</div>
                            <div class="text-muted small">{{ __('Tỷ lệ tinh chỉnh') }}</div>
                        </div>
                    </div>
                </div>

                <div class="progress-stacked">
                    <div class="progress" role="progressbar"
                        style="width: {{ $userBehavior['mobile_searches'] ?? 0 }}%">
                        <div class="progress-bar bg-primary">{{ $userBehavior['mobile_searches'] ?? 0 }}%</div>
                    </div>
                    <div class="progress" role="progressbar"
                        style="width: {{ $userBehavior['desktop_searches'] ?? 0 }}%">
                        <div class="progress-bar bg-success">{{ $userBehavior['desktop_searches'] ?? 0 }}%</div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">Mobile</small>
                    <small class="text-muted">Desktop</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Failed Searches -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Tìm kiếm thất bại') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Từ khóa') }}</th>
                                <th>{{ __('Số lần') }}</th>
                                <th>{{ __('Hành động') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($failedSearches ?? [] as $search)
                            <tr>
                                <td>{{ $search->term }}</td>
                                <td><span class="badge bg-danger">{{ $search->count }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="suggestContent('{{ $search->term }}')">
                                        {{ __('Gợi ý nội dung') }}
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">{{ __('Không có dữ liệu') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Performance -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Hiệu suất tìm kiếm') }}</h5>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Searches -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Tìm kiếm gần đây') }}</h5>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                    <label class="form-check-label" for="autoRefresh">
                        {{ __('Tự động cập nhật') }}
                    </label>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Thời gian') }}</th>
                                <th>{{ __('Từ khóa') }}</th>
                                <th>{{ __('Người dùng') }}</th>
                                <th>{{ __('Kết quả') }}</th>
                                <th>{{ __('Thời gian phản hồi') }}</th>
                                <th>{{ __('IP') }}</th>
                            </tr>
                        </thead>
                        <tbody id="recentSearchesTable">
                            @forelse($recentSearches ?? [] as $search)
                            <tr>
                                <td>{{ $search->created_at->format('H:i:s') }}</td>
                                <td><code>{{ $search->query }}</code></td>
                                <td>{{ $search->user ? $search->user->name : __('Khách') }}</td>
                                <td><span class="badge bg-{{ $search->results_count > 0 ? 'success' : 'warning' }}">{{
                                        $search->results_count }}</span></td>
                                <td>{{ $search->response_time }}ms</td>
                                <td><small class="text-muted">{{ $search->ip_address }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">{{ __('Chưa có tìm kiếm nào') }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Search Trends Chart
    const trendsCtx = document.getElementById('searchTrendsChart').getContext('2d');
    const trendsChart = new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['trends']['labels'] ?? []),
            datasets: [{
                label: '{{ __("Số lượng tìm kiếm") }}',
                data: @json($chartData['trends']['data'] ?? []),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    const categoriesChart = new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chartData['categories']['labels'] ?? []),
            datasets: [{
                data: @json($chartData['categories']['data'] ?? []),
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#fd7e14'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: @json($chartData['performance']['labels'] ?? []),
            datasets: [{
                label: '{{ __("Thời gian phản hồi (ms)") }}',
                data: @json($chartData['performance']['data'] ?? []),
                backgroundColor: '#ffc107',
                borderColor: '#ffca2c',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Time range selector
    const timeRangeInputs = document.querySelectorAll('input[name="timeRange"]');
    timeRangeInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked) {
                updateTrendsChart(this.value);
            }
        });
    });

    function updateTrendsChart(days) {
        fetch(`{{ route('admin.search.analytics.api') }}?days=${days}`)
            .then(response => response.json())
            .then(data => {
                trendsChart.data.labels = data.trends.labels;
                trendsChart.data.datasets[0].data = data.trends.data;
                trendsChart.update();
            })
            .catch(error => {
                console.error('Error updating chart:', error);
            });
    }

    // Auto refresh recent searches
    const autoRefreshCheckbox = document.getElementById('autoRefresh');
    let refreshInterval;

    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            if (autoRefreshCheckbox.checked) {
                refreshRecentSearches();
            }
        }, 30000); // Refresh every 30 seconds
    }

    function refreshRecentSearches() {
        fetch('{{ route("admin.search.analytics.recent") }}')
            .then(response => response.json())
            .then(data => {
                updateRecentSearchesTable(data.searches);
            })
            .catch(error => {
                console.error('Error refreshing searches:', error);
            });
    }

    function updateRecentSearchesTable(searches) {
        const tableBody = document.getElementById('recentSearchesTable');

        if (searches.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">{{ __("Chưa có tìm kiếm nào") }}</td></tr>';
            return;
        }

        let html = '';
        searches.forEach(search => {
            const badgeClass = search.results_count > 0 ? 'success' : 'warning';
            html += `
                <tr>
                    <td>${search.created_at}</td>
                    <td><code>${search.query}</code></td>
                    <td>${search.user || '{{ __("Khách") }}'}</td>
                    <td><span class="badge bg-${badgeClass}">${search.results_count}</span></td>
                    <td>${search.response_time}ms</td>
                    <td><small class="text-muted">${search.ip_address}</small></td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;
    }

    autoRefreshCheckbox.addEventListener('change', function() {
        if (this.checked) {
            startAutoRefresh();
        } else {
            clearInterval(refreshInterval);
        }
    });

    // Start auto refresh if enabled
    if (autoRefreshCheckbox.checked) {
        startAutoRefresh();
    }
});

// Export analytics function
function exportAnalytics() {
    const timeRange = document.querySelector('input[name="timeRange"]:checked').value;
    window.open(`{{ route('admin.search.analytics.export') }}?days=${timeRange}`, '_blank');
}

// Suggest content for failed searches
function suggestContent(term) {
    if (confirm(`{{ __("Bạn có muốn tạo nội dung cho từ khóa") }} "${term}"?`)) {
        // Redirect to content creation with pre-filled keyword
        window.open(`{{ route('admin.pages.create') }}?suggested_keyword=${encodeURIComponent(term)}`, '_blank');
    }
}
</script>
@endpush
