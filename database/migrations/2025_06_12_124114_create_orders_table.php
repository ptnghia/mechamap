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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Mã đơn hàng duy nhất
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Order totals
            $table->decimal('subtotal', 12, 2); // Tổng tiền trước thuế và phí
            $table->decimal('tax_amount', 12, 2)->default(0); // Thuế VAT
            $table->decimal('processing_fee', 12, 2)->default(0); // Phí xử lý
            $table->decimal('discount_amount', 12, 2)->default(0); // Giảm giá
            $table->decimal('total_amount', 12, 2); // Tổng tiền cuối cùng

            // Payment info
            $table->enum('payment_status', [
                'pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'
            ])->default('pending');
            $table->enum('payment_method', ['stripe', 'vnpay', 'bank_transfer'])->nullable();
            $table->string('payment_intent_id')->nullable(); // Stripe payment intent ID
            $table->string('transaction_id')->nullable(); // ID giao dịch từ payment gateway

            // Order status
            $table->enum('status', [
                'pending', 'confirmed', 'processing', 'completed', 'cancelled', 'refunded'
            ])->default('pending');

            // Billing info
            $table->json('billing_address'); // Địa chỉ thanh toán
            $table->string('invoice_number')->nullable(); // Số hóa đơn

            // Metadata
            $table->json('metadata')->nullable(); // Thông tin bổ sung
            $table->text('notes')->nullable(); // Ghi chú đơn hàng

            // Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['payment_status', 'created_at']);
            $table->index('order_number');
            $table->index('payment_intent_id');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
