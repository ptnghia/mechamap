# Deployment Guide - New Localization Structure

**Created:** 2025-07-20 02:55:09
**Version:** 1.0

## 🚀 Deployment Steps

### 1. Backup Current System
```bash
# Backup current lang directory
cp -r resources/lang resources/lang_backup_$(date +%Y%m%d)
```

### 2. Deploy New Structure
```bash
# Copy new lang structure
cp -r resources/lang_new resources/lang
```

### 3. Update Configuration
```bash
# Add to .env
NEW_LOCALIZATION_ENABLED=true
LOCALIZATION_FALLBACK=false
```

### 4. Install Helper Functions
```bash
# Copy helper file
cp storage/localization/TranslationHelper.php app/Helpers/
```

### 5. Update Service Provider
```php
// Add to app/Providers/AppServiceProvider.php
// See service_provider_updates.php for details
```

### 6. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 7. Test Deployment
```bash
# Test key translations
php artisan tinker
>>> __('core.auth.login.title')
>>> __('ui.buttons.save')
```

## 🔄 Rollback Plan

If issues occur:
```bash
# Restore backup
rm -rf resources/lang
cp -r resources/lang_backup_YYYYMMDD resources/lang
```

## ✅ Verification Checklist

- [ ] All pages load without translation errors
- [ ] Language switching works correctly
- [ ] Authentication flows work
- [ ] Form validations display properly
- [ ] Admin interface functions correctly
- [ ] User interface elements display correctly

## 📞 Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify file permissions
3. Clear all caches
4. Use rollback plan if necessary


## 🔄 Migration from Old Structure

If migrating from the old localization structure:

### 1. Backup Current System
```bash
cp -r resources/lang resources/lang_backup_$(date +%Y%m%d)
```

### 2. Switch to New Structure
```bash
# Rename current lang to old
mv resources/lang resources/lang_old

# Activate new structure
mv resources/lang_new resources/lang
```

### 3. Update Configuration
Add to your `.env`:
```
NEW_LOCALIZATION_ENABLED=true
```

