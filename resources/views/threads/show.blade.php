@extends('layouts.app')

@section('title', $thread->title)

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/threads.css') }}">
@endpush

@section('content')
<div class="body_page">
    <!-- Main Thread -->
    <div class="detail_thread">
        <div class="detail_thread_body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center thread_user">
                    <img src="{{ $thread->user->getAvatarUrl() }}" alt="{{ $thread->user->name }}"
                        class="rounded-circle me-2" width="40" height="40"
                        onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($thread->user->name, 0, 1))]) }}'">
                    <div>
                        <a href="{{ route('profile.show', $thread->user->username ?? $thread->user->id) }}"
                            class="fw-bold text-decoration-none">{{
                            $thread->user->name }}</a>
                        <div class="text-muted small">
                            <span>{{ $thread->user->threads_count ?? 0 }} {{ __('thread.posts') }}</span> ·
                            <span>{{ __('thread.joined') }} {{ $thread->user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="thread-actions d-flex gap-2 align-items-center">
                    <x-thread-follow-button :thread="$thread" size="normal" />
                    <a href="#comment-{{ $comments->count() > 0 ? $comments->last()->id : '' }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-down"></i> {{ __('thread.go_to_end') }}
                    </a>
                </div>
            </div>
            <div class="thread-header">
                <div class="mb-2">
                    <h1 class="thread-title">{{ $thread->title }}</h1>
                </div>
                <div class="thread-meta">
                    <div class="d-flex justify-content-start g-3">
                        <div class="thread-meta-item">
                            <i class="fas fa-eye"></i> {{ number_format($thread->view_count) }} {{ __('thread.views') }}
                        </div>
                        <div class="thread-meta-item">
                            <i class="fas fa-comment"></i> {{ number_format($thread->comments_count ?? 0) }} {{ __('thread.replies') }}
                        </div>

                    </div>
                    <div class="d-flex align-items-md-center justify-content-end">
                        <div class="thread-meta-item">
                            <i class="fas fa-users"></i> {{ number_format($thread->participant_count) }} {{ __('thread.participants') }}
                        </div>
                        <div class="thread-meta-item">
                        <i class="fa-solid fa-calendar-days me-1"></i> {{ $thread->created_at->diffForHumans() }}
                        </div>
                    </div>

                </div>
            </div>
            <!-- Thread Featured Image -->
            @if($thread->featured_image)
            <div class="thread-featured-image mb-3">
                <img src="{{ $thread->featured_image }}" alt="{{ $thread->title }}" class="img-fluid rounded shadow"
                    style="max-height: 400px; width: 100%; object-fit: cover;"
                    onerror="this.src='{{ asset('images/placeholder.svg') }}'">
            </div>
            @endif

            <!-- Thread Content -->
            <div class="thread-content">
                {!! $thread->content !!}
            </div>

            <!-- Thread Images Gallery -->
            @if($thread->media && count($thread->media) > 0)
            <div class="thread-images mt-3">
                <h6 class="mb-3 title_page_sub">
                    <i class="fas fa-images me-1"> </i>{{ __('thread.image_gallery_count', ['count' => count($thread->media)]) }}
                </h6>
                <div class="row g-3">
                    @foreach($thread->media as $media)
                    @if($media->file_category === 'image' || str_starts_with($media->mime_type ?? '', 'image/'))
                    <div class="col-md-4 col-sm-6 mb-3">
                        <a href="{{ asset($media->file_path) }}"
                            data-fancybox="thread-images"
                            data-caption="{{ $media->file_name }}">
                            <img src="{{ asset($media->file_path) }}"
                                alt="{{ $media->file_name }}"
                                class="img-fluid rounded shadow-sm"
                                style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;"
                                onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
                        </a>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            <div class="threads-footer d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <!-- Like Button -->
                    @auth
                    <button type="button"
                            class="btn btn-sm btn_meta {{ $isLiked ? 'active' : '' }} btn-like"
                            data-thread-id="{{ $thread->id }}"
                            data-liked="{{ $isLiked ? 'true' : 'false' }}"
                            title="{{ $isLiked ? __('thread.unlike') : __('thread.like') }}">
                        <i class="fas fa-thumbs-up"></i>
                        {{ __('thread.like') }}
                        <span class="badge bg-secondary like-count">{{ $thread->likes_count ?? 0 }}</span>
                    </button>
                    @else
                    <button type="button"
                            class="btn btn-sm btn_meta btn-like"
                            onclick="showLoginModal()"
                            title="{{ __('thread.login_to_like') }}">
                        <i class="fas fa-thumbs-up"></i>
                        {{ __('thread.like') }}
                        <span class="badge bg-secondary like-count">{{ $thread->likes_count ?? 0 }}</span>
                    </button>
                    @endauth

                    <!-- Save Button -->
                    @auth
                    <button type="button"
                            class="btn btn-sm btn_meta {{ $isSaved ? 'active' : '' }} btn-save ms-2"
                            data-thread-id="{{ $thread->id }}"
                            data-saved="{{ $isSaved ? 'true' : 'false' }}"
                            title="{{ $isSaved ? __('thread.unsave') : __('thread.save') }}">
                        <i class="{{ $isSaved ? 'fas fa-bookmark' : 'far fa-bookmark' }}"></i>
                        <span class="save-text">{{ $isSaved ? __('thread.bookmarked') : __('thread.bookmark') }}</span>
                    </button>
                    @else
                    <button type="button"
                            class="btn btn-sm btn_meta btn-save ms-2"
                            onclick="showLoginModal()"
                            title="{{ __('thread.login_to_save') }}">
                        <i class="far fa-bookmark"></i>
                        <span class="save-text">{{ __('thread.bookmark') }}</span>
                    </button>
                    @endauth

                    <!-- Follow Button -->
                    @php
                    $isFollowed = Auth::check() && $thread->isFollowedBy(Auth::user());
                    @endphp
                    @if($isFollowed)
                    <form action="{{ route('threads.follow.remove', $thread) }}" method="POST" class="d-inline ms-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-theodoi btn_meta active">
                            <i class="fas fa-bell-fill"></i>
                            {{ __('thread.following') }}
                        </button>
                    </form>
                    @else
                    <form action="{{ route('threads.follow.add', $thread) }}" method="POST" class="d-inline ms-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-theodoi btn_meta">
                            <i class="fas fa-bell"></i>
                            {{ __('thread.follow') }}
                        </button>
                    </form>
                    @endif
                </div>

                <div class="d-flex align-items-center">
                    <!-- Share Button -->
                    <div class="dropdown dropdown-button d-inline">
                        <button class="btn btn-sm btn-main no-border dropdown-toggle btn-share" type="button"
                            id="shareDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-share-alt"></i> {{ __('thread.share') }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="shareDropdown">
                            <li>
                                <a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank">
                                    <i class="fab fa-facebook-f me-2"></i>Facebook
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($thread->title) }}"target="_blank">
                                    <i class="fab fa-twitter me-2"></i>Twitter
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://wa.me/?text={{ urlencode($thread->title . ' ' . request()->url()) }}"target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('{{ __('thread.link_copied') }}'); return false;">
                                    <i class="fas fa-clipboard me-2"></i>{{ __('thread.copy_link') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Reply Button -->
                    <a href="#reply-form" class="btn btn-sm btn-primary ms-2 btn-traloi">
                        <i class="fas fa-reply"></i> {{ __('thread.reply') }}
                    </a>

                    <!-- Edit/Delete Buttons (if owner) -->
                    @can('update', $thread)
                    <div class="btn-group ms-2">
                        <a href="{{ route('threads.edit', $thread) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit"></i> {{ __('thread.edit') }}
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteThreadModal">
                            <i class="fas fa-trash"></i> {{ __('thread.delete') }}
                        </button>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteThreadModal" tabindex="-1" aria-labelledby="deleteThreadModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteThreadModalLabel">{{ __('thread.delete_confirmation') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ __('thread.delete_thread_message') }}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('thread.cancel') }}</button>
                                    <form action="{{ route('threads.destroy', $thread) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">{{ __('thread.delete_thread_button') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
         @include('threads.partials.poll')

        <!-- Showcase Section -->
        <div id="showcase-section">
        @include('threads.partials.showcase')
        </div>

        <div class="comments-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="title_page_sub mb-2"><i class="fa-regular fa-comment-dots me-1"></i>{{ $comments->total() }} {{ __('thread.replies') }}</h3>
                    <div class="thread-meta-item me-0">
                        {{ __('thread.last_post_by') }}
                        <a href="{{ route('profile.show', $thread->lastCommenter) }}" class="ms-1 fw-semibold">
                            {{ $thread->lastCommenter->name ?? $thread->user->name }}
                        </a>
                        <span class="ms-1">{{ $thread->lastCommentAt ? $thread->lastCommentAt->diffForHumans() :
                            $thread->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <!-- Sort Options -->
                <div class="btn-group" id="comments-sort-options">
                    <button type="button"
                            class="btn btn-sm sort-btn {{ request('sort', 'oldest') == 'oldest' ? 'btn-primary' : 'btn-outline-primary' }}"
                            data-sort="oldest"
                            data-thread-id="{{ $thread->id }}">
                        {{ __('thread.sort_oldest') }}
                    </button>
                    <button type="button"
                            class="btn btn-sm sort-btn {{ request('sort') == 'newest' ? 'btn-primary' : 'btn-outline-primary' }}"
                            data-sort="newest"
                            data-thread-id="{{ $thread->id }}">
                        {{ __('thread.sort_newest') }}
                    </button>
                    <button type="button"
                            class="btn btn-sm sort-btn {{ request('sort') == 'reactions' ? 'btn-primary' : 'btn-outline-primary' }}"
                            data-sort="reactions"
                            data-thread-id="{{ $thread->id }}">
                        {{ __('thread.sort_reactions') }}
                    </button>
                </div>
            </div>

            <!-- Comments List -->
            <div id="comments-container">
            @forelse($comments as $comment)
            <div class="comment_item mb-3" id="comment-{{ $comment->id }}">
                <div class="d-flex">
                    <div class="comment_item_avatar">
                        <img src="{{ $comment->user->getAvatarUrl() }}" alt="{{ $comment->user->name }}"
                                class="rounded-circle me-2" width="40" height="40"
                                onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($comment->user->name, 0, 1)), 'size' => 40]) }}'">
                    </div>
                    <div class="comment_item_body">
                        <div class="comment_item_user">
                            <a href="{{ route('profile.show', $comment->user) }}" class="fw-bold text-decoration-none">
                                {{ $comment->user->name }}
                            </a>
                            <div class="text-muted small">
                                <span>{{ $comment->user->comments_count ?? 0 }} {{ __('thread.comments') }}</span> ·
                                <span>{{ __('thread.joined') }} {{ $comment->user->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                        <div class="comment_item_content">
                            {!! $comment->content !!}
                        </div>
                        @if($comment->has_media && isset($comment->attachments) && count($comment->attachments) > 0)
                        <div class="comment-attachments mt-3">
                            <div class="row g-2">
                                @foreach($comment->attachments as $attachment)
                                <div class="col-md-3 col-sm-4 col-6">
                                    <a href="{{ $attachment->url }}" class="d-block"
                                        data-fancybox="comment-{{ $comment->id }}-images"
                                        data-caption="{{ $attachment->file_name }}">
                                        <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}"
                                            class="img-fluid rounded">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        <div class="comment_item_meta d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="btn btn-sm btn_meta"><i class="fa-regular fa-clock me-1"></i> {{ $comment->created_at->diffForHumans() }}</span>
                                <!-- Like Button -->
                                @auth
                                <button type="button"
                                        class="btn btn-sm btn_meta comment-like-btn {{ $comment->isLikedBy(auth()->user()) ? 'active' : '' }}"
                                        data-comment-id="{{ $comment->id }}"
                                        data-liked="{{ $comment->isLikedBy(auth()->user()) ? 'true' : 'false' }}"
                                        title="{{ $comment->isLikedBy(auth()->user()) ? __('thread.unlike') : __('thread.like') }}">
                                    <i class="fas fa-thumbs-up"></i> <span class="comment-like-count">{{ $comment->like_count }}</span> {{ __('thread.like') }}
                                </button>
                                @else
                                <button type="button"
                                        class="btn btn-sm btn_meta comment-like-btn"
                                        onclick="showLoginModal()"
                                        title="{{ __('thread.login_to_like') }}">
                                    <i class="fas fa-thumbs-up"></i> <span class="comment-like-count">{{ $comment->like_count }}</span> {{ __('thread.like') }}
                                </button>
                                @endauth
                            </div>
                            <div>
                                <!-- Quote Button -->
                                <button class="btn btn-main no-border quote-button" data-comment-id="{{ $comment->id }}"
                                    data-comment-content="{{ $comment->content }}" data-user-name="{{ $comment->user->name }}">
                                    <i class="fa-solid fa-quote-left"></i> {{ __('thread.quote') }}
                                </button>

                                <!-- Reply Button -->
                                <button class="btn btn-main no-border reply-button ms-2"
                                    data-parent-id="{{ $comment->id }}">
                                    <i class="fas fa-reply"></i> {{ __('thread.reply') }}
                                </button>

                                <!-- Edit/Delete Buttons (if owner) -->
                                @can('update', $comment)
                                <div class="btn-group ms-2">
                                    <button class="btn btn-main active edit-comment-button"
                                        data-comment-id="{{ $comment->id }}" data-comment-content="{{ $comment->content }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger delete-comment-btn"
                                            data-comment-id="{{ $comment->id }}"
                                            data-comment-type="comment"
                                            title="{{ __('thread.delete_comment') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endcan
                            </div>
                        </div>
                        <div class="comment_sub">
                            @if(isset($comment->replies) && count($comment->replies) > 0)
                            @foreach($comment->replies as $reply)
                            <div class="comment_item mb-3">
                                <div class="d-flex">
                                    <div class="comment_item_avatar">
                                        <img src="{{ $reply->user->getAvatarUrl() }}" alt="{{ $reply->user->name }}" class="rounded-circle me-2" width="30" height="30" onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($reply->user->name, 0, 1)), 'size' => 40]) }}'">
                                    </div>
                                    <div class="comment_item_body sub">
                                        <div class="comment_item_user">
                                            <a href="{{ route('profile.show', $comment->user) }}" class="fw-bold text-decoration-none">
                                                {{ $reply->user->name }}
                                            </a>
                                            <div class="text-muted small">
                                                <span>{{ $reply->user->comments_count ?? 0 }} {{ __('thread.comments') }}</span> ·
                                                <span>{{ __('thread.joined') }} {{ $reply->user->created_at->format('M Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="comment_item_content">
                                            {!! $reply->content !!}
                                        </div>
                                        @if($reply->has_media && isset($reply->attachments) && count($reply->attachments) > 0)
                                        <div class="reply-attachments mt-2">
                                            <div class="row g-2">
                                                @foreach($reply->attachments as $attachment)
                                                <div class="col-md-3 col-sm-4 col-6">
                                                    <a href="{{ $attachment->url }}" class="d-block"
                                                        data-fancybox="reply-{{ $reply->id }}-images"
                                                        data-caption="{{ $attachment->file_name }}">
                                                        <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}"
                                                            class="img-fluid rounded">
                                                    </a>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        <div class="comment_item_meta d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <span class="btn btn-sm btn_meta"><i class="fa-regular fa-clock me-1"></i> {{ $reply->created_at->diffForHumans() }}</span>
                                                <!-- Like Button -->
                                                @auth
                                                <button type="button"
                                                        class="btn btn-sm btn_meta comment-like-btn {{ $reply->isLikedBy(auth()->user()) ? 'active' : '' }}"
                                                        data-comment-id="{{ $reply->id }}"
                                                        data-liked="{{ $reply->isLikedBy(auth()->user()) ? 'true' : 'false' }}"
                                                        title="{{ $reply->isLikedBy(auth()->user()) ? __('thread.unlike') : __('thread.like') }}">
                                                    <i class="fas fa-thumbs-up"></i> <span class="comment-like-count">{{ $reply->like_count }}</span> {{ __('thread.like') }}
                                                </button>
                                                @else
                                                <button type="button"
                                                        class="btn btn-sm btn_meta comment-like-btn"
                                                        onclick="showLoginModal()"
                                                        title="{{ __('thread.login_to_like') }}">
                                                    <i class="fas fa-thumbs-up"></i> <span class="comment-like-count">{{ $reply->like_count }}</span> {{ __('thread.like') }}
                                                </button>
                                                @endauth
                                            </div>
                                            <div>
                                                <!-- Reply Button -->
                                                <button class="btn btn-sm btn-main no-border reply-button"
                                                    data-parent-id="{{ $comment->id }}">
                                                    <i class="fas fa-reply"></i> {{ __('thread.reply') }}
                                                </button>

                                                <!-- Edit/Delete Buttons (if owner) -->
                                                @can('update', $reply)
                                                <div class="btn-group ms-2">
                                                    <button class="btn btn-sm btn-main active edit-comment-button"
                                                        data-comment-id="{{ $reply->id }}" data-comment-content="{{ $reply->content }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger delete-comment-btn"
                                                            data-comment-id="{{ $reply->id }}"
                                                            data-comment-type="reply"
                                                            title="{{ __('thread.delete_reply') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                {{ __('thread.no_comments') }}
            </div>
            @endforelse
            <!-- Pagination -->
            <div class="d-flex justify-content-center" id="comments-pagination">
                {{ $comments->links() }}
            </div>
            </div> <!-- End comments-container -->
        </div>
    </div>

    <!--div class="card-body">
        @if($thread->status)
        <div class="project-details mb-3 p-3 bg-light rounded">
            @if($thread->status)
            <div><strong>Trạng thái:</strong> {{ $thread->status }}</div>
            @endif
        </div>
        @endif
    </div-->

    <!-- Reply Form -->
    @auth
    <div class="card" id="reply-form">
        <div class="card-header">
            <h4>{{ __('thread.post_reply') }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('threads.comments.store', $thread) }}" method="POST" enctype="multipart/form-data"
                id="reply-form-element">
                @csrf
                <input type="hidden" name="parent_id" id="parent_id" value="">

                <div class="mb-3">
                    <label for="content" class="form-label">
                        <i class="fas fa-comment-text me-2"></i>{{ __('thread.reply_content') }} <span class="text-danger">*</span>
                    </label>
                    <x-tinymce-editor
                        name="content"
                        id="content"
                        :value="old('content')"
                        :placeholder="__('thread.reply_content_placeholder')"
                        context="comment"
                        :height="300"
                        :required="true"
                        class="@error('content') is-invalid @enderror"
                    />
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="content-error" class="invalid-feedback" style="display: none;">
                        {{ __('thread.reply_content_required') }}
                    </div>
                </div>

                <!-- File Upload Component -->
                <x-file-upload
                    name="images"
                    :file-types="['jpg', 'jpeg', 'png', 'gif', 'webp']"
                    max-size="5MB"
                    :multiple="true"
                    :max-files="10"
                    id="thread-reply-images"
                />

                @error('images.*')
                <div class="text-danger small mt-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                </div>
                @enderror

                <!-- Reply to info section -->
                <div id="reply-to-info" class="mb-3" style="display: none;">
                    <div class="card border-start border-primary border-3 bg-light">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <small class="text-muted">
                                        <i class="fas fa-reply me-1"></i>{{ __('thread.replying_to') }}: <strong id="reply-to-name"></strong>
                                    </small>
                                    <div id="reply-to-content" class="mt-1 text-muted small" style="max-height: 100px; overflow-y: auto;">
                                        <!-- Original comment content will be inserted here -->
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="cancel-reply">
                                    <i class="fas fa-times"></i> {{ __('thread.cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">

                    <button type="submit" class="btn btn-primary" id="submit-reply-btn">
                        <i class="fas fa-paper-plane"></i> {{ __('thread.send_reply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        {!! __('thread.login_required', [
            'login' => '<a href="' . route('login') . '">' . __('thread.login') . '</a>',
            'register' => '<a href="' . route('register') . '">' . __('thread.register') . '</a>'
        ]) !!}
    </div>
    @endauth

    <!-- Related Threads -->
    @if(count($relatedThreads) > 0)
    <div class="related-threads mt-4">
        <h3 class="title_page_sub">{{ __('thread.related_topics') }}</h3>
        <div class="list-group">
            @foreach($relatedThreads as $relatedThread)
                @include('partials.thread-item', ['thread' => $relatedThread])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- TinyMCE is now handled by the component -->
<script>
    // Initialize event handlers when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeEventHandlers();
    initializeRealTimeComments();
});

// File upload functionality is now handled by the FileUploadComponent

function initializeFormSubmission() {
    const form = document.getElementById('reply-form-element');
    const submitBtn = document.getElementById('submit-reply-btn');
    const contentTextarea = document.getElementById('content');
    const contentError = document.getElementById('content-error');

    if (!form || !submitBtn || !contentTextarea) return;

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset previous errors
        contentTextarea.classList.remove('is-invalid');
        contentError.style.display = 'none';

        // Get content from TinyMCE
        const editor = tinymce.get('content');
        let content = '';

        if (editor) {
            content = editor.getContent().trim();
            // Sync TinyMCE content to textarea
            contentTextarea.value = content;
        } else {
            content = contentTextarea.value.trim();
        }

        // Validate content
        if (!content || content === '<p></p>' || content === '<p><br></p>' || content === '') {
            // Show error
            contentTextarea.classList.add('is-invalid');
            contentError.style.display = 'block';

            // Add error class to TinyMCE container
            const tinyMCEContainer = document.querySelector('.tox-tinymce');
            if (tinyMCEContainer) {
                tinyMCEContainer.classList.add('is-invalid');
            }

            // Focus TinyMCE editor
            if (editor) {
                editor.focus();
            } else {
                contentTextarea.focus();
            }

            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>{{ __('thread.sending') }}';

        // Prepare form data
        const formData = new FormData(form);

        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast(data.message, 'success');

                // Clear form
                if (tinymce.get('content')) {
                    tinymce.get('content').setContent('');
                } else {
                    contentTextarea.value = '';
                }

                // Reset parent_id if it was a reply
                const parentIdInput = document.getElementById('parent_id');
                const replyToInfo = document.getElementById('reply-to-info');
                if (parentIdInput) parentIdInput.value = '';
                if (replyToInfo) replyToInfo.style.display = 'none';

                // Reload page to show new comment (for now, can be improved later)
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Server error');
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);

            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> {{ __('thread.send_reply') }}';

            // Show error message
            showToast(error.message || '{{ __('thread.form_submission_error') }}', 'error');
        });
    });

    // Handle TinyMCE content change to remove error
    if (tinymce.get('content')) {
        tinymce.get('content').on('input keyup', function() {
            const content = this.getContent().trim();
            if (content && content !== '<p></p>' && content !== '<p><br></p>') {
                contentTextarea.classList.remove('is-invalid');
                contentError.style.display = 'none';

                // Remove error class from TinyMCE container
                const tinyMCEContainer = document.querySelector('.tox-tinymce');
                if (tinyMCEContainer) {
                    tinyMCEContainer.classList.remove('is-invalid');
                }
            }
        });
    }

    // Handle textarea change (fallback)
    contentTextarea.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
            contentError.style.display = 'none';

            // Remove error class from TinyMCE container
            const tinyMCEContainer = document.querySelector('.tox-tinymce');
            if (tinyMCEContainer) {
                tinyMCEContainer.classList.remove('is-invalid');
            }
        }
    });
}

