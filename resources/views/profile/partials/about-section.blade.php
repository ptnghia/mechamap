<div class="card about-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('profile.about') }}</h5>
        @if(!isset($showAll))
            <a href="#about" class="see-all-link">{{ __('profile.see_all') }} <i class="fas fa-arrow-right"></i></a>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('profile.about_me') }}</div>
                @if($user->about_me)
                    <div class="about-value">{{ $user->about_me }}</div>
                @else
                    <div class="about-value">
                        @if(Auth::id() == $user->id)
                            <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                        @else
                            {{ __('profile.no_information_provided') }}
                        @endif
                    </div>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('profile.website') }}</div>
                @if($user->website)
                    <div class="about-value">
                        <a href="{{ $user->website }}" target="_blank" rel="nofollow">{{ $user->website }}</a>
                    </div>
                @else
                    <div class="about-value">
                        @if(Auth::id() == $user->id)
                            <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                        @else
                            {{ __('profile.no_information_provided') }}
                        @endif
                    </div>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('profile.location') }}</div>
                @if($user->location)
                    <div class="about-value">{{ $user->location }}</div>
                @else
                    <div class="about-value">
                        @if(Auth::id() == $user->id)
                            <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                        @else
                            {{ __('profile.no_information_provided') }}
                        @endif
                    </div>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('profile.signature') }}</div>
                @if($user->signature)
                    <div class="about-value">{{ $user->signature }}</div>
                @else
                    <div class="about-value">
                        @if(Auth::id() == $user->id)
                            <a href="{{ route('dashboard.profile.edit') }}">{{ __('profile.edit_in_account_settings') }}</a>
                        @else
                            {{ __('profile.no_information_provided') }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if(isset($showAll))
            <hr>
        @endif

        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('profile.following') }}</div>
                <div class="about-value">
                    @if($following > 0)
                        {{ $following }} {{ __('profile.members') }}
                    @else
                        {{ __('profile.follow_others_message') }}
                    @endif
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('profile.followers') }}</div>
                <div class="about-value">
                    @if($followers > 0)
                        {{ $followers }} {{ __('profile.members') }}
                    @else
                        {{ __('profile.no_followers_yet') }}
                    @endif
                </div>
            </div>

            @if(isset($showAll))
                <div class="col-md-3 mb-3">
                    <div class="about-label">{{ __('profile.joined') }}</div>
                    <div class="about-value">{{ $user->created_at->format('M d, Y') }}</div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="about-label">{{ __('profile.last_seen') }}</div>
                    <div class="about-value">
                        @if($user->last_seen_at)
                            {{ $user->last_seen_at->diffForHumans() }}
                        @else
                            {{ __('profile.never') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
