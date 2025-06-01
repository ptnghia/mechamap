@extends('admin.layouts.app')

@section('title', 'Kiểm tra tìm kiếm')
@section('header', 'Kiểm tra tìm kiếm')

@section('actions')
<a href="{{ route('admin.search.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> {{ __('Quay lại cấu hình') }}
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Kiểm tra chức năng tìm kiếm') }}</h5>
            </div>
            <div class="card-body">
                <form id="searchTestForm">
                    <div class="mb-3">
                        <label for="search_query" class="form-label">{{ __('Từ khóa tìm kiếm') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search_query" name="query"
                                placeholder="{{ __('Nhập từ khóa để kiểm tra...') }}" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search me-1"></i> {{ __('Tìm kiếm') }}
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="search_type" class="form-label">{{ __('Loại tìm kiếm') }}</label>
                                <select class="form-select" id="search_type" name="type">
                                    <option value="all">{{ __('Tất cả') }}</option>
                                    <option value="threads">{{ __('Bài đăng') }}</option>
                                    <option value="comments">{{ __('Bình luận') }}</option>
                                    <option value="pages">{{ __('Trang') }}</option>
                                    <option value="users">{{ __('Người dùng') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="results_limit" class="form-label">{{ __('Giới hạn kết quả') }}</label>
                                <select class="form-select" id="results_limit" name="limit">
                                    <option value="10">10 {{ __('kết quả') }}</option>
                                    <option value="20" selected>20 {{ __('kết quả') }}</option>
                                    <option value="50">50 {{ __('kết quả') }}</option>
                                    <option value="100">100 {{ __('kết quả') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_inactive"
                                name="include_inactive" value="1">
                            <label class="form-check-label" for="include_inactive">
                                {{ __('Bao gồm nội dung không hoạt động') }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Results -->
        <div class="card mt-4" id="resultsCard" style="display: none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Kết quả tìm kiếm') }}</h5>
                <div id="resultsInfo" class="text-muted"></div>
            </div>
            <div class="card-body" id="resultsContainer">
                <!-- Results will be loaded here -->
            </div>
        </div>

        <!-- Search Analytics -->
        <div class="card mt-4" id="analyticsCard" style="display: none;">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Phân tích tìm kiếm') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-primary" id="searchTime">-</div>
                            <div class="text-muted">{{ __('Thời gian (ms)') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-success" id="totalResults">-</div>
                            <div class="text-muted">{{ __('Tổng kết quả') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-info" id="indexSize">-</div>
                            <div class="text-muted">{{ __('Kích thước chỉ mục') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-warning" id="relevanceScore">-</div>
                            <div class="text-muted">{{ __('Điểm liên quan') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>{{ __('Chi tiết truy vấn:') }}</h6>
                    <pre id="queryDetails" class="bg-light p-3 rounded"></pre>
                </div>
            </div>
        </div>

        <!-- Performance Test -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Kiểm tra hiệu suất') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ __('Chạy các truy vấn tìm kiếm phổ biến để kiểm tra hiệu suất hệ thống.') }}
                </p>

                <button type="button" class="btn btn-outline-primary" id="runPerformanceTest">
                    <i class="bi bi-speedometer2 me-1"></i> {{ __('Chạy kiểm tra hiệu suất') }}
                </button>

                <div id="performanceResults" class="mt-3" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Truy vấn') }}</th>
                                    <th>{{ __('Thời gian (ms)') }}</th>
                                    <th>{{ __('Kết quả') }}</th>
                                    <th>{{ __('Trạng thái') }}</th>
                                </tr>
                            </thead>
                            <tbody id="performanceTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchTestForm');
    const resultsCard = document.getElementById('resultsCard');
    const analyticsCard = document.getElementById('analyticsCard');
    const resultsContainer = document.getElementById('resultsContainer');
    const resultsInfo = document.getElementById('resultsInfo');
    const performanceTestBtn = document.getElementById('runPerformanceTest');

    // Search functionality
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performSearch();
    });

    function performSearch() {
        const formData = new FormData(searchForm);
        const query = formData.get('query').trim();

        if (!query) {
            alert('{{ __("Vui lòng nhập từ khóa tìm kiếm.") }}');
            return;
        }

        // Show loading
        resultsCard.style.display = 'block';
        analyticsCard.style.display = 'block';
        resultsContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">{{ __("Đang tìm kiếm...") }}</span></div></div>';

        const startTime = performance.now();

        // Build query string
        const params = new URLSearchParams();
        params.append('query', query);
        params.append('type', formData.get('type'));
        params.append('limit', formData.get('limit'));
        if (formData.get('include_inactive')) {
            params.append('include_inactive', '1');
        }

        fetch(`{{ route('admin.search.test.api') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                const endTime = performance.now();
                const searchTime = Math.round(endTime - startTime);

                displayResults(data.results || [], searchTime);
                displayAnalytics(data.analytics || {}, searchTime, query);
            })
            .catch(error => {
                console.error('Search error:', error);
                resultsContainer.innerHTML = `<div class="alert alert-danger">${'{{ __("Có lỗi xảy ra khi thực hiện tìm kiếm.") }}'}</div>`;
            });
    }

    function displayResults(results, searchTime) {
        resultsInfo.textContent = `${results.length} {{ __('kết quả') }} (${searchTime}ms)`;

        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="alert alert-info">{{ __("Không tìm thấy kết quả nào.") }}</div>';
            return;
        }

        let html = '<div class="list-group list-group-flush">';

        results.forEach(result => {
            const typeIcon = getTypeIcon(result.type);
            const typeLabel = getTypeLabel(result.type);

            html += `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">
                            <i class="bi bi-${typeIcon} me-2"></i>
                            ${result.title || result.name}
                        </h6>
                        <small class="text-muted">${typeLabel}</small>
                    </div>
                    <p class="mb-1">${result.excerpt || result.content || ''}</p>
                    <small class="text-muted">
                        {{ __('Điểm số:') }} ${result.score || 0} |
                        {{ __('Ngày tạo:') }} ${result.created_at || '-'}
                        ${result.url ? ` | <a href="${result.url}" target="_blank">{{ __('Xem') }}</a>` : ''}
                    </small>
                </div>
            `;
        });

        html += '</div>';
        resultsContainer.innerHTML = html;
    }

    function displayAnalytics(analytics, searchTime, query) {
        document.getElementById('searchTime').textContent = searchTime;
        document.getElementById('totalResults').textContent = analytics.total_results || 0;
        document.getElementById('indexSize').textContent = analytics.index_size || '-';
        document.getElementById('relevanceScore').textContent = analytics.avg_relevance || '-';

        const queryDetails = {
            query: query,
            processed_query: analytics.processed_query || query,
            search_method: analytics.search_method || 'unknown',
            filters_applied: analytics.filters || [],
            execution_time: searchTime + 'ms'
        };

        document.getElementById('queryDetails').textContent = JSON.stringify(queryDetails, null, 2);
    }

    function getTypeIcon(type) {
        const icons = {
            'thread': 'file-earmark-text',
            'comment': 'chat-dots',
            'page': 'file-richtext',
            'user': 'person'
        };
        return icons[type] || 'search';
    }

    function getTypeLabel(type) {
        const labels = {
            'thread': '{{ __("Bài đăng") }}',
            'comment': '{{ __("Bình luận") }}',
            'page': '{{ __("Trang") }}',
            'user': '{{ __("Người dùng") }}'
        };
        return labels[type] || type;
    }

    // Performance test
    performanceTestBtn.addEventListener('click', function() {
        runPerformanceTest();
    });

    function runPerformanceTest() {
        const testQueries = [
            'laravel',
            'php',
            'javascript',
            'programming',
            'tutorial',
            'example',
            'code',
            'framework'
        ];

        performanceTestBtn.disabled = true;
        performanceTestBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-2" role="status"></div>{{ __("Đang kiểm tra...") }}';

        const resultsContainer = document.getElementById('performanceResults');
        const tableBody = document.getElementById('performanceTableBody');

        resultsContainer.style.display = 'block';
        tableBody.innerHTML = '';

        let completedTests = 0;
        const totalTests = testQueries.length;

        testQueries.forEach((query, index) => {
            const startTime = performance.now();

            fetch(`{{ route('admin.search.test.api') }}?query=${encodeURIComponent(query)}&limit=10`)
                .then(response => response.json())
                .then(data => {
                    const endTime = performance.now();
                    const duration = Math.round(endTime - startTime);

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><code>${query}</code></td>
                        <td>${duration}ms</td>
                        <td>${data.results ? data.results.length : 0}</td>
                        <td><span class="badge bg-success">{{ __('Thành công') }}</span></td>
                    `;
                    tableBody.appendChild(row);
                })
                .catch(error => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><code>${query}</code></td>
                        <td>-</td>
                        <td>-</td>
                        <td><span class="badge bg-danger">{{ __('Lỗi') }}</span></td>
                    `;
                    tableBody.appendChild(row);
                })
                .finally(() => {
                    completedTests++;
                    if (completedTests === totalTests) {
                        performanceTestBtn.disabled = false;
                        performanceTestBtn.innerHTML = '<i class="bi bi-speedometer2 me-1"></i> {{ __("Chạy lại kiểm tra") }}';
                    }
                });
        });
    }

    // Auto-suggest functionality for search input
    const searchInput = document.getElementById('search_query');
    let suggestTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(suggestTimeout);
        const query = this.value.trim();

        if (query.length >= 3) {
            suggestTimeout = setTimeout(() => {
                // You can implement auto-suggest here
                console.log('Auto-suggest for:', query);
            }, 300);
        }
    });
});
</script>
@endpush