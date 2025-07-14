@extends('layouts.app')

@section('title', $thread->title)

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/threads/show.css') }}">
@endpush

@section('content')
<div class="body_page">

    <!-- Main Thread -->
    <div class="detail_thread">
        <div class="thread-header p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="thread-title">{{ $thread->title }}</h1>
                <div class="thread-actions d-flex gap-2 align-items-center">
                    <x-thread-follow-button :thread="$thread" size="normal" />
                    <a href="#comment-{{ $comments->count() > 0 ? $comments->last()->id : '' }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-down"></i> {{ __('thread.go_to_end') }}
                    </a>
                </div>
            </div>
            <div class="thread-meta">
                <div class="d-flex justify-content-start g-3">
                    <div class="thread-meta-item">
                        <i class="fas fa-eye"></i> {{ number_format($thread->view_count) }} Lượt xem
                    </div>
                    <div class="thread-meta-item">
                        <i class="fas fa-comment"></i> {{ number_format($thread->comments_count ?? 0) }} Phản hồi
                    </div>
                    <div class="thread-meta-item">
                        <i class="fas fa-users"></i> {{ number_format($thread->participant_count) }} Người tham gia
                    </div>
                </div>
                <div class="thread-meta-item">
                    <i class="fas fa-clock"></i> Bài viết cuối bởi
                    <a href="{{ route('profile.show', $thread->lastCommenter) }}" class="ms-1 fw-semibold">
                        {{ $thread->lastCommenter->name ?? $thread->user->name }}
                    </a>
                    <span class="ms-1">{{ $thread->lastCommentAt ? $thread->lastCommentAt->diffForHumans() :
                        $thread->created_at->diffForHumans() }}</span>
                </div>
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
                    <span>{{ $thread->user->threads_count ?? 0 }} Bài viết</span> ·
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
        @if($thread->media && count($thread->media) > 0)
        <div class="thread-images mt-3">
            <div class="row">
                @foreach($thread->media as $media)
                @if(str_starts_with($media->file_type ?? '', 'image/'))
                <div class="col-md-4 mb-3">
                    <a href="{{ $media->url ?? asset('storage/' . $media->file_path) }}"
                        data-fancybox="thread-images"
                        data-caption="Thread image">
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
                    <i class="fas fa-thumbs-up"></i>
                    Thích
                    <span class="badge bg-secondary">{{ $thread->likes_count ?? 0 }}</span>
                </button>
            </form>

            <!-- Save Button -->
            <form action="{{ route('threads.save', $thread) }}" method="POST" class="d-inline ms-2">
                @csrf
                <button type="submit"
                    class="btn btn-sm {{ Auth::check() && $isSaved ? 'btn-success' : 'btn-outline-success' }} btn-save">
                    <i class="{{ Auth::check() && $isSaved ? 'far fa-bookmark-fill' : 'far fa-bookmark' }}"></i>
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
                    <i class="fas fa-bell-fill"></i>
                    Đang theo dõi
                </button>
            </form>
            @else
            <form action="{{ route('threads.follow.add', $thread) }}" method="POST" class="d-inline ms-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-info btn-theodoi">
                    <i class="fas fa-bell"></i>
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
                    <i class="fas fa-share-alt"></i> Chia sẻ
                </button>
                <ul class="dropdown-menu" aria-labelledby="shareDropdown">
                    <li><a class="dropdown-item"
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                            target="_blank"><i class="fab fa-facebook-f me-2"></i>Facebook</a></li>
                    <li><a class="dropdown-item"
                            href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($thread->title) }}"
                            target="_blank"><i class="fab fa-twitter me-2"></i>Twitter</a></li>
                    <li><a class="dropdown-item"
                            href="https://wa.me/?text={{ urlencode($thread->title . ' ' . request()->url()) }}"
                            target="_blank"><i class="fab fa-whatsapp me-2"></i>WhatsApp</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#"
                            onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Đã sao chép liên kết!'); return false;"><i
                                class="fas fa-clipboard me-2"></i>Sao chép liên kết</a></li>
                </ul>
            </div>

            <!-- Reply Button -->
            <a href="#reply-form" class="btn btn-sm btn-primary ms-2 btn-traloi">
                <i class="fas fa-reply"></i> Trả lời
            </a>

            <!-- Edit/Delete Buttons (if owner) -->
            @can('update', $thread)
            <div class="btn-group ms-2">
                <a href="{{ route('threads.edit', $thread) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#deleteThreadModal">
                    <i class="fas fa-trash"></i> Xóa
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

    <!-- Related Showcase Section -->
    @if($thread->showcase)
    <div class="showcase-section mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-star text-warning me-2"></i>
                    {{ __('showcase.related') }}
                </h5>
                <small class="text-muted">{{ __('showcase.for_thread') }}</small>
            </div>
    @else
    <!-- Create Showcase Section -->
    @auth
        @if($thread->userCanCreateShowcase(Auth::user()) && $thread->canCreateShowcase())
        <div class="create-showcase-section mb-4">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Tạo Showcase từ Thread này
                    </h5>
                    <small class="text-muted">Chuyển đổi thread thành showcase để highlight dự án của bạn</small>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Showcase sẽ giúp dự án của bạn được nhiều người biết đến hơn và có thể nhận được đánh giá từ cộng đồng.
                    </p>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createShowcaseModal">
                        <i class="fas fa-star me-2"></i>
                        Tạo Showcase
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endauth
    @endif

    @if($thread->showcase)
            <div class="card-body">
                <div class="row">
                    <!-- Showcase Image -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        @if($thread->showcase->media && $thread->showcase->media->count() > 0)
                            @php
                                $firstMedia = $thread->showcase->media->first();
                            @endphp
                            <img src="{{ $firstMedia->url ?? asset('storage/' . $firstMedia->file_path) }}"
                                 alt="{{ $thread->showcase->title }}"
                                 class="img-fluid rounded shadow-sm"
                                 style="width: 100%; height: 200px; object-fit: cover;"
                                 onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                 style="width: 100%; height: 200px;">
                                <i class="fas fa-image text-muted fs-1"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Showcase Content -->
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-2">{{ $thread->showcase->title }}</h6>

                        @if($thread->showcase->description)
                        <p class="text-muted mb-3">{{ Str::limit($thread->showcase->description, 200) }}</p>
                        @endif

                        <!-- Showcase Meta -->
                        <div class="showcase-meta mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ get_avatar_url($thread->showcase->user) }}"
                                     alt="{{ $thread->showcase->user->name }}"
                                     class="rounded-circle me-2"
                                     width="24" height="24">
                                <small class="text-muted">
                                    {{ __('ui.common.by') }}
                                    <a href="{{ route('profile.show', $thread->showcase->user->id) }}"
                                       class="text-decoration-none">
                                        {{ $thread->showcase->user->name }}
                                    </a>
                                </small>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $thread->showcase->created_at->diffForHumans() }}
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="showcase-actions">
                            <a href="{{ route('showcase.show', $thread->showcase) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>
                                {{ __('ui.actions.view_full_showcase') }}
                            </a>

                            @if($thread->showcase->showcase_url)
                            <a href="{{ $thread->showcase->showcase_url }}"
                               target="_blank"
                               class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-link me-1"></i>
                                {{ __('ui.actions.view_details') }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                            <span>{{ $comment->user->comments_count ?? 0 }} bình luận</span> ·
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

                <!-- Nested Replies -->
                @if(isset($comment->replies) && count($comment->replies) > 0)
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
                        </div>
                        <div class="card-footer py-1 d-flex justify-content-between">
                            <div>
                                <!-- Like Button -->
                                <form action="{{ route('comments.like', $reply) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ Auth::check() && $reply->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span class="badge bg-secondary">{{ $reply->like_count }}</span>
                                    </button>
                                </form>
                            </div>

                            <div>
                                <!-- Reply Button -->
                                <button class="btn btn-sm btn-outline-secondary reply-button"
                                    data-parent-id="{{ $comment->id }}">
                                    <i class="fas fa-reply"></i> Trả lời
                                </button>

                                <!-- Edit/Delete Buttons (if owner) -->
                                @can('update', $reply)
                                <div class="btn-group ms-2">
                                    <button class="btn btn-sm btn-outline-secondary edit-comment-button"
                                        data-comment-id="{{ $reply->id }}" data-comment-content="{{ $reply->content }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('comments.destroy', $reply) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');">
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
                            <i class="fas fa-thumbs-up"></i>
                            <span class="badge bg-secondary">{{ $comment->like_count }}</span>
                        </button>
                    </form>
                </div>

                <div>
                    <!-- Quote Button -->
                    <button class="btn btn-sm btn-outline-secondary quote-button" data-comment-id="{{ $comment->id }}"
                        data-comment-content="{{ $comment->content }}" data-user-name="{{ $comment->user->name }}">
                        <i class="fas fa-comment-quote"></i> Trích dẫn
                    </button>

                    <!-- Reply Button -->
                    <button class="btn btn-sm btn-outline-secondary reply-button ms-2"
                        data-parent-id="{{ $comment->id }}">
                        <i class="fas fa-reply"></i> Trả lời
                    </button>

                    <!-- Edit/Delete Buttons (if owner) -->
                    @can('update', $comment)
                    <div class="btn-group ms-2">
                        <button class="btn btn-sm btn-outline-secondary edit-comment-button"
                            data-comment-id="{{ $comment->id }}" data-comment-content="{{ $comment->content }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
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
                        <i class="fas fa-comment-text me-2"></i>Nội dung phản hồi <span class="text-danger">*</span>
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
                        <i class="fas fa-image me-2"></i>Đính kèm hình ảnh (tùy chọn)
                    </label>

                    <!-- Custom File Upload Area -->
                    <div class="file-upload-area" id="file-upload-area">
                        <div class="upload-zone" id="upload-zone">
                            <div class="upload-content">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
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
                        <i class="fas fa-paper-plane"></i> Gửi phản hồi
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
    @if(count($relatedThreads) > 0)
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
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang gửi...';

        // Submit form
        try {
            form.submit();
        } catch (error) {
            console.error('Form submission error:', error);

            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi phản hồi';

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

<!-- Create Showcase Modal -->
@auth
@if($thread->userCanCreateShowcase(Auth::user()) && $thread->canCreateShowcase())
<div class="modal fade" id="createShowcaseModal" tabindex="-1" aria-labelledby="createShowcaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createShowcaseForm" action="{{ route('threads.create-showcase', $thread) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createShowcaseModalLabel">
                        <i class="fas fa-star text-warning me-2"></i>
                        Tạo Showcase từ Thread
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Progress Steps -->
                    <div class="progress mb-4" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 33%" id="progressBar"></div>
                    </div>

                    <div class="steps-indicator mb-4">
                        <div class="d-flex justify-content-between">
                            <div class="step active" data-step="1">
                                <div class="step-number">1</div>
                                <div class="step-label">Thông tin cơ bản</div>
                            </div>
                            <div class="step" data-step="2">
                                <div class="step-number">2</div>
                                <div class="step-label">Nội dung</div>
                            </div>
                            <div class="step" data-step="3">
                                <div class="step-number">3</div>
                                <div class="step-label">Hoàn tất</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Basic Information -->
                    <div class="step-content" id="step1">
                        <h6 class="mb-3">Bước 1: Thông tin cơ bản</h6>

                        <div class="mb-3">
                            <label for="showcase_title" class="form-label">Tiêu đề Showcase <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="showcase_title" name="title"
                                   value="{{ $thread->title }}" required>
                            <div class="form-text">Tiêu đề sẽ được sử dụng làm tên showcase</div>
                        </div>

                        <div class="mb-3">
                            <label for="showcase_category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="showcase_category" name="category" required>
                                <option value="">Chọn danh mục</option>
                                <option value="Thiết kế Cơ khí" {{ $thread->category && $thread->category->name == 'Thiết kế Cơ khí' ? 'selected' : '' }}>Thiết kế Cơ khí</option>
                                <option value="Công nghệ Chế tạo" {{ $thread->category && $thread->category->name == 'Công nghệ Chế tạo' ? 'selected' : '' }}>Công nghệ Chế tạo</option>
                                <option value="Vật liệu Kỹ thuật" {{ $thread->category && $thread->category->name == 'Vật liệu Kỹ thuật' ? 'selected' : '' }}>Vật liệu Kỹ thuật</option>
                                <option value="Tự động hóa & Robotics" {{ $thread->category && $thread->category->name == 'Tự động hóa & Robotics' ? 'selected' : '' }}>Tự động hóa & Robotics</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="project_type" class="form-label">Loại dự án</label>
                            <select class="form-select" id="project_type" name="project_type">
                                <option value="">Chọn loại dự án</option>
                                <option value="Design Project">Design Project</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Analysis & Simulation">Analysis & Simulation</option>
                                <option value="Research & Development">Research & Development</option>
                                <option value="Case Study">Case Study</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 2: Content -->
                    <div class="step-content d-none" id="step2">
                        <h6 class="mb-3">Bước 2: Nội dung Showcase</h6>

                        <div class="mb-3">
                            <label for="showcase_description" class="form-label">Mô tả dự án <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="showcase_description" name="description" rows="6" required>{{ strip_tags($thread->content) }}</textarea>
                            <div class="form-text">Mô tả chi tiết về dự án, phương pháp và kết quả đạt được</div>
                        </div>

                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Ảnh đại diện <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required>
                            <div class="form-text">Chọn ảnh đại diện cho showcase (JPG, PNG, GIF, WebP - tối đa 5MB)</div>
                            @if($thread->featured_image)
                            <div class="mt-2">
                                <small class="text-muted">Ảnh hiện tại từ thread:</small><br>
                                <img src="{{ asset('storage/' . $thread->featured_image) }}" alt="Thread Image" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="complexity_level" class="form-label">Mức độ phức tạp</label>
                                    <select class="form-select" id="complexity_level" name="complexity_level">
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate" selected>Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="industry_application" class="form-label">Ứng dụng ngành</label>
                                    <input type="text" class="form-control" id="industry_application" name="industry_application"
                                           placeholder="VD: Automotive, Aerospace, Manufacturing">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Confirmation -->
                    <div class="step-content d-none" id="step3">
                        <h6 class="mb-3">Bước 3: Xác nhận tạo Showcase</h6>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Xác nhận thông tin:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Showcase sẽ được tạo từ nội dung thread hiện tại</li>
                                <li>Thread gốc vẫn được giữ nguyên</li>
                                <li>Showcase có thể được chỉnh sửa sau khi tạo</li>
                                <li>Cộng đồng có thể đánh giá và bình luận về showcase</li>
                            </ul>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                Tôi đồng ý cho phép cộng đồng xem và đánh giá showcase này
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Trước
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn">
                        Tiếp theo<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                        <i class="fas fa-star me-2"></i>Tạo Showcase
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endauth

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/threads/show.css') }}">
<style>
.steps-indicator .step {
    text-align: center;
    flex: 1;
}

.steps-indicator .step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-weight: bold;
    font-size: 14px;
}

.steps-indicator .step.active .step-number {
    background-color: #198754;
    color: white;
}

.steps-indicator .step.completed .step-number {
    background-color: #20c997;
    color: white;
}

.steps-indicator .step-label {
    font-size: 12px;
    color: #6c757d;
}

.steps-indicator .step.active .step-label {
    color: #198754;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
    // Additional Fancybox configuration for threads if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Thread-specific Fancybox configuration
        console.log('Thread images ready for Fancybox');

        // Showcase creation wizard
        initShowcaseWizard();
    });

    function initShowcaseWizard() {
        let currentStep = 1;
        const totalSteps = 3;

        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');
        const progressBar = document.getElementById('progressBar');

        if (!nextBtn) return; // Exit if modal doesn't exist

        // Next button click
        nextBtn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                    updateProgress();
                    updateButtons();
                }
            }
        });

        // Previous button click
        prevBtn.addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                updateProgress();
                updateButtons();
            }
        });

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('d-none');
            });

            // Show current step
            document.getElementById('step' + step).classList.remove('d-none');

            // Update step indicators
            document.querySelectorAll('.step').forEach((stepEl, index) => {
                stepEl.classList.remove('active', 'completed');
                if (index + 1 === step) {
                    stepEl.classList.add('active');
                } else if (index + 1 < step) {
                    stepEl.classList.add('completed');
                }
            });
        }

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            progressBar.style.width = progress + '%';
        }

        function updateButtons() {
            // Previous button
            prevBtn.style.display = currentStep > 1 ? 'inline-block' : 'none';

            // Next/Submit buttons
            if (currentStep === totalSteps) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-block';
            } else {
                nextBtn.style.display = 'inline-block';
                submitBtn.style.display = 'none';
            }
        }

        function validateStep(step) {
            let isValid = true;

            if (step === 1) {
                // Validate step 1
                const title = document.getElementById('showcase_title').value.trim();
                const category = document.getElementById('showcase_category').value;

                if (!title) {
                    showError('showcase_title', 'Vui lòng nhập tiêu đề showcase');
                    isValid = false;
                }

                if (!category) {
                    showError('showcase_category', 'Vui lòng chọn danh mục');
                    isValid = false;
                }
            } else if (step === 2) {
                // Validate step 2
                const description = document.getElementById('showcase_description').value.trim();
                const coverImage = document.getElementById('cover_image').files[0];

                if (!description) {
                    showError('showcase_description', 'Vui lòng nhập mô tả dự án');
                    isValid = false;
                }

                if (!coverImage) {
                    showError('cover_image', 'Vui lòng chọn ảnh đại diện');
                    isValid = false;
                } else {
                    // Validate file size (5MB)
                    if (coverImage.size > 5 * 1024 * 1024) {
                        showError('cover_image', 'Kích thước file không được vượt quá 5MB');
                        isValid = false;
                    }
                }
            } else if (step === 3) {
                // Validate step 3
                const agreeTerms = document.getElementById('agree_terms').checked;

                if (!agreeTerms) {
                    showError('agree_terms', 'Vui lòng đồng ý với điều khoản');
                    isValid = false;
                }
            }

            return isValid;
        }

        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);

            // Remove existing error
            const existingError = field.parentNode.querySelector('.text-danger');
            if (existingError) {
                existingError.remove();
            }

            // Add error class
            field.classList.add('is-invalid');

            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger mt-1';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);

            // Remove error on input
            field.addEventListener('input', function() {
                field.classList.remove('is-invalid');
                const errorMsg = field.parentNode.querySelector('.text-danger');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }, { once: true });
        }

        // Form submission
        document.getElementById('createShowcaseForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (validateStep(currentStep)) {
                // Show loading
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo...';
                submitBtn.disabled = true;

                // Submit form
                this.submit();
            }
        });

        // Reset form when modal is hidden
        document.getElementById('createShowcaseModal').addEventListener('hidden.bs.modal', function() {
            currentStep = 1;
            showStep(1);
            updateProgress();
            updateButtons();

            // Reset form
            document.getElementById('createShowcaseForm').reset();

            // Remove validation errors
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
            document.querySelectorAll('.text-danger').forEach(error => {
                error.remove();
            });

            // Reset submit button
            submitBtn.innerHTML = '<i class="fas fa-star me-2"></i>Tạo Showcase';
            submitBtn.disabled = false;
        });
    }
</script>
@endpush
