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
        Schema::create('seller_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('technical_product_id')->constrained()->onDelete('cascade');

            // Earnings breakdown
            $table->decimal('gross_amount', 12, 2); // Tổng tiền trước phí
            $table->decimal('platform_fee', 12, 2); // Phí platform (%)
            $table->decimal('payment_fee', 12, 2)->default(0); // Phí thanh toán
            $table->decimal('tax_amount', 12, 2)->default(0); // Thuế
            $table->decimal('net_amount', 12, 2); // Số tiền thực nhận

            // Platform fee details
            $table->decimal('platform_fee_rate', 5, 4); // Tỷ lệ phí platform (VD: 0.1500 = 15%)
            $table->decimal('payment_fee_rate', 5, 4)->default(0); // Tỷ lệ phí thanh toán

            // Payout info
            $table->enum('payout_status', ['pending', 'available', 'paid', 'failed'])->default('pending');
            $table->foreignId('payout_id')->nullable()->constrained('seller_payouts')->onDelete('set null');
            $table->timestamp('available_at')->nullable(); // Ngày có thể rút tiền
            $table->timestamp('paid_at')->nullable(); // Ngày đã thanh toán

            // Metadata
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['seller_id', 'payout_status']);
            $table->index(['seller_id', 'available_at']);
            $table->index(['order_item_id', 'payout_status']);
            $table->index(['technical_product_id', 'created_at']);
            $table->index('available_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_earnings');
    }
};
