@extends('layouts.app')

@section('title', __('nav.user.dashboard') . ' - ' . __('auth.member_role'))

@section('content')
<div class="container py-4">
    {{-- Welcome Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h2 mb-1 text-white">
                                {{ __('messages.welcome_back') }}, {{ $user->name }}! 
                                <span class="wave">👋</span>
                            </h1>
                            <p class="mb-0 opacity-75">
                                {{ __('auth.member_role_desc') }}
                            </p>
                            <small class="badge bg-light text-success mt-2">
                                <i class="fas fa-user-tag me-1"></i>
                                {{ __('auth.member_role') }} ({{ __('messages.level') }} 8)
                            </small>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                                @foreach($quick_actions as $action)
                                <a href="{{ route($action['route']) }}" class="btn {{ $action['class'] }} btn-sm">
                                    <i class="{{ $action['icon'] }} me-1"></i>
                                    {{ $action['title'] }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Sidebar Navigation --}}
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>
                        {{ __('nav.user.dashboard') }}
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($navigation as $key => $item)
                    <a href="{{ route($item['route']) }}" 
                        class="list-group-item list-group-item-action {{ $item['active'] ?? false ? 'active' : '' }}">
                        <i class="{{ $item['icon'] }} me-2"></i>
                        {{ $item['title'] }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Activity Level Badge --}}
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        {{ __('messages.activity_level') }}
                    </h6>
                </div>
                <div class="card-body text-center">
                    @php
                    $activityLevel = $stats['forum_activity_level'] ?? 'new';
                    $activityConfig = [
                        'very_active' => ['badge' => 'success', 'icon' => 'fire', 'text' => 'Rất tích cực'],
                        'active' => ['badge' => 'primary', 'icon' => 'star', 'text' => 'Tích cực'],
                        'moderate' => ['badge' => 'warning', 'icon' => 'thumbs-up', 'text' => 'Vừa phải'],
                        'new' => ['badge' => 'secondary', 'icon' => 'seedling', 'text' => 'Mới bắt đầu']
                    ];
                    $config = $activityConfig[$activityLevel];
                    @endphp
                    <div class="mb-3">
                        <i class="fas fa-{{ $config['icon'] }} fa-3x text-{{ $config['badge'] }}"></i>
                    </div>
                    <span class="badge bg-{{ $config['badge'] }} fs-6">{{ $config['text'] }}</span>
                    <p class="small text-muted mt-2 mb-0">
                        {{ __('messages.reputation_score') }}: {{ $stats['reputation_score'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-md-9">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center border-primary">
                        <div class="card-body">
                            <i class="fas fa-comments text-primary fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $stats['threads_created'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.threads_created') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <i class="fas fa-comment text-success fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $stats['comments_count'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.comments_count') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <i class="fas fa-bookmark text-info fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $stats['threads_bookmarked'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.bookmarks') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <i class="fas fa-star text-warning fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ number_format($stats['average_rating_received'] ?? 0, 1) }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.avg_rating') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Threads Widget --}}
            @if(isset($widgets['recent_threads']))
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        {{ $widgets['recent_threads']['title'] }}
                    </h5>
                    <a href="{{ route('user.my-threads') }}" class="btn btn-sm btn-outline-primary">
                        {{ __('messages.view_all') }}
                    </a>
                </div>
                <div class="card-body">
                    @forelse($widgets['recent_threads']['data'] as $thread)
                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">
                                    {{ $thread->title }}
                                </a>
                            </h6>
                            <p class="text-muted small mb-1">{{ Str::limit($thread->content, 100) }}</p>
                            <div class="d-flex gap-3 small text-muted">
                                <span><i class="fas fa-folder me-1"></i>{{ $thread->forum->name ?? '' }}</span>
                                <span><i class="fas fa-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}</span>
                                <span><i class="fas fa-comments me-1"></i>{{ $thread->allComments->count() ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($thread->status === 'published')
                            <span class="badge bg-success">{{ __('messages.published') }}</span>
                            @elseif($thread->status === 'pending')
                            <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                            @else
                            <span class="badge bg-secondary">{{ $thread->status }}</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>{{ __('messages.no_threads_yet') }}</p>
                        <a href="{{ route('threads.create') }}" class="btn btn-primary">
                            {{ __('messages.create_first_thread') }}
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Community Engagement --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>
                                {{ __('messages.achievements') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                            $achievements = [
                                ['name' => 'First Thread', 'icon' => 'medal', 'earned' => $stats['threads_created'] > 0],
                                ['name' => 'Active Commenter', 'icon' => 'comment', 'earned' => $stats['comments_count'] > 10],
                                ['name' => 'Bookworm', 'icon' => 'bookmark', 'earned' => $stats['threads_bookmarked'] > 5],
                                ['name' => 'Well Rated', 'icon' => 'star', 'earned' => ($stats['average_rating_received'] ?? 0) > 4],
                            ];
                            @endphp
                            @foreach($achievements as $achievement)
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-{{ $achievement['icon'] }} me-3 {{ $achievement['earned'] ? 'text-warning' : 'text-muted' }}"></i>
                                <span class="{{ $achievement['earned'] ? '' : 'text-muted' }}">{{ $achievement['name'] }}</span>
                                @if($achievement['earned'])
                                <i class="fas fa-check text-success ms-auto"></i>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                {{ __('messages.forum_participation') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                            $totalActivity = ($stats['threads_created'] ?? 0) + ($stats['comments_count'] ?? 0);
                            $threadPercentage = $totalActivity > 0 ? (($stats['threads_created'] ?? 0) / $totalActivity) * 100 : 0;
                            $commentPercentage = 100 - $threadPercentage;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small">{{ __('messages.threads') }}</span>
                                    <span class="small">{{ number_format($threadPercentage, 1) }}%</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $threadPercentage }}%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small">{{ __('messages.comments') }}</span>
                                    <span class="small">{{ number_format($commentPercentage, 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $commentPercentage }}%"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <small class="text-muted">{{ __('messages.total_contributions') }}: {{ $totalActivity }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upgrade to Senior Member --}}
            @if($role === 'member')
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-arrow-up me-2"></i>
                        {{ __('messages.upgrade_to_senior') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ __('messages.senior_member_benefits_desc') }}</p>
                    <div class="row">
                        <div class="col-md-8">
                            <h6>{{ __('messages.requirements') }}:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-1">
                                    <i class="fas fa-{{ $stats['threads_created'] >= 10 ? 'check text-success' : 'times text-muted' }} me-2"></i>
                                    {{ __('messages.create_10_threads') }} ({{ $stats['threads_created'] }}/10)
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-{{ $stats['comments_count'] >= 50 ? 'check text-success' : 'times text-muted' }} me-2"></i>
                                    {{ __('messages.post_50_comments') }} ({{ $stats['comments_count'] }}/50)
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-{{ ($stats['average_rating_received'] ?? 0) >= 4 ? 'check text-success' : 'times text-muted' }} me-2"></i>
                                    {{ __('messages.maintain_4_star_rating') }} ({{ number_format($stats['average_rating_received'] ?? 0, 1) }}/5.0)
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if($stats['threads_created'] >= 10 && $stats['comments_count'] >= 50 && ($stats['average_rating_received'] ?? 0) >= 4)
                            <a href="{{ route('profile.upgrade') }}" class="btn btn-info">
                                <i class="fas fa-crown me-2"></i>
                                {{ __('messages.upgrade_now') }}
                            </a>
                            @else
                            <button class="btn btn-outline-info" disabled>
                                <i class="fas fa-hourglass-half me-2"></i>
                                {{ __('messages.keep_contributing') }}
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.wave {
    animation: wave 2s infinite;
    transform-origin: 70% 70%;
    display: inline-block;
}

@keyframes wave {
    0% { transform: rotate(0deg); }
    10% { transform: rotate(14deg); }
    20% { transform: rotate(-8deg); }
    30% { transform: rotate(14deg); }
    40% { transform: rotate(-4deg); }
    50% { transform: rotate(10deg); }
    60% { transform: rotate(0deg); }
    100% { transform: rotate(0deg); }
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}
</style>
@endpush
