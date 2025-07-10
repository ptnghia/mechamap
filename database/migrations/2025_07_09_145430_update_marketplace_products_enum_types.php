<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update product_type enum to new structure: digital, new_product, used_product
     */
    public function up(): void
    {
        // Step 1: First expand the enum to include new values
        DB::statement("ALTER TABLE marketplace_products MODIFY COLUMN product_type ENUM('physical', 'digital', 'service', 'new_product', 'used_product') NOT NULL DEFAULT 'physical'");

        // Step 2: Map existing data to new enum values
        DB::statement("UPDATE marketplace_products SET product_type = 'new_product' WHERE product_type = 'physical'");
        // Keep 'digital' as is
        DB::statement("UPDATE marketplace_products SET product_type = 'new_product' WHERE product_type = 'service'");

        // Step 3: Remove old enum values, keep only new ones
        DB::statement("ALTER TABLE marketplace_products MODIFY COLUMN product_type ENUM('digital', 'new_product', 'used_product') NOT NULL DEFAULT 'new_product'");
    }

    /**
     * Reverse the migrations.
     * Rollback to original enum values
     */
    public function down(): void
    {
        // Step 1: Map back to original values
        DB::statement("UPDATE marketplace_products SET product_type = 'physical' WHERE product_type = 'new_product'");
        DB::statement("UPDATE marketplace_products SET product_type = 'service' WHERE product_type = 'used_product'");

        // Step 2: Restore original enum
        DB::statement("ALTER TABLE marketplace_products MODIFY COLUMN product_type ENUM('physical', 'digital', 'service') NOT NULL DEFAULT 'physical'");
    }
};
