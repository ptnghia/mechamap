<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Product reviews table for marketplace
     */
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Review Content
            $table->integer('rating')->comment('1-5 stars');
            $table->string('title')->nullable();
            $table->text('content');

            // Review Metadata
            $table->boolean('is_verified_purchase')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'rating']);
            $table->index(['user_id', 'created_at']);
            $table->unique(['product_id', 'user_id'], 'product_user_review_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
