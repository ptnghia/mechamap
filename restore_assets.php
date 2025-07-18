<?php
// MechaMap Assets Restore Script
echo "🔄 Khôi phục assets từ backup...\n";

if (is_dir('assets_backup_2025-07-18_20-26-12')) {
    // Xóa assets hiện tại
    if (is_dir('public/assets')) {
        exec('rmdir /s /q public\\assets 2>nul || rm -rf public/assets');
    }

    // Khôi phục từ backup
    exec('xcopy /e /i assets_backup_2025-07-18_20-26-12 public\\assets 2>nul || cp -r assets_backup_2025-07-18_20-26-12 public/assets');
    echo "✅ Assets đã được khôi phục thành công!\n";
} else {
    echo "❌ Không tìm thấy backup: assets_backup_2025-07-18_20-26-12\n";
}