function initializeEventHandlers() {
    // File upload is now handled by FileUploadComponent

    // Initialize Form Submission Handler
    initializeFormSubmission();

    // Initialize Thread Actions (Like, Save)
    initializeThreadActions();

    // Handle reply buttons
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const parentId = this.getAttribute('data-parent-id');

            // Find the parent user name and content - look for .fw-bold in the comment structure
            const commentItem = this.closest('.comment_item');
            let parentUser = 'Người dùng';
            let parentContent = '';

            if (commentItem) {
                const userLink = commentItem.querySelector('.comment_item_user .fw-bold');
                if (userLink) {
                    parentUser = userLink.textContent.trim();
                }

                // Get comment content - look for the content div
                const contentDiv = commentItem.querySelector('.comment_item_content');
                if (contentDiv) {
                    // Get text content and limit to reasonable length
                    parentContent = contentDiv.textContent.trim();
                    if (parentContent.length > 200) {
                        parentContent = parentContent.substring(0, 200) + '...';
                    }
                }
            }

            const parentIdInput = document.getElementById('parent_id');
            const replyToName = document.getElementById('reply-to-name');
            const replyToContent = document.getElementById('reply-to-content');
            const replyToInfo = document.getElementById('reply-to-info');

            if (parentIdInput) parentIdInput.value = parentId;
            if (replyToName) replyToName.textContent = parentUser;
            if (replyToContent) replyToContent.textContent = parentContent;
            if (replyToInfo) replyToInfo.style.display = 'block';

            // Scroll to reply form
            const replyForm = document.getElementById('reply-form');
            if (replyForm) {
                replyForm.scrollIntoView({ behavior: 'smooth' });

                // Focus TinyMCE editor
                setTimeout(() => {
                    if (tinymce.get('content')) {
                        tinymce.get('content').focus();
                    }
                }, 100);
            }
        });
    });

    // Handle cancel reply
    const cancelReply = document.getElementById('cancel-reply');
    if (cancelReply) {
        cancelReply.addEventListener('click', function() {
            const parentIdInput = document.getElementById('parent_id');
            const replyToInfo = document.getElementById('reply-to-info');

            if (parentIdInput) parentIdInput.value = '';
            if (replyToInfo) replyToInfo.style.display = 'none';
        });
    }

    // Handle quote buttons
    document.querySelectorAll('.quote-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentContent = this.getAttribute('data-comment-content');
            const userName = this.getAttribute('data-user-name');

            const quoteHTML = `
                <blockquote>
                    <p><strong>${userName} đã viết:</strong></p>
                    ${commentContent}
                </blockquote>
                <p></p>
            `;

            // Insert quote into TinyMCE
            if (tinymce.get('content')) {
                tinymce.get('content').insertContent(quoteHTML);

                // Scroll to reply form and focus
                const replyForm = document.getElementById('reply-form');
                if (replyForm) {
                    replyForm.scrollIntoView({ behavior: 'smooth' });
                    setTimeout(() => {
                        tinymce.get('content').focus();
                    }, 100);
                }
            }
        });
    });

    // Handle edit comment buttons
    document.querySelectorAll('.edit-comment-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const commentContent = this.getAttribute('data-comment-content');

            // Create edit form with TinyMCE
            const commentCard = this.closest('.card');
            const commentBody = commentCard.querySelector('.card-body');
            const originalContent = commentBody.innerHTML;

            const editForm = document.createElement('form');
            editForm.setAttribute('action', `/comments/${commentId}`);
            editForm.setAttribute('method', 'POST');
            editForm.classList.add('edit-comment-form');

            editForm.innerHTML = `
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="edit-content-${commentId}" class="form-label">Chỉnh sửa bình luận của bạn</label>
                    <textarea id="edit-content-${commentId}" name="content">${commentContent}</textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary me-2 cancel-edit-button">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            `;

            commentBody.innerHTML = '';
            commentBody.appendChild(editForm);

            // Initialize TinyMCE for edit form
            tinymce.init({
                selector: `#edit-content-${commentId}`,
                height: 150,
                menubar: false,
                branding: false,
                plugins: 'advlist autolink lists link textcolor',
                toolbar: 'bold italic underline | bullist numlist | link',
                placeholder: 'Chỉnh sửa bình luận...',
                content_style: `
                    body {
                        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
                        font-size: 14px;
                        line-height: 1.6;
                    }
                `
            });

            // Handle cancel edit
            commentBody.querySelector('.cancel-edit-button').addEventListener('click', function() {
                tinymce.remove(`#edit-content-${commentId}`);
                commentBody.innerHTML = originalContent;
            });
        });
    });
}

