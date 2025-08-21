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
        // Polls table - thread polls/surveys
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->string('question');
            $table->integer('max_options')->default(1); // Max selections allowed
            $table->boolean('allow_change_vote')->default(true);
            $table->boolean('show_votes_publicly')->default(false);
            $table->boolean('allow_view_without_vote')->default(true);
            $table->timestamp('close_at')->nullable(); // Poll closing time
            $table->timestamps();

            $table->index(['thread_id']);
            $table->index(['close_at']);
        });

        // Poll options table - available answers
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->string('text'); // Option text
            $table->timestamps();

            $table->index(['poll_id']);
        });

        // Poll votes table - user votes on polls
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('poll_option_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Unique constraint ensures user can only vote once per poll per option
            $table->unique(['poll_id', 'poll_option_id', 'user_id']);
            $table->index(['poll_id', 'user_id']);
            $table->index(['poll_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
    }
};
