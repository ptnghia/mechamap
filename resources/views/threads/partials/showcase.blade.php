{{--
    Thread Showcase Partial
    Hiển thị showcase liên quan đến thread với validation bảo mật

    Required variables:
    - $thread: Thread model instance with showcase relationship loaded
--}}

<!-- Related Showcase Section -->
@if($thread->shouldDisplayShowcase())
<div class="showcase-section mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-star text-warning me-2"></i>
                {{ __('showcase.related') }}
            </h5>
            <small class="text-muted">{{ __('showcase.for_thread') }}</small>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Showcase Image -->
                <div class="col-md-4 mb-3 mb-md-0">
                    @if($thread->showcase->cover_image)
                        <img src="{{ asset($thread->showcase->cover_image) }}"
                             alt="{{ $thread->showcase->title }}"
                             class="img-fluid rounded shadow-sm"
                             style="width: 100%; height: 200px; object-fit: cover;"
                             onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
                    @elseif($thread->showcase->media && $thread->showcase->media->count() > 0)
                        @php
                            $firstMedia = $thread->showcase->media->first();
                        @endphp
                        <img src="{{ asset($firstMedia->file_path) }}"
                             alt="{{ $thread->showcase->title }}"
                             class="img-fluid rounded shadow-sm"
                             style="width: 100%; height: 200px; object-fit: cover;"
                             onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
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
@else
<!-- Create Showcase Section -->
@auth
    @if($thread->userCanCreateShowcase(Auth::user()) && $thread->canCreateShowcase() && !$thread->showcase)
    <div class="create-showcase-section mb-4">
        <div class="card shadow-sm border-success">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus-circle text-success me-2"></i>
                    {{ __('showcase.create_from_thread') }}
                </h5>
                <small class="text-muted">{{ __('showcase.create_from_thread_description') }}</small>
            </div>
            <div class="card-body">
                <p class="mb-3">
                    <i class="fas fa-info-circle text-info me-2"></i>
                    {{ __('showcase.create_showcase_info') }}
                </p>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createShowcaseModal">
                    <i class="fas fa-star me-2"></i>
                    {{ __('showcase.create_showcase_button') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Create Showcase Modal -->
    <div class="modal fade" id="createShowcaseModal" tabindex="-1" aria-labelledby="createShowcaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="createShowcaseForm" action="{{ route('threads.create-showcase', $thread) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createShowcaseModalLabel">
                            <i class="fas fa-star text-warning me-2"></i>
                            {{ __('showcase.create_from_thread_title') }}
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
                                    <div class="step-label">{{ __('showcase.basic_info') }}</div>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="step-number">2</div>
                                    <div class="step-label">{{ __('showcase.content') }}</div>
                                </div>
                                <div class="step" data-step="3">
                                    <div class="step-number">3</div>
                                    <div class="step-label">{{ __('showcase.complete') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1: Basic Information -->
                        <div class="step-content" id="step1">
                            <h6 class="mb-3">{{ __('showcase.step_1_title') }}</h6>

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
@endif

{{-- JavaScript for Showcase Modal --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize showcase wizard if modal exists
        if (document.getElementById('createShowcaseModal')) {
            initShowcaseWizard();
        }
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
