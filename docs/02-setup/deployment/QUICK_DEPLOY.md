# 🚀 **QUICK DEPLOYMENT CHECKLIST**
*Deploy to mechamap.com - Run these commands after upload*

---

## 📋 **IMMEDIATE ACTIONS AFTER UPLOAD**

### **1. Cache Management** ⚡
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **2. Storage & Permissions** 🔧
```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### **3. Generate New App Key** 🔑
```bash
# Generate production key
php artisan key:generate --force
```

---

## **3️⃣ Optional Database Operations** 🗄️

> **⚠️ Chỉ thực hiện khi cần thiết - Người dùng đã yêu cầu bỏ qua**

<details>
<summary>Click để xem các lệnh database (OPTIONAL)</summary>

### **Database Migration** (Optional)
```bash
# Chỉ chạy nếu database chưa có tables
php artisan migrate --force
```

### **Import Production Data** (Optional) 
```bash
# Chỉ chạy nếu cần import dữ liệu từ file SQL
mysql -u mechamap_com -p mechamap_com < data_v2_fixed.sql
```

### **Run Seeders** (Optional)
```bash
# Chỉ chạy nếu cần dữ liệu mẫu
php artisan db:seed --force
```

</details>

---

## ✅ **VERIFICATION CHECKLIST**

- [ ] Database connection works
- [ ] Migrations completed successfully  
- [ ] Storage link created
- [ ] Cache cleared and rebuilt
- [ ] Site loads without errors
- [ ] HTTPS certificate working
- [ ] File uploads functional
- [ ] Email sending works

---

## 🔧 **PRODUCTION CONFIG SUMMARY**

**Database**: 
- Host: 127.0.0.1
- DB: mechamap  
- User: root
- Pass: eox_NUMEZB9g

**Domain**: https://mechamap.com
**Environment**: Production  
**Debug**: OFF ✅
**SSL**: Required ✅
**Cache**: Enabled ✅

---

**Status**: 🟢 Ready for Upload & Deploy
**Next**: Upload → Run Commands → Go Live! 🚀
