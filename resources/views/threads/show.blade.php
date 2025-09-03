@extends('layouts.app')

@section('title', $thread->title)

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/threads.css') }}">

@endpush

@section('content')
<div class="body_page">
    <!-- Main Thread -->
    <div class="detail_thread">
        <div class="detail_thread_body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center thread_user">
                    <img src="{{ $thread->user->getAvatarUrl() }}" alt="{{ $thread->user->name }}" class="rounded-circle me-2" width="40" height="40">
                    <div>
                        <a href="{{ route('profile.show', $thread->user->username ?? $thread->user->id) }}" class="fw-bold text-decoration-none">{{ $thread->user->name }}</a>
                        <div class="text-muted small">
                            <span>{{ $thread->user->threads_count ?? 0 }} {{ __('thread.posts') }}</span> ·
                            <span>{{ __('thread.joined') }} {{ $thread->user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="thread-actions d-flex gap-2 align-items-center">
                    <x-thread-follow-button :thread="$thread" size="normal" />
                    <!-- Save Button -->
                    @auth
                    <button type="button" class="btn btn-sm  {{ $isSaved ? 'btn-primary ' : 'btn-outline-primary' }} btn-save" data-thread-id="{{ $thread->id }}"
                            data-thread-slug="{{ $thread->slug }}"
                            data-saved="{{ $isSaved ? 'true' : 'false' }}"
                            title="{{ $isSaved ? __('thread.unsave') : __('thread.save') }}">
                        <i class="{{ $isSaved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark' }} me-1"></i>
                        <span class="save-text">{{ $isSaved ? __('thread.bookmarked') : __('thread.bookmark') }}</span>
                        @if($thread->saves_count > 0)
                        <span class="badge bg-danger text-dark ms-1 save-count">{{ number_format($thread->saves_count) }}</span>
                        @endif
                    </button>
                    @else
                    <button type="button"  class="btn btn-sm btn-outline-primary btn-save ms-2" onclick="showLoginModal()" title="{{ __('thread.login_to_save') }}">
                        <i class="far fa-bookmark me-1"></i>
                        <span class="save-text">{{ __('thread.bookmark') }}</span>
                        @if($thread->saves_count > 0)
                        <span class="badge bg-light text-dark ms-1 save-count">{{ number_format($thread->saves_count) }}</span>
                        @endif
                    </button>
                    @endauth
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
                @if ($thread->featured_image)
                <img src="{{ $thread->featured_image }}" alt="{{ $thread->title }}" class="img-fluid" style="max-height: 400px; width: 100%; object-fit: cover;" >
                @endif
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
                    <div class="col-md-3 col-sm-4 col-6">
                        <a href="{{ asset($media->file_path) }}" data-fancybox="thread-images" data-caption="{{ $media->file_name }}">
                            <img src="{{ asset($media->file_path) }}" alt="{{ $media->file_name }}"class="img-fluid rounded shadow-sm" style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;" onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
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
                            data-thread-slug="{{ $thread->slug }}"
                            data-liked="{{ $isLiked ? 'true' : 'false' }}"
                            title="{{ $isLiked ? __('thread.unlike') : __('thread.like') }}">
                        <i class="fas fa-thumbs-up me-1"></i>
                        {{ __('thread.like') }}
                        <span class="badge bg-danger like-count">{{ $thread->likes_count ?? 0 }}</span>
                    </button>
                    @else
                    <button type="button" class="btn btn-sm btn_meta btn-like" onclick="showLoginModal()" title="{{ __('thread.login_to_like') }}">
                        <i class="fas fa-thumbs-up"></i>
                        {{ __('thread.like') }}
                        <span class="badge bg-secondary like-count">{{ $thread->likes_count ?? 0 }}</span>
                    </button>
                    @endauth
                </div>

                <div class="d-flex align-items-center">
                    <!-- Share Button -->
                    <div class="dropdown dropdown-button d-inline">
                        <button class="btn btn-sm no-border dropdown-toggle btn-share" type="button"
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
                    <a href="#reply-form" class="btn btn-sm  ms-2 btn-traloi">
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

        <!-- Reply Form (Hidden - Using Inline Reply Instead) -->

        <div class="card_coment_thread" id="reply-form">
            @auth
            <h4 class="title_page_sub"><i class="fas fa-comment text-primary"></i> {{ __('thread.post_reply') }}</h4>
            <div class="card_coment_thread_body">
                <form action="{{ route('threads.comments.store', $thread) }}" method="POST" enctype="multipart/form-data"
                    id="reply-form-element">
                    @csrf
                    <input type="hidden" name="parent_id" id="parent_id" value="">
                    <div class="d-flex gap-3">
                        <div class="avatar_user">
                            <img src="{{ auth()->user()->getAvatarUrl() }}"  class="user-avatar" alt="Avatar">
                        </div>

                        <div class="flex-grow-1">
                            <div class="mb-3">
                                <x-tinymce-editor
                                    name="content"
                                    id="content"
                                    :value="old('content')"
                                    :placeholder="__('thread.reply_content_placeholder')"
                                    context="comment"
                                    :height="200"
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
                            <x-comment-image-upload
                                :max-files="5"
                                max-size="5MB"
                                context="thread"
                                upload-text="{{ __('thread.add_new_images') }}"
                                accept-description="{{ __('thread.images_only') }}"
                                :show-preview="true"
                                :compact="true"
                            />
                            @error('images.*')
                            <div class="text-danger small mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                            <div class="d-flex justify-content-end align-items-center mt-3">
                                <button type="submit" class="btn btn-sm btn-primary" id="submit-reply-btn">
                                    <i class="fas fa-paper-plane me-1"></i> {{ __('thread.send_reply') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
             @else
            <div class="alert alert-info">
                {!! __('thread.login_required', [
                    'login' => '<a href="' . route('login') . '">' . __('thread.login') . '</a>',
                    'register' => '<a href="' . route('register') . '">' . __('thread.register') . '</a>'
                ]) !!}
            </div>
            @endauth
            <hr>
            <!-- Comments Section -->
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
                                class="btn btn-sm sort-btn {{ request('sort') == 'oldest' ? 'btn-primary' : 'btn-outline-primary' }}"
                                data-sort="oldest"
                                data-thread-id="{{ $thread->id }}">
                            {{ __('thread.sort_oldest') }}
                        </button>
                        <button type="button"
                                class="btn btn-sm sort-btn {{ request('sort', 'newest') == 'newest' ? 'btn-primary' : 'btn-outline-primary' }}"
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
                                <img src="{{ $comment->user->getAvatarUrl() }}" alt="{{ $comment->user->name }}" class="rounded-circle me-2" width="40" height="40">
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
                                <div class="comment_item_content" id="comment-content-{{ $comment->id }}">
                                    {!! $comment->content !!}
                                </div>

                                 @auth
                                <!-- Inline Edit Form (Hidden by default) -->
                                <div class="inline-edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                    <form class="comment-edit-form" data-comment-id="{{ $comment->id }}">
                                        @csrf
                                        <input type="hidden" name="_method" value="PUT">

                                        <!-- Rich Text Editor Placeholder -->
                                        <div class="mb-3">
                                            <textarea
                                                name="content"
                                                id="edit-content-{{ $comment->id }}"
                                                class="form-control"
                                                rows="6"
                                                style="display: none;"
                                            >{{ $comment->content }}</textarea>
                                            <div id="tinymce-loading-{{ $comment->id }}" class="text-center p-3" style="display: none;">
                                                <div class="spinner-border spinner-border-sm" role="status">
                                                    <span class="visually-hidden">Đang tải...</span>
                                                </div>
                                                <span class="ms-2">Đang khởi tạo editor...</span>
                                            </div>
                                        </div>
                                        <!-- New Image Upload -->
                                        <div class="mb-3">
                                            <x-comment-image-upload
                                                :max-files="5"
                                                max-size="5MB"
                                                context="comment"
                                                upload-text="{{ __('thread.add_new_images') }}"
                                                accept-description="{{ __('thread.images_only') }}"
                                                :show-preview="true"
                                                :compact="true"
                                            />
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm save-comment-btn">
                                                <i class="fas fa-save me-1"></i>
                                                {{ __('thread.save_changes') }}
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn"
                                                    data-comment-id="{{ $comment->id }}">
                                                <i class="fas fa-times me-1"></i>
                                                {{ __('common.cancel') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @endauth

                                @if($comment->has_media && isset($comment->attachments) && count($comment->attachments) > 0)
                                <div class="comment-attachments mt-3">
                                    <div class="row g-2 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5">
                                        @foreach($comment->attachments as $attachment)
                                        <div class="col">
                                            <div class="comment-image-wrapper position-relative">
                                                <a href="{{ $attachment->url }}" class="d-block" data-fancybox="comment-{{ $comment->id }}-images" data-caption="{{ $attachment->file_name }}">
                                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}" class="img-fluid rounded">
                                                </a>
                                                @can('update', $comment)
                                                <button type="button" class="btn btn-danger btn-sm delete-image-btn position-absolute"
                                                        data-image-id="{{ $attachment->id }}"
                                                        data-comment-id="{{ $comment->id }}"
                                                        title="Xóa hình ảnh">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                <div class="comment_item_meta d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="btn btn-sm btn_meta text-muted"><i class="fa-regular fa-clock me-1"></i> {{ $comment->created_at->diffForHumans() }}</span>
                                        <!-- Like Button -->
                                        @auth
                                        <button type="button" class="btn text-muted btn-sm no-border btn_meta comment-like-btn {{ $comment->isLikedBy(auth()->user()) ? 'active' : '' }}"
                                                data-comment-id="{{ $comment->id }}"
                                                data-liked="{{ $comment->isLikedBy(auth()->user()) ? 'true' : 'false' }}"
                                                title="{{ $comment->isLikedBy(auth()->user()) ? __('thread.unlike') : __('thread.like') }}">
                                            <i class="fas fa-thumbs-up me-1"></i> <span class="comment-like-count-{{ $comment->id }} me-1">{{ $comment->like_count }}</span> <span class="text">{{ __('thread.like') }}</span>
                                        </button>
                                        @else
                                        <button type="button" class="btn text-muted btn-sm no-border btn_meta comment-like-btn" onclick="showLoginModal()" title="{{ __('thread.login_to_like') }}">
                                            <i class="fas fa-thumbs-up me-1"></i> <span class="comment-like-count-{{ $comment->id }} me-1">{{ $comment->like_count }}</span> <span class="text">{{ __('thread.like') }}</span>
                                        </button>
                                        @endauth
                                    </div>
                                    <div class="d-flex">
                                        <!-- Quote Button -->
                                        <button class="btn btn-sm text-muted no-border btn_meta quote-button" data-comment-id="{{ $comment->id }}" data-user-name="{{ $comment->user->name }}">
                                            <i class="fa-solid fa-quote-left me-1"></i> <span class="text">{{ __('thread.quote') }}</span>
                                        </button>

                                        <!-- Reply Button -->
                                        <button class="btn text-muted btn-sm no-border btn_meta reply-button ms-2"  data-parent-id="{{ $comment->id }}">
                                            <i class="fas fa-reply me-1"></i> <span class="text">{{ __('thread.reply') }}</span>
                                        </button>

                                        <!-- Edit/Delete Buttons (if owner) -->
                                        @can('update', $comment)
                                        <button class="btn text-warning btn-sm no-border btn_meta inline-edit-comment-btn"  data-comment-id="{{ $comment->id }}" title="{{ __('thread.edit_comment') }}">
                                            <i class="fas fa-edit me-1"></i> <span class="text">{{ __('thread.edit') }}</span>
                                        </button>
                                        <button type="button" class="btn text-danger btn-sm no-border btn_meta delete-comment-btn"  data-comment-id="{{ $comment->id }}"  data-comment-type="comment"  title="{{ __('thread.delete_comment') }}">
                                            <i class="fas fa-trash me-1"></i> <span class="text">{{ __('thread.delete') }}</span>
                                        </button>
                                        @endcan
                                    </div>
                                </div>

                                <!-- Inline Reply Form -->
                                <x-inline-reply-form
                                    :comment-id="$comment->id"
                                    :thread-id="$thread->id"
                                    :parent-user="$comment->user->name"
                                    :parent-content="$comment->content"
                                />

                                <div class="comment_sub">
                                    @if(isset($comment->replies) && count($comment->replies) > 0)
                                    @foreach($comment->replies as $reply)
                                    <div class="comment_item mb-3">
                                        <div class="d-flex">
                                            <div class="comment_item_avatar">
                                                <img src="{{ $reply->user->getAvatarUrl() }}" alt="{{ $reply->user->name }}" class="rounded-circle me-2" width="30" height="30">
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
                                                <div class="comment_item_content" id="comment-content-{{ $reply->id }}">
                                                    {!! $reply->content !!}
                                                </div>

                                                <!-- Inline Edit Form for Reply (Hidden by default) -->
                                                <div class="inline-edit-form" id="edit-form-{{ $reply->id }}" style="display: none;">
                                                    <form class="comment-edit-form" data-comment-id="{{ $reply->id }}">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="PUT">

                                                        <!-- Rich Text Editor Placeholder -->
                                                        <div class="mb-3">
                                                            <textarea
                                                                name="content"
                                                                id="edit-content-{{ $reply->id }}"
                                                                class="form-control"
                                                                rows="4"
                                                                style="display: none;"
                                                            >{{ $reply->content }}</textarea>
                                                            <div id="tinymce-loading-{{ $reply->id }}" class="text-center p-3" style="display: none;">
                                                                <div class="spinner-border spinner-border-sm" role="status">
                                                                    <span class="visually-hidden">Đang tải...</span>
                                                                </div>
                                                                <span class="ms-2">Đang khởi tạo editor...</span>
                                                            </div>
                                                        </div>



                                                        <!-- New Image Upload -->
                                                        <div class="mb-3">
                                                            <x-comment-image-upload
                                                                :max-files="3"
                                                                max-size="5MB"
                                                                context="reply"
                                                                upload-text="{{ __('thread.add_new_images') }}"
                                                                accept-description="{{ __('thread.images_only') }}"
                                                                :show-preview="true"
                                                                :compact="true"
                                                            />
                                                        </div>

                                                        <!-- Form Actions -->
                                                        <div class="d-flex gap-2">
                                                            <button type="submit" class="btn btn-primary btn-sm save-comment-btn">
                                                                <i class="fas fa-save me-1"></i>
                                                                {{ __('thread.save_changes') }}
                                                            </button>
                                                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn"
                                                                    data-comment-id="{{ $reply->id }}">
                                                                <i class="fas fa-times me-1"></i>
                                                                {{ __('common.cancel') }}
                                                            </button>
                                                        </div>
                                                    </form>
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
                                                        <span class="btn text-muted btn-sm no-border btn_meta"><i class="fa-regular fa-clock me-1"></i> {{ $reply->created_at->diffForHumans() }}</span>
                                                        <!-- Like Button -->
                                                        @auth
                                                        <button type="button"
                                                                class="btn text-muted btn-sm no-border btn_meta comment-like-btn {{ $reply->isLikedBy(auth()->user()) ? 'active' : '' }}"
                                                                data-comment-id="{{ $reply->id }}"
                                                                data-liked="{{ $reply->isLikedBy(auth()->user()) ? 'true' : 'false' }}"
                                                                title="{{ $reply->isLikedBy(auth()->user()) ? __('thread.unlike') : __('thread.like') }}">
                                                            <i class="fas fa-thumbs-up me-1"></i> <span class="comment-like-count-{{ $reply->id }} me-1">{{ $reply->like_count }}</span> <span class="text">{{ __('thread.like') }}</span>
                                                        </button>
                                                        @else
                                                        <button type="button"
                                                                class="btn text-muted btn-sm no-border btn_meta  comment-like-btn"
                                                                onclick="showLoginModal()"
                                                                title="{{ __('thread.login_to_like') }}">
                                                            <i class="fas fa-thumbs-up me-1"></i> <span class="comment-like-count-{{ $reply->id }} me-1">{{ $reply->like_count }}</span> <span class="text">{{ __('thread.like') }}</span>
                                                        </button>
                                                        @endauth
                                                    </div>
                                                    <div class="d-flex">
                                                        <!-- Reply Button -->
                                                        <button class="btn text-muted btn-sm no-border btn_meta no-border reply-button"
                                                            data-parent-id="{{ $comment->id }}">
                                                            <i class="fas fa-reply m-1"></i> {{ __('thread.reply') }}
                                                        </button>

                                                        <!-- Edit/Delete Buttons (if owner) -->
                                                        @can('update', $reply)
                                                        <button class="btn text-warning btn-sm no-border btn_meta  inline-edit-comment-btn"
                                                                data-comment-id="{{ $reply->id }}"
                                                                title="{{ __('thread.edit_reply') }}">
                                                            <i class="fas fa-edit me-1"></i> {{ __('thread.edit') }}
                                                        </button>
                                                        <button type="button"
                                                                class="btn text-danger btn-sm no-border btn_meta  delete-comment-btn"
                                                                data-comment-id="{{ $reply->id }}"
                                                                data-comment-type="reply"
                                                                title="{{ __('thread.delete_reply') }}">
                                                            <i class="fas fa-trash me-1"></i> {{ __('thread.delete') }}
                                                        </button>
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
    </div>
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
@auth
<script src="{{ asset_versioned('js/frontend/page/threads.js') }}"></script>
@endauth
<script>
@auth
// Initialize event handlers when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeEventHandlers();
    initializeRealTimeComments();

    // Auto-scroll to new comment if specified
    @if(session('scroll_to_comment'))
        const commentId = {{ session('scroll_to_comment') }};
        const commentElement = document.getElementById('comment-' + commentId);
        if (commentElement) {
            setTimeout(() => {
                commentElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                // Add highlight effect
                commentElement.classList.add('highlight-comment');
                setTimeout(() => {
                    commentElement.classList.remove('highlight-comment');
                }, 3000);
            }, 500);
        }
    @endif
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

        // Debug: Log FormData contents
        console.log('FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

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


                // Clear file upload component
                const fileUploadComponent = document.querySelector('.file-upload-component');
                if (fileUploadComponent && window.FileUploadComponent) {
                    const componentInstance = window.FileUploadComponent.getInstance(fileUploadComponent);
                    if (componentInstance) {
                        componentInstance.clearFiles();
                    }
                }

                // Backup: Clear dropzone manually if FileUploadComponent doesn't work
                const dropzoneElement = document.querySelector('.dropzone');
                if (dropzoneElement && window.Dropzone) {
                    const dropzoneInstance = window.Dropzone.forElement(dropzoneElement);
                    if (dropzoneInstance) {
                        dropzoneInstance.removeAllFiles(true);
                    }
                }

                // Also clear any preview images
                const previewContainer = document.querySelector('.preview-grid');
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                }

                // Add new comment to DOM instead of reloading page
                if (data.comment) {
                    addNewCommentToDOM(data.comment);

                    // Update comment count
                    updateCommentCount(1);

                    // Scroll to new comment
                    setTimeout(() => {
                        const newComment = document.getElementById(`comment-${data.comment.id}`);
                        if (newComment) {
                            newComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            // Add highlight effect
                            newComment.classList.add('highlight-new');
                            setTimeout(() => {
                                newComment.classList.remove('highlight-new');
                            }, 3000);
                        }
                    }, 100);
                }
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

    // Handle inline reply buttons
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const parentId = this.getAttribute('data-parent-id');

            // Hide all other inline reply forms
            document.querySelectorAll('.inline-reply-form').forEach(form => {
                form.style.display = 'none';
            });

            // Show the inline reply form for this comment
            const inlineReplyForm = document.getElementById(`inline-reply-${parentId}`);
            if (inlineReplyForm) {
                inlineReplyForm.style.display = 'block';

                // Focus TinyMCE editor
                setTimeout(() => {
                    const editorId = `inline-reply-content-${parentId}`;
                    if (tinymce.get(editorId)) {
                        tinymce.get(editorId).focus();
                    }
                }, 100);
            }
        });
    });

    // Handle cancel inline reply
    document.querySelectorAll('.cancel-inline-reply').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const inlineReplyForm = document.getElementById(`inline-reply-${commentId}`);
            if (inlineReplyForm) {
                inlineReplyForm.style.display = 'none';

                // Clear the form
                const form = inlineReplyForm.querySelector('.inline-reply-form-element');
                if (form) {
                    const editorId = `inline-reply-content-${commentId}`;
                    if (tinymce.get(editorId)) {
                        tinymce.get(editorId).setContent('');
                    }
                }
            }
        });
    });

    // Handle inline reply form submission
    document.querySelectorAll('.inline-reply-form-element').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const commentId = this.getAttribute('data-comment-id');
            const threadId = this.getAttribute('data-thread-id');
            const submitBtn = this.querySelector('.submit-inline-reply');
            const editorId = `inline-reply-content-${commentId}`;

            // Get content from TinyMCE
            let content = '';
            if (tinymce.get(editorId)) {
                content = tinymce.get(editorId).getContent();
            }

            // Validate content
            if (!content || content.trim() === '' || content.trim() === '<p></p>') {
                showToast('{{ __("thread.reply_content_required") }}', 'error');
                return;
            }

            // Disable submit button
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __("thread.sending") }}';

            // Prepare form data
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('content', content);
            formData.append('parent_id', commentId);

            // Add uploaded images if any
            const uploadComponent = this.querySelector('[data-upload-component]');
            if (uploadComponent) {
                const uploadedImages = uploadComponent.getAttribute('data-uploaded-images');
                if (uploadedImages) {
                    const images = JSON.parse(uploadedImages);
                    images.forEach((image, index) => {
                        formData.append(`uploaded_images[${index}]`, image);
                    });
                }
            }

            // Submit via AJAX
            fetch(`/threads/${threadId}/comments`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showToast('{{ __("thread.reply_posted_successfully") }}', 'success');

                    // Hide the inline reply form
                    const inlineReplyForm = document.getElementById(`inline-reply-${commentId}`);
                    if (inlineReplyForm) {
                        inlineReplyForm.style.display = 'none';
                    }

                    // Clear the form
                    if (tinymce.get(editorId)) {
                        tinymce.get(editorId).setContent('');
                    }

                    // Add the new reply to the DOM
                    if (data.comment) {
                        addNewReplyToDOM(data.comment, commentId);
                    }

                    // Update comment count if provided
                    if (data.comment_count) {
                        updateCommentCount(data.comment_count);
                    }
                } else {
                    showToast(data.message || '{{ __("thread.reply_posting_error") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Reply submission error:', error);
                showToast('{{ __("thread.reply_posting_error") }}', 'error');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    });

    // Handle quote buttons using event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quote-button')) {
            const button = e.target.closest('.quote-button');
            const commentId = button.getAttribute('data-comment-id');
            const userName = button.getAttribute('data-user-name');

            // Get comment content from DOM
            const commentContentElement = document.getElementById(`comment-content-${commentId}`);
            const commentContent = commentContentElement ? commentContentElement.innerHTML : '';

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
        }
    });

    // Handle inline edit comment buttons
    document.querySelectorAll('.inline-edit-comment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');

            // Get content directly from DOM element
            const contentElement = document.querySelector(`#comment-content-${commentId}`);
            if (!contentElement) {
                console.error('Content element not found for comment:', commentId);
                showNotification('{{ __("thread.content_not_found") }}', 'error');
                return;
            }

            const content = contentElement.innerHTML;
            showInlineEditForm(commentId, content);
        });
    });

    // Function to show inline edit form
    function showInlineEditForm(commentId, content) {
        // Hide comment content and show edit form
        const commentContent = document.getElementById(`comment-content-${commentId}`);
        const editForm = document.getElementById(`edit-form-${commentId}`);

        if (commentContent && editForm) {
            // Store original content for cancel functionality
            editForm.setAttribute('data-original-content', content);

            commentContent.style.display = 'none';
            editForm.style.display = 'block';

            // Initialize TinyMCE dynamically for this specific editor
            const editorId = `edit-content-${commentId}`;
            const loadingDiv = document.getElementById(`tinymce-loading-${commentId}`);
            const textarea = document.getElementById(editorId);

            // Show loading indicator
            if (loadingDiv) {
                loadingDiv.style.display = 'block';
            }

            // Wait a bit for the form to be visible, then initialize TinyMCE
            setTimeout(() => {
                if (!tinymce.get(editorId)) {
                    // Initialize TinyMCE for this editor
                    initializeTinyMCEEditor(editorId, 'comment', {
                        height: commentContent.closest('.comment_item_body.sub') ? 150 : 200,
                        placeholder: 'Chỉnh sửa nội dung...',
                        required: true,
                        setup: function(editor) {
                            // Hide loading indicator when editor is ready
                            editor.on('init', function() {
                                if (loadingDiv) {
                                    loadingDiv.style.display = 'none';
                                }
                                editor.setContent(content);
                                editor.focus();
                            });

                            // Add Escape key handler
                            editor.on('keydown', function(e) {
                                if (e.keyCode === 27) { // Escape key
                                    e.preventDefault();
                                    hideInlineEditForm(commentId);
                                }
                            });
                        }
                    });
                } else {
                    // Editor already exists, just set content
                    if (loadingDiv) {
                        loadingDiv.style.display = 'none';
                    }
                    tinymce.get(editorId).setContent(content);
                    tinymce.get(editorId).focus();
                }
            }, 100);
        }
    }

    // Handle cancel edit buttons and click outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('cancel-edit-btn') || e.target.closest('.cancel-edit-btn')) {
            const button = e.target.classList.contains('cancel-edit-btn') ? e.target : e.target.closest('.cancel-edit-btn');
            const commentId = button.getAttribute('data-comment-id');
            hideInlineEditForm(commentId);
        }

        // Handle click outside edit form to cancel
        const activeEditForms = document.querySelectorAll('[id^="edit-form-"]:not([style*="display: none"])');
        activeEditForms.forEach(form => {
            if (!form.contains(e.target) && !e.target.closest('.inline-edit-comment-btn')) {
                const commentId = form.id.replace('edit-form-', '');
                hideInlineEditForm(commentId);
            }
        });
    });

    // Global Escape key handler for inline edit forms
    document.addEventListener('keydown', function(e) {
        if (e.keyCode === 27) { // Escape key
            const activeEditForms = document.querySelectorAll('[id^="edit-form-"]:not([style*="display: none"])');
            activeEditForms.forEach(form => {
                const commentId = form.id.replace('edit-form-', '');
                hideInlineEditForm(commentId);
            });
        }
    });



    // Function to hide inline edit form
    function hideInlineEditForm(commentId) {
        const commentContent = document.getElementById(`comment-content-${commentId}`);
        const editForm = document.getElementById(`edit-form-${commentId}`);

        if (commentContent && editForm) {
            commentContent.style.display = 'block';
            editForm.style.display = 'none';



            // Remove TinyMCE instance to prevent memory leaks
            const editorId = `edit-content-${commentId}`;
            if (tinymce.get(editorId)) {
                tinymce.get(editorId).remove();
            }

            // Clear any uploaded files in the upload component
            const uploadComponent = editForm.querySelector('.advanced-file-upload');
            if (uploadComponent) {
                // Clear file input
                const fileInput = uploadComponent.querySelector('input[type="file"]');
                if (fileInput) {
                    fileInput.value = '';
                }

                // Clear preview area
                const previewArea = uploadComponent.querySelector('.file-preview-area');
                if (previewArea) {
                    previewArea.innerHTML = '';
                }

                // Reset upload text
                const uploadText = uploadComponent.querySelector('.upload-text');
                if (uploadText) {
                    uploadText.style.display = 'block';
                }
            }
        }
    }

    // Handle comment edit form submissions
    document.addEventListener('submit', async function(e) {
        if (e.target.classList.contains('comment-edit-form')) {
            e.preventDefault();

            const form = e.target;
            const commentId = form.getAttribute('data-comment-id');
            const editorId = `edit-content-${commentId}`;

            // Get content from TinyMCE
            let content = '';
            if (tinymce.get(editorId)) {
                content = tinymce.get(editorId).getContent();
            }

            if (!content.trim()) {
                alert('{{ __("thread.content_required") }}');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('.save-comment-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> {{ __("thread.saving") }}';
            submitBtn.disabled = true;

            // Prepare FormData for POST request with method spoofing
            const updateFormData = new FormData();
            updateFormData.append('content', content);
            updateFormData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            updateFormData.append('_method', 'PUT');

            // Handle image uploads from comment-image-upload component
            const uploadComponent = form.querySelector('.comment-image-upload');
            let uploadedImages = [];

            if (uploadComponent && uploadComponent.commentImageUpload && uploadComponent.commentImageUpload.hasFiles()) {
                try {
                    // Upload images first
                    uploadedImages = await uploadComponent.commentImageUpload.uploadFiles();

                    // Add uploaded image URLs to form data
                    uploadedImages.forEach(image => {
                        updateFormData.append('uploaded_images[]', image.url);
                    });
                } catch (error) {
                    console.error('Image upload failed:', error);
                    // Continue with comment update even if image upload fails
                }
            }

            // Function to make API request with CSRF token refresh on 419 error
            const makeUpdateRequest = (retryCount = 0) => {
                return fetch(`/api/comments/${commentId}`, {
                    method: 'POST',
                    body: updateFormData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Handle 419 CSRF token mismatch
                    if (response.status === 419 && retryCount === 0) {
                        console.log('CSRF token expired, refreshing page...');
                        // For CSRF token mismatch, refresh the page to get new token
                        showNotification('Phiên làm việc đã hết hạn. Đang tải lại trang...', 'info');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                        return Promise.reject(new Error('CSRF token expired, refreshing page'));
                    }

                    return response.json();
                });
            };

            // Submit via AJAX with CSRF error handling
            makeUpdateRequest()
            .then(data => {
                if (data.success) {
                    // Update comment content in DOM
                    updateCommentInDOM(commentId, data.comment);

                    // Hide edit form
                    hideInlineEditForm(commentId);

                    // Show success message
                    showNotification('{{ __("thread.comment_updated_successfully") }}', 'success');
                } else {
                    throw new Error(data.message || '{{ __("thread.update_failed") }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (!error.message.includes('CSRF token expired')) {
                    showNotification(error.message || '{{ __("thread.update_failed") }}', 'error');
                }
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
    });



    // Function to update comment in DOM after successful edit
    function updateCommentInDOM(commentId, commentData) {
        const commentContent = document.getElementById(`comment-content-${commentId}`);
        if (commentContent) {
            // Update content
            commentContent.innerHTML = commentData.content;

            // Update attachments if provided
            if (commentData.attachments) {
                // Find existing attachments container
                let attachmentsContainer = commentContent.parentNode.querySelector('.comment-attachments, .reply-attachments');

                if (commentData.attachments.length > 0) {
                    // Create or update attachments container
                    if (!attachmentsContainer) {
                        attachmentsContainer = document.createElement('div');
                        attachmentsContainer.className = commentContent.closest('.comment_item_body.sub') ? 'reply-attachments mt-2' : 'comment-attachments mt-3';
                        commentContent.parentNode.insertBefore(attachmentsContainer, commentContent.nextSibling);
                    }

                    // Build attachments HTML
                    const attachmentsHTML = `
                        <div class="row g-2">
                            ${commentData.attachments.map(attachment => `
                                <div class="col-md-3 col-sm-4 col-6">
                                    <a href="${attachment.url}" class="d-block"
                                       data-fancybox="comment-${commentId}-images"
                                       data-caption="${attachment.file_name}">
                                        <img src="${attachment.url}" alt="${attachment.file_name}" class="img-fluid rounded">
                                    </a>
                                </div>
                            `).join('')}
                        </div>
                    `;

                    attachmentsContainer.innerHTML = attachmentsHTML;
                } else if (attachmentsContainer) {
                    // Remove attachments container if no attachments
                    attachmentsContainer.remove();
                }
            }
        }
    }

    // Function to show notification
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
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


    // Function to clear uploaded images
    function clearUploadedImages() {
        const imagePreview = document.getElementById('image-preview');
        if (imagePreview) {
            imagePreview.innerHTML = '';
        }

        // Reset file input
        const fileInput = document.getElementById('images');
        if (fileInput) {
            fileInput.value = '';
        }

        // Clear deleted images list
        const deletedImages = document.getElementById('deleted-images');
        if (deletedImages) {
            deletedImages.remove();
        }
    }
}

// Function to initialize delete image buttons
function initializeDeleteImageButtons() {
    document.querySelectorAll('.delete-image-btn').forEach(button => {
        // Remove existing listeners to avoid duplicates
        button.replaceWith(button.cloneNode(true));
    });

    document.querySelectorAll('.delete-image-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const imageId = this.getAttribute('data-image-id');
            const commentId = this.getAttribute('data-comment-id');
            const imageWrapper = this.closest('.comment-image-wrapper').parentElement;
            const deleteButton = this;

            // Use SweetAlert2 for confirmation
            window.showDeleteConfirm('hình ảnh này').then((result) => {
                if (result.isConfirmed) {
                    // Disable button during request
                    deleteButton.disabled = true;
                    deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    // Send delete request
                    fetch(`/comments/${commentId}/images/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            window.showSuccess('Thành công!', 'Đã xóa hình ảnh thành công.');

                            // Remove image from DOM with animation
                            imageWrapper.style.transition = 'opacity 0.3s ease';
                            imageWrapper.style.opacity = '0';
                            setTimeout(() => {
                                imageWrapper.remove();

                                // Check if no more images left in this comment
                                const attachmentsContainer = document.querySelector(`#comment-${commentId} .comment-attachments`);
                                if (attachmentsContainer && attachmentsContainer.querySelectorAll('.col').length === 0) {
                                    attachmentsContainer.remove();
                                }
                            }, 300);
                        } else {
                            window.showError('Lỗi!', data.message || 'Có lỗi xảy ra khi xóa hình ảnh.');
                            deleteButton.disabled = false;
                            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.showError('Lỗi!', 'Có lỗi xảy ra khi xóa hình ảnh.');
                        deleteButton.disabled = false;
                        deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                    });
                }
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
            const threadSlug = this.dataset.threadSlug;
            const isLiked = this.dataset.liked === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("thread.processing") }}';

            // Make AJAX request - use slug for route model binding
            fetch(`/threads/${threadSlug}/like`, {
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
            const threadSlug = this.dataset.threadSlug;
            const isSaved = this.dataset.saved === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("thread.processing") }}';

            // Make AJAX request - use slug for route model binding
            fetch(`/threads/${threadSlug}/save`, {
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

                    // Update button appearance - re-query elements to avoid stale references
                    const button = document.querySelector('.btn-save');
                    if (!button) {
                        console.error('Button not found after response');
                        return;
                    }

                    const icon = button.querySelector('i');
                    const text = button.querySelector('.save-text');
                    let countBadge = button.querySelector('.save-count');

                    if (icon && text) {
                        if (newSaved) {
                            button.classList.add('active');
                            icon.className = 'fas fa-bookmark';
                            text.textContent = '{{ __("thread.bookmarked") }}';
                            button.title = '{{ __("thread.unsave") }}';
                        } else {
                            button.classList.remove('active');
                            icon.className = 'far fa-bookmark';
                            text.textContent = '{{ __("thread.bookmark") }}';
                            button.title = '{{ __("thread.save") }}';
                        }

                        // Update save count badge
                        if (data.saves_count !== undefined) {
                            if (data.saves_count > 0) {
                                if (!countBadge) {
                                    countBadge = document.createElement('span');
                                    countBadge.className = 'badge bg-light text-dark ms-1 save-count';
                                    button.appendChild(countBadge);
                                }
                                countBadge.textContent = new Intl.NumberFormat().format(data.saves_count);
                            } else {
                                if (countBadge) {
                                    countBadge.remove();
                                }
                            }
                        }
                    } else {
                        // Fallback: Force page reload if elements can't be updated
                        console.warn('Could not update button elements, reloading page...');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
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
            //this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("thread.processing") }}';

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
                    const likeCountElement = $('.comment-like-count-'+commentId);
                    if (likeCountElement) {
                        console.log('Like count: ', data.like_count);
                        likeCountElement.text(data.like_count);
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
                //this.innerHTML = originalContent;
            });
        });
    });

    // Handle delete comment button clicks
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.dataset.commentId;
            const commentType = this.dataset.commentType;
            const confirmMessage = commentType === 'reply' ?
                '{{ __("thread.delete_reply_message") }}' :
                '{{ __("thread.delete_comment_message") }}';

            // Use SweetAlert2 for confirmation
            window.showDeleteConfirm(confirmMessage).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                // Proceed with deletion
                const button = this;

                // Disable button during request
                button.disabled = true;
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
                    button.disabled = false;
                    button.innerHTML = originalContent;
                });
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

    // Initialize delete image buttons on page load
    initializeDeleteImageButtons();
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
            //const originalContent = this.innerHTML;
            //this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("thread.processing") }}';

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
                    const likeCountElement = this.querySelector('.comment-like-count-'+commentId);
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

        newButton.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.dataset.commentId;
            const commentType = this.dataset.commentType;
            const confirmMessage = commentType === 'reply' ?
                '{{ __("thread.delete_reply_message") }}' :
                '{{ __("thread.delete_comment_message") }}';

            // Use SweetAlert2 for confirmation
            window.showDeleteConfirm(confirmMessage).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                // Proceed with deletion
                const button = this;

                // Disable button during request
                button.disabled = true;
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
                    button.disabled = false;
                    button.innerHTML = originalContent;
                });
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

