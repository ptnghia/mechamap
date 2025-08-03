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
                class="btn {{ $isFollowing ? 'btn-success' : 'btn-outline-primary' }} thread-follow-btn {{ $buttonClass }}"
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
                class="btn btn-outline-primary {{ $buttonClass }}"
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
if (typeof showToast === 'undefined') {
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Thread follow functionality
    document.querySelectorAll('.thread-follow-btn').forEach(button => {
        button.addEventListener('click', function() {
            const threadSlug = this.dataset.threadSlug;
            const isFollowing = this.dataset.following === 'true';
            const action = isFollowing ? 'unfollow' : 'follow';

            // Disable button during request
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __('thread.processing') }}';

            // Make AJAX request
            const url = `/ajax/threads/${threadSlug}/follow`;
            const method = isFollowing ? 'DELETE' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    const newFollowing = data.is_following;
                    this.dataset.following = newFollowing ? 'true' : 'false';

                    // Update button appearance
                    if (newFollowing) {
                        this.className = this.className.replace('btn-outline-primary', 'btn-success');
                        this.innerHTML = '<i class="fas fa-bell me-1"></i><span class="follow-text">{{ __('thread.following') }}</span><span class="follower-count badge bg-light text-dark ms-1">' + data.follower_count + '</span>';
                        this.title = '{{ __('thread.unfollow') }}';
                    } else {
                        this.className = this.className.replace('btn-success', 'btn-outline-primary');
                        this.innerHTML = '<i class="fas fa-bell-slash me-1"></i><span class="follow-text">{{ __('thread.follow') }}</span><span class="follower-count badge bg-light text-dark ms-1">' + data.follower_count + '</span>';
                        this.title = '{{ __('thread.follow') }}';
                    }

                    // Show success message
                    showToast(data.message, 'success');

                    // Update all follower counts on page
                    const threadId = this.dataset.threadId;
                    document.querySelectorAll(`[data-thread-id="${threadId}"] .follower-count`).forEach(el => {
                        el.textContent = data.follower_count;
                    });

                } else {
                    showToast(data.message || '{{ __('thread.error_occurred') }}', 'error');

                    // Reset button state
                    const originalFollowing = this.dataset.following === 'true';
                    if (originalFollowing) {
                        this.innerHTML = '<i class="fas fa-bell me-1"></i><span class="follow-text">{{ __('thread.following') }}</span><span class="follower-count badge bg-light text-dark ms-1">' + (data.follower_count || 0) + '</span>';
                    } else {
                        this.innerHTML = '<i class="fas fa-bell-slash me-1"></i><span class="follow-text">{{ __('thread.follow') }}</span><span class="follower-count badge bg-light text-dark ms-1">' + (data.follower_count || 0) + '</span>';
                    }
                }
            })
            .catch(error => {
                console.error('Thread follow error:', error);
                showToast('{{ __('thread.request_error') }}', 'error');

                // Reset button state
                const originalFollowing = this.dataset.following === 'true';
                const followerCount = this.querySelector('.follower-count')?.textContent || '0';
                if (originalFollowing) {
                    this.innerHTML = '<i class="fas fa-bell me-1"></i><span class="follow-text">{{ __('thread.following') }}</span><span class="follower-count badge bg-light text-dark ms-1">' + followerCount + '</span>';
                } else {
                    this.innerHTML = '<i class="fas fa-bell-slash me-1"></i><span class="follow-text">{{ __('thread.follow') }}</span><span class="follower-count badge bg-light text-dark ms-1">' + followerCount + '</span>';
                }
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Add to page
    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Login modal function (if not already defined)
function showLoginModal() {
    // Check if login modal exists
    const loginModal = document.getElementById('loginModal');
    if (loginModal) {
        const modal = new bootstrap.Modal(loginModal);
        modal.show();
    } else {
        // Redirect to login page
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
    }
}
</script>
@endpush

@push('styles')
<style>
.thread-follow-wrapper .thread-follow-btn {
    transition: all 0.3s ease;
    position: relative;
}

.thread-follow-wrapper .thread-follow-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.thread-follow-wrapper .thread-follow-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.thread-follow-wrapper .follower-count {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
}

.thread-follow-wrapper .btn-success .follower-count {
    background-color: rgba(255,255,255,0.2) !important;
    color: white !important;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .thread-follow-wrapper .thread-follow-btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }

    .thread-follow-wrapper .thread-follow-btn .follow-text {
        display: none;
    }

    .thread-follow-wrapper .thread-follow-btn .fas {
        margin-right: 0 !important;
    }
}
</style>
@endpush