// Initialize Thread Actions (Like, Save)
function initializeThreadActions() {
    // Handle like button clicks
    document.querySelectorAll('.btn-like').forEach(button => {
        button.addEventListener('click', function() {
            if (this.onclick) return; // Skip if it's a login button

            const threadId = this.dataset.threadId;
            const isLiked = this.dataset.liked === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("thread.processing") }}';

            // Make AJAX request
            fetch(`/threads/${threadId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle like state
                    const newLiked = !isLiked;
                    this.dataset.liked = newLiked ? 'true' : 'false';

                    // Update button appearance
                    if (newLiked) {
                        this.classList.add('active');
                        this.title = '{{ __("thread.unlike") }}';
                    } else {
                        this.classList.remove('active');
                        this.title = '{{ __("thread.like") }}';
                    }

                    // Update like count
                    const likeCountElement = this.querySelector('.like-count');
                    if (likeCountElement) {
                        likeCountElement.textContent = data.like_count;
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || '{{ __("thread.error_occurred") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Like error:', error);
                showToast('{{ __("thread.request_error") }}', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Handle save button clicks
    document.querySelectorAll('.btn-save').forEach(button => {
        button.addEventListener('click', function() {
            if (this.onclick) return; // Skip if it's a login button

            const threadId = this.dataset.threadId;
            const isSaved = this.dataset.saved === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("thread.processing") }}';

            // Make AJAX request
            fetch(`/threads/${threadId}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle save state
                    const newSaved = !isSaved;
                    this.dataset.saved = newSaved ? 'true' : 'false';

                    // Update button appearance
                    const icon = this.querySelector('i');
                    const text = this.querySelector('.save-text');

                    if (newSaved) {
                        this.classList.add('active');
                        icon.className = 'fas fa-bookmark';
                        text.textContent = '{{ __("thread.bookmarked") }}';
                        this.title = '{{ __("thread.unsave") }}';
                    } else {
                        this.classList.remove('active');
                        icon.className = 'far fa-bookmark';
                        text.textContent = '{{ __("thread.bookmark") }}';
                        this.title = '{{ __("thread.save") }}';
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || '{{ __("thread.error_occurred") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                showToast('{{ __("thread.request_error") }}', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Handle comment like button clicks
    document.querySelectorAll('.comment-like-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (this.onclick) return; // Skip if it's a login button

            const commentId = this.dataset.commentId;
            const isLiked = this.dataset.liked === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("thread.processing") }}';

            // Make AJAX request
            fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle like state
                    const newLiked = !isLiked;
                    this.dataset.liked = newLiked ? 'true' : 'false';

                    // Update button appearance
                    if (newLiked) {
                        this.classList.add('active');
                        this.title = '{{ __("thread.unlike") }}';
                    } else {
                        this.classList.remove('active');
                        this.title = '{{ __("thread.like") }}';
                    }

                    // Update like count
                    const likeCountElement = this.querySelector('.comment-like-count');
                    if (likeCountElement) {
                        likeCountElement.textContent = data.like_count;
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || '{{ __("thread.error_occurred") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Comment like error:', error);
                showToast('{{ __("thread.request_error") }}', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Handle delete comment button clicks
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const commentType = this.dataset.commentType;
            const confirmMessage = commentType === 'reply' ?
                '{{ __("thread.delete_reply_message") }}' :
                '{{ __("thread.delete_comment_message") }}';

            if (!confirm(confirmMessage)) {
                return;
            }

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Make AJAX request
            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove comment element from DOM
                    const commentElement = document.querySelector(`#comment-${commentId}`);
                    if (commentElement) {
                        commentElement.style.transition = 'opacity 0.3s ease';
                        commentElement.style.opacity = '0';

                        setTimeout(() => {
                            commentElement.remove();
                        }, 300);
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    throw new Error(data.message || 'Server error');
                }
            })
            .catch(error => {
                console.error('Delete comment error:', error);
                showToast(error.message || '{{ __("thread.request_error") }}', 'error');

                // Reset button state
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Handle sort button clicks
    document.querySelectorAll('.sort-btn').forEach(button => {
        button.addEventListener('click', function() {
            const sortType = this.dataset.sort;
            const threadId = this.dataset.threadId;

            // Update button states
            document.querySelectorAll('.sort-btn').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');

            // Show loading state
            const commentsContainer = document.getElementById('comments-container');
            if (commentsContainer) {
                commentsContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>{{ __("thread.loading_comments") }}</div>';
            }

            // Make AJAX request to get sorted comments
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sortType);

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.text();
                } else {
                    throw new Error('Server error');
                }
            })
            .then(html => {
                // Parse the response HTML to extract comments
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newCommentsContainer = doc.getElementById('comments-container');

                if (newCommentsContainer && commentsContainer) {
                    commentsContainer.innerHTML = newCommentsContainer.innerHTML;

                    // Re-initialize event handlers for new content
                    initializeCommentInteractions();

                    // Update URL without page reload
                    window.history.pushState({}, '', url.toString());

                    // Show success message
                    showToast('{{ __("thread.comments_sorted") }}', 'success');
                }
            })
            .catch(error => {
                console.error('Sort comments error:', error);
                showToast('{{ __("thread.request_error") }}', 'error');

                // Reload page as fallback
                window.location.href = url.toString();
            });
        });
    });
}

