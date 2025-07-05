@extends('layouts.app')

@section('title', 'Page Title')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/profile.css') }}">
@endpush

@section('content')<div class="container py-4">
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
                        <p class="text-muted mb-2">{{ __('Registered') }}</p>
                        <p class="small mb-1">{{ __('Joined') }}: {{ $user->created_at->format('M d, Y') }}</p>
                        <p class="small mb-1">
                            {{ __('Last seen') }}:
                            @if($user->last_seen_at)
                                <span title="{{ $user->last_seen_at->format('M d, Y H:i') }}">{{ $user->last_seen_at->diffForHumans() }}</span>
                            @else
                                {{ __('Never') }}
                            @endif
                            ·
                            <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">{{ __('Viewing member profile') }} {{ $user->username }}</a>
                        </p>

                        @if(Auth::check() && Auth::id() != $user->id)
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-secondary">{{ __('Report') }}</button>
                            </div>
                        @endif
                    </div>

                    <!-- User Stats -->
                    <div class="col-md-9">
                        <div class="stats-section">
                            <div class="stat-item">
                                <div class="stat-label">{{ __('Replies') }}</div>
                                <div class="stat-value">{{ $stats['replies'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">{{ __('Discussions Created') }}</div>
                                <div class="stat-value">{{ $stats['discussions_created'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">{{ __('Reaction score') }}</div>
                                <div class="stat-value">{{ $stats['reaction_score'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">{{ __('Points') }}</div>
                                <div class="stat-value">{{ $stats['points'] }}</div>
                            </div>
                        </div>

                        <!-- Setup Progress -->
                        @if(Auth::check() && Auth::id() == $user->id && $setupProgress < 5)
                            <div class="card setup-card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">{{ __('Get set up on MechaMap Forum!') }}</h5>
                                        <button type="button" class="btn-close" aria-label="Close"></button>
                                    </div>
                                    <p class="text-muted">{{ __('Not sure what to do next? Here are some ideas to get you familiar with the community!') }}</p>

                                    <div class="progress mb-3" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($setupProgress / 5) * 100 }}%"
                                            aria-valuenow="{{ $setupProgress }}" aria-valuemin="0" aria-valuemax="5"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="setup-item {{ $user->email_verified_at ? 'completed' : '' }}">
                                                @if($user->email_verified_at)
                                                    <i class="bi bi-check-circle-fill"></i>
                                                @else
                                                    <i class="bi bi-circle"></i>
                                                @endif
                                                <span>{{ __('Verify your email') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="setup-item {{ $user->avatar ? 'completed' : '' }}">
                                                @if($user->avatar)
                                                    <i class="bi bi-check-circle-fill"></i>
                                                @else
                                                    <i class="bi bi-circle"></i>
                                                @endif
                                                <span>{{ __('Add an avatar') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="setup-item {{ $user->about_me ? 'completed' : '' }}">
                                                @if($user->about_me)
                                                    <i class="bi bi-check-circle-fill"></i>
                                                @else
                                                    <i class="bi bi-circle"></i>
                                                @endif
                                                <span>{{ __('Like a post') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Tabs -->
        <ul class="nav nav-tabs profile-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="#overview">{{ __('Overview') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#about">{{ __('About') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#profile-posts">{{ __('Profile posts') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#activity">{{ __('Activity') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#gallery">{{ __('Gallery') }}</a>
            </li>
        </ul>

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
                        <h5 class="mb-0">{{ __('Gallery') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center py-5">{{ __('No media to display.') }}</p>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
