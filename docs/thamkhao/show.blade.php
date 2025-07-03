@extends('layouts.app')

@section('title', $thread->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/thread-detail.css') }}">
@endpush

@section('content')
<div class="container2">



    <!-- Main Thread -->
    <div class="card mb-4">
        <!-- Thread Info -->
        <div class="thread-header p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="thread-title">{{ $thread->title }}</h1>

                <div class="thread-actions">
                    <a href="#comment-{{ $comments->count() > 0 ? $comments->last()->id : '' }}" class="btn-jump">
                        <i class="bi bi-arrow-right"></i> Đến bình luận cuối
                    </a>

                    @php
                    $isFollowed = Auth::check() && $thread->isFollowedBy(Auth::user());
                    @endphp
                    @if($isFollowed)
                    <form action="{{ route('threads.follow.remove', $thread) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-follow">
                            <i class="bi bi-bell-fill"></i> Đang theo dõi
                        </button>
                    </form>
                    @else
                    <form action="{{ route('threads.follow.add', $thread) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-follow">
                            <i class="bi bi-bell"></i> Theo dõi
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="thread-meta">

                <div class="d-flex justify-content-start g-3">
                    <div class="thread-meta-item">
                        <i class="bi bi-eye"></i> {{ number_format($thread->view_count) }} Lượt xem
                    </div>
                    <div class="thread-meta-item">
                        <i class="bi bi-chat"></i> {{ number_format($thread->allComments->count()) }} Phản hồi
                    </div>
                    <div class="thread-meta-item">
                        <i class="bi bi-people"></i> {{ number_format($thread->participant_count) }} Người tham gia
                    </div>
                </div>
                <div class="thread-meta-item">
                    <i class="bi bi-clock"></i> Bài viết cuối bởi
                    <a href="{{ route('profile.show', $thread->lastCommenter) }}" class="ms-1 fw-semibold">
                        {{ $thread->lastCommenter->name ?? $thread->user->name }}
                    </a>
                    <span class="ms-1">{{ $thread->lastCommentAt ? $thread->lastCommentAt->diffForHumans() :
                        $thread->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
        <div class="card-header d-flex justify-content-between align-items-center" style="border-bottom: none;">
            <div class="d-flex align-items-center">
                <img src="{{ $thread->user->getAvatarUrl() }}" alt="{{ $thread->user->name }}"
                    class="rounded-circle me-2" width="40" height="40"
                    onerror="this.src='{{ asset('images/placeholders/50x50.png') }}'">
                <div>
                    <a href="{{ route('profile.show', $thread->user->username ?? $thread->user->id) }}"
                        class="fw-bold text-decoration-none">{{
                        $thread->user->name }}</a>
                    <div class="text-muted small">
                        <span>{{ $thread->user->threads->count() }} Bài viết</span> ·
                        <span>Tham gia {{ $thread->user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="text-muted small">
                #1 · {{ $thread->created_at->diffForHumans() }}
            </div>
        </div>
        <div class="card-body">
            <!-- Project Details -->
            @if($thread->status)
            <!--div class="project-details mb-3 p-3 bg-light rounded">
                @if($thread->status)
                <div><strong>Trạng thái:</strong> {{ $thread->status }}</div>
                @endif
            </div-->
            @endif

            <!-- Poll Section -->
            @include('threads.partials.poll')

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

            <!-- Thread Images -->
            @if($thread->media && $thread->media->count() > 0)
            <div class="thread-images mt-3">
                <div class="row">
                    @foreach($thread->media as $media)
                    @if(str_starts_with($media->file_type ?? '', 'image/'))
                    <div class="col-md-4 mb-3">
                        <a href="{{ $media->url ?? asset('storage/' . $media->file_path) }}"
                            data-lightbox="thread-images">
                            <img src="{{ $media->url ?? asset('storage/' . $media->file_path) }}" alt="Thread image"
                                class="img-fluid rounded"
                                onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
                        </a>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <!-- Like Button -->
                <form action="{{ route('threads.like', $thread) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit"
                        class="btn btn-sm {{ Auth::check() && $isLiked ? 'btn-primary' : 'btn-outline-primary' }} btn-like">
                        <i class="bi bi-hand-thumbs-up"></i>
                        Thích
                        <span class="badge bg-secondary">{{ $thread->likes->count() }}</span>
                    </button>
                </form>

                <!-- Save Button -->
                <form action="{{ route('threads.save', $thread) }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <button type="submit"
                        class="btn btn-sm {{ Auth::check() && $isSaved ? 'btn-success' : 'btn-outline-success' }} btn-save">
                        <i class="bi {{ Auth::check() && $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                        {{ Auth::check() && $isSaved ? 'Đã lưu' : 'Lưu' }}
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
                    <button type="submit" class="btn btn-sm btn-info btn-theodoi">
                        <i class="bi bi-bell-fill"></i>
                        Đang theo dõi
                    </button>
                </form>
                @else
                <form action="{{ route('threads.follow.add', $thread) }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-info btn-theodoi">
                        <i class="bi bi-bell"></i>
                        Theo dõi
                    </button>
                </form>
                @endif
            </div>

            <div>
                <!-- Share Button -->
                <div class="dropdown d-inline">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle btn-share" type="button"
                        id="shareDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-share"></i> Chia sẻ
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="shareDropdown">
                        <li><a class="dropdown-item"
                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                target="_blank"><i class="bi bi-facebook me-2"></i>Facebook</a></li>
                        <li><a class="dropdown-item"
                                href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($thread->title) }}"
                                target="_blank"><i class="bi bi-twitter me-2"></i>Twitter</a></li>
                        <li><a class="dropdown-item"
                                href="https://wa.me/?text={{ urlencode($thread->title . ' ' . request()->url()) }}"
                                target="_blank"><i class="bi bi-whatsapp me-2"></i>WhatsApp</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#"
                                onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Đã sao chép liên kết!'); return false;"><i
                                    class="bi bi-clipboard me-2"></i>Sao chép liên kết</a></li>
                    </ul>
                </div>

                <!-- Reply Button -->
                <a href="#reply-form" class="btn btn-sm btn-primary ms-2 btn-traloi">
                    <i class="bi bi-reply"></i> Trả lời
                </a>

                <!-- Edit/Delete Buttons (if owner) -->
                @can('update', $thread)
                <div class="btn-group ms-2">
                    <a href="{{ route('threads.edit', $thread) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteThreadModal">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteThreadModal" tabindex="-1" aria-labelledby="deleteThreadModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteThreadModalLabel">Xác nhận xóa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Bạn có chắc chắn muốn xóa chủ đề này? Hành động này không thể hoàn tác.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <form action="{{ route('threads.destroy', $thread) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Xóa chủ đề</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>{{ $comments->total() }} Phản hồi</h3>

            <!-- Sort Options -->
            <div class="btn-group">
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}"
                    class="btn btn-sm {{ request('sort', 'oldest') == 'oldest' ? 'btn-primary' : 'btn-outline-primary' }}">Cũ
                    nhất</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
                    class="btn btn-sm {{ request('sort') == 'newest' ? 'btn-primary' : 'btn-outline-primary' }}">Mới
                    nhất</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'reactions']) }}"
                    class="btn btn-sm {{ request('sort') == 'reactions' ? 'btn-primary' : 'btn-outline-primary' }}">Nhiều
                    tương tác</a>
            </div>
        </div>

        <!-- Comments List -->
        @forelse($comments as $comment)
        <div class="card mb-3" id="comment-{{ $comment->id }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="{{ $comment->user->getAvatarUrl() }}" alt="{{ $comment->user->name }}"
                        class="rounded-circle me-2" width="40" height="40"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&color=7F9CF5&background=EBF4FF'">
                    <div>
                        <a href="{{ route('profile.show', $comment->user) }}" class="fw-bold text-decoration-none">{{
                            $comment->user->name }}</a>
                        <div class="text-muted small">
                            <span>{{ $comment->user->comments->count() }} bình luận</span> ·
                            <span>Tham gia {{ $comment->user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-muted small">
                    #{{ $loop->iteration + 1 }} · {{ $comment->created_at->diffForHumans() }}
                </div>
            </div>
            <div class="card-body">
                <div class="comment-content">
                    {!! $comment->content !!}
                </div>

                @if($comment->has_media && $comment->attachments->count() > 0)
                <div class="comment-attachments mt-3">
                    <div class="row g-2">
                        @foreach($comment->attachments as $attachment)
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="{{ $attachment->url }}" class="d-block"
                                data-lightbox="comment-{{ $comment->id }}-images"
                                data-title="{{ $attachment->file_name }}">
                                <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}"
                                    class="img-fluid rounded">
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Nested Replies -->
                @if($comment->replies->count() > 0)
                <div class="nested-replies mt-3">
                    @foreach($comment->replies as $reply)
                    <div class="card mb-2" id="comment-{{ $reply->id }}">
                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <div class="d-flex align-items-center">
                                <img src="{{ $reply->user->getAvatarUrl() }}" alt="{{ $reply->user->name }}"
                                    class="rounded-circle me-2" width="30" height="30"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}&color=7F9CF5&background=EBF4FF'">
                                <div>
                                    <a href="{{ route('profile.show', $reply->user) }}"
                                        class="fw-bold text-decoration-none">{{ $reply->user->name }}</a>
                                </div>
                            </div>
                            <div class="text-muted small">
                                {{ $reply->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="reply-content">
                                {!! $reply->content !!}
                            </div>

                            @if($reply->has_media && $reply->attachments->count() > 0)
                            <div class="reply-attachments mt-2">
                                <div class="row g-2">
                                    @foreach($reply->attachments as $attachment)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <a href="{{ $attachment->url }}" class="d-block"
                                            data-lightbox="reply-{{ $reply->id }}-images"
                                            data-title="{{ $attachment->file_name }}">
                                            <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}"
                                                class="img-fluid rounded">
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer py-1 d-flex justify-content-between">
                            <div>
                                <!-- Like Button -->
                                <form action="{{ route('comments.like', $reply) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ Auth::check() && $reply->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <span class="badge bg-secondary">{{ $reply->like_count }}</span>
                                    </button>
                                </form>
                            </div>

                            <div>
                                <!-- Reply Button -->
                                <button class="btn btn-sm btn-outline-secondary reply-button"
                                    data-parent-id="{{ $comment->id }}">
                                    <i class="bi bi-reply"></i> Trả lời
                                </button>

                                <!-- Edit/Delete Buttons (if owner) -->
                                @can('update', $reply)
                                <div class="btn-group ms-2">
                                    <button class="btn btn-sm btn-outline-secondary edit-comment-button"
                                        data-comment-id="{{ $reply->id }}" data-comment-content="{{ $reply->content }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('comments.destroy', $reply) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-between">
                <div>
                    <!-- Like Button -->
                    <form action="{{ route('comments.like', $comment) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit"
                            class="btn btn-sm {{ Auth::check() && $comment->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-hand-thumbs-up"></i>
                            <span class="badge bg-secondary">{{ $comment->like_count }}</span>
                        </button>
                    </form>
                </div>

                <div>
                    <!-- Quote Button -->
                    <button class="btn btn-sm btn-outline-secondary quote-button" data-comment-id="{{ $comment->id }}"
                        data-comment-content="{{ $comment->content }}" data-user-name="{{ $comment->user->name }}">
                        <i class="bi bi-chat-quote"></i> Trích dẫn
                    </button>

                    <!-- Reply Button -->
                    <button class="btn btn-sm btn-outline-secondary reply-button ms-2"
                        data-parent-id="{{ $comment->id }}">
                        <i class="bi bi-reply"></i> Trả lời
                    </button>

                    <!-- Edit/Delete Buttons (if owner) -->
                    @can('update', $comment)
                    <div class="btn-group ms-2">
                        <button class="btn btn-sm btn-outline-secondary edit-comment-button"
                            data-comment-id="{{ $comment->id }}" data-comment-content="{{ $comment->content }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info">
            Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
        </div>
        @endforelse

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $comments->links() }}
        </div>
    </div>

    <!-- Reply Form -->
    @auth
    <div class="card" id="reply-form">
        <div class="card-header">
            <h4>Đăng phản hồi</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('threads.comments.store', $thread) }}" method="POST" enctype="multipart/form-data"
                id="reply-form-element">
                @csrf
                <input type="hidden" name="parent_id" id="parent_id" value="">

                <div class="mb-3">
                    <label for="content" class="form-label">
                        <i class="bi bi-chat-text me-2"></i>Nội dung phản hồi <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content"
                        placeholder="Nhập nội dung phản hồi của bạn...">{{ old('content') }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="content-error" class="invalid-feedback" style="display: none;">
                        Vui lòng nhập nội dung phản hồi
                    </div>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">
                        <i class="bi bi-image me-2"></i>Đính kèm hình ảnh (tùy chọn)
                    </label>

                    <!-- Custom File Upload Area -->
                    <div class="file-upload-area" id="file-upload-area">
                        <div class="upload-zone" id="upload-zone">
                            <div class="upload-content">
                                <div class="upload-icon">
                                    <i class="bi bi-cloud-upload"></i>
                                </div>
                                <div class="upload-text">
                                    <h6 class="mb-1">Kéo thả hình ảnh vào đây</h6>
                                    <p class="text-muted mb-2">hoặc <span class="text-primary fw-semibold">chọn từ máy
                                            tính</span></p>
                                    <small class="text-muted">Hỗ trợ: JPG, PNG, GIF (tối đa 5MB mỗi file)</small>
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
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div id="reply-to-info" class="text-muted" style="display: none;">
                        Trả lời: <span id="reply-to-name"></span>
                        <button type="button" class="btn btn-sm btn-link p-0 ms-2" id="cancel-reply">Hủy</button>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-reply-btn">
                        <i class="bi bi-send"></i> Gửi phản hồi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        Vui lòng <a href="{{ route('login') }}">đăng nhập</a> hoặc <a href="{{ route('register') }}">đăng ký</a> để đăng
        phản hồi.
    </div>
    @endauth

    <!-- Related Threads -->
    @if($relatedThreads->count() > 0)
    <div class="related-threads mt-4">
        <h3>Chủ đề liên quan</h3>
        <div class="list-group">
            @foreach($relatedThreads as $relatedThread)
            <a href="{{ route('threads.show', $relatedThread) }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $relatedThread->title }}</h5>
                    <small>{{ $relatedThread->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1">{{ Str::limit(strip_tags($relatedThread->content), 100) }}</p>
                <small>Bởi {{ $relatedThread->user->name }} · {{ $relatedThread->view_count }} lượt xem · {{
                    $relatedThread->allComments->count() }} phản hồi</small>
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
                text: 'Trích dẫn',
                icon: 'quote',
                tooltip: 'Thêm trích dẫn',
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
                <i class="bi bi-plus-lg"></i>
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
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn-action btn-remove" title="Xóa">
                                <i class="bi bi-trash"></i>
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
                <i class="bi bi-file-earmark"></i>
            </div>
            <div class="file-info">
                <div class="file-name" title="${file.name}">${truncateFileName(file.name, 15)}</div>
                <div class="file-size">${formatFileSize(file.size)}</div>
            </div>
            <button type="button" class="btn-action btn-remove" title="Xóa">
                <i class="bi bi-trash"></i>
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
                            <i class="bi bi-x-lg"></i>
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
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang gửi...';

        // Submit form
        try {
            form.submit();
        } catch (error) {
            console.error('Form submission error:', error);

            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send"></i> Gửi phản hồi';

            alert('Có lỗi xảy ra khi gửi phản hồi. Vui lòng thử lại.');
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

@push('styles')
<style>
    /* File Upload Styles */
    .file-upload-area {
        border: 2px dashed #e0e7ff;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8faff 0%, #f1f5ff 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .upload-zone {
        position: relative;
        padding: 2rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .upload-zone:hover {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f2ff 100%);
        border-color: #c7d2fe;
        transform: translateY(-2px);
    }

    .upload-zone.drag-over {
        border-color: #4f46e5;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.15);
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .upload-content {
        pointer-events: none;
    }

    .upload-icon {
        font-size: 3rem;
        color: #6366f1;
        margin-bottom: 1rem;
        animation: float 2s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-8px);
        }
    }

    .upload-text h6 {
        color: #374151;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .upload-text p {
        color: #6b7280;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .upload-text .text-primary {
        color: #4f46e5 !important;
        cursor: pointer;
        text-decoration: underline;
    }

    /* Image Previews Grid */
    .image-previews-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
        padding: 0;
    }

    .image-preview {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        border: 2px solid #f3f4f6;
        transition: all 0.3s ease;
        aspect-ratio: 1;
        cursor: pointer;
    }

    .image-preview:hover {
        border-color: #d1d5db;
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .preview-image {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .image-preview:hover .preview-image img {
        transform: scale(1.05);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom,
                rgba(0, 0, 0, 0) 0%,
                rgba(0, 0, 0, 0.1) 50%,
                rgba(0, 0, 0, 0.7) 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0.5rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-preview:hover .image-overlay {
        opacity: 1;
    }

    .image-info {
        align-self: flex-start;
    }

    .file-name {
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        margin-bottom: 2px;
    }

    .file-size {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.7rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .image-actions {
        align-self: flex-end;
        display: flex;
        gap: 0.25rem;
    }

    .btn-action {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 6px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        backdrop-filter: blur(4px);
    }

    .btn-action:hover {
        background: rgba(255, 255, 255, 1);
        transform: scale(1.1);
    }

    .btn-action.btn-remove {
        background: rgba(239, 68, 68, 0.9);
        color: white;
    }

    .btn-action.btn-remove:hover {
        background: rgba(220, 38, 38, 1);
    }

    .btn-action.btn-view {
        color: #374151;
    }

    /* Add More Button */
    .add-more-btn {
        border: 2px dashed #d1d5db;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-more-btn:hover {
        border-color: #6366f1;
        background: #f0f4ff;
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.15);
    }

    .add-more-content {
        text-align: center;
        color: #6b7280;
    }

    .add-more-content i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .add-more-content span {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .add-more-btn:hover .add-more-content {
        color: #6366f1;
    }

    /* File Preview (non-image) */
    .file-preview {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        text-align: center;
        background: #f8fafc;
    }

    .file-icon {
        font-size: 2rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .file-preview .file-info {
        flex: 1;
        margin-bottom: 0.5rem;
    }

    .file-preview .btn-remove {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
    }

    /* Upload Progress */
    .upload-progress {
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .progress {
        height: 6px;
        border-radius: 3px;
        background: #e5e7eb;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }

    .progress-bar {
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
        height: 100%;
        transition: width 0.3s ease;
    }

    /* Image Modal */
    .image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1060;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .image-modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .image-modal-content {
        background: white;
        border-radius: 12px;
        max-width: 90vw;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(50px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .image-modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: between;
        align-items: center;
        background: #f8fafc;
    }

    .image-modal-header h5 {
        margin: 0;
        font-weight: 600;
        color: #374151;
        flex: 1;
        font-size: 1rem;
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: #6b7280;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .btn-close-modal:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .image-modal-body {
        padding: 0;
        max-height: 70vh;
        overflow: auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-modal-body img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .image-previews-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.75rem;
        }

        .upload-zone {
            padding: 1.5rem 1rem;
            min-height: 120px;
        }

        .upload-icon {
            font-size: 2.5rem;
        }

        .upload-text h6 {
            font-size: 0.9rem;
        }

        .upload-text p {
            font-size: 0.8rem;
        }

        .image-modal-content {
            max-width: 95vw;
            max-height: 95vh;
        }

        .image-modal-header {
            padding: 0.75rem 1rem;
        }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .file-upload-area {
            border-color: #4a5568;
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        }

        .upload-zone:hover {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            border-color: #718096;
        }

        .upload-zone.drag-over {
            border-color: #805ad5;
            background: linear-gradient(135deg, #553c9a 0%, #4c51bf 100%);
        }

        .upload-text h6 {
            color: #e2e8f0;
        }

        .upload-text p {
            color: #a0aec0;
        }

        .image-preview {
            background: #2d3748;
            border-color: #4a5568;
        }

        .image-preview:hover {
            border-color: #718096;
        }

        .add-more-btn {
            border-color: #4a5568;
            background: #2d3748;
        }

        .add-more-btn:hover {
            border-color: #805ad5;
            background: #553c9a;
        }

        .file-preview {
            background: #2d3748;
        }

        .image-modal-content {
            background: #2d3748;
        }

        .image-modal-header {
            background: #1a202c;
            border-color: #4a5568;
        }

        .image-modal-header h5 {
            color: #e2e8f0;
        }

        .btn-close-modal {
            color: #a0aec0;
        }

        .btn-close-modal:hover {
            background: #4a5568;
            color: #e2e8f0;
        }
    }

    /* Image preview styles */
    .image-preview {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .image-preview .remove-image {
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }

    /* TinyMCE custom styles */
    .tox-tinymce {
        border-radius: 0.375rem !important;
        border: 1px solid #ced4da !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.05) !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
    }

    .tox-tinymce:focus-within {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }

    /* TinyMCE error state */
    .tox-tinymce.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .form-control.is-invalid~.tox-tinymce {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .tox-toolbar {
        border-bottom: 1px solid #ced4da !important;
        background: #f8f9fa !important;
        padding: 8px !important;
    }

    .tox-toolbar-overlord {
        background: #f8f9fa !important;
    }

    .tox-edit-area {
        border: none !important;
        background: #fff !important;
    }

    .tox-edit-area__iframe {
        background: #fff !important;
    }

    /* Custom button styles */
    .tox-tbtn {
        border-radius: 4px !important;
        margin: 1px !important;
    }

    .tox-tbtn:hover {
        background: #e9ecef !important;
    }

    .tox-tbtn--enabled {
        background: #007bff !important;
        color: #fff !important;
    }

    /* Dark mode support for TinyMCE */
    @media (prefers-color-scheme: dark) {
        .tox-tinymce {
            border-color: #4a5568 !important;
            background: #2d3748 !important;
        }

        .tox-toolbar {
            border-color: #4a5568 !important;
            background: #2d3748 !important;
        }

        .tox-edit-area {
            background: #1a202c !important;
        }

        .tox-edit-area__iframe {
            background: #1a202c !important;
        }
    }

    /* Comment attachment styles */
    .comment-attachments img,
    .reply-attachments img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        transition: transform 0.2s;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .comment-attachments a:hover img,
    .reply-attachments a:hover img {
        transform: scale(1.03);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Thread content styles */
    .thread-content blockquote,
    .comment-content blockquote,
    .reply-content blockquote {
        border-left: 4px solid #007bff;
        padding: 12px 16px;
        margin: 16px 0;
        background-color: #f8f9fa;
        font-style: italic;
        border-radius: 4px;
        position: relative;
    }

    .thread-content blockquote::before,
    .comment-content blockquote::before,
    .reply-content blockquote::before {
        content: '"';
        font-size: 2em;
        color: #007bff;
        position: absolute;
        left: -8px;
        top: -8px;
        font-family: Georgia, serif;
    }

    .thread-content code,
    .comment-content code,
    .reply-content code {
        background-color: #f1f3f4;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Monaco', 'Consolas', 'Courier New', monospace;
        font-size: 13px;
        color: #d63384;
    }

    .thread-content pre,
    .comment-content pre,
    .reply-content pre {
        background-color: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        overflow-x: auto;
        border: 1px solid #e9ecef;
        font-family: 'Monaco', 'Consolas', 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.4;
    }

    /* Forum specific improvements */
    .card-body .thread-content,
    .card-body .comment-content,
    .card-body .reply-content {
        line-height: 1.6;
    }

    .card-body .thread-content p,
    .card-body .comment-content p,
    .card-body .reply-content p {
        margin-bottom: 1rem;
    }

    .card-body .thread-content img,
    .card-body .comment-content img,
    .card-body .reply-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 8px 0;
    }

    /* Responsive TinyMCE */
    @media (max-width: 768px) {
        .tox-tinymce {
            font-size: 16px !important;
            /* Prevent zoom on iOS */
        }

        .tox-toolbar {
            flex-wrap: wrap !important;
        }
    }

    /* Loading state */
    .tox-tinymce.tox-tinymce--loading {
        opacity: 0.7;
    }

    /* Custom forum button styles */
    .btn-like,
    .btn-save,
    .btn-theodoi,
    .btn-share,
    .btn-traloi {
        transition: all 0.2s ease-in-out;
        border-radius: 6px;
        font-weight: 500;
    }

    .btn-like:hover,
    .btn-save:hover,
    .btn-theodoi:hover,
    .btn-share:hover,
    .btn-traloi:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush