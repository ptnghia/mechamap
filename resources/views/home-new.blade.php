@extends('layouts.app')

@section('title', __('nav.home'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home-enhanced.css') }}">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-background">
        <video autoplay muted loop class="hero-video">
            <source src="{{ asset('videos/mechanical-engineering-hero.mp4') }}" type="video/mp4">
        </video>
        <div class="hero-overlay"></div>
    </div>

    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">
                        {{ __('home.hero_title') }}
                        <span class="text-primary">{{ __('home.hero_highlight') }}</span>
                    </h1>
                    <p class="hero-subtitle">{{ __('home.hero_subtitle') }}</p>

                    <!-- Key Statistics -->
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number" data-count="10000">0</div>
                            <div class="stat-label">{{ __('home.engineers') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-count="118">0</div>
                            <div class="stat-label">{{ __('home.discussions') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-count="500">0</div>
                            <div class="stat-label">{{ __('home.companies') }}</div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="hero-actions">
                        @guest
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-rocket me-2"></i>{{ __('home.join_free') }}
                        </a>
                        <a href="#explore" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-compass me-2"></i>{{ __('home.explore_community') }}
                        </a>
                        @else
                        <a href="{{ route('threads.index') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-comments me-2"></i>{{ __('home.join_discussion') }}
                        </a>
                        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-store me-2"></i>{{ __('home.explore_marketplace') }}
                        </a>
                        @endguest
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hero-image">
                    <img src="{{ asset('images/hero-engineering.png') }}" alt="Engineering Community" class="img-fluid">
                    <div class="floating-elements">
                        <div class="floating-card" data-aos="fade-up" data-aos-delay="100">
                            <i class="fas fa-cogs"></i>
                            <span>CAD/CAM</span>
                        </div>
                        <div class="floating-card" data-aos="fade-up" data-aos-delay="200">
                            <i class="fas fa-robot"></i>
                            <span>Robotics</span>
                        </div>
                        <div class="floating-card" data-aos="fade-up" data-aos-delay="300">
                            <i class="fas fa-industry"></i>
                            <span>Manufacturing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Value Proposition Section -->
<section class="value-proposition py-5" id="explore">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="section-title">{{ __('home.why_choose_mechamap') }}</h2>
                <p class="section-subtitle">{{ __('home.value_proposition_subtitle') }}</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>{{ __('home.learn_value') }}</h4>
                    <p>{{ __('home.learn_description') }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>{{ __('home.connect_value') }}</h4>
                    <p>{{ __('home.connect_description') }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h4>{{ __('home.opportunity_value') }}</h4>
                    <p>{{ __('home.opportunity_description') }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h4>{{ __('home.practice_value') }}</h4>
                    <p>{{ __('home.practice_description') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Actions Panel -->
<section class="quick-actions py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2 class="section-title">{{ __('home.quick_actions') }}</h2>
                <p class="section-subtitle">{{ __('home.quick_actions_subtitle') }}</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="quick-action-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="action-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>{{ __('home.quick_search') }}</h5>
                    <p>{{ __('home.quick_search_desc') }}</p>
                    <a href="{{ route('threads.index') }}" class="btn btn-outline-primary btn-sm">
                        {{ __('buttons.search_now') }}
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="quick-action-card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="action-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h5>{{ __('home.ask_question') }}</h5>
                    <p>{{ __('home.ask_question_desc') }}</p>
                    <a href="{{ route('threads.create') }}" class="btn btn-outline-primary btn-sm">
                        {{ __('buttons.ask_now') }}
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="quick-action-card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="action-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h5>{{ __('home.learn_resources') }}</h5>
                    <p>{{ __('home.learn_resources_desc') }}</p>
                    <a href="{{ route('threads.index') }}" class="btn btn-outline-primary btn-sm">
                        {{ __('buttons.explore_now') }}
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="quick-action-card" data-aos="zoom-in" data-aos-delay="400">
                    <div class="action-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h5>{{ __('home.find_jobs') }}</h5>
                    <p>{{ __('home.find_jobs_desc') }}</p>
                    <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary btn-sm">
                        {{ __('buttons.find_now') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Content -->
<section class="featured-content py-5">
    <div class="container">
        <div class="row">
            <!-- Latest Discussions -->
            <div class="col-lg-8">
                <div class="content-section">
                    <div class="section-header">
                        <h3>{{ __('home.latest_discussions') }}</h3>
                        <a href="{{ route('threads.index') }}" class="btn btn-outline-primary btn-sm">
                            {{ __('buttons.view_all') }}
                        </a>
                    </div>

                    <div class="discussions-list">
                        @forelse($latestThreads ?? [] as $thread)
                        <div class="discussion-item" data-aos="fade-up">
                            <div class="discussion-avatar">
                                <img src="{{ $thread['user']['avatar_url'] }}" alt="{{ $thread['user']['name'] }}">
                            </div>
                            <div class="discussion-content">
                                <h5>
                                    <a href="{{ route('threads.show', $thread['id']) }}">{{ $thread['title'] }}</a>
                                </h5>
                                <p>{{ Str::limit($thread['content'], 150) }}</p>
                                <div class="discussion-meta">
                                    <span class="author">{{ $thread['user']['name'] }}</span>
                                    <span class="category">{{ $thread['forum']['name'] }}</span>
                                    <span class="time">{{ $thread['created_at']->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="discussion-stats">
                                <div class="stat">
                                    <i class="fas fa-eye"></i>
                                    <span>{{ $thread['view_count'] }}</span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-comments"></i>
                                    <span>{{ $thread['posts_count'] }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('home.no_discussions') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Live Activity Feed -->
                <div class="sidebar-widget" data-aos="fade-left">
                    <h4>{{ __('home.live_activity') }}</h4>
                    <div class="activity-feed" id="liveActivityFeed">
                        <!-- Live activities will be loaded here -->
                    </div>
                </div>

                <!-- Top Contributors -->
                <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="100">
                    <div class="widget-header">
                        <h4>{{ __('home.top_contributors') }}</h4>
                        <a href="{{ route('threads.index') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('buttons.view_all') }}
                        </a>
                    </div>
                    <div class="contributors-list">
                        @forelse($topContributors ?? [] as $index => $contributor)
                        <div class="contributor-item">
                            <div class="rank">#{{ $index + 1 }}</div>
                            <img src="{{ $contributor['avatar_url'] }}" alt="{{ $contributor['name'] }}" class="contributor-avatar">
                            <div class="contributor-info">
                                <h6>{{ $contributor['name'] }}</h6>
                                <span class="points">{{ $contributor['points'] }} {{ __('home.points') }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">{{ __('home.no_contributors') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- Weekly Challenge -->
                <div class="sidebar-widget challenge-widget" data-aos="fade-left" data-aos-delay="200">
                    <div class="challenge-header">
                        <i class="fas fa-trophy"></i>
                        <h4>{{ __('home.weekly_challenge') }}</h4>
                    </div>
                    <div class="challenge-content">
                        <h5>{{ __('home.current_challenge') }}</h5>
                        <p>{{ __('home.challenge_description') }}</p>
                        <div class="challenge-progress">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 65%"></div>
                            </div>
                            <small>65% {{ __('home.completed') }}</small>
                        </div>
                        <a href="#" class="btn btn-primary btn-sm mt-2">
                            {{ __('buttons.join_challenge') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust & Credibility -->
<section class="trust-section py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2 class="section-title">{{ __('home.trusted_by') }}</h2>
                <p class="section-subtitle">{{ __('home.trusted_subtitle') }}</p>
            </div>
        </div>

        <!-- Partner Logos -->
        <div class="row align-items-center mb-5">
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="100">
                <img src="{{ asset('images/partners/university-1.png') }}" alt="University Partner" class="partner-logo">
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="200">
                <img src="{{ asset('images/partners/company-1.png') }}" alt="Company Partner" class="partner-logo">
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="300">
                <img src="{{ asset('images/partners/university-2.png') }}" alt="University Partner" class="partner-logo">
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="400">
                <img src="{{ asset('images/partners/company-2.png') }}" alt="Company Partner" class="partner-logo">
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="500">
                <img src="{{ asset('images/partners/university-3.png') }}" alt="University Partner" class="partner-logo">
            </div>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="600">
                <img src="{{ asset('images/partners/company-3.png') }}" alt="Company Partner" class="partner-logo">
            </div>
        </div>

        <!-- Testimonials -->
        <div class="row">
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"{{ __('home.testimonial_1') }}"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('images/testimonials/author-1.jpg') }}" alt="Author">
                        <div>
                            <h6>Nguyễn Văn A</h6>
                            <span>{{ __('home.senior_engineer') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"{{ __('home.testimonial_2') }}"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('images/testimonials/author-2.jpg') }}" alt="Author">
                        <div>
                            <h6>Trần Thị B</h6>
                            <span>{{ __('home.cad_specialist') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"{{ __('home.testimonial_3') }}"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('images/testimonials/author-3.jpg') }}" alt="Author">
                        <div>
                            <h6>Lê Văn C</h6>
                            <span>{{ __('home.manufacturing_manager') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Signup -->
<section class="newsletter-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="text-white mb-3">{{ __('home.stay_updated') }}</h2>
                <p class="text-white-50 mb-4">{{ __('home.newsletter_description') }}</p>

                <form class="newsletter-form" id="newsletterForm">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="{{ __('home.enter_email') }}" required>
                        <button class="btn btn-primary" type="submit">
                            {{ __('buttons.subscribe') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="{{ asset('js/home-enhanced.js') }}"></script>
@endpush
