# 🏗️ Business Verification System - Technical Architecture

**Phase:** 2 - Business Verification Implementation  
**Date:** 2025-07-12  
**Status:** 🚧 IMPLEMENTATION READY  

---

## 🎯 **SYSTEM OVERVIEW**

### **📊 Architecture Components:**

```
Frontend Registration → Document Upload → Admin Review → Verification Decision → Role Upgrade
```

**Core Components:**
1. **Business Registration Enhancement** - Multi-step wizard with document upload
2. **Document Management System** - Secure storage and processing
3. **Admin Verification Dashboard** - Review and approval interface
4. **Notification System** - Status updates and communications
5. **Audit Trail System** - Complete verification history
6. **Role Upgrade Workflow** - Automated role transitions

---

## 🗃️ **DATABASE SCHEMA DESIGN**

### **1. Business Verification Applications Table:**
```sql
CREATE TABLE business_verification_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    application_type ENUM('manufacturer', 'supplier', 'brand', 'verified_partner') NOT NULL,
    status ENUM('pending', 'under_review', 'approved', 'rejected', 'requires_additional_info') DEFAULT 'pending',
    
    -- Business Information
    business_name VARCHAR(255) NOT NULL,
    business_type VARCHAR(100) NOT NULL,
    tax_id VARCHAR(50) NOT NULL,
    registration_number VARCHAR(100),
    business_address TEXT NOT NULL,
    business_phone VARCHAR(20),
    business_email VARCHAR(255),
    business_website VARCHAR(255),
    
    -- Verification Details
    submitted_at TIMESTAMP NULL,
    reviewed_at TIMESTAMP NULL,
    approved_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    
    -- Admin Actions
    reviewed_by BIGINT UNSIGNED NULL,
    approved_by BIGINT UNSIGNED NULL,
    rejected_by BIGINT UNSIGNED NULL,
    
    -- Decision Details
    approval_notes TEXT NULL,
    rejection_reason TEXT NULL,
    additional_info_requested TEXT NULL,
    
    -- Metadata
    verification_score INT DEFAULT 0,
    priority_level ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    estimated_review_time INT DEFAULT 72, -- hours
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (rejected_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_application_type (application_type),
    INDEX idx_submitted_at (submitted_at),
    INDEX idx_priority_level (priority_level)
);
```

### **2. Business Verification Documents Table:**
```sql
CREATE TABLE business_verification_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    document_type ENUM('business_license', 'tax_certificate', 'registration_certificate', 
                      'identity_document', 'bank_statement', 'utility_bill', 'other') NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    
    -- Verification Status
    verification_status ENUM('pending', 'verified', 'rejected', 'requires_resubmission') DEFAULT 'pending',
    verification_notes TEXT NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    
    -- Security
    file_hash VARCHAR(64) NOT NULL,
    is_encrypted BOOLEAN DEFAULT FALSE,
    access_count INT DEFAULT 0,
    last_accessed_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES business_verification_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_application_id (application_id),
    INDEX idx_document_type (document_type),
    INDEX idx_verification_status (verification_status),
    INDEX idx_file_hash (file_hash)
);
```

### **3. Verification Audit Trail Table:**
```sql
CREATE TABLE business_verification_audit_trail (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    action_type ENUM('submitted', 'reviewed', 'approved', 'rejected', 'info_requested', 
                    'document_uploaded', 'document_verified', 'status_changed', 'role_upgraded') NOT NULL,
    
    -- Action Details
    performed_by BIGINT UNSIGNED NOT NULL,
    action_description TEXT NOT NULL,
    old_status VARCHAR(50) NULL,
    new_status VARCHAR(50) NULL,
    
    -- Additional Data
    metadata JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES business_verification_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_application_id (application_id),
    INDEX idx_action_type (action_type),
    INDEX idx_performed_by (performed_by),
    INDEX idx_created_at (created_at)
);
```

### **4. Verification Templates Table:**
```sql
CREATE TABLE business_verification_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_type ENUM('approval_email', 'rejection_email', 'info_request_email', 'sms_notification') NOT NULL,
    business_type ENUM('manufacturer', 'supplier', 'brand', 'verified_partner', 'all') DEFAULT 'all',
    
    -- Template Content
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    variables JSON NULL, -- Available template variables
    
    -- Settings
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    language VARCHAR(5) DEFAULT 'vi',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_template_type (template_type),
    INDEX idx_business_type (business_type),
    INDEX idx_is_active (is_active)
);
```

---

## 🔧 **CORE SERVICES ARCHITECTURE**

### **1. BusinessVerificationService:**
```php
class BusinessVerificationService
{
    // Application Management
    public function submitApplication(User $user, array $data): BusinessVerificationApplication
    public function updateApplication(BusinessVerificationApplication $app, array $data): bool
    public function getApplicationStatus(User $user): ?BusinessVerificationApplication
    
    // Document Management
    public function uploadDocument(BusinessVerificationApplication $app, UploadedFile $file, string $type): BusinessVerificationDocument
    public function verifyDocument(BusinessVerificationDocument $doc, User $admin, string $status, ?string $notes = null): bool
    public function deleteDocument(BusinessVerificationDocument $doc): bool
    
    // Review Process
    public function assignReviewer(BusinessVerificationApplication $app, User $reviewer): bool
    public function approveApplication(BusinessVerificationApplication $app, User $admin, ?string $notes = null): bool
    public function rejectApplication(BusinessVerificationApplication $app, User $admin, string $reason): bool
    public function requestAdditionalInfo(BusinessVerificationApplication $app, User $admin, string $info): bool
    
    // Analytics
    public function getVerificationStats(): array
    public function getApplicationsByStatus(string $status): Collection
    public function getReviewerWorkload(User $reviewer): array
}
```

