@props(['thread', 'size' => 'normal'])

@php
    $user = auth()->user();
    $isFollowing = $user ? $user->followedThreads()->where('thread_id', $thread->id)->exists() : false;
    $followerCount = $thread->followers()->count();

    $buttonClass = match($size) {
        'small' => 'btn-sm',
        'large' => 'btn-lg',
        default => ''
    };
@endphp

<div class="thread-follow-wrapper" data-thread-id="{{ $thread->id }}" data-thread-slug="{{ $thread->slug }}">
    @auth
        <button type="button"
                class="btn btn-sm {{ $isFollowing ? 'btn-success' : 'btn-outline-primary' }} thread-follow-btn {{ $buttonClass }}"
                data-thread-id="{{ $thread->id }}"
                data-thread-slug="{{ $thread->slug }}"
                data-following="{{ $isFollowing ? 'true' : 'false' }}"
                title="{{ $isFollowing ? __('thread.unfollow') : __('thread.follow') }}">
            <i class="fas {{ $isFollowing ? 'fa-bell' : 'fa-bell-slash' }} me-1"></i>
            <span class="follow-text">{{ $isFollowing ? __('thread.following') : __('thread.follow') }}</span>
            <span class="follower-count badge bg-light text-dark ms-1">{{ $followerCount }}</span>
        </button>
    @else
        <button type="button"
                class="btn btn-sm btn-outline-primary {{ $buttonClass }}"
                onclick="showLoginModal()"
                title="{{ __('thread.login_to_follow') }}">
            <i class="fas fa-bell-slash me-1"></i>
            <span>{{ __('thread.follow') }}</span>
            <span class="follower-count badge bg-light text-dark ms-1">{{ $followerCount }}</span>
        </button>
    @endauth
</div>

@push('scripts')
<script>
// Toast notification function (if not already defined)

</script>
@endpush

@push('styles')

@endpush
