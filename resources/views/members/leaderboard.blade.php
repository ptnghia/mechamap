@extends('layouts.app')

@section('title', __('messages.members.leaderboard_title'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        {{ __('messages.members.leaderboard_title') }}
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">{{ __('messages.members.leaderboard_description') }}</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Top Posters -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-comments me-2"></i>
                                        Top Contributors (Posts)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @forelse($topPosters as $index => $user)
                                    <div
                                        class="d-flex align-items-center mb-3 @if($index < 3) border-bottom pb-3 @endif">
                                        <div class="me-3">
                                            @if($index == 0)
                                            <span class="badge bg-warning text-dark fs-6">🥇</span>
                                            @elseif($index == 1)
                                            <span class="badge bg-secondary fs-6">🥈</span>
                                            @elseif($index == 2)
                                            <span class="badge bg-dark fs-6">🥉</span>
                                            @else
                                            <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <div class="me-3">
                                            @if($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                                class="rounded-circle" width="40" height="40">
                                            @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white"
                                                style="width: 40px; height: 40px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('profile.show', $user->username) }}"
                                                    class="text-decoration-none">
                                                    {{ $user->name }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">{{ $user->posts_count }} bài viết</small>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted text-center">Chưa có dữ liệu</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Top Thread Creators -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        Top Thread Creators
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @forelse($topThreadCreators as $index => $user)
                                    <div
                                        class="d-flex align-items-center mb-3 @if($index < 3) border-bottom pb-3 @endif">
                                        <div class="me-3">
                                            @if($index == 0)
                                            <span class="badge bg-warning text-dark fs-6">🥇</span>
                                            @elseif($index == 1)
                                            <span class="badge bg-secondary fs-6">🥈</span>
                                            @elseif($index == 2)
                                            <span class="badge bg-dark fs-6">🥉</span>
                                            @else
                                            <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <div class="me-3">
                                            @if($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                                class="rounded-circle" width="40" height="40">
                                            @else
                                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white"
                                                style="width: 40px; height: 40px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('profile.show', $user->username) }}"
                                                    class="text-decoration-none">
                                                    {{ $user->name }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">{{ $user->threads_count }} chủ đề</small>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted text-center">Chưa có dữ liệu</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Most Liked Users -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-heart me-2"></i>
                                        Most Appreciated
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @forelse($topLikedUsers as $index => $user)
                                    <div
                                        class="d-flex align-items-center mb-3 @if($index < 3) border-bottom pb-3 @endif">
                                        <div class="me-3">
                                            @if($index == 0)
                                            <span class="badge bg-warning text-dark fs-6">🥇</span>
                                            @elseif($index == 1)
                                            <span class="badge bg-secondary fs-6">🥈</span>
                                            @elseif($index == 2)
                                            <span class="badge bg-dark fs-6">🥉</span>
                                            @else
                                            <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <div class="me-3">
                                            @if($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                                class="rounded-circle" width="40" height="40">
                                            @else
                                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center text-white"
                                                style="width: 40px; height: 40px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('profile.show', $user->username) }}"
                                                    class="text-decoration-none">
                                                    {{ $user->name }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">{{ $user->total_likes_received ?? 0 }}
                                                likes</small>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted text-center">Chưa có dữ liệu</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Newest Members -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Newest Members
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @forelse($newestMembers as $index => $user)
                                    <div
                                        class="d-flex align-items-center mb-3 @if($index < 3) border-bottom pb-3 @endif">
                                        <div class="me-3">
                                            <span class="badge bg-primary">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="me-3">
                                            @if($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                                class="rounded-circle" width="40" height="40">
                                            @else
                                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark"
                                                style="width: 40px; height: 40px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('profile.show', $user->username) }}"
                                                    class="text-decoration-none">
                                                    {{ $user->name }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                Tham gia {{ $user->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted text-center">Chưa có dữ liệu</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Stats -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-muted mb-3">Thống kê tổng quan cộng đồng MechaMap</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                                <h4 class="text-primary">{{ \App\Models\User::count() }}</h4>
                                                <small class="text-muted">Thành viên</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <i class="fas fa-comments fa-2x text-success mb-2"></i>
                                                <h4 class="text-success">{{ \App\Models\Post::count() }}</h4>
                                                <small class="text-muted">Bài viết</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                                                <h4 class="text-info">{{ \App\Models\Thread::count() }}</h4>
                                                <small class="text-muted">Chủ đề</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <i class="fas fa-user-clock fa-2x text-warning mb-2"></i>
                                                <h4 class="text-warning">{{ \App\Models\User::where('last_seen_at',
                                                    '>=', now()->subHours(24))->count() }}</h4>
                                                <small class="text-muted">Hoạt động 24h</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('members.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-1"></i>
                                    Tất cả thành viên
                                </a>
                                <a href="{{ route('members.online') }}" class="btn btn-outline-success">
                                    <i class="fas fa-circle me-1"></i>
                                    Thành viên online
                                </a>
                                <a href="{{ route('members.staff') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Ban quản trị
                                </a>
                                <a href="{{ route('forums.index') }}" class="btn btn-outline-info">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Quay về diễn đàn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-item {
        padding: 1rem;
    }

    .stat-item i {
        display: block;
    }

    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .badge {
        min-width: 30px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn {
            border-radius: 0.25rem !important;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endsection
