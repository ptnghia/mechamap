# 📦 **FILES TO EXCLUDE FROM ARCHIVE**
*These files/folders should NOT be included when compressing for upload*

---

## 🚫 **EXCLUDE FROM ZIP**

### **Large Directories**
```
vendor/           # Will run composer install on server
node_modules/     # Frontend dependencies (if any)
storage/logs/     # Log files
storage/framework/cache/     # Cache files
storage/framework/sessions/  # Session files
storage/framework/views/     # Compiled views
```

### **Local Development Files**
```
.git/             # Git repository (upload via git clone instead)
.vscode/          # VS Code settings
.phpunit.result.cache
tests/            # Unit tests (optional)
```

### **Files to Keep**
```
✅ .env                    # Production config (updated)
✅ .htaccess               # Apache config
✅ artisan                 # Laravel CLI
✅ composer.json           # Dependencies list
✅ composer.lock           # Exact versions
✅ data_v2_fixed.sql       # Database backup
✅ QUICK_DEPLOY.md         # Deployment guide
✅ app/                    # Application code
✅ bootstrap/              # Bootstrap files
✅ config/                 # Configuration
✅ database/               # Migrations & seeders
✅ lang/                   # Language files
✅ public/                 # Web root
✅ resources/              # Views & assets
✅ routes/                 # Route definitions
✅ storage/app/            # File storage (keep structure)
```

---

## 📋 **COMPRESSION COMMAND**

### **Using 7-Zip (Recommended)**
```bash
7z a -tzip mechamap_backend.zip . -x!vendor -x!node_modules -x!.git -x!storage/logs/* -x!storage/framework/cache/* -x!storage/framework/sessions/* -x!storage/framework/views/* -x!.vscode -x!tests -x!.phpunit.result.cache
```

### **Using Windows Built-in**
1. Select all files EXCEPT: vendor/, node_modules/, .git/, storage/logs/, storage/framework/cache/, storage/framework/sessions/, storage/framework/views/
2. Right-click → Send to → Compressed folder

---

## 🚀 **UPLOAD PRIORITY**

### **Method 1: ZIP Upload** (Fastest)
1. Compress excluding above files
2. Upload ZIP to hosting
3. Extract on server
4. Run `composer install --no-dev`
5. Follow `QUICK_DEPLOY.md`

### **Method 2: Git Clone** (Recommended)
1. Push to GitHub repository
2. Clone on server: `git clone [repo] mechamap`
3. Run `composer install --no-dev`
4. Copy `.env` config
5. Follow `QUICK_DEPLOY.md`

---

**Archive Size**: ~50MB (without vendor/)  
**Upload Time**: ~5-10 minutes (depending on connection)  
**Setup Time**: ~15 minutes total

*Ready for lightning-fast deployment! ⚡*
