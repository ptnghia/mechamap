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
                        onerror="this.src='{{ asset('images/placeholders/50x50.png') }}'">
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
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content"
                        placeholder="{{ __('thread.reply_content_placeholder') }}">{{ old('content') }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="content-error" class="invalid-feedback" style="display: none;">
                        {{ __('thread.reply_content_required') }}
                    </div>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">
                        <i class="fas fa-image me-2"></i>{{ __('forms.upload.attach_images_optional') }}
                    </label>

                    <!-- Custom File Upload Area -->
                    <div class="file-upload-area" id="file-upload-area">
                        <div class="upload-zone" id="upload-zone">
                            <div class="upload-content">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">
                                    <h6 class="mb-1">{{ __('forms.upload.drag_drop_here') }}</h6>
                                    <p class="text-muted mb-2">{{ __('forms.upload.or') }} <span class="text-primary fw-semibold">{{ __('forms.upload.select_from_computer') }}</span></p>
                                    <small class="text-muted">{{ __('forms.upload.supported_formats', ['size' => '5']) }}</small>
                                </div>
                            </div>
                            <input type="file" class="file-input" id="images" name="images[]" multiple accept="image/*">
                        </div>

                        <!-- Image Previews Container -->
                        <div id="image-preview-container" class="image-previews-grid mt-3"></div>

                        <!-- Upload Progress -->
                        <div id="upload-progress" class="upload-progress mt-2" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 0%"></div>
                            </div>
                            <small class="text-muted mt-1">Đang tải lên...</small>
                        </div>
                    </div>

                    @error('images.*')
                    <div class="text-danger small mt-2">
                        <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                    @enderror
                </div>

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
        <h3>{{ __('forms.related.related_topics') }}</h3>
        <div class="list-group">
            @foreach($relatedThreads as $relatedThread)
            <a href="{{ route('threads.show', $relatedThread) }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $relatedThread->title }}</h5>
                    <small>{{ $relatedThread->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1">{{ Str::limit(strip_tags($relatedThread->content), 100) }}</p>
                <small>Bởi {{ $relatedThread->user->name }} · {{ $relatedThread->view_count }} lượt xem · {{
                    $relatedThread->comments_count ?? 0 }} phản hồi</small>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/m3nymn6hdlv8nqnf4g88r0ccz9n86ks2aw92v0opuy7sx20y/tinymce/7/tinymce.min.js"
    referrerpolicy="origin"></script>
<script>
    // Initialize TinyMCE Editor when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeTinyMCE();
    initializeEventHandlers();
});

function initializeTinyMCE() {
    tinymce.init({
        selector: '#content',
        height: 300,
        readonly: false,
        menubar: false,
        branding: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'charmap',
            'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'table', 'wordcount', 'emoticons'
        ],
        toolbar: [
            'undo redo formatselect bold italic underline alignleft aligncenter alignright bullist numlist quote blockquote emoticons fullscreen'
        ],
        toolbar_mode: 'floating',
        placeholder: 'Nhập nội dung phản hồi của bạn...',
        content_style: `
            body {
                font-family: "Roboto Condensed", sans-serif;
                font-size: 14px;
                line-height: 1.6;
                color: #333;
                margin: 8px;
            }
            blockquote {
                border-left: 4px solid #007bff;
                margin: 16px 0;
                padding: 12px 16px;
                background: #f8f9fa;
                font-style: italic;
                border-radius: 4px;
            }
            code {
                background: #f1f3f4;
                padding: 2px 6px;
                border-radius: 4px;
                font-family: "Monaco", "Consolas", "Courier New", monospace;
                font-size: 13px;
            }
            pre {
                background: #f8f9fa;
                padding: 12px;
                border-radius: 6px;
                overflow-x: auto;
                border: 1px solid #e9ecef;
            }
            img {
                max-width: 100%;
                height: auto;
                border-radius: 4px;
            }
        `,
        setup: function(editor) {
            // Custom Quote Button
            editor.ui.registry.addButton('quote', {
                text: '{{ __('forms.upload.quote') }}',
                icon: 'quote',
                tooltip: '{{ __('forms.upload.add_quote') }}',
                onAction: function() {
                    editor.insertContent('<blockquote><p>Nội dung trích dẫn...</p></blockquote><p><br></p>');
                }
            });

            // Custom Blockquote Button
            editor.ui.registry.addButton('blockquote', {
                text: 'Khối trích dẫn',
                icon: 'blockquote',
                tooltip: 'Định dạng khối trích dẫn',
                onAction: function() {
                    editor.execCommand('mceBlockQuote');
                }
            });

            // Handle content change for validation
            editor.on('input keyup change', function() {
                const content = editor.getContent().trim();
                const textarea = document.getElementById('content');
                const errorDiv = document.getElementById('content-error');

                if (textarea && errorDiv) {
                    // Sync content to textarea
                    textarea.value = editor.getContent();

                    // Remove error if content exists
                    if (content && content !== '<p></p>' && content !== '<p><br></p>') {
                        textarea.classList.remove('is-invalid');
                        errorDiv.style.display = 'none';
                    }
                }
            });

            // Handle initialization complete
            editor.on('init', function() {
                console.log('TinyMCE initialized successfully');

                // Set initial content if any
                const textarea = document.getElementById('content');
                if (textarea && textarea.value) {
                    editor.setContent(textarea.value);
                }
            });
        },
        // Image upload settings
        images_upload_credentials: false,
        images_reuse_filename: true,
        automatic_uploads: false,

        // Content filtering
        valid_elements: '*[*]',
        extended_valid_elements: 'img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]',

        // Performance settings
        cache_suffix: '?v=7.0.0',

        // Forum-specific settings
        paste_data_images: true,
        paste_as_text: false,
        smart_paste: true,

        // Remove problematic features
        removed_menuitems: 'newdocument',

        // Style formats for forum posts
        style_formats: [
            {title: 'Tiêu đề 1', format: 'h3'},
            {title: 'Tiêu đề 2', format: 'h4'},
            {title: 'Tiêu đề 3', format: 'h5'},
            {title: 'Đoạn văn', format: 'p'},
            {title: 'Trích dẫn', format: 'blockquote'},
            {title: 'Code inline', format: 'code'}
        ],

        // Prevent form submission on Enter
        init_instance_callback: function(editor) {
            editor.on('keydown', function(e) {
                // Prevent form submission when pressing Enter without Shift
                if (e.keyCode === 13 && !e.shiftKey && !e.ctrlKey) {
                    e.stopPropagation();
                }
            });
        }
    });
}

