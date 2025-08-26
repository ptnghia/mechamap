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

    // Lấy slug từ URL của thread title link
    const threadItem = button.closest('.thread-item');
    const titleLink = threadItem ? threadItem.querySelector('.thread-title a') : null;

    if (!titleLink) {
        console.error('Thread title link not found');
        if (window.showNotification) {
            window.showNotification('Không tìm thấy thông tin bài viết', 'error');
        }
        return;
    }

    const threadUrl = titleLink.href;
    const urlParts = threadUrl.split('/');
    const threadSlug = urlParts[urlParts.length - 1]; // Lấy slug từ URL

    if (!threadSlug) {
        console.error('Thread slug not found');
        if (window.showNotification) {
            window.showNotification('Không tìm thấy thông tin bài viết', 'error');
        }
        return;
    }

    // Disable button during request
    button.disabled = true;

    try {
        const response = await fetch(`/ajax/threads/${threadSlug}/bookmark`, {
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
            throw new Error(data.message || 'Có lỗi xảy ra khi xử lý bookmark');
        }

    } catch (error) {
        console.error('Bookmark error:', error);

        // Show error message
        if (window.showNotification) {
            window.showNotification('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');
        } else {
            alert('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.');
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

    // Lấy slug từ URL của thread title link
    const threadItem = button.closest('.thread-item');
    const titleLink = threadItem ? threadItem.querySelector('.thread-title a') : null;

    if (!titleLink) {
        console.error('Thread title link not found');
        if (window.showNotification) {
            window.showNotification('Không tìm thấy thông tin bài viết', 'error');
        }
        return;
    }

    const threadUrl = titleLink.href;
    const urlParts = threadUrl.split('/');
    const threadSlug = urlParts[urlParts.length - 1]; // Lấy slug từ URL

    if (!threadSlug) {
        console.error('Thread slug not found');
        if (window.showNotification) {
            window.showNotification('Không tìm thấy thông tin bài viết', 'error');
        }
        return;
    }

    // Disable button during request
    button.disabled = true;

    try {
        const response = await fetch(`/ajax/threads/${threadSlug}/follow`, {
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
            throw new Error(data.message || 'Có lỗi xảy ra khi xử lý theo dõi');
        }

    } catch (error) {
        console.error('Follow error:', error);

        // Show error message
        if (window.showNotification) {
            window.showNotification('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.', 'error');
        } else {
            alert('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.');
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
