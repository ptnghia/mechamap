{{-- Ratings List Component (Thread-style layout) --}}
@props([
    'ratings' => collect(),
    'showcase' => null
])

<div class="ratings-list">
    @forelse($ratings as $rating)
        <div class="rating-item mb-4" id="rating-{{ $rating->id }}">
            <div class="d-flex gap-3">
                {{-- Avatar (bên trái) --}}
                <div class="rating-avatar">
                    <a href="{{ route('profile.show', $rating->user->username ?? $rating->user->id) }}">
                        <img src="{{ $rating->user->getAvatarUrl() }}"
                             class="rounded-circle"
                             width="48"
                             height="48"
                             alt="Avatar của {{ $rating->user->display_name }}"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($rating->user->name, 0, 1))) }}&background=6366f1&color=fff&size=48'">
                    </a>
                </div>

                {{-- Content (bên phải) --}}
                <div class="rating-content flex-grow-1">
                    {{-- Header: User info | Overall rating | Time --}}
                    <div class="rating-header d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="user-info">
                                <a href="{{ route('profile.show', $rating->user->username ?? $rating->user->id) }}"
                                   class="fw-semibold text-decoration-none user-name">
                                    {{ $rating->user->display_name }}
                                </a>
                                {{-- User role/badge --}}
                                @if($rating->user->role)
                                <span class="user-role-badge badge bg-secondary ms-2">
                                    {{ ucfirst($rating->user->role) }}
                                </span>
                                @endif
                                {{-- User stats --}}
                                <div class="user-stats text-muted small mt-1">
                                    <span><i class="fas fa-trophy"></i> {{ $rating->user->showcase_items_count ?? 0 }} showcases</span>
                                    @if($rating->user->showcase_ratings_count ?? 0 > 0)
                                    <span class="ms-2"><i class="fas fa-star"></i> {{ $rating->user->showcase_ratings_count }} đánh giá</span>
                                    @endif
                                    <span class="ms-2"><i class="fas fa-calendar"></i> Tham gia {{ $rating->user->created_at->format('M Y') }}</span>
                                </div>
                            </div>

                            {{-- Overall rating stars --}}
                            @php
                                $averageRating = ($rating->technical_quality + $rating->innovation + $rating->usefulness + $rating->documentation) / 4;
                            @endphp
                            @if($averageRating > 0)
                            <div class="overall-rating-display d-flex align-items-center">
                                <div class="stars-small me-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <span class="rating-value fw-bold text-primary">{{ number_format($averageRating, 1) }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="rating-time text-muted small">
                                <i class="fas fa-clock"></i>
                                {{ $rating->created_at->diffForHumans() }}
                            </span>

                            {{-- Delete button --}}
                            @auth
                            @if($rating->user_id === auth()->id() || $showcase->user_id === auth()->id())
                            <div class="rating-actions">
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger delete-rating-btn"
                                        data-rating-id="{{ $rating->id }}"
                                        title="Xóa đánh giá">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endif
                            @endauth
                        </div>
                    </div>

                    {{-- Review content (nếu có) --}}
                    @if($rating->review)
                    <div class="rating-review mb-3">
                        <div class="review-content">
                            {!! nl2br(e($rating->review)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Rating criteria tags --}}
                    <div class="rating-criteria mb-3">
                        <div class="criteria-tags d-flex flex-wrap gap-2">
                            @if($rating->technical_quality)
                            <span class="criteria-tag badge bg-light text-dark">
                                <i class="fas fa-cog"></i>
                                Chất lượng kỹ thuật: {{ $rating->technical_quality }}/5
                            </span>
                            @endif

                            @if($rating->innovation)
                            <span class="criteria-tag badge bg-light text-dark">
                                <i class="fas fa-lightbulb"></i>
                                Tính sáng tạo: {{ $rating->innovation }}/5
                            </span>
                            @endif

                            @if($rating->usefulness)
                            <span class="criteria-tag badge bg-light text-dark">
                                <i class="fas fa-thumbs-up"></i>
                                Tính hữu ích: {{ $rating->usefulness }}/5
                            </span>
                            @endif

                            @if($rating->documentation)
                            <span class="criteria-tag badge bg-light text-dark">
                                <i class="fas fa-file-alt"></i>
                                Chất lượng tài liệu: {{ $rating->documentation }}/5
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Attached images (tạm thời ẩn - chưa có relationship)
                    @if($rating->images && $rating->images->count() > 0)
                    <div class="rating-images mb-3">
                        <div class="row g-2">
                            @foreach($rating->images as $image)
                            <div class="col-auto">
                                <div class="rating-image-item">
                                    <a href="{{ $image->url }}"
                                       data-fancybox="rating-{{ $rating->id }}-images"
                                       data-caption="{{ $image->file_name ?? 'Hình ảnh đánh giá' }}">
                                        <img src="{{ $image->url }}"
                                             alt="{{ $image->file_name ?? 'Hình ảnh đánh giá' }}"
                                             class="img-thumbnail rating-image"
                                             style="max-width: 120px; max-height: 120px; object-fit: cover; cursor: pointer;">
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    --}}

                    {{-- Rating actions --}}
                    <div class="rating-actions-bar d-flex align-items-center gap-3">
                        @auth
                        <button type="button"
                                class="rating-action-btn like-rating-btn"
                                data-rating-id="{{ $rating->id }}">
                            <i class="fas fa-heart"></i>
                            <span class="like-count">0</span>
                            Thích
                        </button>

                        <button type="button"
                                class="rating-action-btn reply-toggle-btn"
                                data-rating-id="{{ $rating->id }}">
                            <i class="fas fa-reply"></i>
                            Trả lời
                        </button>
                        @else
                        <span class="text-muted">
                            <i class="fas fa-heart"></i>
                            {{ $rating->likes_count ?? 0 }} Thích
                        </span>
                        @endauth
                    </div>

                    {{-- Reply form (ẩn mặc định) --}}
                    @auth
                    <div class="rating-reply-form mt-3" id="reply-form-{{ $rating->id }}" style="display: none;">
                        <div class="d-flex gap-3">
                            <img src="{{ auth()->user()->getAvatarUrl() }}"
                                 class="rounded-circle"
                                 width="32"
                                 height="32"
                                 alt="Avatar"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr(auth()->user()->name, 0, 1))) }}&background=6366f1&color=fff&size=32'">
                            <div class="flex-grow-1">
                                <form class="reply-form" data-rating-id="{{ $rating->id }}">
                                    @csrf
                                    <input type="hidden" name="content" id="reply-content-{{ $rating->id }}">

                                    <x-ckeditor5-comment
                                        name="reply_content_editor"
                                        placeholder="Trả lời đánh giá này..."
                                        :id="'reply-editor-' . $rating->id"
                                        :required="true"
                                        minHeight="80px"
                                    />

                                    <x-enhanced-image-upload
                                        name="images"
                                        :id="'reply-upload-' . $rating->id"
                                        :max-files="5"
                                        max-size="5"
                                        :multiple="true"
                                    />

                                    <div class="reply-actions mt-2 d-flex justify-content-end gap-2">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary reply-cancel-btn"
                                                data-rating-id="{{ $rating->id }}">
                                            Hủy
                                        </button>
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"
                                                data-original-text="Gửi trả lời">
                                            <i class="fas fa-paper-plane"></i>
                                            Gửi trả lời
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth

                    {{-- Rating replies (tạm thời ẩn - chưa có relationship)
                    @if($rating->replies && $rating->replies->count() > 0)
                    <div class="rating-replies mt-3">
                        @foreach($rating->replies as $reply)
                        <div class="rating-reply d-flex gap-3 mb-3 ms-4">
                            <div class="reply-avatar">
                                <a href="{{ route('profile.show', $reply->user->username ?? $reply->user->id) }}">
                                    <img src="{{ $reply->user->getAvatarUrl() }}"
                                         class="rounded-circle"
                                         width="32"
                                         height="32"
                                         alt="Avatar của {{ $reply->user->display_name }}"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($reply->user->name, 0, 1))) }}&background=6366f1&color=fff&size=32'">
                                </a>
                            </div>
                            <div class="reply-content flex-grow-1">
                                <div class="reply-header mb-1">
                                    <a href="{{ route('profile.show', $reply->user->username ?? $reply->user->id) }}"
                                       class="fw-semibold text-decoration-none">
                                        {{ $reply->user->display_name }}
                                    </a>
                                    <span class="text-muted small ms-2">
                                        <i class="fas fa-clock"></i>
                                        {{ $reply->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="reply-body">
                                    {!! nl2br(e($reply->content)) !!}
                                </div>

                                Reply images
                                @if($reply->images && $reply->images->count() > 0)
                                <div class="reply-images mt-2">
                                    <div class="row g-1">
                                        @foreach($reply->images as $image)
                                        <div class="col-auto">
                                            <a href="{{ $image->url }}"
                                               data-fancybox="reply-{{ $reply->id }}-images">
                                                <img src="{{ $image->url }}"
                                                     class="img-thumbnail"
                                                     style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    --}}
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-5">
            <i class="fas fa-star fa-3x mb-3 opacity-50"></i>
            <h5>Chưa có đánh giá nào</h5>
            <p>Hãy là người đầu tiên đánh giá showcase này!</p>
        </div>
    @endforelse
