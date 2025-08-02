{{-- TinyMCE Editor Component for MechaMap --}}
@props([
    'name',
    'id' => '',
    'value' => '',
    'placeholder' => 'Nhập nội dung của bạn...',
    'context' => 'comment',
    'height' => 200,
    'required' => false,
    'class' => '',
    'disabled' => false
])

@php
    $editorId = $id ?: $name;
    $editorClass = 'form-control tinymce-editor ' . $class;

    // Generate unique ID if needed
    if (!$id) {
        $editorId = $name . '_' . uniqid();
    }
@endphp

<div class="tinymce-editor-wrapper" data-context="{{ $context }}">
    {{-- Textarea for TinyMCE editor --}}
    <textarea
        name="{{ $name }}"
        id="{{ $editorId }}"
        class="{{ $editorClass }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>

    {{-- Error display --}}
    @error($name)
        <div class="invalid-feedback d-block" id="{{ $editorId }}-error">
            {{ $message }}
        </div>
    @enderror

    {{-- Loading indicator --}}
    <div class="tinymce-loading" id="{{ $editorId }}-loading" style="display: none;">
        <div class="d-flex align-items-center justify-content-center p-3">
            <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <span class="text-muted">Đang khởi tạo editor...</span>
        </div>
    </div>
</div>

{{-- Push TinyMCE scripts to the end of the page --}}
@push('scripts')
{{-- TinyMCE CDN --}}
<script src="https://cdn.tiny.cloud/1/m3nymn6hdlv8nqnf4g88r0ccz9n86ks2aw92v0opuy7sx20y/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

{{-- TinyMCE Configuration and Uploader --}}
<script src="{{ asset('js/tinymce-config.js') }}"></script>
<script src="{{ asset('js/tinymce-uploader.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeTinyMCEEditor('{{ $editorId }}', '{{ $context }}', {
        height: {{ $height }},
        placeholder: '{{ $placeholder }}',
        required: {{ $required ? 'true' : 'false' }}
    });
});

/**
 * Initialize TinyMCE editor with unified configuration
 */
function initializeTinyMCEEditor(editorId, context, options = {}) {
    const textarea = document.getElementById(editorId);
    const loadingDiv = document.getElementById(editorId + '-loading');

    if (!textarea) {
        console.error('TinyMCE: Textarea not found:', editorId);
        return;
    }

    // Show loading indicator
    if (loadingDiv) {
        loadingDiv.style.display = 'block';
    }

    // Get configuration from TinyMCEConfig class
    const config = new TinyMCEConfig();
    let editorConfig;

    switch(context) {
        case 'admin':
            editorConfig = config.getAdminConfig(`#${editorId}`, options);
            break;
        case 'showcase':
            editorConfig = config.getShowcaseConfig(`#${editorId}`, options);
            break;
        case 'minimal':
            editorConfig = config.getMinimalConfig(`#${editorId}`, options);
            break;
        default:
            editorConfig = config.getCommentConfig(`#${editorId}`, options);
    }

    // Add custom setup function
    editorConfig.setup = function(editor) {
        // Add custom buttons
        TinyMCEConfig.addCustomButtons(editor);

        // Add event handlers
        TinyMCEConfig.addEventHandlers(editor);

        // Handle initialization
        editor.on('init', function() {
            // Hide loading indicator
            if (loadingDiv) {
                loadingDiv.style.display = 'none';
            }

            // Hide the original textarea (TinyMCE replaces it)
            textarea.style.display = 'none';

            console.log(`TinyMCE initialized for: ${editorId} (context: ${context})`);

            // Initialize drag & drop and paste handlers
            TinyMCEUploader.initDragDrop(editorId);
            TinyMCEUploader.initPasteHandler(editorId);
        });

        // Handle content change for validation
        editor.on('input keyup change', function() {
            const content = editor.getContent().trim();

            // Update hidden textarea
            textarea.value = content;

            // Trigger validation
            const event = new Event('input', { bubbles: true });
            textarea.dispatchEvent(event);

            // Remove validation errors if content exists
            if (content && options.required) {
                textarea.classList.remove('is-invalid');
                const errorDiv = document.getElementById(editorId + '-error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }

                // Remove error styling from TinyMCE container
                const tinyMCEContainer = editor.getContainer();
                if (tinyMCEContainer) {
                    tinyMCEContainer.classList.remove('is-invalid');
                }
            }
        });

        // Handle form submission
        editor.on('submit', function() {
            textarea.value = editor.getContent();
        });
    };

    // Add image upload handler
    editorConfig.images_upload_handler = function(blobInfo, success, failure, progress) {
        return TinyMCEUploader.uploadImage(blobInfo, success, failure, progress);
    };

    // Add file picker
    editorConfig.file_picker_callback = function(callback, value, meta) {
        TinyMCEUploader.filePicker(callback, value, meta);
    };

    // Initialize TinyMCE
    tinymce.init(editorConfig).catch(function(error) {
        console.error('TinyMCE initialization failed:', error);

        // Hide loading indicator on error
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }

        // Show textarea as fallback
        textarea.style.display = 'block';
    });
}

/**
 * Destroy TinyMCE editor instance
 */
function destroyTinyMCEEditor(editorId) {
    const editor = tinymce.get(editorId);
    if (editor) {
        editor.destroy();
    }
}

/**
 * Get content from TinyMCE editor
 */
function getTinyMCEContent(editorId) {
    const editor = tinymce.get(editorId);
    return editor ? editor.getContent() : '';
}

/**
 * Set content to TinyMCE editor
 */
function setTinyMCEContent(editorId, content) {
    const editor = tinymce.get(editorId);
    if (editor) {
        editor.setContent(content);
    }
}

/**
 * Focus TinyMCE editor
 */
function focusTinyMCEEditor(editorId) {
    const editor = tinymce.get(editorId);
    if (editor) {
        editor.focus();
    }
}
</script>
@endpush

{{-- Push TinyMCE styles --}}
@push('styles')
<style>
.tinymce-editor-wrapper {
    position: relative;
}

.tinymce-loading {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background: #f8f9fa;
    min-height: 100px;
}

/* TinyMCE drag over styling */
.tinymce-drag-over .tox-tinymce {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

/* TinyMCE validation styling */
.tox-tinymce.is-invalid {
    border-color: #dc3545 !important;
}

.tox-tinymce.is-invalid .tox-toolbar,
.tox-tinymce.is-invalid .tox-statusbar {
    border-color: #dc3545 !important;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .tox-tinymce {
        min-width: 100% !important;
    }

    .tox-toolbar__group {
        flex-wrap: wrap;
    }
}

/* Custom button styling */
.tox .tox-tbtn--enabled {
    background: #007bff;
    color: white;
}

.tox .tox-tbtn--enabled:hover {
    background: #0056b3;
}

/* Content area styling */
.tox .tox-edit-area__iframe {
    background: white;
}

/* Placeholder styling */
.tox .tox-edit-area__iframe body[data-mce-placeholder]:not(.mce-visualblocks)::before {
    color: #6c757d;
    font-style: italic;
    position: absolute;
    top: 8px;
    left: 8px;
}
</style>
@endpush
