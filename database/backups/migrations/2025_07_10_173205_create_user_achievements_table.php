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
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('achievement_id');
            $table->timestamp('unlocked_at');
            $table->json('progress_data')->nullable(); // Progress tracking data
            $table->integer('current_progress')->default(0); // Current progress value
            $table->integer('target_progress')->default(1); // Target progress value
            $table->boolean('is_notified')->default(false); // Whether user was notified
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('achievement_id')->references('id')->on('achievements')->onDelete('cascade');

            // Prevent duplicate achievements
            $table->unique(['user_id', 'achievement_id']);

            // Indexes for performance
            $table->index(['user_id', 'unlocked_at']);
            $table->index(['achievement_id', 'unlocked_at']);
            $table->index('is_notified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};