// Initialize comment interactions (likes, delete, etc.) for dynamically loaded content
function initializeCommentInteractions() {
    // Re-initialize comment like buttons
    document.querySelectorAll('.comment-like-btn').forEach(button => {
        // Remove existing event listeners by cloning the element
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);

        newButton.addEventListener('click', function() {
            if (this.onclick) return; // Skip if it's a login button

            const commentId = this.dataset.commentId;
            const isLiked = this.dataset.liked === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("thread.processing") }}';

            // Make AJAX request
            fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle like state
                    const newLiked = !isLiked;
                    this.dataset.liked = newLiked ? 'true' : 'false';

                    // Update button appearance
                    if (newLiked) {
                        this.classList.add('active');
                        this.title = '{{ __("thread.unlike") }}';
                    } else {
                        this.classList.remove('active');
                        this.title = '{{ __("thread.like") }}';
                    }

                    // Update like count
                    const likeCountElement = this.querySelector('.comment-like-count');
                    if (likeCountElement) {
                        likeCountElement.textContent = data.like_count;
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || '{{ __("thread.error_occurred") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Comment like error:', error);
                showToast('{{ __("thread.request_error") }}', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Re-initialize delete comment buttons
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        // Remove existing event listeners by cloning the element
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);

        newButton.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const commentType = this.dataset.commentType;
            const confirmMessage = commentType === 'reply' ?
                '{{ __("thread.delete_reply_message") }}' :
                '{{ __("thread.delete_comment_message") }}';

            if (!confirm(confirmMessage)) {
                return;
            }

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Make AJAX request
            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove comment element from DOM
                    const commentElement = document.querySelector(`#comment-${commentId}`);
                    if (commentElement) {
                        commentElement.style.transition = 'opacity 0.3s ease';
                        commentElement.style.opacity = '0';

                        setTimeout(() => {
                            commentElement.remove();
                        }, 300);
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    throw new Error(data.message || 'Server error');
                }
            })
            .catch(error => {
                console.error('Delete comment error:', error);
                showToast(error.message || '{{ __("thread.request_error") }}', 'error');

                // Reset button state
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });
}