// Add new comment to DOM (for main comment form)
function addNewCommentToDOM(comment) {
    const commentsContainer = document.getElementById('comments-container');
    if (!commentsContainer) return;

    // Create new comment HTML
    const commentHtml = createCommentHtml(comment);

    // Add to top of comments list (newest first if sorted by newest)
    const currentSort = new URLSearchParams(window.location.search).get('sort') || 'newest';
    if (currentSort === 'newest') {
        commentsContainer.insertAdjacentHTML('afterbegin', commentHtml);
    } else {
        commentsContainer.insertAdjacentHTML('beforeend', commentHtml);
    }

    // Initialize event handlers for the new comment
    const newComment = document.getElementById(`comment-${comment.id}`);
    if (newComment) {
        initializeCommentInteractions();
    }
}


// Update comment count in UI
function updateCommentCount(change) {
    // Update in thread stats
    const commentCountElements = document.querySelectorAll('.comment-count, [data-comment-count]');
    commentCountElements.forEach(element => {
        const currentCount = parseInt(element.textContent) || 0;
        const newCount = Math.max(0, currentCount + change);
        element.textContent = newCount;
    });

    // Update in comments header
    const commentsHeader = document.querySelector('#comments-container').previousElementSibling;
    if (commentsHeader) {
        const headerText = commentsHeader.textContent;
        const match = headerText.match(/(\d+)/);
        if (match) {
            const currentCount = parseInt(match[1]);
            const newCount = Math.max(0, currentCount + change);
            commentsHeader.innerHTML = commentsHeader.innerHTML.replace(/\d+/, newCount);
        }
    }
}

