@extends('dashboard.layouts.app')

@section('title', __('showcase.create.title'))

@push('styles')
<style>
    .showcase-create-form {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
    }

    .form-section h5 {
        color: #495057;
        margin-bottom: 1rem;
        font-weight: 600;
        border-bottom: 2px solid #007bff;
        padding-bottom: 0.5rem;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #fff;
    }

    .file-upload-area:hover {
        border-color: #007bff;
        background-color: #f8f9ff;
    }

    .file-upload-area.dragover {
        border-color: #007bff;
        background-color: #e3f2fd;
    }

    .image-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .preview-container {
        position: relative;
        display: inline-block;
        margin: 0.5rem;
    }

    .remove-preview {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        font-size: 12px;
        cursor: pointer;
    }

    .tag-input {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        min-height: 38px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.25rem;
    }

    .tag {
        background: #007bff;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .tag .remove-tag {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0;
        font-size: 0.75rem;
    }

    .progress-indicator {
        background: #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .progress-step {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .progress-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 15px;
        right: -50%;
        width: 100%;
        height: 2px;
        background: #dee2e6;
        z-index: 1;
    }

    .progress-step.active::after {
        background: #007bff;
    }

    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dee2e6;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        font-weight: bold;
        position: relative;
        z-index: 2;
    }

    .progress-step.active .step-circle {
        background: #007bff;
        color: white;
    }

    .progress-step.completed .step-circle {
        background: #28a745;
        color: white;
    }
</style>
@endpush

