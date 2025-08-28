
    // Initialize event handlers when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeEventHandlers();
    initializeRealTimeComments();

    // Auto-scroll to new comment if specified
    });

// File upload functionality is now handled by the FileUploadComponent

function initializeFormSubmission() {
    const form = document.getElementById('reply-form-element');
    const submitBtn = document.getElementById('submit-reply-btn');
    const contentTextarea = document.getElementById('content');
    const contentError = document.getElementById('content-error');

    if (!form || !submitBtn || !contentTextarea) return;

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset previous errors
        contentTextarea.classList.remove('is-invalid');
        contentError.style.display = 'none';

        // Get content from TinyMCE
        const editor = tinymce.get('content');
        let content = '';

        if (editor) {
            content = editor.getContent().trim();
            // Sync TinyMCE content to textarea
            contentTextarea.value = content;
        } else {
            content = contentTextarea.value.trim();
        }

        // Validate content
        if (!content || content === '<p></p>' || content === '<p><br></p>' || content === '') {
            // Show error
            contentTextarea.classList.add('is-invalid');
            contentError.style.display = 'block';

            // Add error class to TinyMCE container
            const tinyMCEContainer = document.querySelector('.tox-tinymce');
            if (tinyMCEContainer) {
                tinyMCEContainer.classList.add('is-invalid');
            }

            // Focus TinyMCE editor
            if (editor) {
                editor.focus();
            } else {
                contentTextarea.focus();
            }

            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang gửi...';

        // Prepare form data
        const formData = new FormData(form);

        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast(data.message, 'success');

                // Clear form
                if (tinymce.get('content')) {
                    tinymce.get('content').setContent('');
                } else {
                    contentTextarea.value = '';
                }

                // Reset parent_id if it was a reply
                const parentIdInput = document.getElementById('parent_id');
                const replyToInfo = document.getElementById('reply-to-info');
                if (parentIdInput) parentIdInput.value = '';
                if (replyToInfo) replyToInfo.style.display = 'none';

                // Reload page to show new comment (for now, can be improved later)
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Server error');
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);

            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi phản hồi';

            // Show error message
            showToast(error.message || 'Có lỗi xảy ra khi gửi form. Vui lòng thử lại.', 'error');
        });
    });

    // Handle TinyMCE content change to remove error
    if (tinymce.get('content')) {
        tinymce.get('content').on('input keyup', function() {
            const content = this.getContent().trim();
            if (content && content !== '<p></p>' && content !== '<p><br></p>') {
                contentTextarea.classList.remove('is-invalid');
                contentError.style.display = 'none';

                // Remove error class from TinyMCE container
                const tinyMCEContainer = document.querySelector('.tox-tinymce');
                if (tinyMCEContainer) {
                    tinyMCEContainer.classList.remove('is-invalid');
                }
            }
        });
    }

    // Handle textarea change (fallback)
    contentTextarea.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
            contentError.style.display = 'none';

            // Remove error class from TinyMCE container
            const tinyMCEContainer = document.querySelector('.tox-tinymce');
            if (tinyMCEContainer) {
                tinyMCEContainer.classList.remove('is-invalid');
            }
        }
    });
}

