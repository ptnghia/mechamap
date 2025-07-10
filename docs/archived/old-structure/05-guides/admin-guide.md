# 🛡️ MechaMap Admin Guide

**Administrative Panel Documentation**  
**Target Audience**: Administrators & Moderators  
**System**: MechaMap v1.0  

---

## 🚀 **ADMIN DASHBOARD OVERVIEW**

### **🔑 Access Levels**

#### **👑 Admin (Full Access)**
- **User Management**: Create/edit/delete all users
- **Content Management**: Full content control
- **System Settings**: Modify system configuration
- **Reports & Analytics**: Access all reports
- **Permissions**: Manage roles and permissions

#### **🛡️ Moderator (Content Focus)**
- **Content Moderation**: Review and moderate content
- **User Management**: Ban/unban regular users
- **Reports**: Handle user reports and complaints
- **Forum Management**: Manage categories and threads
- **Limited Settings**: Basic content settings only

### **📊 Dashboard Statistics**

#### **User Statistics Panel**
```
👥 Total Users: 1,234
📈 New Today: 12
🟢 Online Now: 45
🔒 Banned: 3
👑 Admins: 2
🛡️ Moderators: 5
```

#### **Content Statistics Panel**
```
📝 Total Threads: 5,678
💬 Total Comments: 15,432
📊 Pending Moderation: 8
🚩 Reported Content: 3
⭐ Average Rating: 4.2
```

#### **Commerce Statistics Panel**
```
🛒 Total Orders: 234
💰 Revenue This Month: 12,450,000 VND
📥 Downloads Today: 67
🎯 Conversion Rate: 3.2%
```

---

## 👥 **USER MANAGEMENT**

### **🔍 User Search & Filtering**

#### **Advanced Search Options**
1. **Basic Search**: Name, username, email
2. **Role Filter**: Admin, Moderator, Senior, Member, Guest
3. **Status Filter**: Active, Inactive, Banned
4. **Registration Date**: Date range picker
5. **Activity Filter**: Online, Last seen, Never logged in

#### **Bulk Operations**
```
✅ Activate multiple users
❌ Deactivate multiple users
🚫 Ban multiple users
📧 Send email to selected users
📊 Export user data
🗑️ Delete inactive accounts
```

### **👤 Individual User Management**

#### **Edit User Profile**
```
📝 Basic Information:
   - Name, Username, Email
   - Role assignment
   - Status (Active/Inactive/Banned)
   - Avatar upload

🔐 Security Settings:
   - Reset password
   - Force password change
   - Email verification status
   - Last login tracking

📊 Statistics:
   - Threads created: 15
   - Comments posted: 87
   - Average rating received: 4.1
   - Join date: March 15, 2024
```

#### **Ban User Process**
1. **Select User** → **Actions** → **Ban User**
2. **Ban Duration**:
   - Temporary: 1 day, 1 week, 1 month
   - Permanent: Indefinite ban
3. **Ban Reason**: Required field for audit
4. **Notify User**: Send ban notification email
5. **Ban Appeals**: Users can appeal through support

### **🔧 Role & Permission Management**

#### **Default Roles & Permissions**

**👑 Admin Permissions:**
```
✅ All system permissions
✅ User management (all levels)
✅ System settings
✅ Database access
✅ Backup & restore
✅ Server logs
✅ Financial reports
```

**🛡️ Moderator Permissions:**
```
✅ Content moderation
✅ User management (Member/Guest only)
✅ Report handling
✅ Category management
✅ Basic statistics
❌ System settings
❌ Admin management
❌ Financial data
```

#### **Custom Permission Assignment**
1. **User Profile** → **Permissions Tab**
2. **Select Permissions**:
   - View Dashboard
   - Manage Users
   - Moderate Content
   - Handle Reports
   - Manage Categories
   - View Statistics
   - System Settings
3. **Save & Apply**

---

## 📝 **CONTENT MODERATION**

### **🔍 Content Review Dashboard**

#### **Pending Content Queue**
```
📋 Pending Review:
   📝 New Threads: 5
   💬 New Comments: 12
   🚩 Reported Content: 3
   ⚠️ Auto-flagged: 2

🕐 Review Timeline:
   - High Priority: < 2 hours
   - Normal: < 24 hours
   - Low Priority: < 72 hours
```

