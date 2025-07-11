/**
 * Business Registration Wizard JavaScript
 * Handles multi-step registration process for business verification
 */

class BusinessRegistrationWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.uploadedDocuments = [];
        this.requiredDocuments = {};
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadRequiredDocuments();
        this.updateStepIndicators();
    }

    bindEvents() {
        // Form validation on input
        document.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });

        // Application type change
        document.getElementById('application_type').addEventListener('change', (e) => {
            this.loadRequiredDocuments(e.target.value);
        });
    }

    async loadRequiredDocuments(applicationType = 'verified_partner') {
        try {
            const response = await fetch(`/business/documents/required?application_type=${applicationType}`);
            const data = await response.json();
            
            if (data.success) {
                this.requiredDocuments = data.required_documents;
                this.updateRequiredDocumentsList();
                this.generateDocumentUploadForms();
            }
        } catch (error) {
            console.error('Error loading required documents:', error);
        }
    }

    updateRequiredDocumentsList() {
        const list = document.getElementById('required-documents-list');
        list.innerHTML = '';
        
        Object.entries(this.requiredDocuments).forEach(([type, name]) => {
            const li = document.createElement('li');
            li.textContent = name;
            list.appendChild(li);
        });
    }

    generateDocumentUploadForms() {
        const container = document.getElementById('document-upload-area');
        container.innerHTML = '';
        
        Object.entries(this.requiredDocuments).forEach(([type, name]) => {
            const formHtml = this.createDocumentUploadForm(type, name, true);
            container.insertAdjacentHTML('beforeend', formHtml);
        });
    }

    createDocumentUploadForm(type, name, required = false) {
        const randomId = Math.random().toString(36).substr(2, 9);
        
        return `
            <div class="card mb-3 document-upload-form" data-document-type="${type}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        ${name} ${required ? '<span class="text-danger">*</span>' : ''}
                    </h6>
                    ${!required ? '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDocumentForm(this)"><i class="fas fa-trash"></i></button>' : ''}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Document File ${required ? '<span class="text-danger">*</span>' : ''}</label>
                                <input type="file" class="form-control document-file" 
                                       accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx" 
                                       data-document-type="${type}" ${required ? 'required' : ''}>
                                <div class="form-text">Max size: 10MB. Formats: PDF, Images, Word documents</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Document Name</label>
                                <input type="text" class="form-control" placeholder="Enter document name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Document Date</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Issuing Authority</label>
                                <input type="text" class="form-control" placeholder="e.g., Department of Planning and Investment">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Document Number</label>
                                <input type="text" class="form-control" placeholder="Document reference number">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="2" placeholder="Additional notes about this document"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="uploadDocument(this)">
                        <i class="fas fa-upload"></i> Upload Document
                    </button>
                </div>
            </div>
        `;
    }

    async nextStep() {
        if (!await this.validateCurrentStep()) {
            return;
        }

        if (this.currentStep < this.totalSteps) {
            await this.processCurrentStep();
            this.currentStep++;
            this.updateStepDisplay();
        }
    }

    previousStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateStepDisplay();
        }
    }

    async validateCurrentStep() {
        const currentForm = document.getElementById(`step${this.currentStep}-form`);
        if (!currentForm) return true;

        let isValid = true;
        const inputs = currentForm.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        // Special validation for step 3 (documents)
        if (this.currentStep === 3) {
            const requiredUploaded = this.checkRequiredDocuments();
            if (!requiredUploaded) {
                this.showAlert('Please upload all required documents before proceeding.', 'warning');
                isValid = false;
            }
        }

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            errorMessage = 'This field is required.';
            isValid = false;
        }

        // Email validation
        if (field.type === 'email' && value && !this.isValidEmail(value)) {
            errorMessage = 'Please enter a valid email address.';
            isValid = false;
        }

        // Password validation
        if (field.name === 'password' && value && value.length < 8) {
            errorMessage = 'Password must be at least 8 characters long.';
            isValid = false;
        }

        // Password confirmation validation
        if (field.name === 'password_confirmation' && value) {
            const password = document.getElementById('password').value;
            if (value !== password) {
                errorMessage = 'Passwords do not match.';
                isValid = false;
            }
        }

        // URL validation
        if (field.type === 'url' && value && !this.isValidUrl(value)) {
            errorMessage = 'Please enter a valid URL.';
            isValid = false;
        }

        this.setFieldValidation(field, isValid, errorMessage);
        return isValid;
    }

    setFieldValidation(field, isValid, errorMessage = '') {
        const feedbackElement = field.parentNode.querySelector('.invalid-feedback');
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (feedbackElement) feedbackElement.textContent = '';
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            if (feedbackElement) feedbackElement.textContent = errorMessage;
        }
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const feedbackElement = field.parentNode.querySelector('.invalid-feedback');
        if (feedbackElement) feedbackElement.textContent = '';
    }

    async processCurrentStep() {
        const formData = new FormData();
        const currentForm = document.getElementById(`step${this.currentStep}-form`);
        
        if (currentForm) {
            const inputs = currentForm.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    formData.append(input.name, input.checked);
                } else if (input.type !== 'file') {
                    formData.append(input.name, input.value);
                }
            });
        }

        try {
            this.showLoading(true);
            
            const response = await fetch(`/business/register/step-${this.currentStep}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();
            
            if (data.success) {
                if (this.currentStep === 1) {
                    // User account created, update UI
                    this.showAlert('Account created successfully!', 'success');
                } else if (this.currentStep === 2) {
                    // Business info saved
                    this.showAlert('Business information saved!', 'success');
                }
            } else {
                this.handleFormErrors(data.errors || {});
                throw new Error(data.message || 'An error occurred');
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
            this.currentStep--; // Stay on current step
        } finally {
            this.showLoading(false);
        }
    }

    async uploadDocument(button) {
        const form = button.closest('.document-upload-form');
        const fileInput = form.querySelector('.document-file');
        const file = fileInput.files[0];
        
        if (!file) {
            this.showAlert('Please select a file to upload.', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('document', file);
        formData.append('document_type', form.dataset.documentType);
        
        // Get additional document metadata
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.type !== 'file' && input.value) {
                formData.append(input.name || input.placeholder.toLowerCase().replace(/\s+/g, '_'), input.value);
            }
        });

        try {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            
            const response = await fetch('/business/documents/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.uploadedDocuments.push(data.document);
                this.updateUploadedDocumentsList();
                this.showAlert('Document uploaded successfully!', 'success');
                
                // Disable the form
                form.style.opacity = '0.6';
                form.style.pointerEvents = 'none';
                button.innerHTML = '<i class="fas fa-check"></i> Uploaded';
                button.classList.remove('btn-primary');
                button.classList.add('btn-success');
            } else {
                throw new Error(data.message || 'Upload failed');
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
            button.innerHTML = '<i class="fas fa-upload"></i> Upload Document';
        } finally {
            button.disabled = false;
        }
    }

    updateUploadedDocumentsList() {
        const container = document.getElementById('uploaded-documents');
        
        if (this.uploadedDocuments.length === 0) {
            container.innerHTML = '';
            return;
        }

        let html = '<h5 class="mb-3">Uploaded Documents</h5><div class="list-group">';
        
        this.uploadedDocuments.forEach(doc => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${doc.name}</h6>
                        <small class="text-muted">${doc.type} • ${doc.size}</small>
                    </div>
                    <div>
                        <span class="badge bg-success">${doc.status}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                                onclick="removeUploadedDocument(${doc.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }

    async removeUploadedDocument(documentId) {
        if (!confirm('Are you sure you want to remove this document?')) {
            return;
        }

        try {
            const response = await fetch('/business/documents/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ document_id: documentId })
            });

            const data = await response.json();
            
            if (data.success) {
                this.uploadedDocuments = this.uploadedDocuments.filter(doc => doc.id !== documentId);
                this.updateUploadedDocumentsList();
                this.showAlert('Document removed successfully!', 'success');
                
                // Re-enable the corresponding form
                const forms = document.querySelectorAll('.document-upload-form');
                forms.forEach(form => {
                    form.style.opacity = '1';
                    form.style.pointerEvents = 'auto';
                    const button = form.querySelector('button');
                    button.innerHTML = '<i class="fas fa-upload"></i> Upload Document';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                });
            } else {
                throw new Error(data.message || 'Failed to remove document');
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
        }
    }

    checkRequiredDocuments() {
        const requiredTypes = Object.keys(this.requiredDocuments);
        const uploadedTypes = this.uploadedDocuments.map(doc => doc.type);
        
        return requiredTypes.every(type => uploadedTypes.includes(type));
    }

    updateStepDisplay() {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(step => {
            step.classList.add('d-none');
        });
        
        // Show current step
        document.getElementById(`step${this.currentStep}`).classList.remove('d-none');
        
        // Update step indicators
        this.updateStepIndicators();
        
        // Update navigation buttons
        this.updateNavigationButtons();
        
        // Update progress bar
        const progress = (this.currentStep / this.totalSteps) * 100;
        document.getElementById('progress-bar').style.width = `${progress}%`;
        
        // Populate review step
        if (this.currentStep === 4) {
            this.populateReviewStep();
        }
    }

    updateStepIndicators() {
        for (let i = 1; i <= this.totalSteps; i++) {
            const indicator = document.getElementById(`step${i}-indicator`);
            indicator.classList.remove('active', 'completed');
            
            if (i < this.currentStep) {
                indicator.classList.add('completed');
            } else if (i === this.currentStep) {
                indicator.classList.add('active');
            }
        }
    }

    updateNavigationButtons() {
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');
        
        prevBtn.disabled = this.currentStep === 1;
        
        if (this.currentStep === this.totalSteps) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        } else {
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        }
    }

    populateReviewStep() {
        // Populate account review
        const accountData = this.getFormData('step1-form');
        document.getElementById('account-review').innerHTML = `
            <p><strong>Name:</strong> ${accountData.name}</p>
            <p><strong>Email:</strong> ${accountData.email}</p>
            <p><strong>Username:</strong> ${accountData.username}</p>
            <p><strong>Phone:</strong> ${accountData.phone || 'N/A'}</p>
        `;
        
        // Populate business review
        const businessData = this.getFormData('step2-form');
        document.getElementById('business-review').innerHTML = `
            <p><strong>Business Name:</strong> ${businessData.business_name}</p>
            <p><strong>Type:</strong> ${businessData.application_type}</p>
            <p><strong>Tax ID:</strong> ${businessData.tax_id}</p>
            <p><strong>Address:</strong> ${businessData.business_address}</p>
            <p><strong>Phone:</strong> ${businessData.business_phone || 'N/A'}</p>
            <p><strong>Email:</strong> ${businessData.business_email || 'N/A'}</p>
        `;
        
        // Populate documents review
        let documentsHtml = '';
        if (this.uploadedDocuments.length > 0) {
            documentsHtml = '<ul class="list-unstyled">';
            this.uploadedDocuments.forEach(doc => {
                documentsHtml += `<li><i class="fas fa-file text-primary"></i> ${doc.name} (${doc.type})</li>`;
            });
            documentsHtml += '</ul>';
        } else {
            documentsHtml = '<p class="text-muted">No documents uploaded</p>';
        }
        document.getElementById('documents-review').innerHTML = documentsHtml;
    }

    getFormData(formId) {
        const form = document.getElementById(formId);
        const data = {};
        
        if (form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    data[input.name] = input.checked;
                } else {
                    data[input.name] = input.value;
                }
            });
        }
        
        return data;
    }

    async submitApplication() {
        if (!await this.validateCurrentStep()) {
            return;
        }

        const formData = this.getFormData('step4-form');
        
        try {
            this.showLoading(true, 'Submitting your application...');
            
            const response = await fetch('/business/register/step-4', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Application submitted successfully! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 2000);
            } else {
                throw new Error(data.message || 'Submission failed');
            }
        } catch (error) {
            this.showAlert(error.message, 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    showLoading(show, message = 'Processing...') {
        const modal = document.getElementById('loadingModal');
        const messageElement = modal.querySelector('h5');
        
        if (messageElement) {
            messageElement.textContent = message;
        }
        
        if (show) {
            new bootstrap.Modal(modal).show();
        } else {
            bootstrap.Modal.getInstance(modal)?.hide();
        }
    }

    showAlert(message, type = 'info') {
        // Create alert element
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insert at top of page
        const container = document.querySelector('.container');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    handleFormErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                this.setFieldValidation(field, false, errors[fieldName][0]);
            }
        });
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
}

// Global functions for HTML onclick events
function nextStep() {
    wizard.nextStep();
}

function previousStep() {
    wizard.previousStep();
}

function submitApplication() {
    wizard.submitApplication();
}

function uploadDocument(button) {
    wizard.uploadDocument(button);
}

function removeUploadedDocument(documentId) {
    wizard.removeUploadedDocument(documentId);
}

function addDocumentUpload() {
    const container = document.getElementById('document-upload-area');
    const formHtml = wizard.createDocumentUploadForm('other', 'Additional Document', false);
    container.insertAdjacentHTML('beforeend', formHtml);
}

function removeDocumentForm(button) {
    const form = button.closest('.document-upload-form');
    form.remove();
}

// Initialize wizard when DOM is loaded
let wizard;
document.addEventListener('DOMContentLoaded', function() {
    wizard = new BusinessRegistrationWizard();
});