function initializeEventHandlers() {
    // File upload is now handled by FileUploadComponent

    // Initialize Form Submission Handler
    initializeFormSubmission();

    // Initialize Thread Actions (Like, Save)
    initializeThreadActions();

    // Handle reply buttons
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const parentId = this.getAttribute('data-parent-id');

            // Find the parent user name and content - look for .fw-bold in the comment structure
            const commentItem = this.closest('.comment_item');
            let parentUser = 'Người dùng';
            let parentContent = '';

            if (commentItem) {
                const userLink = commentItem.querySelector('.comment_item_user .fw-bold');
                if (userLink) {
                    parentUser = userLink.textContent.trim();
                }

                // Get comment content - look for the content div
                const contentDiv = commentItem.querySelector('.comment_item_content');
                if (contentDiv) {
                    // Get text content and limit to reasonable length
                    parentContent = contentDiv.textContent.trim();
                    if (parentContent.length > 200) {
                        parentContent = parentContent.substring(0, 200) + '...';
                    }
                }
            }

            const parentIdInput = document.getElementById('parent_id');
            const replyToName = document.getElementById('reply-to-name');
            const replyToContent = document.getElementById('reply-to-content');
            const replyToInfo = document.getElementById('reply-to-info');

            if (parentIdInput) parentIdInput.value = parentId;
            if (replyToName) replyToName.textContent = parentUser;
            if (replyToContent) replyToContent.textContent = parentContent;
            if (replyToInfo) replyToInfo.style.display = 'block';

            // Scroll to reply form
            const replyForm = document.getElementById('reply-form');
            if (replyForm) {
                replyForm.scrollIntoView({ behavior: 'smooth' });

                // Focus TinyMCE editor
                setTimeout(() => {
                    if (tinymce.get('content')) {
                        tinymce.get('content').focus();
                    }
                }, 100);
            }
        });
    });

    // Handle cancel reply
    const cancelReply = document.getElementById('cancel-reply');
    if (cancelReply) {
        cancelReply.addEventListener('click', function() {
            const parentIdInput = document.getElementById('parent_id');
            const replyToInfo = document.getElementById('reply-to-info');

            if (parentIdInput) parentIdInput.value = '';
            if (replyToInfo) replyToInfo.style.display = 'none';
        });
    }

    // Handle quote buttons
    document.querySelectorAll('.quote-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentContent = this.getAttribute('data-comment-content');
            const userName = this.getAttribute('data-user-name');

            const quoteHTML = `
                <blockquote>
                    <p><strong>${userName} đã viết:</strong></p>
                    ${commentContent}
                </blockquote>
                <p></p>
            `;

            // Insert quote into TinyMCE
            if (tinymce.get('content')) {
                tinymce.get('content').insertContent(quoteHTML);

                // Scroll to reply form and focus
                const replyForm = document.getElementById('reply-form');
                if (replyForm) {
                    replyForm.scrollIntoView({ behavior: 'smooth' });
                    setTimeout(() => {
                        tinymce.get('content').focus();
                    }, 100);
                }
            }
        });
    });

    // Handle edit comment buttons
    document.querySelectorAll('.edit-comment-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const commentContent = this.getAttribute('data-comment-content');

            // Scroll to reply form
            const replyForm = document.getElementById('reply-form');
            if (replyForm) {
                replyForm.scrollIntoView({ behavior: 'smooth' });

                // Set form to edit mode
                setFormEditMode(commentId, commentContent);
            }
        });
    });

    // Function to set form to edit mode
    function setFormEditMode(commentId, content) {
        const formContainer = document.getElementById('reply-form');
        const form = document.getElementById('reply-form-element');
        const submitButton = form.querySelector('button[type="submit"]');
        const formTitle = formContainer.querySelector('h4');

        // Change form title and button text
        formTitle.textContent = 'Chỉnh sửa bình luận';
        submitButton.innerHTML = '<i class="fas fa-save"></i> Cập nhật bình luận';

        // Set content in TinyMCE
        if (tinymce.get('content')) {
            tinymce.get('content').setContent(content);
        }

        // Add hidden input for comment ID
        let commentIdInput = form.querySelector('input[name="comment_id"]');
        if (!commentIdInput) {
            commentIdInput = document.createElement('input');
            commentIdInput.type = 'hidden';
            commentIdInput.name = 'comment_id';
            form.appendChild(commentIdInput);
        }
        commentIdInput.value = commentId;

        // Add edit mode flag
        let editModeInput = form.querySelector('input[name="edit_mode"]');
        if (!editModeInput) {
            editModeInput = document.createElement('input');
            editModeInput.type = 'hidden';
            editModeInput.name = 'edit_mode';
            form.appendChild(editModeInput);
        }
        editModeInput.value = '1';

        // Add cancel button
        let cancelButton = form.querySelector('.cancel-edit-btn');
        if (!cancelButton) {
            cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'btn btn-secondary cancel-edit-btn me-2';
            cancelButton.innerHTML = '<i class="fas fa-times"></i> Hủy';
            submitButton.parentNode.insertBefore(cancelButton, submitButton);

            // Handle cancel
            cancelButton.addEventListener('click', function() {
                resetFormToReplyMode();
            });
        }
        cancelButton.style.display = 'inline-block';
    }

    // Function to reset form to reply mode
    function resetFormToReplyMode() {
        const formContainer = document.getElementById('reply-form');
        const form = document.getElementById('reply-form-element');
        const submitButton = form.querySelector('button[type="submit"]');
        const formTitle = formContainer.querySelector('h4');
        const cancelButton = form.querySelector('.cancel-edit-btn');

        // Reset form title and button text
        formTitle.textContent = 'Post reply';
        submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Send reply';

        // Clear TinyMCE content
        if (tinymce.get('content')) {
            tinymce.get('content').setContent('');
        }

        // Remove hidden inputs
        const commentIdInput = form.querySelector('input[name="comment_id"]');
        const editModeInput = form.querySelector('input[name="edit_mode"]');
        if (commentIdInput) commentIdInput.remove();
        if (editModeInput) editModeInput.remove();

        // Hide cancel button
        if (cancelButton) {
            cancelButton.style.display = 'none';
        }

        // Clear uploaded images
        clearUploadedImages();
    }



    // Function to clear uploaded images
    function clearUploadedImages() {
        const imagePreview = document.getElementById('image-preview');
        if (imagePreview) {
            imagePreview.innerHTML = '';
        }

        // Reset file input
        const fileInput = document.getElementById('images');
        if (fileInput) {
            fileInput.value = '';
        }

        // Clear deleted images list
        const deletedImages = document.getElementById('deleted-images');
        if (deletedImages) {
            deletedImages.remove();
        }
    }

