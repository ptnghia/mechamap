/**
 * threads.js (FINAL full-feature parity)
 * Giữ toàn bộ tính năng ban đầu, tối ưu:
 *  - Event delegation
 *  - Gom utilities
 *  - Upload ảnh (main reply, inline reply, edit)
 *  - Realtime, highlight, scroll, quote, inline edit/reply
 */

(function (window, document) {
    'use strict';

    const CFG = window.ThreadPageConfig || {};
    const STATE = {
        editing: new Set(),
        activeInlineReply: null
    };

    /* ====================== Utilities ====================== */

    function transSafe(key) {
        try { if (typeof trans === 'function') return trans(key) || key; } catch (_) {}
        return key;
    }
    const qs  = (sel, ctx = document) => ctx.querySelector(sel);
    const qsa = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

    function ajaxJson(url, { method = 'GET', body, headers = {}, replaceMap, raw = false } = {}) {
        if (replaceMap) {
            Object.entries(replaceMap).forEach(([k, v]) => {
                url = url.replace(':' + k, encodeURIComponent(v));
            });
        }
        const finalHeaders = Object.assign({
            'X-Requested-With': 'XMLHttpRequest'
        }, headers);

        if (!(body instanceof FormData) && body && typeof body === 'object') {
            finalHeaders['Content-Type'] = 'application/json';
            body = JSON.stringify(body);
        }
        if (CFG.csrf && !finalHeaders['X-CSRF-TOKEN']) {
            finalHeaders['X-CSRF-TOKEN'] = CFG.csrf;
        }

        return fetch(url, { method, body, headers: finalHeaders })
            .then(async res => {
                if (raw) return res;
                let data;
                try { data = await res.json(); } catch (_) { data = {}; }
                if (!res.ok) {
                    const err = new Error(data.message || `HTTP ${res.status}`);
                    err.status = res.status;
                    err.payload = data;
                    throw err;
                }
                return data;
            });
    }

    function toast(message, type = 'info', timeout = 5000) {
        const map = { error: 'danger', success: 'success', info: 'info', warning: 'warning' };
        let container = qs('#thread-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'thread-toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = 9999;
            document.body.appendChild(container);
        }
        const el = document.createElement('div');
        el.className = `alert alert-${map[type] || 'info'} alert-dismissible fade show shadow-sm mb-2`;
        el.innerHTML = `
            <div>${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        container.appendChild(el);
        setTimeout(() => {
            if (el && el.parentNode) {
                el.classList.remove('show');
                setTimeout(() => el.remove(), 300);
            }
        }, timeout);
    }

    function showLoginOrRedirect() {
        const loginModal = qs('#loginModal');
        if (loginModal && window.bootstrap) {
            new bootstrap.Modal(loginModal).show();
        } else {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
        }
    }

    function formatTimeAgo(date) {
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        if (diff < 60) return 'vừa xong';
        if (diff < 3600) return Math.floor(diff / 60) + ' phút trước';
        if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước';
        return Math.floor(diff / 86400) + ' ngày trước';
    }

    function updateCommentCount({ delta = null, absolute = null }) {
        let newCount;
        if (absolute !== null) {
            newCount = absolute;
        } else if (delta !== null) {
            const header = qs('[data-type="replies-header"]') || qs('.title_page_sub');
            let current = 0;
            if (header) {
                const m = header.textContent.match(/(\d+)/);
                if (m) current = parseInt(m[1], 10);
            }
            newCount = Math.max(0, current + delta);
        }
        if (newCount === undefined) return;

        const header = qs('[data-type="replies-header"]') || qs('.title_page_sub');
        if (header) header.innerHTML = header.innerHTML.replace(/\d+/, newCount);

        qsa('[data-type="replies"], .comment-count, [data-comment-count], .comments-count')
            .forEach(el => {
                const icon = el.querySelector('i');
                if (icon && el.dataset.type === 'replies') {
                    el.innerHTML = `${icon.outerHTML} ${newCount.toLocaleString()} ${transSafe('thread.replies')}`;
                } else {
                    el.textContent = newCount.toLocaleString();
                }
            });
    }

    function highlightAndScroll(id) {
        const el = typeof id === 'string' ? qs(id.startsWith('#') ? id : `#${id}`) : id;
        if (!el) return;
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.classList.add('highlight-new');
        setTimeout(() => el.classList.remove('highlight-new'), 3000);
    }

    function isNearBottom(px = 200) {
        return window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - px;
    }

    /* ====================== Upload Helper ====================== */

    async function handleUploadComponent(form, formData) {
        const uploadComponent = form.querySelector('.comment-image-upload, .file-upload-component, .advanced-file-upload');
        if (!uploadComponent) return;

        // Component kiểu custom
        if (uploadComponent.commentImageUpload &&
            typeof uploadComponent.commentImageUpload.hasFiles === 'function' &&
            uploadComponent.commentImageUpload.hasFiles()) {
            try {
                const uploaded = await uploadComponent.commentImageUpload.uploadFiles();
                uploaded.forEach(item => {
                    if (item.url) {
                        formData.append('uploaded_images[]', item.url);
                    }
                });
            } catch (e) {
                console.warn('[Upload warning] Failed but continue:', e);
            }
        } else {
            // Fallback input file
            const fileInput = uploadComponent.querySelector('input[type="file"]');
            if (fileInput && fileInput.files?.length) {
                Array.from(fileInput.files).forEach(f => formData.append('images[]', f));
            }
        }
    }

    function clearUploadComponent(form) {
        const uploadComponent = form.querySelector('.comment-image-upload, .file-upload-component, .advanced-file-upload');
        if (!uploadComponent) return;
        // Custom instance
        if (uploadComponent.commentImageUpload &&
            typeof uploadComponent.commentImageUpload.clearFiles === 'function') {
            uploadComponent.commentImageUpload.clearFiles();
        }
        // Clear input fallback
        const fileInput = uploadComponent.querySelector('input[type="file"]');
        if (fileInput) fileInput.value = '';
        // Preview container
        const preview = uploadComponent.querySelector('.file-preview-area, .preview-grid');
        if (preview) preview.innerHTML = '';
    }

    /* ====================== Inline Reply (GIỮ LOGIC CŨ) ====================== */

    function showInlineReplyForm(commentId) {
        qsa('.inline-reply-form').forEach(f => f.style.display = 'none');
        const wrapper = qs(`#inline-reply-${commentId}`);
        if (!wrapper) return;
        wrapper.style.display = 'block';
        STATE.activeInlineReply = commentId;
        const editorId = `inline-reply-content-${commentId}`;
        if (window.tinymce && !tinymce.get(editorId)) {
            setTimeout(() => {
                tinymce.init({
                    selector: `#${editorId}`,
                    height: 160,
                    menubar: false,
                    branding: false,
                    license_key: 'gpl',
                    plugins: 'link lists emoticons code',
                    toolbar: 'bold italic underline | bullist numlist | link emoticons | code',
                    setup(ed) {
                        ed.on('init', () => ed.focus());
                        ed.on('change keyup', () => {
                            const ta = qs(`#${editorId}`);
                            if (ta) ta.value = ed.getContent();
                        });
                    }
                });
            }, 40);
        }
    }

    function hideInlineReplyForm(commentId) {
        const wrapper = qs(`#inline-reply-${commentId}`);
        if (!wrapper) return;
        wrapper.style.display = 'none';
        const editorId = `inline-reply-content-${commentId}`;
        if (window.tinymce && tinymce.get(editorId)) {
            tinymce.get(editorId).setContent('');
        } else {
            const ta = qs(`#${editorId}`);
            if (ta) ta.value = '';
        }
        STATE.activeInlineReply = null;
    }

    /* ====================== Inline Edit (GIỮ LOGIC CŨ) ====================== */

    function showInlineEditForm(commentId) {
        if (STATE.editing.has(commentId)) return;
        const contentEl = qs(`#comment-content-${commentId}`);
        const formEl = qs(`#edit-form-${commentId}`);
        const textarea = qs(`#edit-content-${commentId}`);
        if (!contentEl || !formEl || !textarea) return;

        contentEl.style.display = 'none';
        formEl.style.display = 'block';
        STATE.editing.add(commentId);

        const editorId = `edit-content-${commentId}`;
        if (window.tinymce && !tinymce.get(editorId)) {
            setTimeout(() => {
                tinymce.init({
                    selector: `#${editorId}`,
                    height: contentEl.closest('.comment_item_body.sub') ? 150 : 200,
                    menubar: false,
                    branding: false,
                    license_key: 'gpl',
                    plugins: 'advlist autolink lists link image charmap searchreplace code fullscreen table emoticons',
                    toolbar: 'undo redo | bold italic underline | bullist numlist | link emoticons | code',
                    setup(ed) {
                        ed.on('init', () => ed.focus());
                        ed.on('change keyup', () => { textarea.value = ed.getContent(); });
                        ed.on('keydown', e => {
                            if (e.key === 'Escape') {
                                e.preventDefault();
                                hideInlineEditForm(commentId);
                            }
                        });
                    }
                });
            }, 50);
        }
    }

    function hideInlineEditForm(commentId) {
        const contentEl = qs(`#comment-content-${commentId}`);
        const formEl = qs(`#edit-form-${commentId}`);
        if (!contentEl || !formEl) return;
        formEl.style.display = 'none';
        contentEl.style.display = '';
        const editorId = `edit-content-${commentId}`;
        if (window.tinymce && tinymce.get(editorId)) {
            tinymce.get(editorId).remove();
        }
        STATE.editing.delete(commentId);
    }

    // Click outside để hủy edit (giống logic gốc)
    document.addEventListener('click', (e) => {
        qsa('.inline-edit-form').forEach(formWrapper => {
            if (formWrapper.style.display === 'none') return;
            if (!formWrapper.contains(e.target) && !e.target.closest('.inline-edit-comment-btn')) {
                const id = formWrapper.id.replace('edit-form-', '');
                hideInlineEditForm(id);
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Hủy tất cả edit đang mở
            STATE.editing.forEach(id => hideInlineEditForm(id));
            if (STATE.activeInlineReply) hideInlineReplyForm(STATE.activeInlineReply);
        }
    });

    /* ====================== DOM Builders ====================== */

    function buildCommentHtml(comment, { isReply = false } = {}) {
        const user = comment.user || {};
        const avatar = user.avatar_url || user.avatar || '/images/default-avatar.png';
        const profile = user.username ? `/users/${user.username}` : `/users/${user.id || ''}`;
        const joinDate = user.created_at
            ? new Date(user.created_at).toLocaleDateString('vi-VN', { month: 'short', year: 'numeric' })
            : '';
        const timeAgo = comment.created_at ? formatTimeAgo(new Date(comment.created_at)) : '...';
        const canEdit = String(CFG.userId) === String(user.id);
        const isLiked = !!comment.is_liked;
        const attachmentsHtml = (comment.attachments && comment.attachments.length > 0)
            ? `<div class="${isReply ? 'reply-attachments' : 'comment-attachments'} mt-2">
                <div class="row g-2 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5">
                    ${comment.attachments.map(att => `
                        <div class="col">
                            <div class="comment-image-wrapper position-relative">
                                <a href="${att.url}" data-fancybox="${isReply ? 'reply-' : 'comment-'}${comment.id}-images" data-caption="${att.file_name}">
                                    <img src="${att.url}" class="img-fluid rounded" alt="${att.file_name}">
                                </a>
                                ${canEdit ? `
                                <button type="button" class="btn btn-danger btn-sm delete-image-btn position-absolute"
                                        data-image-id="${att.id}"
                                        data-comment-id="${comment.id}"
                                        style="top:5px;right:5px;"
                                        title="${transSafe('thread.delete_image')}">
                                    <i class="fas fa-trash"></i>
                                </button>` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>`
            : '';

        return `
        <div class="comment_item mb-3 highlight-insert" id="comment-${comment.id}" data-comment-id="${comment.id}" ${isReply ? 'data-reply="true"' : ''}>
          <div class="d-flex">
            <div class="comment_item_avatar">
              <img src="${avatar}" class="rounded-circle me-2" width="${isReply ? 30 : 40}" height="${isReply ? 30 : 40}" alt="${user.name || ''}">
            </div>
            <div class="comment_item_body ${isReply ? 'sub' : ''}">
              <div class="comment_item_user">
                <a href="${profile}" class="fw-bold text-decoration-none">${user.name || 'User'}</a>
                <div class="text-muted small">
                  <span>${user.comments_count || 0} ${transSafe('thread.comments')}</span> ·
                  <span>${transSafe('thread.joined')} ${joinDate}</span>
                </div>
              </div>
              <div class="comment_item_content" id="comment-content-${comment.id}">
                ${comment.content || ''}
              </div>
              ${attachmentsHtml}
              <div class="comment_item_meta d-flex justify-content-between align-items-center mt-1">
                <div class="d-flex align-items-center">
                  <span class="btn btn-sm btn_meta text-muted">
                    <i class="fa-regular fa-clock me-1"></i> ${timeAgo}
                  </span>
                  ${CFG.userId ? `
                  <button type="button"
                          class="btn text-muted btn-sm no-border btn_meta comment-like-btn ${isLiked ? 'active' : ''}"
                          data-comment-id="${comment.id}"
                          data-liked="${isLiked}"
                          title="${isLiked ? transSafe('thread.unlike') : transSafe('thread.like')}">
                      <i class="fas fa-thumbs-up me-1"></i>
                      <span class="comment-like-count-${comment.id} me-1">${comment.like_count || 0}</span>
                      <span class="text">${transSafe('thread.like')}</span>
                  </button>
                  ` : `
                  <button type="button"
                          class="btn text-muted btn-sm no-border btn_meta comment-like-btn"
                          onclick="showLoginModal()"
                          title="${transSafe('thread.login_to_like')}">
                      <i class="fas fa-thumbs-up me-1"></i>
                      <span class="comment-like-count-${comment.id} me-1">${comment.like_count || 0}</span>
                      <span class="text">${transSafe('thread.like')}</span>
                  </button>
                  `}
                </div>
                <div class="d-flex align-items-center">
                  <button class="btn btn-sm text-muted no-border btn_meta quote-button"
                          data-comment-id="${comment.id}"
                          data-user-name="${user.name || ''}">
                      <i class="fa-solid fa-quote-left me-1"></i> <span class="text">${transSafe('thread.quote')}</span>
                  </button>
                  <button class="btn text-muted btn-sm no-border btn_meta reply-button ms-2"
                          data-parent-id="${comment.id}">
                      <i class="fas fa-reply me-1"></i> <span class="text">${transSafe('thread.reply')}</span>
                  </button>
                  ${canEdit ? `
                  <button class="btn text-warning btn-sm no-border btn_meta inline-edit-comment-btn"
                          data-comment-id="${comment.id}">
                      <i class="fas fa-edit me-1"></i> <span class="text">${transSafe('thread.edit')}</span>
                  </button>
                  <button type="button"
                          class="btn text-danger btn-sm no-border btn_meta delete-comment-btn"
                          data-comment-id="${comment.id}"
                          data-comment-type="${isReply ? 'reply' : 'comment'}">
                      <i class="fas fa-trash me-1"></i> <span class="text">${transSafe('thread.delete')}</span>
                  </button>` : ''}
                </div>
              </div>
              ${!isReply ? '<div class="comment_sub"></div>' : ''}
            </div>
          </div>
        </div>
        `;
    }

    function appendNewComment(comment, isReply = false) {
        if (isReply) {
            const parent = qs(`#comment-${comment.parent_id} .comment_sub`);
            if (!parent) return;
            parent.insertAdjacentHTML('beforeend', buildCommentHtml(comment, { isReply: true }));
            setTimeout(() => highlightAndScroll(`comment-${comment.id}`), 50);
        } else {
            const container = qs('#comments-container');
            if (!container) return;
            const currentSort = (new URLSearchParams(window.location.search).get('sort')) || 'newest';
            const html = buildCommentHtml(comment);
            if (currentSort === 'newest') {
                container.insertAdjacentHTML('afterbegin', html);
            } else {
                const pagination = qs('#comments-pagination');
                if (pagination) {
                    pagination.insertAdjacentHTML('beforebegin', html);
                } else {
                    container.insertAdjacentHTML('beforeend', html);
                }
            }
            setTimeout(() => highlightAndScroll(`comment-${comment.id}`), 60);
        }
    }

    function updateCommentInDOM(commentId, dataComment) {
        const contentEl = qs(`#comment-content-${commentId}`);
        if (contentEl) {
            contentEl.innerHTML = dataComment.content;
        }
        // Attachments
        const container = contentEl ? contentEl.parentNode : null;
        if (!container) return;
        let existing = container.querySelector('.comment-attachments, .reply-attachments');
        if (dataComment.attachments && dataComment.attachments.length > 0) {
            const isReply = container.classList.contains('sub');
            if (!existing) {
                existing = document.createElement('div');
                existing.className = isReply ? 'reply-attachments mt-2' : 'comment-attachments mt-3';
                container.insertBefore(existing, contentEl.nextSibling);
            }
            existing.innerHTML = `
                <div class="row g-2">
                    ${dataComment.attachments.map(a => `
                    <div class="col-md-3 col-sm-4 col-6">
                        <div class="comment-image-wrapper position-relative">
                            <a href="${a.url}" data-fancybox="comment-${commentId}-images" data-caption="${a.file_name}">
                                <img src="${a.url}" class="img-fluid rounded" alt="${a.file_name}">
                            </a>
                            ${String(a.user_id) === String(CFG.userId) ? `
                            <button type="button" class="btn btn-danger btn-sm delete-image-btn position-absolute"
                                    data-image-id="${a.id}"
                                    data-comment-id="${commentId}"
                                    style="top:5px;right:5px;">
                                <i class="fas fa-trash"></i>
                            </button>` : ''}
                        </div>
                    </div>
                    `).join('')}
                </div>
            `;
        } else if (existing) {
            existing.remove();
        }
    }

    /* ====================== Quote ====================== */

    function insertQuoteIntoMain(commentId, userName) {
        const contentEl = qs(`#comment-content-${commentId}`);
        if (!contentEl) return;
        const html = `
<blockquote>
<p><strong>${userName} đã viết:</strong></p>
${contentEl.innerHTML}
</blockquote>
<p></p>`;
        if (window.tinymce && tinymce.get('content')) {
            tinymce.get('content').insertContent(html);
            const form = qs('#reply-form');
            if (form) form.scrollIntoView({ behavior: 'smooth' });
            setTimeout(() => tinymce.get('content').focus(), 80);
        } else {
            const ta = qs('#content');
            if (ta) {
                ta.value += html;
                ta.focus();
            }
        }
    }

    /* ====================== Forms ====================== */

    async function handleMainReplySubmit(form) {
        const submitBtn = form.querySelector('#submit-reply-btn');
        const editor = window.tinymce ? tinymce.get('content') : null;
        const textarea = qs('#content', form);
        let content = editor ? editor.getContent().trim() : (textarea ? textarea.value.trim() : '');

        if (!content || content === '<p></p>' || content === '<p><br></p>') {
            const contentError = qs('#content-error');
            if (contentError) contentError.style.display = 'block';
            (textarea || editor).classList?.add?.('is-invalid');
            toast(transSafe('thread.reply_content_required'), 'error');
            if (editor) editor.focus();
            return;
        }

        // Hide previous error
        const contentError = qs('#content-error');
        if (contentError) contentError.style.display = 'none';

        submitBtn.disabled = true;
        const oldHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${transSafe('thread.sending')}`;

        try {
            const formData = new FormData(form);
            if (editor) formData.set('content', content);

            await handleUploadComponent(form, formData);

            const data = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(r => r.json());

            if (!data.success) throw new Error(data.message || 'Server error');

            toast(data.message || transSafe('thread.reply_posted_successfully'), 'success');

            // Clear
            if (editor) editor.setContent('');
            if (textarea) textarea.value = '';
            clearUploadComponent(form);

            if (data.comment) {
                appendNewComment(data.comment, false);
            }
            if (typeof data.comment_count === 'number') {
                updateCommentCount({ absolute: data.comment_count });
            } else {
                updateCommentCount({ delta: 1 });
            }

        } catch (e) {
            toast(e.message || transSafe('thread.reply_posting_error'), 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = oldHtml;
        }
    }

    async function handleInlineReplySubmit(form) {
        // Lấy parentId đúng: form hiện chỉ có data-comment-id, không có data-parent-id
        const parentId = form.getAttribute('data-parent-id')
            || form.getAttribute('data-comment-id')
            || (form.querySelector('input[name="parent_id"]')?.value);
        if (!parentId) {
            toast('Không xác định được bình luận cha', 'error');
            return;
        }
        const submitBtn = form.querySelector('.submit-inline-reply');
        const editorId = `inline-reply-content-${parentId}`;
        const editor = window.tinymce ? tinymce.get(editorId) : null;
        const textarea = qs('#' + editorId);
        const content = editor ? editor.getContent().trim() : (textarea ? textarea.value.trim() : '');

        if (!content || content === '<p></p>' || content === '<p><br></p>') {
            toast(transSafe('thread.reply_content_required'), 'error');
            return;
        }

        submitBtn.disabled = true;
        const old = submitBtn.innerHTML;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${transSafe('thread.sending')}`;

        try {
            const fd = new FormData();
            fd.append('_token', CFG.csrf);
            fd.append('content', content);
            fd.append('parent_id', parentId);

            await handleUploadComponent(form, fd);

            const data = await ajaxJson(CFG.routes.commentStore, { method: 'POST', body: fd });
            if (!data.success) throw new Error(data.message || 'Server error');
            toast(transSafe('thread.reply_posted_successfully'), 'success');

            if (data.comment) appendNewComment(data.comment, true);
            if (typeof data.comment_count === 'number') {
                updateCommentCount({ absolute: data.comment_count });
            } else {
                updateCommentCount({ delta: 1 });
            }

            if (editor) editor.setContent('');
            clearUploadComponent(form);
            hideInlineReplyForm(parentId);

        } catch (e) {
            toast(e.message || transSafe('thread.reply_posting_error'), 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = old;
        }
    }

    async function handleEditCommentSubmit(form) {
        const commentId = form.getAttribute('data-comment-id');
        const editorId = `edit-content-${commentId}`;
        const editor = window.tinymce ? tinymce.get(editorId) : null;
        const textarea = qs('#' + editorId);
        const content = editor ? editor.getContent().trim() : (textarea ? textarea.value.trim() : '');

        if (!content) {
            toast(transSafe('thread.content_required'), 'error');
            return;
        }

        const submitBtn = form.querySelector('.save-comment-btn');
        const old = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${transSafe('thread.saving')}`;

        const fd = new FormData();
        fd.append('_token', CFG.csrf);
        fd.append('_method', 'PUT');
        fd.append('content', content);

        await handleUploadComponent(form, fd);

        try {
            const res = await ajaxJson(`/api/comments/${commentId}`, { method: 'POST', body: fd, raw: true });
            if (res.status === 419) {
                toast('Phiên làm việc hết hạn. Đang tải lại...', 'warning');
                setTimeout(() => location.reload(), 1200);
                return;
            }
            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Server error');

            updateCommentInDOM(commentId, data.comment);
            toast(transSafe('thread.comment_updated_successfully'), 'success');
            hideInlineEditForm(commentId);
        } catch (e) {
            toast(e.message || transSafe('thread.update_failed'), 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = old;
        }
    }

    /* ====================== Thread actions ====================== */

    async function likeThread(btn) {
        if (!CFG.userId) return showLoginOrRedirect();
        const isLiked = btn.dataset.liked === 'true';
        btn.disabled = true;
        try {
            const data = await ajaxJson(CFG.routes.threadLike, { method: 'POST' });
            if (!data.success) throw new Error(data.message || 'Error');
            btn.dataset.liked = (!isLiked).toString();
            btn.classList.toggle('active', !isLiked);
            const countEl = btn.querySelector('.like-count');
            if (countEl) countEl.textContent = data.like_count;
            toast(data.message, 'success');
        } catch (e) {
            toast(e.message, 'error');
        } finally {
            btn.disabled = false;
        }
    }

    async function saveThread(btn) {
        if (!CFG.userId) return showLoginOrRedirect();
        const isSaved = btn.dataset.saved === 'true';
        btn.disabled = true;
        const old = btn.innerHTML;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${transSafe('ui.status.processing')}`;
        try {
            const data = await ajaxJson(CFG.routes.threadSave, { method: 'POST' });
            if (!data.success) throw new Error(data.message || 'Error');
            btn.dataset.saved = (!isSaved).toString();
            btn.classList.toggle('btn-primary', !isSaved);
            btn.classList.toggle('btn-outline-primary', isSaved);
            btn.innerHTML = (!isSaved)
                ? `<i class="fa-solid fa-bookmark me-1"></i><span class="save-text">${transSafe('ui.actions.saved')}</span>${data.saves_count ? `<span class="badge bg-danger text-dark ms-1 save-count">${data.saves_count}</span>` : ''}`
                : `<i class="fa-regular fa-bookmark me-1"></i><span class="save-text">${transSafe('ui.actions.save')}</span>${data.saves_count ? `<span class="badge bg-danger text-dark ms-1 save-count">${data.saves_count}</span>` : ''}`;
            toast(data.message, 'success');
        } catch (e) {
            toast(e.message, 'error');
            btn.innerHTML = old;
        } finally {
            btn.disabled = false;
        }
    }

    async function followThread(btn) {
        if (!CFG.userId) return showLoginOrRedirect();
        const isFollowing = btn.dataset.following === 'true';
        btn.disabled = true;
        const old = btn.innerHTML;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${transSafe('ui.status.processing')}`;

        try {
            const method = isFollowing ? 'DELETE' : 'POST';
            const data = await ajaxJson(CFG.routes.threadFollow, { method });
            if (!data.success) throw new Error(data.message || 'Error');
            btn.dataset.following = data.is_following ? 'true' : 'false';
            btn.classList.toggle('btn-success', data.is_following);
            btn.classList.toggle('btn-outline-primary', !data.is_following);
            btn.innerHTML = data.is_following
                ? `<i class="fas fa-bell me-1"></i><span class="follow-text">${transSafe('ui.actions.following')}</span><span class="follower-count badge bg-light text-dark ms-1">${data.follower_count}</span>`
                : `<i class="fas fa-bell-slash me-1"></i><span class="follow-text">${transSafe('ui.actions.follow')}</span><span class="follower-count badge bg-light text-dark ms-1">${data.follower_count}</span>`;
            toast(data.message, 'success');
        } catch (e) {
            toast(e.message, 'error');
            btn.innerHTML = old;
        } finally {
            btn.disabled = false;
        }
    }

    /* ====================== Comment actions ====================== */

    async function likeComment(btn) {
        if (!CFG.userId) return showLoginOrRedirect();
        const commentId = btn.dataset.commentId;
        const isLiked = btn.dataset.liked === 'true';
        btn.disabled = true;
        try {
            const data = await ajaxJson(CFG.routes.commentLike, {
                method: 'POST',
                replaceMap: { id: commentId }
            });
            if (!data.success) throw new Error(data.message || 'Error');
            btn.dataset.liked = (!isLiked).toString();
            btn.classList.toggle('active', !isLiked);
            const c = qs(`.comment-like-count-${commentId}`, btn);
            if (c) c.textContent = data.like_count;
            toast(data.message, 'success');
        } catch (e) {
            toast(e.message, 'error');
        } finally {
            btn.disabled = false;
        }
    }

    async function deleteComment(btn) {
        if (!CFG.userId) return showLoginOrRedirect();
        const id = btn.dataset.commentId;
        const type = btn.dataset.commentType;
        const confirmMsg = type === 'reply'
            ? transSafe('features.threads.delete_reply_message')
            : transSafe('features.threads.delete_comment_message');

        const confirmRes = await window.showDeleteConfirm(confirmMsg);
        if (!confirmRes.isConfirmed) return;

        const old = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const data = await ajaxJson(CFG.routes.commentDelete, { method: 'DELETE', replaceMap: { id } });
            if (!data.success) throw new Error(data.message || 'Error');
            const el = qs(`#comment-${id}`);
            if (el) {
                el.style.transition = 'opacity .25s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 260);
            }
            toast(data.message || transSafe('thread.comment_deleted_successfully'), 'success');
            if (typeof data.comment_count === 'number') {
                updateCommentCount({ absolute: data.comment_count });
            } else {
                updateCommentCount({ delta: -1 });
            }
        } catch (e) {
            toast(e.message || transSafe('thread.request_error'), 'error');
            btn.innerHTML = old;
            btn.disabled = false;
        }
    }

    async function deleteCommentImage(btn) {
        if (!CFG.userId) return showLoginOrRedirect();
        const imageId = btn.dataset.imageId;
        const commentId = btn.dataset.commentId;
        const confirmRes = await window.showDeleteConfirm(transSafe('ui.confirmations.delete_image'));
        if (!confirmRes.isConfirmed) return;

        const wrapper = btn.closest('.comment-image-wrapper') || btn.parentNode;
        const old = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const data = await ajaxJson(CFG.routes.commentImageDelete, {
                method: 'DELETE',
                replaceMap: { commentId, imageId }
            });
            if (!data.success) throw new Error(data.message || 'Error');
            wrapper.style.transition = 'opacity .25s';
            wrapper.style.opacity = '0';
            setTimeout(() => wrapper.remove(), 250);
            toast(transSafe('ui.messages.delete_image_success') || 'Deleted', 'success');
        } catch (e) {
            toast(e.message || transSafe('ui.messages.delete_image_error'), 'error');
            btn.innerHTML = old;
            btn.disabled = false;
        }
    }

    /* ====================== Sorting ====================== */

    function sortComments(btn) {
        const sortType = btn.dataset.sort;
        if (!sortType) return;

        qsa('.sort-btn').forEach(b => {
            b.classList.toggle('btn-primary', b === btn);
            b.classList.toggle('btn-outline-primary', b !== btn);
        });

        const container = qs('#comments-container');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i><br>
                    ${transSafe('ui.status.loading_comments')}
                </div>`;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortType);

        fetch(url.toString(), {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
        })
            .then(res => {
                if (!res.ok) throw new Error('Server error');
                return res.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.getElementById('comments-container');
                if (newContainer && container) {
                    container.innerHTML = newContainer.innerHTML;
                    window.history.pushState({}, '', url.toString());
                    toast(transSafe('ui.messages.comments_sorted'), 'success');
                }
            })
            .catch(err => {
                toast(err.message || transSafe('ui.messages.request_error'), 'error');
                window.location.href = url.toString();
            });
    }

    /* ====================== Realtime ====================== */

    function initRealtime() {
        if (!window.notificationService || !window.notificationService.socket) return;
        const socket = window.notificationService.socket;
        const ch = `thread.${CFG.threadId}`;
        socket.emit('subscribe_request', { channel: ch });

        socket.on('comment.created', data => {
            if (!data.comment) return;
            if (String(data.comment.user.id) === String(CFG.userId)) return;
            const nearBottom = isNearBottom();
            appendNewComment(data.comment, !!data.comment.parent_id);
            if (typeof data.comment_count === 'number') {
                updateCommentCount({ absolute: data.comment_count });
            } else if (!data.comment.parent_id) {
                updateCommentCount({ delta: 1 });
            }
            if (nearBottom) {
                setTimeout(() => highlightAndScroll(`comment-${data.comment.id}`), 200);
            }
            toast(`${data.comment.user.name} ${transSafe('thread.reply_posted_successfully')}`, 'info', 3000);
        });

        socket.on('comment.updated', data => {
            if (!data.comment) return;
            updateCommentInDOM(data.comment.id, data.comment);
            toast(transSafe('thread.comment_updated_successfully'), 'info');
        });

        socket.on('comment.deleted', data => {
            const el = qs(`#comment-${data.comment_id}`);
            if (el) {
                el.style.transition = 'opacity .25s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 260);
                if (typeof data.comment_count === 'number') {
                    updateCommentCount({ absolute: data.comment_count });
                } else {
                    updateCommentCount({ delta: -1 });
                }
                toast(transSafe('thread.comment_deleted_successfully'), 'warning');
            }
        });

        socket.on('thread.like.updated', data => {
            if (String(data.user_id) === String(CFG.userId)) return;
            qsa('.like-count').forEach(el => el.textContent = data.like_count);
        });

        socket.on('comment.like.updated', data => {
            if (String(data.user_id) === String(CFG.userId)) return;
            const cnt = qs(`.comment-like-count-${data.comment_id}`);
            if (cnt) cnt.textContent = data.like_count;
        });

        socket.on('thread.stats.updated', data => {
            const stats = data.stats;
            if (stats.comments_count !== undefined) {
                updateCommentCount({ absolute: stats.comments_count });
            }
            if (stats.participants_count !== undefined) {
                qsa('[data-type="participants"]').forEach(el => {
                    const icon = el.querySelector('i') || document.createElement('i');
                    if (!icon.className) icon.className = 'fas fa-users';
                    el.innerHTML = `${icon.outerHTML} ${stats.participants_count.toLocaleString()} ${transSafe('thread.participants')}`;
                });
            }
        });
    }

    /* ====================== Event Delegation ====================== */

    function handleClick(e) {
        const t = e.target;

        const likeBtn = t.closest('.btn-like');
        if (likeBtn?.dataset.threadSlug) { e.preventDefault(); likeThread(likeBtn); return; }

        const saveBtn = t.closest('.btn-save');
        if (saveBtn?.dataset.threadSlug) { e.preventDefault(); saveThread(saveBtn); return; }

        const followBtn = t.closest('.thread-follow-btn');
        if (followBtn?.dataset.threadSlug) { e.preventDefault(); followThread(followBtn); return; }

        const commentLike = t.closest('.comment-like-btn');
        if (commentLike?.dataset.commentId) { e.preventDefault(); likeComment(commentLike); return; }

        const deleteBtn = t.closest('.delete-comment-btn');
        if (deleteBtn?.dataset.commentId) { e.preventDefault(); deleteComment(deleteBtn); return; }

        const replyBtn = t.closest('.reply-button');
        if (replyBtn?.dataset.parentId) {
            e.preventDefault();
            if (!CFG.userId) return showLoginOrRedirect();
            showInlineReplyForm(replyBtn.dataset.parentId);
            return;
        }

        const cancelInline = t.closest('.cancel-inline-reply');
        if (cancelInline?.dataset.commentId || cancelInline?.dataset.parentId) {
            e.preventDefault();
            hideInlineReplyForm(cancelInline.dataset.commentId || cancelInline.dataset.parentId);
            return;
        }

        const quoteBtn = t.closest('.quote-button');
        if (quoteBtn?.dataset.commentId) {
            e.preventDefault();
            if (!CFG.userId) return showLoginOrRedirect();
            insertQuoteIntoMain(quoteBtn.dataset.commentId, quoteBtn.dataset.userName);
            return;
        }

        const editBtn = t.closest('.inline-edit-comment-btn');
        if (editBtn?.dataset.commentId) {
            e.preventDefault();
            if (!CFG.userId) return showLoginOrRedirect();
            showInlineEditForm(editBtn.dataset.commentId);
            return;
        }

        const cancelEdit = t.closest('.cancel-edit-btn');
        if (cancelEdit?.dataset.commentId) {
            e.preventDefault();
            hideInlineEditForm(cancelEdit.dataset.commentId);
            return;
        }

        const delImgBtn = t.closest('.delete-image-btn');
        if (delImgBtn) { e.preventDefault(); deleteCommentImage(delImgBtn); return; }

        const sortBtn = t.closest('.sort-btn');
        if (sortBtn) { e.preventDefault(); sortComments(sortBtn); return; }
    }

    function handleSubmit(e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        if (form.id === 'reply-form-element') {
            e.preventDefault();
            handleMainReplySubmit(form);
            return;
        }
        if (form.classList.contains('inline-reply-form-element')) {
            e.preventDefault();
            handleInlineReplySubmit(form);
            return;
        }
        if (form.classList.contains('comment-edit-form')) {
            e.preventDefault();
            handleEditCommentSubmit(form);
            return;
        }
    }

    /* ====================== Scroll target ====================== */

    function scrollToCommentIfNeeded() {
        if (CFG.scrollToCommentId) {
            const el = qs(`#comment-${CFG.scrollToCommentId}`);
            if (el) {
                setTimeout(() => highlightAndScroll(`comment-${CFG.scrollToCommentId}`), 400);
            }
        }
    }

    /* ====================== Init ====================== */

    function init() {
        document.addEventListener('click', handleClick);
        document.addEventListener('submit', handleSubmit);
        initRealtime();
        scrollToCommentIfNeeded();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Debug helpers (optional)
    window.ThreadPage = {
        showInlineReplyForm,
        hideInlineReplyForm,
        showInlineEditForm,
        hideInlineEditForm
    };

})(window, document);
