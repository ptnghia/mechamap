@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<div class="container-fluid px-0">
    <!-- Hero Banner -->
    <div class="hero-banner bg-light py-5 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex flex-column flex-md-row align-items-md-center mb-3">
                        <h1 class="h2 mb-0 me-md-4">{{ config('app.name') }}</h1>
                        <div class="d-flex mt-2 mt-md-0">
                            <div class="me-3">
                                <span class="fw-bold text-primary">{{ App\Models\Thread::count() }}+</span>
                                <span class="text-muted small">bài viết</span>
                            </div>
                            <div class="me-3">
                                <span class="fw-bold text-primary">{{ App\Models\User::count() }}+</span>
                                <span class="text-muted small">thành viên</span>
                            </div>
                            <div>
                                <span class="fw-bold text-primary">{{ date('Y') - 2023 }}</span>
                                <span class="text-muted small">năm</span>
                            </div>
                        </div>
                    </div>
                    <p class="lead mb-4">
                        Cộng đồng dành cho những người yêu thích kiến trúc, xây dựng, phát triển đô thị và môi trường đô thị.
                        Tham gia với chúng tôi để chia sẻ tin tức, quan điểm và thảo luận về kiến trúc, xây dựng, giao thông,
                        đường chân trời và nhiều chủ đề khác!
                    </p>
                    <div class="d-flex flex-wrap">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-primary me-2 mb-2">Tham gia cộng đồng</a>
                        @endguest
                        <a href="{{ route('threads.create') }}" class="btn btn-outline-primary mb-2">Tạo bài viết mới</a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <img src="{{ asset('images/city-illustration.svg') }}" alt="City Illustration" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Latest Threads -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Bài viết mới nhất</h5>
                        <a href="{{ route('threads.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </div>
                    <div class="list-group list-group-flush" id="latest-threads">
                        @foreach($latestThreads as $thread)
                            <div class="list-group-item p-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3 d-none d-sm-block">
                                        <img src="{{ $thread->user->profile_photo_url }}" alt="{{ $thread->user->name }}" class="rounded-circle" width="40" height="40">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="mb-1">
                                                <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">{{ $thread->title }}</a>
                                                @if($thread->is_sticky)
                                                    <span class="badge bg-primary ms-1">Sticky</span>
                                                @endif
                                                @if($thread->is_locked)
                                                    <span class="badge bg-danger ms-1">Locked</span>
                                                @endif
                                            </h5>
                                            <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                                        </div>

                                        <!-- Project Details -->
                                        @if($thread->location || $thread->usage || $thread->floors || $thread->status)
                                        <div class="project-details mb-2 small">
                                            @if($thread->location)
                                                <span class="badge bg-light text-dark me-2">{{ $thread->location }}</span>
                                            @endif

                                            @if($thread->usage)
                                                <span class="badge bg-light text-dark me-2">{{ $thread->usage }}</span>
                                            @endif

                                            @if($thread->floors)
                                                <span class="badge bg-light text-dark me-2">{{ $thread->floors }} tầng</span>
                                            @endif

                                            @if($thread->status)
                                                <span class="badge bg-light text-dark me-2">{{ $thread->status }}</span>
                                            @endif
                                        </div>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="small">
                                                <span class="me-3"><i class="bi bi-person"></i> {{ $thread->user->name }}</span>
                                                <span class="me-3"><i class="bi bi-eye"></i> {{ $thread->view_count }} lượt xem</span>
                                                <span><i class="bi bi-chat"></i> {{ $thread->allComments->count() }} phản hồi</span>
                                            </div>

                                            <div>
                                                @if($thread->category)
                                                    <a href="{{ route('threads.index', ['category' => $thread->category->id]) }}" class="badge bg-secondary text-decoration-none">{{ $thread->category->name }}</a>
                                                @endif

                                                @if($thread->forum)
                                                    <a href="{{ route('threads.index', ['forum' => $thread->forum->id]) }}" class="badge bg-info text-decoration-none">{{ $thread->forum->name }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer text-center">
                        <button id="load-more-threads" class="btn btn-outline-primary">Tải thêm</button>
                    </div>
                </div>

                <!-- Featured Projects -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Dự án nổi bật</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            @foreach($featuredThreads as $thread)
                                <div class="col-md-6 p-3 border-bottom {{ $loop->iteration % 2 == 0 ? 'border-start' : '' }}">
                                    <div class="d-flex flex-column h-100">
                                        <a href="{{ route('threads.show', $thread) }}" class="mb-2">
                                            @if($thread->media && $thread->media->first())
                                                <img src="{{ asset('storage/' . $thread->media->first()->path) }}" alt="{{ $thread->title }}" class="img-fluid rounded">
                                            @else
                                                <div class="bg-light rounded" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                                </div>
                                            @endif
                                        </a>
                                        <h5><a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">{{ $thread->title }}</a></h5>
                                        <div class="project-details mb-2 small">
                                            @if($thread->location)
                                                <span class="badge bg-light text-dark me-2">{{ $thread->location }}</span>
                                            @endif

                                            @if($thread->status)
                                                <span class="badge bg-light text-dark me-2">{{ $thread->status }}</span>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-2">{{ Str::limit(strip_tags($thread->content), 100) }}</p>
                                        <div class="mt-auto small text-muted">
                                            <i class="bi bi-person"></i> {{ $thread->user->name }} ·
                                            <i class="bi bi-calendar"></i> {{ $thread->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Top Forums -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Diễn đàn phổ biến</h5>
                        <a href="{{ route('threads.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($topForums as $forum)
                            <a href="{{ route('threads.index', ['forum' => $forum->id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>{{ $forum->name }}</span>
                                <span class="badge bg-primary rounded-pill">{{ $forum->threads_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Top Contributors -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Top đóng góp tháng này</h5>
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($topContributors as $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="32" height="32">
                                    <a href="{{ route('profile.show', $user) }}" class="text-decoration-none">{{ $user->name }}</a>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $user->contribution_count }} bài viết</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Categories -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Danh mục</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($categories as $category)
                            <a href="{{ route('threads.index', ['category' => $category->id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>{{ $category->name }}</span>
                                <span class="badge bg-secondary rounded-pill">{{ $category->threads_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thống kê</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng số bài viết:</span>
                            <span class="fw-bold">{{ App\Models\Thread::count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng số bình luận:</span>
                            <span class="fw-bold">{{ App\Models\Comment::count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng số thành viên:</span>
                            <span class="fw-bold">{{ App\Models\User::count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Thành viên mới nhất:</span>
                            @php
                                $newestUser = App\Models\User::latest()->first();
                            @endphp
                            <span class="fw-bold">{{ $newestUser ? $newestUser->name : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Load more threads functionality
    let page = 1;
    const loadMoreButton = document.getElementById('load-more-threads');

    loadMoreButton.addEventListener('click', function() {
        page++;
        fetch(`/api/threads?page=${page}`)
            .then(response => response.json())
            .then(data => {
                if (data.threads.length > 0) {
                    const threadsContainer = document.getElementById('latest-threads');

                    data.threads.forEach(thread => {
                        const threadElement = createThreadElement(thread);
                        threadsContainer.appendChild(threadElement);
                    });

                    if (data.has_more === false) {
                        loadMoreButton.disabled = true;
                        loadMoreButton.textContent = 'Không còn bài viết';
                    }
                } else {
                    loadMoreButton.disabled = true;
                    loadMoreButton.textContent = 'Không còn bài viết';
                }
            })
            .catch(error => {
                console.error('Error loading more threads:', error);
            });
    });

    function createThreadElement(thread) {
        const listItem = document.createElement('div');
        listItem.className = 'list-group-item p-3';

        // Format date
        const createdAt = new Date(thread.created_at);
        const timeAgo = timeSince(createdAt);

        // Create badges HTML
        let badgesHtml = '';
        if (thread.location || thread.usage || thread.floors || thread.status) {
            badgesHtml = '<div class="project-details mb-2 small">';
            if (thread.location) {
                badgesHtml += `<span class="badge bg-light text-dark me-2">${thread.location}</span>`;
            }
            if (thread.usage) {
                badgesHtml += `<span class="badge bg-light text-dark me-2">${thread.usage}</span>`;
            }
            if (thread.floors) {
                badgesHtml += `<span class="badge bg-light text-dark me-2">${thread.floors} tầng</span>`;
            }
            if (thread.status) {
                badgesHtml += `<span class="badge bg-light text-dark me-2">${thread.status}</span>`;
            }
            badgesHtml += '</div>';
        }

        // Create category and forum badges
        let categoryForumBadges = '<div>';
        if (thread.category) {
            categoryForumBadges += `<a href="/threads?category=${thread.category.id}" class="badge bg-secondary text-decoration-none">${thread.category.name}</a> `;
        }
        if (thread.forum) {
            categoryForumBadges += `<a href="/threads?forum=${thread.forum.id}" class="badge bg-info text-decoration-none">${thread.forum.name}</a>`;
        }
        categoryForumBadges += '</div>';

        // Build the HTML
        listItem.innerHTML = `
            <div class="d-flex">
                <div class="flex-shrink-0 me-3 d-none d-sm-block">
                    <img src="${thread.user.profile_photo_url}" alt="${thread.user.name}" class="rounded-circle" width="40" height="40">
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="mb-1">
                            <a href="/threads/${thread.id}" class="text-decoration-none">${thread.title}</a>
                            ${thread.is_sticky ? '<span class="badge bg-primary ms-1">Sticky</span>' : ''}
                            ${thread.is_locked ? '<span class="badge bg-danger ms-1">Locked</span>' : ''}
                        </h5>
                        <small class="text-muted">${timeAgo}</small>
                    </div>

                    ${badgesHtml}

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="small">
                            <span class="me-3"><i class="bi bi-person"></i> ${thread.user.name}</span>
                            <span class="me-3"><i class="bi bi-eye"></i> ${thread.view_count} lượt xem</span>
                            <span><i class="bi bi-chat"></i> ${thread.comments_count} phản hồi</span>
                        </div>

                        ${categoryForumBadges}
                    </div>
                </div>
            </div>
        `;

        return listItem;
    }

    function timeSince(date) {
        const seconds = Math.floor((new Date() - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) {
            return Math.floor(interval) + " năm trước";
        }

        interval = seconds / 2592000;
        if (interval > 1) {
            return Math.floor(interval) + " tháng trước";
        }

        interval = seconds / 86400;
        if (interval > 1) {
            return Math.floor(interval) + " ngày trước";
        }

        interval = seconds / 3600;
        if (interval > 1) {
            return Math.floor(interval) + " giờ trước";
        }

        interval = seconds / 60;
        if (interval > 1) {
            return Math.floor(interval) + " phút trước";
        }

        return Math.floor(seconds) + " giây trước";
    }
</script>
@endpush
