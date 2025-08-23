@extends('layouts.app')

@section('title', __('ui.leaderboard.title'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3"><i class="fas fa-trophy text-warning"></i> {{ __('ui.leaderboard.title') }}</h1>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('ui.leaderboard.back_to_list') }}
                </a>
            </div>

            <!-- Leaderboard Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#top-posters">
                        <i class="fas fa-comment"></i> {{ __('ui.leaderboard.top_posts') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#top-thread-creators">
                        <i class="fas fa-file-alt"></i> {{ __('ui.leaderboard.top_threads') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#top-followed">
                        <i class="fas fa-users"></i> {{ __('ui.leaderboard.top_followed') }}
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Top Posters -->
                <div class="tab-pane fade show active" id="top-posters">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('ui.leaderboard.top_posts_description') }}</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($topPosters as $index => $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($index === 0)
                                                <i class="fas fa-crown text-warning fa-2x"></i>
                                            @elseif($index === 1)
                                                <i class="fas fa-medal text-secondary fa-2x"></i>
                                            @elseif($index === 2)
                                                <i class="fas fa-award text-warning fa-2x"></i>
                                            @else
                                                <span class="badge bg-primary fs-5">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}"
                                             class="rounded-circle me-3" width="48" height="48"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary text-white fw-bold"
                                             style="width: 48px; height: 48px; font-size: 18px; display: none;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('profile.show', $user->username) }}"
                                                   class="text-decoration-none">{{ $user->name }}</a>
                                            </h6>
                                            <div class="text-muted small">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary fs-5">{{ number_format($user->comments_count) }}</div>
                                        <div class="small text-muted">{{ __('ui.leaderboard.posts') }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-4">
                                    <i class="fas fa-comment fa-3x text-muted mb-3"></i>
                                    <h5>{{ __('ui.leaderboard.no_data') }}</h5>
                                    <p class="text-muted">{{ __('ui.leaderboard.no_posts_yet') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Top Thread Creators -->
                <div class="tab-pane fade" id="top-thread-creators">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('ui.leaderboard.top_threads_description') }}</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($topThreadCreators as $index => $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($index === 0)
                                                <i class="fas fa-crown text-warning fa-2x"></i>
                                            @elseif($index === 1)
                                                <i class="fas fa-medal text-secondary fa-2x"></i>
                                            @elseif($index === 2)
                                                <i class="fas fa-award text-warning fa-2x"></i>
                                            @else
                                                <span class="badge bg-primary fs-5">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}"
                                             class="rounded-circle me-3" width="48" height="48"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-success text-white fw-bold"
                                             style="width: 48px; height: 48px; font-size: 18px; display: none;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('profile.show', $user->username) }}"
                                                   class="text-decoration-none">{{ $user->name }}</a>
                                            </h6>
                                            <div class="text-muted small">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success fs-5">{{ number_format($user->threads_count) }}</div>
                                        <div class="small text-muted">{{ __('ui.leaderboard.threads') }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5>{{ __('ui.leaderboard.no_data') }}</h5>
                                    <p class="text-muted">{{ __('ui.leaderboard.no_threads_yet') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Top Followed -->
                <div class="tab-pane fade" id="top-followed">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('ui.leaderboard.top_followed_description') }}</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($topFollowed as $index => $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($index === 0)
                                                <i class="fas fa-crown text-warning fa-2x"></i>
                                            @elseif($index === 1)
                                                <i class="fas fa-medal text-secondary fa-2x"></i>
                                            @elseif($index === 2)
                                                <i class="fas fa-award text-warning fa-2x"></i>
                                            @else
                                                <span class="badge bg-primary fs-5">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}"
                                             class="rounded-circle me-3" width="48" height="48"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-info text-white fw-bold"
                                             style="width: 48px; height: 48px; font-size: 18px; display: none;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('profile.show', $user->username) }}"
                                                   class="text-decoration-none">{{ $user->name }}</a>
                                            </h6>
                                            <div class="text-muted small">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-info fs-5">{{ number_format($user->followers_count) }}</div>
                                        <div class="small text-muted">{{ __('ui.leaderboard.followers') }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>{{ __('ui.leaderboard.no_data') }}</h5>
                                    <p class="text-muted">{{ __('ui.leaderboard.no_followers_yet') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-activate tabs based on URL hash
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        const tabTrigger = document.querySelector(`[href="${window.location.hash}"]`);
        if (tabTrigger) {
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }

    // Update URL hash when tab changes
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            window.location.hash = e.target.getAttribute('href');
        });
    });
});
</script>
@endsection
