# CDN Asset URLs Fix - Troubleshooting Guide

## 🚨 **Vấn đề**

Khi deploy production, các file CSS/JS/Images đang load từ `https://cdn.mechamap.com/` thay vì từ VPS local, gây ra lỗi 404 hoặc assets không load được.

## 🔍 **Nguyên nhân**

### **1. Cấu hình CDN mặc định**
File `config/production.php` có cấu hình CDN mặc định:
```php
'cdn' => env('CDN_URL', 'https://cdn.mechamap.com'),
```

### **2. AppServiceProvider tự động set CDN**
File `app/Providers/AppServiceProvider.php` tự động set asset URL thành CDN:
```php
if ($cdnUrl = config('production.domain.cdn')) {
    config(['app.asset_url' => $cdnUrl]);
}
```

### **3. Production domain detection**
Khi `APP_URL` chứa `mechamap.com`, hệ thống tự động kích hoạt production settings.

## ✅ **Giải pháp**

### **Bước 1: Cấu hình Environment Variables**

Thêm vào file `.env` hoặc `.env.production`:
```bash
# Asset Configuration - Disable CDN for local/VPS deployment
CDN_URL=
CDN_ENABLED=false
```

### **Bước 2: Cập nhật Production Template**

File `.env.production.template` đã được cập nhật:
```bash
# Asset Configuration - Set to empty to use local assets instead of CDN
CDN_URL=
CDN_ENABLED=false
```

### **Bước 3: Clear Cache**

```bash
php artisan config:clear
php artisan cache:clear
```

### **Bước 4: Restart Web Server**

```bash
# Nginx
sudo systemctl restart nginx

# Apache
sudo systemctl restart apache2

# PM2 (nếu dùng)
pm2 restart all
```

## 🧪 **Kiểm tra**

### **Test Script**
```bash
php scripts/test_asset_urls.php
```

### **Manual Test**
```bash
php artisan tinker --execute="echo asset('css/main.css');"
```

**Kết quả mong đợi:**
- ✅ `https://your-domain.com/css/main.css`
- ❌ `https://cdn.mechamap.com/css/main.css`

## 🔧 **Cấu hình nâng cao**

### **Cho VPS riêng (không CDN)**
```bash
# .env
CDN_URL=
CDN_ENABLED=false
ASSET_VERSIONING_ENABLED=true
```

### **Cho CDN thực (nếu có)**
```bash
# .env
CDN_URL=https://your-actual-cdn.com
CDN_ENABLED=true
ASSET_VERSIONING_ENABLED=true
```

### **Cho shared hosting**
```bash
# .env
CDN_URL=
CDN_ENABLED=false
ASSET_VERSIONING_ENABLED=false
```

## 📋 **Checklist Deployment**

- [ ] Set `CDN_ENABLED=false` trong `.env`
- [ ] Set `CDN_URL=` (empty) trong `.env`
- [ ] Run `php artisan config:clear`
- [ ] Test asset URLs với script
- [ ] Restart web server
- [ ] Verify assets load correctly trong browser

## 🚨 **Troubleshooting**

### **Assets vẫn load từ CDN**

1. **Kiểm tra environment:**
   ```bash
   php artisan tinker --execute="echo env('CDN_ENABLED');"
   ```

2. **Kiểm tra config cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Kiểm tra browser cache:**
   - Hard refresh (Ctrl+F5)
   - Clear browser cache
   - Test trong incognito mode

### **Assets không load (404)**

1. **Kiểm tra file tồn tại:**
   ```bash
   ls -la public/css/
   ls -la public/js/
   ```

2. **Kiểm tra permissions:**
   ```bash
   chmod -R 644 public/css/
   chmod -R 644 public/js/
   ```

3. **Kiểm tra web server config:**
   - Nginx: Check static file serving
   - Apache: Check .htaccess rules

### **Mixed content errors (HTTP/HTTPS)**

1. **Force HTTPS:**
   ```bash
   # .env
   FORCE_HTTPS=true
   APP_URL=https://your-domain.com
   ```

2. **Check SSL certificate:**
   ```bash
   curl -I https://your-domain.com/css/main.css
   ```

## 📚 **Tài liệu liên quan**

- [Production Deployment Guide](../deployment/PRODUCTION_DEPLOYMENT_README.md)
- [Asset Versioning Configuration](../developer-guides/asset-versioning.md)
- [Environment Configuration](../deployment/environment-configuration.md)

## 🎯 **Kết luận**

Sau khi thực hiện các bước trên, assets sẽ được serve từ VPS local thay vì CDN, giải quyết vấn đề 404 và loading errors.

**Test cuối cùng:**
```bash
# Should return local URL, not CDN
curl -I https://your-domain.com/css/main.css
```