// Toast notification function (if not already defined)
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Login modal function (if not already defined)
function showLoginModal() {
    // Check if login modal exists
    const loginModal = document.getElementById('loginModal');
    if (loginModal) {
        const modal = new bootstrap.Modal(loginModal);
        modal.show();
    } else {
        // Redirect to login page
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
    }
}

// Real-time comments functionality
function initializeRealTimeComments() {
    const threadId = {{ $thread->id }};
    const currentUserId = {{ Auth::id() ?? 'null' }};

    // Subscribe to thread channel for real-time updates
    if (window.notificationService && window.notificationService.socket) {
        const socket = window.notificationService.socket;

        // Join thread channel
        socket.emit('subscribe_request', { channel: `thread.${threadId}` });

        // Listen for new comments
        socket.on('comment.created', function(data) {
            console.log('New comment received:', data);
            handleNewComment(data);
        });

        // Listen for comment updates
        socket.on('comment.updated', function(data) {
            console.log('Comment updated:', data);
            handleCommentUpdate(data);
        });

        // Listen for comment deletions
        socket.on('comment.deleted', function(data) {
            console.log('Comment deleted:', data);
            handleCommentDeletion(data);
        });

        // Listen for thread like updates
        socket.on('thread.like.updated', function(data) {
            console.log('Thread like updated:', data);
            handleThreadLikeUpdate(data);
        });

        // Listen for comment like updates
        socket.on('comment.like.updated', function(data) {
            console.log('Comment like updated:', data);
            handleCommentLikeUpdate(data);
        });

        // Listen for thread stats updates
        socket.on('thread.stats.updated', function(data) {
            console.log('Thread stats updated:', data);
            handleThreadStatsUpdate(data);
        });

        console.log(`Real-time comments initialized for thread ${threadId}`);
    } else {
        console.warn('NotificationService not available for real-time comments');
    }
}