// Function to initialize delete image buttons
function initializeDeleteImageButtons() {
    document.querySelectorAll('.delete-image-btn').forEach(button => {
        // Remove existing listeners to avoid duplicates
        button.replaceWith(button.cloneNode(true));
    });

    document.querySelectorAll('.delete-image-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const imageId = this.getAttribute('data-image-id');
            const commentId = this.getAttribute('data-comment-id');
            const imageWrapper = this.closest('.comment-image-wrapper').parentElement;
            const deleteButton = this;

            // Use SweetAlert2 for confirmation
            Swal.fire({
                title: 'Xác nhận',
                text: 'Bạn có chắc chắn muốn xóa hình ảnh này không?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable button during request
                    deleteButton.disabled = true;
                    deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    // Send delete request
                    fetch(`/comments/${commentId}/images/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove image from DOM with animation
                            imageWrapper.style.transition = 'opacity 0.3s ease';
                            imageWrapper.style.opacity = '0';
                            setTimeout(() => {
                                imageWrapper.remove();

                                // Check if no more images left in this comment
                                const attachmentsContainer = document.querySelector(`#comment-${commentId} .comment-attachments`);
                                if (attachmentsContainer && attachmentsContainer.querySelectorAll('.col-md-3, .col-sm-4, .col-6').length === 0) {
                                    attachmentsContainer.remove();
                                }
                            }, 300);

                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đã xóa hình ảnh thành công.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message || 'Có lỗi xảy ra khi xóa hình ảnh.',
                                icon: 'error'
                            });
                            deleteButton.disabled = false;
                            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra khi xóa hình ảnh.',
                            icon: 'error'
                        });
                        deleteButton.disabled = false;
                        deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                    });
                }
            });
        })
    });
}