function createCommentHtml(comment) {
    const timeAgo = formatTimeAgo(new Date(comment.created_at));
    const isReply = comment.parent_id ? true : false;
    const avatarSize = isReply ? 30 : 40;
    const currentUserId = {{ Auth::id() ?? 'null' }};
    const isAuthenticated = currentUserId !== null;

    // Format join date to match Blade template
    const joinDate = new Date(comment.user.created_at || Date.now()).toLocaleDateString('vi-VN', {
        month: 'short',
        year: 'numeric'
    });

    return `
        <div class="comment_item mb-3" id="comment-${comment.id}">
            <div class="d-flex">
                <div class="comment_item_avatar">
                    <img src="{{ auth()->user()->getAvatarUrl() }}" alt="${comment.user.name}"
                         class="rounded-circle me-2" width="${avatarSize}" height="${avatarSize}">
                </div>
                <div class="comment_item_body ${isReply ? 'sub' : ''}">
                    <div class="comment_item_user">
                        <a href="/users/${comment.user.username || comment.user.id}" class="fw-bold text-decoration-none">
                            ${comment.user.name}
                        </a>
                        <div class="text-muted small">
                            <span>${comment.user.comments_count || 0} {{ __('thread.comments') }}</span> ·
                            <span>{{ __('thread.joined') }} ${joinDate}</span>
                        </div>
                    </div>
                    <div class="comment_item_content" id="comment-content-${comment.id}">
                        ${comment.content}
                    </div>
                    ${comment.attachments && comment.attachments.length > 0 ? `
                    <div class="comment-attachments mt-3">
                        <div class="row g-2 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5">
                            ${comment.attachments.map(attachment => `
                                <div class="col">
                                    <div class="comment-image-wrapper position-relative">
                                        <a href="${attachment.url}" class="d-block" data-fancybox="comment-${comment.id}-images" data-caption="${attachment.file_name}">
                                            <img src="${attachment.url}" alt="${attachment.file_name}" class="img-fluid rounded">
                                        </a>
                                        ${comment.user.id === currentUserId ? `
                                        <button type="button" class="btn btn-danger btn-sm delete-image-btn position-absolute"
                                                data-image-id="${attachment.id}"
                                                data-comment-id="${comment.id}"
                                                style="top: 5px; right: 5px; padding: 2px 6px; font-size: 10px;"
                                                title="{{ __('thread.delete_image') }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        ` : ''}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}
                    <div class="comment_item_meta d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="btn btn-sm btn_meta text-muted"><i class="fa-regular fa-clock me-1"></i> ${timeAgo}</span>
                            ${isAuthenticated ? `
                            <button type="button" class="btn text-muted btn-sm no-border btn_meta comment-like-btn"
                                    data-comment-id="${comment.id}"
                                    data-liked="false"
                                    title="{{ __('thread.like') }}">
                                <i class="fas fa-thumbs-up me-1"></i> <span class="comment-like-count-${comment.id} me-1">${comment.like_count || 0}</span> <span class="text">{{ __('thread.like') }}</span>
                            </button>
                            ` : `
                            <button type="button" class="btn text-muted btn-sm no-border btn_meta comment-like-btn"
                                    onclick="showLoginModal()"
                                    title="{{ __('thread.login_to_like') }}">
                                <i class="fas fa-thumbs-up me-1"></i> <span class="comment-like-count-${comment.id} me-1">${comment.like_count || 0}</span> <span class="text">{{ __('thread.like') }}</span>
                            </button>
                            `}
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-sm text-muted no-border btn_meta quote-button" data-comment-id="${comment.id}" data-user-name="${comment.user.name}">
                                <i class="fa-solid fa-quote-left me-1"></i> <span class="text">{{ __('thread.quote') }}</span>
                            </button>
                            <button class="btn text-muted btn-sm no-border btn_meta reply-button ms-2" data-parent-id="${comment.id}">
                                <i class="fas fa-reply me-1"></i> <span class="text">{{ __('thread.reply') }}</span>
                            </button>
                            ${comment.user.id === currentUserId ? `
                            <button class="btn text-warning btn-sm no-border btn_meta inline-edit-comment-btn" data-comment-id="${comment.id}" title="{{ __('thread.edit_comment') }}">
                                <i class="fas fa-edit me-1"></i> <span class="text">{{ __('thread.edit') }}</span>
                            </button>
                            <button type="button" class="btn text-danger btn-sm no-border btn_meta delete-comment-btn" data-comment-id="${comment.id}" data-comment-type="comment" title="{{ __('thread.delete_comment') }}">
                                <i class="fas fa-trash me-1"></i> <span class="text">{{ __('thread.delete') }}</span>
                            </button>
                            ` : ''}
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

// Add new reply to DOM after successful submission
function addNewReplyToDOM(comment, parentCommentId) {
    const parentComment = document.querySelector(`#comment-${parentCommentId}`);
    if (!parentComment) return;

    const repliesContainer = parentComment.querySelector('.comment_sub');
    if (!repliesContainer) return;

    // Create reply HTML
    const replyHtml = createReplyHtml(comment);

    // Add to replies container
    repliesContainer.insertAdjacentHTML('beforeend', replyHtml);

    // Initialize event handlers for the new reply
    initializeNewReplyHandlers(comment.id);

    // Scroll to new reply
    setTimeout(() => {
        const newReply = document.querySelector(`#comment-${comment.id}`);
        if (newReply) {
            newReply.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 100);
}

// Create HTML for new reply
function createReplyHtml(comment) {
    const timeAgo = formatTimeAgo(new Date(comment.created_at));
    const currentUserId = {{ Auth::id() ?? 'null' }};
    const isAuthenticated = currentUserId !== null;
    const canEdit = currentUserId === comment.user.id;

    // Use unified avatar mechanism - avatar_url should be set by controller using getAvatarUrl()
    const avatarUrl = comment.user.avatar_url;

    // Fix user profile URL
    const userProfileUrl = comment.user.username ? `/users/${comment.user.username}` : `/users/${comment.user.id}`;

    // Fix user join date
    const joinDate = comment.user.created_at ?
        new Date(comment.user.created_at).toLocaleDateString('vi-VN', {month: 'short', year: 'numeric'}) :
        'N/A';

    let attachmentsHtml = '';
    if (comment.attachments && comment.attachments.length > 0) {
        attachmentsHtml = `
            <div class="reply-attachments mt-2">
                <div class="row g-2">
                    ${comment.attachments.map(attachment => `
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="${attachment.url}" class="d-block"
                                data-fancybox="reply-${comment.id}-images"
                                data-caption="${attachment.file_name}">
                                <img src="${attachment.url}" alt="${attachment.file_name}"
                                    class="img-fluid rounded">
                            </a>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    const editButtons = canEdit ? `
        <div class="btn-group ms-2">
            <button class="btn btn-sm btn-main inline-edit-comment-btn"
                    data-comment-id="${comment.id}"
                    title="{{ __('thread.edit_reply') }}">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button"
                    class="btn btn-sm btn-outline-danger delete-comment-btn"
                    data-comment-id="${comment.id}"
                    data-comment-type="reply"
                    title="{{ __('thread.delete_reply') }}">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    ` : '';

    return `
        <div class="comment_item mb-3" id="comment-${comment.id}">
            <div class="d-flex">
                <div class="comment_item_avatar">
                    <img src="{{ auth()->user()->getAvatarUrl() }}" alt="${comment.user.name}" class="rounded-circle me-2">
                </div>
                <div class="comment_item_body sub">
                    <div class="comment_item_user">
                        <a href="${userProfileUrl}" class="fw-bold text-decoration-none">
                            ${comment.user.name}
                        </a>
                        <div class="text-muted small">
                            <span>${comment.user.comments_count || 0} {{ __('thread.comments') }}</span> ·
                            <span>{{ __('thread.joined') }} ${joinDate}</span>
                        </div>
                    </div>
                    <div class="comment_item_content" id="comment-content-${comment.id}">
                        ${comment.content}
                    </div>
                    ${attachmentsHtml}
                    <div class="comment_item_meta d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="btn btn-sm btn_meta">
                                <i class="fa-regular fa-clock me-1"></i> ${timeAgo}
                            </span>
                            ${isAuthenticated ? `
                                <button type="button"
                                        class="btn btn-sm btn_meta comment-like-btn"
                                        data-comment-id="${comment.id}"
                                        data-liked="false"
                                        title="{{ __('thread.like') }}">
                                    <i class="fas fa-thumbs-up"></i> <span class="comment-like-count-${comment.id}">${comment.likes_count || 0}</span> {{ __('thread.like') }}
                                </button>
                            ` : `
                                <button type="button"
                                        class="btn btn-sm btn_meta comment-like-btn"
                                        onclick="showLoginModal()"
                                        title="{{ __('thread.login_to_like') }}">
                                    <i class="fas fa-thumbs-up"></i> <span class="comment-like-count-${comment.id}">${comment.likes_count || 0}</span> {{ __('thread.like') }}
                                </button>
                            `}
                        </div>
                        <div>
                            <button class="btn btn-sm btn-main no-border reply-button"
                                data-parent-id="${comment.id}">
                                <i class="fas fa-reply"></i> {{ __('thread.reply') }}
                            </button>
                            ${editButtons}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Initialize comment like button
function initializeCommentLikeButton(likeBtn) {
    if (!likeBtn) return;

    likeBtn.addEventListener('click', function(e) {
        e.preventDefault();

        const commentId = this.getAttribute('data-comment-id');
        const isLiked = this.getAttribute('data-liked') === 'true';
        const likeCountSpan = this.querySelector('.comment-like-count-'+commentId);

        // Disable button during request
        this.disabled = true;

        // Send AJAX request
        fetch(`/comments/${commentId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                this.setAttribute('data-liked', data.liked ? 'true' : 'false');
                if (likeCountSpan) {
                    likeCountSpan.textContent = data.like_count;
                }

                // Update button appearance
                const icon = this.querySelector('i');
                if (data.liked) {
                    icon.classList.remove('fas');
                    icon.classList.add('fas');
                    this.classList.add('liked');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('fas');
                    this.classList.remove('liked');
                }
            }
        })
        .catch(error => {
            console.error('Like error:', error);
        })
        .finally(() => {
            this.disabled = false;
        });
    });
}

// Initialize inline edit button
function initializeInlineEditButton(editBtn) {
    if (!editBtn) return;

    editBtn.addEventListener('click', function() {
        const commentId = this.getAttribute('data-comment-id');
        // Add inline edit functionality here if needed
        console.log('Edit comment:', commentId);
    });
}

// Initialize delete button
function initializeDeleteButton(deleteBtn) {
    if (!deleteBtn) return;

    deleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const commentId = this.dataset.commentId;
        const commentType = this.dataset.commentType;
        const confirmMessage = commentType === 'reply' ?
            '{{ __("thread.delete_reply_message") }}' :
            '{{ __("thread.delete_comment_message") }}';

        // Use SweetAlert2 for confirmation
        window.showDeleteConfirm(confirmMessage).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            // Proceed with deletion
            const button = this;

            // Disable button during request
            button.disabled = true;
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
                    let commentElement = document.querySelector(`#comment-${commentId}`);

                    // If not found by ID, find by closest .comment_item containing this button
                    if (!commentElement) {
                        commentElement = button.closest('.comment_item');
                    }

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
                button.disabled = false;
                button.innerHTML = originalContent;
            });
        });
    });
}

