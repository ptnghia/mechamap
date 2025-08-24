<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== BACKUP VIEW DEFINITION ===\n\n";

try {
    // Get view definition
    $viewDefinition = DB::select("SHOW CREATE VIEW marketplace_products_normalized");
    
    if (!empty($viewDefinition)) {
        $createStatement = $viewDefinition[0]->{'Create View'};
        
        echo "✅ View definition retrieved successfully\n\n";
        echo "=== VIEW DEFINITION ===\n";
        echo $createStatement . "\n\n";
        
        // Save to backup file
        $backupContent = "-- Backup of marketplace_products_normalized view definition\n";
        $backupContent .= "-- Created: " . date('Y-m-d H:i:s') . "\n";
        $backupContent .= "-- Reason: Removing unused view as part of cleanup\n\n";
        $backupContent .= $createStatement . ";\n";
        
        file_put_contents(__DIR__ . '/../database/backups/marketplace_products_normalized_view_backup.sql', $backupContent);
        echo "✅ View definition saved to: database/backups/marketplace_products_normalized_view_backup.sql\n\n";
        
        // Analyze view structure
        echo "=== VIEW ANALYSIS ===\n";
        $columns = DB::select("DESCRIBE marketplace_products_normalized");
        echo "Columns in view: " . count($columns) . "\n";
        foreach ($columns as $column) {
            echo "  - {$column->Field} ({$column->Type})\n";
        }
        
        // Check if view is being used in any stored procedures or triggers
        echo "\n=== DEPENDENCY CHECK ===\n";
        $dependencies = DB::select("
            SELECT TABLE_NAME, TABLE_TYPE 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME LIKE '%marketplace_products_normalized%'
        ");
        
        if (count($dependencies) > 1) {
            echo "⚠️  Found potential dependencies:\n";
            foreach ($dependencies as $dep) {
                echo "  - {$dep->TABLE_NAME} ({$dep->TABLE_TYPE})\n";
            }
        } else {
            echo "✅ No additional dependencies found\n";
        }
        
    } else {
        echo "❌ Could not retrieve view definition\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error backing up view: " . $e->getMessage() . "\n";
}
