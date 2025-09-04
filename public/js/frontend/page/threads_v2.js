/**
 * Thread Page Unified Script (Refactored + Edit Image Upload)
 * - Thread actions: like, save, follow
 * - Comment: create, inline reply, edit (hidden form), delete, like
 * - Add NEW images while editing (images[] + uploaded_images[])
 * - Image delete
 * - Sort comments (AJAX)
 * - Real-time updates
 * - I18n via ThreadPageConfig.i18n
 * - Event delegation
 */

(function() {
    'use strict';

    if (!window.ThreadPageConfig) {
        console.warn('ThreadPageConfig not found');
        return;
    }

    const CFG  = window.ThreadPageConfig;
    const I18N = CFG.i18n || {};
    const isAuth = !!CFG.userId;

    /* ===================== Utilities ===================== */
    function t(key, fallback='') {
        if (I18N[key]) return I18N[key];
        if (typeof window.trans === 'function') {
            try { return window.trans(key) || fallback || key; } catch(_) {}
        }
        return fallback || key;
    }
    function qs(sel, ctx=document)  { return ctx.querySelector(sel); }
    function qsa(sel, ctx=document) { return Array.from(ctx.querySelectorAll(sel)); }

    function showToast(message, type='info') {
        const kind = type === 'error' ? 'danger' : type;
        const el = document.createElement('div');
        el.className = `alert alert-${kind} alert-dismissible fade show position-fixed`;
        el.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
        el.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 5000);
    }

    function showLoginModalOrRedirect() {
        const loginModal = document.getElementById('loginModal');
        if (loginModal && window.bootstrap) {
            new bootstrap.Modal(loginModal).show();
        } else {
            window.location.href = '/login?redirect=' + encodeURIComponent(location.href);
        }
    }
    window.__threadLogin = showLoginModalOrRedirect;

    function buildUrl(pattern, replacements) {
        return Object.entries(replacements).reduce((acc,[k,v]) => acc.replace(`{${k}}`, v), pattern);
    }

    function setCSRF(headers={}) {
        headers['X-CSRF-TOKEN'] = CFG.csrf;
        return headers;
    }

    function formatTimeAgo(date) {
        if (!(date instanceof Date)) date = new Date(date);
        const diff = (Date.now() - date.getTime()) / 1000;
        if (diff < 60) return 'vừa xong';
        if (diff < 3600) return Math.floor(diff/60) + ' phút trước';
        if (diff < 86400) return Math.floor(diff/3600) + ' giờ trước';
        return Math.floor(diff/86400) + ' ngày trước';
    }

    function applyCommentCountDelta(delta) {
        qsa('[data-type="replies"], .comment-count, .comments-count').forEach(el => {
            const raw = el.textContent;
            const numMatch = raw.match(/\d+/);
            if (numMatch) {
                const newNum = Math.max(parseInt(numMatch[0],10) + delta, 0);
                el.textContent = raw.replace(/\d+/, newNum);
            }
        });
        const header = qs('[data-type="replies-header"]');
        if (header) {
            const m = header.textContent.match(/\d+/);
            if (m) {
                const val = Math.max(parseInt(m[0],10) + delta, 0);
                header.innerHTML = header.innerHTML.replace(/\d+/, val);
            }
        }
    }

    /* ===================== Templates ===================== */
    function userProfileUrl(user) {
        return user.username ? `/users/${user.username}` : `/users/${user.id}`;
    }

    function attachmentsHTML(attachments, commentId, canEdit, isReply) {
        if (!attachments || !attachments.length) return '';
        const wrapperClass = isReply ? 'reply-attachments mt-2' : 'comment-attachments mt-3';
        return `
            <div class="${wrapperClass}">
                <div class="row g-2 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5">
                    ${attachments.map(a => `
                        <div class="col">
                          <div class="comment-image-wrapper position-relative">
                            <a href="${a.url}" class="d-block" data-fancybox="comment-${commentId}-images" data-caption="${a.file_name}">
                              <img src="${a.url}" alt="${a.file_name}" class="img-fluid rounded">
                            </a>
                            ${canEdit ? `
                              <button type="button"
                                  class="btn btn-danger btn-sm delete-image-btn position-absolute"
                                  data-image-id="${a.id}"
                                  data-comment-id="${commentId}"
                                  style="top:5px;right:5px;">
                                <i class="fas fa-trash"></i>
                              </button>` : ''}
                          </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    function renderComment(comment) {
        const isReply = !!comment.parent_id;
        const canEdit = isAuth && Number(comment.user.id) === Number(CFG.userId);
        const avatar = comment.user.avatar_url || CFG.userAvatar || '/images/placeholders/avatar.png';
        const timeAgo = formatTimeAgo(comment.created_at);
        const attach = attachmentsHTML(comment.attachments, comment.id, canEdit, isReply);

        return `
        <div class="comment_item mb-3" id="comment-${comment.id}">
          <div class="d-flex">
            <div class="comment_item_avatar">
              <img src="${avatar}" alt="${comment.user.name}" class="rounded-circle me-2"
                   width="${isReply ? 30 : 40}" height="${isReply ? 30 : 40}">
            </div>
            <div class="comment_item_body ${isReply ? 'sub' : ''}">
              <div class="comment_item_user">
                <a href="${userProfileUrl(comment.user)}" class="fw-bold text-decoration-none">
                  ${comment.user.name}
                </a>
                <div class="text-muted small">
                  <span>${comment.user.comments_count || 0} ${t('reply','replies')}</span> ·
                  <span>Joined ${new Date(comment.user.created_at).toLocaleDateString('vi-VN', {month:'short', year:'numeric'})}</span>
                </div>
              </div>
              <div class="comment_item_content" id="comment-content-${comment.id}">
                ${comment.content}
              </div>
              ${attach}
              <div class="comment_item_meta d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <span class="btn btn-sm btn_meta text-muted">
                    <i class="fa-regular fa-clock me-1"></i>${timeAgo}
                  </span>
                  ${isAuth ? `
                    <button type="button"
                        class="btn text-muted btn-sm no-border btn_meta comment-like-btn ${comment.is_liked ? 'active':''}"
                        data-comment-id="${comment.id}"
                        data-liked="${comment.is_liked ? 'true':'false'}"
                        title="${comment.is_liked ? t('unlike'):t('like')}">
                      <i class="fas fa-thumbs-up me-1"></i>
                      <span class="comment-like-count-${comment.id} me-1">${comment.like_count || 0}</span>
                      <span class="text">${t('like')}</span>
                    </button>` : `
                    <button type="button"
                        class="btn text-muted btn-sm no-border btn_meta comment-like-btn"
                        onclick="window.__threadLogin()"
                        title="${t('login_to_like')}">
                      <i class="fas fa-thumbs-up me-1"></i>
                      <span class="comment-like-count-${comment.id} me-1">${comment.like_count || 0}</span>
                      <span class="text">${t('like')}</span>
                    </button>`}
                </div>
                <div class="d-flex">
                  <button class="btn btn-sm text-muted no-border btn_meta quote-button"
                      data-comment-id="${comment.id}" data-user-name="${comment.user.name}">
                    <i class="fa-solid fa-quote-left me-1"></i><span class="text">${t('quote')}</span>
                  </button>
                  <button class="btn text-muted btn-sm no-border btn_meta reply-button ms-2"
                      data-parent-id="${comment.id}">
                    <i class="fas a-reply me-1"></i><span class="text">${t('reply')}</span>
                  </button>
                  ${canEdit ? `
                    <button class="btn text-warning btn-sm no-border btn_meta inline-edit-comment-btn"
                        data-comment-id="${comment.id}" title="${t('edit')}">
                      <i class="fas fa-edit me-1"></i><span class="text">${t('edit')}</span>
                    </button>
                    <button type="button"
                        class="btn text-danger btn-sm no-border btn_meta delete-comment-btn"
                        data-comment-id="${comment.id}"
                        data-comment-type="${isReply ? 'reply':'comment'}"
                        title="${t('delete')}">
                      <i class="fas fa-trash me-1"></i><span class="text">${t('delete')}</span>
                    </button>` : ''}
                </div>
              </div>
              ${!isReply ? '<div class="comment_sub"></div>' : ''}
            </div>
          </div>
        </div>`;
    }

    /* ================= Thread Actions ================= */
    function handleThreadLike(button) {
        const slug = button.dataset.threadSlug;
        const isLiked = button.dataset.liked === 'true';
        button.disabled = true;

        fetch(`/threads/${slug}/like`, {
            method:'POST',
            headers:setCSRF({
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            })
        })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || t('error'));
            const newLiked = !isLiked;
            button.dataset.liked = newLiked ? 'true':'false';
            button.classList.toggle('active', newLiked);
            const countEl = button.querySelector('.like-count');
            if (countEl) countEl.textContent = data.like_count;
            button.title = newLiked ? t('unlike') : t('like');
            showToast(data.message,'success');
        })
        .catch(e => showToast(e.message || t('request_error'),'error'))
        .finally(()=> button.disabled = false);
    }

    function handleThreadSave(button) {
        const slug = button.dataset.threadSlug;
        const isSaved = button.dataset.saved === 'true';
        button.disabled = true;
        const oldHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> '+t('processing');

        fetch(`/threads/${slug}/save`, {
            method:'POST',
            headers:setCSRF({
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            })
        })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || t('error'));
            const newSaved = !isSaved;
            button.dataset.saved = newSaved ? 'true':'false';

            if (newSaved) {
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-primary');
                button.innerHTML = `
                    <i class="fa-solid fa-bookmark me-1"></i>
                    <span class="save-text">${t('saved')}</span>
                    ${data.saves_count>0?`<span class="badge bg-danger text-dark ms-1 save-count">${data.saves_count}</span>`:''}
                `;
            } else {
                button.classList.remove('btn-primary');
                button.classList.add('btn-outline-primary');
                button.innerHTML = `
                    <i class="fa-regular fa-bookmark me-1"></i>
                    <span class="save-text">${t('save')}</span>
                    ${data.saves_count>0?`<span class="badge bg-danger text-dark ms-1 save-count">${data.saves_count}</span>`:''}
                `;
            }
            button.title = newSaved ? t('saved') : t('unsave');
            showToast(data.message,'success');
        })
        .catch(e => {
            showToast(e.message || t('request_error'),'error');
            button.innerHTML = oldHtml;
        })
        .finally(()=> button.disabled = false);
    }

    function handleThreadFollow(button) {
        const slug = button.dataset.threadSlug;
        const isFollowing = button.dataset.following === 'true';
        const method = isFollowing ? 'DELETE':'POST';
        const threadId = button.dataset.threadId;

        button.disabled = true;
        const old = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>'+t('processing');

        fetch(`/ajax/threads/${slug}/follow`, {
            method,
            headers:setCSRF({
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            })
        })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || t('error'));
            button.dataset.following = data.is_following ? 'true':'false';
            const count = data.follower_count ?? 0;

            if (data.is_following) {
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
                button.innerHTML =
                    `<i class="fas fa-bell me-1"></i><span class="follow-text">${t('saved','Following')}</span>` +
                    `<span class="follower-count badge bg-light text-dark ms-1">${count}</span>`;
                button.title = t('unsave','Unfollow');
            } else {
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
                button.innerHTML =
                    `<i class="fas fa-bell-slash me-1"></i><span class="follow-text">${t('save','Follow')}</span>` +
                    `<span class="follower-count badge bg-light text-dark ms-1">${count}</span>`;
                button.title = t('save','Follow');
            }

            qsa(`[data-thread-id="${threadId}"] .follower-count`).forEach(el => el.textContent = count);
            showToast(data.message,'success');
        })
        .catch(e => {
            showToast(e.message || t('request_error'),'error');
            button.innerHTML = old;
        })
        .finally(()=> button.disabled = false);
    }

    /* ================= Comment Create (Main Form) ================= */
    function initMainReplyForm() {
        const form = qs('#reply-form-element');
        if (!form) return;

        form.addEventListener('submit', e => {
            e.preventDefault();
            if (!isAuth) return showLoginModalOrRedirect();

            const btn = qs('#submit-reply-btn', form);
            const textarea = qs('#content', form);
            const editor = window.tinymce ? tinymce.get('content') : null;
            let content = editor ? editor.getContent().trim() : (textarea.value||'').trim();

            const contentError = qs('#content-error');
            textarea.classList.remove('is-invalid');
            if (contentError) contentError.style.display = 'none';

            if (!content || content === '<p></p>' || content === '<p><br></p>') {
                textarea.classList.add('is-invalid');
                if (contentError) contentError.style.display='block';
                if (editor) editor.focus();
                return;
            }

            btn.disabled = true;
            const fd = new FormData(form);

            fetch(CFG.routes.commentStore, {
                method:'POST',
                body: fd,
                headers:setCSRF({'X-Requested-With':'XMLHttpRequest','Accept':'application/json'})
            })
            .then(r=>r.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || t('request_error'));
                showToast(data.message || t('reply_posted_successfully'),'success');
                if (editor) editor.setContent('');
                textarea.value='';
                const pid = qs('#parent_id');
                if (pid) pid.value='';

                if (data.comment) {
                    insertNewComment(data.comment);
                    applyCommentCountDelta(1);
                }
            })
            .catch(e => showToast(e.message || t('request_error'),'error'))
            .finally(()=> btn.disabled = false);
        });
    }

    function insertNewComment(comment) {
        const container = qs('#comments-container');
        if (!container) return;
        const html = renderComment(comment);
        const sort = new URLSearchParams(location.search).get('sort') || 'newest';
        const pagination = qs('#comments-pagination', container);

        if (sort === 'newest') {
            container.insertAdjacentHTML('afterbegin', html);
        } else if (pagination) {
            pagination.insertAdjacentHTML('beforebegin', html);
        } else {
            container.insertAdjacentHTML('beforeend', html);
        }
    }

    /* ================= Inline Reply ================= */
    function showInlineReplyForm(parentId) {
        qsa('.inline-reply-form').forEach(f => f.style.display='none');
        const form = document.getElementById(`inline-reply-${parentId}`);
        if (form) {
            form.style.display='block';
            setTimeout(() => {
                const edId = `inline-reply-content-${parentId}`;
                if (window.tinymce && tinymce.get(edId)) tinymce.get(edId).focus();
            },80);
        }
    }

    function submitInlineReply(form) {
        const parentId = form.getAttribute('data-comment-id');
        const btn = form.querySelector('.submit-inline-reply');
        const edId = `inline-reply-content-${parentId}`;
        const editor = window.tinymce ? tinymce.get(edId) : null;
        const content = editor ? editor.getContent().trim() : '';

        if (!content || content === '<p></p>' || content === '<p><br></p>') {
            showToast(t('reply_required'),'error');
            return;
        }

        btn.disabled = true;
        const fd = new FormData();
        fd.append('_token', CFG.csrf);
        fd.append('content', content);
        fd.append('parent_id', parentId);

        fetch(`/threads/${CFG.threadId}/comments`, {
            method:'POST',
            body: fd,
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || t('request_error'));
            if (editor) editor.setContent('');
            form.style.display='none';
            if (data.comment) {
                addReplyToDOM(data.comment);
                applyCommentCountDelta(1);
            }
            showToast(t('reply_posted_successfully'),'success');
        })
        .catch(e => showToast(e.message || t('request_error'),'error'))
        .finally(()=> btn.disabled = false);
    }

    function addReplyToDOM(reply) {
        const parent = document.getElementById(`comment-${reply.parent_id}`);
        if (!parent) return;
        const sub = parent.querySelector('.comment_sub') || (() => {
            const div = document.createElement('div');
            div.className='comment_sub';
            parent.appendChild(div);
            return div;
        })();
        sub.insertAdjacentHTML('beforeend', renderComment(reply));
        setTimeout(() => {
            const el = document.getElementById(`comment-${reply.id}`);
            if (el) el.scrollIntoView({behavior:'smooth', block:'center'});
        },120);
    }

    /* ================= Comment Like ================= */
    function toggleCommentLike(button) {
        const id = button.dataset.commentId;
        const isLiked = button.dataset.liked === 'true';
        button.disabled = true;

        fetch(buildUrl(CFG.routes.commentLike, {id}), {
            method:'POST',
            headers:setCSRF({
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            })
        })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || t('request_error'));
            const newLiked = !isLiked;
            button.dataset.liked = newLiked ? 'true':'false';
            button.classList.toggle('active', newLiked);
            const countEl = button.querySelector(`.comment-like-count-${id}`);
            if (countEl) countEl.textContent = data.like_count;
            button.title = newLiked ? t('unlike'):t('like');
        })
        .catch(e => showToast(e.message || t('request_error'),'error'))
        .finally(()=> button.disabled = false);
    }

    /* ================= Delete Comment / Image ================= */
    function confirmDeleteComment(btn) {
        const id = btn.dataset.commentId;
        const type = btn.dataset.commentType;
        const msg = type === 'reply' ? t('delete_reply_confirm') : t('delete_comment_confirm');
        if (window.showDeleteConfirm) {
            window.showDeleteConfirm(msg).then(res => res.isConfirmed && executeDeleteComment(id, btn));
        } else if (confirm(msg)) {
            executeDeleteComment(id, btn);
        }
    }

    function executeDeleteComment(id, btn) {
        btn.disabled = true;
        const old = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(buildUrl(CFG.routes.commentDelete, {id}), {
            method:'DELETE',
            headers:setCSRF({
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            })
        })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || t('request_error'));
            const el = document.getElementById(`comment-${id}`);
            if (el) {
                el.style.opacity='0';
                setTimeout(()=> el.remove(),300);
            }
            applyCommentCountDelta(-1);
            showToast(t('comment_deleted'),'success');
        })
        .catch(e => {
            showToast(e.message || t('request_error'),'error');
            btn.innerHTML = old;
            btn.disabled = false;
        });
    }

    function confirmDeleteImage(btn) {
        const imageId = btn.dataset.imageId;
        const commentId = btn.dataset.commentId;
        const msg = t('delete_image_confirm');
        if (window.showDeleteConfirm) {
            window.showDeleteConfirm(msg).then(res => res.isConfirmed && executeDeleteImage(commentId, imageId, btn));
        } else if (confirm(msg)) {
            executeDeleteImage(commentId, imageId, btn);
        }
    }

    function executeDeleteImage(commentId, imageId, btn) {
        const wrapper = btn.closest('.comment-image-wrapper');
        btn.disabled = true;
        const old = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const url = CFG.routes.commentImageDelete
            .replace('{comment_id}', commentId)
            .replace('{image_id}', imageId);

        fetch(url, {
            method:'DELETE',
            headers:setCSRF({'Content-Type':'application/json','Accept':'application/json'})
        })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || t('request_error'));
            if (wrapper) {
                wrapper.style.opacity='0';
                setTimeout(() => {
                    const col = wrapper.closest('.col');
                    if (col) col.remove();
                },200);
            }
        })
        .catch(e => {
            showToast(e.message || t('request_error'),'error');
            btn.innerHTML = old;
            btn.disabled = false;
        });
    }

    /* ================= Edit Comment (Hidden Form + New Image Upload) ================= */
    function showEditForm(commentId) {
        const contentEl   = document.getElementById(`comment-content-${commentId}`);
        const formWrapper = document.getElementById(`edit-form-${commentId}`);
        if (!contentEl || !formWrapper) return;
        if (formWrapper.style.display === 'block') return;

        contentEl.style.display = 'none';
        formWrapper.style.display = 'block';

        const textarea = formWrapper.querySelector(`#edit-content-${commentId}`);
        if (!textarea) {
            console.warn('Edit textarea not found for comment', commentId);
            return;
        }

        // Khởi tạo TinyMCE nếu chưa tồn tại
        if (window.tinymce && !tinymce.get(`edit-content-${commentId}`)) {
            tinymce.init({
                selector: `#edit-content-${commentId}`,
                height: 200,
                menubar: false,
                branding: false,
                plugins: 'link lists code emoticons image table',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link emoticons | code',
                setup: ed => ed.on('init', () => ed.focus())
            });
        }

        // Gắn submit 1 lần
        const form = formWrapper.querySelector('form.comment-edit-form');
        if (form && !form.dataset.boundSubmit) {
            form.addEventListener('submit', e => {
                e.preventDefault();
                submitEditComment(form, commentId);
            });
            form.dataset.boundSubmit = '1';
        }

        // Gắn cancel
        const cancelBtn = formWrapper.querySelector('.cancel-edit-btn');
        if (cancelBtn && !cancelBtn.dataset.boundCancel) {
            cancelBtn.addEventListener('click', () => hideEditForm(commentId));
            cancelBtn.dataset.boundCancel = '1';
        }
    }

    function hideEditForm(commentId) {
        const contentEl   = document.getElementById(`comment-content-${commentId}`);
        const formWrapper = document.getElementById(`edit-form-${commentId}`);
        if (!contentEl || !formWrapper) return;
        formWrapper.style.display = 'none';
        contentEl.style.display = 'block';

        if (window.tinymce) {
            const ed = tinymce.get(`edit-content-${commentId}`);
            if (ed) ed.remove();
        }
    }

    async function submitEditComment(form, commentId) {
        const submitBtn = form.querySelector('.save-comment-btn');
        const editorId  = `edit-content-${commentId}`;
        const editor    = window.tinymce ? tinymce.get(editorId) : null;
        const newContent = editor ? editor.getContent().trim() : (form.querySelector(`#${editorId}`)?.value || '').trim();

        if (!newContent) {
            showToast(t('reply_required','Content required'),'error');
            return;
        }

        submitBtn.disabled = true;
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>'+t('processing');

        const fd = new FormData();
        fd.append('_method','PUT');
        fd.append('_token', CFG.csrf);
        fd.append('content', newContent);

        /* --- Bổ sung upload ảnh mới trong lúc edit --- */

        // 1. Thu thập file inputs (giả định component dùng input[name="images[]"])
        const fileInputs = form.querySelectorAll('input[type="file"]');
        const ALLOWED = ['image/png','image/jpeg','image/jpg','image/gif','image/webp','image/svg+xml'];
        const MAX_SIZE_MB = 5;

        fileInputs.forEach(input => {
            if (!input.files) return;
            Array.from(input.files).forEach(file => {
                if (!ALLOWED.includes(file.type)) {
                    showToast('File không hợp lệ: ' + file.name, 'error');
                    return;
                }
                if (file.size > MAX_SIZE_MB * 1024 * 1024) {
                    showToast(`Ảnh ${file.name} vượt quá ${MAX_SIZE_MB}MB`, 'error');
                    return;
                }
                fd.append('images[]', file);
            });
        });

        // 2. Nếu component đã upload tạm & tạo hidden inputs uploaded_images[]
        const tempUploaded = form.querySelectorAll('input[name="uploaded_images[]"]');
        tempUploaded.forEach(h => fd.append('uploaded_images[]', h.value));

        // 3. Nếu component của bạn có JS API (vd: commentImageUpload.uploadFiles()), có thể gọi trước
        //    và append các URL nhận được vào fd (ví dụ).
        // const comp = form.querySelector('.comment-image-upload');
        // if (comp && comp.commentImageUpload && comp.commentImageUpload.hasFiles()) {
        //     const uploaded = await comp.commentImageUpload.uploadFiles();
        //     uploaded.forEach(u => fd.append('uploaded_images[]', u.url));
        // }

        try {
            const res  = await fetch(`/api/comments/${commentId}`, {
                method:'POST',
                body: fd,
                headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message || t('request_error'));

            // Cập nhật nội dung
            const contentEl = document.getElementById(`comment-content-${commentId}`);
            if (contentEl) contentEl.innerHTML = data.comment.content;

            // Cập nhật attachments
            if (data.comment.attachments) {
                const commentBody = contentEl.parentNode;
                const oldAttach = commentBody.querySelector('.comment-attachments, .reply-attachments');
                if (oldAttach) oldAttach.remove();

                if (data.comment.attachments.length > 0) {
                    const isReply = !!data.comment.parent_id;
                    const wrapperClass = isReply ? 'reply-attachments mt-2' : 'comment-attachments mt-3';
                    const html = `
                        <div class="${wrapperClass}">
                          <div class="row g-2 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5">
                            ${data.comment.attachments.map(a => `
                              <div class="col">
                                <div class="comment-image-wrapper position-relative">
                                  <a href="${a.url}" class="d-block" data-fancybox="comment-${commentId}-images" data-caption="${a.file_name}">
                                    <img src="${a.url}" alt="${a.file_name}" class="img-fluid rounded">
                                  </a>
                                  <button type="button"
                                      class="btn btn-danger btn-sm delete-image-btn position-absolute"
                                      data-image-id="${a.id}"
                                      data-comment-id="${commentId}"
                                      style="top:5px;right:5px;">
                                    <i class="fas fa-trash"></i>
                                  </button>
                                </div>
                              </div>
                            `).join('')}
                          </div>
                        </div>`;
                    contentEl.insertAdjacentHTML('afterend', html);
                }
            }

            showToast(t('comment_updated','Updated'),'success');
            hideEditForm(commentId);

            // Reset file inputs (nếu muốn)
            fileInputs.forEach(inp => { inp.value=''; });

        } catch (err) {
            showToast(err.message || t('request_error'),'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    }

    /* ================= Quote ================= */
    function quoteComment(commentId, userName) {
        const original = document.getElementById(`comment-content-${commentId}`);
        const html = original ? original.innerHTML : '';
        const block = `
            <blockquote>
              <p><strong>${userName}:</strong></p>
              ${html}
            </blockquote><p></p>`;
        if (window.tinymce && tinymce.get('content')) {
            tinymce.get('content').insertContent(block);
            const replyForm = document.getElementById('reply-form');
            if (replyForm) replyForm.scrollIntoView({behavior:'smooth'});
            setTimeout(()=> tinymce.get('content').focus(),80);
        }
    }

    /* ================= Sort Comments ================= */
    function handleSort(btn) {
        const sortType = btn.dataset.sort;
        const container = qs('#comments-container');
        if (!container) return;

        qsa('.sort-btn').forEach(b => {
            b.classList.toggle('btn-primary', b === btn);
            b.classList.toggle('btn-outline-primary', b !== btn);
        });

        container.innerHTML = `
            <div class="text-center py-4">
              <i class="fas fa-spinner fa-spin fa-2x"></i><br>${t('processing','Loading...')}
            </div>`;

        const url = new URL(location.href);
        url.searchParams.set('sort', sortType);

        fetch(url.toString(), {
            method:'GET',
            headers:{'X-Requested-With':'XMLHttpRequest','Accept':'text/html'}
        })
        .then(r => {
            if (!r.ok) throw new Error('Server error');
            return r.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html,'text/html');
            const newBlock = doc.getElementById('comments-container');
            if (newBlock) {
                container.innerHTML = newBlock.innerHTML;
                history.pushState({},'',url.toString());
                showToast('Comments sorted','success');
            }
        })
        .catch(e => {
            console.error(e);
            showToast(t('request_error'),'error');
            location.href = url.toString(); // fallback
        });
    }

    /* ================= Real-time ================= */
    function initRealtime() {
        if (!window.notificationService || !window.notificationService.socket) return;
        const socket = window.notificationService.socket;
        socket.emit('subscribe_request', {channel:`thread.${CFG.threadId}`});

        socket.on('comment.created', d => {
            const c = d.comment;
            if (Number(c.user.id) === Number(CFG.userId)) return;
            if (c.parent_id) {
                addReplyToDOM(c);
            } else {
                insertNewComment(c);
                applyCommentCountDelta(1);
            }
            showToast(`${c.user.name} bình luận mới`,'success');
        });

        socket.on('comment.updated', d => {
            const c = d.comment;
            const el = document.getElementById(`comment-content-${c.id}`);
            if (el) el.innerHTML = c.content;
        });

        socket.on('comment.deleted', d => {
            const el = document.getElementById(`comment-${d.comment_id}`);
            if (el) {
                el.style.opacity='0';
                setTimeout(()=> el.remove(),300);
                applyCommentCountDelta(-1);
            }
        });

        socket.on('thread.like.updated', data => {
            if (Number(data.user_id) === Number(CFG.userId)) return;
            qsa('.like-count').forEach(el => el.textContent = data.like_count);
        });

        socket.on('comment.like.updated', data => {
            if (Number(data.user_id) === Number(CFG.userId)) return;
            const countEl = qs(`#comment-${data.comment_id} .comment-like-count-${data.comment_id}`);
            if (countEl) countEl.textContent = data.like_count;
        });

        socket.on('thread.stats.updated', data => {
            const stats = data.stats;
            if (stats.comments_count !== undefined) {
                qsa('[data-type="replies"]').forEach(el => {
                    const icon = el.querySelector('i');
                    el.innerHTML = `${icon?icon.outerHTML:'<i class="fas fa-comment"></i>'} ${stats.comments_count.toLocaleString()} ${t('reply','replies')}`;
                });
                const header = qs('[data-type="replies-header"]');
                if (header) {
                    const icon = header.querySelector('i');
                    header.innerHTML = `${icon?icon.outerHTML:''}${stats.comments_count.toLocaleString()} ${t('reply','replies')}`;
                }
            }
            if (stats.participants_count !== undefined) {
                qsa('[data-type="participants"]').forEach(el => {
                    const icon = el.querySelector('i');
                    el.innerHTML = `${icon?icon.outerHTML:'<i class="fas fa-users"></i>'} ${stats.participants_count.toLocaleString()} ${t('participants','Participants')}`;
                });
            }
        });
    }

    /* ================= Language Change ================= */
    document.addEventListener('languageChanged', () => {
        qsa('.btn-like').forEach(b => {
            b.title = b.dataset.liked === 'true' ? t('unlike') : t('like');
        });
        qsa('.btn-save').forEach(b => {
            const saved = b.dataset.saved === 'true';
            const span = b.querySelector('.save-text');
            if (span) span.textContent = saved ? t('saved'):t('save');
            b.title = saved ? t('saved'):t('unsave');
        });
        qsa('.comment-like-btn').forEach(b => {
            b.title = b.dataset.liked === 'true' ? t('unlike') : t('like');
        });
    });

    /* ================= Global Event Delegation ================= */
    function bindGlobalEvents() {
        document.addEventListener('click', e => {
            const likeThreadBtn = e.target.closest('.btn-like');
            if (likeThreadBtn && likeThreadBtn.dataset.threadSlug) {
                if (!isAuth && !likeThreadBtn.hasAttribute('onclick')) return showLoginModalOrRedirect();
                handleThreadLike(likeThreadBtn); return;
            }

            const saveBtn = e.target.closest('.btn-save');
            if (saveBtn && saveBtn.dataset.threadSlug) {
                if (!isAuth && !saveBtn.hasAttribute('onclick')) return showLoginModalOrRedirect();
                handleThreadSave(saveBtn); return;
            }

            const followBtn = e.target.closest('.thread-follow-btn');
            if (followBtn) {
                if (!isAuth) return showLoginModalOrRedirect();
                handleThreadFollow(followBtn); return;
            }

            const sortBtn = e.target.closest('.sort-btn');
            if (sortBtn) { handleSort(sortBtn); return; }

            const cLike = e.target.closest('.comment-like-btn');
            if (cLike && cLike.dataset.commentId) {
                if (!isAuth && !cLike.hasAttribute('onclick')) return showLoginModalOrRedirect();
                toggleCommentLike(cLike); return;
            }

            const replyBtn = e.target.closest('.reply-button');
            if (replyBtn) {
                if (!isAuth) return showLoginModalOrRedirect();
                showInlineReplyForm(replyBtn.dataset.parentId); return;
            }

            const quoteBtn = e.target.closest('.quote-button');
            if (quoteBtn) {
                if (!isAuth) return showLoginModalOrRedirect();
                quoteComment(quoteBtn.dataset.commentId, quoteBtn.dataset.userName); return;
            }

            const delCommentBtn = e.target.closest('.delete-comment-btn');
            if (delCommentBtn) {
                if (!isAuth) return showLoginModalOrRedirect();
                confirmDeleteComment(delCommentBtn); return;
            }

            const delImageBtn = e.target.closest('.delete-image-btn');
            if (delImageBtn) {
                if (!isAuth) return showLoginModalOrRedirect();
                confirmDeleteImage(delImageBtn); return;
            }

            const editBtn = e.target.closest('.inline-edit-comment-btn');
            if (editBtn) {
                if (!isAuth) return showLoginModalOrRedirect();
                showEditForm(editBtn.dataset.commentId); return;
            }

            const cancelInlineReply = e.target.closest('.cancel-inline-reply');
            if (cancelInlineReply) {
                const id = cancelInlineReply.dataset.commentId;
                const f = document.getElementById(`inline-reply-${id}`);
                if (f) f.style.display='none';
            }
        });

        document.addEventListener('submit', e => {
            const inlineForm = e.target.closest('.inline-reply-form-element');
            if (inlineForm) {
                e.preventDefault();
                if (!isAuth) return showLoginModalOrRedirect();
                submitInlineReply(inlineForm);
            }
        });
    }

    /* ================= Scroll to Comment if Needed ================= */
    function maybeScrollTo() {
        if (!CFG.scrollToCommentId) return;
        const el = document.getElementById(`comment-${CFG.scrollToCommentId}`);
        if (el) {
            setTimeout(() => {
                el.scrollIntoView({behavior:'smooth', block:'center'});
                el.classList.add('highlight-comment');
                setTimeout(()=> el.classList.remove('highlight-comment'),3000);
            },400);
        }
    }

    /* ================= Init ================= */
    document.addEventListener('DOMContentLoaded', () => {
        initMainReplyForm();
        bindGlobalEvents();
        initRealtime();
        maybeScrollTo();
    });

})();
