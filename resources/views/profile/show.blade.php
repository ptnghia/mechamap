@extends('layouts.app')

@section('title', 'Profile ' . $user->name . ' | MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/profile.css') }}">
@endpush

@section('content')
<div class="container py-4">
    <!-- Profile Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Avatar and User Info -->
                <div class="col-md-3 text-center">
                    <div class="avatar-container mb-3">
                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" width="150" height="150">
                        <div class="avatar-letter">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    </div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->username }}</p>
                    <p class="card-text">
                        <span class="badge bg-success">active</span>
                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : 'info') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>
                    <p class="small mb-1">
                        {{ __('profile.last_seen') }}
                        @if($user->last_seen_at)
                            <span title="{{ $user->last_seen_at->format('M d, Y H:i') }}">{{ $user->last_seen_at->diffForHumans() }}</span>
                        @else
                            {{ __('profile.never') }}
                        @endif
                    </p>

                    @if(Auth::check() && Auth::id() != $user->id)
                        <div class="mt-3">
                            <button class="btn btn-sm btn-primary me-2">Follow</button>
                            <button class="btn btn-sm btn-outline-secondary me-2">Contact</button>
                            <button class="btn btn-sm btn-outline-danger">Report</button>
                        </div>
                    @endif
                </div>

                <!-- User Stats -->
                <div class="col-md-9">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-value">{{ $stats['replies'] ?? 15 }}</div>
                                <div class="stat-label">{{ __('profile.replies') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-value">{{ $stats['discussions_created'] ?? 5 }}</div>
                                <div class="stat-label">{{ __('profile.threads') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-value">{{ $stats['reactions'] ?? 22 }}</div>
                                <div class="stat-label">{{ __('profile.reactions') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <!-- About Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('profile.about') }}</h5>
                </div>
                <div class="card-body">
                    @if ($user->about_me)
                        <p>{{ $user->about_me }}</p>
                    @else
                        <p class="text-muted">{{ __('profile.no_information_provided') }}</p>
                    @endif

                    @if ($user->location)
                        <p><i class="fas fa-map-marker-alt"></i> {{ $user->location }}</p>
                    @endif

                    @if ($user->website)
                        <p><i class="fas fa-link"></i> <a href="{{ $user->website }}" target="_blank" rel="nofollow">{{ $user->website }}</a></p>
                    @endif

                    <p><i class="fas fa-calendar"></i> {{ __('profile.joined') }} {{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>

            <!-- Following/Followers -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('profile.following') }}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="fw-bold">{{ $following ?? 0 }}</div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('profile.followers') }}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="fw-bold">{{ $followers ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Main Content with Tabs -->
        <div class="col-md-9">
            <!-- Tab Navigation -->
            <div class="profile-tabs mb-4">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#overview" data-tab="overview">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about" data-tab="about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#profile-posts" data-tab="profile-posts">Profile posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#activity" data-tab="activity">Activity</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#gallery" data-tab="gallery">Gallery</a>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview Tab -->
                <div id="overview" class="tab-pane active">
                    <!-- About Section (Overview) -->
                    @include('profile.partials.about-section', ['user' => $user])

                    <!-- Activity Section (Overview) -->
                    @include('profile.partials.activity-section', ['user' => $user, 'activities' => $activities])
                </div>

                <!-- About Tab -->
                <div id="about" class="tab-pane d-none">
                    @include('profile.partials.about-section', ['user' => $user, 'showAll' => true])
                </div>

                <!-- Profile Posts Tab -->
                <div id="profile-posts" class="tab-pane d-none">
                    @include('profile.partials.profile-posts-section', ['user' => $user, 'profilePosts' => $profilePosts])
                </div>

                <!-- Activity Tab -->
                <div id="activity" class="tab-pane d-none">
                    @include('profile.partials.activity-section', ['user' => $user, 'activities' => $activities, 'showAll' => true])
                </div>

                <!-- Gallery Tab -->
                <div id="gallery" class="tab-pane d-none">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('profile.gallery') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted text-center py-5">{{ __('profile.no_media_to_display') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile tab switching
    const tabLinks = document.querySelectorAll('.profile-tabs .nav-link');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active class from all tabs and panes
            tabLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => {
                p.classList.remove('active');
                p.classList.add('d-none');
            });

            // Add active class to clicked tab
            this.classList.add('active');

            // Show corresponding pane
            const targetTab = this.getAttribute('data-tab');
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
                targetPane.classList.remove('d-none');
            }
        });
    });
});
</script>
@endpush
