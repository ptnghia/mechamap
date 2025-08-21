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
        Schema::create('seller_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payout_id')->unique(); // ID payout duy nhất
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');

            // Payout details
            $table->decimal('total_amount', 12, 2); // Tổng số tiền payout
            $table->string('currency', 3)->default('VND');
            $table->unsignedInteger('earnings_count'); // Số lượng earnings trong payout này

            // Date range
            $table->date('period_start'); // Ngày bắt đầu kỳ payout
            $table->date('period_end'); // Ngày kết thúc kỳ payout

            // Payout method
            $table->enum('payout_method', ['bank_transfer', 'stripe_transfer', 'paypal']);
            $table->json('payout_details'); // Chi tiết tài khoản nhận (encrypted)

            // Status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled']);
            $table->text('failure_reason')->nullable();
            $table->string('transaction_reference')->nullable(); // Mã tham chiếu giao dịch

            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['seller_id', 'status']);
            $table->index(['seller_id', 'period_start', 'period_end']);
            $table->index(['status', 'created_at']);
            $table->index('payout_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_payouts');
    }
};
