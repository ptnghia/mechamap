@extends('admin.layouts.app')

@section('title', 'Thống kê bài đăng')

@section('header', 'Thống kê bài đăng')

@section('actions')
    <a href="{{ route('admin.threads.index') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-arrow-left me-1"></i> {{ __('Quay lại danh sách') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thống kê theo trạng thái') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thống kê theo diễn đàn') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="forumChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thống kê theo chuyên mục') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thống kê theo thời gian') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="timeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Top người dùng đăng bài') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Người dùng') }}</th>
                            <th>{{ __('Số bài đăng') }}</th>
                            <th>{{ __('Tỷ lệ') }}</th>
                            <th>{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userStats as $stat)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $stat->user->getAvatarUrl() }}" alt="{{ $stat->user->name }}" class="rounded-circle me-2" width="32" height="32">
                                        <div>
                                            <div class="fw-bold">{{ $stat->user->name }}</div>
                                            <div class="small text-muted">{{ '@' . $stat->user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $stat->total }}</td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ ($stat->total / $userStats->max('total')) * 100 }}%;" aria-valuenow="{{ $stat->total }}" aria-valuemin="0" aria-valuemax="{{ $userStats->max('total') }}"></div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $stat->user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> {{ __('Xem') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Biểu đồ thống kê theo trạng thái
        const statusChartCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusChartCtx, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($statusStats as $stat)
                        '{{ ucfirst($stat->status) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($statusStats as $stat)
                            {{ $stat->total }},
                        @endforeach
                    ],
                    backgroundColor: [
                        'rgba(29, 202, 188, 0.8)',
                        'rgba(51, 102, 204, 0.8)',
                        'rgba(255, 120, 70, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        'rgba(29, 202, 188, 1)',
                        'rgba(51, 102, 204, 1)',
                        'rgba(255, 120, 70, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Biểu đồ thống kê theo diễn đàn
        const forumChartCtx = document.getElementById('forumChart').getContext('2d');
        const forumChart = new Chart(forumChartCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($forumStats as $stat)
                        '{{ $stat->forum->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: '{{ __("Số bài đăng") }}',
                    data: [
                        @foreach($forumStats as $stat)
                            {{ $stat->total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(51, 102, 204, 0.8)',
                    borderColor: 'rgba(51, 102, 204, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Biểu đồ thống kê theo chuyên mục
        const categoryChartCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryChartCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($categoryStats as $stat)
                        '{{ $stat->category->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: '{{ __("Số bài đăng") }}',
                    data: [
                        @foreach($categoryStats as $stat)
                            {{ $stat->total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(29, 202, 188, 0.8)',
                    borderColor: 'rgba(29, 202, 188, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Biểu đồ thống kê theo thời gian
        const timeChartCtx = document.getElementById('timeChart').getContext('2d');
        const timeChart = new Chart(timeChartCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($timeStats as $stat)
                        '{{ date("m/Y", mktime(0, 0, 0, $stat->month, 1, date("Y"))) }}',
                    @endforeach
                ],
                datasets: [{
                    label: '{{ __("Số bài đăng") }}',
                    data: [
                        @foreach($timeStats as $stat)
                            {{ $stat->total }},
                        @endforeach
                    ],
                    borderColor: 'rgba(255, 120, 70, 1)',
                    backgroundColor: 'rgba(255, 120, 70, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
