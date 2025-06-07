@extends('layouts.admin')

@section('title', 'Moderation Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-shield-alt me-2"></i>
                    Dashboard Moderation
                </h1>
                <div class="page-subtitle">Quản lý và kiểm duyệt nội dung diễn đàn</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['threads']['pending'] ?? 0 }}</h3>
                            <p class="mb-0">Threads Chờ Duyệt</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.moderation.threads', ['status' => 'pending']) }}"
                        class="text-white text-decoration-none">
                        <i class="fas fa-arrow-right me-1"></i>Xem chi tiết
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['threads']['flagged'] ?? 0 }}</h3>
                            <p class="mb-0">Threads Bị Báo Cáo</p>
                        </div>
                        <i class="fas fa-flag fa-2x opacity-75"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.moderation.threads', ['status' => 'flagged']) }}"
                        class="text-white text-decoration-none">
                        <i class="fas fa-arrow-right me-1"></i>Xem chi tiết
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['comments']['pending'] ?? 0 }}</h3>
                            <p class="mb-0">Comments Chờ Duyệt</p>
                        </div>
                        <i class="fas fa-comments fa-2x opacity-75"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.moderation.comments', ['status' => 'pending']) }}"
                        class="text-white text-decoration-none">
                        <i class="fas fa-arrow-right me-1"></i>Xem chi tiết
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $todayActivity['approved_threads'] + $todayActivity['approved_comments']
                                }}</h3>
                            <p class="mb-0">Đã Duyệt Hôm Nay</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="text-white-50">
                        {{ $todayActivity['approved_threads'] }} threads, {{ $todayActivity['approved_comments'] }}
                        comments
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Threads -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Threads Cần Xử Lý
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentThreads->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentThreads as $thread)
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('admin.moderation.threads') }}?thread_id={{ $thread->id }}"
                                        class="text-decoration-none">
                                        {{ Str::limit($thread->title, 50) }}
                                    </a>
                                </h6>
                                <p class="mb-1 text-muted small">
                                    Bởi: {{ $thread->user->name }} trong {{ $thread->forum->name }}
                                </p>
                                <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                            </div>
                            <span
                                class="badge bg-{{ $thread->moderation_status === 'pending' ? 'warning' : 'danger' }}">
                                {{ ucfirst($thread->moderation_status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.moderation.threads') }}" class="btn btn-outline-primary btn-sm">
                            Xem Tất Cả Threads
                        </a>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">Không có threads nào cần xử lý!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comment-dots me-2"></i>
                        Comments Cần Xử Lý
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentComments->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentComments as $comment)
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('admin.moderation.comments') }}?comment_id={{ $comment->id }}"
                                        class="text-decoration-none">
                                        {{ Str::limit($comment->content, 50) }}
                                    </a>
                                </h6>
                                <p class="mb-1 text-muted small">
                                    Bởi: {{ $comment->user->name }} trong "{{ Str::limit($comment->thread->title, 30)
                                    }}"
                                </p>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <span
                                class="badge bg-{{ $comment->moderation_status === 'pending' ? 'warning' : 'danger' }}">
                                {{ ucfirst($comment->moderation_status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.moderation.comments') }}" class="btn btn-outline-primary btn-sm">
                            Xem Tất Cả Comments
                        </a>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">Không có comments nào cần xử lý!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Hoạt Động Moderation Hôm Nay
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-success mb-1">{{ $todayActivity['approved_threads'] }}</h4>
                                <small class="text-muted">Threads Đã Duyệt</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-danger mb-1">{{ $todayActivity['rejected_threads'] }}</h4>
                                <small class="text-muted">Threads Bị Từ Chối</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-success mb-1">{{ $todayActivity['approved_comments'] }}</h4>
                                <small class="text-muted">Comments Đã Duyệt</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-danger mb-1">{{ $todayActivity['rejected_comments'] }}</h4>
                                <small class="text-muted">Comments Bị Từ Chối</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto refresh statistics every 30 seconds
setInterval(function() {
    fetch('{{ route("admin.moderation.statistics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update statistics cards
                // This can be expanded to update the numbers dynamically
                console.log('Statistics updated', data.data);
            }
        })
        .catch(error => console.log('Error fetching statistics:', error));
}, 30000);
</script>
@endpush
@endsection