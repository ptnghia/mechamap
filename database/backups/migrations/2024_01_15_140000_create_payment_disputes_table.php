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
        Schema::create('payment_disputes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('dispute_reference')->unique();
            
            // Related entities
            $table->foreignId('centralized_payment_id')->constrained('centralized_payments')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('marketplace_orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_email');
            
            // Dispute details
            $table->enum('dispute_type', [
                'chargeback',           // Bank/card chargeback
                'payment_not_received', // Customer claims payment not received
                'unauthorized',         // Unauthorized transaction
                'duplicate',           // Duplicate charge
                'product_not_received', // Product/service not delivered
                'product_defective',   // Product quality issues
                'service_issue',       // Service delivery problems
                'billing_error',       // Billing/amount error
                'other'                // Other disputes
            ]);
            
            $table->enum('status', [
                'pending',      // New dispute, awaiting review
                'investigating', // Under investigation
                'evidence_required', // Need more evidence
                'escalated',    // Escalated to payment gateway
                'resolved',     // Resolved in favor of merchant
                'lost',         // Lost dispute, refund issued
                'withdrawn',    // Customer withdrew dispute
                'expired'       // Dispute expired
            ])->default('pending');
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Financial details
            $table->decimal('disputed_amount', 15, 2);
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('VND');
            
            // Gateway information
            $table->string('gateway_dispute_id')->nullable(); // Stripe dispute ID, etc.
            $table->string('gateway_reason_code')->nullable();
            $table->json('gateway_response')->nullable();
            
            // Dispute content
            $table->text('customer_reason');
            $table->text('customer_description')->nullable();
            $table->json('customer_evidence')->nullable(); // Files, screenshots, etc.
            
            // Merchant response
            $table->text('merchant_response')->nullable();
            $table->json('merchant_evidence')->nullable();
            $table->timestamp('merchant_response_deadline')->nullable();
            
            // Admin handling
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->json('internal_notes')->nullable(); // Array of timestamped notes
            
            // Resolution
            $table->text('resolution_summary')->nullable();
            $table->enum('resolution_type', [
                'full_refund',
                'partial_refund', 
                'no_refund',
                'replacement',
                'store_credit',
                'other'
            ])->nullable();
            
            // Important timestamps
            $table->timestamp('dispute_date'); // When dispute was created
            $table->timestamp('gateway_deadline')->nullable(); // Gateway response deadline
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'priority']);
            $table->index(['dispute_type', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['dispute_date', 'status']);
            $table->index('gateway_dispute_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_disputes');
    }
};
