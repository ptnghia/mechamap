@extends('dashboard.layouts.app')

@section('title', __('showcase.edit.title'))

@push('styles')
<style>
    .showcase-edit-form {
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

    .current-image {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
    }

    .image-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 0.375rem;
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

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #fff;
    }

    .file-upload-area:hover {
        border-color: #007bff;
        background-color: #f8f9ff;
    }

    .existing-files {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem;
        border-bottom: 1px solid #f1f3f4;
    }

    .file-item:last-child {
        border-bottom: none;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .file-icon {
        width: 32px;
        height: 32px;
        background: #007bff;
        color: white;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-approved {
        background: #d1edff;
        color: #0c5460;
    }

    .status-featured {
        background: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .showcase-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('dashboard-content')
<div class="showcase-edit-form">
    <!-- Header -->
    <div class="showcase-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-1">{{ __('showcase.edit.title') }}</h3>
                <p class="mb-0 opacity-75">{{ $showcase->title }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="status-badge status-{{ $showcase->status }}">
                    {{ __('showcase.status.' . $showcase->status) }}
                </span>
            </div>
        </div>
    </div>

    <form action="{{ route('dashboard.community.showcases.update', $showcase) }}" method="POST" enctype="multipart/form-data" id="showcaseEditForm">
        @csrf
        @method('PATCH')

        <!-- Basic Information Section -->
        <div class="form-section">
            <h5><i class="fas fa-info-circle me-2"></i>{{ __('showcase.edit.basic_info') }}</h5>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="title" class="form-label">{{ __('showcase.edit.title') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title', $showcase->title) }}" 
                           placeholder="{{ __('showcase.edit.title_placeholder') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">{{ __('showcase.edit.category') }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">{{ __('showcase.edit.select_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('category_id', $showcase->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('showcase.edit.current_status') }}</label>
                    <div class="form-control-plaintext">
                        <span class="status-badge status-{{ $showcase->status }}">
                            {{ __('showcase.status.' . $showcase->status) }}
                        </span>
                    </div>
                    <small class="form-text text-muted">{{ __('showcase.edit.status_help') }}</small>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">{{ __('showcase.edit.description') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="5" 
                              placeholder="{{ __('showcase.edit.description_placeholder') }}" required>{{ old('description', $showcase->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Current Media Section -->
        @if($showcase->cover_image || ($showcase->images && $showcase->images->count() > 0))
            <div class="form-section">
                <h5><i class="fas fa-images me-2"></i>{{ __('showcase.edit.current_media') }}</h5>
                
                @if($showcase->cover_image)
                    <div class="mb-3">
                        <label class="form-label">{{ __('showcase.edit.current_cover') }}</label>
                        <div>
                            <img src="{{ asset('storage/' . $showcase->cover_image) }}" 
                                 class="current-image" alt="Current Cover">
                        </div>
                    </div>
                @endif

                @if($showcase->images && $showcase->images->count() > 0)
                    <div class="existing-files">
                        <h6>{{ __('showcase.edit.current_images') }}</h6>
                        <div class="row">
                            @foreach($showcase->images as $image)
                                <div class="col-md-3 mb-2">
                                    <div class="file-item">
                                        <img src="{{ asset('storage/' . $image->file_path) }}" 
                                             class="current-image w-100" alt="Current Image">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- New Media Upload Section -->
        <div class="form-section">
            <h5><i class="fas fa-upload me-2"></i>{{ __('showcase.edit.new_media') }}</h5>
            
            <!-- New Image Upload -->
            <div class="mb-4">
                <label class="form-label">{{ __('showcase.edit.add_images') }}</label>
                <div class="file-upload-area" id="imageUploadArea">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                    <h6>{{ __('showcase.edit.drag_images') }}</h6>
                    <p class="text-muted">{{ __('showcase.edit.image_formats') }}</p>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="d-none">
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('images').click()">
                        {{ __('showcase.edit.select_images') }}
                    </button>
                </div>
                <div id="imagePreviews" class="mt-3"></div>
            </div>

            <!-- New Attachment Upload -->
            <div class="mb-3">
                <label class="form-label">{{ __('showcase.edit.add_attachments') }}</label>
                <div class="file-upload-area" id="attachmentUploadArea">
                    <i class="fas fa-paperclip fa-2x text-muted mb-2"></i>
                    <h6>{{ __('showcase.edit.drag_files') }}</h6>
                    <p class="text-muted">{{ __('showcase.edit.file_formats') }}</p>
                    <input type="file" id="attachments" name="attachments[]" multiple class="d-none">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('attachments').click()">
                        {{ __('showcase.edit.select_files') }}
                    </button>
                </div>
                <div id="attachmentList" class="mt-3"></div>
            </div>
        </div>

        <!-- Current Attachments -->
        @if($showcase->attachments && $showcase->attachments->count() > 0)
            <div class="form-section">
                <h5><i class="fas fa-paperclip me-2"></i>{{ __('showcase.edit.current_attachments') }}</h5>
                <div class="existing-files">
                    @foreach($showcase->attachments as $attachment)
                        <div class="file-item">
                            <div class="file-info">
                                <div class="file-icon">
                                    <i class="fas fa-file"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $attachment->file_name }}</h6>
                                    <small class="text-muted">
                                        {{ number_format($attachment->file_size / 1024, 2) }} KB
                                    </small>
                                </div>
                            </div>
                            <div>
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                   class="btn btn-outline-primary btn-sm me-2" download>
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                        onclick="confirmDeleteAttachment({{ $attachment->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Additional Details Section -->
        <div class="form-section">
            <h5><i class="fas fa-cogs me-2"></i>{{ __('showcase.edit.additional_details') }}</h5>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="tags" class="form-label">{{ __('showcase.edit.tags') }}</label>
                    <div class="tag-input" id="tagInput">
                        <input type="text" id="tagInputField" placeholder="{{ __('showcase.edit.add_tags') }}" 
                               style="border: none; outline: none; flex: 1; min-width: 120px;">
                    </div>
                    <input type="hidden" name="tags" id="tagsHidden" value="{{ implode(',', $showcase->tags ?? []) }}">
                    <small class="form-text text-muted">{{ __('showcase.edit.tags_help') }}</small>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                               {{ old('is_featured', $showcase->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">
                            {{ __('showcase.edit.featured') }}
                        </label>
                        <small class="form-text text-muted d-block">{{ __('showcase.edit.featured_help') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('dashboard.community.showcases.show', $showcase) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>{{ __('common.back') }}
            </a>
            <div>
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-save me-2"></i>{{ __('showcase.edit.update') }}
                </button>
                <a href="{{ route('dashboard.community.showcases.index') }}" class="btn btn-outline-secondary">
                    {{ __('common.cancel') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize file upload handlers
    initializeFileUploads();
    
    // Initialize tag system with existing tags
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
    
    // Load existing tags
    let tags = tagsHidden.value ? tagsHidden.value.split(',').filter(tag => tag.trim()) : [];
    
    // Display existing tags
    updateTagDisplay();
    
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
    const form = document.getElementById('showcaseEditForm');
    
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const categoryId = document.getElementById('category_id').value;
        
        if (!title || !description || !categoryId) {
            e.preventDefault();
            alert('{{ __("showcase.edit.validation_error") }}');
            return false;
        }
    });
}

function confirmDeleteAttachment(attachmentId) {
    if (confirm('{{ __("showcase.edit.delete_attachment_confirm") }}')) {
        // Create a form to delete the attachment
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('dashboard.community.showcases.index') }}/attachments/${attachmentId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
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
