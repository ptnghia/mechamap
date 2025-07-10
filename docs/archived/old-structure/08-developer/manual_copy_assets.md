# 🔧 **MANUAL DASON ASSETS COPY GUIDE**

## **📋 Vấn đề hiện tại**

Chúng ta đang gặp vấn đề với việc copy assets từ Dason template bằng command line. Hãy thực hiện copy thủ công.

## **📁 Đường dẫn cần copy**

### **Source (Dason Template):**
```
D:\Dev\mechamap-forum\Dason-Laravel_v1.0.0\Admin\public\assets\
├── css\
├── js\
├── images\
├── fonts\
└── libs\
```

### **Destination (MechaMap):**
```
D:\Dev\mechamap-forum\public\assets\
├── css\
├── js\
├── images\
├── fonts\
└── libs\
```

## **🔧 BƯỚC THỰC HIỆN MANUAL COPY**

### **Bước 1: Mở File Explorer**
1. Mở File Explorer (Windows + E)
2. Navigate đến: `D:\Dev\mechamap-forum`

### **Bước 2: Copy CSS Files**
1. Mở thư mục: `Dason-Laravel_v1.0.0\Admin\public\assets\css`
2. Select All files (Ctrl + A)
3. Copy (Ctrl + C)
4. Navigate đến: `public\assets\css`
5. Paste (Ctrl + V)

**Files cần copy:**
- app.css
- app.min.css
- app.rtl.css
- bootstrap.css
- bootstrap.min.css
- bootstrap.rtl.css
- icons.css
- icons.min.css
- icons.rtl.css
- preloader.css
- preloader.min.css
- preloader.rtl.css

### **Bước 3: Copy JS Files**
1. Mở thư mục: `Dason-Laravel_v1.0.0\Admin\public\assets\js`
2. Select All files và folders (Ctrl + A)
3. Copy (Ctrl + C)
4. Navigate đến: `public\assets\js`
5. Paste (Ctrl + V)

**Files cần copy:**
- app.min.js
- pages\ (entire folder)

### **Bước 4: Copy Images**
1. Mở thư mục: `Dason-Laravel_v1.0.0\Admin\public\assets\images`
2. Select All files và folders (Ctrl + A)
3. Copy (Ctrl + C)
4. Navigate đến: `public\assets\images`
5. Paste (Ctrl + V)

### **Bước 5: Copy Fonts**
1. Mở thư mục: `Dason-Laravel_v1.0.0\Admin\public\assets\fonts`
2. Select All files (Ctrl + A)
3. Copy (Ctrl + C)
4. Navigate đến: `public\assets\fonts`
5. Paste (Ctrl + V)

### **Bước 6: Copy Libraries**
1. Mở thư mục: `Dason-Laravel_v1.0.0\Admin\public\assets\libs`
2. Select All folders (Ctrl + A)
3. Copy (Ctrl + C)
4. Navigate đến: `public\assets\libs`
5. Paste (Ctrl + V)

**⚠️ Lưu ý:** Thư mục libs có rất nhiều files, có thể mất vài phút để copy.

## **✅ VERIFICATION CHECKLIST**

Sau khi copy xong, kiểm tra các thư mục sau có files:

### **CSS Files (12 files):**
- [ ] `public\assets\css\app.min.css`
- [ ] `public\assets\css\bootstrap.min.css`
- [ ] `public\assets\css\icons.min.css`

### **JS Files:**
- [ ] `public\assets\js\app.min.js`
- [ ] `public\assets\js\pages\` (folder with many files)

### **Images:**
- [ ] `public\assets\images\users\` (folder with avatar files)
- [ ] `public\assets\images\flags\` (folder with flag images)

### **Fonts:**
- [ ] `public\assets\fonts\boxicons.woff2`
- [ ] `public\assets\fonts\fa-solid-900.woff2`

### **Libraries (40+ folders):**
- [ ] `public\assets\libs\jquery\`
- [ ] `public\assets\libs\bootstrap\`
- [ ] `public\assets\libs\apexcharts\`
- [ ] `public\assets\libs\datatables.net\`

## **🚀 NEXT STEPS AFTER COPY**

### **1. Update package.json**
```json
{
    "private": true,
    "name": "MechaMap",
    "version": "2.0.0",
    "scripts": {
        "dev": "npm run development",
        "development": "mix",
        "watch": "mix watch",
        "hot": "mix watch --hot",
        "prod": "npm run production",
        "production": "mix --production"
    },
    "devDependencies": {
        "axios": "^1.6.0",
        "laravel-mix": "^6.0.49",
        "lodash": "^4.17.21",
        "postcss": "^8.4.31",
        "sass": "^1.69.5",
        "sass-loader": "^13.3.2"
    },
    "dependencies": {
        "apexcharts": "^3.44.0",
        "bootstrap": "^5.3.2",
        "chart.js": "^4.4.0",
        "choices.js": "^10.2.0",
        "datatables.net": "^1.13.6",
        "datatables.net-bs5": "^1.13.6",
        "feather-icons": "^4.29.1",
        "jquery": "^3.7.1",
        "metismenu": "^3.0.7",
        "simplebar": "^6.2.5",
        "sweetalert2": "^11.7.32"
    }
}
```

### **2. Create webpack.mix.js**
```javascript
const mix = require('laravel-mix');

// Compile main application assets
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
       processCssUrls: false
   });

// Copy Dason assets
mix.copy('public/assets/css/app.min.css', 'public/css/dason-admin.css')
   .copy('public/assets/js/app.min.js', 'public/js/dason-admin.js');

// Version files for cache busting
if (mix.inProduction()) {
    mix.version();
}
```

### **3. Install Dependencies**
```bash
npm install
npm run dev
```

### **4. Test Dason Dashboard**
```bash
php artisan serve
# Visit: http://localhost:8000/admin/dason
```

## **🎯 SUCCESS INDICATORS**

Khi copy thành công, bạn sẽ thấy:
- [ ] Thư mục `public\assets\` có khoảng 500+ files
- [ ] File `public\assets\css\app.min.css` có size ~200KB
- [ ] File `public\assets\js\app.min.js` có size ~500KB
- [ ] Thư mục `public\assets\libs\` có 40+ sub-folders

## **🆘 TROUBLESHOOTING**

### **Nếu copy bị lỗi:**
1. **Check disk space** - Cần ít nhất 50MB free space
2. **Run as Administrator** - Right-click File Explorer → Run as Administrator
3. **Close antivirus** - Temporarily disable real-time protection
4. **Copy in batches** - Copy từng thư mục một thay vì tất cả cùng lúc

### **Nếu files bị missing:**
1. **Re-copy specific folders** - Copy lại từng thư mục bị thiếu
2. **Check file permissions** - Ensure read/write access
3. **Verify source files** - Check Dason template integrity

---

**🎉 Sau khi copy xong, chúng ta sẽ tiếp tục với Bước 2.4: Update Dependencies!**
