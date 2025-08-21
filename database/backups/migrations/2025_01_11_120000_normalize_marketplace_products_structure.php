<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Normalize marketplace products structure and add missing constraints
     */
    public function up(): void
    {
        Schema::table('marketplace_products', function (Blueprint $table) {
            // Add missing indexes for better performance
            $table->index(['product_type', 'is_active', 'status'], 'idx_product_type_active_status');
            $table->index(['seller_type', 'is_active'], 'idx_seller_type_active');
            $table->index(['price', 'is_active'], 'idx_price_active');
            $table->index(['created_at', 'is_active'], 'idx_created_active');
            $table->index(['view_count'], 'idx_view_count');
            $table->index(['purchase_count'], 'idx_purchase_count');
            $table->index(['rating_average'], 'idx_rating_average');
            
            // Add constraint to ensure sale_price is less than or equal to price
            // Note: This will be enforced at application level due to MySQL limitations
        });

        // Add check constraints using raw SQL (MySQL 8.0+)
        try {
            // Ensure sale_price <= price when is_on_sale is true
            DB::statement('ALTER TABLE marketplace_products ADD CONSTRAINT chk_sale_price_valid 
                          CHECK (is_on_sale = 0 OR sale_price IS NULL OR sale_price <= price)');
            
            // Ensure stock_quantity >= 0
            DB::statement('ALTER TABLE marketplace_products ADD CONSTRAINT chk_stock_quantity_positive 
                          CHECK (stock_quantity >= 0)');
            
            // Ensure price >= 0
            DB::statement('ALTER TABLE marketplace_products ADD CONSTRAINT chk_price_positive 
                          CHECK (price >= 0)');
            
            // Ensure rating_average is between 0 and 5
            DB::statement('ALTER TABLE marketplace_products ADD CONSTRAINT chk_rating_average_valid 
                          CHECK (rating_average >= 0 AND rating_average <= 5)');
            
        } catch (\Exception $e) {
            // Constraints might not be supported in older MySQL versions
            // Log the error but continue with migration
            \Log::warning('Could not add check constraints: ' . $e->getMessage());
        }

        // Create a view for normalized product data
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the view
        DB::statement('DROP VIEW IF EXISTS marketplace_products_normalized');
        
        // Drop check constraints
        try {
            DB::statement('ALTER TABLE marketplace_products DROP CONSTRAINT IF EXISTS chk_sale_price_valid');
            DB::statement('ALTER TABLE marketplace_products DROP CONSTRAINT IF EXISTS chk_stock_quantity_positive');
            DB::statement('ALTER TABLE marketplace_products DROP CONSTRAINT IF EXISTS chk_price_positive');
            DB::statement('ALTER TABLE marketplace_products DROP CONSTRAINT IF EXISTS chk_rating_average_valid');
        } catch (\Exception $e) {
            // Ignore errors if constraints don't exist
        }

        Schema::table('marketplace_products', function (Blueprint $table) {
            // Drop the indexes we added
            $table->dropIndex('idx_product_type_active_status');
            $table->dropIndex('idx_seller_type_active');
            $table->dropIndex('idx_price_active');
            $table->dropIndex('idx_created_active');
            $table->dropIndex('idx_view_count');
            $table->dropIndex('idx_purchase_count');
            $table->dropIndex('idx_rating_average');
        });
    }
};