function initializeFileUpload() {
    const uploadArea = document.getElementById('file-upload-area');
    const uploadZone = document.getElementById('upload-zone');
    const fileInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview-container');

    if (!uploadArea || !uploadZone || !fileInput || !previewContainer) return;

    let selectedFiles = [];

    // Drag & Drop Events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadZone.addEventListener(eventName, () => {
            uploadZone.classList.add('drag-over');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, () => {
            uploadZone.classList.remove('drag-over');
        }, false);
    });

    uploadZone.addEventListener('drop', handleDrop, false);
    uploadZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFileSelect);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        selectedFiles = Array.from(files);
        updateFileInput();
        displayPreviews();
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function displayPreviews() {
        previewContainer.innerHTML = '';

        if (selectedFiles.length === 0) {
            uploadZone.style.display = 'flex';
            return;
        }

        uploadZone.style.display = 'none';

        selectedFiles.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                createImagePreview(file, index);
            } else {
                createFilePreview(file, index);
            }
        });

        // Add upload more button
        const addMoreBtn = document.createElement('div');
        addMoreBtn.className = 'image-preview add-more-btn';
        addMoreBtn.innerHTML = `
            <div class="add-more-content">
                <i class="fas fa-plus"></i>
                <span>Thêm ảnh</span>
            </div>
        `;
        addMoreBtn.addEventListener('click', () => fileInput.click());
        previewContainer.appendChild(addMoreBtn);
    }

    function createImagePreview(file, index) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('div');
            preview.className = 'image-preview';
            preview.innerHTML = `
                <div class="preview-image">
                    <img src="${e.target.result}" alt="${file.name}">
                    <div class="image-overlay">
                        <div class="image-info">
                            <div class="file-name" title="${file.name}">${truncateFileName(file.name, 15)}</div>
                            <div class="file-size">${formatFileSize(file.size)}</div>
                        </div>
                        <div class="image-actions">
                            <button type="button" class="btn-action btn-view" title="Xem">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn-action btn-remove" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Handle view image
            preview.querySelector('.btn-view').addEventListener('click', (e) => {
                e.stopPropagation();
                showImageModal(reader.result, file.name);
            });

            // Handle remove image
            preview.querySelector('.btn-remove').addEventListener('click', (e) => {
                e.stopPropagation();
                removeFile(index);
            });

            previewContainer.appendChild(preview);
        };
        reader.readAsDataURL(file);
    }

    function createFilePreview(file, index) {
        const preview = document.createElement('div');
        preview.className = 'image-preview file-preview';
        preview.innerHTML = `
            <div class="file-icon">
                <i class="fas fa-file"></i>
            </div>
            <div class="file-info">
                <div class="file-name" title="${file.name}">${truncateFileName(file.name, 15)}</div>
                <div class="file-size">${formatFileSize(file.size)}</div>
            </div>
            <button type="button" class="btn-action btn-remove" title="Xóa">
                <i class="fas fa-trash"></i>
            </button>
        `;

        preview.querySelector('.btn-remove').addEventListener('click', (e) => {
            e.stopPropagation();
            removeFile(index);
        });

        previewContainer.appendChild(preview);
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateFileInput();
        displayPreviews();

        if (selectedFiles.length === 0) {
            uploadZone.style.display = 'flex';
        }
    }

    function truncateFileName(name, maxLength) {
        if (name.length <= maxLength) return name;
        const ext = name.split('.').pop();
        const nameWithoutExt = name.substring(0, name.lastIndexOf('.'));
        const truncated = nameWithoutExt.substring(0, maxLength - ext.length - 3) + '...';
        return truncated + '.' + ext;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showImageModal(src, title) {
        // Create modal for image preview
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        modal.innerHTML = `
            <div class="image-modal-backdrop">
                <div class="image-modal-content">
                    <div class="image-modal-header">
                        <h5>${title}</h5>
                        <button type="button" class="btn-close-modal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="image-modal-body">
                        <img src="${src}" alt="${title}">
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.display = 'flex';

        // Close modal events
        modal.querySelector('.btn-close-modal').addEventListener('click', () => {
            document.body.removeChild(modal);
        });

        modal.querySelector('.image-modal-backdrop').addEventListener('click', (e) => {
            if (e.target === modal.querySelector('.image-modal-backdrop')) {
                document.body.removeChild(modal);
            }
        });

        document.addEventListener('keydown', function escapeHandler(e) {
            if (e.key === 'Escape') {
                document.body.removeChild(modal);
                document.removeEventListener('keydown', escapeHandler);
            }
        });
    }
}

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
    // Initialize File Upload with Drag & Drop
    initializeFileUpload();

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
