<?php
/**
 * Restore Backup Script
 * Created: 2025-07-20 02:24:29
 * Backup: 2025-07-20_02-24-29
 */

echo "🔄 Restoring localization backup...\n";

// 1. Restore language files
echo "📚 Restoring language files...\n";
if (is_dir('resources/lang')) {
    shell_exec('rm -rf resources/lang');
}
shell_exec('cp -r storage/localization/backup_2025-07-20_02-24-29/lang resources/');
echo "   ✅ Language files restored\n";

// 2. Restore view files
echo "👁️ Restoring view files...\n";
shell_exec('cp -r storage/localization/backup_2025-07-20_02-24-29/views/* resources/views/');
echo "   ✅ View files restored\n";

// 3. Restore PHP files
echo "🔧 Restoring PHP files...\n";
if (file_exists('storage/localization/backup_2025-07-20_02-24-29/php/LanguageController.php')) {
    copy('storage/localization/backup_2025-07-20_02-24-29/php/LanguageController.php', 'app/Http/Controllers/LanguageController.php');
}
if (file_exists('storage/localization/backup_2025-07-20_02-24-29/php/LanguageService.php')) {
    copy('storage/localization/backup_2025-07-20_02-24-29/php/LanguageService.php', 'app/Services/LanguageService.php');
}
if (file_exists('storage/localization/backup_2025-07-20_02-24-29/php/Localization.php')) {
    copy('storage/localization/backup_2025-07-20_02-24-29/php/Localization.php', 'app/Http/Middleware/Localization.php');
}
echo "   ✅ PHP files restored\n";

echo "\n🎉 Backup restored successfully!\n";
echo "⚠️ Remember to clear caches: php artisan cache:clear\n";
