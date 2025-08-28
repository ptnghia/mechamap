{{-- Enhanced Comment Form with Rich Text Editor and Image Upload --}}
<div class="comment-form mb-4" id="{{ $formId ?? 'main-comment-form' }}">
    <form action="{{ route('showcase.comment', $showcase) }}" method="POST" enctype="multipart/form-data" class="comment-form-enhanced">
        @csrf
        @if(isset($parentId))
            <input type="hidden" name="parent_id" value="{{ $parentId }}">
        @endif

        <div class="d-flex gap-3">
            <img src="{{ auth()->user()->avatar_url ?? route('avatar.generate', ['initial' => strtoupper(substr(auth()->user()->name, 0, 1)), 'size' => 40]) }}"
                class="rounded-circle" width="40" height="40" alt="Avatar của bạn">

            <div class="flex-grow-1">
                {{-- TinyMCE Editor Component --}}
                <x-tinymce-editor
                    name="content"
                    id="comment-editor-{{ $formId ?? 'main' }}"
                    placeholder="{{ $placeholder ?? 'Viết bình luận của bạn...' }}"
                    context="comment"
                    :height="100"
                    :required="true"
                    value=""
                />

                {{-- Additional Image Upload Section --}}
                <div class="mt-2">
                    <label class="form-label small">
                        <i class="fas fa-images"></i>
                        Đính kèm ảnh (tùy chọn)
                    </label>
                    <input type="file" name="images[]" id="comment-images-{{ $formId ?? 'main' }}"
                           accept="image/*" multiple class="form-control form-control-sm">
                    <small class="text-muted">Chọn tối đa 5 ảnh (JPG, PNG, GIF, WebP). Kích thước tối đa: 5MB mỗi ảnh.</small>

                    {{-- Image Preview --}}
                    <div class="image-preview mt-2" id="image-preview-{{ $formId ?? 'main' }}"></div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="comment-actions">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Hỗ trợ định dạng văn bản và ảnh
                        </small>
                    </div>

                    <div class="submit-actions">
                        @if(isset($parentId))
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="cancelReply({{ $parentId }})">
                                Hủy
                            </button>
                        @endif
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-paper-plane"></i>
                            {{ isset($parentId) ? __('common.labels.replies') : 'Gửi bình luận' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.comment-editor {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
    background-color: #fff;
}

.comment-editor:focus {
    outline: none;
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.comment-editor:empty:before {
    content: attr(data-placeholder);
    color: #6c757d;
    font-style: italic;
}

.editor-toolbar {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
}

.image-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.image-preview-item {
    position: relative;
    display: inline-block;
}

.image-preview-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.image-preview-item .remove-image {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.comment-form-enhanced {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    border: 1px solid #e9ecef;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCommentForm('{{ $formId ?? "main" }}');
});

function initializeCommentForm(formId) {
    const imageInput = document.getElementById(`comment-images-${formId}`);
    const imagePreview = document.getElementById(`image-preview-${formId}`);

    // Image upload functionality
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            previewImages(this.files, imagePreview);
        });
    }
}

function previewImages(files, previewContainer) {
    previewContainer.innerHTML = '';

    if (files.length > 5) {
        alert('Chỉ được chọn tối đa 5 ảnh.');
        return;
    }

    Array.from(files).forEach((file, index) => {
        if (file.size > 5 * 1024 * 1024) {
            alert(`Ảnh "${file.name}" quá lớn. Kích thước tối đa: 5MB.`);
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'image-preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-image" onclick="removeImagePreview(this, ${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            previewContainer.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

function removeImagePreview(button, index) {
    const previewItem = button.closest('.image-preview-item');
    previewItem.remove();

    // Reset file input to remove the file
    const fileInput = button.closest('.comment-form').querySelector('input[type="file"]');
    const dt = new DataTransfer();
    const files = fileInput.files;

    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }

    fileInput.files = dt.files;
}

function cancelReply(parentId) {
    const replyForm = document.getElementById(`reply-form-${parentId}`);
    if (replyForm) {
        replyForm.style.display = 'none';
    }
}
</script>
