# 🔧 Quick Commands Reference

## Verification Scripts
```bash
# Kiểm tra tổng thể hệ thống
php scripts/simple_verification.php

# Test navigation helpers
php scripts/test_navigation_helpers.php

# Kiểm tra via.placeholder replacement
php scripts/final_verification_placeholder.php

# Tạo lại placeholder images
php scripts/generate_placeholders.php
```

## Helper Functions Available

### Navigation Assets (Database-driven)
```php
get_logo_url()     // Logo từ database
get_favicon_url()  // Favicon từ database  
get_banner_url()   // Banner từ database
get_site_name()    // Site name từ database
```

### Placeholder System
```php
placeholder_image(300, 200, 'Custom Text')  // Smart placeholder
avatar_placeholder('User Name', 150)        // Avatar generator
```

## File Locations
```
public/images/placeholders/    # Local placeholder images
app/Helpers/SettingHelper.php  # All helper functions
docs/reports/                  # Documentation & reports
scripts/                       # Utility scripts
```

## Quick Health Check
```bash
# Verify all systems working
php scripts/simple_verification.php

# Expected output: ✅ EXCELLENT - All files present
```

---
*Use these commands for maintenance and verification*
