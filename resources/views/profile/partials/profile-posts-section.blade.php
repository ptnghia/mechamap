<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">{{ __('profile.posts') }}</h5>
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

        @if($profilePosts->count() > 0)
            <div class="profile-posts">
                @foreach($profilePosts as $post)
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

                @if($profilePosts->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $profilePosts->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="empty-state">
                <p>{{ __('profile.no_posts_yet') }}</p>
            </div>
        @endif
    </div>
</div>
