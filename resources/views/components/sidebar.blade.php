@props(['showSidebar' => true])

@if($showSidebar)
<div class="sidebar-container">
    <!-- Thông tin về cộng đồng -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-info-circle-fill me-2 text-primary"></i>{{ config('app.name') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="forum-stats mb-3">
                <div class="d-flex gap-4 mb-3">
                    <div class="stat-item">
                        <strong>{{ App\Models\Thread::count() }}</strong>
                        <span class="text-muted">bài đăng</span>
                    </div>
                    <div class="stat-item">
                        <strong>{{ App\Models\User::count() }}</strong>
                        <span class="text-muted">thành viên</span>
                    </div>
                </div>
                <div class="text-muted small mb-3">
                    <i class="bi bi-calendar-check me-1"></i> Hoạt động từ {{
                    \Carbon\Carbon::parse(config('app.established_year', '2023'))->format('Y') }}
                </div>
            </div>

            <p class="mb-3">{{ config('app.description', 'Cộng đồng chia sẻ thông tin về kiến trúc, xây dựng, giao
                thông, quy hoạch đô thị và nhiều chủ đề khác.') }}</p>

            <div class="d-grid gap-2">
                @guest
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>Tham gia cộng đồng
                </a>
                @endguest
                <a href="{{ route('business.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-graph-up me-2"></i>Phát triển doanh nghiệp
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
            $relatedCommunities = [
            [
            'name' => 'Kiến Trúc Việt',
            'url' => '#',
            'members' => '25K',
            'image' => 'https://example.com/nonexistent-image.jpg'
            ],
            [
            'name' => 'Quy Hoạch Đô Thị',
            'url' => '#',
            'members' => '18K',
            'image' => placeholder_image(50, 50, 'QH')
            ],
            [
            'name' => 'Giao Thông Xanh',
            'url' => '#',
            'members' => '12K',
            'image' => placeholder_image(50, 50, 'GT')
            ]
            ];
            @endphp

            @foreach($relatedCommunities as $community)
            <div class="d-flex align-items-center mb-2">
                <div class="flex-shrink-0 me-2">
                    <img src="{{ get_image_url($community['image']) }}" alt="{{ $community['name'] }}"
                        class="rounded shadow-sm" width="40" height="40">
                </div>
                <div>
                    <h6 class="mb-1">
                        <a href="{{ $community['url'] }}" class="text-decoration-none">{{ $community['name'] }}</a>
                    </h6>
                    <p class="text-muted small mb-0"><i class="bi bi-people-fill me-1"></i>{{ $community['members'] }}
                        thành viên</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif