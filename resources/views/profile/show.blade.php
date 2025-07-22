@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <!-- Left Sidebar - User Info -->
                <div class="col-md-3">
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-body text-center">
                            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle mb-3" width="120" height="120">
                            <h5 class="card-title">{{ $user->name }}</h5>
                            <p class="card-text text-muted">{{ '@' . $user->username }}</p>
                            <p class="card-text">
                                <span class="badge bg-secondary">{{ $user->status }}</span>
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : 'info') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>

                            @if ($user->last_seen_at)
                                <p class="small text-muted">
                                    {{ __('profile.last_seen') }} {{ $user->last_seen_at->diffForHumans() }}
                                </p>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="row text-center">
                                <div class="col">
                                    <div class="fw-bold">{{ $stats['replies'] }}</div>
                                    <div class="small text-muted">{{ __('profile.replies') }}</div>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">{{ $stats['discussions_created'] }}</div>
                                    <div class="small text-muted">{{ __('profile.threads') }}</div>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">{{ $stats['reaction_score'] }}</div>
                                    <div class="small text-muted">{{ __('profile.reactions') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('profile.about') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($user->about_me)
                                <p>{{ $user->about_me }}</p>
                            @else
                                <p class="text-muted">{{ __('profile.no_information_provided') }}</p>
                            @endif

                            @if ($user->location)
                                <p>
                                    <i class="geo-alt"></i> {{ $user->location }}
                                </p>
                            @endif

                            @if ($user->website)
                                <p>
                                    <i class="link"></i>
                                    <a href="{{ $user->website }}" target="_blank" rel="nofollow">{{ $user->website }}</a>
                                </p>
                            @endif

                            <p>
                                <i class="calendar3"></i>
                                {{ __('profile.joined') }} {{ $user->created_at->format('M Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('profile.following') }}</h5>
                            <span class="badge bg-secondary">{{ $following }}</span>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('profile.followers') }}</h5>
                            <span class="badge bg-secondary">{{ $followers }}</span>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-9">
                    <!-- Setup Progress -->
                    @if (Auth::id() === $user->id && $setupProgress < 5)
                        <div class="card shadow-sm rounded-3 mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('profile.get_set_up_title') }}</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ __('profile.get_set_up_description') }}</p>

                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($setupProgress / 5) * 100 }}%"
                                        aria-valuenow="{{ $setupProgress }}" aria-valuemin="0" aria-valuemax="5">
                                        {{ $setupProgress }}/5
                                    </div>
                                </div>

                                <div class="list-group">
                                    <div class="list-group-item {{ $user->email_verified_at ? 'list-group-item-success' : '' }}">
                                        @if ($user->email_verified_at)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('profile.verify_email') }}
                                    </div>

                                    <div class="list-group-item {{ $user->avatar ? 'list-group-item-success' : '' }}">
                                        @if ($user->avatar)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('profile.add_avatar') }}
                                    </div>

                                    <div class="list-group-item {{ $user->about_me ? 'list-group-item-success' : '' }}">
                                        @if ($user->about_me)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('profile.add_information') }}
                                    </div>

                                    <div class="list-group-item {{ $user->location ? 'list-group-item-success' : '' }}">
                                        @if ($user->location)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('profile.add_location') }}
                                    </div>

                                    <div class="list-group-item {{ $stats['replies'] > 0 || $stats['discussions_created'] > 0 ? 'list-group-item-success' : '' }}">
                                        @if ($stats['replies'] > 0 || $stats['discussions_created'] > 0)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('profile.create_post_reply') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Profile Posts -->
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('profile.profile_posts') }}</h5>
                        </div>
                        <div class="card-body">
                            @auth
                                <form action="{{ route('profile.posts.store', $user->username) }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea name="content" class="form-control" rows="3" placeholder="{{ __('profile.write_something_on') }} {{ $user->name }}'s {{ __('profile.profile') }}..."></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">{{ __('profile.post') }}</button>
                                    </div>
                                </form>
                            @endauth

                            @if ($profilePosts->count() > 0)
                                <div class="profile-posts">
                                    @foreach ($profilePosts as $post)
                                        <div class="card mb-3">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $post->user->getAvatarUrl() }}" alt="{{ $post->user->name }}" class="rounded-circle me-2" width="32" height="32">
                                                    <div>
                                                        <a href="{{ route('profile.show', $post->user->username) }}" class="fw-bold text-decoration-none">{{ $post->user->name }}</a>
                                                        <div class="small text-muted">{{ $post->created_at->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">{{ $post->content }}</p>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{ $profilePosts->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">{{ __('profile.no_profile_posts') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('profile.recent_activity') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($activities->count() > 0)
                                <div class="list-group">
                                    @foreach ($activities as $activity)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @switch($activity->activity_type)
                                                        @case('thread_created')
                                                            <i class="fas fa-plus-circle text-success me-2"></i>
                                                            {{ __('profile.created_new_thread') }}
                                                            @break
                                                        @case('post_created')
                                                            <i class="fas fa-comment-left-text text-primary me-2"></i>
                                                            {{ __('profile.replied_to_thread') }}
                                                            @break
                                                        @case('profile_updated')
                                                            <i class="person text-info me-2"></i>
                                                            {{ __('profile.updated_profile_info') }}
                                                            @break
                                                        @default
                                                            <i class="activity text-secondary me-2"></i>
                                                            {{ $activity->activity_type }}
                                                    @endswitch
                                                </div>
                                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">{{ __('profile.no_recent_activity') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
