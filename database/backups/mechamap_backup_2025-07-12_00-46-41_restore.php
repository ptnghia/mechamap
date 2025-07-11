<?php
/**
 * 🔄 MechaMap Database Restore Script
 * Generated: 2025-07-12_00-46-41
 */

require_once __DIR__ . "/../../vendor/autoload.php";

$app = require_once __DIR__ . "/../../bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔄 Restoring MechaMap database from backup 2025-07-12_00-46-41...\n";

try {
    // Restore structure
    echo "📋 Restoring database structure...\n";
    $structureSql = file_get_contents(__DIR__ . "/mechamap_backup_2025-07-12_00-46-41_structure.sql");
    DB::unprepared($structureSql);

    // Restore critical data
    echo "🔑 Restoring critical data...\n";
    $criticalSql = file_get_contents(__DIR__ . "/mechamap_backup_2025-07-12_00-46-41_critical_data.sql");
    DB::unprepared($criticalSql);

    // Restore users
    echo "👥 Restoring user data...\n";
    $usersSql = file_get_contents(__DIR__ . "/mechamap_backup_2025-07-12_00-46-41_users.sql");
    DB::unprepared($usersSql);

    echo "✅ Database restore completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Restore failed: " . $e->getMessage() . "\n";
    exit(1);
}
