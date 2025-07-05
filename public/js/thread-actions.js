/**
 * Thread Actions JavaScript
 * Xử lý bookmark và follow actions cho thread items
 */

document.addEventListener('DOMContentLoaded', function() {
    // Lấy CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        // console.warn('CSRF token không tìm thấy');
        return;
    }

    // Xử lý bookmark buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.bookmark-btn')) {
            e.preventDefault();
            handleBookmarkAction(e.target.closest('.bookmark-btn'));
        }
    });

    // Xử lý follow buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.follow-btn')) {
            e.preventDefault();
            handleFollowAction(e.target.closest('.follow-btn'));
        }
    });

    /**
     * Xử lý bookmark action
     */
    async function handleBookmarkAction(button) {
        const threadId = button.getAttribute('data-thread-id');
        const isBookmarked = button.getAttribute('data-bookmarked') === 'true';

        if (!threadId) {
            console.error('Thread ID không tìm thấy');
            return;
        }

        // Disable button và hiển thị loading
        setButtonLoading(button, true);

        try {
            const url = `/api/threads/${threadId}/bookmark`;
            const method = isBookmarked ? 'DELETE' : 'POST';

            // console.log(`🔗 Making ${method} request to:`, url);
            // console.log('🔑 CSRF Token:', csrfToken ? 'Present' : 'Missing');

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            // console.log('📡 Response status:', response.status);
            const data = await response.json();
            // console.log('📦 Response data:', data);

            if (response.ok) {
                // Cập nhật trạng thái button
                updateBookmarkButton(button, !isBookmarked);

                // Hiển thị toast notification
                showToast(isBookmarked ? 'Đã bỏ bookmark' : 'Đã thêm bookmark', 'success');
            } else {
                console.error('Lỗi bookmark:', data);
                showToast(data.message || 'Có lỗi xảy ra', 'error');
            }
        } catch (error) {
            console.error('Lỗi network:', error);
            showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
        } finally {
            setButtonLoading(button, false);
        }
    }

    /**
     * Xử lý follow action
     */
    async function handleFollowAction(button) {
        const threadId = button.getAttribute('data-thread-id');
        const isFollowed = button.getAttribute('data-followed') === 'true';

        if (!threadId) {
            console.error('Thread ID không tìm thấy');
            return;
        }

        // Disable button và hiển thị loading
        setButtonLoading(button, true);

        try {
            const url = `/api/threads/${threadId}/follow`;
            const method = isFollowed ? 'DELETE' : 'POST';

            // console.log(`🔗 Making ${method} request to:`, url);
            // console.log('🔑 CSRF Token:', csrfToken ? 'Present' : 'Missing');

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            // console.log('📡 Response status:', response.status);
            const data = await response.json();
            // console.log('📦 Response data:', data);

            if (response.ok) {
                // Cập nhật trạng thái button
                updateFollowButton(button, !isFollowed);

                // Hiển thị toast notification
                showToast(isFollowed ? 'Đã bỏ theo dõi' : 'Đã theo dõi thread', 'success');
            } else {
                console.error('Lỗi follow:', data);
                showToast(data.message || 'Có lỗi xảy ra', 'error');
            }
        } catch (error) {
            console.error('Lỗi network:', error);
            showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
        } finally {
            setButtonLoading(button, false);
        }
    }

    /**
     * Cập nhật bookmark button state
     */
    function updateBookmarkButton(button, isBookmarked) {
        const icon = button.querySelector('i');
        const text = button.querySelector('span');

        button.setAttribute('data-bookmarked', isBookmarked ? 'true' : 'false');
        button.title = isBookmarked ? 'Bỏ bookmark' : 'Thêm bookmark';

        if (isBookmarked) {
            button.classList.add('active');
            icon.className = 'bi bi-bookmark-fill';
            if (text) text.textContent = 'Đã lưu';
        } else {
            button.classList.remove('active');
            icon.className = 'bi bi-bookmark';
            if (text) text.textContent = 'Lưu';
        }
    }

    /**
     * Cập nhật follow button state
     */
    function updateFollowButton(button, isFollowed) {
        const icon = button.querySelector('i');
        const text = button.querySelector('span');

        button.setAttribute('data-followed', isFollowed ? 'true' : 'false');
        button.title = isFollowed ? 'Bỏ theo dõi' : 'Theo dõi';

        if (isFollowed) {
            button.classList.add('active');
            icon.className = 'bi bi-bell-fill';
            if (text) text.textContent = 'Đang theo dõi';
        } else {
            button.classList.remove('active');
            icon.className = 'bi bi-bell';
            if (text) text.textContent = 'Theo dõi';
        }
    }

    /**
     * Set button loading state
     */
    function setButtonLoading(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            const originalIcon = button.querySelector('i').className;
            button.querySelector('i').className = 'spinner-border spinner-border-sm';
            button.setAttribute('data-original-icon', originalIcon);
        } else {
            button.disabled = false;
            const originalIcon = button.getAttribute('data-original-icon');
            if (originalIcon) {
                button.querySelector('i').className = originalIcon;
                button.removeAttribute('data-original-icon');
            }
        }
    }

    /**
     * Hiển thị toast notification
     */
    function showToast(message, type = 'info') {
        // Tìm hoặc tạo toast container
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '1055';
            document.body.appendChild(toastContainer);
        }

        // Tạo toast element
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bi ${getToastIcon(type)} me-2"></i>
                    <strong class="me-auto">${getToastTitle(type)}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        // Hiển thị toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });

        toast.show();

        // Xóa toast sau khi ẩn
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }

    /**
     * Lấy icon cho toast dựa trên type
     */
    function getToastIcon(type) {
        switch (type) {
            case 'success': return 'bi-check-circle-fill text-success';
            case 'error': return 'bi-exclamation-triangle-fill text-danger';
            case 'warning': return 'bi-exclamation-triangle-fill text-warning';
            default: return 'bi-info-circle-fill text-info';
        }
    }

    /**
     * Lấy title cho toast dựa trên type
     */
    function getToastTitle(type) {
        switch (type) {
            case 'success': return 'Thành công';
            case 'error': return 'Lỗi';
            case 'warning': return 'Cảnh báo';
            default: return 'Thông báo';
        }
    }
});
