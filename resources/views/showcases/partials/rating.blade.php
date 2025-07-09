{{-- Rating Display & Form for Showcases --}}
<div class="showcase-rating-section">
    {{-- Rating Summary --}}
    <div class="rating-summary mb-4">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="overall-rating text-center">
                    <div class="rating-number">{{ number_format($showcase->average_rating, 1) }}</div>
                    <div class="rating-stars mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($showcase->average_rating) ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </div>
                    <div class="rating-count text-muted">
                        {{ $showcase->ratings_count }} đánh giá
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="rating-breakdown">
                    @php $categories = $showcase->getCategoryAverages(); @endphp
                    @foreach(\App\Models\ShowcaseRating::getCategoryNames() as $key => $name)
                        <div class="rating-category mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="category-name">{{ $name }}</span>
                                <div class="category-rating">
                                    <div class="stars-small">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($categories[$key]) ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="rating-value ms-2">{{ number_format($categories[$key], 1) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Rating Form --}}
    @auth
        @if($showcase->user_id !== auth()->id())
            @php $userRating = $showcase->getUserRating(auth()->user()); @endphp

            @if(!$userRating)
            {{-- Hiển thị form đánh giá nếu user chưa đánh giá --}}
            <div class="rating-form-section">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-star"></i>
                            Đánh giá showcase này
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="rating-form" data-showcase-id="{{ $showcase->id }}">
                            @csrf
                            <div class="row">
                                @foreach(\App\Models\ShowcaseRating::getCategoryNames() as $key => $name)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ $name }}</label>
                                        <div class="rating-input" data-category="{{ $key }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star rating-star"
                                                   data-rating="{{ $i }}"
                                                   data-category="{{ $key }}"></i>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="{{ $key }}" id="{{ $key }}" value="">
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nhận xét (tùy chọn)</label>
                                <x-rich-text-editor
                                    name="review"
                                    placeholder="Chia sẻ nhận xét chi tiết về showcase này..."
                                    id="rating-editor"
                                    :required="false"
                                    :allowImages="true"
                                    minHeight="100px"
                                    value=""
                                />
                            </div>

                            <div class="rating-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-star"></i>
                                    Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @else
            {{-- Hiển thị thông báo và nút xóa nếu user đã đánh giá --}}
            <div class="user-rated-notice">
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Bạn đã đánh giá showcase này!</h6>
                        <p class="mb-0 small">Đánh giá của bạn đã được ghi nhận. Muốn đánh giá lại? Hãy xóa đánh giá hiện tại.</p>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm ms-3" id="delete-rating">
                        <i class="fas fa-trash"></i>
                        Xóa đánh giá
                    </button>
                </div>
            </div>
            @endif
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Bạn không thể đánh giá showcase của chính mình.
            </div>
        @endif
    @else
        <div class="alert alert-warning">
            <i class="fas fa-sign-in-alt"></i>
            <a href="{{ route('login') }}">Đăng nhập</a> để đánh giá showcase này.
        </div>
    @endauth
</div>

<style>
.rating-number {
    font-size: 3rem;
    font-weight: bold;
    color: #ffc107;
}

.rating-stars .fa-star {
    font-size: 1.5rem;
    margin: 0 2px;
}

.stars-small .fa-star {
    font-size: 0.9rem;
    margin: 0 1px;
}

.rating-input {
    margin: 0.5rem 0;
}

.rating-star {
    font-size: 1.5rem;
    color: #dee2e6;
    cursor: pointer;
    margin: 0 2px;
    transition: color 0.2s ease;
}

.rating-star:hover,
.rating-star.active {
    color: #ffc107;
}

.rating-category {
    padding: 0.25rem 0;
}

.category-name {
    font-weight: 500;
    font-size: 0.9rem;
}

.rating-value {
    font-weight: 600;
    color: #495057;
}
</style>