// Initialize Thread Actions (Like, Save)
function initializeThreadActions() {
    // Handle like button clicks
    document.querySelectorAll('.btn-like').forEach(button => {
        button.addEventListener('click', function() {
            if (this.onclick) return; // Skip if it's a login button

            const threadId = this.dataset.threadId;
            const threadSlug = this.dataset.threadSlug;
            const isLiked = this.dataset.liked === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            // Make AJAX request - use slug for route model binding
            fetch(`/threads/${threadSlug}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle like state
                    const newLiked = !isLiked;
                    this.dataset.liked = newLiked ? 'true' : 'false';

                    // Update button appearance
                    if (newLiked) {
                        this.classList.add('active');
                        this.title = 'Bỏ thích';
                    } else {
                        this.classList.remove('active');
                        this.title = 'Thích';
                    }

                    // Update like count
                    const likeCountElement = this.querySelector('.like-count');
                    if (likeCountElement) {
                        likeCountElement.textContent = data.like_count;
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(error => {
                console.error('Like error:', error);
                showToast('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Handle save button clicks
    document.querySelectorAll('.btn-save').forEach(button => {
        button.addEventListener('click', function() {
            if (this.onclick) return; // Skip if it's a login button

            const threadId = this.dataset.threadId;
            const threadSlug = this.dataset.threadSlug;
            const isSaved = this.dataset.saved === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            // Make AJAX request - use slug for route model binding
            fetch(`/threads/${threadSlug}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle save state
                    const newSaved = !isSaved;
                    this.dataset.saved = newSaved ? 'true' : 'false';

                    // Update button appearance - re-query elements to avoid stale references
                    const button = document.querySelector('.btn-save');
                    if (!button) {
                        console.error('Button not found after response');
                        return;
                    }

                    const icon = button.querySelector('i');
                    const text = button.querySelector('.save-text');
                    let countBadge = button.querySelector('.save-count');

                    if (icon && text) {
                        if (newSaved) {
                            button.classList.add('active');
                            icon.className = 'fas fa-bookmark';
                            text.textContent = 'Đã đánh dấu';
                            button.title = 'thread.unsave';
                        } else {
                            button.classList.remove('active');
                            icon.className = 'far fa-bookmark';
                            text.textContent = 'Đánh dấu';
                            button.title = 'thread.save';
                        }

                        // Update save count badge
                        if (data.saves_count !== undefined) {
                            if (data.saves_count > 0) {
                                if (!countBadge) {
                                    countBadge = document.createElement('span');
                                    countBadge.className = 'badge bg-light text-dark ms-1 save-count';
                                    button.appendChild(countBadge);
                                }
                                countBadge.textContent = new Intl.NumberFormat().format(data.saves_count);
                            } else {
                                if (countBadge) {
                                    countBadge.remove();
                                }
                            }
                        }
                    } else {
                        // Fallback: Force page reload if elements can't be updated
                        console.warn('Could not update button elements, reloading page...');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                showToast('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Handle comment like button clicks
    document.querySelectorAll('.comment-like-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (this.onclick) return; // Skip if it's a login button

            const commentId = this.dataset.commentId;
            const isLiked = this.dataset.liked === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            // Make AJAX request
            fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle like state
                    const newLiked = !isLiked;
                    this.dataset.liked = newLiked ? 'true' : 'false';

                    // Update button appearance
                    if (newLiked) {
                        this.classList.add('active');
                        this.title = 'Bỏ thích';
                    } else {
                        this.classList.remove('active');
                        this.title = 'Thích';
                    }

                    // Update like count
                    const likeCountElement = this.querySelector('.comment-like-count');
                    if (likeCountElement) {
                        likeCountElement.textContent = data.like_count;
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(error => {
                console.error('Comment like error:', error);
                showToast('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Handle delete comment button clicks
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.dataset.commentId;
            const commentType = this.dataset.commentType;
            const confirmMessage = commentType === 'reply' ?
                'Bạn có chắc chắn muốn xóa phản hồi này không?' :
                'Bạn có chắc chắn muốn xóa bình luận này không?';

            // Use SweetAlert2 for confirmation
            window.showDeleteConfirm(confirmMessage).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                // Proceed with deletion
                const button = this;

                // Disable button during request
                button.disabled = true;
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                // Make AJAX request
                fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove comment element from DOM
                        const commentElement = document.querySelector(`#comment-${commentId}`);
                        if (commentElement) {
                            commentElement.style.transition = 'opacity 0.3s ease';
                            commentElement.style.opacity = '0';

                            setTimeout(() => {
                                commentElement.remove();
                            }, 300);
                        }

                        // Show success message
                        showToast(data.message, 'success');
                    } else {
                        throw new Error(data.message || 'Server error');
                    }
                })
                .catch(error => {
                    console.error('Delete comment error:', error);
                    showToast(error.message || 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');

                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = originalContent;
                });
            });
        });
    });

    // Handle sort button clicks
    document.querySelectorAll('.sort-btn').forEach(button => {
        button.addEventListener('click', function() {
            const sortType = this.dataset.sort;
            const threadId = this.dataset.threadId;

            // Update button states
            document.querySelectorAll('.sort-btn').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');

            // Show loading state
            const commentsContainer = document.getElementById('comments-container');
            if (commentsContainer) {
                commentsContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Đang tải bình luận...</div>';
            }

            // Make AJAX request to get sorted comments
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sortType);

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.text();
                } else {
                    throw new Error('Server error');
                }
            })
            .then(html => {
                // Parse the response HTML to extract comments
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newCommentsContainer = doc.getElementById('comments-container');

                if (newCommentsContainer && commentsContainer) {
                    commentsContainer.innerHTML = newCommentsContainer.innerHTML;

                    // Re-initialize event handlers for new content
                    initializeCommentInteractions();

                    // Update URL without page reload
                    window.history.pushState({}, '', url.toString());

                    // Show success message
                    showToast('Bình luận đã được sắp xếp', 'success');
                }
            })
            .catch(error => {
                console.error('Sort comments error:', error);
                showToast('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');

                // Reload page as fallback
                window.location.href = url.toString();
            });
        });
    });

    // Initialize delete image buttons on page load
    initializeDeleteImageButtons();
}

