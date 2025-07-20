@extends('layouts.app')

@section('title', __('coming_soon.page_title'))

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card shadow-lg border-0 rounded-4" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);">
                    <div class="card-body p-5 text-center">
                        <!-- Icon Animation -->
                        <div class="mb-4">
                            <div class="coming-soon-icon position-relative d-inline-block">
                                <i class="fa-solid fa-rocket fa-4x text-primary mb-3"></i>
                                <div class="pulse-ring position-absolute top-50 start-50 translate-middle"></div>
                            </div>
                        </div>

                        <!-- Main Title -->
                        <h1 class="display-4 fw-bold text-dark mb-3">
                            {{ $title ?? __('coming_soon.title') }}
                        </h1>

                        <!-- Feature Name -->
                        @if(isset($feature))
                        <div class="feature-badge mb-4">
                            <span class="badge bg-primary-subtle text-primary fs-5 px-4 py-2 rounded-pill">
                                <i class="fa-solid fa-star me-2"></i>
                                {{ $feature['name'] ?? __('coming_soon.default_feature') }}
                            </span>
                        </div>
                        @endif

                        <!-- Description -->
                        <p class="lead text-muted mb-4">
                            {{ $description ?? __('coming_soon.default_description') }}
                        </p>

                        <!-- Feature Details -->
                        @if(isset($features) && is_array($features))
                        <div class="row g-3 mb-5">
                            @foreach($features as $item)
                            <div class="col-md-6">
                                <div class="feature-item p-3 rounded-3 bg-light">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    <span class="fw-medium">{{ $item }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Timeline -->
                        <div class="timeline-section mb-5">
                            <h5 class="fw-bold text-dark mb-3">
                                <i class="fa-solid fa-calendar-alt me-2 text-primary"></i>
                                {{ __('coming_soon.timeline_title') }}
                            </h5>
                            <div class="timeline-item">
                                <span class="badge bg-warning text-dark">
                                    {{ $timeline ?? __('coming_soon.default_timeline') }}
                                </span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress-section mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-medium text-dark">{{ __('coming_soon.progress_label') }}</span>
                                <span class="text-primary fw-bold">{{ $progress ?? '75' }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 8px;">
                                <div class="progress-bar bg-gradient-primary"
                                     role="progressbar"
                                     style="width: {{ $progress ?? '75' }}%"
                                     aria-valuenow="{{ $progress ?? '75' }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- Notification Signup -->
                        <div class="notification-section mb-4">
                            <h6 class="fw-bold text-dark mb-3">
                                {{ __('coming_soon.notify_title') }}
                            </h6>
                            <form class="row g-2 justify-content-center" id="notifyForm">
                                <div class="col-auto">
                                    <input type="email"
                                           class="form-control"
                                           placeholder="{{ __('coming_soon.email_placeholder') }}"
                                           required>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-bell me-1"></i>
                                        {{ __('coming_soon.notify_button') }}
                                    </button>
                                </div>
                            </form>
                            <small class="text-muted mt-2 d-block">
                                {{ __('coming_soon.notify_description') }}
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-3">
                                <i class="fa-solid fa-arrow-left me-2"></i>
                                {{ __('coming_soon.back_button') }}
                            </a>
                            <a href="{{ route('forums.index') }}" class="btn btn-primary">
                                <i class="fa-solid fa-comments me-2"></i>
                                {{ __('coming_soon.explore_forums') }}
                            </a>
                        </div>

                        <!-- Social Share -->
                        <div class="social-section mt-5 pt-4 border-top">
                            <p class="text-muted mb-3">{{ __('coming_soon.share_excitement') }}</p>
                            <div class="social-buttons">
                                <a href="#" class="btn btn-outline-primary btn-sm me-2" onclick="shareOnFacebook()">
                                    <i class="fab fa-facebook-f me-1"></i>
                                    Facebook
                                </a>
                                <a href="#" class="btn btn-outline-info btn-sm me-2" onclick="shareOnTwitter()">
                                    <i class="fab fa-twitter me-1"></i>
                                    Twitter
                                </a>
                                <a href="#" class="btn btn-outline-success btn-sm" onclick="copyLink()">
                                    <i class="fa-solid fa-link me-1"></i>
                                    {{ __('coming_soon.copy_link') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Custom Styles -->
<style>
.pulse-ring {
    width: 120px;
    height: 120px;
    border: 3px solid var(--bs-primary);
    border-radius: 50%;
    opacity: 0;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.5);
        opacity: 0;
    }
}

.feature-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.feature-item:hover {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.timeline-item {
    position: relative;
    padding: 1rem;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    border-radius: 0.5rem;
    border-left: 4px solid var(--bs-warning);
}

.progress-bar {
    transition: width 1s ease-in-out;
}

.social-buttons a {
    transition: all 0.3s ease;
}

.social-buttons a:hover {
    transform: translateY(-2px);
}

.feature-badge {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bar on load
    setTimeout(() => {
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = progressBar.getAttribute('style').match(/width:\s*(\d+)%/)[0];
        }
    }, 500);

    // Handle notification form
    const notifyForm = document.getElementById('notifyForm');
    if (notifyForm) {
        notifyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;

            // Show success message
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fa-solid fa-check me-1"></i> {{ __("coming_soon.notify_success") }}';
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');
            button.disabled = true;

            // Reset after 3 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
                button.classList.add('btn-primary');
                button.disabled = false;
                this.reset();
            }, 3000);
        });
    }
});

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('{{ __("coming_soon.share_text") }}');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${text}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('{{ __("coming_soon.share_text") }}');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const button = event.target.closest('a');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fa-solid fa-check me-1"></i> {{ __("coming_soon.copied") }}';
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    });
}
</script>
@endsection
