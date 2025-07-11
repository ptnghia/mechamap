# 📋 MechaMap User Registration & Permission System Migration Plan

**Generated:** 2025-07-12 00:46:41  
**Backup Timestamp:** 2025-07-12_00-46-41  
**Project:** MechaMap Laravel Backend  
**Version:** User Registration & Permission System Overhaul

---

## 🎯 **MIGRATION OVERVIEW**

### **Objective**
Triển khai hệ thống đăng ký và phân quyền người dùng MechaMap hoàn chỉnh theo 4 phases để fix các vấn đề nghiêm trọng:
- Thiếu business information collection cho business partners
- Missing verification workflow cho business accounts  
- Inconsistent marketplace permissions logic
- Lack of security và compliance features

### **Current Database State**
- **Total Tables:** 135 tables
- **Total Users:** 92 users
- **Migration Status:** 89 migrations ran, 2 pending
- **Backup Created:** ✅ Complete (Structure + Critical Data + Users)

---

## 🔄 **MIGRATION PHASES**

### **Phase 1: Cải thiện form đăng ký (Ưu tiên cao)**
**Duration:** 1-2 tuần  
**Risk Level:** 🟡 Medium

#### **Database Changes:**
- ✅ **New Migration:** `add_business_fields_to_users_table.php`
  - `company_name` VARCHAR(255) NULL
  - `business_license` VARCHAR(255) NULL  
  - `tax_code` VARCHAR(50) NULL
  - `business_description` TEXT NULL
  - `business_categories` JSON NULL
  - `business_phone` VARCHAR(20) NULL
  - `business_email` VARCHAR(255) NULL
  - `business_address` TEXT NULL
  - `verification_documents` JSON NULL

#### **New Files:**
- `app/Http/Controllers/Auth/RegisterWizardController.php`
- `app/Http/Requests/Auth/BasicRegistrationRequest.php`
- `app/Http/Requests/Auth/BusinessRegistrationRequest.php`
- `app/Services/RegistrationWizardService.php`
- `resources/views/auth/wizard/step1.blade.php`
- `resources/views/auth/wizard/step2.blade.php`
- `resources/views/components/registration-wizard.blade.php`
- `public/js/registration-wizard.js`
- `public/css/registration-wizard.css`

#### **Modified Files:**
- `app/Models/User.php` - Add business fields to $fillable
- `routes/auth.php` - Add wizard routes

---

### **Phase 2: Business verification system (Ưu tiên cao)**
**Duration:** 1-2 tuần  
**Risk Level:** 🟡 Medium

#### **Database Changes:**
- ✅ **Update existing fields:**
  - `is_verified_business` BOOLEAN DEFAULT FALSE
  - `verified_at` TIMESTAMP NULL
  - `verified_by` BIGINT UNSIGNED NULL
  - `verification_notes` TEXT NULL

#### **New Files:**
- `app/Http/Controllers/Admin/BusinessVerificationController.php`
- `app/Services/BusinessVerificationService.php`
- `app/Mail/BusinessVerificationNotification.php`
- `app/Events/BusinessVerificationStatusChanged.php`
- `app/Http/Controllers/Business/DashboardController.php`
- `app/Services/ProfileCompletionService.php`
- `app/Http/Controllers/DocumentUploadController.php`
- `resources/views/admin/business-verification/`
- `resources/views/business/dashboard.blade.php`

#### **Modified Files:**
- `routes/admin.php` - Add verification routes
- `app/Http/Middleware/AdminPermissionCheck.php` - Add verification permissions

---

### **Phase 3: Marketplace permissions refinement (Ưu tiên trung bình)**
**Duration:** 1 tuần  
**Risk Level:** 🟢 Low

#### **Configuration Changes:**
- ✅ **Update:** `config/mechamap_permissions.php`
  - Fix Guest vs Member marketplace logic
  - Clarify Brand role permissions
  - Update permission matrix consistency

#### **New Files:**
- `app/Http/Middleware/BusinessVerifiedMiddleware.php`
- `app/Services/CommissionService.php`
- `app/Services/RoleRedirectService.php`

#### **Modified Files:**
- `app/Http/Middleware/MarketplacePermissionMiddleware.php`
- `app/Services/MarketplacePermissionService.php`
- `resources/views/layouts/header.blade.php`

---

### **Phase 4: Security và compliance (Ưu tiên trung bình)**
**Duration:** 1-2 tuần  
**Risk Level:** 🟡 Medium