// Initialize comment interactions (likes, delete, etc.) for dynamically loaded content
function initializeCommentInteractions() {
    // Re-initialize comment like buttons
    document.querySelectorAll('.comment-like-btn').forEach(button => {
        // Remove existing event listeners by cloning the element
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);

        newButton.addEventListener('click', function() {
            if (this.onclick) return; // Skip if it's a login button

            const commentId = this.dataset.commentId;
            const isLiked = this.dataset.liked === 'true';

            // Disable button during request
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            // Make AJAX request
            fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle like state
                    const newLiked = !isLiked;
                    this.dataset.liked = newLiked ? 'true' : 'false';

                    // Update button appearance
                    if (newLiked) {
                        this.classList.add('active');
                        this.title = 'Bỏ thích';
                    } else {
                        this.classList.remove('active');
                        this.title = 'Thích';
                    }

                    // Update like count
                    const likeCountElement = this.querySelector('.comment-like-count');
                    if (likeCountElement) {
                        likeCountElement.textContent = data.like_count;
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(error => {
                console.error('Comment like error:', error);
                showToast('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalContent;
            });
        });
    });

    // Re-initialize delete comment buttons
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        // Remove existing event listeners by cloning the element
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);

        newButton.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.dataset.commentId;
            const commentType = this.dataset.commentType;
            const confirmMessage = commentType === 'reply' ?
                'Bạn có chắc chắn muốn xóa phản hồi này không?' :
                'Bạn có chắc chắn muốn xóa bình luận này không?';

            // Use SweetAlert2 for confirmation
            window.showDeleteConfirm(confirmMessage).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                // Proceed with deletion
                const button = this;

                // Disable button during request
                button.disabled = true;
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                // Make AJAX request
                fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove comment element from DOM
                        const commentElement = document.querySelector(`#comment-${commentId}`);
                        if (commentElement) {
                            commentElement.style.transition = 'opacity 0.3s ease';
                            commentElement.style.opacity = '0';

                            setTimeout(() => {
                                commentElement.remove();
                            }, 300);
                        }

                        // Show success message
                        showToast(data.message, 'success');
                    } else {
                        throw new Error(data.message || 'Server error');
                    }
                })
                .catch(error => {
                    console.error('Delete comment error:', error);
                    showToast(error.message || 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');

                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = originalContent;
                });
            });
        });
    });
}

