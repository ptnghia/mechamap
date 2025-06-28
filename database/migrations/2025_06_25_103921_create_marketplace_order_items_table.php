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
        Schema::create('marketplace_order_items', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('order_id')->constrained('marketplace_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('marketplace_products')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('marketplace_sellers')->onDelete('cascade');

            // Product Information (snapshot at time of order)
            $table->string('product_name');
            $table->string('product_sku');
            $table->text('product_description')->nullable();
            $table->json('product_specifications')->nullable();

            // Pricing Information
            $table->decimal('unit_price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);

            // Digital Product Information
            $table->json('download_links')->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('download_limit')->nullable();
            $table->timestamp('download_expires_at')->nullable();

            // Fulfillment Status
            $table->enum('fulfillment_status', [
                'pending',          // Chờ xử lý
                'processing',       // Đang xử lý
                'ready_to_ship',    // Sẵn sàng gửi hàng
                'shipped',          // Đã gửi hàng
                'delivered',        // Đã giao hàng
                'downloaded',       // Đã tải xuống (digital)
                'completed',        // Hoàn thành
                'cancelled',        // Đã hủy
                'refunded'          // Đã hoàn tiền
            ])->default('pending');

            // Seller Commission
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->decimal('seller_earnings', 12, 2);

            // Tracking & Notes
            $table->string('tracking_number')->nullable();
            $table->text('seller_notes')->nullable();
            $table->text('customer_notes')->nullable();

            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'seller_id']);
            $table->index(['product_id', 'fulfillment_status']);
            $table->index(['seller_id', 'fulfillment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_order_items');
    }
};
