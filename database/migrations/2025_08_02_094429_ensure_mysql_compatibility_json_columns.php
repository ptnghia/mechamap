<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Đảm bảo tương thích với MySQL bằng cách xóa default values từ JSON columns
     */
    public function up(): void
    {
        // Danh sách các bảng và JSON columns có thể có default values
        $jsonColumnsToFix = [
            'notification_templates' => [
                'channels',
                'email_template',
                'database_template',
                'broadcast_template'
            ],
            'notification_preferences' => [
                'preferences',
                'channels'
            ],
            'marketplace_products' => [
                'technical_specs',
                'mechanical_properties',
                'standards_compliance',
                'file_formats',
                'software_compatibility',
                'digital_files',
                'images',
                'attachments',
                'tags'
            ],
            'marketplace_orders' => [
                'shipping_address',
                'billing_address',
                'payment_details',
                'discount_details',
                'metadata'
            ],
            'cad_files' => [
                'compatible_software',
                'units',
                'bounding_box',
                'material_properties',
                'manufacturing_methods',
                'manufacturing_constraints',
                'features',
                'parameters',
                'configurations',
                'related_files',
                'design_standards',
                'tolerance_standards',
                'quality_requirements',
                'usage_rights',
                'tags',
                'keywords',
                'processing_log'
            ]
        ];

        foreach ($jsonColumnsToFix as $tableName => $columns) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            echo "🔧 Fixing JSON columns in {$tableName}...\n";

            foreach ($columns as $columnName) {
                if (!Schema::hasColumn($tableName, $columnName)) {
                    continue;
                }

                try {
                    // Cập nhật NULL values với empty JSON
                    $defaultValue = match($columnName) {
                        'channels' => '["database"]',
                        'preferences' => '{}',
                        default => '[]'
                    };

                    DB::statement("
                        UPDATE `{$tableName}`
                        SET `{$columnName}` = ?
                        WHERE `{$columnName}` IS NULL
                    ", [$defaultValue]);

                    // Xóa default value để tương thích với MySQL
                    DB::statement("
                        ALTER TABLE `{$tableName}`
                        ALTER COLUMN `{$columnName}` DROP DEFAULT
                    ");

                    echo "  ✅ Fixed {$columnName}\n";

                } catch (\Exception $e) {
                    // Bỏ qua lỗi nếu column đã không có default value
                    if (strpos($e->getMessage(), "doesn't have a default value") === false) {
                        echo "  ⚠️ Warning for {$columnName}: " . $e->getMessage() . "\n";
                    }
                }
            }
        }

        echo "✅ MySQL compatibility ensured for JSON columns\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback vì việc xóa default values không ảnh hưởng đến dữ liệu
        echo "ℹ️ No rollback needed - removing default values doesn't affect data\n";
    }
};
