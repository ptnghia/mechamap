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
        // Notifications table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type', 50)->index();
                $table->string('title');
                $table->text('message');
                $table->json('data')->nullable();
                $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'is_read']);
                $table->index(['type', 'created_at']);
            });
        }

        // B2B Quotes table
        if (!Schema::hasTable('b2b_quotes')) {
            Schema::create('b2b_quotes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
                $table->string('title');
                $table->text('description');
                $table->integer('quantity');
                $table->json('specifications')->nullable();
                $table->json('delivery_requirements')->nullable();
                $table->string('budget_range')->nullable();
                $table->date('deadline')->nullable();
                $table->enum('status', ['pending', 'quoted', 'accepted', 'rejected', 'completed'])->default('pending');
                $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
                $table->decimal('quoted_amount', 15, 2)->nullable();
                $table->decimal('final_amount', 15, 2)->nullable();
                $table->text('seller_notes')->nullable();
                $table->timestamp('quoted_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamps();
                
                $table->index(['buyer_id', 'status']);
                $table->index(['seller_id', 'status']);
                $table->index('deadline');
            });
        }

        // Commissions table
        if (!Schema::hasTable('commissions')) {
            Schema::create('commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
                $table->string('transaction_type', 50);
                $table->decimal('gross_amount', 15, 2);
                $table->decimal('commission_rate', 5, 2);
                $table->decimal('commission_amount', 15, 2);
                $table->decimal('seller_earnings', 15, 2);
                $table->string('currency', 3)->default('VND');
                $table->enum('status', ['pending', 'calculated', 'paid'])->default('pending');
                $table->string('period', 7); // YYYY-MM format
                $table->json('metadata')->nullable();
                $table->timestamp('calculated_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
                
                $table->index(['seller_id', 'status']);
                $table->index(['period', 'status']);
                $table->index('transaction_type');
            });
        }

        // User Verification Documents table
        if (!Schema::hasTable('user_verification_documents')) {
            Schema::create('user_verification_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('document_type', 50);
                $table->string('original_name');
                $table->string('file_path');
                $table->bigInteger('file_size');
                $table->string('mime_type');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
                $table->index('document_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_verification_documents');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('b2b_quotes');
        Schema::dropIfExists('notifications');
    }
};
