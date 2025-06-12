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
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('technical_product_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2); // Giá tại thời điểm thêm vào cart
            $table->decimal('total_price', 12, 2); // Tổng giá (quantity * unit_price)
            $table->json('product_snapshot')->nullable(); // Snapshot sản phẩm tại thời điểm thêm
            $table->enum('status', ['active', 'saved_for_later', 'expired'])->default('active');
            $table->timestamp('expires_at')->nullable(); // Cart có thể có thời hạn
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'technical_product_id']);
            $table->index('expires_at');

            // Unique constraint: user chỉ có thể thêm 1 sản phẩm 1 lần vào cart
            $table->unique(['user_id', 'technical_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_carts');
    }
};
