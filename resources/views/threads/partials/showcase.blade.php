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
                            {{ __('common.labels.by') }}
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
                                <label for="showcase_title" class="form-label">{{ __('showcase.showcase_title') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="showcase_title" name="title"
                                       value="{{ $thread->title }}" required>
                                <div class="form-text">{{ __('showcase.showcase_title_help') }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="showcase_category" class="form-label">{{ __('showcase.category') }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="showcase_category" name="category" required>
                                    <option value="">{{ __('showcase.select_category') }}</option>
                                    <option value="Thiết kế Cơ khí" {{ $thread->category && $thread->category->name == 'Thiết kế Cơ khí' ? 'selected' : '' }}>Thiết kế Cơ khí</option>
                                    <option value="Công nghệ Chế tạo" {{ $thread->category && $thread->category->name == 'Công nghệ Chế tạo' ? 'selected' : '' }}>Công nghệ Chế tạo</option>
                                    <option value="Vật liệu Kỹ thuật" {{ $thread->category && $thread->category->name == 'Vật liệu Kỹ thuật' ? 'selected' : '' }}>Vật liệu Kỹ thuật</option>
                                    <option value="Tự động hóa & Robotics" {{ $thread->category && $thread->category->name == 'Tự động hóa & Robotics' ? 'selected' : '' }}>Tự động hóa & Robotics</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="project_type" class="form-label">{{ __('showcase.project_type') }}</label>
                                <select class="form-select" id="project_type" name="project_type">
                                    <option value="">{{ __('showcase.select_project_type') }}</option>
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
                            <h6 class="mb-3">{{ __('showcase.step_2_title') }}</h6>

                            <div class="mb-3">
                                <label for="showcase_description" class="form-label">{{ __('showcase.project_description') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="showcase_description" name="description" rows="6" required>{{ strip_tags($thread->content) }}</textarea>
                                <div class="form-text">{{ __('showcase.project_description_help') }}</div>
                            </div>

                            <!-- Cover Image Upload Component -->
                            <x-file-upload
                                name="cover_image"
                                :file-types="['jpg', 'jpeg', 'png', 'gif', 'webp']"
                                max-size="5MB"
                                :required="true"
                                label="{{ __('showcase.cover_image') }} <span class='text-danger'>*</span>"
                                help-text="{{ __('showcase.cover_image_help') }}"
                                id="showcase-cover-image"
                            />
                            @if($thread->featured_image)
                            <div class="mt-2">
                                <small class="text-muted">{{ __('showcase.current_thread_image') }}</small><br>
                                <img src="{{ asset('' . $thread->featured_image) }}" alt="Thread Image" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="complexity_level" class="form-label">{{ __('showcase.complexity_level') }}</label>
                                        <select class="form-select" id="complexity_level" name="complexity_level">
                                            <option value="Beginner">{{ __('showcase.complexity_levels.beginner') }}</option>
                                            <option value="Intermediate" selected>{{ __('showcase.complexity_levels.intermediate') }}</option>
                                            <option value="Advanced">{{ __('showcase.complexity_levels.advanced') }}</option>
                                            <option value="Expert">{{ __('showcase.complexity_levels.expert') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="industry_application" class="form-label">{{ __('showcase.industry_application') }}</label>
                                        <input type="text" class="form-control" id="industry_application" name="industry_application"
                                               placeholder="{{ __('showcase.industry_placeholder') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- File Attachments Section -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-paperclip me-1"></i>
                                    {{ __('showcase.file_attachments') }}
                                    <small class="text-muted">({{ __('showcase.file_attachments_optional') }})</small>
                                </label>
                                <div class="file-upload-area border rounded p-3" id="fileUploadArea">
                                    <div class="upload-zone text-center py-3" id="uploadZone">
                                        <i class="fas fa-cloud-upload-alt fs-2 text-muted mb-2"></i>
                                        <p class="mb-2">{{ __('showcase.file_upload_area') }} <button type="button" class="btn btn-link p-0" id="browseFiles">{{ __('showcase.browse_files') }}</button></p>
                                        <small class="text-muted">
                                            {{ __('showcase.file_upload_help') }}<br>
                                            {{ __('showcase.file_upload_limits') }}
                                        </small>
                                        <input type="file" id="fileInput" name="attachments[]" multiple
                                               accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.dwg,.dxf,.step,.stp,.stl,.obj,.iges,.igs"
                                               style="display: none;">
                                    </div>

                                    <!-- File Preview Area -->
                                    <div class="file-previews mt-3" id="filePreviews" style="display: none;">
                                        <h6 class="mb-2">{{ __('showcase.files_selected') }}</h6>
                                        <div class="row" id="filePreviewContainer"></div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    {{ __('showcase.file_upload_description') }}
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div class="step-content d-none" id="step3">
                            <h6 class="mb-3">{{ __('showcase.step_3_title') }}</h6>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>{{ __('showcase.confirm_info') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach(__('showcase.confirm_points') as $point)
                                    <li>{{ $point }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    {{ __('showcase.agree_terms') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('showcase.previous') }}
                        </button>
                        <button type="button" class="btn btn-primary" id="nextBtn">
                            {{ __('showcase.next') }}<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fas fa-star me-2"></i>{{ __('showcase.create_showcase') }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.actions.cancel') }}</button>
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

/* File Upload Styling */
.file-upload-area {
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6 !important;
    transition: all 0.3s ease;
}

.file-upload-area.dragover {
    border-color: #007bff !important;
    background-color: #e3f2fd;
}

.upload-zone {
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.upload-zone:hover {
    background-color: #e9ecef;
}

.file-preview-item {
    position: relative;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
    background-color: #fff;
    margin-bottom: 0.5rem;
}

.file-preview-item .file-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    margin-right: 0.75rem;
}

.file-preview-item .remove-file {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #dc3545;
    color: white;
    border: none;
    font-size: 0.75rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-preview-item .remove-file:hover {
    background-color: #c82333;
}

.file-preview-item.image-preview img {
    max-width: 60px;
    max-height: 60px;
    object-fit: cover;
    border-radius: 0.25rem;
}

.file-size {
    font-size: 0.75rem;
    color: #6c757d;
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
        let selectedFiles = []; // Store selected files

        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');
        const progressBar = document.getElementById('progressBar');

        if (!nextBtn) return; // Exit if modal doesn't exist

        // Initialize file upload functionality
        initFileUpload();

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
                    showError('showcase_title', '{{ __('showcase.title_required') }}');
                    isValid = false;
                }

                if (!category) {
                    showError('showcase_category', '{{ __('showcase.category_required') }}');
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
                    showError('showcase_description', '{{ __('showcase.description_required') }}');
                    isValid = false;
                }

                if (!coverImage) {
                    showError('cover_image', '{{ __('showcase.cover_image_required') }}');
                    isValid = false;
                } else {
                    // Validate file size (5MB)
                    if (coverImage.size > 5 * 1024 * 1024) {
                        showError('cover_image', '{{ __('showcase.file_size_error') }}');
                        isValid = false;
                    }
                }

                // Validate attachments if any
                if (selectedFiles.length > 0) {
                    for (let file of selectedFiles) {
                        if (!validateFile(file)) {
                            isValid = false;
                            break;
                        }
                    }
                }
            } else if (step === 3) {
                // Validate step 3
                const agreeTerms = document.getElementById('agree_terms').checked;

                if (!agreeTerms) {
                    showError('agree_terms', '{{ __('showcase.terms_required') }}');
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('showcase.creating') }}';
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

            // Reset file uploads
            selectedFiles = [];
            document.getElementById('filePreviewContainer').innerHTML = '';
            document.getElementById('filePreviews').style.display = 'none';
            document.getElementById('fileInput').value = '';

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
            submitBtn.innerHTML = '<i class="fas fa-star me-2"></i>{{ __('showcase.create_showcase') }}';
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

        // File Upload Functions
        function initFileUpload() {
            const fileInput = document.getElementById('fileInput');
            const browseBtn = document.getElementById('browseFiles');
            const uploadZone = document.getElementById('uploadZone');
            const fileUploadArea = document.getElementById('fileUploadArea');

            if (!fileInput || !browseBtn || !uploadZone) return;

            // Browse files button
            browseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fileInput.click();
            });

            // File input change
            fileInput.addEventListener('change', function(e) {
                handleFileSelection(Array.from(e.target.files));
            });

            // Drag and drop
            uploadZone.addEventListener('click', function() {
                fileInput.click();
            });

            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files);
                handleFileSelection(files);
            });
        }

        function handleFileSelection(files) {
            // Validate file count
            if (selectedFiles.length + files.length > 10) {
                alert('{{ __('showcase.max_files_exceeded') }}');
                return;
            }

            // Validate and add files
            files.forEach(file => {
                if (validateFile(file)) {
                    selectedFiles.push(file);
                    addFilePreview(file);
                }
            });

            updateFileInput();
            toggleFilePreviewsVisibility();
        }

        function validateFile(file) {
            // Check file size (50MB max)
            if (file.size > 50 * 1024 * 1024) {
                alert(`{{ __('showcase.file_too_large', ['filename' => '']) }}`.replace(':filename', file.name));
                return false;
            }

            // Check file type
            const allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/octet-stream' // For CAD files
            ];

            const allowedExtensions = [
                'jpg', 'jpeg', 'png', 'gif', 'webp',
                'pdf', 'doc', 'docx',
                'dwg', 'dxf', 'step', 'stp', 'stl', 'obj', 'iges', 'igs'
            ];

            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                alert(`{{ __('showcase.file_type_not_supported', ['filename' => '']) }}`.replace(':filename', file.name));
                return false;
            }

            return true;
        }

        function addFilePreview(file) {
            const previewContainer = document.getElementById('filePreviewContainer');
            const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

            const isImage = file.type.startsWith('image/');
            const fileSize = formatFileSize(file.size);
            const fileIcon = getFileIcon(file);

            const previewHtml = `
                <div class="col-md-6 mb-2">
                    <div class="file-preview-item ${isImage ? 'image-preview' : ''}" data-file-id="${fileId}">
                        <button type="button" class="remove-file" onclick="removeFile('${fileId}')">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="file-icon">
                                ${isImage ?
                                    `<img src="${URL.createObjectURL(file)}" alt="${file.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 0.25rem;">` :
                                    `<i class="${fileIcon} fs-4"></i>`
                                }
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium text-truncate" style="max-width: 150px;" title="${file.name}">
                                    ${file.name}
                                </div>
                                <div class="file-size">${fileSize}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            previewContainer.insertAdjacentHTML('beforeend', previewHtml);
        }

        function removeFile(fileId) {
            // Remove from selectedFiles array
            const previewElement = document.querySelector(`[data-file-id="${fileId}"]`);
            if (previewElement) {
                const fileName = previewElement.querySelector('.fw-medium').textContent.trim();
                selectedFiles = selectedFiles.filter(file => file.name !== fileName);
                previewElement.closest('.col-md-6').remove();
                updateFileInput();
                toggleFilePreviewsVisibility();
            }
        }

        function updateFileInput() {
            // Create new DataTransfer object to update file input
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            document.getElementById('fileInput').files = dt.files;
        }

        function toggleFilePreviewsVisibility() {
            const filePreviews = document.getElementById('filePreviews');
            if (selectedFiles.length > 0) {
                filePreviews.style.display = 'block';
            } else {
                filePreviews.style.display = 'none';
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function getFileIcon(file) {
            const extension = file.name.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
                return 'fas fa-image text-success';
            } else if (extension === 'pdf') {
                return 'fas fa-file-pdf text-danger';
            } else if (['doc', 'docx'].includes(extension)) {
                return 'fas fa-file-word text-primary';
            } else if (['dwg', 'dxf', 'step', 'stp', 'stl', 'obj', 'iges', 'igs'].includes(extension)) {
                return 'fas fa-cube text-info';
            } else {
                return 'fas fa-file text-secondary';
            }
        }

        // Make removeFile function global
        window.removeFile = removeFile;
    }
</script>
@endpush
