# 📁 **FILE ORGANIZATION GUIDELINES**

> **Quy tắc tổ chức file cho dự án MechaMap - CHỈ giữ files cần thiết cho PRODUCTION ở root**  
> **Mục tiêu**: Làm việc cá nhân hiệu quả - chỉ quản lý essential files lên Git

---

## 🎯 **NGUYÊN TẮC CORE: ROOT = PRODUCTION ONLY**

### **✅ ĐƯỢC PHÉP Ở ROOT (7 files)**
```
📁 mechamap_backend/
├── artisan                 # Laravel CLI tool
├── composer.json           # Dependencies configuration  
├── composer.lock           # Dependency lock file
├── phpunit.xml             # Testing configuration
├── README.md               # Main project documentation
├── QUICK_DEPLOY.md         # Essential deployment guide
└── data_v2_fixed.sql       # Production database backup
```

### **⚠️ CHỈ 7 FILES NÀY ĐƯỢC COMMIT LÊN GIT**
- Mọi file khác đều phải tổ chức vào `docs/` subdirectories
- Root directory phải luôn sạch và professional
- Production deployment chỉ cần copy root directory

---

## 🚫 **CẤM TẠO Ở ROOT DIRECTORY**

### **📊 Report & Analysis Files**
```
❌ KHÔNG tạo ở root:
- MIGRATION_REPORT.md
- PRODUCTION_ANALYSIS.md  
- DATABASE_SUMMARY.md
- PROJECT_STATUS.md
- CLEANUP_COMPLETE.md

✅ TẠO ở: docs/reports/
```

### **🧪 Test Files** 
```
❌ KHÔNG tạo ở root:
- test_migration.php
- check_database.php
- TestController.php
- migration_test.js

✅ TẠO ở: tests/ hoặc docs/testing/
```

### **🔧 Development Files**
```
❌ KHÔNG tạo ở root:
- dev_script.php
- prototype_feature.php
- experiment_api.php
- development_notes.md

✅ TẠO ở: docs/development/
```

### **⚙️ Scripts & Batch Files**
```
❌ KHÔNG tạo ở root:
- create_migration.bat
- setup_database.sh
- deploy.ps1
- backup_data.bat

✅ TẠO ở: docs/development-tools/
```

### **💾 Backup & Archive Files**
```
❌ KHÔNG tạo ở root:
- database_backup.sql
- old_migration.php
- archive_2025.zip
- temp_data.json

✅ TẠO ở: docs/archived/
```

---

## ✅ **CHỈ ĐƯỢC PHÉP Ở ROOT**

### **Essential Laravel Files**
- `artisan` - Laravel CLI
- `composer.json` - Dependencies
- `composer.lock` - Dependency lock
- `phpunit.xml` - Testing configuration

### **Project Documentation**
- `README.md` - Main project documentation
- `QUICK_DEPLOY.md` - Deployment guide

### **Production Data**
- `data_v2_fixed.sql` - Main database file

### **Configuration Templates**
- `.env.example` - Environment template
- `.env.production` - Production template

---

## 📂 **ORGANIZED DIRECTORY STRUCTURE**

### **docs/reports/** - Project Reports
```
✅ Tạo reports ở đây:
- MIGRATION_COMPLETION_REPORT.md
- PRODUCTION_READINESS_SUMMARY.md
- DATABASE_ANALYSIS_COMPLETE.md
- PROJECT_FINAL_STATUS.md
```

### **docs/testing/** - Test Documentation
```
✅ Tạo test docs ở đây:
- api-tests/
- browser-tests/
- integration-tests/
- performance-tests/
- verification-tests/
```

### **docs/development/** - Development Notes
```
✅ Tạo dev files ở đây:
- feature-development/
- bug-fixes/
- experimental-features/
- development-notes/
```

### **docs/deployment/** - Deployment Guides
```
✅ Tạo deployment files ở đây:
- server-setup-guides/
- deployment-scripts/
- configuration-templates/
- environment-setup/
```

### **docs/archived/** - Historical Files
```
✅ Tạo archive files ở đây:
- old-versions/
- backup-configurations/
- deprecated-features/
- historical-data/
```

---

## 🛡️ **GITIGNORE PROTECTION**

### **Patterns Blocked in Root**
```gitignore
# Reports (must be in docs/reports/)
/*_REPORT.md
/*_ANALYSIS.md
/*_SUMMARY.md
/*_STATUS.md

# Tests (must be in tests/ or docs/testing/)
/test_*.php
/*_test.php
/Test*.php

# Development (must be in docs/development/)
/dev_*.php
/*_dev.php
/experiment_*.php

# Scripts (must be in docs/development-tools/)
/*.bat
/*.sh
/*.ps1

# Archives (temporary files)
/*.zip
/*.tar
/*.rar
```

---

## 💡 **BEST PRACTICES**

### **1. File Naming Convention**
```
✅ Good:
- docs/reports/MIGRATION_COMPLETION_REPORT.md
- tests/Feature/ThreadControllerTest.php
- docs/testing/api-tests/thread-api-test.php

❌ Bad:
- MIGRATION_REPORT.md (in root)
- test_thread.php (in root)
- check_api.php (in root)
```

### **2. Documentation Organization**
```
✅ Logical Structure:
docs/
├── reports/           # Completion reports
├── testing/          # Test procedures
├── development/      # Dev notes & guides
├── deployment/       # Deployment docs
└── archived/         # Historical files
```

### **3. Development Workflow**
```
1. ✅ Create feature documentation in docs/development/
2. ✅ Write tests in tests/ directory
3. ✅ Create reports in docs/reports/
4. ✅ Archive old files in docs/archived/
5. ❌ NEVER create temporary files in root
```

---

## 🚨 **VIOLATION EXAMPLES**

### **Common Mistakes to Avoid**
```
❌ Creating in root:
- DATABASE_CHECK.md
- test_feature.php  
- migration_script.bat
- temp_backup.sql
- old_config.php

✅ Correct locations:
- docs/reports/DATABASE_CHECK.md
- tests/Feature/FeatureTest.php
- docs/development-tools/migration_script.bat
- docs/archived/temp_backup.sql
- docs/archived/old_config.php
```

---

## 🎯 **BENEFITS**

### **Clean Root Directory**
- ✅ Professional appearance
- ✅ Easy navigation
- ✅ Clear project structure
- ✅ Better maintainability

### **Organized Documentation**
- ✅ Easy to find specific files
- ✅ Logical categorization
- ✅ Better team collaboration
- ✅ Historical preservation

### **Version Control Benefits**
- ✅ Cleaner Git history
- ✅ Reduced merge conflicts
- ✅ Better code reviews
- ✅ Professional repository

---

## 📋 **COMPLIANCE CHECKLIST**

### **Before Creating Any File**
- [ ] Is this a report/analysis? → Use `docs/reports/`
- [ ] Is this a test file? → Use `tests/` or `docs/testing/`
- [ ] Is this development related? → Use `docs/development/`
- [ ] Is this a script/tool? → Use `docs/development-tools/`
- [ ] Is this a backup/archive? → Use `docs/archived/`
- [ ] Is this essential for Laravel? → Only then use root

### **Git Commit Guidelines**
- [ ] No temporary files in root
- [ ] All documentation properly organized
- [ ] No development artifacts in root
- [ ] Clean and professional structure

---

**🎯 GOAL**: Maintain a clean, professional, and well-organized codebase that's easy to navigate and maintain.

**🚀 RESULT**: Professional Laravel project ready for production deployment and team collaboration.

---

*Follow these guidelines to keep MechaMap codebase clean and organized!*