#### **Moderation Actions**
```
✅ Approve: Publish content
❌ Reject: Remove content
🚩 Flag: Mark for further review
✏️ Edit: Modify content before approval
📧 Contact Author: Request clarification
🗑️ Delete: Permanent removal
```

### **📊 Content Quality Management**

#### **Quality Scoring System**
```
🌟 5 Stars: Exceptional quality, featured content
⭐ 4 Stars: High quality, valuable contribution
⭐ 3 Stars: Good quality, standard content
⭐ 2 Stars: Below average, needs improvement
⭐ 1 Star: Poor quality, consider removal
```

#### **Auto-Moderation Rules**
```
🤖 Automatic Flagging:
   - Spam keywords detected
   - Too many external links
   - Duplicate content
   - Inappropriate language
   - Suspicious user behavior

🔍 Manual Review Required:
   - First posts from new users
   - Content with attachments
   - Posts with multiple reports
   - High-value commercial content
```

### **🚨 Report Management**

#### **Report Categories**
```
📊 Spam: Unwanted promotional content
🔞 Inappropriate: Offensive or adult content
📋 Off-topic: Not related to mechanical engineering
⚖️ Copyright: Potential copyright violation
👤 Personal Attack: Harassment or abuse
🔗 Broken Links: Dead or malicious links
🚫 Other: Custom reason provided
```

#### **Report Resolution Process**
1. **Review Report**: Read report details and evidence
2. **Investigate Content**: Check reported thread/comment
3. **Contact Parties**: Message reporter and/or author if needed
4. **Take Action**: 
   - Dismiss report (no action needed)
   - Edit content
   - Remove content
   - Ban user
   - Issue warning
5. **Document Decision**: Add resolution notes
6. **Notify Parties**: Send outcome notifications

---

## 🛒 **MARKETPLACE MANAGEMENT**

### **📦 Product Management**

#### **Product Approval Workflow**
```
📝 Submission → 🔍 Review → ✅ Approve/❌ Reject
```

**Review Checklist:**
```
📋 Content Quality:
   ✅ Clear product description
   ✅ Accurate technical specifications
   ✅ Proper file format
   ✅ Complete documentation
   ✅ Original content (no copyright issues)

💰 Pricing Review:
   ✅ Market-appropriate pricing
   ✅ Value matches quality
   ✅ No predatory pricing
   ✅ Proper currency/format

🔐 Security Check:
   ✅ File integrity verified
   ✅ No malicious content
   ✅ Proper file permissions
   ✅ Virus scan passed
```

#### **Product Categories Management**
1. **Categories** → **Product Categories**
2. **Add New Category**:
   - Category Name
   - Description
   - Icon/Image
   - Sort Order
   - Active/Inactive
3. **Manage Hierarchy**: Parent/child relationships
4. **SEO Settings**: Meta descriptions, keywords

### **💳 Order & Payment Management**

#### **Order Dashboard**
```
📊 Today's Orders:
   💰 Total Revenue: 2,340,000 VND
   📦 Orders Completed: 23
   ⏳ Orders Pending: 5
   ❌ Orders Failed: 2
   💸 Refunds Processed: 1
```

#### **Payment Gateway Management**
```
🏦 VNPay Configuration:
   ✅ Merchant ID: VNPAY123456
   ✅ Hash Secret: Configured
   ✅ Test Mode: Disabled
   ✅ Webhook URL: Active

💳 Stripe Configuration:
   ✅ Public Key: pk_live_...
   ✅ Secret Key: sk_live_... (Hidden)
   ✅ Webhook Endpoint: Active
   ✅ Currency: VND, USD
```

#### **Refund Processing**
1. **Orders** → **Find Order** → **Refund**
2. **Refund Type**:
   - Full Refund: 100% amount
   - Partial Refund: Custom amount
3. **Refund Reason**: Required documentation
4. **Payment Gateway**: Auto-process through original method
5. **Notify Customer**: Send refund confirmation email

---

## ⚙️ **SYSTEM SETTINGS**

### **🌐 General Settings**

#### **Site Configuration**
```
🏢 Site Information:
   - Site Name: MechaMap
   - Site Description: Cộng đồng Kỹ thuật Cơ khí
   - Contact Email: admin@mechamap.com
   - Support Phone: 024.xxxx.xxxx
   - Business Address: [Địa chỉ công ty]

🌍 Localization:
   - Default Language: Vietnamese
   - Timezone: Asia/Ho_Chi_Minh
   - Date Format: dd/mm/yyyy
   - Currency: VND
   - Number Format: 1.234.567,89
```

#### **SEO Configuration**
```
🔍 SEO Settings:
   - Meta Title Template: %title% | MechaMap
   - Meta Description: Default site description
   - Keywords: kỹ thuật cơ khí, CAD, CNC, forum
   - Google Analytics: UA-XXXXXXXX-X
   - Google Search Console: Verified
   - Sitemap: Auto-generated (/sitemap.xml)
```

### **📧 Email Configuration**

#### **SMTP Settings**
```
📧 Email Configuration:
   - SMTP Host: smtp.gmail.com
   - SMTP Port: 587
   - Encryption: TLS
   - Username: noreply@mechamap.com
   - Password: [Hidden]
   - From Name: MechaMap System
   - From Email: noreply@mechamap.com
```

#### **Email Templates**
```
📨 System Emails:
   ✅ Welcome Email: New user registration
   ✅ Email Verification: Account activation
   ✅ Password Reset: Forgot password
   ✅ Order Confirmation: Purchase success
   ✅ Download Notification: File ready
   ✅ Ban Notification: Account suspended
   ✅ Report Update: Moderation decisions
```

### **🔐 Security Settings**

#### **Security Configuration**
```
🛡️ Security Options:
   - Password Min Length: 8 characters
   - Require Email Verification: Yes
   - Two-Factor Authentication: Available
   - Session Timeout: 24 hours
   - Max Login Attempts: 5
   - Account Lockout Duration: 30 minutes
   - IP Whitelist: Admin access only
```

#### **File Upload Security**
```
📁 Upload Settings:
   - Max File Size: 10MB
   - Allowed Types: PDF, DOC, DWG, STEP, IGES
   - Virus Scanning: Enabled
   - File Quarantine: 24 hours
   - Admin Approval: Required for large files
```

---

## 📊 **REPORTS & ANALYTICS**

### **📈 User Analytics**

#### **User Growth Report**
```
📊 Monthly User Statistics:
   - New Registrations: 234 (+15% from last month)
   - Active Users (30 days): 1,456 (78% retention)
   - User Engagement Score: 8.2/10
   - Average Session Duration: 12 minutes
   - Pages per Session: 4.7
```

#### **User Behavior Analysis**
```
🔍 User Activity Patterns:
   - Peak Hours: 9:00-11:00, 14:00-17:00
   - Most Active Days: Tuesday, Wednesday
   - Popular Content: CAD tutorials, CNC programming
   - Search Terms: "solidworks", "cnc programming", "vật liệu"
   - Exit Pages: Download pages (expected)
```

### **💰 Revenue Analytics**

#### **Financial Dashboard**
```
💰 Revenue Summary (This Month):
   - Total Revenue: 45,670,000 VND
   - Number of Orders: 234
   - Average Order Value: 195,170 VND
   - Conversion Rate: 3.2%
   - Refund Rate: 1.8%

📊 Top Products:
   1. CAD Block Library - 2,340,000 VND
   2. CNC Programming Guide - 1,890,000 VND
   3. Material Properties Database - 1,560,000 VND
```

#### **Payment Method Analysis**
```
💳 Payment Methods:
   - VNPay: 67% (preferred by Vietnamese users)
   - Stripe: 33% (international users)
   - Success Rate: 94.2%
   - Failed Payments: 5.8%
   - Average Processing Time: 2.3 seconds
```

### **📋 Content Analytics**

#### **Forum Performance**
```
📝 Content Statistics:
   - Total Threads: 5,678
   - Total Comments: 23,456
   - Daily New Content: 15-20 threads
   - Most Active Category: CAD/CAM Software
   - Average Thread Rating: 4.2/5
   - Moderation Queue: 3-5 items daily
```

#### **Download Analytics**
```
📥 Download Performance:
   - Total Downloads: 12,345
   - Successful Downloads: 98.7%
   - Failed Downloads: 1.3%
   - Peak Download Hours: 14:00-16:00
   - Average Download Time: 45 seconds
   - Bandwidth Usage: 2.3 TB/month
```

