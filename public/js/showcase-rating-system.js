/**
 * Showcase Rating System - AJAX & Real-time Integration
 *
 * Features:
 * - AJAX form submission for ratings and replies
 * - Real-time notifications via WebSocket
 * - Like/unlike functionality
 * - Image upload with preview
 * - Form validation
 * - Toast notifications
 */

class ShowcaseRatingSystem {
    constructor(showcaseId) {
        this.showcaseId = showcaseId;
        this.apiBaseUrl = '/api';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.toastContainer = null;
        this.currentUser = null;

        this.init();
    }

    async init() {
        // Initializing Showcase Rating System

        // Initialize components
        this.createToastContainer();
        await this.getCurrentUser();
        this.bindEvents();
        this.initializeWebSocket();

        // Showcase Rating System initialized
    }

    /**
     * Get current authenticated user
     */
    async getCurrentUser() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/auth/user`, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.currentUser = data.user;
            }
        } catch (error) {
            // User not authenticated
        }
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Rating form submission
        const ratingForm = document.getElementById('rating-comment-form');
        if (ratingForm) {
            ratingForm.addEventListener('submit', (e) => this.handleRatingSubmit(e));
        }

        // Like buttons for ratings
        document.addEventListener('click', (e) => {
            if (e.target.closest('.like-rating-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.like-rating-btn');
                const ratingId = btn.dataset.ratingId;
                this.toggleRatingLike(ratingId, btn);
            }
        });

        // Like buttons for replies
        document.addEventListener('click', (e) => {
            if (e.target.closest('.like-reply-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.like-reply-btn');
                const replyId = btn.dataset.replyId;
                this.toggleReplyLike(replyId, btn);
            }
        });

        // Reply form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('reply-form')) {
                e.preventDefault();
                this.handleReplySubmit(e);
            }
        });

        // Reply toggle buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.reply-toggle-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.reply-toggle-btn');
                const ratingId = btn.dataset.ratingId;
                this.toggleReplyForm(ratingId);
            }
        });

        // Reply cancel buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.reply-cancel-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.reply-cancel-btn');
                const ratingId = btn.dataset.ratingId;
                this.toggleReplyForm(ratingId, false);
            }
        });
    }

    /**
     * Handle rating form submission
     */
    async handleRatingSubmit(event) {
        event.preventDefault();

        if (!this.currentUser) {
            this.showToast('Bạn cần đăng nhập để đánh giá showcase này.', 'warning');
            return;
        }

        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        // Validate required fields
        const requiredFields = ['technical_quality', 'innovation', 'usefulness', 'documentation'];
        for (const field of requiredFields) {
            if (!formData.get(field)) {
                this.showToast(`Vui lòng đánh giá ${this.getFieldLabel(field)}.`, 'warning');
                return;
            }
        }

        // Show loading state
        this.setButtonLoading(submitBtn, true);

        try {
            const response = await fetch(`${this.apiBaseUrl}/showcases/${this.showcaseId}/ratings`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showToast(data.message, 'success');
                this.addRatingToList(data.data.rating);
                this.updateShowcaseStats(data.data.showcase_stats);
                form.reset();
                this.resetStarRatings();

                // Clear CKEditor content
                const editorId = 'rating-comment-editor';
                if (window[`ckeditor_${editorId}`]) {
                    window[`ckeditor_${editorId}`].setData('');
                }
            } else {
                this.showToast(data.message, 'error');
                if (data.errors) {
                    this.displayValidationErrors(data.errors);
                }
            }
        } catch (error) {
            console.error('Error submitting rating:', error);
            this.showToast('Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.', 'error');
        } finally {
            this.setButtonLoading(submitBtn, false);
        }
    }

    /**
     * Handle reply form submission
     */
    async handleReplySubmit(event) {
        event.preventDefault();

        if (!this.currentUser) {
            this.showToast('Bạn cần đăng nhập để trả lời.', 'warning');
            return;
        }

        const form = event.target;
        const ratingId = form.dataset.ratingId;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        // Get content from CKEditor
        const editorId = `reply-editor-${ratingId}`;
        let content = '';
        if (window[`ckeditor_${editorId}`]) {
            content = window[`ckeditor_${editorId}`].getData().trim();
        }

        // Validate content
        if (!content) {
            this.showToast('Vui lòng nhập nội dung trả lời.', 'warning');
            return;
        }

        // Set content in form data
        formData.set('content', content);

        this.setButtonLoading(submitBtn, true);

        try {
            const response = await fetch(`${this.apiBaseUrl}/ratings/${ratingId}/replies`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showToast(data.message, 'success');
                this.addReplyToList(ratingId, data.data.reply);
                form.reset();

                // Clear CKEditor content
                const editorId = `reply-editor-${ratingId}`;
                if (window[`ckeditor_${editorId}`]) {
                    window[`ckeditor_${editorId}`].setData('');
                }

                // Hide reply form
                this.toggleReplyForm(ratingId, false);
            } else {
                this.showToast(data.message, 'error');
                if (data.errors) {
                    this.displayValidationErrors(data.errors);
                }
            }
        } catch (error) {
            console.error('Error submitting reply:', error);
            this.showToast('Có lỗi xảy ra khi gửi trả lời. Vui lòng thử lại.', 'error');
        } finally {
            this.setButtonLoading(submitBtn, false);
        }
    }

    /**
     * Toggle rating like
     */
    async toggleRatingLike(ratingId, button) {
        if (!this.currentUser) {
            this.showToast('Bạn cần đăng nhập để thích đánh giá này.', 'warning');
            return;
        }

        const originalState = {
            isActive: button.classList.contains('active'),
            count: parseInt(button.querySelector('.like-count').textContent)
        };

        // Optimistic update
        this.updateLikeButton(button, !originalState.isActive, originalState.count + (originalState.isActive ? -1 : 1));

        try {
            const response = await fetch(`${this.apiBaseUrl}/ratings/${ratingId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                // Update with actual server response
                this.updateLikeButton(button, data.data.is_liked, data.data.like_count);
            } else {
                // Revert optimistic update
                this.updateLikeButton(button, originalState.isActive, originalState.count);
                this.showToast(data.message, 'error');
            }
        } catch (error) {
            // Revert optimistic update
            this.updateLikeButton(button, originalState.isActive, originalState.count);
            console.error('Error toggling rating like:', error);
            this.showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        }
    }

    /**
     * Toggle reply like
     */
    async toggleReplyLike(replyId, button) {
        if (!this.currentUser) {
            this.showToast('Bạn cần đăng nhập để thích trả lời này.', 'warning');
            return;
        }

        const originalState = {
            isActive: button.classList.contains('active'),
            count: parseInt(button.querySelector('.like-count').textContent)
        };

        // Optimistic update
        this.updateLikeButton(button, !originalState.isActive, originalState.count + (originalState.isActive ? -1 : 1));

        try {
            const response = await fetch(`${this.apiBaseUrl}/replies/${replyId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateLikeButton(button, data.data.is_liked, data.data.like_count);
            } else {
                // Revert optimistic update
                this.updateLikeButton(button, originalState.isActive, originalState.count);
                this.showToast(data.message, 'error');
            }
        } catch (error) {
            // Revert optimistic update
            this.updateLikeButton(button, originalState.isActive, originalState.count);
            console.error('Error toggling reply like:', error);
            this.showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        }
    }

    /**
     * Update like button state
     */
    updateLikeButton(button, isLiked, count) {
        const likeCountSpan = button.querySelector('.like-count');
        const heartIcon = button.querySelector('i');

        if (isLiked) {
            button.classList.add('active');
            heartIcon.classList.remove('far');
            heartIcon.classList.add('fas');
        } else {
            button.classList.remove('active');
            heartIcon.classList.remove('fas');
            heartIcon.classList.add('far');
        }

        likeCountSpan.textContent = count;
    }

    /**
     * Toggle reply form visibility
     */
    toggleReplyForm(ratingId, forceState = null) {
        const replyForm = document.getElementById(`reply-form-${ratingId}`);
        if (!replyForm) return;

        const isVisible = forceState !== null ? forceState : replyForm.style.display !== 'none';
        replyForm.style.display = isVisible ? 'none' : 'block';

        if (!isVisible) {
            // Focus on CKEditor when showing
            setTimeout(() => {
                const editorId = `reply-editor-${ratingId}`;
                if (window[`ckeditor_${editorId}`]) {
                    window[`ckeditor_${editorId}`].editing.view.focus();
                }
            }, 100);
        }
    }

    /**
     * Add new rating to the list
     */
    addRatingToList(rating) {
        const ratingsList = document.querySelector('.ratings-list');
        if (!ratingsList) return;

        const ratingHtml = this.generateRatingHtml(rating);
        ratingsList.insertAdjacentHTML('afterbegin', ratingHtml);

        // Highlight new rating
        const newRating = ratingsList.firstElementChild;
        newRating.classList.add('highlight');
        setTimeout(() => newRating.classList.remove('highlight'), 3000);
    }

    /**
     * Add new reply to rating
     */
    addReplyToList(ratingId, reply) {
        const repliesContainer = document.querySelector(`#rating-${ratingId} .rating-replies`);
        if (!repliesContainer) return;

        const replyHtml = this.generateReplyHtml(reply);
        repliesContainer.insertAdjacentHTML('beforeend', replyHtml);

        // Highlight new reply
        const newReply = repliesContainer.lastElementChild;
        newReply.classList.add('highlight');
        setTimeout(() => newReply.classList.remove('highlight'), 3000);
    }

    /**
     * Generate HTML for rating
     */
    generateRatingHtml(rating) {
        // This would be a complex template - for now return placeholder
        return `<div class="rating-item" id="rating-${rating.id}">
            <!-- Rating HTML template will be implemented -->
        </div>`;
    }

    /**
     * Generate HTML for reply
     */
    generateReplyHtml(reply) {
        // This would be a complex template - for now return placeholder
        return `<div class="rating-reply" id="reply-${reply.id}">
            <!-- Reply HTML template will be implemented -->
        </div>`;
    }

    /**
     * Update showcase statistics
     */
    updateShowcaseStats(stats) {
        // Update rating summary
        const avgRatingElement = document.querySelector('.rating-average');
        if (avgRatingElement) {
            avgRatingElement.textContent = stats.average_rating?.toFixed(1) || '0.0';
        }

        const totalRatingsElement = document.querySelector('.total-ratings');
        if (totalRatingsElement) {
            totalRatingsElement.textContent = `${stats.total_ratings} đánh giá`;
        }

        // Update breakdown
        Object.entries(stats.ratings_breakdown).forEach(([key, value]) => {
            const element = document.querySelector(`[data-rating-type="${key}"] .rating-score`);
            if (element) {
                element.textContent = value?.toFixed(1) || '0.0';
            }
        });
    }

    /**
     * Initialize WebSocket connection for real-time updates
     */
    initializeWebSocket() {
        if (typeof window.MechaMapWebSocket !== 'undefined' && this.currentUser) {
            // Connecting to WebSocket for real-time updates

            // Subscribe to showcase-specific events
            window.MechaMapWebSocket.subscribe(`showcase.${this.showcaseId}`, (data) => {
                this.handleRealtimeUpdate(data);
            });
        }
    }

    /**
     * Handle real-time updates from WebSocket
     */
    handleRealtimeUpdate(data) {
        // Received real-time update

        switch (data.type) {
            case 'showcase_rating_created':
                if (data.rating.user.id !== this.currentUser?.id) {
                    this.showToast(`${data.rating.user.name} đã đánh giá showcase này`, 'info');
                    this.addRatingToList(data.rating);
                    this.updateShowcaseStats(data.showcase_stats);
                }
                break;

            case 'rating_reply_created':
                if (data.reply.user.id !== this.currentUser?.id) {
                    this.showToast(`${data.reply.user.name} đã trả lời một đánh giá`, 'info');
                    this.addReplyToList(data.rating_id, data.reply);
                }
                break;

            case 'rating_liked':
                if (data.liker.id !== this.currentUser?.id) {
                    this.showToast(`${data.liker.name} đã thích một đánh giá`, 'info');
                }
                break;
        }
    }

    /**
     * Utility methods
     */
    setButtonLoading(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || 'Gửi';
        }
    }

    resetStarRatings() {
        document.querySelectorAll('.star-rating .star').forEach(star => {
            star.classList.remove('active');
        });
    }

    getFieldLabel(field) {
        const labels = {
            'technical_quality': 'Chất lượng kỹ thuật',
            'innovation': 'Tính sáng tạo',
            'usefulness': 'Tính hữu ích',
            'documentation': 'Chất lượng tài liệu'
        };
        return labels[field] || field;
    }

    displayValidationErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            messages.forEach(message => {
                this.showToast(message, 'error');
            });
        });
    }

    showToast(message, type = 'info') {
        const toastId = `toast-${Date.now()}`;
        const bgClass = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        }[type] || 'bg-info';

        const iconClass = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        }[type] || 'fa-info-circle';

        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="toast-body ${bgClass} text-white d-flex align-items-center">
                    <i class="fas ${iconClass} me-2"></i>
                    <span>${message}</span>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        this.toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    createToastContainer() {
        if (!this.toastContainer) {
            this.toastContainer = document.createElement('div');
            this.toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            this.toastContainer.style.zIndex = '9999';
            document.body.appendChild(this.toastContainer);
        }
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const showcaseId = document.querySelector('[data-showcase-id]')?.dataset.showcaseId;
    if (showcaseId) {
        window.showcaseRatingSystem = new ShowcaseRatingSystem(showcaseId);
    }
});