@section('dashboard-content')
<div class="showcase-create-form">
    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="progress-steps">
            <div class="progress-step active">
                <div class="step-circle">1</div>
                <small>{{ __('showcase.create.step_basic') }}</small>
            </div>
            <div class="progress-step">
                <div class="step-circle">2</div>
                <small>{{ __('showcase.create.step_media') }}</small>
            </div>
            <div class="progress-step">
                <div class="step-circle">3</div>
                <small>{{ __('showcase.create.step_details') }}</small>
            </div>
            <div class="progress-step">
                <div class="step-circle">4</div>
                <small>{{ __('showcase.create.step_review') }}</small>
            </div>
        </div>
    </div>

    <form action="{{ route('dashboard.community.showcases.store') }}" method="POST" enctype="multipart/form-data" id="showcaseForm">
        @csrf

        <!-- Basic Information Section -->
        <div class="form-section" id="section-basic">
            <h5><i class="fas fa-info-circle me-2"></i>{{ __('showcase.create.basic_info') }}</h5>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="title" class="form-label">{{ __('showcase.create.title') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" 
                           placeholder="{{ __('showcase.create.title_placeholder') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">{{ __('showcase.create.category') }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">{{ __('showcase.create.select_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="thread_id" class="form-label">{{ __('showcase.create.related_thread') }}</label>
                    <select class="form-select @error('thread_id') is-invalid @enderror" id="thread_id" name="thread_id">
                        <option value="">{{ __('showcase.create.no_thread') }}</option>
                        {{-- TODO: Load user's threads via AJAX --}}
                    </select>
                    @error('thread_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">{{ __('showcase.create.description') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="5" 
                              placeholder="{{ __('showcase.create.description_placeholder') }}" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Media Upload Section -->
        <div class="form-section" id="section-media">
            <h5><i class="fas fa-images me-2"></i>{{ __('showcase.create.media_files') }}</h5>
            
            <!-- Image Upload -->
            <div class="mb-4">
                <label class="form-label">{{ __('showcase.create.images') }}</label>
                <div class="file-upload-area" id="imageUploadArea">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                    <h6>{{ __('showcase.create.drag_images') }}</h6>
                    <p class="text-muted">{{ __('showcase.create.image_formats') }}</p>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="d-none">
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('images').click()">
                        {{ __('showcase.create.select_images') }}
                    </button>
                </div>
                <div id="imagePreviews" class="mt-3"></div>
            </div>

            <!-- Attachment Upload -->
            <div class="mb-3">
                <label class="form-label">{{ __('showcase.create.attachments') }}</label>
                <div class="file-upload-area" id="attachmentUploadArea">
                    <i class="fas fa-paperclip fa-2x text-muted mb-2"></i>
                    <h6>{{ __('showcase.create.drag_files') }}</h6>
                    <p class="text-muted">{{ __('showcase.create.file_formats') }}</p>
                    <input type="file" id="attachments" name="attachments[]" multiple class="d-none">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('attachments').click()">
                        {{ __('showcase.create.select_files') }}
                    </button>
                </div>
                <div id="attachmentList" class="mt-3"></div>
            </div>
        </div>

        <!-- Additional Details Section -->
        <div class="form-section" id="section-details">
            <h5><i class="fas fa-cogs me-2"></i>{{ __('showcase.create.additional_details') }}</h5>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="tags" class="form-label">{{ __('showcase.create.tags') }}</label>
                    <div class="tag-input" id="tagInput">
                        <input type="text" id="tagInputField" placeholder="{{ __('showcase.create.add_tags') }}" 
                               style="border: none; outline: none; flex: 1; min-width: 120px;">
                    </div>
                    <input type="hidden" name="tags" id="tagsHidden">
                    <small class="form-text text-muted">{{ __('showcase.create.tags_help') }}</small>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">
                            {{ __('showcase.create.featured') }}
                        </label>
                        <small class="form-text text-muted d-block">{{ __('showcase.create.featured_help') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('dashboard.community.showcases.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>{{ __('common.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>{{ __('showcase.create.submit') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize file upload handlers
    initializeFileUploads();
    
    // Initialize tag system
    initializeTagSystem();
    
    // Initialize form validation
    initializeFormValidation();
});

function initializeFileUploads() {
    // Image upload handling
    const imageInput = document.getElementById('images');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imagePreviews = document.getElementById('imagePreviews');
    
    imageInput.addEventListener('change', function(e) {
        handleImageFiles(e.target.files);
    });
    
    // Drag and drop for images
    imageUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    imageUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    imageUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        handleImageFiles(e.dataTransfer.files);
    });
    
    // Attachment upload handling
    const attachmentInput = document.getElementById('attachments');
    const attachmentUploadArea = document.getElementById('attachmentUploadArea');
    const attachmentList = document.getElementById('attachmentList');
    
    attachmentInput.addEventListener('change', function(e) {
        handleAttachmentFiles(e.target.files);
    });
}

function handleImageFiles(files) {
    const imagePreviews = document.getElementById('imagePreviews');
    
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.createElement('div');
                previewContainer.className = 'preview-container';
                previewContainer.innerHTML = `
                    <img src="${e.target.result}" class="image-preview" alt="Preview">
                    <button type="button" class="remove-preview" onclick="this.parentElement.remove()">×</button>
                `;
                imagePreviews.appendChild(previewContainer);
            };
            reader.readAsDataURL(file);
        }
    });
}

function handleAttachmentFiles(files) {
    const attachmentList = document.getElementById('attachmentList');
    
    Array.from(files).forEach(file => {
        const fileItem = document.createElement('div');
        fileItem.className = 'alert alert-info d-flex justify-content-between align-items-center';
        fileItem.innerHTML = `
            <span><i class="fas fa-file me-2"></i>${file.name} (${formatFileSize(file.size)})</span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        attachmentList.appendChild(fileItem);
    });
}

function initializeTagSystem() {
    const tagInput = document.getElementById('tagInputField');
    const tagContainer = document.getElementById('tagInput');
    const tagsHidden = document.getElementById('tagsHidden');
    let tags = [];
    
    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag(this.value.trim());
            this.value = '';
        }
    });
    
    function addTag(tagText) {
        if (tagText && !tags.includes(tagText)) {
            tags.push(tagText);
            updateTagDisplay();
            updateHiddenInput();
        }
    }
    
    function removeTag(tagText) {
        tags = tags.filter(tag => tag !== tagText);
        updateTagDisplay();
        updateHiddenInput();
    }
    
    function updateTagDisplay() {
        const existingTags = tagContainer.querySelectorAll('.tag');
        existingTags.forEach(tag => tag.remove());
        
        tags.forEach(tagText => {
            const tagElement = document.createElement('span');
            tagElement.className = 'tag';
            tagElement.innerHTML = `
                ${tagText}
                <button type="button" class="remove-tag" onclick="removeTag('${tagText}')">×</button>
            `;
            tagContainer.insertBefore(tagElement, tagInput);
        });
    }
    
    function updateHiddenInput() {
        tagsHidden.value = tags.join(',');
    }
    
    // Make removeTag function global
    window.removeTag = removeTag;
}

function initializeFormValidation() {
    const form = document.getElementById('showcaseForm');
    
    form.addEventListener('submit', function(e) {
        // Add any custom validation here
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const categoryId = document.getElementById('category_id').value;
        
        if (!title || !description || !categoryId) {
            e.preventDefault();
            alert('{{ __("showcase.create.validation_error") }}');
            return false;
        }
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush
