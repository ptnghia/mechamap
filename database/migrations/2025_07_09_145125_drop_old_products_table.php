<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop old products table as part of marketplace restructuring
     */
    public function up(): void
    {
        // Backup data before dropping
        if (Schema::hasTable('products')) {
            $products = DB::table('products')->get();
            $backupFile = storage_path('app/backup_products_' . date('Y_m_d_H_i_s') . '.json');
            file_put_contents($backupFile, json_encode($products->toArray(), JSON_PRETTY_PRINT));

            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Drop dependent tables/constraints first
            if (Schema::hasTable('product_reviews')) {
                DB::table('product_reviews')->where('product_id', '>', 0)->delete();
            }

            // Drop the old products table
            Schema::dropIfExists('products');

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse the migrations.
     * Recreate basic products table structure if needed
     */
    public function down(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_category_id')->constrained()->onDelete('cascade');
            $table->enum('product_type', ['physical', 'digital', 'service', 'technical_file']);
            $table->enum('seller_type', ['supplier', 'manufacturer', 'brand']);
            $table->decimal('price', 12, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
