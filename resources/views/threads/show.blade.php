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
                    <form action="{{ route('threads.like', $thread) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn_meta {{ Auth::check() && $isLiked ? 'active' : '' }} btn-like">
                            <i class="fas fa-thumbs-up"></i>
                            {{ __('thread.like') }}
                            <span class="badge bg-secondary">{{ $thread->likes_count ?? 0 }}</span>

                        </button>
                    </form>

                    <!-- Save Button -->
                    <form action="{{ route('threads.save', $thread) }}" method="POST" class="d-inline ms-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn_meta {{ Auth::check() && $isSaved ? 'active' : '' }} btn-save">
                            <i class="{{ Auth::check() && $isSaved ? 'far fa-bookmark-fill' : 'far fa-bookmark' }}"></i>
                            {{ Auth::check() && $isSaved ? __('thread.bookmarked') : __('thread.bookmark') }}
                        </button>
                    </form>

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
        @include('threads.partials.showcase')

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
                <div class="btn-group">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}"
                        class="btn btn-sm {{ request('sort', 'oldest') == 'oldest' ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ __('thread.sort_oldest') }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
                        class="btn btn-sm {{ request('sort') == 'newest' ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ __('thread.sort_newest') }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'reactions']) }}"
                        class="btn btn-sm {{ request('sort') == 'reactions' ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ __('thread.sort_reactions') }}
                    </a>
                </div>
            </div>

            <!-- Comments List -->
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
                                <form action="{{ route('comments.like', $comment) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn_meta {{ Auth::check() && $comment->isLikedBy(auth()->user()) ? 'active' : '' }}">
                                        <i class="fas fa-thumbs-up"></i> {{ $comment->like_count }} {{ __('thread.like') }}
                                    </button>
                                </form>
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
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('{{ __('thread.delete_comment_message') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
                                                <form action="{{ route('comments.like', $reply) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn_meta {{ Auth::check() && $reply->isLikedBy(auth()->user()) ? 'active' : '' }}">
                                                        <i class="fas fa-thumbs-up"></i> {{ $reply->like_count }}  {{ __('thread.like') }}
                                                    </button>
                                                </form>
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
                                                    <form action="{{ route('comments.destroy', $reply) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('{{ __('thread.delete_reply_message') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
            <div class="d-flex justify-content-center">
                {{ $comments->links() }}
            </div>
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
                    label="{{ '<i class=\"fas fa-image me-2\"></i>' . __('thread.attach_images_optional') }}"
                    id="thread-reply-images"
                />

                @error('images.*')
                <div class="text-danger small mt-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                </div>
                @enderror

                <div class="d-flex justify-content-between align-items-center">
                    <div id="reply-to-info" class="text-muted" style="display: none;">
                        Trả lời: <span id="reply-to-name"></span>
                        <button type="button" class="btn btn-sm btn-link p-0 ms-2" id="cancel-reply">Hủy</button>
                    </div>

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
        <h3>{{ __('thread.related_topics') }}</h3>
        <div class="list-group">
            @foreach($relatedThreads as $relatedThread)
            <a href="{{ route('threads.show', $relatedThread) }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $relatedThread->title }}</h5>
                    <small>{{ $relatedThread->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1">{{ Str::limit(strip_tags($relatedThread->content), 100) }}</p>
                <small>Bởi {{ $relatedThread->user->name }} · {{ $relatedThread->view_count }} lượt xem · {{
                    $relatedThread->comments_count ?? 0 }} {{ __('thread.replies') }}</small>
            </a>
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

        // Submit form
        try {
            form.submit();
        } catch (error) {
            console.error('Form submission error:', error);

            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> {{ __('thread.send_reply') }}';

            alert('{{ __('thread.form_submission_error') }}');
        }
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

    // Handle reply buttons
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const parentId = this.getAttribute('data-parent-id');
            const parentUser = this.closest('.card').querySelector('.fw-bold').textContent;

            const parentIdInput = document.getElementById('parent_id');
            const replyToName = document.getElementById('reply-to-name');
            const replyToInfo = document.getElementById('reply-to-info');

            if (parentIdInput) parentIdInput.value = parentId;
            if (replyToName) replyToName.textContent = parentUser;
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
    const commentsContainer = document.querySelector('.comments-section .comments-container');
    if (commentsContainer) {
        commentsContainer.insertAdjacentHTML('beforeend', commentHtml);

        // Update comment count
        updateCommentCount(1);

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

    return `
        <div class="comment_item" id="comment-${comment.id}">
            <div class="comment_item_header">
                <div class="d-flex align-items-center">
                    <img src="${comment.user.avatar_url}" alt="${comment.user.name}"
                         class="rounded-circle me-2" width="32" height="32"
                         onerror="this.src='{{ route('avatar.generate', ['initial' => 'U']) }}'">
                    <div>
                        <a href="/users/${comment.user.username || comment.user.id}" class="fw-bold text-decoration-none">
                            ${comment.user.name}
                        </a>
                        <div class="text-muted small">
                            <span>${timeAgo}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="comment_item_content">
                ${comment.content}
            </div>
            <div class="comment_item_actions">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary like-button" data-comment-id="${comment.id}">
                        <i class="fas fa-thumbs-up"></i> <span class="like-count">0</span>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary reply-button" data-comment-id="${comment.id}">
                        <i class="fas fa-reply"></i> Trả lời
                    </button>
                </div>
            </div>
        </div>
    `;
}

function updateCommentCount(delta) {
    const countElements = document.querySelectorAll('.comments-count, [data-comments-count]');
    countElements.forEach(element => {
        const currentCount = parseInt(element.textContent) || 0;
        const newCount = Math.max(0, currentCount + delta);
        element.textContent = newCount;
    });
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
