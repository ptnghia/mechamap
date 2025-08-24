/**
 * Notification Archive Management
 * Handles archive view functionality, restore, and bulk operations
 */

class NotificationArchive {
    constructor() {
        this.baseUrl = '/dashboard/notifications';
        this.selectedNotifications = new Set();
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateBulkActionsVisibility();
        console.log('✅ Notification Archive initialized');
    }

    bindEvents() {
        // Select all checkbox
        document.getElementById('selectAll')?.addEventListener('change', (e) => {
            this.handleSelectAll(e.target.checked);
        });

        // Individual notification checkboxes
        document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.handleNotificationSelect(e.target);
            });
        });

        // Restore buttons
        document.querySelectorAll('.restore-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const notificationId = e.target.closest('.restore-btn').dataset.notificationId;
                this.restoreNotification(notificationId);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const notificationId = e.target.closest('.delete-btn').dataset.notificationId;
                this.deleteNotification(notificationId);
            });
        });

        // Bulk action buttons
        document.getElementById('bulkRestoreBtn')?.addEventListener('click', () => {
            this.bulkRestore();
        });

        document.getElementById('bulkDeleteBtn')?.addEventListener('click', () => {
            this.bulkDelete();
        });

        // Global action buttons
        document.getElementById('restoreAllBtn')?.addEventListener('click', () => {
            this.restoreAll();
        });

        document.getElementById('deleteAllArchivedBtn')?.addEventListener('click', () => {
            this.deleteAllArchived();
        });

        // Filter and search
        document.getElementById('applyFilters')?.addEventListener('click', () => {
            this.applyFilters();
        });

        document.getElementById('searchInput')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.applyFilters();
            }
        });
    }

    handleSelectAll(checked) {
        const checkboxes = document.querySelectorAll('.notification-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
            this.handleNotificationSelect(checkbox);
        });
    }

    handleNotificationSelect(checkbox) {
        const notificationId = checkbox.value;
        
        if (checkbox.checked) {
            this.selectedNotifications.add(notificationId);
        } else {
            this.selectedNotifications.delete(notificationId);
        }

        this.updateSelectedCount();
        this.updateBulkActionsVisibility();
        this.updateSelectAllState();
    }

    updateSelectedCount() {
        const countElement = document.getElementById('selectedCount');
        if (countElement) {
            const count = this.selectedNotifications.size;
            countElement.textContent = `${count} ${count === 1 ? 'đã chọn' : 'đã chọn'}`;
        }
    }

    updateBulkActionsVisibility() {
        const bulkActions = document.getElementById('bulkActions');
        if (bulkActions) {
            bulkActions.style.display = this.selectedNotifications.size > 0 ? 'block' : 'none';
        }
    }

    updateSelectAllState() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.notification-checkbox');
        
        if (selectAllCheckbox && checkboxes.length > 0) {
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            if (checkedCount === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCount === checkboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }
    }

    async restoreNotification(notificationId) {
        if (!confirm('Bạn có chắc muốn khôi phục thông báo này?')) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/${notificationId}/restore`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.removeNotificationFromUI(notificationId);
                this.showSuccess('Đã khôi phục thông báo thành công');
                this.updateStats();
            } else {
                this.showError(data.message || 'Có lỗi xảy ra khi khôi phục thông báo');
            }
        } catch (error) {
            console.error('Failed to restore notification:', error);
            this.showError('Không thể khôi phục thông báo');
        }
    }

    async deleteNotification(notificationId) {
        if (!confirm('Bạn có chắc muốn xóa vĩnh viễn thông báo này? Hành động này không thể hoàn tác.')) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.removeNotificationFromUI(notificationId);
                this.showSuccess('Đã xóa thông báo vĩnh viễn');
                this.updateStats();
            } else {
                this.showError(data.message || 'Có lỗi xảy ra khi xóa thông báo');
            }
        } catch (error) {
            console.error('Failed to delete notification:', error);
            this.showError('Không thể xóa thông báo');
        }
    }

    async bulkRestore() {
        if (this.selectedNotifications.size === 0) {
            this.showWarning('Vui lòng chọn ít nhất một thông báo');
            return;
        }

        if (!confirm(`Bạn có chắc muốn khôi phục ${this.selectedNotifications.size} thông báo đã chọn?`)) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: 'restore',
                    notification_ids: JSON.stringify(Array.from(this.selectedNotifications))
                })
            });

            const data = await response.json();

            if (data.success) {
                this.selectedNotifications.forEach(id => {
                    this.removeNotificationFromUI(id);
                });
                this.selectedNotifications.clear();
                this.updateBulkActionsVisibility();
                this.showSuccess(`Đã khôi phục ${data.count || 'các'} thông báo thành công`);
                this.updateStats();
            } else {
                this.showError(data.message || 'Có lỗi xảy ra khi khôi phục thông báo');
            }
        } catch (error) {
            console.error('Failed to bulk restore notifications:', error);
            this.showError('Không thể khôi phục thông báo');
        }
    }

    async bulkDelete() {
        if (this.selectedNotifications.size === 0) {
            this.showWarning('Vui lòng chọn ít nhất một thông báo');
            return;
        }

        if (!confirm(`Bạn có chắc muốn xóa vĩnh viễn ${this.selectedNotifications.size} thông báo đã chọn? Hành động này không thể hoàn tác.`)) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: 'delete',
                    notification_ids: JSON.stringify(Array.from(this.selectedNotifications))
                })
            });

            const data = await response.json();

            if (data.success) {
                this.selectedNotifications.forEach(id => {
                    this.removeNotificationFromUI(id);
                });
                this.selectedNotifications.clear();
                this.updateBulkActionsVisibility();
                this.showSuccess(`Đã xóa vĩnh viễn ${data.count || 'các'} thông báo`);
                this.updateStats();
            } else {
                this.showError(data.message || 'Có lỗi xảy ra khi xóa thông báo');
            }
        } catch (error) {
            console.error('Failed to bulk delete notifications:', error);
            this.showError('Không thể xóa thông báo');
        }
    }

    async restoreAll() {
        if (!confirm('Bạn có chắc muốn khôi phục tất cả thông báo đã lưu trữ?')) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/restore-all`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                this.showError(data.message || 'Có lỗi xảy ra khi khôi phục thông báo');
            }
        } catch (error) {
            console.error('Failed to restore all notifications:', error);
            this.showError('Không thể khôi phục tất cả thông báo');
        }
    }

    async deleteAllArchived() {
        if (!confirm('Bạn có chắc muốn xóa vĩnh viễn TẤT CẢ thông báo đã lưu trữ? Hành động này không thể hoàn tác.')) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/delete-all-archived`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                this.showError(data.message || 'Có lỗi xảy ra khi xóa thông báo');
            }
        } catch (error) {
            console.error('Failed to delete all archived notifications:', error);
            this.showError('Không thể xóa tất cả thông báo đã lưu trữ');
        }
    }

    applyFilters() {
        const params = new URLSearchParams();
        
        const category = document.getElementById('categoryFilter')?.value;
        const dateArchived = document.getElementById('dateArchivedFilter')?.value;
        const search = document.getElementById('searchInput')?.value;

        if (category) params.set('category', category);
        if (dateArchived) params.set('date_archived', dateArchived);
        if (search) params.set('search', search);

        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }

    removeNotificationFromUI(notificationId) {
        const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notificationElement) {
            notificationElement.remove();
            this.selectedNotifications.delete(notificationId);
            this.updateSelectedCount();
            this.updateBulkActionsVisibility();
            this.updateSelectAllState();
        }
    }

    updateStats() {
        // Reload page to update statistics
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    showSuccess(message) {
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            console.log(`[SUCCESS] ${message}`);
        }
    }

    showError(message) {
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        } else {
            console.error(`[ERROR] ${message}`);
        }
    }

    showWarning(message) {
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            console.warn(`[WARNING] ${message}`);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new NotificationArchive();
});
