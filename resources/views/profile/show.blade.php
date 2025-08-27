@extends('layouts.app-full')

@section('title', 'Profile ' . $user->name . ' | MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/users.css') }}">
@endpush

@section('content')
<div class="body_page">
    <!-- Profile Header -->
    <div class="card mb-4">
        <div class="card-body">
            @if(isset($isBusinessUser) && $isBusinessUser)
                @include('profile.partials.header-business', ['user' => $user, 'stats' => $stats])
            @else
                @include('profile.partials.header-personal', ['user' => $user, 'stats' => $stats])
            @endif
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
                        <a class="nav-link active" href="#overview" data-tab="overview">
                            <i class="fas fa-home"></i> {{ __('profile.overview') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#professional" data-tab="professional">
                            @if(isset($isBusinessUser) && $isBusinessUser)
                                <i class="fas fa-building"></i> {{ __('profile.business_info') }}
                            @else
                                <i class="fas fa-user-tie"></i> {{ __('profile.professional_info') }}
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#my-threads" data-tab="my-threads">
                            <i class="fas fa-comments"></i> {{ __('profile.my_threads') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#activity" data-tab="activity">
                            <i class="fas fa-chart-line"></i> {{ __('profile.activity') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#portfolio" data-tab="portfolio">
                            <i class="fas fa-briefcase"></i> {{ __('profile.portfolio') }}
                        </a>
                    </li>

                    @if($isSellerUser)
                    <li class="nav-item">
                        <a class="nav-link" href="#products" data-tab="products">
                            <i class="fas fa-box"></i> {{ __('profile.products') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview Tab -->
                <div id="overview" class="tab-pane active">
                    <!-- Professional Info Section (Overview) -->
                    @include('profile.partials.professional-info-section', [
                        'user' => $user,
                        'followers' => $followers,
                        'following' => $following
                    ])

                    <!-- Activity Section (Overview) -->
                    @include('profile.partials.activity-section', ['user' => $user, 'activities' => $activities])
                </div>

                <!-- Professional Info Tab -->
                <div id="professional" class="tab-pane d-none">
                    @include('profile.partials.professional-info-section', [
                        'user' => $user,
                        'followers' => $followers,
                        'following' => $following,
                        'showAll' => true
                    ])
                </div>

                <!-- My Threads Tab -->
                <div id="my-threads" class="tab-pane d-none">
                    @include('profile.partials.my-threads-section', ['user' => $user, 'userThreads' => $userThreads])
                </div>

                <!-- Activity Tab -->
                <div id="activity" class="tab-pane d-none">
                    @include('profile.partials.activity-section', ['user' => $user, 'activities' => $activities, 'showAll' => true])
                </div>

                <!-- Portfolio Tab -->
                <div id="portfolio" class="tab-pane d-none">
                    @include('profile.partials.portfolio-section', ['user' => $user, 'portfolioItems' => $portfolioItems])
                </div>

                @if($isSellerUser)
                <!-- Products Tab -->
                <div id="products" class="tab-pane d-none">
                    @include('profile.partials.products-section', ['user' => $user])
                </div>
                @endif
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
