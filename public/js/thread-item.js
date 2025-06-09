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

        // Format date
        const createdAt = new Date(thread.created_at);
        const timeAgo = this.timeSince(createdAt);

        // Đảm bảo user có avatar
        const userAvatar = thread.user?.profile_photo_url || '/images/default-avatar.png';

        // Đảm bảo thread có slug (fallback to id)
        const threadUrl = thread.slug ? `/threads/${thread.slug}` : `/threads/${thread.id}`;

        // Xử lý content preview
        const contentPreview = thread.content ?
            (thread.content.length > 120 ? thread.content.substring(0, 120) + '...' : thread.content) : '';

        // Build the HTML với cấu trúc giống partial view
        listItem.innerHTML = `
            <!-- Thread Header với user info và badges -->
            <div class="thread-item-header">
                <div class="thread-user-info">
                    <div class="flex-shrink-0 me-3 d-none d-sm-block">
                        <img src="${userAvatar}" alt="${thread.user?.name || 'User'}" class="avatar">
                    </div>
                    <div>
                        <strong class="thread-user-name">${thread.user?.name || 'Người dùng'}</strong><br>
                        <span class="d-none d-md-inline text-muted">${timeAgo}</span>
                    </div>
                </div>
                <div class="thread-badges">
                    ${thread.is_sticky ? `<span class="badge bg-primary"><i class="bi bi-pin-angle"></i> ${translations.sticky}</span>` : ''}
                    ${thread.is_locked ? `<span class="badge bg-danger"><i class="bi bi-lock-fill"></i> ${translations.locked}</span>` : ''}
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

                <!-- Hình ảnh - chỉ hiển thị khi có ảnh -->
                ${thread.featured_image ? `
                <div class="col-md-3 d-none d-md-block">
                    <div class="thread-image-container">
                        <img src="${thread.featured_image}" alt="${thread.title}" class="img-fluid rounded"
                            onerror="this.style.display='none'">
                    </div>
                </div>` : ''}
            </div>

            <div class="thread-item-footer">
                <div class="thread-meta">
                    <span class="meta-item"><i class="bi bi-eye"></i> ${thread.view_count || 0} lượt xem</span>
                    <span class="meta-item"><i class="bi bi-chat"></i> ${thread.comments_count || 0} phản hồi</span>
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
        `;

        return listItem;
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
                <div class="thread-meta">
                    <div class="skeleton-text skeleton-short"></div>
                </div>
                <div class="thread-category-badges">
                    <div class="skeleton-badge"></div>
                    <div class="skeleton-badge"></div>
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
