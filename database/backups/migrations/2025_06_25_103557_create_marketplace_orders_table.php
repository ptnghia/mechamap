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
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('order_number')->unique();

            // Customer Information
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            // Order Details
            $table->enum('order_type', ['product_purchase', 'service_booking', 'digital_download'])->default('product_purchase');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 3)->default('VND');

            // Status Management
            $table->enum('status', [
                'pending',           // Chờ xử lý
                'confirmed',         // Đã xác nhận
                'processing',        // Đang xử lý
                'shipped',          // Đã gửi hàng
                'delivered',        // Đã giao hàng
                'completed',        // Hoàn thành
                'cancelled',        // Đã hủy
                'refunded'          // Đã hoàn tiền
            ])->default('pending');

            $table->enum('payment_status', [
                'pending',          // Chờ thanh toán
                'processing',       // Đang xử lý
                'paid',            // Đã thanh toán
                'failed',          // Thanh toán thất bại
                'refunded',        // Đã hoàn tiền
                'partially_refunded' // Hoàn tiền một phần
            ])->default('pending');

            // Shipping Information
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Payment Information
            $table->string('payment_method')->nullable(); // vnpay, momo, bank_transfer
            $table->string('payment_gateway_id')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Business Information (for B2B orders)
            $table->string('company_name')->nullable();
            $table->string('tax_code')->nullable();
            $table->boolean('requires_invoice')->default(false);
            $table->json('invoice_details')->nullable();

            // Order Notes & Communication
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Timestamps for workflow
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['customer_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index(['order_number']);
            $table->index(['created_at', 'status']);
            $table->index(['payment_method', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_orders');
    }
};
