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
        // Posts table - different from comments, for structured content
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['thread_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // Universal reactions system (polymorphic)
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // like, thanks, helpful, disagree, love, etc.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('reactable_id');
            $table->string('reactable_type');
            $table->timestamps();

            $table->unique(['user_id', 'reactable_id', 'reactable_type']);
            $table->index(['reactable_type', 'reactable_id']);
            $table->index(['user_id', 'type']);
            $table->index(['reactable_type', 'reactable_id', 'type']);
        });

        // Universal bookmarks system (polymorphic)
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('bookmarkable_id');
            $table->string('bookmarkable_type');
            $table->timestamps();

            $table->unique(['user_id', 'bookmarkable_id', 'bookmarkable_type']);
            $table->index(['bookmarkable_type', 'bookmarkable_id']);
            $table->index(['user_id', 'created_at']);
        });

        // Thread-specific bookmarks (for backward compatibility)
        Schema::create('thread_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'thread_id']);
            $table->index(['thread_id', 'created_at']);
        });

        // Thread follows for notifications
        Schema::create('thread_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'thread_id']);
            $table->index(['thread_id', 'created_at']);
        });

        // Thread-specific likes (legacy)
        Schema::create('thread_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'thread_id']);
            $table->index(['thread_id', 'created_at']);
        });

        // Thread rating system
        Schema::create('thread_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'thread_id']);
            $table->index(['thread_id', 'rating']);
            $table->index(['rating', 'created_at']);
        });

        // Thread saves (different from bookmarks)
        Schema::create('thread_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'thread_id']);
            $table->index(['user_id', 'created_at']);
        });

        // Comment likes (specific to comments)
        Schema::create('comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'comment_id']);
            $table->index(['comment_id', 'created_at']);
        });

        // Comment dislikes (specific to comments)
        Schema::create('comment_dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'comment_id']);
            $table->index(['comment_id', 'created_at']);
        });

        // User following system
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['follower_id', 'following_id']);
            $table->index(['following_id', 'created_at']);
            $table->index(['follower_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
        Schema::dropIfExists('comment_dislikes');
        Schema::dropIfExists('comment_likes');
        Schema::dropIfExists('thread_saves');
        Schema::dropIfExists('thread_ratings');
        Schema::dropIfExists('thread_likes');
        Schema::dropIfExists('thread_follows');
        Schema::dropIfExists('thread_bookmarks');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('posts');
    }
};
