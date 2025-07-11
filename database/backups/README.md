# 🔒 MechaMap Database Backup System

**Created:** 2025-07-12 00:46:41  
**Purpose:** Backup system cho User Registration & Permission System Overhaul  
**Database:** mechamap_backend (135 tables, 92 users)

---

## 📁 **BACKUP FILES**

### **Current Backup Set: `2025-07-12_00-46-41`**

| File | Size | Description | Status |
|------|------|-------------|--------|
| `mechamap_backup_2025-07-12_00-46-41_structure.sql` | 244.32 KB | Database structure (135 tables) | ✅ Valid |
| `mechamap_backup_2025-07-12_00-46-41_critical_data.sql` | 1,484.2 KB | Critical tables data | ✅ Valid |
| `mechamap_backup_2025-07-12_00-46-41_users.sql` | 166.58 KB | User data (92 users) | ✅ Valid |
| `mechamap_backup_2025-07-12_00-46-41_restore.php` | 1.11 KB | Restore script | ✅ Valid |

**Total Backup Size:** ~1.9 MB  
**Validation Status:** ✅ All files validated successfully

---

## 🚀 **USAGE**

### **Create New Backup**
```bash
cd /d/xampp/htdocs/laravel/mechamap_backend
php database/backups/backup_script.php
```

### **Validate Backup**
```bash
php database/backups/validate_backup.php
```

### **Restore Database (EMERGENCY ONLY)**
```bash
# ⚠️ WARNING: This will OVERWRITE current database!
php database/backups/mechamap_backup_2025-07-12_00-46-41_restore.php
```

---

## 🔍 **BACKUP CONTENTS**

### **Structure Backup (`_structure.sql`)**
- All 135 table definitions
- Indexes and constraints
- Foreign key relationships
- Database schema complete

### **Critical Data Backup (`_critical_data.sql`)**
**Tables included:**
- `users` - User accounts and profiles
- `roles` - User roles system
- `permissions` - Permission definitions
- `role_has_permissions` - Role-permission mappings
- `user_has_roles` - User-role assignments
- `categories` - Forum categories
- `forums` - Forum definitions
- `threads` - Discussion threads
- `comments` - Thread comments
- `showcases` - User showcases
- `marketplace_products` - Marketplace products
- `marketplace_sellers` - Seller accounts

### **User Data Backup (`_users.sql`)**
- Complete user table data
- 92 user accounts
- All user profiles and settings
- Business account information

---

## ⚠️ **SAFETY GUIDELINES**

### **Before Restore**
1. **STOP APPLICATION** - Put site in maintenance mode
2. **NOTIFY TEAM** - Inform all stakeholders
3. **BACKUP CURRENT STATE** - Create new backup of current data
4. **VERIFY BACKUP** - Run validation script

### **During Restore**
1. **SINGLE OPERATOR** - Only one person should perform restore
2. **MONITOR PROCESS** - Watch for errors during execution
3. **VERIFY COMPLETION** - Check all tables restored correctly
4. **TEST FUNCTIONALITY** - Verify critical features work

### **After Restore**
1. **CLEAR CACHE** - `php artisan cache:clear`
2. **RESTART SERVICES** - Restart web server and queue workers
3. **TEST LOGIN** - Verify user authentication works
4. **CHECK PERMISSIONS** - Verify marketplace permissions
5. **MONITOR LOGS** - Watch for errors in application logs

---

## 🔧 **TROUBLESHOOTING**

### **Common Issues**

#### **"Table doesn't exist" Error**
```bash
# Check if database exists
php artisan db:show

# Recreate database if needed
mysql -u root -p -e "CREATE DATABASE mechamap_backend;"
```

#### **"Foreign key constraint fails"**
```bash
# The restore script handles this automatically with:
# SET FOREIGN_KEY_CHECKS = 0;
# ... restore operations ...
# SET FOREIGN_KEY_CHECKS = 1;
```

#### **"Access denied" Error**
```bash
# Check database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mechamap_backend
DB_USERNAME=root
DB_PASSWORD=
```

#### **"File not found" Error**
```bash
# Verify backup files exist
ls -la database/backups/mechamap_backup_2025-07-12_00-46-41*

# Check file permissions
chmod 644 database/backups/mechamap_backup_2025-07-12_00-46-41*
```

---

## 📊 **BACKUP VERIFICATION**

### **Automatic Validation**
The backup system includes automatic validation:
- ✅ File existence check
- ✅ File size validation
- ✅ SQL syntax verification
- ✅ Data integrity check
- ✅ Restore script validation

### **Manual Verification**
```bash
# Check table count
php artisan tinker
>>> DB::select('SHOW TABLES') |> count()
// Should return 135+

# Check user count
>>> DB::table('users')->count()
// Should return 92+

# Check critical tables
>>> DB::table('roles')->count()
>>> DB::table('permissions')->count()
>>> DB::table('marketplace_products')->count()
```

---

## 🔄 **BACKUP ROTATION**

### **Retention Policy**
- **Keep:** Last 7 daily backups
- **Keep:** Last 4 weekly backups  
- **Keep:** Last 12 monthly backups
- **Archive:** Yearly backups

### **Cleanup Old Backups**
```bash
# Remove backups older than 30 days
find database/backups/ -name "mechamap_backup_*" -mtime +30 -delete

# Keep only last 10 backups
ls -t database/backups/mechamap_backup_* | tail -n +11 | xargs rm -f
```

---

## 📞 **EMERGENCY CONTACTS**

**Database Issues:**
- Database Admin: [Contact Info]
- Lead Developer: [Contact Info]

**System Issues:**
- DevOps Engineer: [Contact Info]
- System Administrator: [Contact Info]

**Business Critical:**
- Project Manager: [Contact Info]
- Technical Lead: [Contact Info]

---

## 📝 **BACKUP LOG**

| Date | Time | Type | Status | Size | Notes |
|------|------|------|--------|------|-------|
| 2025-07-12 | 00:46:41 | Full | ✅ Success | 1.9 MB | Pre-migration backup |

---

**🚨 CRITICAL REMINDER:** Always test restore procedure on staging environment before using in production!
