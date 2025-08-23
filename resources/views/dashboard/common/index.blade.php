@extends('dashboard.layouts.app')

@section('title', 'Dashboard')

@section('dashboard-content')
<div class="dashboard-home">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="welcome-title">{{ __('dashboard.welcome_back') }}, {{ $currentUser->name }}!</h2>
                <p class="welcome-text text-muted">{{ __('dashboard.welcome_description') }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="welcome-actions">
                    <a href="{{ route('threads.create') }}" class="btn btn-primary me-2">
                        <i class="fas fa-plus"></i> {{ __('dashboard.new_thread') }}
                    </a>
                    {{-- TODO: Implement marketplace permissions --}}
                    {{-- @if($currentUser->hasMarketplacePermission('sell'))
                        <a href="{{ route('dashboard.marketplace.seller.products.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-box"></i> Add Product
                        </a>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats mb-4">
        <div class="row">
            @foreach($quickStats as $stat)
                <div class="col-lg-3 col-md-6 mb-3">
                    @include('dashboard.components.stats-card', $stat)
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Recent Activity -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        {{ __('dashboard.recent_activity') }}
                    </h5>
                    <a href="{{ route('dashboard.activity') }}" class="btn btn-sm btn-outline-primary">
                        {{ __('dashboard.view_all') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        @include('dashboard.components.activity-feed', ['activities' => $recentActivity])
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-line fa-3x mb-3 opacity-50"></i>
                            <p>{{ __('dashboard.no_recent_activity') }}</p>
                            <a href="{{ route('threads.create') }}" class="btn btn-primary">
                                {{ __('dashboard.create_first_thread') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Threads -->
            @if($recentThreads->count() > 0)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-comments me-2"></i>
                            {{ __('dashboard.my_recent_threads') }}
                        </h5>
                        <a href="{{ route('dashboard.community.threads.index') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($recentThreads as $thread)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">
                                            <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">
                                                {{ $thread->title }}
                                            </a>
                                        </div>
                                        <small class="text-muted">
                                            in {{ $thread->forum->name }} • {{ $thread->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="thread-stats">
                                        <span class="badge bg-primary rounded-pill me-1">
                                            {{ $thread->comments_count }} {{ __('dashboard.comments') }}
                                        </span>
                                        <span class="badge bg-secondary rounded-pill">
                                            {{ $thread->likes_count }} {{ __('dashboard.likes') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- TODO: Implement marketplace permissions --}}
            {{-- <!-- Marketplace Activity (if user has marketplace permissions) -->
            @if($currentUser->hasAnyMarketplacePermission() && isset($marketplaceActivity) && $marketplaceActivity->count() > 0)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Marketplace Activity
                        </h5>
                        <a href="{{ route('dashboard.marketplace.orders') }}" class="btn btn-sm btn-outline-primary">
                            View Orders
                        </a>
                    </div>
                    <div class="card-body">
                        @include('dashboard.components.activity-feed', ['activities' => $marketplaceActivity])
                    </div>
                </div>
            @endif --}}
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        {{ __('dashboard.quick_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    @include('dashboard.components.quick-actions')
                </div>
            </div>

            <!-- Recent Notifications -->
            @if($recentNotifications->count() > 0)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bell me-2"></i>
                            Recent Notifications
                        </h5>
                        <a href="{{ route('dashboard.notifications') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($recentNotifications->take(5) as $notification)
                                <div class="list-group-item px-0 {{ !$notification->is_read ? 'bg-light' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($notification->message, 100) }}</p>
                                    @if(!$notification->is_read)
                                        <small class="text-primary">New</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Upcoming Events (placeholder) -->
            @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar me-2"></i>
                            Upcoming Events
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Events content here -->
                    </div>
                </div>
            @endif

            <!-- Help & Support -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Help & Support
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('help.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-book me-2"></i>
                            Documentation
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-envelope me-2"></i>
                            Contact Support
                        </a>
                        <a href="{{ route('faq.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-question me-2"></i>
                            FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.dashboard-home {
    padding: 1.5rem 0;
}

.welcome-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.welcome-title {
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.welcome-text {
    font-size: 1.1rem;
    opacity: 0.9;
}

.welcome-actions .btn {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.quick-stats .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.quick-stats .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.thread-stats .badge {
    font-size: 0.7rem;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endpush
