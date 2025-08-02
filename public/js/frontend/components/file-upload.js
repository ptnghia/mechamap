/**
 * File Upload Component JavaScript
 * JavaScript cho component upload file có thể tái sử dụng
 */

class FileUploadComponent {
    constructor(componentId) {
        this.componentId = componentId;
        this.component = document.getElementById(componentId);

        if (!this.component) {
            console.error(`FileUploadComponent: Component with ID ${componentId} not found`);
            return;
        }

        // Get component configuration from data attributes
        this.config = {
            name: this.component.dataset.name,
            fileTypes: JSON.parse(this.component.dataset.fileTypes || '[]'),
            maxSize: parseInt(this.component.dataset.maxSize),
            multiple: this.component.dataset.multiple === 'true',
            maxFiles: parseInt(this.component.dataset.maxFiles || '10'),
            showProgress: this.component.dataset.showProgress === 'true',
            showPreview: this.component.dataset.showPreview === 'true',
            dragDrop: this.component.dataset.dragDrop === 'true'
        };

        // Get DOM elements
        this.uploadArea = document.getElementById(`${componentId}-area`);
        this.uploadZone = document.getElementById(`${componentId}-zone`);
        this.fileInput = document.getElementById(`${componentId}-input`);
        this.browseBtn = document.getElementById(`${componentId}-browse`);
        this.previewsContainer = document.getElementById(`${componentId}-previews`);

        // Separate containers for images and files
        this.imagePreviewsSection = document.getElementById(`${componentId}-image-previews-section`);
        this.imagePreviewContainer = document.getElementById(`${componentId}-image-preview-container`);
        this.filePreviewsSection = document.getElementById(`${componentId}-file-previews-section`);
        this.filePreviewContainer = document.getElementById(`${componentId}-file-preview-container`);

        // Legacy container for backward compatibility
        this.previewContainer = document.getElementById(`${componentId}-preview-container`);

        this.progressContainer = document.getElementById(`${componentId}-progress`);
        this.errorsContainer = document.getElementById(`${componentId}-errors`);

        // State
        this.selectedFiles = [];
        this.isUploading = false;

        // Initialize
        this.init();
    }

    init() {
        // Check if already initialized
        if (this.component.hasAttribute('data-file-upload-initialized')) {
            console.warn(`FileUploadComponent ${this.componentId} is already initialized`);
            return;
        }

        this.setupEventListeners();
        this.clearErrors();

        // Mark as initialized
        this.component.setAttribute('data-file-upload-initialized', 'true');
    }

