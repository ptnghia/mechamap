/**
 * Rich Text Editor with Image Upload
 * MechaMap - Showcase Comments & Ratings
 */

class RichTextEditor {
    constructor(editorElement) {
        this.editor = editorElement;
        this.editorId = this.editor.dataset.editorId;
        this.content = this.editor.querySelector('.editor-content');
        this.hiddenInput = this.editor.querySelector(`#${this.editorId}-input`);
        this.toolbar = this.editor.querySelector('.editor-toolbar');

        this.init();
    }

    init() {
        this.bindToolbarEvents();
        this.bindContentEvents();
        this.bindImageUpload();
        this.bindLinkModal();
        this.updateHiddenInput();
    }

    bindToolbarEvents() {
        this.toolbar.addEventListener('click', (e) => {
            if (e.target.classList.contains('toolbar-btn') || e.target.closest('.toolbar-btn')) {
                e.preventDefault();
                const btn = e.target.classList.contains('toolbar-btn') ? e.target : e.target.closest('.toolbar-btn');
                const command = btn.dataset.command;

                this.executeCommand(command);
                this.updateToolbarState();
            }
        });
    }

    bindContentEvents() {
        this.content.addEventListener('input', () => {
            this.updateHiddenInput();
        });

        this.content.addEventListener('keyup', () => {
            this.updateToolbarState();
        });

        this.content.addEventListener('mouseup', () => {
            this.updateToolbarState();
        });

        // Prevent default drag and drop, handle custom image upload
        this.content.addEventListener('dragover', (e) => {
            e.preventDefault();
        });

        this.content.addEventListener('drop', (e) => {
            e.preventDefault();
            const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            if (files.length > 0) {
                this.uploadImages(files);
            }
        });
    }

    executeCommand(command) {
        this.content.focus();

        switch (command) {
            case 'createLink':
                this.showLinkModal();
                break;
            case 'insertImage':
                this.showImageModal();
                break;
            default:
                document.execCommand(command, false, null);
                break;
        }
    }

    updateToolbarState() {
        const buttons = this.toolbar.querySelectorAll('.toolbar-btn[data-command]');
        buttons.forEach(btn => {
            const command = btn.dataset.command;
            if (['bold', 'italic', 'underline', 'insertUnorderedList', 'insertOrderedList'].includes(command)) {
                if (document.queryCommandState(command)) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            }
        });
    }

    updateHiddenInput() {
        this.hiddenInput.value = this.content.innerHTML;
    }

    showImageModal() {
        const modal = new bootstrap.Modal(document.getElementById(`${this.editorId}-image-modal`));
        modal.show();
    }

    showLinkModal() {
        const selection = window.getSelection();
        const selectedText = selection.toString();

        const linkTextInput = document.getElementById(`${this.editorId}-link-text`);
        if (selectedText) {
            linkTextInput.value = selectedText;
        }

        const modal = new bootstrap.Modal(document.getElementById(`${this.editorId}-link-modal`));
        modal.show();
    }

