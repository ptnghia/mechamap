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
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('technical_products')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');

            // Transaction Details
            $table->string('purchase_token', 64)->unique(); // Unique per purchase
            $table->decimal('amount_paid', 10, 2);
            $table->string('currency', 3);
            $table->decimal('platform_fee', 10, 2);
            $table->decimal('seller_revenue', 10, 2);

            // Payment Information
            $table->string('payment_method', 50)->nullable(); // "card", "paypal", "bank_transfer"
            $table->string('payment_id')->nullable(); // Gateway transaction ID
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_gateway', 50)->nullable(); // "stripe", "paypal", "vnpay"

            // License & Access
            $table->enum('license_type', ['single_use', 'commercial', 'educational', 'unlimited'])->default('single_use');
            $table->string('license_key', 128)->unique();
            $table->integer('download_limit')->default(5); // Number of allowed downloads
            $table->integer('download_count')->default(0);
            $table->timestamp('expires_at')->nullable(); // License expiration

            // Download Security
            $table->string('download_token', 128)->unique(); // Changes after each download
            $table->timestamp('last_download_at')->nullable();
            $table->json('download_ip_addresses')->nullable(); // Track download IPs

            // Status & Tracking
            $table->enum('status', ['active', 'expired', 'revoked', 'refunded'])->default('active');
            $table->text('refund_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['buyer_id', 'status']);
            $table->index(['product_id', 'payment_status']);
            $table->index('purchase_token');
            $table->index('download_token');

            // Ensure one active purchase per user per product
            $table->unique(['product_id', 'buyer_id', 'status'], 'unique_active_purchase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_purchases');
    }
};
