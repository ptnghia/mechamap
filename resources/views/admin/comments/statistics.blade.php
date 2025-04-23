@extends('admin.layouts.app')

@section('title', 'Thống kê bình luận')

@section('header', 'Thống kê bình luận')

@section('actions')
    <a href="{{ route('admin.comments.index') }}" class="btn btn-sm btn-primary">
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
                    <h5 class="card-title mb-0">{{ __('Thống kê theo thời gian') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="timeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Top bài đăng có nhiều bình luận nhất') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Bài đăng') }}</th>
                                    <th>{{ __('Tác giả') }}</th>
                                    <th>{{ __('Số bình luận') }}</th>
                                    <th>{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($threadStats as $stat)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.threads.show', $stat->thread) }}" class="text-decoration-none">
                                                {{ Str::limit($stat->thread->title, 40) }}
                                            </a>
                                        </td>
                                        <td>{{ $stat->thread->user->name }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $stat->total }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.threads.show', $stat->thread) }}" class="btn btn-sm btn-outline-primary">
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
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Top người dùng bình luận nhiều nhất') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Người dùng') }}</th>
                                    <th>{{ __('Số bình luận') }}</th>
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
                labels: ['Bình thường', 'Đã đánh dấu', 'Đã ẩn', 'Bị báo cáo'],
                datasets: [{
                    data: [
                        {{ $statusStats['total'] - $statusStats['flagged'] - $statusStats['hidden'] - $statusStats['reported'] }},
                        {{ $statusStats['flagged'] }},
                        {{ $statusStats['hidden'] }},
                        {{ $statusStats['reported'] }}
                    ],
                    backgroundColor: [
                        'rgba(29, 202, 188, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(255, 120, 70, 0.8)'
                    ],
                    borderColor: [
                        'rgba(29, 202, 188, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(255, 120, 70, 1)'
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
                    label: '{{ __("Số bình luận") }}',
                    data: [
                        @foreach($timeStats as $stat)
                            {{ $stat->total }},
                        @endforeach
                    ],
                    borderColor: 'rgba(51, 102, 204, 1)',
                    backgroundColor: 'rgba(51, 102, 204, 0.1)',
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
