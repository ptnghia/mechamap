@extends('layouts.app')

@section('title', __('nav.user.dashboard') . ' - ' . __('auth.guest_role'))

@section('content')
<div class="container py-4">
    {{-- Welcome Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h2 mb-1 text-white">
                                {{ __('messages.welcome_back') }}, {{ $user->name }}!
                                <span class="wave">👋</span>
                            </h1>
                            <p class="mb-0 opacity-75">
                                {{ __('auth.guest_role_desc') }}
                            </p>
                            <small class="badge bg-light text-primary mt-2">
                                <i class="fas fa-user-tag me-1"></i>
                                {{ __('auth.guest_role') }} ({{ __('messages.level') }} 9)
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

            {{-- Guest Permissions Info --}}
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('messages.your_permissions') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            {{ __('messages.view_content') }}
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            {{ __('messages.follow_users') }}
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            {{ __('messages.marketplace_digital') }}
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-times text-muted me-2"></i>
                            {{ __('messages.create_threads') }}
                        </div>
                        <div class="mb-0">
                            <i class="fas fa-times text-muted me-2"></i>
                            {{ __('messages.post_comments') }}
                        </div>
                    </div>
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
                            <i class="fas fa-heart text-primary fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $stats['following_count'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.following') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <i class="fas fa-users text-success fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $stats['followers_count'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.followers') }}</p>
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
                            <i class="fas fa-store text-warning fa-2x mb-3"></i>
                            <h4 class="mb-1">{{ $stats['marketplace_views'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.marketplace_views') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Marketplace Highlights Widget --}}
            @if(isset($widgets['marketplace_highlights']))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>
                        {{ $widgets['marketplace_highlights']['title'] }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($widgets['marketplace_highlights']['data'] as $product)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $product->name }}</h6>
                                    <p class="card-text small text-muted">{{ Str::limit($product->description, 80) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary fw-bold">{{ number_format($product->price) }} VND</span>
                                        <a href="{{ route('marketplace.products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('messages.view_details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <p>{{ __('messages.no_products_available') }}</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            {{-- Community Activity --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        {{ __('messages.community_activity') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('messages.latest_threads') }}</h6>
                            <div class="list-group list-group-flush">
                                @php
                                $latestThreads = \App\Models\Thread::with(['user', 'forum'])
                                    ->where('status', 'published')
                                    ->latest()
                                    ->limit(5)
                                    ->get();
                                @endphp
                                @forelse($latestThreads as $thread)
                                <a href="{{ route('threads.show', $thread) }}" class="list-group-item list-group-item-action border-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ Str::limit($thread->title, 40) }}</h6>
                                        <small>{{ $thread->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 small text-muted">{{ __('messages.by') }} {{ $thread->user->name }}</p>
                                    <small class="text-muted">{{ $thread->forum->name ?? '' }}</small>
                                </a>
                                @empty
                                <div class="text-center text-muted py-3">
                                    <p class="mb-0">{{ __('messages.no_threads_available') }}</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('messages.trending_topics') }}</h6>
                            <div class="list-group list-group-flush">
                                @php
                                $trendingTopics = ['CAD Design', 'Automation', 'IoT', 'Sustainability', 'Manufacturing'];
                                @endphp
                                @foreach($trendingTopics as $topic)
                                <div class="list-group-item border-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <span class="badge bg-primary">{{ $topic }}</span>
                                        <small class="text-muted">{{ rand(10, 50) }} {{ __('messages.discussions') }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upgrade Suggestion --}}
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-arrow-up me-2"></i>
                        {{ __('messages.upgrade_account') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ __('messages.upgrade_to_member_desc') }}</p>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('messages.member_benefits') }}:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.create_threads') }}</li>
                                <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.post_comments') }}</li>
                                <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.rate_content') }}</li>
                                <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.upload_files') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="{{ route('register') }}" class="btn btn-warning">
                                <i class="fas fa-user-plus me-2"></i>
                                {{ __('messages.upgrade_now') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
</style>
@endpush
