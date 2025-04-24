@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<!-- Latest Threads -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bài viết mới nhất</h5>
        <a href="{{ route('threads.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
    </div>
    <div class="list-group list-group-flush" id="latest-threads">
        @foreach($latestThreads as $thread)
            <div class="list-group-item thread-item">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3 d-none d-sm-block">
                        <img src="{{ get_avatar_url($thread->user) }}" alt="{{ $thread->user->name }}" class="avatar avatar-md">
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="thread-title">
                                <a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a>
                                @if($thread->is_sticky)
                                    <span class="badge bg-primary ms-1">Sticky</span>
                                @endif
                                @if($thread->is_locked)
                                    <span class="badge bg-danger ms-1">Locked</span>
                                @endif
                            </div>
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

                        <div class="d-flex justify-content-between align-items-center mt-2 thread-meta">
                            <div>
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
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Dự án nổi bật</h5>
    </div>
    <div class="card-body p-0">
        <div class="row g-0">
            @foreach($featuredThreads as $thread)
                <div class="col-md-6 p-3 border-bottom {{ $loop->iteration % 2 == 0 ? 'border-start' : '' }}">
                    <div class="featured-project">
                        <a href="{{ route('threads.show', $thread) }}" class="mb-2 d-block">
                            @php
                                $imagePath = $thread->media && $thread->media->first() ? 'storage/' . $thread->media->first()->path : null;
                            @endphp
                            <img src="{{ get_image_url($imagePath) }}" alt="{{ $thread->title }}" class="featured-project-image">
                        </a>
                        <h5 class="featured-project-title"><a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a></h5>
                        <div class="project-details mb-2 small">
                            @if($thread->location)
                                <span class="badge bg-light text-dark me-2">{{ $thread->location }}</span>
                            @endif

                            @if($thread->status)
                                <span class="badge bg-light text-dark me-2">{{ $thread->status }}</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-2">{{ Str::limit(strip_tags($thread->content), 80) }}</p>
                        <div class="thread-meta">
                            <i class="bi bi-person"></i> {{ $thread->user->name }} ·
                            <i class="bi bi-calendar"></i> {{ $thread->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
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
        listItem.className = 'list-group-item thread-item';
        listItem.innerHTML = `
            <div class="d-flex">
                <div class="flex-shrink-0 me-3 d-none d-sm-block">
                    <img src="${thread.user.profile_photo_url}" alt="${thread.user.name}" class="avatar avatar-md">
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="thread-title">
                            <a href="/threads/${thread.id}">${thread.title}</a>
                            ${thread.is_sticky ? '<span class="badge bg-primary ms-1">Sticky</span>' : ''}
                            ${thread.is_locked ? '<span class="badge bg-danger ms-1">Locked</span>' : ''}
                        </div>
                        <small class="text-muted">${timeAgo}</small>
                    </div>

                    ${badgesHtml}

                    <div class="d-flex justify-content-between align-items-center mt-2 thread-meta">
                        <div>
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
