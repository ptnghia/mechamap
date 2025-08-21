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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('shopping_cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained('marketplace_products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2); // Price at time of adding to cart
            $table->decimal('sale_price', 12, 2)->nullable(); // Sale price if applicable
            $table->decimal('total_price', 12, 2); // quantity * unit_price
            $table->string('product_name'); // Store product name for historical purposes
            $table->string('product_sku')->nullable();
            $table->string('product_image')->nullable();
            $table->json('product_options')->nullable(); // For variants, customizations
            $table->json('metadata')->nullable(); // Additional item data
            $table->timestamps();

            // Indexes for performance
            $table->index(['shopping_cart_id', 'product_id']);
            $table->index('product_id');

            // Ensure unique product per cart (unless variants are different)
            $table->unique(['shopping_cart_id', 'product_id'], 'unique_cart_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
