/**
 * MechaMap Group Creation Wizard
 * Enhanced JavaScript for group creation with step-by-step wizard
 */

class GroupCreationWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.formData = {};
        this.selectedMembers = [];
        this.uploadedFiles = [];

        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeValidation();
        this.setupFileUpload();
        this.setupMemberSearch();
        this.updateStepIndicator();
    }

    /**
     * Add member to selected list
     */
    addMember(event) {
        event.preventDefault();
        const $btn = $(event.target);
        const userId = $btn.data('user-id');
        const userName = $btn.data('user-name');
        const userEmail = $btn.data('user-email');
        const userAvatar = $btn.data('user-avatar');

        // Check if already selected
        if (this.selectedMembers.find(member => member.id === userId)) {
            this.showNotification('Thành viên này đã được chọn', 'warning');
            return;
        }

        // Check limit
        if (this.selectedMembers.length >= 10) {
            this.showNotification('Chỉ có thể mời tối đa 10 thành viên ban đầu', 'warning');
            return;
        }

        // Add to selected list
        this.selectedMembers.push({
            id: userId,
            name: userName,
            email: userEmail,
            avatar: userAvatar
        });

        // Update UI
        this.updateSelectedMembersList();
        $btn.prop('disabled', true).text('Đã chọn');
    }

    /**
     * Remove member from selected list
     */
    removeMember(event) {
        event.preventDefault();
        const $btn = $(event.target);
        const userId = $btn.data('user-id');

        // Remove from selected list
        this.selectedMembers = this.selectedMembers.filter(member => member.id !== userId);

        // Update UI
        this.updateSelectedMembersList();

        // Re-enable add button
        $(`.add-member-btn[data-user-id="${userId}"]`).prop('disabled', false).text('+');
    }

    /**
     * Handle avatar upload
     */
    handleAvatarUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            this.showNotification('Vui lòng chọn file hình ảnh', 'error');
            return;
        }

        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            this.showNotification('Kích thước file không được vượt quá 2MB', 'error');
            return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = (e) => {
            $('.avatar-preview').attr('src', e.target.result).show();
            $('.avatar-placeholder').hide();
        };
        reader.readAsDataURL(file);

        // Store file for upload
        this.uploadedFiles.avatar = file;
    }

    /**
     * Remove uploaded file
     */
    removeFile(event) {
        event.preventDefault();
        const $btn = $(event.target);
        const fileType = $btn.data('file-type');

        if (fileType === 'avatar') {
            $('.avatar-preview').hide();
            $('.avatar-placeholder').show();
            $('#group-avatar-input').val('');
            delete this.uploadedFiles.avatar;
        }
    }

    /**
     * Create group
     */
    async createGroup(event) {
        event.preventDefault();

        if (!this.validateCurrentStep()) {
            this.showNotification('Vui lòng kiểm tra lại thông tin', 'error');
            return;
        }

        const $btn = $(event.target);
        const originalText = $btn.text();

        try {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo nhóm...');

            // Prepare form data
            const formData = new FormData();
            formData.append('name', this.formData.name);
            formData.append('description', this.formData.description);
            formData.append('category', this.formData.category);
            formData.append('privacy', this.formData.privacy);
            formData.append('join_approval', this.formData.join_approval);
            formData.append('additional_info', this.formData.additional_info || '');

            // Add selected members
            this.selectedMembers.forEach((member, index) => {
                formData.append(`members[${index}]`, member.id);
            });

            // Add avatar if uploaded
            if (this.uploadedFiles.avatar) {
                formData.append('avatar', this.uploadedFiles.avatar);
            }

            // Add CSRF token
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            // Submit form
            const response = await fetch('/dashboard/messages/groups', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Nhóm đã được tạo thành công!', 'success');
                this.clearDraft();

                // Redirect to group page
                setTimeout(() => {
                    window.location.href = result.redirect || '/dashboard/messages/groups';
                }, 1500);
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra khi tạo nhóm');
            }

        } catch (error) {
            console.error('Error creating group:', error);
            this.showNotification(error.message || 'Có lỗi xảy ra khi tạo nhóm', 'error');
        } finally {
            $btn.prop('disabled', false).text(originalText);
        }
    }

    bindEvents() {
        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            console.warn('jQuery not available for GroupCreationWizard');
            return;
        }

        // Step navigation
        $(document).on('click', '.btn-next-step', this.nextStep.bind(this));
        $(document).on('click', '.btn-prev-step', this.prevStep.bind(this));
        $(document).on('click', '.step-indicator', this.goToStep.bind(this));

        // Form validation
        $(document).on('input', '.wizard-input', this.validateCurrentStep.bind(this));
        $(document).on('change', '.wizard-select', this.validateCurrentStep.bind(this));

        // Member management
        $(document).on('click', '.add-member-btn', this.addMember.bind(this));
        $(document).on('click', '.remove-member-btn', this.removeMember.bind(this));

        // File management
        $(document).on('change', '#group-avatar-input', this.handleAvatarUpload.bind(this));
        $(document).on('click', '.remove-file-btn', this.removeFile.bind(this));

        // Final submission
        $(document).on('click', '.btn-create-group', this.createGroup.bind(this));

        // Auto-save draft
        setInterval(this.saveDraft.bind(this), 30000); // Save every 30 seconds
    }

    /**
     * Navigate to next step
     */
    nextStep() {
        if (this.validateCurrentStep()) {
            this.saveStepData();

            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.showStep(this.currentStep);
                this.updateStepIndicator();
                this.scrollToTop();
            }
        }
    }

    /**
     * Navigate to previous step
     */
    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
            this.updateStepIndicator();
            this.scrollToTop();
        }
    }

    /**
     * Go to specific step
     */
    goToStep(event) {
        const targetStep = parseInt($(event.target).data('step'));

        if (targetStep && targetStep <= this.totalSteps) {
            // Validate all previous steps
            let canProceed = true;
            for (let i = 1; i < targetStep; i++) {
                if (!this.validateStep(i)) {
                    canProceed = false;
                    break;
                }
            }

            if (canProceed) {
                this.currentStep = targetStep;
                this.showStep(this.currentStep);
                this.updateStepIndicator();
                this.scrollToTop();
            } else {
                this.showNotification('Vui lòng hoàn thành các bước trước đó', 'warning');
            }
        }
    }

    /**
     * Show specific step
     */
    showStep(step) {
        // Hide all steps
        $('.wizard-step').removeClass('active').hide();

        // Show target step
        $(`.wizard-step[data-step="${step}"]`).addClass('active').fadeIn(300);

        // Update navigation buttons
        this.updateNavigationButtons();

        // Load step-specific data
        this.loadStepData(step);
    }

    /**
     * Update step indicator
     */
    updateStepIndicator() {
        $('.step-indicator').each((index, element) => {
            const stepNumber = index + 1;
            const $indicator = $(element);

            $indicator.removeClass('active completed');

            if (stepNumber < this.currentStep) {
                $indicator.addClass('completed');
            } else if (stepNumber === this.currentStep) {
                $indicator.addClass('active');
            }
        });
    }

    /**
     * Update navigation buttons
     */
    updateNavigationButtons() {
        const $prevBtn = $('.btn-prev-step');
        const $nextBtn = $('.btn-next-step');
        const $createBtn = $('.btn-create-group');

        // Previous button
        if (this.currentStep === 1) {
            $prevBtn.hide();
        } else {
            $prevBtn.show();
        }

        // Next/Create button
        if (this.currentStep === this.totalSteps) {
            $nextBtn.hide();
            $createBtn.show();
        } else {
            $nextBtn.show();
            $createBtn.hide();
        }
    }

    /**
     * Validate current step
     */
    validateCurrentStep() {
        return this.validateStep(this.currentStep);
    }

    /**
     * Validate specific step
     */
    validateStep(step) {
        const $stepContainer = $(`.wizard-step[data-step="${step}"]`);
        let isValid = true;

        // Clear previous errors
        $stepContainer.find('.is-invalid').removeClass('is-invalid');
        $stepContainer.find('.invalid-feedback').remove();

        switch (step) {
            case 1: // Basic Information
                isValid = this.validateBasicInfo($stepContainer);
                break;
            case 2: // Group Settings
                isValid = this.validateGroupSettings($stepContainer);
                break;
            case 3: // Add Members
                isValid = this.validateMembers($stepContainer);
                break;
            case 4: // Review & Create
                isValid = this.validateReview($stepContainer);
                break;
        }

        return isValid;
    }

    /**
     * Validate basic information step
     */
    validateBasicInfo($container) {
        let isValid = true;

        // Group name validation
        const $groupName = $container.find('#group-name');
        if (!$groupName.val().trim()) {
            this.showFieldError($groupName, 'Tên nhóm là bắt buộc');
            isValid = false;
        } else if ($groupName.val().trim().length < 3) {
            this.showFieldError($groupName, 'Tên nhóm phải có ít nhất 3 ký tự');
            isValid = false;
        }

        // Group description validation
        const $groupDescription = $container.find('#group-description');
        if (!$groupDescription.val().trim()) {
            this.showFieldError($groupDescription, 'Mô tả nhóm là bắt buộc');
            isValid = false;
        } else if ($groupDescription.val().trim().length < 10) {
            this.showFieldError($groupDescription, 'Mô tả nhóm phải có ít nhất 10 ký tự');
            isValid = false;
        }

        // Category validation
        const $category = $container.find('#group-category');
        if (!$category.val()) {
            this.showFieldError($category, 'Vui lòng chọn danh mục');
            isValid = false;
        }

        return isValid;
    }

    /**
     * Validate group settings step
     */
    validateGroupSettings($container) {
        let isValid = true;

        // Privacy setting validation
        const $privacy = $container.find('input[name="privacy"]:checked');
        if (!$privacy.length) {
            this.showNotification('Vui lòng chọn cài đặt riêng tư', 'warning');
            isValid = false;
        }

        // Join approval validation
        const $joinApproval = $container.find('input[name="join_approval"]:checked');
        if (!$joinApproval.length) {
            this.showNotification('Vui lòng chọn cài đặt phê duyệt thành viên', 'warning');
            isValid = false;
        }

        return isValid;
    }

    /**
     * Validate members step
     */
    validateMembers($container) {
        // Members are optional, but if added, validate them
        if (this.selectedMembers.length > 50) {
            this.showNotification('Nhóm không thể có quá 50 thành viên', 'warning');
            return false;
        }

        return true;
    }

    /**
     * Validate review step
     */
    validateReview($container) {
        // Final validation of all data
        const hasRequiredData = this.formData.name &&
                               this.formData.description &&
                               this.formData.category;

        if (!hasRequiredData) {
            this.showNotification('Vui lòng hoàn thành tất cả thông tin bắt buộc', 'error');
            return false;
        }

        return true;
    }

    /**
     * Show field validation error
     */
    showFieldError($field, message) {
        $field.addClass('is-invalid');
        $field.after(`<div class="invalid-feedback">${message}</div>`);
    }

    /**
     * Save current step data
     */
    saveStepData() {
        const $currentStep = $(`.wizard-step[data-step="${this.currentStep}"]`);

        switch (this.currentStep) {
            case 1:
                this.formData.name = $currentStep.find('#group-name').val().trim();
                this.formData.description = $currentStep.find('#group-description').val().trim();
                this.formData.category = $currentStep.find('#group-category').val();
                break;
            case 2:
                this.formData.privacy = $currentStep.find('input[name="privacy"]:checked').val();
                this.formData.join_approval = $currentStep.find('input[name="join_approval"]:checked').val();
                this.formData.message_approval = $currentStep.find('input[name="message_approval"]:checked').val();
                this.formData.max_members = $currentStep.find('#max-members').val();
                break;
            case 3:
                this.formData.members = this.selectedMembers;
                break;
        }
    }

    /**
     * Load step data
     */
    loadStepData(step) {
        const $stepContainer = $(`.wizard-step[data-step="${step}"]`);

        switch (step) {
            case 4: // Review step
                this.renderReviewData($stepContainer);
                break;
        }
    }

    /**
     * Render review data
     */
    renderReviewData($container) {
        const reviewHtml = `
            <div class="review-section">
                <h5>Thông tin cơ bản</h5>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Tên nhóm:</strong></div>
                    <div class="col-sm-8">${this.formData.name || 'Chưa nhập'}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Mô tả:</strong></div>
                    <div class="col-sm-8">${this.formData.description || 'Chưa nhập'}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Danh mục:</strong></div>
                    <div class="col-sm-8">${this.getCategoryName(this.formData.category)}</div>
                </div>
            </div>

            <div class="review-section">
                <h5>Cài đặt nhóm</h5>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Quyền riêng tư:</strong></div>
                    <div class="col-sm-8">${this.getPrivacyName(this.formData.privacy)}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Phê duyệt thành viên:</strong></div>
                    <div class="col-sm-8">${this.formData.join_approval === 'required' ? 'Bắt buộc' : 'Không bắt buộc'}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Số thành viên tối đa:</strong></div>
                    <div class="col-sm-8">${this.formData.max_members || 'Không giới hạn'}</div>
                </div>
            </div>

            <div class="review-section">
                <h5>Thành viên (${this.selectedMembers.length})</h5>
                <div class="selected-members-review">
                    ${this.renderSelectedMembersReview()}
                </div>
            </div>
        `;

        $container.find('.review-content').html(reviewHtml);
    }

    /**
     * Scroll to top of wizard
     */
    scrollToTop() {
        $('.group-creation-wizard').get(0).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Implementation depends on your notification system
        if (window.showNotification) {
            window.showNotification(message, type);
        } else {
            alert(message);
        }
    }

    /**
     * Save draft to localStorage
     */
    saveDraft() {
        const draftData = {
            currentStep: this.currentStep,
            formData: this.formData,
            selectedMembers: this.selectedMembers,
            timestamp: Date.now()
        };

        localStorage.setItem('group_creation_draft', JSON.stringify(draftData));
    }

    /**
     * Load draft from localStorage
     */
    loadDraft() {
        const draftData = localStorage.getItem('group_creation_draft');

        if (draftData) {
            try {
                const parsed = JSON.parse(draftData);

                // Check if draft is not too old (24 hours)
                if (Date.now() - parsed.timestamp < 24 * 60 * 60 * 1000) {
                    this.currentStep = parsed.currentStep || 1;
                    this.formData = parsed.formData || {};
                    this.selectedMembers = parsed.selectedMembers || [];

                    this.showStep(this.currentStep);
                    this.populateFormData();

                    this.showNotification('Đã khôi phục bản nháp trước đó', 'success');
                }
            } catch (error) {
                console.error('Error loading draft:', error);
            }
        }
    }

    /**
     * Clear draft
     */
    clearDraft() {
        localStorage.removeItem('group_creation_draft');
    }

    /**
     * Update selected members list UI
     */
    updateSelectedMembersList() {
        const $container = $('.selected-members-list');
        const $count = $('.selected-count');

        $count.text(this.selectedMembers.length);

        if (this.selectedMembers.length === 0) {
            $container.html('<p class="text-muted">Chưa có thành viên nào được chọn</p>');
            return;
        }

        const membersHtml = this.selectedMembers.map(member => `
            <div class="selected-member-item d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                    <img src="${member.avatar}" alt="${member.name}" class="rounded-circle me-2" width="32" height="32">
                    <div>
                        <div class="fw-medium">${member.name}</div>
                        <small class="text-muted">${member.email}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-member-btn" data-user-id="${member.id}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');

        $container.html(membersHtml);
    }

    /**
     * Handle avatar upload
     */
    handleAvatarUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            this.showNotification('Vui lòng chọn file hình ảnh', 'error');
            return;
        }

        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            this.showNotification('Kích thước file không được vượt quá 2MB', 'error');
            return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = (e) => {
            $('.avatar-preview').attr('src', e.target.result).show();
            $('.avatar-placeholder').hide();
        };
        reader.readAsDataURL(file);

        // Store file for upload
        this.uploadedFiles.avatar = file;
    }
}

    /**
     * Remove uploaded file
     */
    removeFile(event) {
        event.preventDefault();
        const $btn = $(event.target);
        const fileType = $btn.data('file-type');

        if (fileType === 'avatar') {
            $('.avatar-preview').hide();
            $('.avatar-placeholder').show();
            $('#group-avatar-input').val('');
            delete this.uploadedFiles.avatar;
        }
    }

    /**
     * Create group
     */
    async createGroup(event) {
        event.preventDefault();

        if (!this.validateCurrentStep()) {
            this.showNotification('Vui lòng kiểm tra lại thông tin', 'error');
            return;
        }

        const $btn = $(event.target);
        const originalText = $btn.text();

        try {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo nhóm...');

            // Prepare form data
            const formData = new FormData();
            formData.append('name', this.formData.name);
            formData.append('description', this.formData.description);
            formData.append('category', this.formData.category);
            formData.append('privacy', this.formData.privacy);
            formData.append('join_approval', this.formData.join_approval);
            formData.append('additional_info', this.formData.additional_info || '');

            // Add selected members
            this.selectedMembers.forEach((member, index) => {
                formData.append(`members[${index}]`, member.id);
            });

            // Add avatar if uploaded
            if (this.uploadedFiles.avatar) {
                formData.append('avatar', this.uploadedFiles.avatar);
            }

            // Add CSRF token
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            // Submit form
            const response = await fetch('/dashboard/messages/groups', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Nhóm đã được tạo thành công!', 'success');
                this.clearDraft();

                // Redirect to group page
                setTimeout(() => {
                    window.location.href = result.redirect || '/dashboard/messages/groups';
                }, 1500);
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra khi tạo nhóm');
            }

        } catch (error) {
            console.error('Error creating group:', error);
            this.showNotification(error.message || 'Có lỗi xảy ra khi tạo nhóm', 'error');
        } finally {
            $btn.prop('disabled', false).text(originalText);
        }
    }

    /**
     * Initialize validation
     */
    initializeValidation() {
        // Character counters
        $(document).on('input', '#group-name', function() {
            const length = $(this).val().length;
            $(this).siblings('.char-counter').find('.current').text(length);
        });

        $(document).on('input', '#group-description', function() {
            const length = $(this).val().length;
            $(this).siblings('.char-counter').find('.current').text(length);
        });

        $(document).on('input', '#group-purpose', function() {
            const length = $(this).val().length;
            $(this).siblings('.char-counter').find('.current').text(length);
        });

        $(document).on('input', '#additional-info', function() {
            const length = $(this).val().length;
            $(this).siblings('.char-counter').find('.current').text(length);
        });
    }

    /**
     * Setup file upload
     */
    setupFileUpload() {
        // Avatar upload
        $(document).on('change', '#group-avatar', (event) => {
            this.handleAvatarUpload(event);
        });
    }

    /**
     * Setup member search
     */
    setupMemberSearch() {
        let searchTimeout;

        $(document).on('input', '#member-search', function() {
            const query = $(this).val().trim();

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (query.length >= 2) {
                    // Implement search functionality
                    // For now, just filter existing members
                    $('.member-item').each(function() {
                        const memberName = $(this).find('.member-name').text().toLowerCase();
                        const memberEmail = $(this).find('.member-email').text().toLowerCase();

                        if (memberName.includes(query.toLowerCase()) || memberEmail.includes(query.toLowerCase())) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                } else {
                    $('.member-item').show();
                }
            }, 300);
        });
    }

    /**
     * Populate form data from saved state
     */
    populateFormData() {
        if (this.formData.name) $('#group-name').val(this.formData.name);
        if (this.formData.description) $('#group-description').val(this.formData.description);
        if (this.formData.category) $('#group-category').val(this.formData.category);
        if (this.formData.privacy) $(`input[name="privacy"][value="${this.formData.privacy}"]`).prop('checked', true);
        if (this.formData.join_approval) $(`input[name="join_approval"][value="${this.formData.join_approval}"]`).prop('checked', true);
        if (this.formData.additional_info) $('#additional-info').val(this.formData.additional_info);

        // Update selected members
        this.updateSelectedMembersList();
    }
}

// Auto-initialize on group creation page
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.group-creation-wizard')) {
        window.groupCreationWizard = new GroupCreationWizard();
        console.log('✅ GroupCreationWizard initialized');
    }
});

// Export for manual initialization
window.GroupCreationWizard = GroupCreationWizard;