---

## 🔧 **MAINTENANCE TASKS**

### **📅 Daily Tasks**

#### **Morning Checklist (9:00 AM)**
```
✅ Check moderation queue
✅ Review overnight registrations  
✅ Check payment failures
✅ Monitor server resources
✅ Review security logs
✅ Process refund requests
```

#### **Evening Review (17:00 PM)**
```
✅ Daily statistics summary
✅ Backup status verification
✅ Outstanding issues review
✅ Next day preparation
✅ User feedback review
```

### **📅 Weekly Tasks**

#### **Monday - Weekly Planning**
```
📋 Weekly Tasks:
✅ System backup verification
✅ Security updates check
✅ User growth analysis
✅ Content quality review
✅ Revenue report generation
✅ Feature requests review
```

#### **Friday - Week Wrap-up**
```
📊 Weekly Report:
✅ User statistics summary
✅ Revenue vs targets
✅ System performance metrics
✅ Top issues resolved
✅ Next week priorities
```

### **📅 Monthly Tasks**

#### **System Maintenance**
```
🔧 Monthly System Tasks:
✅ Database optimization
✅ Log file cleanup
✅ Inactive user cleanup
✅ File storage review
✅ Performance tuning
✅ Security audit
```

#### **Business Review**
```
📈 Monthly Business Review:
✅ Revenue analysis
✅ User growth trends
✅ Content quality metrics
✅ Customer satisfaction survey
✅ Competitor analysis
✅ Feature roadmap update
```

---

## 🚨 **EMERGENCY PROCEDURES**

### **🔥 Site Down Emergency**

#### **Immediate Response (0-5 minutes)**
1. **Check Server Status**: Hosting provider dashboard
2. **Verify DNS**: Domain resolution working?
3. **Check Database**: MySQL connection active?
4. **Review Logs**: Recent error messages
5. **Notify Team**: Admin group chat/email

#### **Investigation (5-15 minutes)**
1. **Server Resources**: CPU, RAM, Disk usage
2. **Network Connectivity**: Ping tests, traceroute
3. **Service Status**: Nginx, PHP-FPM, MySQL
4. **Recent Changes**: Deploy logs, config changes
5. **External Issues**: CDN, payment gateways

#### **Recovery Actions**
```
🔧 Common Solutions:
✅ Restart web services
✅ Clear application cache
✅ Restart database
✅ Check disk space
✅ Review recent deployments
✅ Contact hosting support
```

### **🛡️ Security Incident Response**

#### **Suspected Attack**
1. **Immediate**: Block suspicious IPs
2. **Assessment**: Check access logs
3. **Containment**: Limit access if needed
4. **Investigation**: Full security audit
5. **Recovery**: Fix vulnerabilities
6. **Documentation**: Incident report

#### **Data Breach Protocol**
1. **Immediate Containment**: Stop the breach
2. **Assessment**: What data was accessed?
3. **Legal Notification**: GDPR/privacy law compliance
4. **User Communication**: Transparent notification
5. **Recovery**: Secure systems and data
6. **Review**: Update security measures

---

## 📞 **SUPPORT & ESCALATION**

### **📧 Internal Support Chain**

```
🆘 Escalation Levels:
Level 1: Community Moderators
   → Forum issues, basic user support
   
Level 2: System Administrators
   → Technical issues, user management
   
Level 3: Technical Director
   → System failures, security incidents
   
Level 4: External Support
   → Hosting provider, payment gateways
```

### **📋 Contact Information**

```
🏢 Internal Contacts:
👑 Lead Admin: admin@mechamap.com
🛡️ Moderator Team: moderator@mechamap.com
🔧 Technical Support: tech@mechamap.com
💰 Finance Team: finance@mechamap.com

🌐 External Contacts:
🏢 Hosting Provider: support@digitalocean.com
💳 Payment Support: 
   - VNPay: support@vnpay.vn
   - Stripe: support@stripe.com
🔐 Security: security@mechamap.com
```

---

**📚 Additional Resources:**
- [Technical Documentation](../07-developer/)
- [Database Guide](../02-database/)
- [Deployment Guide](../08-deployment/)
- [User Guide](../05-user-guides/)

**🚀 Happy Administrating!**