    bindImageUpload() {
        const imageUpload = document.getElementById(`${this.editorId}-image-upload`);
        const imageUrl = document.getElementById(`${this.editorId}-image-url`);
        const imagePreview = document.getElementById(`${this.editorId}-image-preview`);
        const insertBtn = document.getElementById(`${this.editorId}-insert-image`);

        if (!imageUpload) return;

        imageUpload.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            this.previewImages(files, imagePreview);
        });

        imageUrl.addEventListener('input', (e) => {
            const url = e.target.value.trim();
            if (url && this.isValidImageUrl(url)) {
                this.previewImageUrl(url, imagePreview);
            }
        });

        insertBtn.addEventListener('click', () => {
            this.insertImagesFromModal();
        });
    }

    bindLinkModal() {
        const insertBtn = document.getElementById(`${this.editorId}-insert-link`);
        if (!insertBtn) return;

        insertBtn.addEventListener('click', () => {
            this.insertLinkFromModal();
        });
    }

    previewImages(files, previewContainer) {
        previewContainer.innerHTML = '';

        files.forEach((file, index) => {
            if (file.type.startsWith('image/') && file.size <= 5 * 1024 * 1024) { // 5MB limit
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewItem = this.createPreviewItem(e.target.result, index);
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    previewImageUrl(url, previewContainer) {
        previewContainer.innerHTML = '';
        const previewItem = this.createPreviewItem(url, 'url');
        previewContainer.appendChild(previewItem);
    }

    createPreviewItem(src, index) {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.innerHTML = `
            <img src="${src}" alt="Preview">
            <button type="button" class="remove-preview" data-index="${index}">×</button>
        `;

        div.querySelector('.remove-preview').addEventListener('click', () => {
            div.remove();
        });

        return div;
    }

    insertImagesFromModal() {
        const imageUpload = document.getElementById(`${this.editorId}-image-upload`);
        const imageUrl = document.getElementById(`${this.editorId}-image-url`);
        const modal = bootstrap.Modal.getInstance(document.getElementById(`${this.editorId}-image-modal`));

        // Handle file uploads
        if (imageUpload.files.length > 0) {
            const files = Array.from(imageUpload.files);
            this.uploadImages(files);
        }

        // Handle URL
        if (imageUrl.value.trim()) {
            this.insertImageByUrl(imageUrl.value.trim());
        }

        // Reset and close modal
        imageUpload.value = '';
        imageUrl.value = '';
        document.getElementById(`${this.editorId}-image-preview`).innerHTML = '';
        modal.hide();
    }

    uploadImages(files) {
        const formData = new FormData();
        files.forEach(file => {
            formData.append('images[]', file);
        });
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Show loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải lên...';
        loadingDiv.className = 'text-center text-muted p-2';
        this.content.appendChild(loadingDiv);

        // Use jQuery AJAX for simplicity
        $.ajax({
            url: '/upload-images',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                loadingDiv.remove();
                if (response.success) {
                    response.images.forEach(imageUrl => {
                        this.insertImageByUrl(imageUrl);
                    });
                } else {
                    alert('Lỗi upload hình ảnh: ' + response.message);
                }
            },
            error: (xhr, status, error) => {
                loadingDiv.remove();
                console.error('Upload error:', error);
                alert('Có lỗi xảy ra khi upload hình ảnh');
            }
        });
    }

    insertImageByUrl(url) {
        this.content.focus();
        const img = document.createElement('img');
        img.src = url;
        img.style.maxWidth = '100%';
        img.style.height = 'auto';

        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(img);
            range.collapse(false);
        } else {
            this.content.appendChild(img);
        }

        this.updateHiddenInput();
    }

    insertLinkFromModal() {
        const linkUrl = document.getElementById(`${this.editorId}-link-url`).value.trim();
        const linkText = document.getElementById(`${this.editorId}-link-text`).value.trim();
        const modal = bootstrap.Modal.getInstance(document.getElementById(`${this.editorId}-link-modal`));

        if (linkUrl) {
            this.content.focus();
            const link = document.createElement('a');
            link.href = linkUrl;
            link.textContent = linkText || linkUrl;
            link.target = '_blank';

            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                range.deleteContents();
                range.insertNode(link);
                range.collapse(false);
            }

            this.updateHiddenInput();
        }

        // Reset and close modal
        document.getElementById(`${this.editorId}-link-url`).value = '';
        document.getElementById(`${this.editorId}-link-text`).value = '';
        modal.hide();
    }

    isValidImageUrl(url) {
        return /\.(jpg|jpeg|png|gif|webp)$/i.test(url);
    }
}

// Initialize all rich text editors on page load
document.addEventListener('DOMContentLoaded', function() {
    const editors = document.querySelectorAll('.rich-text-editor');
    editors.forEach(editor => {
        new RichTextEditor(editor);
    });
});

// Export for manual initialization
window.RichTextEditor = RichTextEditor;