### **2. DocumentSecurityService:**
```php
class DocumentSecurityService
{
    // File Security
    public function encryptFile(string $filePath): string
    public function decryptFile(string $encryptedPath): string
    public function generateSecureFilename(UploadedFile $file): string
    public function validateFileType(UploadedFile $file): bool
    public function scanForMalware(string $filePath): bool
    
    // Access Control
    public function generateAccessToken(BusinessVerificationDocument $doc, User $user): string
    public function validateAccess(string $token, User $user): bool
    public function logAccess(BusinessVerificationDocument $doc, User $user): void
    
    // File Management
    public function storeSecurely(UploadedFile $file, string $directory): string
    public function deleteSecurely(string $filePath): bool
    public function createThumbnail(string $filePath): ?string
}
```

### **3. VerificationNotificationService:**
```php
class VerificationNotificationService
{
    // Email Notifications
    public function sendApplicationSubmitted(BusinessVerificationApplication $app): bool
    public function sendApplicationApproved(BusinessVerificationApplication $app): bool
    public function sendApplicationRejected(BusinessVerificationApplication $app): bool
    public function sendAdditionalInfoRequested(BusinessVerificationApplication $app): bool
    
    // Admin Notifications
    public function notifyAdminsNewApplication(BusinessVerificationApplication $app): bool
    public function notifyReviewerAssigned(BusinessVerificationApplication $app, User $reviewer): bool
    public function sendDailyDigest(User $admin): bool
    
    // SMS Notifications (Optional)
    public function sendSMSNotification(User $user, string $message): bool
    
    // In-App Notifications
    public function createInAppNotification(User $user, string $type, array $data): bool
}
```

### **4. VerificationAuditService:**
```php
class VerificationAuditService
{
    // Audit Logging
    public function logAction(BusinessVerificationApplication $app, string $action, User $user, array $details = []): void
    public function logDocumentAction(BusinessVerificationDocument $doc, string $action, User $user): void
    public function logStatusChange(BusinessVerificationApplication $app, string $oldStatus, string $newStatus, User $user): void
    
    // Audit Retrieval
    public function getApplicationAuditTrail(BusinessVerificationApplication $app): Collection
    public function getUserAuditTrail(User $user): Collection
    public function getAdminActionHistory(User $admin): Collection
    
    // Compliance
    public function generateComplianceReport(Carbon $startDate, Carbon $endDate): array
    public function exportAuditData(array $filters): string
}
```

---

## 🎨 **FRONTEND ARCHITECTURE**

### **1. Registration Wizard Enhancement:**
```
Step 1: Account Type Selection (Existing)
Step 2: Basic Information (Enhanced)
Step 3: Business Information (NEW)
Step 4: Document Upload (NEW)
Step 5: Review & Submit (NEW)
```

### **2. Document Upload Component:**
```javascript
// Vue.js Component for Document Upload
const DocumentUploadComponent = {
    props: ['documentType', 'required', 'maxSize'],
    data() {
        return {
            files: [],
            uploading: false,
            progress: 0,
            errors: []
        }
    },
    methods: {
        handleFileSelect(event) {
            // File validation and preview
        },
        uploadFile(file) {
            // Secure file upload with progress
        },
        removeFile(index) {
            // File removal
        }
    }
}
```

### **3. Admin Dashboard Components:**
```
- ApplicationListComponent: Filterable list of applications
- ApplicationDetailComponent: Detailed application review
- DocumentViewerComponent: Secure document preview
- BulkActionComponent: Bulk approval/rejection
- AnalyticsComponent: Verification statistics
```

---

## 🔒 **SECURITY ARCHITECTURE**

### **1. File Security:**
- **Encrypted storage** for sensitive documents
- **Virus scanning** before storage
- **Access token system** for document viewing
- **Audit logging** for all file access
- **Automatic cleanup** of temporary files

### **2. Access Control:**
- **Role-based permissions** for verification actions
- **IP whitelisting** for admin access (optional)
- **Two-factor authentication** for sensitive operations
- **Session management** with timeout controls

### **3. Data Protection:**
- **GDPR compliance** for document handling
- **Data retention policies** for verification records
- **Secure deletion** of rejected applications
- **Encryption at rest** for sensitive data

---

## 📊 **PERFORMANCE ARCHITECTURE**

### **1. File Handling:**
- **Chunked uploads** for large files
- **Background processing** for document analysis
- **CDN integration** for file delivery
- **Thumbnail generation** for quick preview

### **2. Database Optimization:**
- **Proper indexing** for search queries
- **Query optimization** for dashboard views
- **Caching strategy** for frequently accessed data
- **Archive strategy** for old applications

### **3. Scalability:**
- **Queue system** for background tasks
- **Microservice architecture** for document processing
- **Load balancing** for file uploads
- **Horizontal scaling** support

---

## 🎊 **IMPLEMENTATION PHASES**

### **Phase 2A: Foundation (Week 1)**
- Database schema creation
- Core service development
- Basic admin interface
- Document upload functionality

### **Phase 2B: Enhancement (Week 2)**
- Advanced admin features
- Notification system
- Audit trail implementation
- Security hardening

### **Phase 2C: Polish (Week 3)**
- UI/UX refinements
- Performance optimization
- Comprehensive testing
- Documentation completion

**🎯 BUSINESS VERIFICATION ARCHITECTURE:** ✅ **COMPREHENSIVE & SCALABLE** - Ready for enterprise-grade business verification workflow!