// Toast notification function (if not already defined)
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Login modal function (if not already defined)
function showLoginModal() {
    // Check if login modal exists
    const loginModal = document.getElementById('loginModal');
    if (loginModal) {
        const modal = new bootstrap.Modal(loginModal);
        modal.show();
    } else {
        // Redirect to login page
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
    }
}

// Real-time comments functionality
function initializeRealTimeComments() {
    const threadId = 11;
    const currentUserId = 16;

    // Subscribe to thread channel for real-time updates
    if (window.notificationService && window.notificationService.socket) {
        const socket = window.notificationService.socket;

        // Join thread channel
        socket.emit('subscribe_request', { channel: `thread.${threadId}` });

        // Listen for new comments
        socket.on('comment.created', function(data) {
            console.log('New comment received:', data);
            handleNewComment(data);
        });

        // Listen for comment updates
        socket.on('comment.updated', function(data) {
            console.log('Comment updated:', data);
            handleCommentUpdate(data);
        });

        // Listen for comment deletions
        socket.on('comment.deleted', function(data) {
            console.log('Comment deleted:', data);
            handleCommentDeletion(data);
        });

        // Listen for thread like updates
        socket.on('thread.like.updated', function(data) {
            console.log('Thread like updated:', data);
            handleThreadLikeUpdate(data);
        });

        // Listen for comment like updates
        socket.on('comment.like.updated', function(data) {
            console.log('Comment like updated:', data);
            handleCommentLikeUpdate(data);
        });

        // Listen for thread stats updates
        socket.on('thread.stats.updated', function(data) {
            console.log('Thread stats updated:', data);
            handleThreadStatsUpdate(data);
        });

        console.log(`Real-time comments initialized for thread ${threadId}`);
    } else {
        console.warn('NotificationService not available for real-time comments');
    }
}

function handleNewComment(data) {
    const comment = data.comment;
    const currentUserId = 16;

    // Don't show our own comments (they're already added by form submission)
    if (comment.user.id === currentUserId) {
        return;
    }

    // Create comment HTML
    const commentHtml = createCommentHtml(comment);

    // Add to comments section
    const commentsContainer = document.getElementById('comments-container');
    if (commentsContainer) {
        // Check if it's a reply to an existing comment
        if (comment.parent_id) {
            // Find parent comment and add as reply
            const parentComment = document.querySelector(`#comment-${comment.parent_id}`);
            if (parentComment) {
                const repliesContainer = parentComment.querySelector('.comment_sub');
                if (repliesContainer) {
                    repliesContainer.insertAdjacentHTML('beforeend', commentHtml);
                } else {
                    // Create replies container if it doesn't exist
                    const commentBody = parentComment.querySelector('.comment_item_body');
                    if (commentBody) {
                        commentBody.insertAdjacentHTML('beforeend', `<div class="comment_sub">${commentHtml}</div>`);
                    }
                }
            }
        } else {
            // Add as new top-level comment
            const noCommentsAlert = commentsContainer.querySelector('.alert-info');
            if (noCommentsAlert) {
                noCommentsAlert.remove();
            }

            // Insert before pagination
            const pagination = document.getElementById('comments-pagination');
            if (pagination) {
                pagination.insertAdjacentHTML('beforebegin', commentHtml);
            } else {
                commentsContainer.insertAdjacentHTML('beforeend', commentHtml);
            }
        }

        // Update comment count
        updateCommentCount(1);

        // Re-initialize event handlers for new comment
        initializeCommentInteractions();

        // Show notification
        showCommentNotification(comment.user.name + ' đã bình luận mới', 'success');

        // Scroll to new comment if user is near bottom
        scrollToNewCommentIfNeeded(comment.id);
    }
}