function handleNewComment(data) {
    const comment = data.comment;
    const currentUserId = {{ Auth::id() ?? 'null' }};

    // Don't show our own comments (they're already added by form submission)
    if (comment.user.id === currentUserId) {
        return;
    }

    // Create comment HTML
    const commentHtml = createCommentHtml(comment);

    // Add to comments section
    const commentsContainer = document.getElementById('comments-container');
    if (commentsContainer) {
        // Check if it's a reply to an existing comment
        if (comment.parent_id) {
            // Find parent comment and add as reply
            const parentComment = document.querySelector(`#comment-${comment.parent_id}`);
            if (parentComment) {
                const repliesContainer = parentComment.querySelector('.comment_sub');
                if (repliesContainer) {
                    repliesContainer.insertAdjacentHTML('beforeend', commentHtml);
                } else {
                    // Create replies container if it doesn't exist
                    const commentBody = parentComment.querySelector('.comment_item_body');
                    if (commentBody) {
                        commentBody.insertAdjacentHTML('beforeend', `<div class="comment_sub">${commentHtml}</div>`);
                    }
                }
            }
        } else {
            // Add as new top-level comment
            const noCommentsAlert = commentsContainer.querySelector('.alert-info');
            if (noCommentsAlert) {
                noCommentsAlert.remove();
            }

            // Insert before pagination
            const pagination = document.getElementById('comments-pagination');
            if (pagination) {
                pagination.insertAdjacentHTML('beforebegin', commentHtml);
            } else {
                commentsContainer.insertAdjacentHTML('beforeend', commentHtml);
            }
        }

        // Update comment count
        updateCommentCount(1);

        // Re-initialize event handlers for new comment
        initializeCommentInteractions();

        // Show notification
        showCommentNotification(comment.user.name + ' đã bình luận mới', 'success');

        // Scroll to new comment if user is near bottom
        scrollToNewCommentIfNeeded(comment.id);
    }
}

