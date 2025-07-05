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
                                    {{ __('Last seen:') }} {{ $user->last_seen_at->diffForHumans() }}
                                </p>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="row text-center">
                                <div class="col">
                                    <div class="fw-bold">{{ $stats['replies'] }}</div>
                                    <div class="small text-muted">{{ __('Replies') }}</div>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">{{ $stats['discussions_created'] }}</div>
                                    <div class="small text-muted">{{ __('Threads') }}</div>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">{{ $stats['reaction_score'] }}</div>
                                    <div class="small text-muted">{{ __('Reactions') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('About') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($user->about_me)
                                <p>{{ $user->about_me }}</p>
                            @else
                                <p class="text-muted">{{ __('No information provided.') }}</p>
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
                                {{ __('Joined:') }} {{ $user->created_at->format('M Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('Following') }}</h5>
                            <span class="badge bg-secondary">{{ $following }}</span>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('Followers') }}</h5>
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
                                <h5 class="card-title mb-0">{{ __('Get set up on MechaMap Forum!') }}</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ __('Not sure what to do next? Here are some ideas to get you familiar with the community!') }}</p>
                                
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
                                        {{ __('Verify your email') }}
                                    </div>
                                    
                                    <div class="list-group-item {{ $user->avatar ? 'list-group-item-success' : '' }}">
                                        @if ($user->avatar)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('Add an avatar') }}
                                    </div>
                                    
                                    <div class="list-group-item {{ $user->about_me ? 'list-group-item-success' : '' }}">
                                        @if ($user->about_me)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('Add information about yourself') }}
                                    </div>
                                    
                                    <div class="list-group-item {{ $user->location ? 'list-group-item-success' : '' }}">
                                        @if ($user->location)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('Add your location') }}
                                    </div>
                                    
                                    <div class="list-group-item {{ $stats['replies'] > 0 || $stats['discussions_created'] > 0 ? 'list-group-item-success' : '' }}">
                                        @if ($stats['replies'] > 0 || $stats['discussions_created'] > 0)
                                            <i class="fas fa-check-circle-fill text-success me-2"></i>
                                        @else
                                            <i class="circle text-muted me-2"></i>
                                        @endif
                                        {{ __('Create a post or reply to a thread') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Profile Posts -->
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Profile Posts') }}</h5>
                        </div>
                        <div class="card-body">
                            @auth
                                <form action="{{ route('profile.posts.store', $user->username) }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea name="content" class="form-control" rows="3" placeholder="{{ __('Write something on') }} {{ $user->name }}'s {{ __('profile') }}..."></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">{{ __('Post') }}</button>
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
                                    <p class="text-muted">{{ __('No profile posts yet.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Recent Activity') }}</h5>
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
                                                            {{ __('Created a new thread') }}
                                                            @break
                                                        @case('post_created')
                                                            <i class="fas fa-comment-left-text text-primary me-2"></i>
                                                            {{ __('Replied to a thread') }}
                                                            @break
                                                        @case('profile_updated')
                                                            <i class="person text-info me-2"></i>
                                                            {{ __('Updated profile information') }}
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
                                    <p class="text-muted">{{ __('No recent activity.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
