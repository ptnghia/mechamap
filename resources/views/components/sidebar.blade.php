{{--
    MechaMap Unified Sidebar Component
    Sidebar thông minh tự động chọn loại sidebar phù hợp theo context
--}}
@props(['showSidebar' => true])

@if($showSidebar)
@php
$currentRoute = Route::currentRouteName();
$isProfessionalMode = request()->get('professional', true); // Enable by default

// Determine sidebar type based on current route
$sidebarType = 'default';
if (str_contains($currentRoute, 'showcase') || str_contains(request()->path(), 'showcase')) {
    $sidebarType = 'showcase';
} elseif (str_contains($currentRoute, 'marketplace') || str_contains(request()->path(), 'marketplace')) {
    $sidebarType = 'marketplace';
} elseif ($currentRoute === 'threads.create') {
    $sidebarType = 'thread-creation';
} elseif ($isProfessionalMode) {
    $sidebarType = 'professional';
}
@endphp

@if($sidebarType === 'showcase')
<!-- Showcase Sidebar -->
@include('components.sidebar-showcase', ['user' => auth()->user()])
@elseif($sidebarType === 'marketplace')
<!-- Marketplace Sidebar -->
@include('components.sidebar-marketplace', ['user' => auth()->user()])
@elseif($sidebarType === 'thread-creation')
{{-- {{ t_sidebar('main.thread_creation_sidebar') }} --}}
@include('components.thread-creation-sidebar')
@elseif($sidebarType === 'professional')
{{-- {{ t_sidebar('main.professional_sidebar') }} --}}
@include('components.sidebar-professional', ['user' => auth()->user()])
@else
{{-- {{ t_sidebar('main.default_sidebar') }} --}}
<div class="sidebar-container">
    {{-- {{ t_sidebar('main.community_info') }} --}}
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <div class="forum-stats mb-3">
                <div class="d-flex gap-4 mb-3">
                    <div class="stat-item">
                        <strong>{{ App\Models\Thread::count() }}</strong>
                        <span class="text-muted">{{ t_sidebar('main.threads') }}</span>
                    </div>
                    <div class="stat-item">
                        <strong>{{ App\Models\User::count() }}</strong>
                        <span class="text-muted">{{ t_sidebar('main.members') }}</span>
                    </div>
                </div>
                <div class="text-muted small mb-3">
                    <i class="fas fa-calendar-check me-1"></i> {{ t_sidebar('main.established_since') }} {{
                    \Carbon\Carbon::parse(config('app.established_year', '2023'))->format('Y') }}
                </div>
            </div>

            <p class="mb-3">{{ config('app.description', t_sidebar('main.community_description')) }}</p>

            <div class="d-grid gap-2">
                @guest
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>{{ t_sidebar('main.join_community') }}
                </a>
                @endguest
                <a href="{{ route('business.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-chart-line me-2"></i>{{ t_sidebar('main.business_development') }}
                </a>
            </div>
        </div>
    </div>

    {{-- {{ t_sidebar('main.featured_topics') }} --}}
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-star me-2 text-warning"></i>{{ t_sidebar('main.featured_topics') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @php
                $featuredThreads = App\Models\Thread::with('user')
                ->where('is_featured', true)
                ->orWhere('is_sticky', true)
                ->latest()
                ->take(5)
                ->get();
                @endphp

                @forelse($featuredThreads as $thread)
                <a href="{{ route('threads.show', $thread) }}" class="list-group-item list-group-item-action py-2">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-2">
                            <img src="{{ $thread->user->getAvatarUrl() }}" alt="{{ $thread->user->name }}"
                                class="rounded-circle" width="32" height="32"
                                onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($thread->user->name, 0, 1))]) }}'"
                        </div>
                        <div>
                            <h6 class="mb-1 text-truncate" style="max-width: 100%;">{{ $thread->title }}</h6>
                            <div class="d-flex align-items-center small text-muted">
                                <span class="me-2"><i class="fas fa-user me-1"></i>{{ $thread->user->name
                                    }}</span>
                                <span class="me-2"><i class="fas fa-comment-dots me-1"></i>{{ $thread->comments->count()
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="list-group-item py-2">
                    <p class="text-muted mb-0 small">{{ t_sidebar('main.no_featured_topics') }}</p>
                </div>
                @endforelse
            </div>

            <div class="card-footer bg-white text-center">
                <a href="{{ route('threads.index') }}" class="btn btn-sm btn-link text-decoration-none">{{ t_sidebar('main.view_more') }}</a>
            </div>
        </div>
    </div>

    {{-- {{ t_sidebar('main.popular_forums') }} --}}
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-folder me-2 text-primary"></i>{{ t_sidebar('main.popular_forums') }}</h5>
            <a href="{{ route('forums.index') }}" class="btn btn-sm btn-link">{{ t_sidebar('main.view_all') }}</a>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @php
                $topForums = App\Models\Forum::withCount('threads')
                ->orderBy('threads_count', 'desc')
                ->take(5)
                ->get();
                @endphp

                @forelse($topForums as $forum)
                <a href="{{ route('forums.show', $forum) }}" class="list-group-item list-group-item-action py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $forum->name }}</h6>
                            <p class="text-muted small mb-0">{{ Str::limit($forum->description, 50) }}</p>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $forum->threads_count }}</span>
                    </div>
                </a>
                @empty
                <div class="list-group-item py-2">
                    <p class="text-muted mb-0 small">{{ t_sidebar('main.no_forums') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- {{ t_sidebar('main.active_members') }} --}}
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-users me-2 text-success"></i>{{ t_sidebar('main.active_members') }}</h5>
            <a href="{{ route('members.index') }}" class="btn btn-sm btn-link">{{ t_sidebar('main.view_all') }}</a>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @php
                $topContributors = App\Models\User::withCount(['threads', 'comments'])
                ->orderByRaw('threads_count + comments_count DESC')
                ->take(5)
                ->get();
                @endphp

                @forelse($topContributors as $user)
                <a href="{{ route('profile.show', $user->username) }}"
                    class="list-group-item list-group-item-action py-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle"
                                width="32" height="32"
                                onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($user->name, 0, 1))]) }}'"
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <p class="text-muted small mb-0">{{ $user->threads_count + $user->comments_count }} {{ t_sidebar('main.contributions') }}
                            </p>
                        </div>
                    </div>
                </a>
                @empty
                <div class="list-group-item py-2">
                    <p class="text-muted mb-0 small">{{ t_sidebar('main.no_active_members') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- {{ t_sidebar('main.related_communities') }} --}}
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-globe me-2 text-info"></i>{{ t_sidebar('main.related_communities') }}</h5>
        </div>
        <div class="card-body">
            @php
            // Lấy các diễn đàn phổ biến nhất (có nhiều threads nhất)
            $relatedForums = \App\Models\Forum::with(['media' => function($query) {
            $query->where('file_type', 'like', 'image/%');
            }])
            ->withCount('threads')
            ->where('parent_id', null) // Chỉ lấy forums chính, không phải sub-forums
            ->orderBy('threads_count', 'desc')
            ->limit(3)
            ->get();
            @endphp

            @forelse($relatedForums as $forum)
            <div class="d-flex align-items-center mb-2">
                <div class="flex-shrink-0 me-2"> @php
                    // Lấy ảnh đại diện của forum từ media relationship
                    $forumImage = $forum->media->first();
                    if ($forumImage) {
                        // Nếu file_path là URL đầy đủ thì dùng trực tiếp
                        if (filter_var($forumImage->file_path, FILTER_VALIDATE_URL)) {
                            $imageUrl = $forumImage->file_path;
                        } elseif (strpos($forumImage->file_path, '/images/') === 0) {
                            // Nếu file_path bắt đầu bằng /images/ thì dùng asset() trực tiếp
                            $imageUrl = asset($forumImage->file_path);
                        } else {
                            // Loại bỏ slash đầu để tránh double slash
                            $cleanPath = ltrim($forumImage->file_path, '/');
                            $imageUrl = asset('storage/' . $cleanPath);
                        }
                    } else {
                        // Fallback về avatar generator nội bộ nếu không có ảnh
                        $forumInitials = strtoupper(substr($forum->name, 0, 2));
                        $imageUrl = route('avatar.generate', ['initial' => $forumInitials]);
                    }
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $forum->name }}" class="rounded shadow-sm" width="40" height="40"
                        style="object-fit: cover;">
                </div>
                <div>
                    <h6 class="mb-1">
                        <a href="{{ route('forums.show', $forum->slug) }}" class="text-decoration-none">{{ $forum->name
                            }}</a>
                    </h6>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-comments me-1"></i>{{ $forum->threads_count }}
                        {{ t_sidebar('main.topics') }}
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-3">
                <p class="text-muted mb-0 small">{{ t_sidebar('main.no_forums') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endif
@endif
