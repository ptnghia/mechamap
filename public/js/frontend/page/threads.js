

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
            const oldHtml = this.innerHTML;
            //this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + trans('ui.status.processing');

            // Make AJAX request
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
                    const newLiked = !isLiked;
                    this.dataset.liked = newLiked ? 'true' : 'false';

                    // Toggle trạng thái
                    if (newLiked) {
                        this.classList.add('active');
                        this.title = trans('ui.actions.unlike');
                    } else {
                        this.classList.remove('active');
                        this.title = trans('ui.actions.like');
                    }

                    // Cập nhật số lượt thích
                    const likeCountElement = this.querySelector('.like-count');
                    if (likeCountElement) {
                        likeCountElement.textContent = data.like_count;
                    }

                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || trans('ui.messages.error_occurred'), 'error');
                    this.innerHTML = oldHtml; // khôi phục nếu thất bại
                }
            })
            .catch(error => {
                console.error('Like error:', error);
                showToast(trans('ui.messages.request_error'), 'error');
                this.innerHTML = oldHtml; // khôi phục nếu lỗi
            })
            .finally(() => {
                this.disabled = false;
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
            const oldHtml = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + trans('ui.status.processing');

            // Make AJAX request
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
                    const newSaved = !isSaved;
                    this.dataset.saved = newSaved ? 'true' : 'false';

                    if (newSaved) {
                        this.className = this.className.replace('btn-outline-primary', 'btn-primary');
                        this.innerHTML = `
                            <i class="fa-solid fa-bookmark me-1"></i>
                            <span class="save-text">${trans('ui.actions.saved')}</span>
                            ${data.saves_count > 0 ? `<span class="badge bg-danger text-dark ms-1 save-count">${data.saves_count}</span>` : ''}
                        `;
                        this.title = trans('ui.actions.saved');
                    } else {
                        this.className = this.className.replace('btn-primary', 'btn-outline-primary');
                        this.innerHTML = `
                            <i class="fa-regular fa-bookmark me-1"></i>
                            <span class="save-text">${trans('ui.actions.save')}</span>
                            ${data.saves_count > 0 ? `<span class="badge bg-danger text-dark ms-1 save-count">${data.saves_count}</span>` : ''}
                        `;
                        this.title = trans('ui.actions.unsave');
                    }

                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || trans('ui.messages.error_occurred'), 'error');
                    this.innerHTML = oldHtml; // khôi phục lại nếu thất bại
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                showToast(trans('ui.messages.request_error'), 'error');
                this.innerHTML = oldHtml; // khôi phục lại nếu lỗi
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });

    // Thread follow functionality
    document.querySelectorAll('.thread-follow-btn').forEach(button => {
        button.addEventListener('click', function() {
            const threadSlug = this.dataset.threadSlug;
            const isFollowing = this.dataset.following === 'true';
            const action = isFollowing ? 'unfollow' : 'follow';

            // Disable button during request
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> ' + trans('ui.status.processing');

            // Make AJAX request
            const url = `/ajax/threads/${threadSlug}/follow`;
            const method = isFollowing ? 'DELETE' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    const newFollowing = data.is_following;
                    this.dataset.following = newFollowing ? 'true' : 'false';

                    // Update button appearance
                    if (newFollowing) {
                        this.className = this.className.replace('btn-outline-primary', 'btn-success');
                        this.innerHTML = '<i class="fas fa-bell me-1"></i><span class="follow-text">' + trans('ui.actions.following') + '</span><span class="follower-count badge bg-light text-dark ms-1">' + data.follower_count + '</span>';
                        this.title = trans('ui.actions.unfollow');
                    } else {
                        this.className = this.className.replace('btn-success', 'btn-outline-primary');
                        this.innerHTML = '<i class="fas fa-bell-slash me-1"></i><span class="follow-text">' + trans('ui.actions.follow') + '</span><span class="follower-count badge bg-light text-dark ms-1">' + data.follower_count + '</span>';
                        this.title = trans('ui.actions.follow');
                    }

                    // Show success message
                    showToast(data.message, 'success');

                    // Update all follower counts on page
                    const threadId = this.dataset.threadId;
                    document.querySelectorAll(`[data-thread-id="${threadId}"] .follower-count`).forEach(el => {
                        el.textContent = data.follower_count;
                    });

                } else {
                    showToast(data.message || trans('ui.messages.error_occurred'), 'error');

                    // Reset button state
                    const originalFollowing = this.dataset.following === 'true';
                    if (originalFollowing) {
                        this.innerHTML = '<i class="fas fa-bell me-1"></i><span class="follow-text">' + trans('ui.actions.following') + '</span><span class="follower-count badge bg-light text-dark ms-1">' + (data.follower_count || 0) + '</span>';
                    } else {
                        this.innerHTML = '<i class="fas fa-bell-slash me-1"></i><span class="follow-text">' + trans('ui.actions.follow') + '</span><span class="follower-count badge bg-light text-dark ms-1">' + (data.follower_count || 0) + '</span>';
                    }
                }
            })
            .catch(error => {
                console.error('Thread follow error:', error);
                showToast(trans('ui.messages.request_error'), 'error');

                // Reset button state
                const originalFollowing = this.dataset.following === 'true';
                const followerCount = this.querySelector('.follower-count')?.textContent || '0';
                if (originalFollowing) {
                    this.innerHTML = '<i class="fas fa-bell me-1"></i><span class="follow-text">' + trans('ui.actions.following') + '</span><span class="follower-count badge bg-light text-dark ms-1">' + followerCount + '</span>';
                } else {
                    this.innerHTML = '<i class="fas fa-bell-slash me-1"></i><span class="follow-text">' + trans('ui.actions.follow') + '</span><span class="follower-count badge bg-light text-dark ms-1">' + followerCount + '</span>';
                }
            })
            .finally(() => {
                this.disabled = false;
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
            //this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + trans('ui.status.processing');

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
                        this.title = trans('ui.actions.unlike');
                    } else {
                        this.classList.remove('active');
                        this.title = trans('ui.actions.like');
                    }

                    // Update like count
                    const likeCountElement = $('.comment-like-count-'+commentId);
                    if (likeCountElement) {
                        console.log('Like count: ', data.like_count);
                        likeCountElement.text(data.like_count);
                    }

                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || trans('ui.messages.error_occurred'), 'error');
                }
            })
            .catch(error => {
                console.error('Comment like error:', error);
                showToast(trans('ui.messages.request_error'), 'error');
            })
            .finally(() => {
                this.disabled = false;
                //this.innerHTML = originalContent;
            });
        });
    });

    // Handle delete comment button clicks
    function initializeDeleteCommentButtons() {
        document.addEventListener('click', function (e) {
            const deleteButton = e.target.closest('.delete-comment-btn');
            if (!deleteButton) return;

            e.preventDefault();

            const commentId = deleteButton.dataset.commentId;
            const commentType = deleteButton.dataset.commentType;
            const confirmMessage = commentType === 'reply'
                ? trans('features.threads.delete_reply_message')
                : trans('features.threads.delete_comment_message');

            window.showDeleteConfirm(confirmMessage).then((result) => {
                if (!result.isConfirmed) return;

                const oldHtml = deleteButton.innerHTML;
                deleteButton.disabled = true;
                deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Server error');

                    const commentElement = document.querySelector(`#comment-${commentId}`);
                    if (commentElement) {
                        commentElement.style.transition = 'opacity 0.3s ease';
                        commentElement.style.opacity = '0';
                        setTimeout(() => commentElement.remove(), 300);
                    }

                    showToast(data.message, 'success');
                })
                .catch(err => {
                    console.error('Delete comment error:', err);
                    showToast(err.message || trans('ui.messages.request_error'), 'error');
                    deleteButton.innerHTML = oldHtml;
                    deleteButton.disabled = false;
                });
            });
        });
    }


    // Handle sort button clicks
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.sort-btn');
        if (!btn) return;

        const sortType = btn.dataset.sort;
        const threadId = btn.dataset.threadId;

        // Toggle button states
        document.querySelectorAll('.sort-btn').forEach(b => {
            b.classList.toggle('btn-primary', b === btn);
            b.classList.toggle('btn-outline-primary', b !== btn);
        });

        // Show loading state
        const commentsContainer = document.getElementById('comments-container');
        if (commentsContainer) {
            commentsContainer.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i><br>
                    ${trans('ui.status.loading_comments')}
                </div>`;
        }

        // Update URL
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortType);

        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Server error');
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newComments = doc.getElementById('comments-container');

            if (newComments && commentsContainer) {
                commentsContainer.innerHTML = newComments.innerHTML;
                initializeCommentInteractions(); // re-init events
                window.history.pushState({}, '', url.toString());
                showToast(trans('ui.messages.comments_sorted'), 'success');
            }
        })
        .catch(err => {
            console.error('Sort comments error:', err);
            showToast(trans('ui.messages.request_error'), 'error');
            window.location.href = url.toString(); // fallback
        });
    });

    // Function to initialize delete image buttons
    initializeDeleteImageButtons();
}
// Function to initialize delete image buttons
function initializeDeleteImageButtons() {
        // Event delegation: chỉ gán 1 listener cho toàn bộ document
    document.addEventListener('click', function(e) {
        const deleteButton = e.target.closest('.delete-image-btn');
        if (!deleteButton) return; // không phải nút xóa thì bỏ qua

        e.preventDefault();
        e.stopPropagation();

        const imageId = deleteButton.dataset.imageId;
        const commentId = deleteButton.dataset.commentId;
        const imageWrapper = deleteButton.closest('.comment-image-wrapper');
        const oldHtml = deleteButton.innerHTML;

        window.showDeleteConfirm(trans('ui.confirmations.delete_image')).then((result) => {
            if (!result.isConfirmed) return;

            // Disable button trong lúc request
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
                    // Animation remove
                    imageWrapper.style.transition = 'opacity 0.3s ease';
                    imageWrapper.style.opacity = '0';
                    setTimeout(() => {
                        imageWrapper.remove();

                        const attachmentsContainer = document.querySelector(`#comment-${commentId} .comment-attachments`);
                        if (attachmentsContainer && !attachmentsContainer.querySelector('.col')) {
                            attachmentsContainer.remove();
                        }
                    }, 300);
                } else {
                    window.showError('Lỗi!', data.message || trans('ui.messages.delete_image_error'));
                    resetButton();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showError('Lỗi!', trans('ui.messages.delete_image_error'));
                resetButton();
            });

            function resetButton() {
                deleteButton.disabled = false;
                deleteButton.innerHTML = oldHtml;
            }
        });
    });
}