// Initialize event handlers for new reply
function initializeNewReplyHandlers(commentId) {
    const newReply = document.querySelector(`#comment-${commentId}`);
    if (!newReply) return;

    // Initialize like button
    const likeBtn = newReply.querySelector('.comment-like-btn');
    if (likeBtn && !likeBtn.onclick) {
        initializeCommentLikeButton(likeBtn);
    }

    // Initialize reply button
    const replyBtn = newReply.querySelector('.reply-button');
    if (replyBtn) {
        replyBtn.addEventListener('click', function() {
            const parentId = this.getAttribute('data-parent-id');

            // Hide all other inline reply forms
            document.querySelectorAll('.inline-reply-form').forEach(form => {
                form.style.display = 'none';
            });

            // Show the inline reply form for this comment
            const inlineReplyForm = document.getElementById(`inline-reply-${parentId}`);
            if (inlineReplyForm) {
                inlineReplyForm.style.display = 'block';

                // Focus TinyMCE editor
                setTimeout(() => {
                    const editorId = `inline-reply-content-${parentId}`;
                    if (tinymce.get(editorId)) {
                        tinymce.get(editorId).focus();
                    }
                }, 100);
            }
        });
    }

    // Initialize edit/delete buttons if they exist
    const editBtn = newReply.querySelector('.inline-edit-comment-btn');
    if (editBtn) {
        initializeInlineEditButton(editBtn);
    }

    const deleteBtn = newReply.querySelector('.delete-comment-btn');
    if (deleteBtn) {
        initializeDeleteButton(deleteBtn);
    }
}

// Update comment count in UI
function updateCommentCount(newCount) {
    document.querySelectorAll('.comment-count, .comments-count').forEach(element => {
        element.textContent = newCount;
    });
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
        const likeCountElement = commentElement.querySelector('.comment-like-count-'+data.comment_id);
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
@endauth
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
