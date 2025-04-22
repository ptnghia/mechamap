<div class="card about-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('About') }}</h5>
        @if(!isset($showAll))
            <a href="#about" class="see-all-link">{{ __('See All') }} <i class="bi bi-arrow-right"></i></a>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('About Me') }}</div>
                @if($user->about_me)
                    <div class="about-value">{{ $user->about_me }}</div>
                @else
                    <div class="about-value">
                        @if(Auth::id() == $user->id)
                            <a href="{{ route('profile.edit') }}">{{ __('Edit in account settings') }}</a>
                        @else
                            {{ __('No information provided.') }}
                        @endif
                    </div>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('Website') }}</div>
                @if($user->website)
                    <div class="about-value">
                        <a href="{{ $user->website }}" target="_blank" rel="nofollow">{{ $user->website }}</a>
                    </div>
                @else
                    <div class="about-value">
                        @if(Auth::id() == $user->id)
                            <a href="{{ route('profile.edit') }}">{{ __('Edit in account settings') }}</a>
                        @else
                            {{ __('No information provided.') }}
                        @endif
                    </div>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('Location') }}</div>
                @if($user->location)
                    <div class="about-value">{{ $user->location }}</div>
                @else
                    <div class="about-value">
                        @if(Auth::id() == $user->id)
                            <a href="{{ route('profile.edit') }}">{{ __('Edit in account settings') }}</a>
                        @else
                            {{ __('No information provided.') }}
                        @endif
                    </div>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('Signature') }}</div>
                @if($user->signature)
                    <div class="about-value">{{ $user->signature }}</div>
                @else
                    <div class="about-value">
                        @if(Auth::id() == $user->id)
                            <a href="{{ route('profile.edit') }}">{{ __('Edit in account settings') }}</a>
                        @else
                            {{ __('No information provided.') }}
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
                <div class="about-label">{{ __('Following') }}</div>
                <div class="about-value">
                    @if($following > 0)
                        {{ $following }} {{ __('members') }}
                    @else
                        {{ __('Follow others to stay up to date on what they post') }}
                    @endif
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="about-label">{{ __('Followers') }}</div>
                <div class="about-value">
                    @if($followers > 0)
                        {{ $followers }} {{ __('members') }}
                    @else
                        {{ __('No followers yet.') }}
                    @endif
                </div>
            </div>
            
            @if(isset($showAll))
                <div class="col-md-3 mb-3">
                    <div class="about-label">{{ __('Joined') }}</div>
                    <div class="about-value">{{ $user->created_at->format('M d, Y') }}</div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="about-label">{{ __('Last Seen') }}</div>
                    <div class="about-value">
                        @if($user->last_seen_at)
                            {{ $user->last_seen_at->diffForHumans() }}
                        @else
                            {{ __('Never') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
