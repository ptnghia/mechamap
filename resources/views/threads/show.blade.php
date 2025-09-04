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
                        <span class="like-count me-1">{{ $thread->likes_count ?? 0 }}</span>
                        <span class="text">{{ __('thread.like') }}</span>
                    </button>
                    @else
                    <button type="button" class="btn btn-sm btn_meta btn-like" onclick="showLoginModal()" title="{{ __('thread.login_to_like') }}">
                        <i class="fas fa-thumbs-up"></i>
                        <span class=" like-count">{{ $thread->likes_count ?? 0 }}</span>
                        <span class="text">{{ __('thread.like') }}</span>

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
                                    <!-- Reply item -->
                                    <div class="comment_item mb-3" id="comment-{{ $reply->id }}" data-reply="true">
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
                                data-parent-id="{{ $comment->id }}"
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
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
@endauth
@php
    // Translation keys required by threads.js (keep list minimal for payload efficiency)
    $threadJsKeys = [
        'thread.replies',
        'thread.comments',
        'thread.joined',
        'thread.unlike',
        'thread.like',
        'thread.login_to_like',
        'thread.quote',
        'thread.reply',
        'thread.edit',
        'thread.delete',
        'thread.delete_image',
        'thread.reply_content_required',
        'thread.sending',
        'thread.reply_posted_successfully',
        'thread.reply_posting_error',
        'thread.comment_updated_successfully',
        'thread.content_required',
        'thread.saving',
        'thread.update_failed',
        'features.threads.delete_reply_message',
        'features.threads.delete_comment_message',
        'thread.comment_deleted_successfully',
        'thread.request_error',
        'ui.confirmations.delete_image',
        'ui.messages.delete_image_success',
        'ui.messages.delete_image_error',
        'ui.status.processing',
        'ui.actions.saved',
        'ui.actions.save',
        'ui.actions.following',
        'ui.actions.follow',
        'ui.status.loading_comments',
        'ui.messages.comments_sorted',
        'ui.messages.request_error',
        'thread.participants',
    ];
    $threadJsTranslations = [];
    foreach ($threadJsKeys as $k) {
        $threadJsTranslations[$k] = __($k);
    }
@endphp
<script>
// Preloaded translations for thread page JS (generated server-side)
window.ThreadTranslations = Object.assign({}, window.ThreadTranslations || {}, @json($threadJsTranslations, JSON_UNESCAPED_UNICODE));
// Lightweight lookup used inside threads.js
window.transSafe = function(key){
    return (window.ThreadTranslations && window.ThreadTranslations[key]) || key;
};
</script>
<script>
window.ThreadPageConfig = {
    threadId: {{ $thread->id }},
    userId: {{ auth()->id() ?? 'null' }},
    userAvatar: "{{ auth()->user() ? auth()->user()->getAvatarUrl() : '' }}",
    scrollToCommentId: {{ session('scroll_to_comment') ?? 'null' }},
    csrf: "{{ csrf_token() }}",
    routes: {
        storeComment: "{{ route('threads.comments.store', $thread) }}",
        threadLike: "{{ route('threads.like', $thread->slug) }}",
        threadSave: "{{ route('threads.save', $thread->slug) }}",
        threadFollow: "/ajax/threads/{{ $thread->slug }}/follow",
        commentStore: "/threads/{{ $thread->id }}/comments",
        commentLike: "/comments/:id/like",
        commentDelete: "/comments/:id",
        commentImageDelete: "/comments/:commentId/images/:imageId"
    }
};

function showLoginModal() {
    const loginModal = document.getElementById('loginModal');
    if (loginModal && window.bootstrap) {
        new bootstrap.Modal(loginModal).show();
    } else {
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
    }
}
</script>
<script src="{{ asset_versioned('js/frontend/page/threads.js') }}"></script>
@endpush
