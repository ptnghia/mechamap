/**
 * Thread Item Helper
 * Script xử lý tạo và hiển thị các thread item, đồng nhất giữa server-side render và client-side render.
 *
 * @file thread-item.js
 * @version 1.0.0
 */

class ThreadItemBuilder {
    /**
     * Tạo element HTML cho thread item
     *
     * @param {Object} thread - Đối tượng thread từ API
     * @param {Object} translations - Các chuỗi dịch cần thiết
     * @returns {HTMLElement} - Element HTML đã được tạo
     */
    static createThreadElement(thread, translations) {
        const listItem = document.createElement('div');
        listItem.className = 'list-group-item thread-item thread-item-container';
        listItem.setAttribute('data-thread-id', thread.id || '');

        // Format date
        const createdAt = new Date(thread.created_at);
        const timeAgo = this.timeSince(createdAt);

        // User info với avatar fallback
        const userName = thread.user?.name || 'Người dùng';
        const userAvatar = thread.user?.profile_photo_url || thread.user?.avatar ||
            `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&color=7F9CF5&background=EBF4FF`;

        // Thread URL
        const threadUrl = thread.slug ? `/threads/${thread.slug}` : `/threads/${thread.id}`;

        // Content preview
        const contentPreview = thread.content ?
            (thread.content.length > 220 ? thread.content.substring(0, 220) + '...' : thread.content) : '';

        // Build the HTML với cấu trúc giống partial view mới
        listItem.innerHTML = `
            <!-- Thread Header với user info và badges -->
            <div class="thread-item-header">
                <div class="thread-user-info">
                    <div class="flex-shrink-0 me-3 d-none d-sm-block">
                        <img src="${userAvatar}"
                             alt="${userName}"
                             class="rounded-circle"
                             width="50"
                             height="50"
                             style="object-fit: cover;"
                             onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&color=7F9CF5&background=EBF4FF'">
                    </div>
                    <div>
                        <strong class="thread-user-name">${userName}</strong><br>
                        <span class="d-none d-md-inline text-muted">${timeAgo}</span>
                    </div>
                </div>
                <div class="thread-badges">
                    ${thread.is_sticky ? `<span class="btn btn-sm badge bg-primary"><i class="bi bi-pin-angle"></i> ${translations.sticky}</span>` : ''}
                    ${thread.is_locked ? `<span class="btn btn-sm badge bg-danger"><i class="bi bi-lock-fill"></i> ${translations.locked}</span>` : ''}
                    <!-- Action buttons cho authenticated users -->
                     ${this.generateActionButtons(thread)}
                </div>
            </div>

            <div class="row">
                <!-- Nội dung chính -->
                <div class="${thread.featured_image ? 'col-md-9' : 'col-12'}">
                    <div class="thread-title-section">
                        <div class="thread-title">
                            <a href="${threadUrl}">${thread.title}</a>
                        </div>
                        <small class="text-muted d-md-none">${timeAgo}</small>
                    </div>

                    <!-- Status badge nếu có -->
                    ${thread.status ? `
                    <div class="mb-2 small">
                        <span class="badge bg-light text-dark"><i class="bi bi-info-circle me-1"></i>${thread.status}</span>
                    </div>` : ''}

                    <!-- Mô tả ngắn thread -->
                    ${contentPreview ? `<p class="text-muted small mb-2 thread-content">${contentPreview}</p>` : ''}
                </div>

                <!-- Hình ảnh -->
                ${thread.featured_image ? `
                <div class="col-md-3 d-none d-md-block">
                    <div class="thread-image-container">
                        <img src="${thread.featured_image}" alt="${thread.title}" class="img-fluid rounded"
                            onerror="this.style.display='none'">
                    </div>
                </div>` : ''}
            </div>

            <div class="thread-item-footer">
                <div class="thread-meta-left">
                    <div class="thread-meta">
                        <span class="meta-item"><i class="bi bi-eye"></i> ${thread.view_count || 0} lượt xem</span>
                        <span class="meta-item"><i class="bi bi-chat"></i> ${thread.comments_count || thread.comment_count || 0} phản hồi</span>
                    </div>

                    <div class="thread-category-badges">
                        ${thread.category ? `
                        <a href="/threads?category=${thread.category.id}" class="badge bg-secondary text-decoration-none">
                            <i class="bi bi-tag"></i> ${thread.category.name}
                        </a>` : ''}

                        ${thread.forum ? `
                        <a href="/threads?forum=${thread.forum.id}" class="badge bg-info text-decoration-none">
                            <i class="bi bi-folder"></i> ${thread.forum.name}
                        </a>` : ''}
                    </div>
                </div>


            </div>
        `;

        return listItem;
    }