    setupEventListeners() {
        // Browse button click
        if (this.browseBtn) {
            this.browseBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation(); // Prevent event bubbling to upload zone
                this.fileInput.click();
            });
        }

        // File input change
        this.fileInput.addEventListener('change', (e) => {
            this.handleFileSelection(Array.from(e.target.files));
        });

        // Drag & drop events
        if (this.config.dragDrop) {
            this.setupDragDrop();
        }

        // Click on upload zone (but not on browse button)
        if (this.config.dragDrop && this.uploadZone) {
            this.uploadZone.addEventListener('click', (e) => {
                // Only trigger if click is not on browse button or its children
                if (!this.browseBtn || (!this.browseBtn.contains(e.target) && e.target !== this.browseBtn)) {
                    // Additional check: make sure we're not clicking on any button or link
                    if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A' &&
                        !e.target.closest('button') && !e.target.closest('a')) {
                        this.fileInput.click();
                    }
                }
            });
        }
    }

    setupDragDrop() {
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.uploadArea.addEventListener(eventName, this.preventDefaults, false);
            document.body.addEventListener(eventName, this.preventDefaults, false);
        });

        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            this.uploadArea.addEventListener(eventName, () => {
                this.uploadArea.classList.add('drag-over');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            this.uploadArea.addEventListener(eventName, () => {
                this.uploadArea.classList.remove('drag-over');
            }, false);
        });

        // Handle dropped files
        this.uploadArea.addEventListener('drop', (e) => {
            const files = Array.from(e.dataTransfer.files);
            this.handleFileSelection(files);
        }, false);
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    handleFileSelection(files) {
        this.clearErrors();

        // Validate file count for multiple uploads
        if (this.config.multiple) {
            const totalFiles = this.selectedFiles.length + files.length;
            if (totalFiles > this.config.maxFiles) {
                const currentCount = this.selectedFiles.length;
                const remainingSlots = this.config.maxFiles - currentCount;

                if (remainingSlots <= 0) {
                    this.showError(`Đã đạt giới hạn tối đa ${this.config.maxFiles} tệp. Vui lòng xóa bớt tệp hiện có trước khi thêm mới.`);
                } else {
                    this.showError(`Chỉ có thể thêm ${remainingSlots} tệp nữa (tối đa ${this.config.maxFiles} tệp). Bạn đang chọn ${files.length} tệp.`);
                }
                return;
            }
        } else {
            // Single file mode - replace existing file
            this.selectedFiles = [];
            if (this.previewContainer) {
                this.previewContainer.innerHTML = '';
            }
        }

        // Validate and add files
        const validFiles = [];
        for (const file of files) {
            if (this.validateFile(file)) {
                validFiles.push(file);
            }
        }

        if (validFiles.length > 0) {
            this.selectedFiles.push(...validFiles);
            this.updateFileInput();
            this.updatePreviews();
            this.emitEvent('filesAdded', { files: validFiles });
        }
    }

    validateFile(file) {
        // Check file size
        if (file.size > this.config.maxSize) {
            const maxSizeMB = (this.config.maxSize / (1024 * 1024)).toFixed(1);
            this.showError(`File "${file.name}" quá lớn. Kích thước tối đa: ${maxSizeMB}MB`);
            return false;
        }

        // Check file type
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const isValidType = this.config.fileTypes.some(type =>
            type.toLowerCase() === fileExtension
        );

        if (!isValidType) {
            const allowedTypes = this.config.fileTypes.map(t => t.toUpperCase()).join(', ');
            this.showError(`File "${file.name}" không được hỗ trợ. Các định dạng được phép: ${allowedTypes}`);
            return false;
        }

        return true;
    }

    updateFileInput() {
        // Update the actual file input with selected files
        const dt = new DataTransfer();
        this.selectedFiles.forEach(file => dt.items.add(file));
        this.fileInput.files = dt.files;
    }

    updatePreviews() {
        if (!this.config.showPreview) return;

        // Separate files by type
        const imageFiles = [];
        const regularFiles = [];

        this.selectedFiles.forEach((file, index) => {
            if (this.isImageFile(file)) {
                imageFiles.push({ file, index });
            } else {
                regularFiles.push({ file, index });
            }
        });

        // Show/hide main previews container
        if (this.previewsContainer) {
            if (this.selectedFiles.length > 0) {
                this.previewsContainer.classList.remove('d-none');
                this.updateFileCounter();
            } else {
                this.previewsContainer.classList.add('d-none');
            }
        }

        // Handle image previews
        this.updateImagePreviews(imageFiles);

        // Handle file previews
        this.updateFilePreviews(regularFiles);

        // Add "add more" button for multiple uploads
        if (this.config.multiple && this.selectedFiles.length < this.config.maxFiles) {
            this.addMoreButton();
        }

        // Hide upload zone if files are selected and not multiple
        if (!this.config.multiple && this.selectedFiles.length > 0) {
            this.uploadZone.style.display = 'none';
        } else if (this.selectedFiles.length === 0) {
            this.uploadZone.style.display = 'block';
        }
    }

    updateFileCounter() {
        if (!this.config.multiple) return;

        // Find or create file counter element
        let counterElement = this.previewsContainer.querySelector('.file-counter');
        if (!counterElement) {
            counterElement = document.createElement('div');
            counterElement.className = 'file-counter alert alert-info py-2 px-3 mb-2';
            this.previewsContainer.insertBefore(counterElement, this.previewsContainer.firstChild);
        }

        const currentCount = this.selectedFiles.length;
        const maxFiles = this.config.maxFiles;
        const remaining = maxFiles - currentCount;

        let counterClass = 'alert-info';
        let icon = 'fas fa-info-circle';

        if (remaining <= 1) {
            counterClass = 'alert-warning';
            icon = 'fas fa-exclamation-triangle';
        }
        if (remaining === 0) {
            counterClass = 'alert-danger';
            icon = 'fas fa-exclamation-circle';
        }

        counterElement.className = `file-counter alert ${counterClass} py-2 px-3 mb-2`;
        counterElement.innerHTML = `
            <i class="${icon} me-2"></i>
            <strong>${currentCount}/${maxFiles}</strong> tệp đã chọn
            ${remaining > 0 ? `(còn lại ${remaining} tệp)` : '(đã đạt giới hạn)'}
        `;
    }

    isImageFile(file) {
        return file.type.startsWith('image/') ||
               /\.(jpg|jpeg|png|gif|webp)$/i.test(file.name);
    }

    updateImagePreviews(imageFiles) {
        if (!this.imagePreviewsSection || !this.imagePreviewContainer) return;

        if (imageFiles.length > 0) {
            this.imagePreviewsSection.classList.remove('d-none');
            this.imagePreviewContainer.innerHTML = '';

            imageFiles.forEach(({ file, index }) => {
                this.addImagePreview(file, index);
            });
        } else {
            this.imagePreviewsSection.classList.add('d-none');
        }
    }

    updateFilePreviews(regularFiles) {
        if (!this.filePreviewsSection || !this.filePreviewContainer) return;

        if (regularFiles.length > 0) {
            this.filePreviewsSection.classList.remove('d-none');
            this.filePreviewContainer.innerHTML = '';

            regularFiles.forEach(({ file, index }) => {
                this.addFilePreview(file, index);
            });
        } else {
            this.filePreviewsSection.classList.add('d-none');
        }
    }

    addImagePreview(file, index) {
        const fileSize = this.formatFileSize(file.size);
        const fileExtension = file.name.split('.').pop().toUpperCase();

        const previewCol = document.createElement('div');
        previewCol.className = 'col-6 col-md-3 mb-2'; // 2 per row on mobile, 4 per row on desktop

        const previewItem = document.createElement('div');
        previewItem.className = 'image-preview-item';
        previewItem.innerHTML = `
            <button type="button" class="remove-file" onclick="fileUploadComponents['${this.componentId}'].removeFile(${index})">
                <i class="fas fa-times"></i>
            </button>
            <img src="${URL.createObjectURL(file)}" alt="${file.name}" class="image-thumbnail">
            <div class="image-info">
                <div class="image-format">${fileExtension}</div>
                <div class="image-size">${fileSize}</div>
            </div>
        `;

        // Add click to preview for images
        previewItem.addEventListener('click', (e) => {
            if (!e.target.closest('.remove-file')) {
                this.showImageModal(URL.createObjectURL(file), file.name);
            }
        });

        previewCol.appendChild(previewItem);
        this.imagePreviewContainer.appendChild(previewCol);
    }

    addFilePreview(file, index) {
        const fileSize = this.formatFileSize(file.size);
        const fileIcon = this.getFileIcon(file);

        const previewCol = document.createElement('div');
        previewCol.className = 'col-md-6 mb-2'; // 2 per row on desktop, 1 per row on mobile

        const previewItem = document.createElement('div');
        previewItem.className = 'file-preview-item';
        previewItem.innerHTML = `
            <button type="button" class="remove-file" onclick="fileUploadComponents['${this.componentId}'].removeFile(${index})">
                <i class="fas fa-times"></i>
            </button>
            <div class="d-flex align-items-center">
                <div class="file-icon">
                    <i class="${fileIcon}"></i>
                </div>
                <div class="file-info">
                    <div class="file-name" title="${file.name}">
                        ${this.truncateFileName(file.name, 30)}
                    </div>
                    <div class="file-size">${fileSize}</div>
                </div>
            </div>
        `;

        previewCol.appendChild(previewItem);
        this.filePreviewContainer.appendChild(previewCol);
    }

    addMoreButton() {
        // Add to file preview container (for non-images)
        if (this.filePreviewContainer) {
            const addMoreCol = document.createElement('div');
            addMoreCol.className = 'col-md-6 mb-2';

            const addMoreBtn = document.createElement('div');
            addMoreBtn.className = 'file-preview-item add-more-btn';
            addMoreBtn.innerHTML = `
                <div class="add-more-content">
                    <i class="fas fa-plus"></i>
                    <span>Thêm file</span>
                </div>
            `;

            addMoreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation(); // Prevent event bubbling
                this.fileInput.click();
            });

            addMoreCol.appendChild(addMoreBtn);
            this.filePreviewContainer.appendChild(addMoreCol);
        }
    }

    removeFile(index) {
        if (index >= 0 && index < this.selectedFiles.length) {
            const removedFile = this.selectedFiles.splice(index, 1)[0];
            this.updateFileInput();
            this.updatePreviews();
            this.emitEvent('fileRemoved', { file: removedFile, index });
        }
    }

    showProgress(progress = 0) {
        if (!this.config.showProgress || !this.progressContainer) return;

        this.progressContainer.classList.remove('d-none');
        const progressBar = this.progressContainer.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }

        this.component.classList.add('loading');
        this.isUploading = true;
    }

    hideProgress() {
        if (this.progressContainer) {
            this.progressContainer.classList.add('d-none');
        }

        this.component.classList.remove('loading');
        this.isUploading = false;
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'upload-error';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <span>${message}</span>
            <button type="button" class="close-error" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        this.errorsContainer.appendChild(errorDiv);
        this.uploadArea.classList.add('has-error');

        this.emitEvent('validationError', { message });
    }

    clearErrors() {
        this.errorsContainer.innerHTML = '';
        this.uploadArea.classList.remove('has-error');
    }

    // Utility methods
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    getFileIcon(file) {
        const extension = file.name.split('.').pop().toLowerCase();

        const iconMap = {
            // Images
            'jpg': 'fas fa-image text-success',
            'jpeg': 'fas fa-image text-success',
            'png': 'fas fa-image text-success',
            'gif': 'fas fa-image text-success',
            'webp': 'fas fa-image text-success',

            // Documents
            'pdf': 'fas fa-file-pdf text-danger',
            'doc': 'fas fa-file-word text-primary',
            'docx': 'fas fa-file-word text-primary',

            // CAD files
            'dwg': 'fas fa-cube text-info',
            'dxf': 'fas fa-cube text-info',
            'step': 'fas fa-cube text-info',
            'stp': 'fas fa-cube text-info',
            'stl': 'fas fa-cube text-info',
            'obj': 'fas fa-cube text-info',
            'iges': 'fas fa-cube text-info',
            'igs': 'fas fa-cube text-info'
        };

        return iconMap[extension] || 'fas fa-file text-secondary';
    }

    truncateFileName(name, maxLength) {
        if (name.length <= maxLength) return name;

        const extension = name.split('.').pop();
        const nameWithoutExt = name.substring(0, name.lastIndexOf('.'));
        const truncatedName = nameWithoutExt.substring(0, maxLength - extension.length - 4) + '...';

        return truncatedName + '.' + extension;
    }

    showImageModal(src, title) {
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        modal.innerHTML = `
            <div class="image-modal-content">
                <div class="image-modal-header">
                    <h5>${title}</h5>
                    <button type="button" class="btn-close-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="image-modal-body">
                    <img src="${src}" alt="${title}">
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.display = 'flex';

        // Close modal events
        const closeBtn = modal.querySelector('.btn-close-modal');
        const closeModal = () => {
            document.body.removeChild(modal);
        };

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // ESC key to close
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);
    }

    emitEvent(eventName, detail = {}) {
        const event = new CustomEvent(`fileUpload:${eventName}`, {
            detail: { componentId: this.componentId, ...detail }
        });
        this.component.dispatchEvent(event);
    }

    // Public API methods
    getFiles() {
        return this.selectedFiles;
    }

    clearFiles() {
        this.selectedFiles = [];
        this.updateFileInput();
        this.updatePreviews();
        this.clearErrors();
    }

    addFiles(files) {
        this.handleFileSelection(Array.isArray(files) ? files : [files]);
    }

    setProgress(progress) {
        this.showProgress(progress);
    }

    reset() {
        this.clearFiles();
        this.hideProgress();
    }
}

// Global registry for component instances
window.fileUploadComponents = window.fileUploadComponents || {};

// Auto-initialize components
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.file-upload-component').forEach(component => {
        if (!window.fileUploadComponents[component.id]) {
            window.fileUploadComponents[component.id] = new FileUploadComponent(component.id);
        }
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FileUploadComponent;
}
