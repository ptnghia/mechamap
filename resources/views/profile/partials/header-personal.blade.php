{{-- Header cho User Cá nhân (Member, Senior Member) --}}
<div class="profile-header-personal">
    <div class="row">
        <!-- Avatar and Basic Info -->
        <div class="col-md-3 text-center">
            <div class="avatar-container mb-3">
                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" width="150" height="150" class="rounded-circle">
                <div class="avatar-letter">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            </div>
            
            <!-- Action Buttons -->
            @if(Auth::check() && Auth::id() != $user->id)
                <div class="action-buttons mt-3">
                    <button class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-user-plus"></i> {{ __('profile.follow') }}
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-envelope"></i> {{ __('profile.contact') }}
                    </button>
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-flag"></i> {{ __('profile.report') }}
                    </button>
                </div>
            @endif
        </div>

        <!-- Professional Info and Stats -->
        <div class="col-md-9">
            <!-- Name and Professional Title -->
            <div class="professional-title mb-3">
                <h2 class="mb-1">{{ $user->name }}</h2>
                <p class="text-muted mb-2">{{ '@' . $user->username }}</p>
                
                @if($user->job_title || $user->company)
                    <div class="job-info mb-2">
                        @if($user->job_title)
                            <span class="job-title">{{ $user->job_title }}</span>
                        @endif
                        @if($user->job_title && $user->company)
                            <span class="text-muted"> tại </span>
                        @endif
                        @if($user->company)
                            <span class="company">{{ $user->company }}</span>
                        @endif
                    </div>
                @endif

                <!-- Location and Experience -->
                <div class="location-experience mb-2">
                    @if($user->location)
                        <span class="location me-3">
                            <i class="fas fa-map-marker-alt text-muted"></i> {{ $user->location }}
                        </span>
                    @endif
                    
                    @if($user->experience_years)
                        <span class="experience-badge badge bg-info">
                            <i class="fas fa-briefcase"></i> 
                            {{ __('profile.experience_' . str_replace(['-', '+'], '_', $user->experience_years)) }}
                        </span>
                    @endif
                </div>

                <!-- Status and Role Badges -->
                <div class="badges mb-3">
                    <span class="badge bg-success">
                        <i class="fas fa-circle"></i> {{ __('profile.active') }}
                    </span>
                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : 'info') }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                    
                    @if($user->is_verified_business)
                        <span class="badge bg-primary">
                            <i class="fas fa-check-circle"></i> {{ __('profile.verified') }}
                        </span>
                    @endif
                </div>

                <!-- Contact Information -->
                <div class="contact-info mb-3">
                    <div class="contact-links">
                        @if($user->phone)
                            <a href="tel:{{ $user->phone }}" class="contact-link me-3" title="{{ __('profile.phone') }}">
                                <i class="fas fa-phone"></i> {{ $user->phone }}
                            </a>
                        @endif
                        
                        @if($user->linkedin_url)
                            <a href="{{ $user->linkedin_url }}" target="_blank" rel="nofollow" class="contact-link me-3" title="LinkedIn">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </a>
                        @endif
                        
                        @if($user->github_url)
                            <a href="{{ $user->github_url }}" target="_blank" rel="nofollow" class="contact-link me-3" title="GitHub">
                                <i class="fab fa-github"></i> GitHub
                            </a>
                        @endif
                        
                        @if($user->website)
                            <a href="{{ $user->website }}" target="_blank" rel="nofollow" class="contact-link me-3" title="{{ __('profile.website') }}">
                                <i class="fas fa-globe"></i> Website
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Last Seen -->
                <p class="last-seen small text-muted mb-3">
                    <i class="fas fa-clock"></i> {{ __('profile.last_seen') }}
                    @if($user->last_seen_at)
                        <span title="{{ $user->last_seen_at->format('M d, Y H:i') }}">{{ $user->last_seen_at->diffForHumans() }}</span>
                    @else
                        {{ __('profile.never') }}
                    @endif
                </p>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['replies'] ?? 0 }}</div>
                            <div class="stat-label">{{ __('profile.replies') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['discussions_created'] ?? 0 }}</div>
                            <div class="stat-label">{{ __('profile.threads') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['reaction_score'] ?? 0 }}</div>
                            <div class="stat-label">{{ __('profile.reactions') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['profile_views'] ?? 0 }}</div>
                            <div class="stat-label">{{ __('profile.profile_views') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
