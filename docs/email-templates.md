# 📧 MechaMap Email Template System

## 🎨 **Overview**

MechaMap sử dụng một hệ thống email template chuyên nghiệp với design đẹp, responsive và có thể tái sử dụng. Tất cả email đều tuân theo brand identity của MechaMap với màu sắc và typography nhất quán.

## 📁 **File Structure**

```
resources/views/emails/
├── layouts/
│   └── base.blade.php              # Base template với header/footer
├── auth/
│   ├── verify-email.blade.php      # Email xác minh tài khoản
│   ├── welcome.blade.php           # Email chào mừng
│   └── reset-password.blade.php    # Email đặt lại mật khẩu
├── business/
│   └── verification-status.blade.php # Email xác minh doanh nghiệp
└── notifications/
    └── general.blade.php           # Template email thông báo chung

app/Mail/
├── Auth/
│   ├── VerifyEmailMail.php         # Mail class cho email verification
│   └── WelcomeMail.php             # Mail class cho welcome email
└── Business/
    └── VerificationStatusMail.php  # Mail class cho business verification

app/Notifications/
└── CustomVerifyEmail.php           # Custom notification override Laravel default

app/Listeners/
└── SendWelcomeEmail.php            # Event listener gửi welcome email
```

## 🎨 **Design System**

### **Colors**
- **Primary**: `#3498db` (Blue)
- **Secondary**: `#2c3e50` (Dark Blue)
- **Success**: `#27ae60` (Green)
- **Warning**: `#f39c12` (Orange)
- **Text**: `#333333` (Dark Gray)
- **Background**: `#f8f9fa` (Light Gray)

### **Typography**
- **Font Family**: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
- **Line Height**: 1.6
- **Headings**: Bold, với màu `#2c3e50`
- **Body Text**: Regular, với màu `#555555`

### **Components**
- **Buttons**: Gradient background với hover effects
- **Info Boxes**: Border-left accent với background tương ứng
- **Features Grid**: 3-column layout với icons
- **Footer**: Dark background với social links

## 📧 **Available Templates**

### **1. Email Verification (`verify-email.blade.php`)**
- **Purpose**: Xác minh email sau khi đăng ký
- **Features**: 
  - Community stats động
  - Security information
  - Time expiration warning
  - Fallback link
- **Usage**: Tự động gửi khi user đăng ký

### **2. Welcome Email (`welcome.blade.php`)**
- **Purpose**: Chào mừng user sau khi verify email
- **Features**:
  - Role-based content
  - Next steps guidance
  - Usage tips
  - Support information
- **Usage**: Tự động gửi sau khi verify email thành công

### **3. Business Verification (`verification-status.blade.php`)**
- **Purpose**: Thông báo trạng thái xác minh doanh nghiệp
- **Statuses**:
  - `approved`: Xác minh thành công
  - `rejected`: Bị từ chối với lý do
  - `pending`: Đang xử lý
- **Features**: Business info summary, next actions

### **4. Password Reset (`reset-password.blade.php`)**
- **Purpose**: Đặt lại mật khẩu
- **Features**:
  - Security guidelines
  - Time expiration
  - Safety warnings
- **Usage**: Khi user request password reset

### **5. General Notifications (`general.blade.php`)**
- **Purpose**: Template chung cho các thông báo khác
- **Features**:
  - Flexible content structure
  - Optional action buttons
  - Related items section
  - Unsubscribe option

## 🔧 **Usage Examples**

### **Sending Email Verification**
```php
use App\Mail\Auth\VerifyEmailMail;
use Illuminate\Support\Facades\Mail;

$user = User::find(1);
$verificationUrl = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
    'id' => $user->id,
    'hash' => sha1($user->email)
]);
$stats = ['users' => '64+', 'discussions' => '118+', 'showcases' => '25+'];

Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl, $stats));
```

### **Sending Welcome Email**
```php
use App\Mail\Auth\WelcomeMail;

$user = User::find(1);
Mail::to($user->email)->send(new WelcomeMail($user));
```

### **Sending Business Verification Status**
```php
use App\Mail\Business\VerificationStatusMail;

$user = User::find(1);
$businessInfo = [
    'company_name' => 'Công ty ABC',
    'tax_code' => '0123456789'
];

// Approved
Mail::to($user->email)->send(new VerificationStatusMail($user, 'approved', $businessInfo));

// Rejected
$rejectionReason = 'Thông tin không chính xác';
Mail::to($user->email)->send(new VerificationStatusMail($user, 'rejected', $businessInfo, $rejectionReason));
```

## 🧪 **Testing**

### **Development Routes**
Trong môi trường development, có thể test email templates qua các routes:

```
/test-email/verify                  # Preview email verification
/test-email/welcome                 # Preview welcome email
/test-email/business-approved       # Preview business approved
/test-email/business-rejected       # Preview business rejected
/test-email/business-pending        # Preview business pending

/test-email/send/verify            # Send actual test email
/test-email/send/welcome           # Send actual welcome email
/test-email/send/business-approved # Send actual business email
```

### **Email Configuration**
Đảm bảo `.env` có cấu hình email đúng:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@mechamap.com"
MAIL_FROM_NAME="MechaMap"
```

## 🔄 **Event System**

### **Automatic Email Sending**
- **Registration**: `CustomVerifyEmail` notification được gửi tự động
- **Email Verified**: `SendWelcomeEmail` listener được trigger
- **Business Status**: Manual sending qua admin panel

### **Event Listeners Registration**
Trong `AppServiceProvider::boot()`:
```php
Event::listen(Verified::class, SendWelcomeEmail::class);
```

## 📱 **Responsive Design**

Tất cả email templates đều responsive với:
- **Desktop**: Full width layout với sidebar
- **Mobile**: Single column, larger buttons
- **Email Clients**: Tested với Gmail, Outlook, Apple Mail

## 🔒 **Security Features**

- **Signed URLs**: Verification links có signature bảo mật
- **Time Expiration**: Links tự động hết hạn sau 60 phút
- **CSRF Protection**: Tất cả forms được bảo vệ
- **Rate Limiting**: Giới hạn số lần gửi email

## 🎯 **Best Practices**

1. **Always use Mail classes** thay vì raw Blade views
2. **Queue emails** cho performance tốt hơn
3. **Test thoroughly** trên nhiều email clients
4. **Keep content concise** và actionable
5. **Include fallback options** cho accessibility
6. **Monitor delivery rates** và spam scores

## 🚀 **Future Enhancements**

- [ ] Email templates cho marketplace notifications
- [ ] Newsletter templates
- [ ] Automated email sequences
- [ ] A/B testing framework
- [ ] Email analytics dashboard
- [ ] Multi-language support
- [ ] Dark mode email templates
