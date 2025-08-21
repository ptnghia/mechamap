{{-- CKEditor5 Comment Component for Showcase Comments --}}
@props([
    'name' => 'content',
    'placeholder' => 'Viết bình luận của bạn...',
    'value' => '',
    'id' => null,
    'required' => false,
    'minHeight' => '120px'
])

@php
    $editorId = $id ?? 'ckeditor-' . uniqid();
@endphp

<div class="ckeditor5-comment-wrapper" data-editor-id="{{ $editorId }}">
    {{-- CKEditor5 Container --}}
    <div class="ckeditor5-container">
        <textarea
            id="{{ $editorId }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            style="display: none;"
            data-required="{{ $required ? 'true' : 'false' }}"
        >{!! $value !!}</textarea>
    </div>
</div>

<style>
.ckeditor5-comment-wrapper {
    margin-bottom: 1rem;
}

.ckeditor5-container {
    border: 1px solid #e3e6f0;
    border-radius: 0.375rem;
    overflow: hidden;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.ckeditor5-container:focus-within {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.ckeditor5-container .ck-editor {
    border: none !important;
}

.ckeditor5-container .ck-toolbar {
    border: none !important;
    border-bottom: 1px solid #e3e6f0 !important;
    background: #f8f9fa !important;
    padding: 0.5rem;
}

.ckeditor5-container .ck-editor__editable {
    border: none !important;
    padding: 0.75rem;
    min-height: {{ $minHeight }};
    font-size: 0.875rem;
    line-height: 1.5;
}

.ckeditor5-container .ck-editor__editable:focus {
    box-shadow: none !important;
    outline: none !important;
}

.ckeditor5-container .ck-button {
    border-radius: 0.25rem;
    margin: 0 0.125rem;
}

.ckeditor5-container .ck-button:hover {
    background: #e9ecef !important;
}

.ckeditor5-container .ck-button.ck-on {
    background: #6366f1 !important;
    color: white !important;
}

.ckeditor5-container .ck-toolbar__separator {
    background: #dee2e6 !important;
    margin: 0 0.25rem;
}

/* Placeholder styling */
.ckeditor5-container .ck-editor__editable.ck-placeholder::before {
    color: #6c757d;
    font-style: italic;
}

/* Link styling */
.ckeditor5-container .ck-content a {
    color: #6366f1;
    text-decoration: underline;
}

.ckeditor5-container .ck-content a:hover {
    color: #4f46e5;
}

/* List styling */
.ckeditor5-container .ck-content ul,
.ckeditor5-container .ck-content ol {
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}

.ckeditor5-container .ck-content li {
    margin: 0.25rem 0;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .ckeditor5-container .ck-toolbar {
        padding: 0.375rem;
    }

    .ckeditor5-container .ck-toolbar .ck-toolbar__items {
        flex-wrap: wrap;
        gap: 0.125rem;
    }

    .ckeditor5-container .ck-button {
        padding: 0.25rem;
        font-size: 0.75rem;
    }

    .ckeditor5-container .ck-editor__editable {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCKEditor5Comment('{{ $editorId }}');
});

function initializeCKEditor5Comment(editorId) {
    const editorElement = document.getElementById(editorId);
    if (!editorElement) {
        console.error('CKEditor5 element not found:', editorId);
        return;
    }

    // Check if CKEditor5 is loaded
    if (typeof ClassicEditor === 'undefined') {
        console.error('CKEditor5 not loaded, falling back to textarea');
        // Fallback to textarea
        editorElement.style.display = 'block';
        editorElement.style.minHeight = '{{ $minHeight }}';
        editorElement.classList.add('form-control');
        return;
    }

    ClassicEditor
        .create(editorElement, {
            toolbar: {
                items: [
                    'bold', 'italic', '|',
                    'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'undo', 'redo'
                ]
            },
            language: 'vi',
            placeholder: '{{ $placeholder }}',
            link: {
                decorators: {
                    openInNewTab: {
                        mode: 'manual',
                        label: 'Mở trong tab mới',
                        attributes: {
                            target: '_blank',
                            rel: 'noopener noreferrer'
                        }
                    }
                }
            },
            typing: {
                transformations: {
                    remove: [
                        'enDash',
                        'emDash',
                        'oneHalf',
                        'oneThird',
                        'twoThirds',
                        'oneForth',
                        'threeQuarters'
                    ]
                }
            }
        })
        .then(editor => {
            // Store editor instance
            window[`ckeditor_${editorId}`] = editor;

            // Auto-resize editor
            editor.editing.view.change(writer => {
                writer.setStyle('min-height', '{{ $minHeight }}', editor.editing.view.document.getRoot());
            });

            // Handle form submission with validation
            const form = editorElement.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Get editor content
                    const content = editor.getData().trim();
                    const isRequired = editorElement.dataset.required === 'true';

                    // Custom validation for required fields
                    if (isRequired && (!content || content === '<p></p>' || content === '')) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Focus the editor
                        editor.editing.view.focus();

                        // Show validation message
                        alert('Vui lòng nhập nội dung bình luận.');
                        return false;
                    }

                    // Update textarea value
                    editorElement.value = content;
                });
            }

            console.log('CKEditor5 initialized for:', editorId);
        })
        .catch(error => {
            console.error('Error initializing CKEditor5:', error);
            // Fallback to textarea
            editorElement.style.display = 'block';
            editorElement.style.minHeight = '{{ $minHeight }}';
            editorElement.classList.add('form-control');
        });
}
</script>
