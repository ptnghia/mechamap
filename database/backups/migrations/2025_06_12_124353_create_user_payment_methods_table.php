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
        Schema::create('user_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Payment method info
            $table->enum('type', ['stripe_card', 'stripe_bank', 'vnpay', 'bank_account']);
            $table->string('gateway_payment_method_id')->nullable(); // ID từ payment gateway
            $table->string('name'); // Tên hiển thị (VD: "Visa **** 1234")

            // Card/Bank info (encrypted)
            $table->string('last_four')->nullable(); // 4 số cuối thẻ/tài khoản
            $table->string('brand')->nullable(); // Visa, Mastercard, etc.
            $table->string('exp_month')->nullable(); // Tháng hết hạn
            $table->string('exp_year')->nullable(); // Năm hết hạn
            $table->string('bank_name')->nullable(); // Tên ngân hàng

            // Settings
            $table->boolean('is_default')->default(false); // Phương thức mặc định
            $table->boolean('is_verified')->default(false); // Đã xác minh
            $table->boolean('is_active')->default(true); // Còn hoạt động

            // Metadata
            $table->json('metadata')->nullable(); // Thông tin bổ sung
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'is_default']);
            $table->index('gateway_payment_method_id');
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_payment_methods');
    }
};
