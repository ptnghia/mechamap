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
        Schema::create('payment_refunds', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('refund_reference')->unique();
            
            // Related entities
            $table->foreignId('centralized_payment_id')->constrained('centralized_payments')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('marketplace_orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dispute_id')->nullable()->constrained('payment_disputes')->onDelete('set null');
            
            // Refund details
            $table->enum('refund_type', [
                'full',         // Full order refund
                'partial',      // Partial refund
                'shipping',     // Shipping cost refund only
                'tax',          // Tax refund only
                'item',         // Specific item refund
                'goodwill',     // Goodwill/courtesy refund
                'chargeback',   // Chargeback refund
                'error'         // Error correction refund
            ]);
            
            $table->enum('reason', [
                'customer_request',     // Customer requested refund
                'product_defective',    // Product quality issues
                'wrong_item',          // Wrong item sent
                'not_delivered',       // Item not delivered
                'damaged_shipping',    // Damaged during shipping
                'duplicate_payment',   // Duplicate charge
                'billing_error',       // Billing error
                'fraud_prevention',    // Fraud prevention
                'dispute_resolution',  // Dispute resolution
                'goodwill',           // Goodwill gesture
                'admin_error',        // Admin/system error
                'other'               // Other reason
            ]);
            
            $table->enum('status', [
                'pending',      // Refund requested, awaiting approval
                'approved',     // Approved, awaiting processing
                'processing',   // Being processed by gateway
                'completed',    // Successfully refunded
                'failed',       // Refund failed
                'cancelled',    // Refund cancelled
                'rejected'      // Refund rejected
            ])->default('pending');
            
            // Financial details
            $table->decimal('original_amount', 15, 2); // Original payment amount
            $table->decimal('refund_amount', 15, 2);   // Amount to refund
            $table->decimal('gateway_fee', 15, 2)->default(0); // Gateway refund fee
            $table->decimal('net_refund', 15, 2);      // Net amount refunded
            $table->string('currency', 3)->default('VND');
            
            // Gateway information
            $table->string('payment_method'); // stripe, sepay, etc.
            $table->string('gateway_refund_id')->nullable(); // Gateway refund ID
            $table->json('gateway_response')->nullable();
            $table->text('gateway_error')->nullable();
            
            // Refund details
            $table->text('customer_reason')->nullable();
            $table->text('admin_reason')->nullable();
            $table->json('refund_items')->nullable(); // Specific items being refunded
            
            // Processing information
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->text('admin_notes')->nullable();
            $table->json('internal_notes')->nullable();
            
            // Seller impact
            $table->json('seller_adjustments')->nullable(); // How this affects seller payouts
            $table->boolean('adjust_seller_earnings')->default(true);
            $table->decimal('seller_deduction', 15, 2)->default(0);
            
            // Important timestamps
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            // Notification tracking
            $table->boolean('customer_notified')->default(false);
            $table->boolean('seller_notified')->default(false);
            $table->timestamp('customer_notified_at')->nullable();
            $table->timestamp('seller_notified_at')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'refund_type']);
            $table->index(['customer_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['requested_at', 'status']);
            $table->index('gateway_refund_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_refunds');
    }
};
