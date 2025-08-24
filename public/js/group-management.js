/**
 * MechaMap Group Management JavaScript
 * Handles group conversation features: member management, settings, invitations
 */

class GroupManagement {
    constructor() {
        this.memberCounts = new Map(); // Track member counts for groups
        this.init();
    }

    // Helper methods for DOM manipulation
    $(selector) {
        if (typeof selector === 'string') {
            return document.querySelector(selector);
        }
        return selector;
    }

    $$(selector) {
        return document.querySelectorAll(selector);
    }

    on(element, event, handler) {
        if (typeof element === 'string') {
            document.addEventListener(event, (e) => {
                if (e.target.matches(element) || e.target.closest(element)) {
                    handler.call(e.target.closest(element) || e.target, e);
                }
            });
        } else {
            element.addEventListener(event, handler);
        }
    }

    init() {
        this.bindEvents();
        this.initializeModals();
        this.initializeSearch();
        this.setupWebSocketCallbacks();
        console.log('✅ Group Management initialized successfully');
    }

    bindEvents() {
        // Member management buttons
        this.on('.btn-manage-members', 'click', this.openMemberModal.bind(this));
        this.on('.btn-group-settings', 'click', this.openSettingsModal.bind(this));
        this.on('.btn-invite-member', 'click', this.openInviteModal.bind(this));

        // Member actions
        this.on('.btn-remove-member', 'click', this.removeMember.bind(this));
        this.on('.btn-change-role', 'click', this.changeMemberRole.bind(this));
        // this.on('.btn-transfer-ownership', 'click', this.transferOwnership.bind(this)); // TODO: Implement transferOwnership method

        // Search functionality
        this.on('#member-search', 'input', this.searchMembers.bind(this));
        this.on('#user-search', 'input', this.searchUsers.bind(this));

        // Form submissions
        this.on('#group-settings-form', 'submit', this.updateGroupSettings.bind(this));
        // this.on('#invite-member-form', 'submit', this.inviteMember.bind(this)); // TODO: Implement inviteMember method
    }

    initializeModals() {
        // Initialize Bootstrap modals
        const memberModalEl = this.$('#memberManagementModal');
        const settingsModalEl = this.$('#groupSettingsModal');
        const inviteModalEl = this.$('#inviteMemberModal');

        if (memberModalEl) this.memberModal = new bootstrap.Modal(memberModalEl);
        if (settingsModalEl) this.settingsModal = new bootstrap.Modal(settingsModalEl);
        if (inviteModalEl) this.inviteModal = new bootstrap.Modal(inviteModalEl);
    }

    initializeSearch() {
        // Initialize search with debounce
        this.searchTimeout = null;
        this.searchDelay = 300; // ms
    }

    /**
     * Open member management modal
     */
    openMemberModal(e) {
        e.preventDefault();
        const conversationId = e.target.dataset.conversationId;

        if (!conversationId) {
            this.showError('Không tìm thấy ID cuộc trò chuyện');
            return;
        }

        this.loadMembers(conversationId);
        if (this.memberModal) this.memberModal.show();
    }

    /**
     * Load members for a conversation
     */
    async loadMembers(conversationId) {
        try {
            this.showLoading('#member-list');

            const response = await fetch(`/dashboard/messages/groups/${conversationId}/members`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.renderMembers(data.members);
                $('#current-conversation-id').val(conversationId);
            } else {
                throw new Error(data.message || 'Không thể tải danh sách thành viên');
            }
        } catch (error) {
            console.error('Error loading members:', error);
            this.showError('Không thể tải danh sách thành viên: ' + error.message);
        }
    }

    /**
     * Render members list
     */
    renderMembers(members) {
        const memberList = $('#member-list');

        if (!members || members.length === 0) {
            memberList.html(`
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có thành viên nào</p>
                </div>
            `);
            return;
        }

        const memberHtml = members.map(member => `
            <div class="member-item border rounded p-3 mb-2" data-member-id="${member.id}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="${member.avatar || this.getDefaultAvatar(member.name)}"
                             alt="${member.name}"
                             class="rounded-circle me-3"
                             width="40" height="40">
                        <div>
                            <h6 class="mb-0">${member.name}</h6>
                            <small class="text-muted">${member.email}</small>
                            <div>
                                <span class="badge bg-${this.getRoleBadgeColor(member.role)} me-1">
                                    ${this.getRoleDisplayName(member.role)}
                                </span>
                                ${member.is_online ? '<span class="badge bg-success">Online</span>' : '<span class="badge bg-secondary">Offline</span>'}
                            </div>
                        </div>
                    </div>
                    <div class="member-actions">
                        ${this.renderMemberActions(member)}
                    </div>
                </div>
            </div>
        `).join('');

        memberList.html(memberHtml);
    }