// Load translations for threads functionality
function loadThreadsTranslations() {
    if (window.translationService) {
        // Load UI and features translations needed for threads
        return window.translationService.loadTranslations(['ui', 'features'])
            .then(() => {
                console.log('Threads translations loaded successfully');
                return true;
            })
            .catch(error => {
                console.error('Failed to load threads translations:', error);
                return false;
            });
    }
    return Promise.resolve(false);
}

// Initialize threads functionality when translations are ready
function initializeThreadsWithTranslations() {
    loadThreadsTranslations().then(success => {
        if (success) {
            // Translations loaded, initialize thread actions
            initializeThreadActions();
        } else {
            // Fallback: initialize without translations (will use keys as fallback)
            console.warn('Initializing threads without translations');
            initializeThreadActions();
        }
    });
}

// Listen for language change events
document.addEventListener('languageChanged', function(event) {
    console.log('Language changed to:', event.detail.locale);
    // Reload translations and update UI elements
    loadThreadsTranslations().then(() => {
        // Update any visible UI elements with new translations
        updateThreadsUILanguage();
    });
});

// Handle real-time thread like updates
function handleThreadLikeUpdate(data) {
    const currentUserId = window.appConfig.userId;

    if (Number(data.user_id) === Number(currentUserId)) return;

    document.querySelectorAll('.like-count').forEach(el => {
        el.textContent = data.like_count;
    });

    if (data.is_liked) {
        showCommentNotification(`${data.user_name} ${trans('thread.liked_thread')}`, 'info');
    }
}

