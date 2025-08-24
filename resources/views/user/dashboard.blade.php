{{-- User Dashboard - My Content và Moderation Status --}}
@extends('layouts.app')

@section('title', 'Dashboard - Quản lý nội dung của tôi')

@section('content')
<div class="container">
    <div class="row">
        {{-- Sidebar Navigation --}}
        <div class="col-md-3">
            @include('components.user-dashboard-sidebar')
        </div>

        {{-- Main Content --}}
        <div class="col-md-9">
            {{-- Quick Stats Card --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Thống kê nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-2">
                                <i class="fas fa-comments text-primary fa-2x"></i>
                            </div>
                            <h5 class="mb-0">{{ $user->threads_count ?? 0 }}</h5>
                            <small class="text-muted">Threads</small>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <i class="fas fa-comment text-success fa-2x"></i>
                            </div>
                            <h5 class="mb-0">{{ $user->comments_count ?? 0 }}</h5>
                            <small class="text-muted">Comments</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-2">
                                <i class="fas fa-thumbs-up text-warning fa-2x"></i>
                            </div>
                            <h5 class="mb-0">{{ $user->total_likes ?? 0 }}</h5>
                            <small class="text-muted">Likes nhận</small>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <i class="fas fa-star text-info fa-2x"></i>
                            </div>
                            <h5 class="mb-0">{{ number_format($user->average_rating ?? 0, 1) }}</h5>
                            <small class="text-muted">Đánh giá TB</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-md-9">
            {{-- Welcome Banner --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">Chào mừng trở lại, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted mb-0">
                                Quản lý nội dung và theo dõi hoạt động của bạn trên MechaMap
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('threads.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Tạo Thread mới
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Moderation Alerts --}}
            @if($pendingThreadsCount > 0 || $pendingCommentsCount > 0 || $rejectedCount > 0)
            <div class="alert alert-info border-0 mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-lg me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Thông báo về moderation</h6>
                        <div class="mb-0">
                            @if($pendingThreadsCount > 0)
                            <span class="badge bg-warning me-2">{{ $pendingThreadsCount }} thread chờ duyệt</span>
                            @endif
                            @if($pendingCommentsCount > 0)
                            <span class="badge bg-warning me-2">{{ $pendingCommentsCount }} comment chờ duyệt</span>
                            @endif
                            @if($rejectedCount > 0)
                            <span class="badge bg-danger me-2">{{ $rejectedCount }} nội dung bị từ chối</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Overview Statistics --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-comments text-primary fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $statistics['threads']['total'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Tổng threads</p>
                            @if(($statistics['threads']['approved'] ?? 0) > 0)
                            <small class="text-success">
                                {{ $statistics['threads']['approved'] }} đã duyệt
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-comment text-success fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $statistics['comments']['total'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Tổng comments</p>
                            @if(($statistics['comments']['approved'] ?? 0) > 0)
                            <small class="text-success">
                                {{ $statistics['comments']['approved'] }} đã duyệt
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-bookmark text-warning fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $statistics['bookmarks'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Bookmarks</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-eye text-info fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $statistics['total_views'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Lượt xem</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Threads --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-comments me-2"></i>
                            Threads gần đây
                        </h5>
                        <a href="{{ route('dashboard.community.threads') }}" class="btn btn-sm btn-outline-primary">
                            Xem tất cả
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentThreads->count() > 0)
                    @foreach($recentThreads as $thread)
                    <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                        @if($thread->image)
                        <img src="{{ $thread->image }}" alt="Thread image" class="me-3 rounded"
                            style="width: 60px; height: 40px; object-fit: cover;">
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('threads.show', $thread->slug) }}" class="text-decoration-none">
                                    {{ $thread->title }}
                                </a>
                            </h6>
                            <p class="text-muted small mb-2">
                                {{ Str::limit(strip_tags($thread->content), 120) }}
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="badge bg-secondary">{{ $thread->forum->name }}</span>
                                    @php
                                    $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'flagged' => 'bg-danger'
                                    ];
                                    $statusLabels = [
                                    'pending' => 'Chờ duyệt',
                                    'approved' => 'Đã duyệt',
                                    'rejected' => 'Từ chối',
                                    'flagged' => 'Đã báo cáo'
                                    ];
                                    @endphp
                                    <span
                                        class="badge {{ $statusClasses[$thread->moderation_status] ?? 'bg-secondary' }}">
                                        {{ $statusLabels[$thread->moderation_status] ?? $thread->moderation_status }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i>{{ $thread->views_count ?? 0 }}
                                    <i class="fas fa-comment ms-2 me-1"></i>{{ $thread->comments_count ?? 0 }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Chưa có threads nào</h6>
                        <p class="text-muted">Tạo thread đầu tiên của bạn để chia sẻ với cộng đồng!</p>
                        <a href="{{ route('threads.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Tạo Thread
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Recent Comments --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-comment me-2"></i>
                            Comments gần đây
                        </h5>
                        <a href="{{ route('dashboard.community.comments') }}" class="btn btn-sm btn-outline-primary">
                            Xem tất cả
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentComments->count() > 0)
                    @foreach($recentComments as $comment)
                    <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">
                                    <a href="{{ route('threads.show', $comment->thread->slug) }}#comment-{{ $comment->id }}"
                                        class="text-decoration-none">
                                        {{ Str::limit($comment->thread->title, 60) }}
                                    </a>
                                </h6>
                                @php
                                $statusClasses = [
                                'pending' => 'bg-warning',
                                'approved' => 'bg-success',
                                'rejected' => 'bg-danger',
                                'flagged' => 'bg-danger'
                                ];
                                $statusLabels = [
                                'pending' => 'Chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                                'flagged' => 'Đã báo cáo'
                                ];
                                @endphp
                                <span class="badge {{ $statusClasses[$comment->moderation_status] ?? 'bg-secondary' }}">
                                    {{ $statusLabels[$comment->moderation_status] ?? $comment->moderation_status }}
                                </span>
                            </div>
                            <p class="text-muted mb-2">
                                {{ Str::limit(strip_tags($comment->content), 150) }}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $comment->created_at->diffForHumans() }}
                                @if($comment->parent_id)
                                <i class="fas fa-reply ms-2 me-1"></i>
                                Trả lời comment
                                @endif
                            </small>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-comment fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Chưa có comments nào</h6>
                        <p class="text-muted">Tham gia thảo luận bằng cách comment vào các threads!</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Activity Timeline --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Hoạt động gần đây
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                @if($activity->type === 'thread_created')
                                <i class="fas fa-plus text-primary"></i>
                                @elseif($activity->type === 'comment_created')
                                <i class="fas fa-comment text-success"></i>
                                @elseif($activity->type === 'thread_approved')
                                <i class="fas fa-check text-success"></i>
                                @elseif($activity->type === 'thread_rejected')
                                <i class="fas fa-times text-danger"></i>
                                @else
                                <i class="fas fa-circle text-muted"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $activity->title }}</h6>
                                <p class="timeline-description">{{ $activity->description }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $activity->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Chưa có hoạt động nào</h6>
                        <p class="text-muted">Hoạt động của bạn sẽ được hiển thị ở đây.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- End Main Content --}}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -37px;
        top: 5px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: white;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #007bff;
    }

    .timeline-title {
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: 600;
    }

    .timeline-description {
        margin-bottom: 5px;
        font-size: 13px;
        color: #6c757d;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        background-color: rgba(0, 123, 255, 0.05);
        border-bottom: 1px solid rgba(0, 123, 255, 0.1);
    }
</style>
@endpush