#### **New Files:**
- `app/Services/DocumentVerificationService.php`
- `app/Services/AuditTrailService.php`
- `app/Http/Controllers/Admin/ComplianceController.php`
- `app/Http/Middleware/BusinessSecurityMiddleware.php`
- `app/Services/SecurityService.php`
- `app/Services/IdentityVerificationService.php`
- `app/Services/EncryptionService.php`
- `app/Services/FraudDetectionService.php`

---

## 🔒 **ROLLBACK STRATEGY**

### **Immediate Rollback (Emergency)**
```bash
# Restore from backup
php database/backups/mechamap_backup_2025-07-12_00-46-41_restore.php

# Reset migrations if needed
php artisan migrate:reset
php artisan migrate
```

### **Selective Rollback**
```bash
# Rollback specific migration
php artisan migrate:rollback --step=1

# Rollback to specific batch
php artisan migrate:rollback --batch=X
```

### **File Rollback**
- Git reset to commit before changes
- Restore modified files from backup
- Clear cache: `php artisan cache:clear`

---

## ⚠️ **RISK ASSESSMENT**

### **High Risk Areas**
1. **User Model Changes** - Affects authentication system
2. **Permission System** - Could break marketplace functionality  
3. **Database Schema** - Large number of existing users (92)

### **Mitigation Strategies**
1. ✅ **Complete Database Backup** - Created and verified
2. 🔄 **Staging Environment Testing** - Required before production
3. 📊 **Gradual Rollout** - Phase-by-phase implementation
4. 🔍 **Monitoring** - Track errors and performance
5. 📞 **Rollback Plan** - Ready for immediate execution

---

## 🧪 **TESTING STRATEGY**

### **Pre-Migration Testing**
- [ ] Backup restoration test
- [ ] Current functionality verification
- [ ] Performance baseline measurement

### **Phase Testing**
- [ ] Unit tests for new components
- [ ] Integration tests for workflows
- [ ] Browser tests for UI changes
- [ ] Permission matrix validation
- [ ] Load testing for critical paths

### **Post-Migration Validation**
- [ ] All existing users can login
- [ ] Marketplace permissions work correctly
- [ ] Business verification workflow functional
- [ ] No data loss or corruption
- [ ] Performance within acceptable limits

---

## 📊 **SUCCESS CRITERIA**

### **Phase 1 Success**
- [ ] Multi-step registration form functional
- [ ] Business information collected for new registrations
- [ ] Existing users unaffected
- [ ] No authentication issues

### **Phase 2 Success**
- [ ] Admin can verify business accounts
- [ ] Users receive verification notifications
- [ ] Business dashboard accessible
- [ ] Document upload working

### **Phase 3 Success**
- [ ] Permission matrix consistent
- [ ] Marketplace features work per role
- [ ] No unauthorized access
- [ ] Commission rates applied correctly

### **Phase 4 Success**
- [ ] Security enhancements active
- [ ] Audit trail logging
- [ ] Compliance reporting functional
- [ ] No security vulnerabilities

---

## 🚀 **DEPLOYMENT CHECKLIST**

### **Pre-Deployment**
- [x] Database backup completed
- [x] Migration plan documented
- [ ] Staging environment setup
- [ ] Testing environment configured
- [ ] Team notification sent

### **During Deployment**
- [ ] Maintenance mode enabled
- [ ] Database migrations executed
- [ ] File deployments completed
- [ ] Cache cleared
- [ ] Services restarted

### **Post-Deployment**
- [ ] Functionality verification
- [ ] Error monitoring active
- [ ] Performance monitoring
- [ ] User feedback collection
- [ ] Documentation updated

---

## 📞 **EMERGENCY CONTACTS**

**Development Team:**
- Lead Developer: [Contact Info]
- Database Admin: [Contact Info]
- DevOps Engineer: [Contact Info]

**Rollback Authority:**
- Project Manager: [Contact Info]
- Technical Lead: [Contact Info]

---

## 📝 **CHANGE LOG**

| Date | Phase | Changes | Status |
|------|-------|---------|--------|
| 2025-07-12 | Preparation | Database backup created | ✅ Complete |
| 2025-07-12 | Preparation | Migration plan documented | ✅ Complete |
| TBD | Phase 1 | Multi-step registration | 🔄 Pending |
| TBD | Phase 2 | Business verification | 🔄 Pending |
| TBD | Phase 3 | Permission refinement | 🔄 Pending |
| TBD | Phase 4 | Security & compliance | 🔄 Pending |

---

**⚡ CRITICAL NOTE:** This migration affects core user authentication and marketplace functionality. Proceed with extreme caution and ensure all testing is completed before production deployment.
