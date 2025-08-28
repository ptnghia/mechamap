@extends('layouts.app')

@section('title', 'My Profile - MechaMap')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">My Profile</li>
        </ol>
    </nav>

    <!-- Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card profile-header">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="profile-avatar-container">
                                <img src="{{ $user->getAvatarUrl() }}"
                                     alt="{{ $user->name }}"
                                     class="profile-avatar rounded-circle">
                                <div class="avatar-overlay">
                                    <button class="btn btn-sm btn-light rounded-circle"
                                            onclick="changeAvatar()" title="Change Avatar">
                                        <i class="bx bx-camera"></i>
                                    </button>
                                </div>
                                @if($user->is_online)
                                <div class="online-indicator"></div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <div class="profile-badges">
                                    @if($user->is_verified)
                                    <span class="badge bg-success" title="Verified User">
                                        <i class="bx bx-check-circle me-1"></i>Verified
                                    </span>
                                    @endif
                                    @if($user->role)
                                    <span class="badge bg-primary">{{ ucfirst($user->role->name) }}</span>
                                    @endif
                                    @if($user->is_premium)
                                    <span class="badge bg-warning">
                                        <i class="bx bx-crown me-1"></i>Premium
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h2 class="mb-1">{{ $user->name }}</h2>
                            <p class="text-muted mb-2">{{ '@' . $user->username }}</p>
                            @if($user->title)
                            <p class="text-primary mb-2">{{ $user->title }}</p>
                            @endif
                            @if($user->bio)
                            <p class="mb-3">{{ $user->bio }}</p>
                            @endif

                            <!-- Contact Info -->
                            <div class="contact-info">
                                @if($user->email_public || auth()->id() === $user->id)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-envelope text-muted me-2"></i>
                                    <span>{{ $user->email }}</span>
                                </div>
                                @endif
                                @if($user->phone)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-phone text-muted me-2"></i>
                                    <span>{{ $user->phone }}</span>
                                </div>
                                @endif
                                @if($user->location)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-map text-muted me-2"></i>
                                    <span>{{ $user->location }}</span>
                                </div>
                                @endif
                                @if($user->website)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-link text-muted me-2"></i>
                                    <a href="{{ $user->website }}" target="_blank" class="text-decoration-none">
                                        {{ $user->website }}
                                    </a>
                                </div>
                                @endif
                            </div>

                            <!-- Social Links -->
                            @if($user->social_links)
                            <div class="social-links mt-3">
                                @foreach($user->social_links as $platform => $url)
                                <a href="{{ $url }}" target="_blank" class="btn btn-outline-secondary btn-sm me-2">
                                    <i class="bx bxl-{{ $platform }}"></i>
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="col-md-3 text-md-end">
                            @if(auth()->id() === $user->id)
                            <div class="d-grid gap-2">
                                <a href="{{ route('users.profile.edit') }}" class="btn btn-primary">
                                    <i class="bx bx-edit me-1"></i>
                                    Edit Profile
                                </a>
                                <a href="{{ route('users.settings.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-cog me-1"></i>
                                    Settings
                                </a>
                            </div>
                            @else
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" onclick="sendMessage({{ $user->id }})">
                                    <i class="bx bx-message me-1"></i>
                                    Send Message
                                </button>
                                <button class="btn btn-outline-secondary" onclick="followUser({{ $user->id }})">
                                    <i class="bx bx-user-plus me-1"></i>
                                    Follow
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bx bx-message-dots display-6 text-primary"></i>
                    <h4 class="mt-2 mb-1">{{ $stats['forum_posts'] }}</h4>
                    <p class="text-muted mb-0">Forum Posts</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bx bx-package display-6 text-success"></i>
                    <h4 class="mt-2 mb-1">{{ $stats['marketplace_orders'] }}</h4>
                    <p class="text-muted mb-0">Orders Placed</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bx bx-star display-6 text-warning"></i>
                    <h4 class="mt-2 mb-1">{{ $stats['reviews_given'] }}</h4>
                    <p class="text-muted mb-0">Reviews Given</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bx bx-calendar display-6 text-info"></i>
                    <h4 class="mt-2 mb-1">{{ $user->created_at->diffInDays() }}</h4>
                    <p class="text-muted mb-0">Days Active</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Recent Activity -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-time me-2"></i>
                        Recent Activity
                    </h5>
                    <a href="{{ route('users.activity.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                    <div class="activity-timeline">
                        @foreach($recentActivity as $activity)
                        <div class="activity-item">
                            <div class="activity-icon bg-{{ $activity->getTypeColor() }}">
                                <i class="bx {{ $activity->getTypeIcon() }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">
                                    {!! $activity->getFormattedDescription() !!}
                                </div>
                                <div class="activity-time text-muted">
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-time display-4 text-muted"></i>
                        <h6 class="mt-3">No Recent Activity</h6>
                        <p class="text-muted">Start engaging with the community to see your activity here.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Forum Posts -->
            @if($recentPosts->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-message-square me-2"></i>
                        Recent Forum Posts
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($recentPosts as $post)
                    <div class="post-item d-flex align-items-start mb-3">
                        <div class="post-icon me-3">
                            <i class="bx bx-message-dots text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('threads.show', $post->thread) }}" class="text-decoration-none">
                                    {{ $post->thread->title }}
                                </a>
                            </h6>
                            <p class="text-muted mb-1">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                            <div class="d-flex align-items-center text-muted small">
                                <span class="me-3">
                                    <i class="bx bx-time me-1"></i>
                                    {{ $post->created_at->diffForHumans() }}
                                </span>
                                <span class="me-3">
                                    <i class="bx bx-message me-1"></i>
                                    {{ $post->thread->replies_count }} replies
                                </span>
                                <span>
                                    <i class="bx bx-show me-1"></i>
                                    {{ $post->thread->views_count }} views
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Achievements -->
            @if($achievements->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-trophy me-2"></i>
                        Achievements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="achievements-grid">
                        @foreach($achievements as $achievement)
                        <div class="achievement-item">
                            <div class="achievement-icon">
                                <i class="bx {{ $achievement->icon }} text-{{ $achievement->color }}"></i>
                            </div>
                            <div class="achievement-info">
                                <h6 class="mb-1">{{ $achievement->name }}</h6>
                                <p class="text-muted small mb-0">{{ $achievement->description }}</p>
                                <div class="text-muted small">
                                    Earned {{ $achievement->pivot->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Profile Completion -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-user-check me-2"></i>
                        Profile Completion
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Profile Strength</span>
                        <span class="fw-bold">{{ $profileCompletion }}%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ $profileCompletion }}%"></div>
                    </div>

                    @if($profileCompletion < 100)
                    <div class="profile-suggestions">
                        <h6 class="small text-muted mb-2">SUGGESTIONS TO IMPROVE</h6>
                        @foreach($profileSuggestions as $suggestion)
                        <div class="suggestion-item d-flex align-items-center mb-2">
                            <i class="bx bx-plus-circle text-success me-2"></i>
                            <span class="small">{{ $suggestion }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Skills & Expertise -->
            @if($user->skills && count($user->skills) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-brain me-2"></i>
                        Skills & Expertise
                    </h6>
                </div>
                <div class="card-body">
                    <div class="skills-tags">
                        @foreach($user->skills as $skill)
                        <span class="badge bg-light text-dark me-1 mb-1">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Interests -->
            @if($user->interests && count($user->interests) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-heart me-2"></i>
                        Interests
                    </h6>
                </div>
                <div class="card-body">
                    <div class="interests-tags">
                        @foreach($user->interests as $interest)
                        <span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1">{{ $interest }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-zap me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('forums.threads.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Create Forum Post
                        </a>
                        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="bx bx-store me-1"></i>
                            Browse Marketplace
                        </a>
                        <a href="{{ route('users.activity.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bx bx-time me-1"></i>
                            View Activity
                        </a>
                        <a href="{{ route('users.notifications.index') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bx bx-bell me-1"></i>
                            Notifications
                            @if($unreadNotifications > 0)
                            <span class="badge bg-danger ms-1">{{ $unreadNotifications }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Avatar Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="avatarForm" enctype="multipart/form-data">
                    @csrf
                    <div class="text-center">
                        <x-avatar-upload
                            name="avatar"
                            id="profile-avatar-upload"
                            :current-avatar="$user->getAvatarUrl()"
                            :size="120"
                            max-size="2MB"
                            :required="true"
                            shape="circle"
                            :show-remove="true"
                            placeholder-text="Click để thay đổi avatar"
                            upload-url="{{ route('profile.avatar.upload') }}"
                        />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadAvatar()">Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.profile-avatar-container {
    position: relative;
    display: inline-block;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border: 4px solid white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.avatar-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.profile-avatar-container:hover .avatar-overlay {
    opacity: 1;
}

.online-indicator {
    position: absolute;
    bottom: 10px;
    right: 10px;
    width: 20px;
    height: 20px;
    background: #28a745;
    border: 3px solid white;
    border-radius: 50%;
}

.profile-badges .badge {
    margin: 0.25rem;
}

.stat-card {
    transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.activity-timeline {
    position: relative;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
}

.activity-content {
    flex-grow: 1;
}

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.achievement-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: transform 0.2s ease-in-out;
}

.achievement-item:hover {
    transform: translateY(-2px);
}

.achievement-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.5rem;
}

.skills-tags .badge,
.interests-tags .badge {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .profile-avatar {
        width: 100px;
        height: 100px;
    }

    .achievements-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
function changeAvatar() {
    const modal = new bootstrap.Modal(document.getElementById('avatarModal'));
    modal.show();
}

function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function uploadAvatar() {
    const formData = new FormData(document.getElementById('avatarForm'));

    fetch('{{ route("users.profile.avatar") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error uploading avatar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error uploading avatar');
    });
}

function sendMessage(userId) {
    window.location.href = `/messages/compose?to=${userId}`;
}

function followUser(userId) {
    fetch(`/users/${userId}/follow`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endpush
