# 🚀 **cPanel Deployment Configuration**

> **Moved from root** - cPanel auto-deployment configuration example

---

## 📋 **Original .cpanel.yml Content**

```yaml
---
deployment:
  tasks:
    - export DEPLOYPATH=/home/username/public_html/mechamap
    - /bin/cp -R app $DEPLOYPATH
    - /bin/cp -R bootstrap $DEPLOYPATH
    - /bin/cp -R config $DEPLOYPATH
    - /bin/cp -R database $DEPLOYPATH
    - /bin/cp -R lang $DEPLOYPATH
    - /bin/cp -R public $DEPLOYPATH
    - /bin/cp -R resources $DEPLOYPATH
    - /bin/cp -R routes $DEPLOYPATH
    - /bin/cp -R storage $DEPLOYPATH
    - /bin/cp -R .htaccess $DEPLOYPATH
    - /bin/cp -R artisan $DEPLOYPATH
    - /bin/cp -R composer.json $DEPLOYPATH
    - /bin/cp -R composer.lock $DEPLOYPATH
    - /bin/cp -R package.json $DEPLOYPATH
    - /bin/cp -R package-lock.json $DEPLOYPATH
    - /bin/cp -R postcss.config.js $DEPLOYPATH
    - /bin/cp -R tailwind.config.js $DEPLOYPATH
    - /bin/cp -R vite.config.js $DEPLOYPATH
```

---

## ⚠️ **Issues with Original Config**

### **🚫 Non-existent Files Referenced**
- `package.json` - Laravel backend doesn't use Node.js
- `package-lock.json` - Not applicable for Laravel
- `postcss.config.js` - Not present in Laravel backend
- `tailwind.config.js` - Not used in this project
- `vite.config.js` - Not used in Laravel backend

---

## ✅ **Updated cPanel Configuration**

### **For MechaMap Laravel Backend (mechamap.com)**

```yaml
---
deployment:
  tasks:
    - export DEPLOYPATH=/home/mechamap_com/public_html
    - /bin/cp -R app $DEPLOYPATH
    - /bin/cp -R bootstrap $DEPLOYPATH
    - /bin/cp -R config $DEPLOYPATH
    - /bin/cp -R database $DEPLOYPATH
    - /bin/cp -R lang $DEPLOYPATH
    - /bin/cp -R public $DEPLOYPATH
    - /bin/cp -R resources $DEPLOYPATH
    - /bin/cp -R routes $DEPLOYPATH
    - /bin/cp -R storage $DEPLOYPATH
    - /bin/cp -R vendor $DEPLOYPATH
    - /bin/cp .htaccess $DEPLOYPATH
    - /bin/cp artisan $DEPLOYPATH
    - /bin/cp composer.json $DEPLOYPATH
    - /bin/cp composer.lock $DEPLOYPATH
    - /bin/cp phpunit.xml $DEPLOYPATH
    - /bin/cp data_v2_fixed.sql $DEPLOYPATH
    # Post-deployment commands
    - cd $DEPLOYPATH && php artisan config:cache
    - cd $DEPLOYPATH && php artisan route:cache
    - cd $DEPLOYPATH && php artisan view:cache
    - cd $DEPLOYPATH && php artisan storage:link
    - cd $DEPLOYPATH && chmod -R 755 storage
    - cd $DEPLOYPATH && chmod -R 755 bootstrap/cache
```

---

## 📋 **Manual Deployment Steps (Recommended)**

### **Instead of cPanel auto-deployment, use manual upload:**

1. **Compress production files:**
```bash
zip -r mechamap_production.zip . -x "docs/*" ".git/*" "tests/*" "node_modules/*"
```

2. **Upload via cPanel File Manager**

3. **Extract and set permissions**

4. **Run deployment commands:**
```bash
php artisan config:cache
php artisan route:cache
php artisan storage:link
```

---

## 🔧 **Environment Configuration**

### **Production .env setup:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mechamap.com
DB_HOST=127.0.0.1
DB_DATABASE=mechamap_com
DB_USERNAME=mechamap_com
DB_PASSWORD=KhGZ3e91r0wTwXF9
```

---

## 💡 **Best Practices**

### **✅ Recommended Approach**
- **Manual upload** with QUICK_DEPLOY.md guide
- **Better control** over deployment process
- **Easy troubleshooting** if issues arise
- **No dependency** on cPanel auto-deployment

### **🚫 Issues with Auto-deployment**
- Less control over process
- Harder to troubleshoot
- May not handle Laravel-specific requirements
- Configuration can become outdated

---

**Status**: 📁 **Moved to docs/deployment/ for reference only**  
**Recommendation**: Use manual deployment with QUICK_DEPLOY.md guide

---

*MechaMap Laravel Backend - cPanel Configuration Reference*
