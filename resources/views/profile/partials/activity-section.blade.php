<div class="card activity-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Activity') }}</h5>
        @if(!isset($showAll))
            <a href="{{ route('profile.activities', $user) }}" class="see-all-link">{{ __('See All') }} <i class="fas fa-arrow-right"></i></a>
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
                                    @if($activity->thread)
                                        <a href="{{ $activity->getUrl() }}" class="text-decoration-none">
                                            {{ __('Created thread:') }} {{ Str::limit($activity->thread->title, 50) }}
                                        </a>
                                    @else
                                        {{ __('Created a new thread') }}
                                    @endif
                                    @break
                                @case('comment_created')
                                    @if($activity->comment && $activity->comment->thread)
                                        <a href="{{ $activity->getUrl() }}" class="text-decoration-none">
                                            {{ __('Commented on:') }} {{ Str::limit($activity->comment->thread->title, 50) }}
                                        </a>
                                    @else
                                        {{ __('Commented on a thread') }}
                                    @endif
                                    @break
                                @case('thread_liked')
                                    @if($activity->thread)
                                        <a href="{{ $activity->getUrl() }}" class="text-decoration-none">
                                            {{ __('Liked thread:') }} {{ Str::limit($activity->thread->title, 50) }}
                                        </a>
                                    @else
                                        {{ __('Liked a thread') }}
                                    @endif
                                    @break
                                @case('thread_saved')
                                    @if($activity->thread)
                                        <a href="{{ $activity->getUrl() }}" class="text-decoration-none">
                                            {{ __('Saved thread:') }} {{ Str::limit($activity->thread->title, 50) }}
                                        </a>
                                    @else
                                        {{ __('Saved a thread') }}
                                    @endif
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

            @if(isset($showAll) && method_exists($activities, 'hasPages') && $activities->hasPages())
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