function handleCommentUpdate(data) {
    const comment = data.comment;
    const commentElement = document.querySelector(`#comment-${comment.id}`);

    if (commentElement) {
        // Update comment content
        const contentElement = commentElement.querySelector('.comment_item_content');
        if (contentElement) {
            contentElement.innerHTML = comment.content;

            // Show update indicator
            showCommentNotification('Bình luận đã được cập nhật', 'info');
        }
    }
}

function handleCommentDeletion(data) {
    const commentElement = document.querySelector(`#comment-${data.comment_id}`);

    if (commentElement) {
        // Fade out and remove
        commentElement.style.transition = 'opacity 0.3s ease';
        commentElement.style.opacity = '0';

        setTimeout(() => {
            commentElement.remove();

            // Update comment count
            updateCommentCount(-1);

            // Show notification
            showCommentNotification('Bình luận đã được xóa', 'warning');
        }, 300);
    }
}

function createCommentHtml(comment) {
    const timeAgo = formatTimeAgo(new Date(comment.created_at));
    const isReply = comment.parent_id ? true : false;
    const avatarSize = isReply ? 30 : 40;
    const currentUserId = 16;
    const isAuthenticated = currentUserId !== null;

    return `
        <div class="comment_item mb-3" id="comment-${comment.id}">
            <div class="d-flex">
                <div class="comment_item_avatar">
                    <img src="${comment.user.avatar_url}" alt="${comment.user.name}"
                         class="rounded-circle me-2" width="${avatarSize}" height="${avatarSize}">
                </div>
                <div class="comment_item_body ${isReply ? 'sub' : ''}">
                    <div class="comment_item_user">
                        <a href="/users/${comment.user.username || comment.user.id}" class="fw-bold text-decoration-none">
                            ${comment.user.name}
                        </a>
                        <div class="text-muted small">
                            <span>0 bình luận</span> ·
                            <span>Tham gia ${new Date(comment.user.created_at || Date.now()).toLocaleDateString('vi-VN', {month: 'short', year: 'numeric'})}</span>
                        </div>
                    </div>
                    <div class="comment_item_content">
                        ${comment.content}
                    </div>
                    <div class="comment_item_meta d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="btn btn-sm btn_meta"><i class="fa-regular fa-clock me-1"></i> ${timeAgo}</span>
                            ${isAuthenticated ? `
                            <button type="button"
                                    class="btn btn-sm btn_meta comment-like-btn"
                                    data-comment-id="${comment.id}"
                                    data-liked="false"
                                    title="Thích">
                                <i class="fas fa-thumbs-up"></i> <span class="comment-like-count">0</span> Thích
                            </button>
                            ` : `
                            <button type="button"
                                    class="btn btn-sm btn_meta comment-like-btn"
                                    onclick="showLoginModal()"
                                    title="Đăng nhập để thích">
                                <i class="fas fa-thumbs-up"></i> <span class="comment-like-count">0</span> Thích
                            </button>
                            `}
                        </div>
                        <div>
                            <button class="btn btn-main no-border quote-button" data-comment-id="${comment.id}"
                                data-comment-content="${comment.content}" data-user-name="${comment.user.name}">
                                <i class="fa-solid fa-quote-left"></i> Trích dẫn
                            </button>
                            <button class="btn btn-main no-border reply-button ms-2"
                                data-parent-id="${comment.parent_id || comment.id}">
                                <i class="fas fa-reply"></i> Trả lời
                            </button>
                        </div>
                    </div>
                    ${!isReply ? '<div class="comment_sub"></div>' : ''}
                </div>
            </div>
        </div>
    `;
}

function updateCommentCount(delta) {
    // Update comment count in thread meta
    const commentCountElements = document.querySelectorAll('.comments-count, [data-comments-count]');
    commentCountElements.forEach(element => {
        const currentCount = parseInt(element.textContent) || 0;
        const newCount = Math.max(0, currentCount + delta);
        element.textContent = newCount;
    });

    // Update comment count in section header
    const sectionHeader = document.querySelector('.title_page_sub');
    if (sectionHeader) {
        const text = sectionHeader.textContent;
        const match = text.match(/(\d+)/);
        if (match) {
            const currentCount = parseInt(match[1]);
            const newCount = Math.max(0, currentCount + delta);
            sectionHeader.innerHTML = sectionHeader.innerHTML.replace(/\d+/, newCount);
        }
    }
}

function showCommentNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function scrollToNewCommentIfNeeded(commentId) {
    const scrollPosition = window.pageYOffset;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    // If user is near bottom (within 200px), scroll to new comment
    if (scrollPosition + windowHeight >= documentHeight - 200) {
        setTimeout(() => {
            const commentElement = document.querySelector(`#comment-${commentId}`);
            if (commentElement) {
                commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 100);
    }
}

function formatTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'vừa xong';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' phút trước';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' giờ trước';
    return Math.floor(diffInSeconds / 86400) + ' ngày trước';
}

// Handle real-time thread like updates
function handleThreadLikeUpdate(data) {
    const currentUserId = 16;

    // Don't update if it's our own action (already updated by AJAX)
    if (data.user_id === currentUserId) {
        return;
    }

    // Update all thread like count displays
    document.querySelectorAll('.like-count').forEach(element => {
        element.textContent = data.like_count;
    });

    // Show notification if someone else liked the thread
    if (data.is_liked) {
        showCommentNotification(`${data.user_name} đã thích thread này`, 'info');
    }
}

// Handle real-time comment like updates
function handleCommentLikeUpdate(data) {
    const currentUserId = 16;

    // Don't update if it's our own action (already updated by AJAX)
    if (data.user_id === currentUserId) {
        return;
    }

    // Update comment like count
    const commentElement = document.querySelector(`#comment-${data.comment_id}`);
    if (commentElement) {
        const likeCountElement = commentElement.querySelector('.comment-like-count');
        if (likeCountElement) {
            likeCountElement.textContent = data.like_count;
        }
    }

    // Show notification if someone else liked the comment
    if (data.is_liked) {
        showCommentNotification(`${data.user_name} đã thích một bình luận`, 'info');
    }
}

// Handle real-time thread stats updates
function handleThreadStatsUpdate(data) {
    const stats = data.stats;

    // Update comments count
    if (stats.comments_count !== undefined) {
        // Update in thread meta
        const commentMetaElements = document.querySelectorAll('.thread-meta-item');
        commentMetaElements.forEach(element => {
            const text = element.textContent;
            if (text.includes('phản hồi')) {
                const icon = element.querySelector('i');
                const iconHtml = icon ? icon.outerHTML : '<i class="fas fa-comment"></i>';
                element.innerHTML = `${iconHtml} ${stats.comments_count.toLocaleString()} phản hồi`;
            }
        });

        // Update in comments section header
        const sectionHeader = document.querySelector('.title_page_sub');
        if (sectionHeader && sectionHeader.textContent.includes('phản hồi')) {
            const icon = sectionHeader.querySelector('i');
            const iconHtml = icon ? icon.outerHTML : '<i class="fa-regular fa-comment-dots me-1"></i>';
            sectionHeader.innerHTML = `${iconHtml}${stats.comments_count} phản hồi`;
        }
    }

    // Update participants count
    if (stats.participants_count !== undefined) {
        const participantElements = document.querySelectorAll('.thread-meta-item');
        participantElements.forEach(element => {
            const text = element.textContent;
            if (text.includes('người tham gia')) {
                const icon = element.querySelector('i');
                const iconHtml = icon ? icon.outerHTML : '<i class="fas fa-users"></i>';
                element.innerHTML = `${iconHtml} ${stats.participants_count.toLocaleString()} người tham gia`;
            }
        });
    }

    // Update last activity time
    if (stats.last_activity) {
        const lastActivityElements = document.querySelectorAll('.thread-meta-item');
        lastActivityElements.forEach(element => {
            const text = element.textContent;
            if (text.includes('Tham gia') || text.includes('trước')) {
                // This might be the last activity element, but we need to be more specific
                // For now, we'll skip updating this as it's complex to identify correctly
            }
        });
    }
}

