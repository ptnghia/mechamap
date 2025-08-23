<div class="card activity-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('profile.activity') }}</h5>
        @if(!isset($showAll))
            <a href="{{ route('profile.activities', $user) }}" class="see-all-link">{{ __('profile.see_all') }} <i class="fas fa-arrow-right"></i></a>
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
                                            {{ __('profile.created_thread') }} {{ Str::limit($activity->thread->title, 50) }}
                                        </a>
                                    @else
                                        {{ __('profile.created_a_new_thread') }}
                                    @endif
                                    @break
                                @case('comment_created')
                                    @if($activity->comment && $activity->comment->thread)
                                        <a href="{{ $activity->getUrl() }}" class="text-decoration-none">
                                            {{ __('profile.commented_on') }} {{ Str::limit($activity->comment->thread->title, 50) }}
                                        </a>
                                    @else
                                        {{ __('profile.commented_on_a_thread') }}
                                    @endif
                                    @break
                                @case('thread_liked')
                                    @if($activity->thread)
                                        <a href="{{ $activity->getUrl() }}" class="text-decoration-none">
                                            {{ __('profile.liked_thread') }} {{ Str::limit($activity->thread->title, 50) }}
                                        </a>
                                    @else
                                        {{ __('profile.liked_a_thread') }}
                                    @endif
                                    @break
                                @case('thread_saved')
                                    @if($activity->thread)
                                        <a href="{{ $activity->getUrl() }}" class="text-decoration-none">
                                            {{ __('profile.saved_thread') }} {{ Str::limit($activity->thread->title, 50) }}
                                        </a>
                                    @else
                                        {{ __('profile.saved_a_thread') }}
                                    @endif
                                    @break
                                @case('profile_updated')
                                    {{ __('profile.updated_profile_information') }}
                                    @break
                                @case('showcase_created_from_thread')
                                    {{ formatActivityType('showcase_created_from_thread') }}
                                    @break
                                @case('comment_posted')
                                    {{ formatActivityType('comment_posted') }}
                                    @break
                                @case('comment_liked')
                                    {{ formatActivityType('comment_liked') }}
                                    @break
                                @case('thread_bookmarked')
                                    {{ formatActivityType('thread_bookmarked') }}
                                    @break
                                @case('user_followed')
                                    {{ formatActivityType('user_followed') }}
                                    @break
                                @case('showcase_created')
                                    {{ formatActivityType('showcase_created') }}
                                    @break
                                @case('showcase_liked')
                                    {{ formatActivityType('showcase_liked') }}
                                    @break
                                @default
                                    {{ formatActivityType($activity->activity_type) }}
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
                <p>{{ __('profile.news_feed_empty') }}</p>
            </div>
        @endif
    </div>
</div>
