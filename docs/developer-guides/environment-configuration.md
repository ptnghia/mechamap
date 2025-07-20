# 🔧 Environment Configuration Guide

## 📋 Tổng quan

MechaMap sử dụng Laravel environment configuration để quản lý cấu hình cho các môi trường khác nhau (development, production).

## 📁 File Environment

### ✅ **File hiện có:**

#### **`.env`** - Development Environment
- **Mục đích**: Cấu hình cho môi trường development/local
- **Bảo mật**: ❌ **KHÔNG** commit vào Git
- **Nội dung**: Chứa thông tin nhạy cảm (APP_KEY, JWT_SECRET, database credentials)

#### **`.env.example`** - Environment Template
- **Mục đích**: Template cho production deployment
- **Bảo mật**: ✅ **AN TOÀN** để commit vào Git
- **Nội dung**: Chỉ chứa placeholder values, không có thông tin nhạy cảm

## 🔒 Bảo mật Environment

### **Thông tin nhạy cảm được bảo vệ:**
- `APP_KEY` - Laravel application key
- `JWT_SECRET` - WebSocket authentication secret
- `DB_PASSWORD` - Database password
- `MAIL_PASSWORD` - Email password
- `GOOGLE_CLIENT_SECRET` - Google OAuth secret
- `FACEBOOK_CLIENT_SECRET` - Facebook OAuth secret
- `AWS_SECRET_ACCESS_KEY` - AWS secret key

### **Cấu hình .gitignore:**
```gitignore
# Environment Files
.env
.env.local
.env.*.local
.env.backup

# Allow only template
!.env.example
```

## 🚀 Production Deployment

### **Bước 1: Copy template**
```bash
cp .env.example .env.production
```

### **Bước 2: Cấu hình production values**
```bash
# Cập nhật các giá trị sau trong .env.production:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mechamap.com

# Generate APP_KEY
php artisan key:generate --env=production

# Cấu hình database
DB_DATABASE=mechamap_production
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# Cấu hình mail
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password

# Cấu hình social login
GOOGLE_CLIENT_SECRET=your_google_secret
FACEBOOK_CLIENT_SECRET=your_facebook_secret
```

### **Bước 3: Bảo mật production file**
```bash
# Set proper permissions
chmod 600 .env.production

# Đảm bảo không commit
echo ".env.production" >> .gitignore
```

## ⚙️ Environment Variables quan trọng

### **Application Settings**
- `APP_NAME` - Tên ứng dụng
- `APP_ENV` - Môi trường (local/production)
- `APP_KEY` - Laravel encryption key
- `APP_DEBUG` - Debug mode (true/false)
- `APP_URL` - URL chính của ứng dụng

### **Database Configuration**
- `DB_CONNECTION` - Database driver (mysql)
- `DB_HOST` - Database host
- `DB_PORT` - Database port (3306)
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

### **WebSocket Configuration**
- `JWT_SECRET` - JWT secret cho WebSocket authentication
- `FRONTEND_URL` - URL frontend cho CORS

### **Cache & Session**
- `CACHE_DRIVER` - Cache driver (file/redis)
- `SESSION_DRIVER` - Session driver (database)
- `QUEUE_CONNECTION` - Queue driver (database)

## 🔄 Migration từ file cũ

Nếu bạn có file `.env` cũ, hãy:

1. **Backup file hiện tại**
2. **So sánh với `.env.example`** để tìm missing configurations
3. **Cập nhật các cấu hình mới** (CDN, JWT, Asset versioning)
4. **Test thoroughly** trước khi deploy

## 🚨 Lưu ý bảo mật

### **❌ KHÔNG BAO GIỜ:**
- Commit file `.env` vào Git
- Share file `.env` qua email/chat
- Để file `.env` có permission 777
- Hardcode secrets trong code

### **✅ LUÔN LUÔN:**
- Sử dụng `.env.example` làm template
- Set proper file permissions (600)
- Rotate secrets định kỳ
- Sử dụng strong passwords

## 📞 Hỗ trợ

Nếu gặp vấn đề với environment configuration:
1. Kiểm tra [troubleshooting guide](../troubleshooting/)
2. Xem [deployment documentation](../deployment/)
3. Liên hệ development team

---

*📅 Cập nhật: Tháng 7, 2025*
