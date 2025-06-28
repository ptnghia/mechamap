@props(['showSidebar' => true])

@if($showSidebar)
@php
$currentRoute = Route::currentRouteName();
$isProfessionalMode = request()->get('professional', true); // Enable by default
@endphp

@if($isProfessionalMode)
<!-- Professional Engineering Sidebar -->
@include('components.sidebar-professional', ['user' => auth()->user()])
@elseif($currentRoute === 'threads.create')
<!-- Sidebar chuyên dụng cho trang tạo threads -->
@include('components.thread-creation-sidebar')
@else
<!-- Sidebar thông thường -->
<div class="sidebar-container">
    <!-- Thông tin về cộng đồng -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <div class="forum-stats mb-3">
                <div class="d-flex gap-4 mb-3">
                    <div class="stat-item">
                        <strong>{{ App\Models\Thread::count() }}</strong>
                        <span class="text-muted">{{ __('forum.threads') }}</span>
                    </div>
                    <div class="stat-item">
                        <strong>{{ App\Models\User::count() }}</strong>
                        <span class="text-muted">{{ __('user.members') }}</span>
                    </div>
                </div>
                <div class="text-muted small mb-3">
                    <i class="bi bi-calendar-check me-1"></i> {{ __('content.active_since') }} {{
                    \Carbon\Carbon::parse(config('app.established_year', '2023'))->format('Y') }}
                </div>
            </div>

            <p class="mb-3">{{ config('app.description', 'Cộng đồng chia sẻ thông tin về kiến trúc, xây dựng, giao
                thông, quy hoạch đô thị và nhiều chủ đề khác.') }}</p>

            <div class="d-grid gap-2">
                @guest
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>{{ __('content.join_community') }}
                </a>
                @endguest
                <a href="{{ route('business.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-graph-up me-2"></i>{{ __('content.business_development') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Các chủ đề mới/nổi bật -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-star-fill me-2 text-warning"></i>Chủ đề nổi bật</h5>
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
                            <img src="{{ get_avatar_url($thread->user) }}" alt="{{ $thread->user->name }}"
                                class="rounded-circle" width="32" height="32">
                        </div>
                        <div>
                            <h6 class="mb-1 text-truncate" style="max-width: 100%;">{{ $thread->title }}</h6>
                            <div class="d-flex align-items-center small text-muted">
                                <span class="me-2"><i class="bi bi-person-fill me-1"></i>{{ $thread->user->name
                                    }}</span>
                                <span class="me-2"><i class="bi bi-chat-dots me-1"></i>{{ $thread->comments->count()
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="list-group-item py-2">
                    <p class="text-muted mb-0 small">Chưa có chủ đề nổi bật.</p>
                </div>
                @endforelse
            </div>

            <div class="card-footer bg-white text-center">
                <a href="{{ route('threads.index') }}" class="btn btn-sm btn-link text-decoration-none">Xem thêm</a>
            </div>
        </div>
    </div>

    <!-- Các diễn đàn hàng đầu -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-collection-fill me-2 text-primary"></i>Diễn đàn phổ biến</h5>
            <a href="{{ route('forums.index') }}" class="btn btn-sm btn-link">Xem tất cả</a>
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
                    <p class="text-muted mb-0 small">Chưa có diễn đàn nào.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Những người đóng góp hàng đầu -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-people-fill me-2 text-success"></i>Thành viên tích cực</h5>
            <a href="{{ route('members.index') }}" class="btn btn-sm btn-link">Xem tất cả</a>
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
                            <img src="{{ get_avatar_url($user) }}" alt="{{ $user->name }}" class="rounded-circle"
                                width="32" height="32">
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <p class="text-muted small mb-0">{{ $user->threads_count + $user->comments_count }} đóng góp
                            </p>
                        </div>
                    </div>
                </a>
                @empty
                <div class="list-group-item py-2">
                    <p class="text-muted mb-0 small">Chưa có thành viên tích cực.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Các cộng đồng được đề xuất -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-globe2 me-2 text-info"></i>Cộng đồng liên quan</h5>
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
                        // Fallback về UI Avatars nếu không có ảnh
                        $imageUrl = 'https://ui-avatars.com/api/?name=' . urlencode(substr($forum->name, 0, 2)) .
                        '&background=random&color=fff&size=40';
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
                        <i class="bi bi-chat-text me-1"></i>{{ $forum->threads_count }}
                        {{ $forum->threads_count == 1 ? 'chủ đề' : 'chủ đề' }}
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-3">
                <p class="text-muted mb-0 small">Chưa có diễn đàn nào.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endif
@endif
