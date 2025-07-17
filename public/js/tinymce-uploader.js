/**
 * TinyMCE Unified Upload Handler for MechaMap
 * Handles image and file uploads with consistent validation and error handling
 */

class TinyMCEUploader {
    constructor() {
        this.uploadUrl = '/api/tinymce/upload';
        this.maxFileSize = 5 * 1024 * 1024; // 5MB
        this.allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        this.allowedFileTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/octet-stream' // For CAD files
        ];
    }

    /**
     * Upload image from TinyMCE editor
     */
    static uploadImage(blobInfo, success, failure, progress) {
        const uploader = new TinyMCEUploader();
        
        // Validate file
        const blob = blobInfo.blob();
        if (!uploader.validateImage(blob)) {
            failure('File không hợp lệ hoặc quá lớn');
            return;
        }

        // Create FormData
        const formData = new FormData();
        formData.append('image', blob, blobInfo.filename());
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        // Upload with progress
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                if (progress) progress(percentComplete);
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success && response.url) {
                        success(response.url);
                    } else {
                        failure(response.message || 'Upload failed');
                    }
                } catch (e) {
                    failure('Invalid response from server');
                }
            } else {
                failure(`Upload failed with status: ${xhr.status}`);
            }
        });

        xhr.addEventListener('error', function() {
            failure('Network error occurred');
        });

        xhr.open('POST', uploader.uploadUrl);
        xhr.send(formData);
    }

    /**
     * File picker for links and media
     */
    static filePicker(callback, value, meta) {
        const uploader = new TinyMCEUploader();
        
        // Create file input
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        
        if (meta.filetype === 'image') {
            input.setAttribute('accept', 'image/*');
        } else if (meta.filetype === 'media') {
            input.setAttribute('accept', 'video/*,audio/*');
        } else {
            input.setAttribute('accept', '*/*');
        }

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file
            if (!uploader.validateFile(file, meta.filetype)) {
                alert('File không hợp lệ hoặc quá lớn');
                return;
            }

            // Upload file
            uploader.uploadFile(file, meta.filetype)
                .then(response => {
                    if (response.success && response.url) {
                        callback(response.url, {
                            alt: file.name,
                            title: file.name
                        });
                    } else {
                        alert(response.message || 'Upload failed');
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Có lỗi xảy ra khi upload file');
                });
        });

        input.click();
    }

    /**
     * Validate image file
     */
    validateImage(file) {
        // Check file size
        if (file.size > this.maxFileSize) {
            return false;
        }

        // Check file type
        if (!this.allowedImageTypes.includes(file.type)) {
            return false;
        }

        return true;
    }

    /**
     * Validate any file
     */
    validateFile(file, filetype) {
        // Check file size
        if (file.size > this.maxFileSize) {
            return false;
        }

        // Check file type based on context
        if (filetype === 'image') {
            return this.allowedImageTypes.includes(file.type);
        } else if (filetype === 'file') {
            return this.allowedFileTypes.includes(file.type) || 
                   this.allowedImageTypes.includes(file.type);
        }

        return true;
    }

    /**
     * Upload file with Promise
     */
    uploadFile(file, filetype) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('filetype', filetype);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch(this.uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => resolve(data))
            .catch(error => reject(error));
        });
    }

    /**
     * Handle drag and drop upload
     */
    static initDragDrop(editorId) {
        const editor = tinymce.get(editorId);
        if (!editor) return;

        const container = editor.getContainer();
        if (!container) return;

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area
        ['dragenter', 'dragover'].forEach(eventName => {
            container.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            container.classList.add('tinymce-drag-over');
        }

        function unhighlight(e) {
            container.classList.remove('tinymce-drag-over');
        }

        // Handle dropped files
        container.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                handleFiles(files, editor);
            }
        }

        function handleFiles(files, editor) {
            const uploader = new TinyMCEUploader();
            
            Array.from(files).forEach(file => {
                if (uploader.validateImage(file)) {
                    // Upload image
                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                    fetch(uploader.uploadUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.url) {
                            editor.insertContent(`<img src="${data.url}" alt="${file.name}" style="max-width: 100%;">`);
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                    });
                }
            });
        }
    }

    /**
     * Initialize paste handling for images
     */
    static initPasteHandler(editorId) {
        const editor = tinymce.get(editorId);
        if (!editor) return;

        editor.on('paste', function(e) {
            const clipboardData = e.clipboardData || window.clipboardData;
            if (!clipboardData) return;

            const items = clipboardData.items;
            if (!items) return;

            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                
                if (item.type.indexOf('image') !== -1) {
                    e.preventDefault();
                    
                    const blob = item.getAsFile();
                    if (blob) {
                        const uploader = new TinyMCEUploader();
                        
                        if (uploader.validateImage(blob)) {
                            // Create a unique filename
                            const filename = `pasted-image-${Date.now()}.png`;
                            
                            // Upload the pasted image
                            const formData = new FormData();
                            formData.append('image', blob, filename);
                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                            fetch(uploader.uploadUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.url) {
                                    editor.insertContent(`<img src="${data.url}" alt="Pasted image" style="max-width: 100%;">`);
                                }
                            })
                            .catch(error => {
                                console.error('Paste upload error:', error);
                            });
                        }
                    }
                    break;
                }
            }
        });
    }
}

// Export for global use
window.TinyMCEUploader = TinyMCEUploader;
