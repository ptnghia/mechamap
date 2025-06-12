<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shopping_carts', function (Blueprint $table) {
            // Drop existing unique constraint
            $table->dropUnique(['user_id', 'technical_product_id']);

            // Add license_type column
            $table->enum('license_type', ['standard', 'extended', 'commercial'])->default('standard')->after('total_price');

            // Add new unique constraint including license_type
            $table->unique(['user_id', 'technical_product_id', 'license_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_carts', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique(['user_id', 'technical_product_id', 'license_type']);

            // Drop license_type column
            $table->dropColumn('license_type');

            // Restore original unique constraint
            $table->unique(['user_id', 'technical_product_id']);
        });
    }
};
