# 👨‍💼 Admin Guide - Business Verification Platform

**Hướng dẫn quản trị hệ thống xác thực doanh nghiệp MechaMap**

[![Admin Guide](https://img.shields.io/badge/Guide-Administrator-red.svg)](README.md)
[![Security Level](https://img.shields.io/badge/Security-High%20Privilege-orange.svg)](#security)
[![Manual Review](https://img.shields.io/badge/Review-Manual%20Process-blue.svg)](#manual-review)

---

## 🎯 **Tổng quan hệ thống Admin**

Hệ thống quản trị Business Verification được thiết kế để admin có thể quản lý hiệu quả việc xác thực doanh nghiệp với manual review workflow, đảm bảo chất lượng và bảo mật cao.

### **🔑 Quyền truy cập Admin**

| Role | Permissions | Access Level |
|------|-------------|--------------|
| **Super Admin** | Full system access | Level 1 |
| **System Admin** | Technical management | Level 2 |
| **Content Admin** | Content & verification | Level 3 |
| **Marketplace Moderator** | Marketplace management | Level 5 |

### **🏗️ Admin Dashboard Structure**

```
📊 Admin Dashboard
├── 🏢 Business Verification
│   ├── Applications Management
│   ├── Document Review
│   ├── Bulk Operations
│   └── Verification Reports
├── 💰 Commission Rates
│   ├── Rate Management
│   ├── User-specific Rates
│   └── Analytics
├── 🔒 Security & Compliance
│   ├── Security Monitoring
│   ├── Audit Trail
│   ├── Privacy Management
│   └── Compliance Reports
└── 👥 User Management
    ├── Role Management
    ├── Permission Control
    └── Account Actions
```

## 🏢 **Business Verification Management**

### **📋 Applications Dashboard**

**Truy cập**: `Admin Panel > Business Verification > Applications`

#### **📊 Overview Statistics**
- **Total Applications**: Tổng số đơn xác thực
- **Pending Review**: Đơn chờ xem xét
- **Under Review**: Đơn đang xử lý
- **Approved This Month**: Đơn đã duyệt trong tháng
- **Average Processing Time**: Thời gian xử lý trung bình

#### **🔍 Filter & Search Options**
```
Filters:
├── Status: All, Pending, Under Review, Approved, Rejected
├── Application Type: Manufacturer, Supplier, Brand, Verified Partner
├── Date Range: Custom date picker
├── Assigned Reviewer: Admin user dropdown
└── Priority: High, Medium, Low
```

### **📄 Document Review Process**

#### **🔹 Step 1: Application Assignment**
1. **Auto-assignment**: Hệ thống tự động phân công theo workload
2. **Manual assignment**: Admin có thể reassign applications
3. **Priority setting**: Đặt mức độ ưu tiên (High/Medium/Low)

#### **🔹 Step 2: Document Verification**

**📋 Verification Checklist cho Business License:**
- [ ] Tên doanh nghiệp khớp với thông tin đăng ký
- [ ] Số giấy phép kinh doanh hợp lệ
- [ ] Ngày cấp và ngày hết hạn rõ ràng
- [ ] Địa chỉ kinh doanh khớp với thông tin
- [ ] Ngành nghề kinh doanh phù hợp
- [ ] Chữ ký và con dấu cơ quan cấp
- [ ] Chất lượng ảnh/scan rõ nét

**📋 Verification Checklist cho Tax Certificate:**
- [ ] Mã số thuế khớp với thông tin đăng ký
- [ ] Tên doanh nghiệp chính xác
- [ ] Địa chỉ thuế khớp với giấy phép
- [ ] Trạng thái hoạt động của mã số thuế
- [ ] Ngày cấp hợp lệ
- [ ] Chữ ký và con dấu cơ quan thuế

**📋 Verification Checklist cho Identity Card:**
- [ ] Họ tên khớp với thông tin đăng ký
- [ ] Số CMND/CCCD hợp lệ
- [ ] Ảnh rõ nét, không bị che khuất
- [ ] Ngày sinh và nơi sinh rõ ràng
- [ ] Ngày cấp và nơi cấp hợp lệ
- [ ] Không có dấu hiệu giả mạo

#### **🔹 Step 3: Decision Making**

**✅ Approval Process:**
1. Complete verification checklist
2. Add approval notes
3. Set verification score (0-100)
4. Click "Approve Application"
5. System auto-upgrades user role
6. Email notification sent to user

**❌ Rejection Process:**
1. Select rejection reasons
2. Add detailed explanation
3. Specify required corrections
4. Click "Reject Application"
5. Email notification with feedback sent

**📞 Request Additional Info:**
1. Specify missing documents
2. Add clarification requests
3. Set deadline for response
4. Send notification to user

### **⚡ Bulk Operations**

#### **📊 Bulk Actions Available**
- **Bulk Approve**: Approve multiple applications
- **Bulk Reject**: Reject multiple applications with same reason
- **Bulk Assign**: Assign multiple applications to reviewer
- **Bulk Priority**: Change priority for multiple applications
- **Bulk Export**: Export application data

#### **🔒 Security Measures**
- **Confirmation dialogs** for all bulk actions
- **Audit logging** of all bulk operations
- **Permission checks** before execution
- **Rollback capability** for accidental actions

## 💰 **Commission Rate Management**

### **📊 Rate Configuration**

**Truy cập**: `Admin Panel > Commission Rates`

#### **🎯 Default Rates by Role**
```php
Default Commission Rates:
├── Manufacturer (Unverified): 10.0%
├── Manufacturer (Verified): 5.0%
├── Supplier (Unverified): 10.0%
├── Supplier (Verified): 3.0%
├── Brand (Any): 0.0%
├── Verified Partner (Unverified): 10.0%
├── Verified Partner (Verified): 2.0%
└── Admin Roles: 0.0%
```

#### **⚙️ Rate Management Features**
- **Global rate updates**: Change rates for all users of a role
- **Individual rate overrides**: Set custom rates for specific users
- **Temporary rate adjustments**: Time-limited promotional rates
- **Rate history tracking**: Complete audit trail of rate changes

### **📈 Commission Analytics**

#### **📊 Key Metrics**
- **Total commission collected** by period
- **Average commission rate** by user type
- **Revenue impact** of verification program
- **User behavior** changes after verification

#### **📋 Reports Available**
- **Commission Summary Report**: Overview by time period
- **User Commission Report**: Individual user breakdown
- **Verification Impact Report**: Before/after verification analysis
- **Revenue Projection Report**: Forecasting based on current trends

## 🔒 **Security & Compliance Management**

### **🛡️ Security Monitoring Dashboard**

**Truy cập**: `Admin Panel > Security & Compliance`

#### **⚠️ Real-time Security Alerts**
- **Failed login attempts**: Monitor brute force attacks
- **Suspicious file uploads**: Malicious content detection
- **Unusual access patterns**: After-hours or rapid actions
- **IP address changes**: Potential account compromise
- **Privilege escalation attempts**: Unauthorized access attempts

#### **📊 Security Metrics**
```
Security Dashboard:
├── 🟢 Security Score: 94.5%
├── 🔴 Critical Incidents: 0
├── 🟠 High-Risk Activities: 2
├── 🟡 Medium Alerts: 5
└── 📊 Total Monitored Events: 1,247
```

### **📋 Audit Trail Management**

#### **🔍 Audit Categories**
- **Application Activities**: Registration, submission, approval
- **Document Activities**: Upload, verification, rejection
- **Admin Activities**: All admin actions and decisions
- **Security Events**: Login attempts, suspicious activities
- **System Activities**: Automated processes and notifications

#### **📊 Audit Reports**
- **Activity Summary**: Overview of all activities
- **User Activity Report**: Specific user action history
- **Admin Action Report**: All administrative actions
- **Security Incident Report**: Security-related events
- **Compliance Report**: GDPR/CCPA compliance metrics

### **🔐 Data Privacy Management**

#### **📋 GDPR Compliance Tools**
- **Data Export**: Generate user data exports
- **Data Anonymization**: Anonymize user data for retention
- **Data Deletion**: Secure deletion of user data
- **Consent Management**: Track user consent preferences
- **Breach Notification**: Automated breach reporting

#### **⏰ Data Retention Management**
```
Retention Policies:
├── Business Verification Data: 7 years
├── Personal Data: 5 years
├── Financial Data: 10 years
├── Audit Logs: 3 years
└── Security Logs: 1 year
```

## 🔧 **System Configuration**

### **⚙️ Verification Settings**

#### **📋 Document Requirements**
```php
Required Documents by Role:
├── Manufacturer: [business_license, tax_certificate, identity_card]
├── Supplier: [business_license, tax_certificate, identity_card]
├── Brand: [business_license, tax_certificate, identity_card]
└── Verified Partner: [business_license, tax_certificate, identity_card, bank_statement]
```

#### **🔒 Security Settings**
- **File upload limits**: Max size, allowed types
- **Security scanning**: Enable/disable malware detection
- **Rate limiting**: API request limits
- **Session timeout**: Admin session duration
- **Two-factor authentication**: Enable for admin accounts

### **📧 Email Template Management**

#### **📨 Available Templates**
- **Application Received**: Confirmation email
- **Under Review**: Review started notification
- **Additional Info Required**: Request for more documents
- **Application Approved**: Approval notification
- **Application Rejected**: Rejection with feedback
- **Document Verified**: Individual document approval

#### **✏️ Template Customization**
- **Subject line editing**: Customize email subjects
- **Content editing**: Rich text editor for email body
- **Variable insertion**: Dynamic content (user name, application ID)
- **Multi-language support**: Vietnamese and English versions

## 📊 **Reporting & Analytics**

### **📈 Performance Metrics**

#### **⏱️ Processing Time Metrics**
- **Average processing time**: Overall and by application type
- **Processing time trends**: Monthly/quarterly analysis
- **Bottleneck identification**: Slowest processing steps
- **Admin performance**: Individual reviewer statistics

#### **📊 Application Statistics**
- **Application volume**: Daily/weekly/monthly submissions
- **Approval rates**: Success rates by application type
- **Rejection reasons**: Most common rejection causes
- **Resubmission rates**: Applications requiring multiple reviews

### **💼 Business Intelligence**

#### **📋 Executive Reports**
- **Monthly verification summary**: High-level overview
- **Revenue impact analysis**: Commission rate effectiveness
- **User growth analysis**: Verification program impact
- **Compliance status report**: Regulatory compliance metrics

#### **📊 Operational Reports**
- **Admin workload report**: Task distribution analysis
- **Document quality report**: Common document issues
- **Processing efficiency report**: Workflow optimization insights
- **Security incident summary**: Monthly security overview

## 🆘 **Troubleshooting & Support**

### **🔧 Common Issues**

#### **📄 Document Upload Issues**
- **File too large**: Check file size limits (10MB max)
- **Invalid format**: Ensure PDF, JPG, PNG formats
- **Upload timeout**: Check network connection
- **Security scan failure**: Review file content for malicious patterns

#### **⚙️ System Performance Issues**
- **Slow loading**: Check server resources and database performance
- **Memory errors**: Monitor PHP memory limits
- **Database timeouts**: Optimize queries and indexing
- **Cache issues**: Clear Redis cache if needed

### **📞 Escalation Procedures**

#### **🚨 Critical Issues (Immediate Response)**
- **System downtime**: Contact technical team immediately
- **Security breaches**: Activate incident response plan
- **Data corruption**: Initiate backup recovery procedures
- **Legal compliance issues**: Notify legal team

#### **⚠️ High Priority Issues (4-hour Response)**
- **Application processing errors**: Debug and resolve
- **Email delivery failures**: Check mail server status
- **Permission errors**: Review role configurations
- **Performance degradation**: Investigate and optimize

### **📋 Admin Best Practices**

#### **🔒 Security Best Practices**
- **Regular password updates**: Change passwords monthly
- **Two-factor authentication**: Always enable 2FA
- **Session management**: Log out when not in use
- **Access logging**: Monitor your own access patterns
- **Suspicious activity reporting**: Report unusual activities

#### **📊 Operational Best Practices**
- **Regular reviews**: Check pending applications daily
- **Documentation**: Maintain detailed review notes
- **Consistency**: Follow verification checklists strictly
- **Communication**: Provide clear feedback to users
- **Continuous learning**: Stay updated on verification procedures

---

**© 2025 MechaMap. Administrator Guide for Business Verification Platform.**
