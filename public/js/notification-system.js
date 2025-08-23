/**
 * Notification System with SweetAlert2 Integration
 * Handles all notification-related operations with beautiful alerts
 */

// Notification operations with SweetAlert
window.NotificationSystem = {
    
    /**
     * Mark notification as read
     */
    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/dashboard/notifications/${notificationId}/mark-read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                window.showToast('Đã đánh dấu là đã đọc', 'success');
                location.reload();
            } else {
                window.showError('Lỗi', data.message || 'Không thể đánh dấu thông báo');
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
            window.showError('Lỗi', 'Có lỗi xảy ra khi đánh dấu thông báo');
        }
    },

    /**
     * Archive notification
     */
    async archive(notificationId) {
        const result = await window.showConfirm(
            'Lưu trữ thông báo',
            'Bạn có muốn lưu trữ thông báo này không?'
        );

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/dashboard/notifications/${notificationId}/archive`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    window.showToast('Đã lưu trữ thông báo', 'success');
                    location.reload();
                } else {
                    window.showError('Lỗi', data.message || 'Không thể lưu trữ thông báo');
                }
            } catch (error) {
                console.error('Error archiving notification:', error);
                window.showError('Lỗi', 'Có lỗi xảy ra khi lưu trữ thông báo');
            }
        }
    },

    /**
     * Delete notification
     */
    async delete(notificationId) {
        const result = await window.showDeleteConfirm('thông báo này');

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/dashboard/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    window.showToast('Đã xóa thông báo', 'success');
                    location.reload();
                } else {
                    window.showError('Lỗi', data.message || 'Không thể xóa thông báo');
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
                window.showError('Lỗi', 'Có lỗi xảy ra khi xóa thông báo');
            }
        }
    },

    /**
     * Restore archived notification
     */
    async restore(notificationId) {
        const result = await window.showConfirm(
            'Khôi phục thông báo',
            'Bạn có muốn khôi phục thông báo này từ lưu trữ không?'
        );

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/dashboard/notifications/${notificationId}/restore`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    window.showToast('Đã khôi phục thông báo', 'success');
                    location.reload();
                } else {
                    window.showError('Lỗi', data.message || 'Không thể khôi phục thông báo');
                }
            } catch (error) {
                console.error('Error restoring notification:', error);
                window.showError('Lỗi', 'Có lỗi xảy ra khi khôi phục thông báo');
            }
        }
    },

    /**
     * Bulk operations
     */
    async bulkOperation(operation, selectedIds) {
        if (selectedIds.length === 0) {
            window.showWarning('Chưa chọn thông báo', 'Vui lòng chọn ít nhất một thông báo để thực hiện thao tác.');
            return;
        }

        let title, message, confirmText;
        
        switch (operation) {
            case 'mark-read':
                title = 'Đánh dấu đã đọc';
                message = `Bạn có muốn đánh dấu ${selectedIds.length} thông báo là đã đọc không?`;
                confirmText = 'Đánh dấu';
                break;
            case 'archive':
                title = 'Lưu trữ thông báo';
                message = `Bạn có muốn lưu trữ ${selectedIds.length} thông báo không?`;
                confirmText = 'Lưu trữ';
                break;
            case 'delete':
                return await this.bulkDelete(selectedIds);
            case 'restore':
                title = 'Khôi phục thông báo';
                message = `Bạn có muốn khôi phục ${selectedIds.length} thông báo từ lưu trữ không?`;
                confirmText = 'Khôi phục';
                break;
            default:
                window.showError('Lỗi', 'Thao tác không hợp lệ');
                return;
        }

        const result = await window.showConfirm(title, message, { confirmButtonText: confirmText });

        if (result.isConfirmed) {
            try {
                const response = await fetch('/dashboard/notifications/bulk-action', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: operation,
                        notification_ids: selectedIds
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    window.showSuccess('Thành công', data.message);
                    location.reload();
                } else {
                    window.showError('Lỗi', data.message || 'Không thể thực hiện thao tác');
                }
            } catch (error) {
                console.error('Error performing bulk operation:', error);
                window.showError('Lỗi', 'Có lỗi xảy ra khi thực hiện thao tác');
            }
        }
    },

    /**
     * Bulk delete with special confirmation
     */
    async bulkDelete(selectedIds) {
        const result = await window.showDeleteConfirm(`${selectedIds.length} thông báo`);

        if (result.isConfirmed) {
            try {
                const response = await fetch('/dashboard/notifications/bulk-action', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        notification_ids: selectedIds
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    window.showSuccess('Thành công', data.message);
                    location.reload();
                } else {
                    window.showError('Lỗi', data.message || 'Không thể xóa thông báo');
                }
            } catch (error) {
                console.error('Error performing bulk delete:', error);
                window.showError('Lỗi', 'Có lỗi xảy ra khi xóa thông báo');
            }
        }
    },

    /**
     * Toggle archive view
     */
    toggleArchiveView() {
        const currentUrl = new URL(window.location.href);
        const isArchived = currentUrl.searchParams.get('archived') === '1';
        
        if (isArchived) {
            currentUrl.searchParams.delete('archived');
        } else {
            currentUrl.searchParams.set('archived', '1');
        }
        
        window.location.href = currentUrl.toString();
    }
};

// Initialize notification system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Notification System with SweetAlert2 initialized');
});