    /**
     * Render member action buttons
     */
    renderMemberActions(member) {
        const currentUserId = window.MechaMapConfig?.user?.id;
        const isOwner = member.role === 'owner';
        const isCurrentUser = member.id == currentUserId;

        if (isCurrentUser) {
            return '<span class="text-muted small">Bạn</span>';
        }

        let actions = '';

        if (!isOwner) {
            actions += `
                <button class="btn btn-sm btn-outline-danger btn-remove-member me-1"
                        data-member-id="${member.id}"
                        title="Xóa thành viên">
                    <i class="fas fa-user-minus"></i>
                </button>
            `;
        }

        actions += `
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle btn-change-role"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-member-id="${member.id}"
                        title="Thay đổi vai trò">
                    <i class="fas fa-user-cog"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item role-option" href="#" data-role="member">Thành viên</a></li>
                    <li><a class="dropdown-item role-option" href="#" data-role="moderator">Quản trị viên</a></li>
                    ${!isOwner ? '<li><a class="dropdown-item role-option" href="#" data-role="owner">Chuyển quyền sở hữu</a></li>' : ''}
                </ul>
            </div>
        `;

        return actions;
    }

    /**
     * Search members
     */
    searchMembers(e) {
        const query = $(e.target).val().toLowerCase();

        $('.member-item').each(function() {
            const memberName = $(this).find('h6').text().toLowerCase();
            const memberEmail = $(this).find('small').text().toLowerCase();

            if (memberName.includes(query) || memberEmail.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    /**
     * Remove member from group
     */
    async removeMember(e) {
        e.preventDefault();
        const memberId = $(e.target).closest('.btn-remove-member').data('member-id');
        const conversationId = $('#current-conversation-id').val();

        const confirmed = await this.confirmAction(
            'Xóa thành viên',
            'Bạn có chắc chắn muốn xóa thành viên này khỏi nhóm?',
            'Xóa',
            'btn-danger'
        );

        if (!confirmed) return;

        try {
            const response = await fetch(`/dashboard/messages/groups/${conversationId}/members/${memberId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess('Đã xóa thành viên thành công');
                this.loadMembers(conversationId); // Reload members
            } else {
                throw new Error(data.message || 'Không thể xóa thành viên');
            }
        } catch (error) {
            console.error('Error removing member:', error);
            this.showError('Không thể xóa thành viên: ' + error.message);
        }
    }

    /**
     * Change member role
     */
    async changeMemberRole(e) {
        e.preventDefault();
        const memberId = $(e.target).closest('.btn-change-role').data('member-id');
        const newRole = $(e.target).data('role');
        const conversationId = $('#current-conversation-id').val();

        if (!newRole) return;

        const roleNames = {
            'member': 'Thành viên',
            'moderator': 'Quản trị viên',
            'owner': 'Chủ sở hữu'
        };

        const confirmed = await this.confirmAction(
            'Thay đổi vai trò',
            `Bạn có chắc chắn muốn thay đổi vai trò thành "${roleNames[newRole]}"?`,
            'Thay đổi',
            'btn-primary'
        );

        if (!confirmed) return;

        try {
            const response = await fetch(`/dashboard/messages/groups/${conversationId}/members/${memberId}/role`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ role: newRole })
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess('Đã thay đổi vai trò thành công');
                this.loadMembers(conversationId); // Reload members
            } else {
                throw new Error(data.message || 'Không thể thay đổi vai trò');
            }
        } catch (error) {
            console.error('Error changing member role:', error);
            this.showError('Không thể thay đổi vai trò: ' + error.message);
        }
    }

    /**
     * Helper methods
     */
    getDefaultAvatar(name) {
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=007bff&color=fff`;
    }

    getRoleBadgeColor(role) {
        const colors = {
            'owner': 'danger',
            'moderator': 'warning',
            'member': 'primary'
        };
        return colors[role] || 'secondary';
    }

    getRoleDisplayName(role) {
        const names = {
            'owner': 'Chủ sở hữu',
            'moderator': 'Quản trị viên',
            'member': 'Thành viên'
        };
        return names[role] || role;
    }

    showLoading(selector) {
        $(selector).html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2 text-muted">Đang tải...</p>
            </div>
        `);
    }

    showSuccess(message) {
        if (window.NotificationSystem) {
            window.NotificationSystem.success(message);
        } else {
            alert(message);
        }
    }

    showError(message) {
        if (window.NotificationSystem) {
            window.NotificationSystem.error(message);
        } else {
            alert(message);
        }
    }

    setupWebSocketCallbacks() {
        if (!window.GroupWebSocket) {
            console.log('GroupManagement: GroupWebSocket not available');
            return;
        }

        // Member join/leave events
        window.GroupWebSocket.addCallback('onMemberJoin', (data) => {
            this.handleMemberJoin(data);
        });

        window.GroupWebSocket.addCallback('onMemberLeave', (data) => {
            this.handleMemberLeave(data);
        });

        // Member count updates
        window.GroupWebSocket.addCallback('onMemberCountUpdate', (data) => {
            this.updateMemberCountDisplay(data.group_id, data.count);
        });

        // Group updates
        window.GroupWebSocket.addCallback('onGroupUpdate', (data) => {
            this.handleGroupUpdate(data);
        });

        console.log('✅ GroupManagement: WebSocket callbacks setup');
    }

    handleMemberJoin(data) {
        // Update member count
        this.memberCounts.set(data.group_id, (this.memberCounts.get(data.group_id) || 0) + 1);
        this.updateMemberCountDisplay(data.group_id, this.memberCounts.get(data.group_id));

        // Show notification
        this.showNotification(`${data.user_name || 'Thành viên mới'} đã tham gia nhóm`, 'success');
    }

    handleMemberLeave(data) {
        // Update member count
        const currentCount = this.memberCounts.get(data.group_id) || 0;
        const newCount = Math.max(0, currentCount - 1);
        this.memberCounts.set(data.group_id, newCount);
        this.updateMemberCountDisplay(data.group_id, newCount);

        // Show notification
        this.showNotification(`${data.user_name || 'Thành viên'} đã rời khỏi nhóm`, 'info');
    }

    updateMemberCountDisplay(groupId, count) {
        // Update member count in group cards
        const groupCards = document.querySelectorAll(`[data-group-id="${groupId}"]`);
        groupCards.forEach(card => {
            const memberCountElement = card.querySelector('.member-count');
            if (memberCountElement) {
                memberCountElement.textContent = count;
            }
        });

        // Update stats cards if on groups index page
        const totalGroupsElement = document.querySelector('.stats-card .total-groups');
        if (totalGroupsElement) {
            // Recalculate total members across all groups
            let totalMembers = 0;
            this.memberCounts.forEach(count => totalMembers += count);

            const totalMembersElement = document.querySelector('.stats-card .total-members');
            if (totalMembersElement) {
                totalMembersElement.textContent = totalMembers;
            }
        }
    }

    handleGroupUpdate(data) {
        // Refresh group data or update specific group info
        const groupCard = document.querySelector(`[data-group-id="${data.group_id}"]`);
        if (groupCard) {
            // Update group name if changed
            if (data.name) {
                const nameElement = groupCard.querySelector('.group-name');
                if (nameElement) {
                    nameElement.textContent = data.name;
                }
            }

            // Update group description if changed
            if (data.description) {
                const descElement = groupCard.querySelector('.group-description');
                if (descElement) {
                    descElement.textContent = data.description;
                }
            }
        }

        this.showNotification('Thông tin nhóm đã được cập nhật', 'info');
    }

    showNotification(message, type = 'info') {
        if (window.Swal) {
            window.Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            console.log(`GroupManagement Notification (${type}): ${message}`);
        }
    }

    async confirmAction(title, message, confirmText = 'Xác nhận', buttonClass = 'btn-primary') {
        return new Promise((resolve) => {
            if (window.Swal) {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Hủy',
                    customClass: {
                        confirmButton: `btn ${buttonClass}`,
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    resolve(result.isConfirmed);
                });
            } else {
                resolve(confirm(message));
            }
        });
    }

    /**
     * Open group settings modal
     */
    openSettingsModal(e) {
        e.preventDefault();
        const conversationId = $(e.target).data('conversation-id');

        if (!conversationId) {
            this.showError('Không tìm thấy ID cuộc trò chuyện');
            return;
        }

        this.loadGroupSettings(conversationId);
        this.settingsModal.show();
    }

    /**
     * Load group settings
     */
    async loadGroupSettings(conversationId) {
        try {
            this.showLoading('#group-settings-content');

            const response = await fetch(`/dashboard/messages/groups/${conversationId}/settings`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.renderGroupSettings(data.settings);
                $('#settings-conversation-id').val(conversationId);
            } else {
                throw new Error(data.message || 'Không thể tải cài đặt nhóm');
            }
        } catch (error) {
            console.error('Error loading group settings:', error);
            this.showError('Không thể tải cài đặt nhóm: ' + error.message);
        }
    }

    /**
     * Render group settings form
     */
    renderGroupSettings(settings) {
        const settingsHtml = `
            <form id="group-settings-form">
                <div class="mb-3">
                    <label for="group-name" class="form-label">Tên nhóm</label>
                    <input type="text" class="form-control" id="group-name" name="name"
                           value="${settings.name || ''}" required>
                </div>

                <div class="mb-3">
                    <label for="group-description" class="form-label">Mô tả nhóm</label>
                    <textarea class="form-control" id="group-description" name="description"
                              rows="3">${settings.description || ''}</textarea>
                </div>

                <div class="mb-3">
                    <label for="group-type" class="form-label">Loại nhóm</label>
                    <select class="form-select" id="group-type" name="type">
                        <option value="public" ${settings.type === 'public' ? 'selected' : ''}>Công khai</option>
                        <option value="private" ${settings.type === 'private' ? 'selected' : ''}>Riêng tư</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="allow-member-invite"
                               name="allow_member_invite" ${settings.allow_member_invite ? 'checked' : ''}>
                        <label class="form-check-label" for="allow-member-invite">
                            Cho phép thành viên mời người khác
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="require-approval"
                               name="require_approval" ${settings.require_approval ? 'checked' : ''}>
                        <label class="form-check-label" for="require-approval">
                            Yêu cầu phê duyệt khi tham gia
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        `;

        $('#group-settings-content').html(settingsHtml);
    }

    /**
     * Update group settings
     */
    async updateGroupSettings(e) {
        e.preventDefault();
        const conversationId = $('#settings-conversation-id').val();
        const formData = new FormData(e.target);

        try {
            const response = await fetch(`/dashboard/messages/groups/${conversationId}/settings`, {
                method: 'PUT',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess('Đã cập nhật cài đặt nhóm thành công');
                this.settingsModal.hide();
                // Reload page or update UI as needed
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Không thể cập nhật cài đặt nhóm');
            }
        } catch (error) {
            console.error('Error updating group settings:', error);
            this.showError('Không thể cập nhật cài đặt nhóm: ' + error.message);
        }
    }

    /**
     * Open invite member modal
     */
    openInviteModal(e) {
        e.preventDefault();
        const conversationId = $(e.target).data('conversation-id');

        if (!conversationId) {
            this.showError('Không tìm thấy ID cuộc trò chuyện');
            return;
        }

        $('#invite-conversation-id').val(conversationId);
        $('#user-search').val('');
        $('#user-search-results').html('');
        this.inviteModal.show();
    }

    /**
     * Search users for invitation
     */
    async searchUsers(e) {
        const query = $(e.target).val().trim();

        if (query.length < 2) {
            $('#user-search-results').html('');
            return;
        }

        // Clear previous timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }

        // Set new timeout
        this.searchTimeout = setTimeout(async () => {
            try {
                this.showLoading('#user-search-results');

                const response = await fetch(`/dashboard/messages/groups/search-users?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.renderUserSearchResults(data.users);
                } else {
                    throw new Error(data.message || 'Không thể tìm kiếm người dùng');
                }
            } catch (error) {
                console.error('Error searching users:', error);
                $('#user-search-results').html(`
                    <div class="alert alert-danger">
                        Không thể tìm kiếm người dùng: ${error.message}
                    </div>
                `);
            }
        }, this.searchDelay);
    }

    /**
     * Render user search results
     */
    renderUserSearchResults(users) {
        const resultsContainer = $('#user-search-results');

        if (!users || users.length === 0) {
            resultsContainer.html(`
                <div class="text-center py-3">
                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                    <p class="text-muted">Không tìm thấy người dùng nào</p>
                </div>
            `);
            return;
        }

        const userHtml = users.map(user => `
            <div class="user-search-item border rounded p-2 mb-2" data-user-id="${user.id}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="${user.avatar || this.getDefaultAvatar(user.name)}"
                             alt="${user.name}"
                             class="rounded-circle me-2"
                             width="32" height="32">
                        <div>
                            <h6 class="mb-0 small">${user.name}</h6>
                            <small class="text-muted">${user.email}</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary btn-invite-user"
                            data-user-id="${user.id}"
                            data-user-name="${user.name}">
                        <i class="fas fa-plus"></i> Mời
                    </button>
                </div>
            </div>
        `).join('');

        resultsContainer.html(userHtml);

        // Bind invite button events
        $('.btn-invite-user').on('click', this.inviteUser.bind(this));
    }

    /**
     * Invite user to group
     */
    async inviteUser(e) {
        e.preventDefault();
        const userId = $(e.target).data('user-id');
        const userName = $(e.target).data('user-name');
        const conversationId = $('#invite-conversation-id').val();

        try {
            $(e.target).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang mời...');

            const response = await fetch(`/dashboard/messages/groups/${conversationId}/members`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ user_id: userId })
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(`Đã mời ${userName} vào nhóm thành công`);
                $(e.target).removeClass('btn-primary').addClass('btn-success')
                          .html('<i class="fas fa-check"></i> Đã mời');
            } else {
                throw new Error(data.message || 'Không thể mời người dùng');
            }
        } catch (error) {
            console.error('Error inviting user:', error);
            this.showError('Không thể mời người dùng: ' + error.message);
            $(e.target).prop('disabled', false).html('<i class="fas fa-plus"></i> Mời');
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.GroupManagement === 'undefined') {
        window.GroupManagement = new GroupManagement();
    }
});
