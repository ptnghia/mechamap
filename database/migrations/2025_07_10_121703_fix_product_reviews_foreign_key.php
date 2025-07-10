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
        // Drop the existing foreign key constraint
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Add new foreign key constraint to marketplace_products
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('marketplace_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the marketplace_products foreign key
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Restore original foreign key to products table
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