</div>

<style>
.rating-item {
    padding: 1.5rem;
    background: #fff;
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    transition: box-shadow 0.15s ease-in-out;
}

.rating-item:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.user-name {
    color: #5a5c69;
    font-size: 0.95rem;
}

.user-name:hover {
    color: #6366f1;
}

.user-role-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
}

.user-stats {
    font-size: 0.75rem;
    line-height: 1.2;
}

.user-stats i {
    margin-right: 0.25rem;
    width: 12px;
    text-align: center;
}

.user-stats span {
    white-space: nowrap;
}

.overall-rating-display .stars-small .fa-star {
    font-size: 0.8rem;
    margin: 0 1px;
}

.rating-value {
    font-size: 0.9rem;
}

.rating-time {
    font-size: 0.8rem;
}

.review-content {
    color: #5a5c69;
    line-height: 1.6;
    font-size: 0.95rem;
}

.criteria-tag {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border: 1px solid #dee2e6;
}

.criteria-tag i {
    margin-right: 0.25rem;
}

.rating-image {
    border-radius: 0.375rem;
    transition: transform 0.2s ease;
}

.rating-image:hover {
    transform: scale(1.05);
}

.rating-action-btn {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.rating-action-btn:hover {
    background: #f8f9fa;
    color: #495057;
}

.rating-action-btn.active {
    color: #dc3545;
}

.rating-action-btn i {
    margin-right: 0.25rem;
}

.rating-reply {
    border-left: 3px solid #e3e6f0;
    padding-left: 1rem;
}

.reply-actions {
    border-top: 1px solid #e3e6f0;
    padding-top: 0.75rem;
}

/* Responsive */
@media (max-width: 768px) {
    .rating-item {
        padding: 1rem;
    }

    .rating-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem !important;
    }

    .user-info {
        width: 100%;
    }

    .user-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.25rem;
    }

    .user-stats span {
        font-size: 0.7rem;
    }

    .user-role-badge {
        display: inline-block;
        margin-top: 0.25rem;
        margin-left: 0 !important;
    }

    .criteria-tags {
        flex-direction: column;
        align-items: flex-start;
    }

    .rating-actions-bar {
        flex-wrap: wrap;
    }

    .overall-rating-display {
        margin-top: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeRatingsList();
});

function initializeRatingsList() {
    // Delete rating buttons
    document.querySelectorAll('.delete-rating-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa đánh giá này?')) {
                deleteRating(this.dataset.ratingId);
            }
        });
    });

    // Like rating buttons
    document.querySelectorAll('.like-rating-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            toggleRatingLike(this.dataset.ratingId, this);
        });
    });

    // Reply forms
    document.querySelectorAll('.rating-reply-form-inner').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitRatingReply(this.dataset.ratingId);
        });
    });
}

function toggleRatingReplyForm(ratingId) {
    const form = document.getElementById(`reply-form-${ratingId}`);
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
}

function deleteRating(ratingId) {
    // Implementation will be added later
    console.log('Delete rating:', ratingId);
}

function toggleRatingLike(ratingId, button) {
    // Implementation will be added later
    console.log('Toggle rating like:', ratingId);
}

function submitRatingReply(ratingId) {
    // Implementation will be added later
    console.log('Submit rating reply:', ratingId);
}
</script>
