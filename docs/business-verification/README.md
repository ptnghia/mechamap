# 🏢 Business Verification System Documentation

**Enterprise-Grade Business Verification Platform for MechaMap**

[![Status](https://img.shields.io/badge/Status-Production%20Ready-green.svg)](../deployment-guide.md)
[![Version](https://img.shields.io/badge/Version-2.0-blue.svg)](CHANGELOG.md)
[![Security](https://img.shields.io/badge/Security-GDPR%20Compliant-orange.svg)](../security-guide.md)

---

## 🎯 **System Overview**

MechaMap Business Verification System là giải pháp enterprise-grade được phát triển qua 4 phases để quản lý và xác minh các đối tác kinh doanh trên marketplace. Hệ thống tập trung vào manual review workflow, bảo mật cao và tuân thủ pháp lý.

### **🏗️ Architecture Diagram**

```
┌─────────────────────────────────────────────────────────────────┐
│                    Business Verification Platform               │
├─────────────────────────────────────────────────────────────────┤
│  📝 Phase 1: Multi-Step Registration                           │
│  ├── Registration Wizard (4 steps)                             │
│  ├── Role-based Forms                                          │
│  └── Real-time Validation                                      │
├─────────────────────────────────────────────────────────────────┤
│  🔍 Phase 2: Document Verification                             │
│  ├── Document Upload & Security Scan                           │
│  ├── Manual Admin Review                                       │
│  ├── Approval/Rejection Workflow                               │
│  └── Email Notifications                                       │
├─────────────────────────────────────────────────────────────────┤
│  🛒 Phase 3: Marketplace Permissions                           │
│  ├── Unified Permission Service                                │
│  ├── Dynamic Commission Rates                                  │
│  ├── Business Verification Integration                         │
│  └── Performance Optimization                                  │
├─────────────────────────────────────────────────────────────────┤
│  🔒 Phase 4: Security & Compliance                             │
│  ├── Real-time Security Monitoring                             │
│  ├── Data Encryption & Privacy (GDPR/CCPA)                     │
│  ├── Comprehensive Audit Trail                                 │
│  └── Compliance Reporting Dashboard                            │
└─────────────────────────────────────────────────────────────────┘
```

## 🚀 **Key Features**

### **✅ Multi-Step Registration (Phase 1)**
- **4-Step Wizard**: Account → Business Info → Documents → Review
- **8 User Roles**: From Guest to Verified Partner
- **Real-time Validation**: Instant feedback và error handling
- **Progress Tracking**: Visual progress indicators
- **Professional UI**: Bootstrap 5 responsive design

### **✅ Document Verification (Phase 2)**
- **Manual Review**: No OCR dependency, pure admin workflow
- **8 Document Types**: Business license, tax certificate, ID, etc.
- **Security Scanning**: Malicious file detection
- **Verification Checklist**: Consistent review process
- **Bulk Operations**: Admin efficiency tools
- **Audit Trail**: Complete activity tracking

### **✅ Unified Permissions (Phase 3)**
- **Single Source of Truth**: Consolidated permission logic
- **Verification Integration**: Permissions based on verification status
- **Dynamic Commission**: Rates vary by verification (2%-10%)
- **Performance Caching**: Redis-based permission caching
- **Security Fixes**: Eliminated permission vulnerabilities

### **✅ Security & Compliance (Phase 4)**
- **Real-time Monitoring**: Threat detection với 4 levels
- **Data Encryption**: 10 sensitive fields encrypted
- **GDPR/CCPA Compliance**: Data anonymization & retention
- **Incident Response**: Real-time security alerts
- **Compliance Dashboard**: Professional reporting interface

## 👥 **User Roles & Permissions**

### **🎭 Role Hierarchy**

| Role | Level | Description | Marketplace Access |
|------|-------|-------------|-------------------|
| **Super Admin** | L1 | System administrator | Full Access |
| **System Admin** | L2 | Technical administrator | Full Access |
| **Content Admin** | L3 | Content management | Full Access |
| **Content Moderator** | L4 | Content moderation | Limited |
| **Marketplace Moderator** | L5 | Marketplace management | Full Access |
| **Community Moderator** | L6 | Community management | View Only |
| **Senior Member** | L7 | Experienced community member | Buy Digital Only |
| **Member** | L8 | Regular community member | Buy Digital Only |
| **Guest** | L10 | Unregistered visitor | View Only |
| **Verified Partner** | L11 | Verified business partner | Full Access |
| **Manufacturer** | L12 | Manufacturing company | Limited → Full* |
| **Supplier** | L13 | Supply company | Limited → Full* |
| **Brand** | L14 | Brand company | View Only |

*\*Access expands after business verification*

### **🛒 Marketplace Permission Matrix**

```
┌─────────────────────┬─────────────┬─────────────┬─────────────┬─────────────┐
│ Role                │ Buy Digital │ Buy New     │ Sell Digital│ Sell New    │
├─────────────────────┼─────────────┼─────────────┼─────────────┼─────────────┤
│ Guest               │ ❌          │ ❌          │ ❌          │ ❌          │
│ Member              │ ✅          │ ❌          │ ❌          │ ❌          │
│ Senior Member       │ ✅          │ ❌          │ ❌          │ ❌          │
│ Manufacturer (❌)   │ ✅          │ ❌          │ ❌          │ ❌          │
│ Manufacturer (✅)   │ ✅          │ ✅          │ ✅          │ ✅          │
│ Supplier (❌)       │ ✅          │ ❌          │ ❌          │ ❌          │
│ Supplier (✅)       │ ✅          │ ❌          │ ✅          │ ✅          │
│ Brand (Any)         │ ❌          │ ❌          │ ❌          │ ❌          │
│ Verified Partner    │ ✅          │ ✅          │ ✅          │ ✅          │
│ Admin Roles         │ ✅          │ ✅          │ ✅          │ ✅          │
└─────────────────────┴─────────────┴─────────────┴─────────────┴─────────────┘

Legend: ✅ = Allowed, ❌ = Not Allowed, (✅) = Verified, (❌) = Unverified
```

## 💰 **Commission Rate Structure**

### **📊 Dynamic Commission Rates**

| Role | Verification Status | Commission Rate | Incentive |
|------|-------------------|-----------------|-----------|
| **Manufacturer** | ❌ Unverified | 10.0% | High rate encourages verification |
| **Manufacturer** | ✅ Verified | 5.0% | 50% reduction reward |
| **Supplier** | ❌ Unverified | 10.0% | High rate encourages verification |
| **Supplier** | ✅ Verified | 3.0% | 70% reduction reward |
| **Brand** | Any Status | 0.0% | No selling, no commission |
| **Verified Partner** | ❌ Unverified | 10.0% | High rate encourages verification |
| **Verified Partner** | ✅ Verified | 2.0% | 80% reduction reward |
| **Admin Roles** | Any Status | 0.0% | No commission for admins |
| **Community Members** | Any Status | 0.0% | No selling capability |

### **💡 Business Logic**
- **Verification Incentive**: Unverified users pay 5x higher commission
- **Automatic Adjustment**: Rates change immediately after verification
- **Admin Override**: Commission rates can be manually adjusted
- **Performance Tracking**: Commission analytics và reporting

## 🔒 **Security Architecture**

### **🛡️ Multi-Layer Security**

```
┌─────────────────────────────────────────────────────────────┐
│                    Security Layers                         │
├─────────────────────────────────────────────────────────────┤
│  🔍 Layer 1: Input Validation & Sanitization               │
│  ├── File type validation                                  │
│  ├── Malicious pattern detection                           │
│  ├── Size và format restrictions                           │
│  └── CSRF protection                                       │
├─────────────────────────────────────────────────────────────┤
│  🔐 Layer 2: Data Encryption & Privacy                     │
│  ├── Field-level encryption (10 sensitive fields)          │
│  ├── GDPR/CCPA compliance                                  │
│  ├── Data anonymization                                    │
│  └── Secure deletion                                       │
├─────────────────────────────────────────────────────────────┤
│  👁️ Layer 3: Real-time Monitoring                          │
│  ├── Threat detection (4 levels)                           │
│  ├── Suspicious activity patterns                          │
│  ├── IP change detection                                   │
│  └── Unusual access alerts                                 │
├─────────────────────────────────────────────────────────────┤
│  📋 Layer 4: Audit Trail & Compliance                      │
│  ├── Comprehensive activity logging                        │
│  ├── Risk-based categorization                             │
│  ├── Compliance metrics                                    │
│  └── Long-term retention                                   │
└─────────────────────────────────────────────────────────────┘
```

### **🔐 Encrypted Fields**
- `tax_id` - Tax identification number
- `registration_number` - Business registration number
- `business_phone` - Business contact phone
- `business_email` - Business contact email
- `business_address` - Business address
- `contact_person_phone` - Contact person phone
- `contact_person_email` - Contact person email
- `bank_account_number` - Bank account information
- `identity_card_number` - ID card number
- `passport_number` - Passport number

### **⚠️ Threat Detection Levels**
- **🟢 Low**: Routine activities, normal patterns
- **🟡 Medium**: Unusual access times, minor anomalies
- **🟠 High**: Rapid actions, IP changes, suspicious uploads
- **🔴 Critical**: Security incidents, data breach attempts

## 📊 **Technical Specifications**

### **🏗️ Core Services**

| Service | Purpose | Lines of Code | Key Features |
|---------|---------|---------------|--------------|
| `UnifiedMarketplacePermissionService` | Permission management | 400+ | Caching, verification integration |
| `DocumentVerificationService` | Manual document review | 300+ | Security scanning, bulk operations |
| `VerificationAuditService` | Audit trail management | 300+ | Risk categorization, compliance |
| `DataEncryptionService` | Data protection | 300+ | GDPR compliance, anonymization |
| `SecurityMonitoringService` | Threat detection | 300+ | Real-time monitoring, alerts |

### **🗃️ Database Schema**

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `business_verification_applications` | Main application data | `status`, `business_name`, `application_type` |
| `business_verification_documents` | Document storage | `document_type`, `verification_status`, `file_path` |
| `business_verification_audit_trail` | Activity logging | `action_type`, `performed_by`, `metadata` |
| `business_verification_templates` | Email templates | `template_type`, `subject`, `content` |

### **🎨 Frontend Components**

| Component | Purpose | Technology |
|-----------|---------|------------|
| Registration Wizard | Multi-step form | Bootstrap 5, Vanilla JS |
| Document Upload | File upload interface | Drag & drop, AJAX |
| Admin Dashboard | Verification management | Professional UI, charts |
| Compliance Dashboard | Security monitoring | Real-time updates, alerts |

## 📋 **Workflow Documentation**

### **🔄 Business Registration Workflow**

```
1. User Registration
   ├── Step 1: Account Information
   ├── Step 2: Business Details
   ├── Step 3: Document Upload
   └── Step 4: Review & Submit

2. Document Verification
   ├── Security Scan
   ├── Admin Assignment
   ├── Manual Review
   └── Approval/Rejection

3. Permission Activation
   ├── Role Upgrade
   ├── Commission Rate Update
   ├── Marketplace Access
   └── Notification Sent
```

### **👨‍💼 Admin Review Process**

```
1. Application Received
   ├── Auto-assignment to reviewer
   ├── Security scan results
   └── Verification checklist

2. Document Review
   ├── Manual verification
   ├── Checklist completion
   ├── Notes và comments
   └── Decision making

3. Final Action
   ├── Approve → Role upgrade
   ├── Reject → Notification sent
   ├── Request info → User notified
   └── Audit trail updated
```

## 📖 **Quick Links**

- [🚀 Installation Guide](installation-guide.md)
- [👤 User Manual](user-manual.md)
- [👨‍💼 Admin Guide](admin-guide.md)
- [🔧 API Documentation](api-documentation.md)
- [🔒 Security Guide](security-guide.md)
- [🧪 Testing Guide](testing-guide.md)
- [📊 Analytics Guide](analytics-guide.md)
- [🚀 Deployment Guide](deployment-guide.md)

---

**© 2025 MechaMap. Enterprise Business Verification Platform.**
