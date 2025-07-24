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
            {{ $required ? 'required' : '' }}
            style="display: none;"
        >{!! $value !!}</textarea>
    </div>
</div>

<style>
.ckeditor5-comment-wrapper {
    margin-bottom: 1rem;
}

.ckeditor5-container .ck-editor {
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.ckeditor5-container .ck-editor:focus-within {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.ckeditor5-container .ck-editor__editable {
    min-height: {{ $minHeight }};
    padding: 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    border: none;
    border-radius: 0 0 0.375rem 0.375rem;
}

.ckeditor5-container .ck-toolbar {
    border-radius: 0.375rem 0.375rem 0 0;
    border-bottom: 1px solid #dee2e6;
    background-color: #f8f9fa;
    padding: 0.5rem;
}

.ckeditor5-container .ck-toolbar .ck-toolbar__items {
    gap: 0.25rem;
}

.ckeditor5-container .ck-button {
    border-radius: 0.25rem;
    padding: 0.25rem 0.5rem;
    transition: all 0.15s ease-in-out;
}

.ckeditor5-container .ck-button:hover {
    background-color: #e9ecef;
}

.ckeditor5-container .ck-button.ck-on {
    background-color: #0d6efd;
    color: white;
}

/* Dark mode support */
[data-bs-theme="dark"] .ckeditor5-container .ck-editor {
    border-color: #495057;
}

[data-bs-theme="dark"] .ckeditor5-container .ck-editor__editable {
    background-color: #212529;
    color: #fff;
}

[data-bs-theme="dark"] .ckeditor5-container .ck-toolbar {
    background-color: #343a40;
    border-color: #495057;
}

[data-bs-theme="dark"] .ckeditor5-container .ck-button:hover {
    background-color: #495057;
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
    if (typeof ClassicEditor === 'undefined') {
        console.error('CKEditor5 not loaded');
        return;
    }

    const editorElement = document.getElementById(editorId);
    if (!editorElement) {
        console.error('Editor element not found:', editorId);
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
            
            // Handle form submission
            const form = editorElement.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    // CKEditor5 automatically updates the textarea value
                    // No additional action needed
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
