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
        Schema::create('marketplace_wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('marketplace_products')->onDelete('cascade');
            $table->boolean('notify_when_available')->default(true);
            $table->boolean('notify_price_drops')->default(true);
            $table->decimal('target_price', 12, 2)->nullable();
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate wishlist items
            $table->unique(['user_id', 'product_id']);

            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['product_id', 'notify_when_available']);
            $table->index(['notify_price_drops']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_wishlists');
    }
};
