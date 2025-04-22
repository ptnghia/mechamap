<div class="card activity-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Activity') }}</h5>
        @if(!isset($showAll))
            <a href="#activity" class="see-all-link">{{ __('See All') }} <i class="bi bi-arrow-right"></i></a>
        @endif
    </div>
    <div class="card-body">
        @if($activities->count() > 0)
            @foreach($activities as $activity)
                <div class="activity-item">
                    <div class="activity-avatar">
                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle" width="40" height="40">
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">
                            @switch($activity->activity_type)
                                @case('thread_created')
                                    {{ __('Created a new thread') }}
                                    @break
                                @case('post_created')
                                    {{ __('Replied to a thread') }}
                                    @break
                                @case('profile_updated')
                                    {{ __('Updated profile information') }}
                                    @break
                                @default
                                    {{ $activity->activity_type }}
                            @endswitch
                        </div>
                        <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
            
            @if(isset($showAll) && $activities->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $activities->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <p>{{ __('The news feed is currently empty.') }}</p>
            </div>
        @endif
    </div>
</div>
