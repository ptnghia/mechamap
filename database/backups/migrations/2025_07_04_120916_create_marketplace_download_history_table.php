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
        Schema::create('marketplace_download_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // User và Order information
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('marketplace_orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('marketplace_order_items')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('marketplace_products')->onDelete('cascade');

            // File information
            $table->string('file_name');
            $table->string('file_path');
            $table->string('original_filename');
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();

            // Download tracking
            $table->timestamp('downloaded_at');
            $table->string('ip_address', 45); // Support IPv6
            $table->string('user_agent')->nullable();
            $table->string('download_method')->default('direct'); // direct, api, token
            $table->string('download_token')->nullable();

            // Security và validation
            $table->boolean('is_valid_download')->default(true);
            $table->string('validation_status')->default('success'); // success, failed, expired, unauthorized
            $table->text('validation_notes')->nullable();

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'downloaded_at']);
            $table->index(['order_id', 'downloaded_at']);
            $table->index(['product_id', 'downloaded_at']);
            $table->index(['ip_address', 'downloaded_at']);
            $table->index('download_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_download_history');
    }
};
