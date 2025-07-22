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
                        <p class="text-muted mb-2">{{ __('profile.registered') }}</p>
                        <p class="small mb-1">{{ __('profile.joined') }}: {{ $user->created_at->format('M d, Y') }}</p>
                        <p class="small mb-1">
                            {{ __('profile.last_seen') }}:
                            @if($user->last_seen_at)
                                <span title="{{ $user->last_seen_at->format('M d, Y H:i') }}">{{ $user->last_seen_at->diffForHumans() }}</span>
                            @else
                                {{ __('profile.never') }}
                            @endif
                            ·
                            <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">{{ __('profile.viewing_member_profile') }} {{ $user->username }}</a>
                        </p>

                        @if(Auth::check() && Auth::id() != $user->id)
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-secondary">{{ __('profile.report') }}</button>
                            </div>
                        @endif
                    </div>

                    <!-- User Stats -->
                    <div class="col-md-9">
                        <div class="stats-section">
                            <div class="stat-item">
                                <div class="stat-label">{{ __('profile.replies') }}</div>
                                <div class="stat-value">{{ $stats['replies'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">{{ __('profile.discussions_created') }}</div>
                                <div class="stat-value">{{ $stats['discussions_created'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">{{ __('profile.reaction_score') }}</div>
                                <div class="stat-value">{{ $stats['reaction_score'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">{{ __('profile.points') }}</div>
                                <div class="stat-value">{{ $stats['points'] }}</div>
                            </div>
                        </div>

                        <!-- Setup Progress -->
                        @if(Auth::check() && Auth::id() == $user->id && $setupProgress < 5)
                            <div class="card setup-card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">{{ __('profile.get_set_up_title') }}</h5>
                                        <button type="button" class="btn-close" aria-label="Close"></button>
                                    </div>
                                    <p class="text-muted">{{ __('profile.get_set_up_description') }}</p>

                                    <div class="progress mb-3" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($setupProgress / 5) * 100 }}%"
                                            aria-valuenow="{{ $setupProgress }}" aria-valuemin="0" aria-valuemax="5"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="setup-item {{ $user->email_verified_at ? 'completed' : '' }}">
                                                @if($user->email_verified_at)
                                                    <i class="fas fa-check-circle-fill"></i>
                                                @else
                                                    <i class="circle"></i>
                                                @endif
                                                <span>{{ __('profile.verify_email') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="setup-item {{ $user->avatar ? 'completed' : '' }}">
                                                @if($user->avatar)
                                                    <i class="fas fa-check-circle-fill"></i>
                                                @else
                                                    <i class="circle"></i>
                                                @endif
                                                <span>{{ __('profile.add_avatar') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="setup-item {{ $user->about_me ? 'completed' : '' }}">
                                                @if($user->about_me)
                                                    <i class="fas fa-check-circle-fill"></i>
                                                @else
                                                    <i class="circle"></i>
                                                @endif
                                                <span>{{ __('profile.like_post') }}</span>
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
                <a class="nav-link active" href="#overview">{{ __('profile.overview') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#about">{{ __('profile.about') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#profile-posts">{{ __('profile.profile_posts') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#activity">{{ __('profile.activity') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#gallery">{{ __('profile.gallery') }}</a>
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
                        <h5 class="mb-0">{{ __('profile.gallery') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center py-5">{{ __('profile.no_media_to_display') }}</p>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
