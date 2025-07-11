/**
 * Admin Business Verification JavaScript
 * Handles admin interface interactions for business verification management
 */

class BusinessVerificationAdmin {
    constructor() {
        this.selectedApplications = new Set();
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeTooltips();
        this.loadAnalytics();
    }

    bindEvents() {
        // Bulk selection
        document.getElementById('selectAll')?.addEventListener('change', (e) => {
            this.toggleSelectAll(e.target.checked);
        });

        // Individual checkboxes
        document.querySelectorAll('.application-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.toggleApplicationSelection(e.target.value, e.target.checked);
            });
        });

        // Auto-refresh every 30 seconds
        setInterval(() => {
            this.refreshApplicationStatus();
        }, 30000);
    }

    initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    async loadAnalytics() {
        try {
            const response = await fetch('/admin/verification/analytics');
            const data = await response.json();
            
            if (data.success) {
                this.updateAnalyticsDisplay(data.stats, data.monthly_stats);
            }
        } catch (error) {
            console.error('Error loading analytics:', error);
        }
    }

    updateAnalyticsDisplay(stats, monthlyStats) {
        // Update statistics cards if they exist
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                element.textContent = stats[key];
            }
        });

        // Update charts if chart library is available
        if (typeof Chart !== 'undefined' && monthlyStats) {
            this.updateCharts(monthlyStats);
        }
    }

    toggleSelectAll(checked) {
        document.querySelectorAll('.application-checkbox').forEach(checkbox => {
            checkbox.checked = checked;
            this.toggleApplicationSelection(checkbox.value, checked);
        });
        this.updateBulkActionsBar();
    }

    toggleApplicationSelection(applicationId, selected) {
        if (selected) {
            this.selectedApplications.add(applicationId);
        } else {
            this.selectedApplications.delete(applicationId);
        }
        this.updateBulkActionsBar();
    }

    updateBulkActionsBar() {
        const bulkBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');
        
        if (this.selectedApplications.size > 0) {
            bulkBar?.classList.remove('d-none');
            if (selectedCount) {
                selectedCount.textContent = this.selectedApplications.size;
            }
        } else {
            bulkBar?.classList.add('d-none');
        }
    }

    async assignReviewer(event, applicationId) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const reviewerId = formData.get('reviewer_id');
        
        if (!reviewerId) {
            this.showAlert('Please select a reviewer', 'warning');
            return;
        }

        try {
            this.showLoading(true);
            
            const response = await fetch(`/admin/verification/applications/${applicationId}/assign-reviewer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ reviewer_id: reviewerId })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Reviewer assigned successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    async approveApplication(applicationId) {
        const notes = await this.promptForNotes('Approval Notes (optional):');
        
        try {
            this.showLoading(true);
            
            const response = await fetch(`/admin/verification/applications/${applicationId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ notes })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Application approved successfully', 'success');
                this.updateApplicationStatus(applicationId, data.status);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    async rejectApplication(applicationId) {
        const reason = await this.promptForReason('Rejection Reason (required):');
        
        if (!reason) {
            this.showAlert('Rejection reason is required', 'warning');
            return;
        }

        try {
            this.showLoading(true);
            
            const response = await fetch(`/admin/verification/applications/${applicationId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ reason })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Application rejected successfully', 'success');
                this.updateApplicationStatus(applicationId, data.status);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    async requestAdditionalInfo(applicationId) {
        const info = await this.promptForInfo('Additional Information Required:');
        
        if (!info) {
            this.showAlert('Please specify what additional information is required', 'warning');
            return;
        }

        try {
            this.showLoading(true);
            
            const response = await fetch(`/admin/verification/applications/${applicationId}/request-info`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ info_requested: info })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Additional information requested successfully', 'success');
                this.updateApplicationStatus(applicationId, data.status);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    async verifyDocument(documentId, status) {
        const notes = status === 'rejected' ? 
            await this.promptForNotes('Rejection reason:') : 
            await this.promptForNotes('Verification notes (optional):');

        try {
            this.showLoading(true);
            
            const response = await fetch(`/admin/verification/documents/${documentId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status, notes })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Document verification updated successfully', 'success');
                this.updateDocumentStatus(documentId, data.document);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    async previewDocument(documentId) {
        try {
            const url = `/admin/verification/documents/${documentId}/preview`;
            window.open(url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
        } catch (error) {
            this.showAlert('Error opening document preview', 'danger');
        }
    }

    async bulkAction(action) {
        if (this.selectedApplications.size === 0) {
            this.showAlert('Please select applications first', 'warning');
            return;
        }

        let additionalData = {};
        
        if (action === 'assign_reviewer') {
            const reviewerId = await this.promptForReviewer();
            if (!reviewerId) return;
            additionalData.reviewer_id = reviewerId;
        } else if (action === 'reject') {
            const reason = await this.promptForReason('Rejection reason for all selected applications:');
            if (!reason) return;
            additionalData.reason = reason;
        } else if (action === 'set_priority') {
            const priority = await this.promptForPriority();
            if (!priority) return;
            additionalData.priority = priority;
        }

        try {
            this.showLoading(true);
            
            const response = await fetch('/admin/verification/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action,
                    application_ids: Array.from(this.selectedApplications),
                    ...additionalData
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    updateApplicationStatus(applicationId, status) {
        const row = document.querySelector(`tr[data-application-id="${applicationId}"]`);
        if (row) {
            const statusCell = row.querySelector('.status-cell');
            if (statusCell) {
                statusCell.innerHTML = this.getStatusBadge(status);
            }
        }
    }

    updateDocumentStatus(documentId, documentData) {
        const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
        if (row) {
            const statusCell = row.querySelector('.document-status-cell');
            if (statusCell) {
                statusCell.innerHTML = documentData.verification_status_badge;
            }
        }
    }

    getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning">Chờ xử lý</span>',
            'under_review': '<span class="badge bg-info">Đang xem xét</span>',
            'approved': '<span class="badge bg-success">Đã duyệt</span>',
            'rejected': '<span class="badge bg-danger">Từ chối</span>',
            'requires_additional_info': '<span class="badge bg-secondary">Cần bổ sung</span>'
        };
        return badges[status] || '<span class="badge bg-light">Không xác định</span>';
    }

    async refreshApplicationStatus() {
        // Silently refresh application statuses without full page reload
        try {
            const currentUrl = new URL(window.location);
            const response = await fetch(currentUrl.pathname + currentUrl.search, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                // Update only the table content
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('tbody');
                const currentTableBody = document.querySelector('tbody');
                
                if (newTableBody && currentTableBody) {
                    currentTableBody.innerHTML = newTableBody.innerHTML;
                    this.bindEvents(); // Re-bind events for new elements
                }
            }
        } catch (error) {
            console.error('Error refreshing application status:', error);
        }
    }

    // Utility methods
    async promptForNotes(message) {
        return new Promise((resolve) => {
            const notes = prompt(message);
            resolve(notes);
        });
    }

    async promptForReason(message) {
        return new Promise((resolve) => {
            const reason = prompt(message);
            resolve(reason);
        });
    }

    async promptForInfo(message) {
        return new Promise((resolve) => {
            const info = prompt(message);
            resolve(info);
        });
    }

    async promptForReviewer() {
        return new Promise((resolve) => {
            const reviewers = document.querySelectorAll('#reviewer_id option');
            let options = 'Select a reviewer:\n';
            reviewers.forEach((option, index) => {
                if (option.value) {
                    options += `${index}: ${option.textContent}\n`;
                }
            });
            
            const selection = prompt(options + '\nEnter the number:');
            const selectedOption = reviewers[parseInt(selection)];
            resolve(selectedOption ? selectedOption.value : null);
        });
    }

    async promptForPriority() {
        return new Promise((resolve) => {
            const priority = prompt('Select priority:\n1: Low\n2: Medium\n3: High\n4: Urgent\n\nEnter number:');
            const priorities = ['', 'low', 'medium', 'high', 'urgent'];
            resolve(priorities[parseInt(priority)] || null);
        });
    }

    showLoading(show, message = 'Processing...') {
        let overlay = document.getElementById('loadingOverlay');
        
        if (show) {
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'loadingOverlay';
                overlay.className = 'loading-overlay';
                overlay.innerHTML = `
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2">${message}</div>
                    </div>
                `;
                document.body.appendChild(overlay);
            }
            overlay.style.display = 'flex';
        } else {
            if (overlay) {
                overlay.style.display = 'none';
            }
        }
    }

    showAlert(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

// Global functions for HTML onclick events
function toggleBulkActions() {
    const bulkBar = document.getElementById('bulkActionsBar');
    bulkBar?.classList.toggle('d-none');
}

function quickApprove(applicationId) {
    verificationAdmin.approveApplication(applicationId);
}

function quickReject(applicationId) {
    verificationAdmin.rejectApplication(applicationId);
}

function approveApplication(applicationId) {
    verificationAdmin.approveApplication(applicationId);
}

function rejectApplication(applicationId) {
    verificationAdmin.rejectApplication(applicationId);
}

function requestAdditionalInfo(applicationId) {
    verificationAdmin.requestAdditionalInfo(applicationId);
}

function assignReviewer(event, applicationId) {
    verificationAdmin.assignReviewer(event, applicationId);
}

function verifyDocument(documentId, status) {
    verificationAdmin.verifyDocument(documentId, status);
}

function previewDocument(documentId) {
    verificationAdmin.previewDocument(documentId);
}

function bulkAction(action) {
    verificationAdmin.bulkAction(action);
}

function sendNotification(applicationId) {
    verificationAdmin.showAlert('Notification feature coming soon', 'info');
}

function addInternalNote(applicationId) {
    verificationAdmin.showAlert('Internal notes feature coming soon', 'info');
}

function exportApplication(applicationId) {
    verificationAdmin.showAlert('Export feature coming soon', 'info');
}

// Initialize admin interface when DOM is loaded
let verificationAdmin;
document.addEventListener('DOMContentLoaded', function() {
    verificationAdmin = new BusinessVerificationAdmin();
});
