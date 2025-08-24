<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop the unused marketplace_products_normalized view.
     * This view was created for data normalization but is not being used in the application.
     * All business logic has been implemented in the application layer instead.
     *
     * Backup of the view definition is available at:
     * database/backups/marketplace_products_normalized_view_backup.sql
     */
    public function up(): void
    {
        // Check if view exists before dropping
        $viewExists = DB::select("
            SELECT TABLE_NAME
            FROM INFORMATION_SCHEMA.VIEWS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'marketplace_products_normalized'
        ");

        if (!empty($viewExists)) {
            DB::statement('DROP VIEW marketplace_products_normalized');

            // Log the action
            \Log::info('Dropped unused view: marketplace_products_normalized', [
                'reason' => 'View cleanup - not used in application',
                'backup_location' => 'database/backups/marketplace_products_normalized_view_backup.sql'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Recreate the view from backup if needed.
     * Note: This should only be used if the view is actually needed.
     */
    public function down(): void
    {
        // Recreate the view from the original definition
        DB::statement('DROP VIEW IF EXISTS marketplace_products_normalized');

        DB::statement('
            CREATE VIEW marketplace_products_normalized AS
            SELECT
                id,
                uuid,
                name,
                slug,
                description,
                short_description,
                sku,
                seller_id,
                product_category_id,
                product_type,
                seller_type,
                industry_category,

                -- Normalized pricing
                price as regular_price,
                CASE
                    WHEN is_on_sale = 1 AND sale_price IS NOT NULL AND sale_price < price
                    THEN sale_price
                    ELSE price
                END as effective_price,

                CASE
                    WHEN is_on_sale = 1 AND sale_price IS NOT NULL AND sale_price < price
                    THEN ROUND(((price - sale_price) / price) * 100, 2)
                    ELSE 0
                END as discount_percentage,

                -- Normalized stock status
                CASE
                    WHEN product_type = "digital" THEN 1
                    WHEN manage_stock = 0 THEN 1
                    WHEN stock_quantity > 0 THEN 1
                    ELSE 0
                END as is_available,

                -- Normalized digital product detection
                CASE
                    WHEN product_type = "digital" THEN 1
                    WHEN JSON_LENGTH(COALESCE(digital_files, "[]")) > 0 THEN 1
                    ELSE 0
                END as is_digital_product,

                -- Normalized file size
                COALESCE(file_size_mb, 0) as file_size_mb,

                -- Status and visibility
                status,
                is_featured,
                is_active,
                approved_at,

                -- Analytics
                view_count,
                purchase_count,
                download_count,
                rating_average,
                rating_count,

                -- Timestamps
                created_at,
                updated_at

            FROM marketplace_products
            WHERE deleted_at IS NULL
        ');

        \Log::info('Recreated view: marketplace_products_normalized (rollback)');
    }
};
