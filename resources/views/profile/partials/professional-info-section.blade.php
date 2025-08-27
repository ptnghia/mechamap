{{-- Professional Info Section (thay thế About Section) --}}
<div class="card professional-info-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            @if(in_array($user->role, ['manufacturer', 'supplier', 'brand']))
                <i class="fas fa-building"></i> {{ __('profile.business_information') }}
            @else
                <i class="fas fa-user-tie"></i> {{ __('profile.professional_information') }}
            @endif
        </h5>
        @if(!isset($showAll))
            <a href="#about" class="see-all-link">{{ __('profile.see_all') }} <i class="fas fa-arrow-right"></i></a>
        @endif
    </div>
    <div class="card-body">
        @if(in_array($user->role, ['manufacturer', 'supplier', 'brand']))
            {{-- Business User Information --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.company_name') }}</div>
                        <div class="info-value">
                            {{ $user->company_name ?: __('profile.no_information_provided') }}
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.business_type') }}</div>
                        <div class="info-value">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.business_description') }}</div>
                        <div class="info-value">
                            @if($user->business_description)
                                {{ $user->business_description }}
                            @else
                                @if(Auth::id() == $user->id)
                                    <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                                @else
                                    {{ __('profile.no_information_provided') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.business_categories') }}</div>
                        <div class="info-value">
                            @if($user->business_categories)
                                @php
                                    $categories = is_string($user->business_categories) 
                                        ? json_decode($user->business_categories, true) 
                                        : $user->business_categories;
                                @endphp
                                @if(is_array($categories))
                                    @foreach($categories as $category)
                                        <span class="badge bg-secondary me-1 mb-1">{{ $category }}</span>
                                    @endforeach
                                @endif
                            @else
                                {{ __('profile.no_categories_specified') }}
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.verification_status') }}</div>
                        <div class="info-value">
                            @if($user->is_verified_business)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> {{ __('profile.verified') }}
                                </span>
                                @if($user->business_verified_at)
                                    <small class="text-muted d-block">{{ __('profile.verified_on') }} {{ $user->business_verified_at->format('M d, Y') }}</small>
                                @endif
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock"></i> {{ __('profile.pending_verification') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Personal User Information --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.about_me') }}</div>
                        <div class="info-value">
                            @if($user->about_me)
                                {{ $user->about_me }}
                            @else
                                @if(Auth::id() == $user->id)
                                    <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                                @else
                                    {{ __('profile.no_information_provided') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.job_title') }}</div>
                        <div class="info-value">
                            @if($user->job_title)
                                {{ $user->job_title }}
                            @else
                                @if(Auth::id() == $user->id)
                                    <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                                @else
                                    {{ __('profile.no_information_provided') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.company') }}</div>
                        <div class="info-value">
                            @if($user->company)
                                {{ $user->company }}
                            @else
                                @if(Auth::id() == $user->id)
                                    <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                                @else
                                    {{ __('profile.no_information_provided') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.experience_years') }}</div>
                        <div class="info-value">
                            @if($user->experience_years)
                                {{ __('profile.experience_' . str_replace(['-', '+'], '_', $user->experience_years)) }}
                            @else
                                @if(Auth::id() == $user->id)
                                    <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                                @else
                                    {{ __('profile.no_information_provided') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.bio') }}</div>
                        <div class="info-value">
                            @if($user->bio)
                                {{ $user->bio }}
                            @else
                                @if(Auth::id() == $user->id)
                                    <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                                @else
                                    {{ __('profile.no_information_provided') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.skills') }}</div>
                        <div class="info-value">
                            @if($user->skills)
                                @php
                                    $skills = is_string($user->skills) 
                                        ? json_decode($user->skills, true) 
                                        : $user->skills;
                                @endphp
                                @if(is_array($skills))
                                    @foreach($skills as $skill)
                                        <span class="badge bg-primary me-1 mb-1">{{ $skill }}</span>
                                    @endforeach
                                @else
                                    {{ $user->skills }}
                                @endif
                            @else
                                @if(Auth::id() == $user->id)
                                    <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                                @else
                                    {{ __('profile.no_skills_listed') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.signature') }}</div>
                        <div class="info-value">
                            @if($user->signature)
                                {{ $user->signature }}
                            @else
                                @if(Auth::id() == $user->id)
                                    <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                                @else
                                    {{ __('profile.no_information_provided') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($showAll))
            <hr>
            {{-- Additional info for detailed view --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.following') }}</div>
                        <div class="info-value">
                            @if($following > 0)
                                {{ $following }} {{ __('profile.members') }}
                            @else
                                {{ __('profile.follow_others_message') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.followers') }}</div>
                        <div class="info-value">
                            @if($followers > 0)
                                {{ $followers }} {{ __('profile.members') }}
                            @else
                                {{ __('profile.no_followers_yet') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="info-item">
                        <div class="info-label">{{ __('profile.joined') }}</div>
                        <div class="info-value">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
