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
        Schema::create('typing_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User who is typing
            $table->string('context_type'); // Type of context (thread, comment, message, etc.)
            $table->unsignedBigInteger('context_id'); // ID of the context (thread_id, comment_id, etc.)
            $table->string('typing_type')->default('comment'); // Type of typing (comment, reply, message)
            $table->timestamp('started_at'); // When typing started
            $table->timestamp('last_activity_at'); // Last typing activity
            $table->timestamp('expires_at'); // When indicator expires
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes for performance
            $table->index(['context_type', 'context_id', 'expires_at']);
            $table->index(['user_id', 'last_activity_at']);
            $table->index('expires_at');

            // Unique constraint to prevent duplicate indicators
            $table->unique(['user_id', 'context_type', 'context_id', 'typing_type'], 'typing_unique_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('typing_indicators');
    }
};
