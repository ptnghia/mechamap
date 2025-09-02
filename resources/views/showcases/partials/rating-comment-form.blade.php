{{-- Rating & Comment Integrated Form --}}
@props([
    'showcase' => null,
    'userRating' => null
])

@auth
    @if($showcase->user_id !== auth()->id())
        @if(!$userRating)
        {{-- Form đánh giá và bình luận tích hợp --}}
        <div class="rating-comment-form-section">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-star"></i>
                        Đánh giá và nhận xét showcase này
                    </h6>
                </div>
                <div class="card-body">
                    <form id="rating-comment-form" data-showcase-id="{{ $showcase->id }}" action="{{ route('showcase.rating.store', $showcase) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Phần đánh giá sao --}}
                        <div class="rating-section mb-4">
                            <h6 class="form-section-title">
                                <i class="fas fa-star text-warning"></i>
                                Đánh giá theo tiêu chí
                            </h6>
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
                        </div>

                        {{-- Phần nhận xét với TinyMCE --}}
                        <div class="comment-section mb-4">
                            <h6 class="form-section-title">
                                <i class="fas fa-comment text-primary"></i>
                                Nhận xét chi tiết
                            </h6>
                            <div class="d-flex gap-3">
                                <img src="{{ auth()->user()->getAvatarUrl() }}"
                                    class="user-avatar"
                                    alt="Avatar"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr(auth()->user()->name, 0, 1))) }}&background=6366f1&color=fff&size=48'">
                                <div class="flex-grow-1">
                                    <x-tinymce-editor
                                        name="review"
                                        id="rating-comment-editor"
                                        placeholder="Chia sẻ nhận xét chi tiết về showcase này..."
                                        context="comment"
                                        :height="120"
                                        :required="false"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Phần upload hình ảnh --}}
                        <div class="images-section mb-4">
                            <h6 class="form-section-title">
                                <i class="fas fa-images text-success"></i>
                                Hình ảnh minh họa (tùy chọn)
                            </h6>
                            <x-comment-image-upload
                                :max-files="10"
                                max-size="5MB"
                                context="showcase-rating"
                                upload-text="Kéo thả hình ảnh vào đây hoặc click để chọn"
                                accept-description="Tối đa 10 file • 5MB mỗi file • JPG, PNG, GIF, WEBP"
                                :show-preview="true"
                                :compact="false"
                            />
                        </div>

                        {{-- Form actions --}}
                        <div class="form-actions d-flex justify-content-between align-items-center">
                            <div class="form-info">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Đánh giá và nhận xét sẽ được hiển thị công khai
                                </small>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                    <i class="fas fa-undo"></i>
                                    Đặt lại
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    Gửi đánh giá & nhận xét
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @else
        {{-- Thông báo đã đánh giá --}}
        <div class="user-rated-notice">
            <div class="alert alert-success d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-1">Bạn đã đánh giá showcase này!</h6>
                    <p class="mb-0 small">Đánh giá của bạn đã được ghi nhận. Muốn đánh giá lại? Hãy xóa đánh giá hiện tại.</p>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm ms-3" id="delete-rating" data-rating-id="{{ $userRating->id }}">
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
        <a href="{{ route('login') }}">Đăng nhập</a> để đánh giá và nhận xét showcase này.
    </div>
@endauth

<style>
.rating-comment-form-section .card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.form-section-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e3e6f0;
}

.rating-input {
    margin: 0.5rem 0;
}

.rating-star {
    font-size: 1.5rem;
    color: #dee2e6;
    cursor: pointer;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.rating-star:hover,
.rating-star.active {
    color: #ffc107;
    transform: scale(1.1);
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.form-actions {
    padding-top: 1rem;
    border-top: 1px solid #e3e6f0;
}

.action-buttons .btn {
    min-width: 120px;
}

.form-info {
    flex-grow: 1;
}

/* Responsive */
@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }

    .form-info {
        text-align: center;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .action-buttons .btn {
        flex: 1;
        min-width: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeRatingCommentForm();
});

function initializeRatingCommentForm() {
    // Initialize rating stars
    const ratingStars = document.querySelectorAll('.rating-star');

    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            const category = this.dataset.category;
            const categoryStars = document.querySelectorAll(`[data-category="${category}"]`);

            // Update visual state
            categoryStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });

            // Update hidden input
            document.getElementById(category).value = rating;
        });

        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            const category = this.dataset.category;
            const categoryStars = document.querySelectorAll(`[data-category="${category}"]`);

            categoryStars.forEach((s, index) => {
                if (index < rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#dee2e6';
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            const category = this.dataset.category;
            const currentRating = parseInt(document.getElementById(category).value) || 0;
            const categoryStars = document.querySelectorAll(`[data-category="${category}"]`);

            categoryStars.forEach((s, index) => {
                if (index < currentRating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#dee2e6';
                }
            });
        });
    });

    // Form submission
    const form = document.getElementById('rating-comment-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitRatingComment();
        });
    }

    // Delete rating button
    const deleteBtn = document.getElementById('delete-rating');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa đánh giá này?')) {
                deleteRating(this.dataset.ratingId);
            }
        });
    }
}

function resetForm() {
    // Reset rating stars
    document.querySelectorAll('.rating-star').forEach(star => {
        star.classList.remove('active');
        star.style.color = '#dee2e6';
    });

    // Reset hidden inputs
    document.querySelectorAll('input[type="hidden"]').forEach(input => {
        if (input.name !== '_token') {
            input.value = '';
        }
    });

    // Reset TinyMCE if available
    const editorId = 'rating-comment-editor';
    const editor = tinymce.get(editorId);
    if (editor) {
        editor.setContent('');
    }

    // Reset comment image upload component
    const uploadComponents = document.querySelectorAll('.comment-image-upload');
    uploadComponents.forEach(component => {
        if (component.commentImageUpload) {
            component.commentImageUpload.clearFiles();
        }
    });
}

async function submitRatingComment() {
    const form = document.getElementById('rating-comment-form');
    if (!form) return;

    // Validate ratings - sử dụng đúng field names từ model
    const requiredRatings = ['technical_quality', 'innovation', 'usefulness', 'documentation'];
    const missingRatings = [];

    requiredRatings.forEach(category => {
        const input = document.getElementById(category);
        if (!input || !input.value) {
            missingRatings.push(category);
        }
    });

    if (missingRatings.length > 0) {
        alert('Vui lòng đánh giá đầy đủ tất cả các tiêu chí.');
        return;
    }

    try {
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

        // Get uploaded images from comment-image-upload component
        const uploadComponent = document.querySelector('.comment-image-upload');
        let uploadedImages = [];

        if (uploadComponent && uploadComponent.commentImageUpload && uploadComponent.commentImageUpload.hasFiles()) {
            uploadedImages = await uploadComponent.commentImageUpload.uploadFiles();
        }

        // Prepare form data
        const formData = new FormData(form);

        // Add uploaded image data
        if (uploadedImages.length > 0) {
            uploadedImages.forEach((image, index) => {
                formData.append(`uploaded_images[${index}]`, JSON.stringify(image));
            });
        }

        // Submit form
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Success - reload page or update UI
            window.location.reload();
        } else {
            throw new Error(data.message || 'Có lỗi xảy ra khi gửi đánh giá');
        }

    } catch (error) {
        console.error('Submit error:', error);
        alert(error.message || 'Có lỗi xảy ra khi gửi đánh giá');

        // Reset button state
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function deleteRating(ratingId) {
    // Implementation will be added in the next part
    console.log('Delete rating:', ratingId);
}
</script>
