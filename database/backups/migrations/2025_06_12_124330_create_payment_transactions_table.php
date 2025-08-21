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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // ID giao dịch duy nhất
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Payment gateway info
            $table->enum('payment_method', ['stripe', 'vnpay', 'bank_transfer']);
            $table->string('gateway_transaction_id')->nullable(); // ID từ payment gateway
            $table->string('payment_intent_id')->nullable(); // Stripe payment intent
            $table->string('charge_id')->nullable(); // Stripe charge ID

            // Transaction details
            $table->enum('type', ['payment', 'refund', 'chargeback', 'fee']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled']);
            $table->decimal('amount', 12, 2); // Số tiền giao dịch
            $table->string('currency', 3)->default('VND'); // Đơn vị tiền tệ
            $table->decimal('fee_amount', 12, 2)->default(0); // Phí giao dịch
            $table->decimal('net_amount', 12, 2); // Số tiền thực nhận

            // Gateway response
            $table->json('gateway_response')->nullable(); // Response từ payment gateway
            $table->text('failure_reason')->nullable(); // Lý do thất bại
            $table->string('receipt_url')->nullable(); // URL receipt

            // Refund info (nếu có)
            $table->foreignId('refund_transaction_id')->nullable()->constrained('payment_transactions');
            $table->decimal('refunded_amount', 12, 2)->default(0);

            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index('gateway_transaction_id');
            $table->index('payment_intent_id');
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
