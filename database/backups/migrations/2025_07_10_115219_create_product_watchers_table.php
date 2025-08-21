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
        Schema::create('product_watchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('marketplace_products')->onDelete('cascade');
            $table->decimal('target_price', 12, 2)->nullable(); // Alert when price drops to this level
            $table->boolean('notify_any_drop')->default(true); // Notify on any price drop
            $table->boolean('notify_stock_changes')->default(false); // Also notify stock changes
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate watchers
            $table->unique(['user_id', 'product_id']);

            // Indexes for performance
            $table->index(['product_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
            $table->index(['target_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_watchers');
    }
};