function handleCommentUpdate(data) {
    const comment = data.comment;
    const commentElement = document.querySelector(`#comment-${comment.id}`);

    if (commentElement) {
        // Update comment content
        const contentElement = commentElement.querySelector('.comment_item_content');
        if (contentElement) {
            contentElement.innerHTML = comment.content;

            // Show update indicator
            showCommentNotification('Bình luận đã được cập nhật', 'info');
        }
    }
}

function handleCommentDeletion(data) {
    const commentElement = document.querySelector(`#comment-${data.comment_id}`);

    if (commentElement) {
        // Fade out and remove
        commentElement.style.transition = 'opacity 0.3s ease';
        commentElement.style.opacity = '0';

        setTimeout(() => {
            commentElement.remove();

            // Update comment count
            updateCommentCount(-1);

            // Show notification
            showCommentNotification('Bình luận đã được xóa', 'warning');
        }, 300);
    }
}

function createCommentHtml(comment) {
    const timeAgo = formatTimeAgo(new Date(comment.created_at));
    const isReply = comment.parent_id ? true : false;
    const avatarSize = isReply ? 30 : 40;
    const currentUserId = {{ Auth::id() ?? 'null' }};
    const isAuthenticated = currentUserId !== null;

    return `
        <div class="comment_item mb-3" id="comment-${comment.id}">
            <div class="d-flex">
                <div class="comment_item_avatar">
                    <img src="${comment.user.avatar_url}" alt="${comment.user.name}"
                         class="rounded-circle me-2" width="${avatarSize}" height="${avatarSize}"
                         onerror="this.src='{{ route('avatar.generate', ['initial' => 'U']) }}'">
                </div>
                <div class="comment_item_body ${isReply ? 'sub' : ''}">
                    <div class="comment_item_user">
                        <a href="/users/${comment.user.username || comment.user.id}" class="fw-bold text-decoration-none">
                            ${comment.user.name}
                        </a>
                        <div class="text-muted small">
                            <span>0 {{ __('thread.comments') }}</span> ·
                            <span>{{ __('thread.joined') }} ${new Date(comment.user.created_at || Date.now()).toLocaleDateString('vi-VN', {month: 'short', year: 'numeric'})}</span>
                        </div>
                    </div>
                    <div class="comment_item_content">
                        ${comment.content}
                    </div>
                    <div class="comment_item_meta d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="btn btn-sm btn_meta"><i class="fa-regular fa-clock me-1"></i> ${timeAgo}</span>
                            ${isAuthenticated ? `
                            <button type="button"
                                    class="btn btn-sm btn_meta comment-like-btn"
                                    data-comment-id="${comment.id}"
                                    data-liked="false"
                                    title="{{ __('thread.like') }}">
                                <i class="fas fa-thumbs-up"></i> <span class="comment-like-count">0</span> {{ __('thread.like') }}
                            </button>
                            ` : `
                            <button type="button"
                                    class="btn btn-sm btn_meta comment-like-btn"
                                    onclick="showLoginModal()"
                                    title="{{ __('thread.login_to_like') }}">
                                <i class="fas fa-thumbs-up"></i> <span class="comment-like-count">0</span> {{ __('thread.like') }}
                            </button>
                            `}
                        </div>
                        <div>
                            <button class="btn btn-main no-border quote-button" data-comment-id="${comment.id}"
                                data-comment-content="${comment.content}" data-user-name="${comment.user.name}">
                                <i class="fa-solid fa-quote-left"></i> {{ __('thread.quote') }}
                            </button>
                            <button class="btn btn-main no-border reply-button ms-2"
                                data-parent-id="${comment.parent_id || comment.id}">
                                <i class="fas fa-reply"></i> {{ __('thread.reply') }}
                            </button>
                        </div>
                    </div>
                    ${!isReply ? '<div class="comment_sub"></div>' : ''}
                </div>
            </div>
        </div>
    `;
}

