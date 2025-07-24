@extends('layouts.user-dashboard')

@section('title', __('nav.user.settings'))

@php
    $pageTitle = __('nav.user.settings');
    $pageDescription = __('messages.settings_desc');
    $breadcrumbs = [
        ['title' => __('nav.user.settings'), 'url' => '#']
    ];
@endphp

@section('dashboard-content')
<!-- Settings Navigation Tabs -->
<ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" 
                type="button" role="tab" aria-controls="profile" aria-selected="true">
            <i class="fas fa-user me-2"></i>{{ __('messages.profile_settings') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" 
                type="button" role="tab" aria-controls="account" aria-selected="false">
            <i class="fas fa-cog me-2"></i>{{ __('messages.account_settings') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" 
                type="button" role="tab" aria-controls="notifications" aria-selected="false">
            <i class="fas fa-bell me-2"></i>{{ __('messages.notifications') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="privacy-tab" data-bs-toggle="tab" data-bs-target="#privacy" 
                type="button" role="tab" aria-controls="privacy" aria-selected="false">
            <i class="fas fa-shield-alt me-2"></i>{{ __('messages.privacy') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-danger" id="danger-tab" data-bs-toggle="tab" data-bs-target="#danger" 
                type="button" role="tab" aria-controls="danger" aria-selected="false">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ __('messages.danger_zone') }}
        </button>
    </li>
</ul>

<div class="tab-content" id="settingsTabContent">
    <!-- Profile Settings Tab -->
    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.profile_information') }}</h5>
            </div>
            <div class="card-body">
                <form id="profileForm" action="{{ route('user.settings.profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Avatar Upload -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <img src="{{ auth()->user()->getAvatarUrl() }}" alt="Avatar" 
                                     class="rounded-circle mb-3" width="120" height="120" id="avatarPreview">
                                <div>
                                    <label for="avatar" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-camera me-1"></i>{{ __('messages.change_avatar') }}
                                    </label>
                                    <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                                </div>
                                <small class="text-muted d-block mt-2">
                                    {{ __('messages.avatar_requirements') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">{{ __('messages.full_name') }} *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">{{ __('messages.username') }} *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="{{ auth()->user()->username }}" required>
                                    <small class="text-muted">{{ __('messages.username_requirements') }}</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ __('messages.email') }} *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ auth()->user()->email }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ __('messages.phone') }}</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="{{ auth()->user()->phone }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bio and Location -->
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label for="bio" class="form-label">{{ __('messages.bio') }}</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" 
                                      placeholder="{{ __('messages.bio_placeholder') }}">{{ auth()->user()->bio }}</textarea>
                            <small class="text-muted">{{ __('messages.bio_requirements') }}</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">{{ __('messages.location') }}</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="{{ auth()->user()->location }}" placeholder="{{ __('messages.location_placeholder') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">{{ __('messages.website') }}</label>
                            <input type="url" class="form-control" id="website" name="website" 
                                   value="{{ auth()->user()->website }}" placeholder="https://example.com">
                        </div>
                    </div>
                    
                    <!-- Social Links -->
                    <div class="mb-4">
                        <h6>{{ __('messages.social_links') }}</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="linkedin" class="form-label">
                                    <i class="fab fa-linkedin me-1"></i>LinkedIn
                                </label>
                                <input type="url" class="form-control" id="linkedin" name="social_links[linkedin]" 
                                       value="{{ auth()->user()->social_links['linkedin'] ?? '' }}" 
                                       placeholder="https://linkedin.com/in/username">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="github" class="form-label">
                                    <i class="fab fa-github me-1"></i>GitHub
                                </label>
                                <input type="url" class="form-control" id="github" name="social_links[github]" 
                                       value="{{ auth()->user()->social_links['github'] ?? '' }}" 
                                       placeholder="https://github.com/username">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Account Settings Tab -->
    <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
        <div class="row">
            <!-- Change Password -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('messages.change_password') }}</h6>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm" action="{{ route('user.settings.password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="current_password" class="form-label">{{ __('messages.current_password') }} *</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">{{ __('messages.new_password') }} *</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <small class="text-muted">{{ __('messages.password_requirements') }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">{{ __('messages.confirm_password') }} *</label>
                                <input type="password" class="form-control" id="new_password_confirmation" 
                                       name="new_password_confirmation" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i>{{ __('messages.update_password') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Language & Timezone -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('messages.preferences') }}</h6>
                    </div>
                    <div class="card-body">
                        <form id="preferencesForm" action="{{ route('user.settings.preferences') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="language" class="form-label">{{ __('messages.language') }}</label>
                                <select class="form-select" id="language" name="language">
                                    <option value="vi" {{ (auth()->user()->language ?? 'vi') === 'vi' ? 'selected' : '' }}>
                                        Tiếng Việt
                                    </option>
                                    <option value="en" {{ (auth()->user()->language ?? 'vi') === 'en' ? 'selected' : '' }}>
                                        English
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="timezone" class="form-label">{{ __('messages.timezone') }}</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="Asia/Ho_Chi_Minh" {{ (auth()->user()->timezone ?? 'Asia/Ho_Chi_Minh') === 'Asia/Ho_Chi_Minh' ? 'selected' : '' }}>
                                        (UTC+07:00) Ho Chi Minh City
                                    </option>
                                    <option value="UTC" {{ (auth()->user()->timezone ?? 'Asia/Ho_Chi_Minh') === 'UTC' ? 'selected' : '' }}>
                                        (UTC+00:00) UTC
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="theme" class="form-label">{{ __('messages.theme') }}</label>
                                <select class="form-select" id="theme" name="theme">
                                    <option value="light" {{ (auth()->user()->theme ?? 'light') === 'light' ? 'selected' : '' }}>
                                        {{ __('messages.light_theme') }}
                                    </option>
                                    <option value="dark" {{ (auth()->user()->theme ?? 'light') === 'dark' ? 'selected' : '' }}>
                                        {{ __('messages.dark_theme') }}
                                    </option>
                                    <option value="auto" {{ (auth()->user()->theme ?? 'light') === 'auto' ? 'selected' : '' }}>
                                        {{ __('messages.auto_theme') }}
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('messages.save_preferences') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Tab -->
    <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.notification_preferences') }}</h5>
            </div>
            <div class="card-body">
                <form id="notificationsForm" action="{{ route('user.settings.notifications') }}" method="POST">
                    @csrf
                    
                    <!-- Email Notifications -->
                    <div class="mb-4">
                        <h6>{{ __('messages.email_notifications') }}</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="email_thread_replies" 
                                   name="notifications[email_thread_replies]" value="1"
                                   {{ (auth()->user()->notification_settings['email_thread_replies'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_thread_replies">
                                {{ __('messages.thread_replies') }}
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="email_new_followers" 
                                   name="notifications[email_new_followers]" value="1"
                                   {{ (auth()->user()->notification_settings['email_new_followers'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_new_followers">
                                {{ __('messages.new_followers') }}
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="email_mentions" 
                                   name="notifications[email_mentions]" value="1"
                                   {{ (auth()->user()->notification_settings['email_mentions'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_mentions">
                                {{ __('messages.mentions') }}
                            </label>
                        </div>
                    </div>
                    
                    <!-- Push Notifications -->
                    <div class="mb-4">
                        <h6>{{ __('messages.push_notifications') }}</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="push_thread_replies" 
                                   name="notifications[push_thread_replies]" value="1"
                                   {{ (auth()->user()->notification_settings['push_thread_replies'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="push_thread_replies">
                                {{ __('messages.thread_replies') }}
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="push_new_followers" 
                                   name="notifications[push_new_followers]" value="1"
                                   {{ (auth()->user()->notification_settings['push_new_followers'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="push_new_followers">
                                {{ __('messages.new_followers') }}
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ __('messages.save_notifications') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Privacy Tab -->
    <div class="tab-pane fade" id="privacy" role="tabpanel" aria-labelledby="privacy-tab">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.privacy_settings') }}</h5>
            </div>
            <div class="card-body">
                <form id="privacyForm" action="{{ route('user.settings.privacy') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <h6>{{ __('messages.profile_visibility') }}</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" id="profile_public" 
                                   name="profile_visibility" value="public"
                                   {{ (auth()->user()->privacy_settings['profile_visibility'] ?? 'public') === 'public' ? 'checked' : '' }}>
                            <label class="form-check-label" for="profile_public">
                                <strong>{{ __('messages.public') }}</strong> - {{ __('messages.public_profile_desc') }}
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" id="profile_members" 
                                   name="profile_visibility" value="members"
                                   {{ (auth()->user()->privacy_settings['profile_visibility'] ?? 'public') === 'members' ? 'checked' : '' }}>
                            <label class="form-check-label" for="profile_members">
                                <strong>{{ __('messages.members_only') }}</strong> - {{ __('messages.members_profile_desc') }}
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" id="profile_private" 
                                   name="profile_visibility" value="private"
                                   {{ (auth()->user()->privacy_settings['profile_visibility'] ?? 'public') === 'private' ? 'checked' : '' }}>
                            <label class="form-check-label" for="profile_private">
                                <strong>{{ __('messages.private') }}</strong> - {{ __('messages.private_profile_desc') }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6>{{ __('messages.contact_preferences') }}</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="allow_messages" 
                                   name="privacy[allow_messages]" value="1"
                                   {{ (auth()->user()->privacy_settings['allow_messages'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_messages">
                                {{ __('messages.allow_direct_messages') }}
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_online_status" 
                                   name="privacy[show_online_status]" value="1"
                                   {{ (auth()->user()->privacy_settings['show_online_status'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_online_status">
                                {{ __('messages.show_online_status') }}
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ __('messages.save_privacy') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Danger Zone Tab -->
    <div class="tab-pane fade" id="danger" role="tabpanel" aria-labelledby="danger-tab">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('messages.danger_zone') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ __('messages.danger_zone_warning') }}
                </div>
                
                <div class="mb-4">
                    <h6 class="text-danger">{{ __('messages.delete_account') }}</h6>
                    <p class="text-muted">{{ __('messages.delete_account_desc') }}</p>
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteAccount()">
                        <i class="fas fa-trash me-2"></i>{{ __('messages.delete_my_account') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('messages.confirm_delete_account') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>{{ __('messages.warning') }}:</strong> {{ __('messages.delete_account_warning') }}
                </div>
                <p>{{ __('messages.delete_account_confirmation') }}</p>
                <form id="deleteAccountForm" action="{{ route('user.settings.delete-account') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">{{ __('messages.confirm_password') }}</label>
                        <input type="password" class="form-control" id="delete_password" name="password" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirm_delete" required>
                        <label class="form-check-label" for="confirm_delete">
                            {{ __('messages.understand_consequences') }}
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>{{ __('messages.delete_account') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Avatar preview
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Form submissions
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ __("messages.profile_updated") }}');
});

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ __("messages.password_updated") }}');
});

document.getElementById('preferencesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ __("messages.preferences_updated") }}');
});

document.getElementById('notificationsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ __("messages.notifications_updated") }}');
});

document.getElementById('privacyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ __("messages.privacy_updated") }}');
});

function submitForm(form, successMessage) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("messages.saving") }}';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', successMessage);
            if (form.id === 'passwordForm') {
                form.reset();
            }
        } else {
            showAlert('danger', data.message || '{{ __("messages.error_occurred") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', '{{ __("messages.error_occurred") }}');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.dashboard-content');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

function confirmDeleteAccount() {
    const modal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
    modal.show();
}

// Handle tab switching with URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    
    if (activeTab) {
        const tabButton = document.getElementById(`${activeTab}-tab`);
        const tabPane = document.getElementById(activeTab);
        
        if (tabButton && tabPane) {
            // Remove active from current tab
            document.querySelector('.nav-link.active').classList.remove('active');
            document.querySelector('.tab-pane.active').classList.remove('show', 'active');
            
            // Activate new tab
            tabButton.classList.add('active');
            tabPane.classList.add('show', 'active');
        }
    }
});
</script>
@endsection
