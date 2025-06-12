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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('technical_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // Người bán

            // Product info at time of purchase
            $table->string('product_title'); // Tên sản phẩm tại thời điểm mua
            $table->text('product_description')->nullable(); // Mô tả sản phẩm
            $table->json('product_snapshot'); // Snapshot đầy đủ sản phẩm

            // Pricing
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2); // Giá đơn vị
            $table->decimal('total_price', 12, 2); // Tổng giá (quantity * unit_price)
            $table->decimal('seller_earnings', 12, 2); // Số tiền seller nhận được (sau phí)
            $table->decimal('platform_fee', 12, 2)->default(0); // Phí platform

            // License info
            $table->enum('license_type', ['single', 'commercial', 'extended'])->default('single');
            $table->json('license_terms')->nullable(); // Điều khoản license
            $table->timestamp('license_expires_at')->nullable(); // Ngày hết hạn license

            // Download info
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('download_limit')->nullable(); // Giới hạn download
            $table->timestamp('first_downloaded_at')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();

            // Status
            $table->enum('status', ['pending', 'active', 'expired', 'revoked'])->default('pending');

            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'technical_product_id']);
            $table->index(['seller_id', 'created_at']);
            $table->index(['technical_product_id', 'status']);
            $table->index('license_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
