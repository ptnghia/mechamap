/**
 * Thread Actions JavaScript
 * Handles bookmark and follow functionality for threads
 */

document.addEventListener('DOMContentLoaded', function() {
    //console.log('Thread Actions: Initializing...');

    // Initialize bookmark buttons
    initializeBookmarkButtons();

    // Initialize follow buttons
    initializeFollowButtons();

    //console.log('Thread Actions: Initialized successfully');
});

/**
 * Initialize bookmark button functionality
 */
function initializeBookmarkButtons() {
    const bookmarkButtons = document.querySelectorAll('.btn-bookmark');

    bookmarkButtons.forEach(button => {
        button.addEventListener('click', handleBookmarkClick);
    });

    //console.log(`Thread Actions: Initialized ${bookmarkButtons.length} bookmark buttons`);
}

/**
 * Initialize follow button functionality
 */
function initializeFollowButtons() {
    const followButtons = document.querySelectorAll('.btn-follow');

    followButtons.forEach(button => {
        button.addEventListener('click', handleFollowClick);
    });

    //console.log(`Thread Actions: Initialized ${followButtons.length} follow buttons`);
}

/**
 * Handle bookmark button click
 */
async function handleBookmarkClick(event) {
    event.preventDefault();

    const button = event.currentTarget;
    const isBookmarked = button.dataset.bookmarked === 'true';

    // Lấy thread slug từ container (ưu tiên slug, fallback về ID)
    const threadContainer = button.closest('[data-thread-id]');
    if (!threadContainer) {
        console.error('Thread container not found');
        if (window.showNotification) {
            window.showNotification('Không tìm thấy thông tin bài viết', 'error');
        }
        return;
    }

    const threadSlug = threadContainer.getAttribute('data-thread-slug');
    const threadId = threadContainer.getAttribute('data-thread-id');
    const threadIdentifier = threadSlug || threadId;

    if (!threadIdentifier) {
        console.error('Thread identifier not found');
        if (window.showNotification) {
            window.showNotification('Không tìm thấy thông tin bài viết', 'error');
        }
        return;
    }

    // Disable button during request
    button.disabled = true;

    try {
        const response = await fetch(`/ajax/threads/${threadIdentifier}/bookmark`, {
            method: isBookmarked ? 'DELETE' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok && data.success !== false) {
            // Update button state
            updateBookmarkButton(button, !isBookmarked);

            // Show success message
            if (window.showNotification) {
                const message = !isBookmarked ? 'Đã lưu bài viết.' : 'Đã bỏ lưu bài viết.';
                window.showNotification(message, 'success');
            }
        } else {
            // Handle specific error cases
            if (response.status === 404) {
                throw new Error('Thread không tồn tại hoặc đã bị xóa');
            } else if (response.status === 401) {
                throw new Error('Bạn cần đăng nhập để sử dụng tính năng này');
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra khi xử lý bookmark');
            }
        }

    } catch (error) {
        console.error('Bookmark error:', error);

        // Show specific error message
        let errorMessage = 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.';
        if (error.message.includes('Thread không tồn tại')) {
            errorMessage = 'Thread không tồn tại hoặc đã bị xóa. Trang sẽ được tải lại.';
            // Reload page after showing error for deleted threads
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else if (error.message.includes('đăng nhập')) {
            errorMessage = 'Bạn cần đăng nhập để sử dụng tính năng này.';
        } else if (error.message) {
            errorMessage = error.message;
        }

        if (window.showNotification) {
            window.showNotification(errorMessage, 'error');
        } else {
            alert(errorMessage);
        }
    } finally {
        // Re-enable button
        button.disabled = false;
    }
}

/**
 * Handle follow button click
 */
async function handleFollowClick(event) {
    event.preventDefault();

    const button = event.currentTarget;
    const isFollowed = button.dataset.followed === 'true';

    // Lấy thread slug từ container (ưu tiên slug, fallback về ID)
    const threadContainer = button.closest('[data-thread-id]');
    if (!threadContainer) {
        console.error('Thread container not found');
        if (window.showNotification) {
            window.showNotification('Không tìm thấy thông tin bài viết', 'error');
        }
        return;
    }

    const threadSlug = threadContainer.getAttribute('data-thread-slug');
    const threadId = threadContainer.getAttribute('data-thread-id');
    const threadIdentifier = threadSlug || threadId;

    if (!threadIdentifier) {
        console.error('Thread identifier not found');
        if (window.showNotification) {
            window.showNotification('Không tìm thấy thông tin bài viết', 'error');
        }
        return;
    }

    // Disable button during request
    button.disabled = true;

    try {
        const response = await fetch(`/ajax/threads/${threadIdentifier}/follow`, {
            method: isFollowed ? 'DELETE' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok && data.success !== false) {
            // Update button state
            updateFollowButton(button, !isFollowed);

            // Show success message
            if (window.showNotification) {
                const message = !isFollowed ? 'Đã theo dõi chủ đề.' : 'Đã bỏ theo dõi chủ đề.';
                window.showNotification(message, 'success');
            }
        } else {
            // Handle specific error cases
            if (response.status === 404) {
                throw new Error('Thread không tồn tại hoặc đã bị xóa');
            } else if (response.status === 401) {
                throw new Error('Bạn cần đăng nhập để sử dụng tính năng này');
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra khi xử lý theo dõi');
            }
        }

    } catch (error) {
        console.error('Follow error:', error);

        // Show specific error message
        let errorMessage = 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.';
        if (error.message.includes('Thread không tồn tại')) {
            errorMessage = 'Thread không tồn tại hoặc đã bị xóa. Trang sẽ được tải lại.';
            // Reload page after showing error for deleted threads
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else if (error.message.includes('đăng nhập')) {
            errorMessage = 'Bạn cần đăng nhập để sử dụng tính năng này.';
        } else if (error.message) {
            errorMessage = error.message;
        }

        if (window.showNotification) {
            window.showNotification(errorMessage, 'error');
        } else {
            alert(errorMessage);
        }
    } finally {
        // Re-enable button
        button.disabled = false;
    }
}

/**
 * Update bookmark button appearance
 */
function updateBookmarkButton(button, isBookmarked) {
    const icon = button.querySelector('i');
    const text = button.querySelector('.bookmark-text');

    // Update data attribute
    button.dataset.bookmarked = isBookmarked ? 'true' : 'false';

    if (isBookmarked) {
        // Bookmarked state
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-primary', 'active');
        icon.className = 'fas fa-bookmark';
        if (text) text.textContent = 'Đã đánh dấu'; // t_forums('actions.bookmarked')
        button.title = 'Xóa đánh dấu'; // t_forums('actions.bookmark_remove')
    } else {
        // Not bookmarked state
        button.classList.remove('btn-primary', 'active');
        button.classList.add('btn-outline-primary');
        icon.className = 'far fa-bookmark';
        if (text) text.textContent = 'Đánh dấu'; // t_forums('actions.bookmark')
        button.title = 'Thêm đánh dấu'; // t_forums('actions.bookmark_add')
    }
}

/**
 * Update follow button appearance
 */
function updateFollowButton(button, isFollowed) {
    const icon = button.querySelector('i');
    const text = button.querySelector('.follow-text');

    // Update data attribute
    button.dataset.followed = isFollowed ? 'true' : 'false';

    if (isFollowed) {
        // Followed state
        button.classList.remove('btn-outline-success');
        button.classList.add('btn-success', 'active');
        icon.className = 'fas fa-bell';
        if (text) text.textContent = 'Đang theo dõi'; // t_forums('actions.following')
        button.title = 'Bỏ theo dõi chủ đề'; // t_forums('actions.unfollow_thread')
    } else {
        // Not followed state
        button.classList.remove('btn-success', 'active');
        button.classList.add('btn-outline-success');
        icon.className = 'far fa-bell';
        if (text) text.textContent = 'Theo dõi'; // t_forums('actions.follow')
        button.title = 'Theo dõi chủ đề'; // t_forums('actions.follow_thread')
    }
}

/**
 * Reinitialize buttons for dynamically loaded content
 */
window.reinitializeThreadActions = function() {
    //console.log('Thread Actions: Reinitializing for dynamic content...');
    initializeBookmarkButtons();
    initializeFollowButtons();
};
