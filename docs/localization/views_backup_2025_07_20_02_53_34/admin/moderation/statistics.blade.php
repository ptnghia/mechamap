@extends('admin.layouts.dason')

@section('title', 'Báo Cáo Nội Dung')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">📊 Báo Cáo Nội Dung</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Báo Cáo</a></li>
                        <li class="breadcrumb-item active">Báo Cáo Nội Dung</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng Threads</p>
                            <h4 class="mb-0">{{ $threadsByType->sum('count') }}</h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-comments font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Threads Đã Duyệt</p>
                            <h4 class="mb-0">{{ $threadsByStatus->where('moderation_status', 'approved')->first()->count ?? 0 }}</h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Threads Chờ Duyệt</p>
                            <h4 class="mb-0">{{ $threadsByStatus->where('moderation_status', 'pending')->first()->count ?? 0 }}</h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Top Rated Threads</p>
                            <h4 class="mb-0">{{ $topRatedThreads->count() }}</h4>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-star font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Threads by Type -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">📊 Phân Bố Theo Loại Thread</h4>
                </div>
                <div class="card-body">
                    <canvas id="threadsByTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Threads by Status -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">🔍 Phân Bố Theo Trạng Thái</h4>
                </div>
                <div class="card-body">
                    <canvas id="threadsByStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quality Distribution -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">⭐ Phân Bố Chất Lượng Nội Dung</h4>
                </div>
                <div class="card-body">
                    <canvas id="qualityDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Rating Trends -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">📈 Xu Hướng Đánh Giá</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Đánh Giá TB</th>
                                    <th>Tổng Đánh Giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ratingTrends as $trend)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($trend->date)->format('d/m') }}</td>
                                    <td>
                                        <span class="badge badge-soft-primary">
                                            {{ number_format($trend->avg_rating, 1) }}
                                        </span>
                                    </td>
                                    <td>{{ $trend->total_ratings }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Rated Threads -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">🏆 Top Threads Được Đánh Giá Cao</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Tiêu Đề</th>
                                    <th>Tác Giả</th>
                                    <th>Đánh Giá TB</th>
                                    <th>Số Đánh Giá</th>
                                    <th>Ngày Tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topRatedThreads as $thread)
                                <tr>
                                    <td>
                                        <a href="{{ route('threads.show', $thread->slug) }}" target="_blank" class="text-body fw-bold">
                                            {{ Str::limit($thread->title, 50) }}
                                        </a>
                                    </td>
                                    <td>{{ $thread->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-soft-success me-1">
                                                {{ number_format($thread->average_rating, 1) }}
                                            </span>
                                            <i class="fas fa-star text-warning"></i>
                                        </div>
                                    </td>
                                    <td>{{ $thread->ratings_count }}</td>
                                    <td>{{ $thread->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không có threads được đánh giá cao</td>
                                </tr>
                                @endforelse
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
<script src="{{ asset('assets/libs/chart.js/chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Threads by Type Chart
    const threadsByTypeData = @json($threadsByType);
    const typeCtx = document.getElementById('threadsByTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: threadsByTypeData.map(item => item.thread_type || 'Khác'),
            datasets: [{
                data: threadsByTypeData.map(item => item.count),
                backgroundColor: ['#556ee6', '#34c38f', '#f1b44c', '#50a5f1', '#f46a6a']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Threads by Status Chart
    const threadsByStatusData = @json($threadsByStatus);
    const statusCtx = document.getElementById('threadsByStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: threadsByStatusData.map(item => item.moderation_status || 'Khác'),
            datasets: [{
                label: 'Số lượng',
                data: threadsByStatusData.map(item => item.count),
                backgroundColor: '#556ee6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Quality Distribution Chart
    const qualityData = @json($qualityDistribution);
    const qualityCtx = document.getElementById('qualityDistributionChart').getContext('2d');
    new Chart(qualityCtx, {
        type: 'pie',
        data: {
            labels: qualityData.map(item => item.quality_range),
            datasets: [{
                data: qualityData.map(item => item.count),
                backgroundColor: ['#34c38f', '#556ee6', '#f1b44c', '#f46a6a', '#50a5f1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endsection
