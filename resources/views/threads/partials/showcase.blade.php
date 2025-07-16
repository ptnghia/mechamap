{{--
    Thread Showcase Partial
    Hiển thị showcase liên quan đến thread với validation bảo mật

    Required variables:
    - $thread: Thread model instance with showcase relationship loaded
--}}

<!-- Related Showcase Section -->
@if($thread->shouldDisplayShowcase())
<div class="showcase-section mb-4">
    <div class="showcase-section-header">
        <h5 class="title_page_sub">
            <i class="fas fa-tools text-muted me-1 me-2"></i>
            {{ __('showcase.related') }}
        </h5>
        <!--small class="text-muted">{{ __('showcase.for_thread') }}</!smal-->
    </div>
    <div class="showcase-section-body">
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
                    <div class="d-flex align-items-center me-3">
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
                        class="btn btn-main btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i>
                        {{ __('ui.actions.view_full_showcase') }}
                    </a>

                    @if($thread->showcase->showcase_url)
                    <a href="{{ $thread->showcase->showcase_url }}"
                        target="_blank"
                        class="btn btn-main btn-sm ms-2">
                        <i class="fas fa-link me-1"></i>
                        {{ __('ui.actions.view_details') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- Create Showcase Section -->
@auth
    @if($thread->userCanCreateShowcase(Auth::user()) && $thread->canCreateShowcase() && !$thread->showcase)
    <div class="create-showcase-section mb-4 d-flex justify-content-between align-items-center">
        <div class="create-showcase-left">
            <h5 class="create-showcase-title mb-0">
                <i class="fas fa-tools text-success me-2"></i>
                {{ __('showcase.create_from_thread') }}
            </h5>
            <small class="text-muted">{{ __('showcase.create_showcase_info') }}</small>
        </div>
        <div class="create-showcase-rigth">
            <button type="button" class="btn btn-main active" data-bs-toggle="modal" data-bs-target="#createShowcaseModal">
                <i class="fa-solid fa-file-circle-plus me-2"></i>
                {{ __('showcase.create_showcase_button') }}
            </button>
        </div>
    </div>

    <!-- Create Showcase Modal -->
    <div class="modal fade" id="createShowcaseModal" tabindex="-1" aria-labelledby="createShowcaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content createShowcaseModal">
                <form id="createShowcaseForm" action="{{ route('threads.create-showcase', $thread) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createShowcaseModalLabel">
                            <i class="fas fa-tools text-success me-2"></i>
                            {{ __('showcase.create_from_thread_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Progress Steps -->
                        <div class="progress mb-4" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 33%" id="progressBar"></div>
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

{{-- CSS for TinyMCE in Modal --}}
@push('styles')
<style>
/* TinyMCE trong modal showcase */
.modal .tox-tinymce {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.modal .tox-editor-header {
    border-bottom: 1px solid #dee2e6;
}

.modal .tox-toolbar {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal .tox-edit-area {
    border: none;
}

.modal .tox-statusbar {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
}

/* Đảm bảo TinyMCE không bị overflow trong modal */
.modal-body .tox-tinymce {
    max-width: 100%;
}

/* Validation styling cho TinyMCE */
.modal .tox-tinymce.is-invalid {
    border-color: #dc3545;
}

.modal .tox-tinymce.is-invalid .tox-editor-header {
    border-bottom-color: #dc3545;
}
</style>
@endpush

{{-- JavaScript for Showcase Modal --}}
@push('scripts')
<!-- TinyMCE Editor for Showcase -->
<script src="https://cdn.tiny.cloud/1/m3nymn6hdlv8nqnf4g88r0ccz9n86ks2aw92v0opuy7sx20y/tinymce/7/tinymce.min.js"
    referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize showcase wizard if modal exists
        if (document.getElementById('createShowcaseModal')) {
            initShowcaseWizard();
            initShowcaseTinyMCE();
        }
    });

    function initShowcaseTinyMCE() {
        // Initialize TinyMCE for showcase description
        tinymce.init({
            selector: '#showcase_description',
            height: 250,
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
            language: 'vi',
            placeholder: 'Mô tả chi tiết về dự án, phương pháp và kết quả đạt được...',
            content_style: `
                body {
                    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                    background-color: #fff;
                    margin: 8px;
                }
                blockquote {
                    border-left: 4px solid #007bff;
                    margin: 1.5em 0;
                    padding: 0.5em 1em;
                    background-color: #f8f9fa;
                    font-style: italic;
                }
                pre {
                    background-color: #f8f9fa;
                    border: 1px solid #e9ecef;
                    border-radius: 4px;
                    padding: 1em;
                    overflow-x: auto;
                }
                code {
                    background-color: #f8f9fa;
                    padding: 0.2em 0.4em;
                    border-radius: 3px;
                    font-size: 0.875em;
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

                // Handle initialization complete
                editor.on('init', function() {
                    console.log('TinyMCE for showcase description initialized successfully');
                });

                // Handle content changes for validation
                editor.on('change keyup', function() {
                    const content = editor.getContent();
                    const textarea = document.getElementById('showcase_description');
                    if (textarea) {
                        textarea.value = content;
                        // Remove validation error if content exists
                        if (content.trim()) {
                            textarea.classList.remove('is-invalid');
                            const errorMsg = textarea.parentNode.querySelector('.text-danger');
                            if (errorMsg) {
                                errorMsg.remove();
                            }
                        }
                    }
                });
            },

            // Style formats for showcase descriptions
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
                let description = '';

                // Get content from TinyMCE if available, otherwise from textarea
                if (tinymce.get('showcase_description')) {
                    description = tinymce.get('showcase_description').getContent().trim();
                } else {
                    description = document.getElementById('showcase_description').value.trim();
                }

                const coverImage = document.getElementById('cover_image').files[0];

                if (!description || description === '<p><br></p>' || description === '<p></p>') {
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

            // Add error class - special handling for TinyMCE
            if (fieldId === 'showcase_description' && tinymce.get(fieldId)) {
                // For TinyMCE, add error class to the TinyMCE container
                const tinymceContainer = field.parentNode.querySelector('.tox-tinymce');
                if (tinymceContainer) {
                    tinymceContainer.classList.add('is-invalid');
                }
            } else {
                field.classList.add('is-invalid');
            }

            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger mt-1';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);

            // Remove error on input - special handling for TinyMCE
            if (fieldId === 'showcase_description' && tinymce.get(fieldId)) {
                // For TinyMCE, listen to editor changes
                const editor = tinymce.get(fieldId);
                const removeError = function() {
                    const tinymceContainer = field.parentNode.querySelector('.tox-tinymce');
                    if (tinymceContainer) {
                        tinymceContainer.classList.remove('is-invalid');
                    }
                    field.classList.remove('is-invalid');
                    const errorMsg = field.parentNode.querySelector('.text-danger');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                    editor.off('change keyup', removeError);
                };
                editor.on('change keyup', removeError);
            } else {
                field.addEventListener('input', function() {
                    field.classList.remove('is-invalid');
                    const errorMsg = field.parentNode.querySelector('.text-danger');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }, { once: true });
            }
        }

        // Form submission
        document.getElementById('createShowcaseForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (validateStep(currentStep)) {
                // Sync TinyMCE content with textarea before submission
                if (tinymce.get('showcase_description')) {
                    const content = tinymce.get('showcase_description').getContent();
                    document.getElementById('showcase_description').value = content;
                }

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

            // Reset TinyMCE content
            if (tinymce.get('showcase_description')) {
                tinymce.get('showcase_description').setContent('');
            }

            // Remove validation errors
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
            // Also remove is-invalid from TinyMCE containers
            document.querySelectorAll('.tox-tinymce.is-invalid').forEach(container => {
                container.classList.remove('is-invalid');
            });
            document.querySelectorAll('.text-danger').forEach(error => {
                error.remove();
            });

            // Reset submit button
            submitBtn.innerHTML = '<i class="fas fa-star me-2"></i>Tạo Showcase';
            submitBtn.disabled = false;
        });

        // Initialize TinyMCE when modal is shown
        document.getElementById('createShowcaseModal').addEventListener('shown.bs.modal', function() {
            // Ensure TinyMCE is properly initialized when modal is visible
            setTimeout(function() {
                if (!tinymce.get('showcase_description')) {
                    initShowcaseTinyMCE();
                }
            }, 100);
        });
    }
</script>
@endpush