function updateCommentCount(delta) {
    // Update comment count in thread meta
    const commentCountElements = document.querySelectorAll('.comments-count, [data-comments-count]');
    commentCountElements.forEach(element => {
        const currentCount = parseInt(element.textContent) || 0;
        const newCount = Math.max(0, currentCount + delta);
        element.textContent = newCount;
    });

    // Update comment count in section header
    const sectionHeader = document.querySelector('.title_page_sub');
    if (sectionHeader) {
        const text = sectionHeader.textContent;
        const match = text.match(/(\d+)/);
        if (match) {
            const currentCount = parseInt(match[1]);
            const newCount = Math.max(0, currentCount + delta);
            sectionHeader.innerHTML = sectionHeader.innerHTML.replace(/\d+/, newCount);
        }
    }
}

function showCommentNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function scrollToNewCommentIfNeeded(commentId) {
    const scrollPosition = window.pageYOffset;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    // If user is near bottom (within 200px), scroll to new comment
    if (scrollPosition + windowHeight >= documentHeight - 200) {
        setTimeout(() => {
            const commentElement = document.querySelector(`#comment-${commentId}`);
            if (commentElement) {
                commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 100);
    }
}

function formatTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'vừa xong';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' phút trước';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' giờ trước';
    return Math.floor(diffInSeconds / 86400) + ' ngày trước';
}

// Handle real-time thread like updates
function handleThreadLikeUpdate(data) {
    const currentUserId = {{ Auth::id() ?? 'null' }};

    // Don't update if it's our own action (already updated by AJAX)
    if (data.user_id === currentUserId) {
        return;
    }

    // Update all thread like count displays
    document.querySelectorAll('.like-count').forEach(element => {
        element.textContent = data.like_count;
    });

    // Show notification if someone else liked the thread
    if (data.is_liked) {
        showCommentNotification(`${data.user_name} đã thích thread này`, 'info');
    }
}

// Handle real-time comment like updates
function handleCommentLikeUpdate(data) {
    const currentUserId = {{ Auth::id() ?? 'null' }};

    // Don't update if it's our own action (already updated by AJAX)
    if (data.user_id === currentUserId) {
        return;
    }

    // Update comment like count
    const commentElement = document.querySelector(`#comment-${data.comment_id}`);
    if (commentElement) {
        const likeCountElement = commentElement.querySelector('.comment-like-count');
        if (likeCountElement) {
            likeCountElement.textContent = data.like_count;
        }
    }

    // Show notification if someone else liked the comment
    if (data.is_liked) {
        showCommentNotification(`${data.user_name} đã thích một bình luận`, 'info');
    }
}

// Handle real-time thread stats updates
function handleThreadStatsUpdate(data) {
    const stats = data.stats;

    // Update comments count
    if (stats.comments_count !== undefined) {
        // Update in thread meta
        const commentMetaElements = document.querySelectorAll('.thread-meta-item');
        commentMetaElements.forEach(element => {
            const text = element.textContent;
            if (text.includes('{{ __("thread.replies") }}')) {
                const icon = element.querySelector('i');
                const iconHtml = icon ? icon.outerHTML : '<i class="fas fa-comment"></i>';
                element.innerHTML = `${iconHtml} ${stats.comments_count.toLocaleString()} {{ __('thread.replies') }}`;
            }
        });

        // Update in comments section header
        const sectionHeader = document.querySelector('.title_page_sub');
        if (sectionHeader && sectionHeader.textContent.includes('{{ __("thread.replies") }}')) {
            const icon = sectionHeader.querySelector('i');
            const iconHtml = icon ? icon.outerHTML : '<i class="fa-regular fa-comment-dots me-1"></i>';
            sectionHeader.innerHTML = `${iconHtml}${stats.comments_count} {{ __('thread.replies') }}`;
        }
    }

    // Update participants count
    if (stats.participants_count !== undefined) {
        const participantElements = document.querySelectorAll('.thread-meta-item');
        participantElements.forEach(element => {
            const text = element.textContent;
            if (text.includes('{{ __("thread.participants") }}')) {
                const icon = element.querySelector('i');
                const iconHtml = icon ? icon.outerHTML : '<i class="fas fa-users"></i>';
                element.innerHTML = `${iconHtml} ${stats.participants_count.toLocaleString()} {{ __('thread.participants') }}`;
            }
        });
    }

    // Update last activity time
    if (stats.last_activity) {
        const lastActivityElements = document.querySelectorAll('.thread-meta-item');
        lastActivityElements.forEach(element => {
            const text = element.textContent;
            if (text.includes('{{ __("thread.joined") }}') || text.includes('trước')) {
                // This might be the last activity element, but we need to be more specific
                // For now, we'll skip updating this as it's complex to identify correctly
            }
        });
    }
}
</script>
@endpush

@push('scripts')
<script>
    // Additional Fancybox configuration for threads if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Thread-specific Fancybox configuration
        console.log('Thread images ready for Fancybox');
    });
</script>
@endpush
