{{-- Header cho User Doanh nghiệp (Manufacturer, Supplier, Brand) --}}
<div class="profile-header-business">
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

        <!-- Business Info and Stats -->
        <div class="col-md-9">
            <!-- Company Name and Info -->
            <div class="business-title mb-3">
                <h2 class="mb-1">
                    {{ $user->company_name ?: $user->name }}
                    @if($user->is_verified_business)
                        <span class="verified-badge">
                            <i class="fas fa-check-circle text-primary"></i>
                        </span>
                    @endif
                </h2>
                <p class="text-muted mb-2">{{ '@' . $user->username }}</p>
                
                @if($user->business_description)
                    <p class="business-description mb-2">{{ Str::limit($user->business_description, 150) }}</p>
                @endif

                <!-- Business Address and Categories -->
                <div class="business-details mb-2">
                    @if($user->business_address)
                        <div class="business-address mb-2">
                            <i class="fas fa-map-marker-alt text-muted"></i> {{ $user->business_address }}
                        </div>
                    @endif
                    
                    @if($user->business_categories)
                        <div class="business-categories mb-2">
                            @php
                                $categories = is_string($user->business_categories) 
                                    ? json_decode($user->business_categories, true) 
                                    : $user->business_categories;
                            @endphp
                            @if(is_array($categories))
                                @foreach(array_slice($categories, 0, 3) as $category)
                                    <span class="badge bg-secondary me-1">{{ $category }}</span>
                                @endforeach
                                @if(count($categories) > 3)
                                    <span class="text-muted">+{{ count($categories) - 3 }} {{ __('profile.more') }}</span>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Status and Verification Badges -->
                <div class="badges mb-3">
                    <span class="badge bg-success">
                        <i class="fas fa-circle"></i> {{ __('profile.active') }}
                    </span>
                    <span class="badge bg-{{ $user->role === 'manufacturer' ? 'primary' : ($user->role === 'supplier' ? 'info' : 'warning') }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                    
                    @if($user->is_verified_business)
                        <span class="badge bg-success">
                            <i class="fas fa-shield-alt"></i> {{ __('profile.verified_business') }}
                        </span>
                    @endif

                    @if($user->business_rating && $user->business_rating > 0)
                        <span class="badge bg-warning">
                            <i class="fas fa-star"></i> {{ number_format($user->business_rating, 1) }}
                        </span>
                    @endif
                </div>

                <!-- Business Contact Information -->
                <div class="contact-info mb-3">
                    <div class="contact-links">
                        @if($user->business_phone)
                            <a href="tel:{{ $user->business_phone }}" class="contact-link me-3" title="{{ __('profile.business_phone') }}">
                                <i class="fas fa-phone"></i> {{ $user->business_phone }}
                            </a>
                        @endif
                        
                        @if($user->business_email)
                            <a href="mailto:{{ $user->business_email }}" class="contact-link me-3" title="{{ __('profile.business_email') }}">
                                <i class="fas fa-envelope"></i> {{ $user->business_email }}
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

            <!-- Business Stats Row -->
            <div class="stats-row">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['products_count'] ?? 0 }}</div>
                            <div class="stat-label">{{ __('profile.products') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['total_reviews'] ?? 0 }}</div>
                            <div class="stat-label">{{ __('profile.reviews') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-value">{{ number_format($user->business_rating ?? 0, 1) }}</div>
                            <div class="stat-label">{{ __('profile.rating') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['business_score'] ?? 0 }}</div>
                            <div class="stat-label">{{ __('profile.business_score') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