    /**
     * Tạo action buttons cho authenticated users
     * @param {Object} thread - Thread object
     * @returns {string} - HTML string cho action buttons
     */
    static generateActionButtons(thread) {
        // Kiểm tra authentication status từ global variable hoặc meta tag
        const isAuthenticated = window.isAuthenticated ||
            document.querySelector('meta[name="user-authenticated"]')?.content === 'true';

        if (!isAuthenticated) {
            return '';
        }

        const isBookmarked = thread.is_bookmarked || false;
        const isFollowed = thread.is_followed || false;

        return `
        <div class="thread-actions">
            ${isBookmarked ? `
            <!-- Remove bookmark form -->
            <form method="POST" action="/threads/${thread.id}/bookmark" style="display: inline;" class="bookmark-form">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || ''}">
                <button type="submit" class="btn btn-sm btn-primary" title="Bỏ bookmark">
                    <i class="bi bi-bookmark-fill"></i>
                    <span class="d-none d-md-inline ms-1">Đã lưu</span>
                </button>
            </form>` : `
            <!-- Add bookmark form -->
            <form method="POST" action="/threads/${thread.id}/bookmark" style="display: inline;" class="bookmark-form">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || ''}">
                <button type="submit" class="btn btn-sm btn-outline-primary" title="Thêm bookmark">
                    <i class="bi bi-bookmark"></i>
                    <span class="d-none d-md-inline ms-1">Lưu</span>
                </button>
            </form>`}

            ${isFollowed ? `
            <!-- Unfollow form -->
            <form method="POST" action="/threads/${thread.id}/follow" style="display: inline;" class="follow-form">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || ''}">
                <button type="submit" class="btn btn-sm btn-success" title="Bỏ theo dõi">
                    <i class="bi bi-bell-fill"></i>
                    <span class="d-none d-md-inline ms-1">Đang theo dõi</span>
                </button>
            </form>` : `
            <!-- Follow form -->
            <form method="POST" action="/threads/${thread.id}/follow" style="display: inline;" class="follow-form">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || ''}">
                <button type="submit" class="btn btn-sm btn-outline-success" title="Theo dõi">
                    <i class="bi bi-bell"></i>
                    <span class="d-none d-md-inline ms-1">Theo dõi</span>
                </button>
            </form>`}
        </div>`;
    }

    /**
     * Tạo placeholder skeleton loading cho thread item
     * @returns {HTMLElement} - Element HTML đại diện cho skeleton loading
     */
    static createSkeletonLoader() {
        const skeleton = document.createElement('div');
        skeleton.className = 'list-group-item thread-item thread-item-container skeleton-item';

        skeleton.innerHTML = `
            <div class="thread-item-header">
                <div class="thread-user-info">
                    <div class="flex-shrink-0 me-3 d-none d-sm-block skeleton-circle"></div>
                    <div>
                        <div class="skeleton-text skeleton-title"></div>
                        <div class="skeleton-text skeleton-short"></div>
                    </div>
                </div>
                <div class="thread-badges">
                    <div class="skeleton-badge"></div>
                    <!-- Skeleton action buttons -->
                    <div class="thread-actions">
                        <div class="skeleton-button"></div>
                        <div class="skeleton-button"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9">
                    <div class="thread-title-section">
                        <div class="skeleton-text skeleton-title-long"></div>
                    </div>
                    <div class="skeleton-text"></div>
                    <div class="skeleton-text"></div>
                    <div class="skeleton-text skeleton-short"></div>
                </div>
                <div class="col-md-3 d-none d-md-block">
                    <div class="skeleton-image"></div>
                </div>
            </div>

            <div class="thread-item-footer">
                <div class="thread-meta-left">
                    <div class="thread-meta">
                        <div class="skeleton-text skeleton-short"></div>
                    </div>
                    <div class="thread-category-badges">
                        <div class="skeleton-badge"></div>
                        <div class="skeleton-badge"></div>
                    </div>
                </div>

            </div>
        `;

        return skeleton;
    }

    /**
     * Chuyển đổi thời gian sang dạng "X thời gian trước"
     *
     * @param {Date} date - Đối tượng Date
     * @returns {string} - Chuỗi biểu diễn thời gian tương đối
     */
    static timeSince(date) {
        const seconds = Math.floor((new Date() - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) {
            return Math.floor(interval) + " năm trước";
        }

        interval = seconds / 2592000;
        if (interval > 1) {
            return Math.floor(interval) + " tháng trước";
        }

        interval = seconds / 86400;
        if (interval > 1) {
            return Math.floor(interval) + " ngày trước";
        }

        interval = seconds / 3600;
        if (interval > 1) {
            return Math.floor(interval) + " giờ trước";
        }

        interval = seconds / 60;
        if (interval > 1) {
            return Math.floor(interval) + " phút trước";
        }

        return Math.floor(seconds) + " giây trước";
    }

    /**
     * Hiển thị skeleton loading
     *
     * @param {HTMLElement} container - Container để thêm skeleton
     * @param {number} count - Số lượng skeleton items cần tạo
     */
    static showSkeletonLoading(container, count = 3) {
        // Xóa skeleton cũ nếu có
        const existingSkeletons = container.querySelectorAll('.skeleton-item');
        existingSkeletons.forEach(skeleton => skeleton.remove());

        // Thêm skeleton mới
        for (let i = 0; i < count; i++) {
            container.appendChild(this.createSkeletonLoader());
        }
    }

    /**
     * Loại bỏ skeleton loading
     *
     * @param {HTMLElement} container - Container chứa skeleton
     */
    static removeSkeletonLoading(container) {
        const skeletons = container.querySelectorAll('.skeleton-item');
        skeletons.forEach(skeleton => skeleton.remove());
    }
}

// Export để sử dụng ở nơi khác
window.ThreadItemBuilder = ThreadItemBuilder;