// Handle real-time comment like updates
function handleCommentLikeUpdate(data) {
    const currentUserId = window.appConfig.userId;

    if (Number(data.user_id) === Number(currentUserId)) return;

    const commentElement = document.querySelector(`#comment-${data.comment_id}`);
    if (commentElement) {
        const likeCountElement = commentElement.querySelector(`.comment-like-count-${data.comment_id}`);
        if (likeCountElement) {
            likeCountElement.textContent = data.like_count;
        }
    }

    if (data.is_liked) {
        showCommentNotification(`${data.user_name} ${trans('thread.liked_comment')}`, 'info');
    }
}

// Handle real-time thread stats updates
function handleThreadStatsUpdate(data) {
    const stats = data.stats;

    if (stats.comments_count !== undefined) {
        const repliesElements = document.querySelectorAll('[data-type="replies"]');
        repliesElements.forEach(el => {
            const icon = el.querySelector('i');
            const iconHtml = icon ? icon.outerHTML : '<i class="fas fa-comment"></i>';
            el.innerHTML = `${iconHtml} ${stats.comments_count.toLocaleString()} ${trans('thread.replies')}`;
        });

        const repliesHeader = document.querySelector('[data-type="replies-header"]');
        if (repliesHeader) {
            const icon = repliesHeader.querySelector('i');
            const iconHtml = icon ? icon.outerHTML : '<i class="fa-regular fa-comment-dots me-1"></i>';
            repliesHeader.innerHTML = `${iconHtml}${stats.comments_count.toLocaleString()} ${trans('thread.replies')}`;
        }
    }

    if (stats.participants_count !== undefined) {
        const participantElements = document.querySelectorAll('[data-type="participants"]');
        participantElements.forEach(el => {
            const icon = el.querySelector('i');
            const iconHtml = icon ? icon.outerHTML : '<i class="fas fa-users"></i>';
            el.innerHTML = `${iconHtml} ${stats.participants_count.toLocaleString()} ${trans('thread.participants')}`;
        });
    }

    if (stats.last_activity) {
        const lastActivityElements = document.querySelectorAll('[data-type="last-activity"]');
        lastActivityElements.forEach(el => {
            const icon = el.querySelector('i');
            const iconHtml = icon ? icon.outerHTML : '<i class="fas fa-clock"></i>';
            el.innerHTML = `${iconHtml} ${stats.last_activity}`;
        });
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

// Update UI elements with current language
function updateThreadsUILanguage() {
    // Update like button titles
    document.querySelectorAll('.btn-like').forEach(button => {
        const isLiked = button.dataset.liked === 'true';
        button.title = isLiked ? trans('ui.actions.unlike') : trans('ui.actions.like');
    });

    // Update save button text and titles
    document.querySelectorAll('.btn-save').forEach(button => {
        const isSaved = button.dataset.saved === 'true';
        const saveText = button.querySelector('.save-text');
        if (saveText) {
            saveText.textContent = isSaved ? trans('ui.actions.saved') : trans('ui.actions.save');
        }
        button.title = isSaved ? trans('ui.actions.saved') : trans('ui.actions.unsave');
    });

    // Update follow button text and titles
    document.querySelectorAll('.thread-follow-btn').forEach(button => {
        const isFollowing = button.dataset.following === 'true';
        const followText = button.querySelector('.follow-text');
        if (followText) {
            followText.textContent = isFollowing ? trans('ui.actions.following') : trans('ui.actions.follow');
        }
        button.title = isFollowing ? trans('ui.actions.unfollow') : trans('ui.actions.follow');
    });

    // Update comment like button titles
    document.querySelectorAll('.comment-like-btn').forEach(button => {
        const isLiked = button.dataset.liked === 'true';
        button.title = isLiked ? trans('ui.actions.unlike') : trans('ui.actions.like');
    });
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize threads with translations
    initializeThreadsWithTranslations();
});

// Also initialize if DOM is already loaded
if (document.readyState === 'loading') {
    // DOM is still loading, wait for DOMContentLoaded
} else {
    // DOM is already loaded, initialize immediately
    initializeThreadsWithTranslations();
}


document.addEventListener('DOMContentLoaded', () => {
    initializeEventHandlers();
    initializeRealTimeComments();

    // Nếu Laravel trả về comment cần scroll thì gọi hàm tiện ích
    if (window.appConfig && window.appConfig.scrollToCommentId) {
        scrollToAndHighlight(`comment-${window.appConfig.scrollToCommentId}`);
    }
});

/**
 * Cuộn tới phần tử và highlight trong 3 giây
 * @param {string} elementId
 */
function scrollToAndHighlight(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;

    setTimeout(() => {
        el.scrollIntoView({ behavior: "smooth", block: "center" });
        el.classList.add("highlight-comment");
        setTimeout(() => el.classList.remove("highlight-comment"), 3000);
    }, 500);
}
