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
        // Profile Posts Table
        Schema::create('profile_posts', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['profile_id']);
        });

        // Showcase Comments Table
        Schema::create('showcase_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showcase_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('showcase_comments')->onDelete('cascade');
            $table->text('comment');
            $table->integer('like_count')->default(0);
            $table->timestamps();

            $table->index(['showcase_id', 'parent_id']);
            $table->index(['user_id']);
            $table->index(['showcase_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // Showcase Follows Table
        Schema::create('showcase_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['follower_id', 'following_id']);
            $table->index(['follower_id']);
            $table->index(['following_id']);
            $table->index(['follower_id', 'following_id']);
            $table->index(['following_id', 'created_at']);
        });

        // Showcase Likes Table
        Schema::create('showcase_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showcase_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['showcase_id', 'user_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_likes');
        Schema::dropIfExists('showcase_follows');
        Schema::dropIfExists('showcase_comments');
        Schema::dropIfExists('profile_posts');
    }
};
