<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tạo các indexes bổ sung để tối ưu performance cho hệ thống rating mới
     */
    public function up(): void
    {
        // Indexes cho showcase_ratings
        Schema::table('showcase_ratings', function (Blueprint $table) {
            // Index cho queries phổ biến
            $table->index(['overall_rating', 'has_media'], 'idx_ratings_quality_media');
            $table->index(['created_at', 'like_count'], 'idx_ratings_recent_popular');
        });

        // Indexes cho showcase_rating_replies
        Schema::table('showcase_rating_replies', function (Blueprint $table) {
            // Index cho thread queries
            $table->index(['rating_id', 'parent_id', 'like_count'], 'idx_replies_thread_popular');
        });

        // Indexes cho showcase_rating_likes
        Schema::table('showcase_rating_likes', function (Blueprint $table) {
            // Index cho user activity queries
            $table->index(['user_id', 'created_at'], 'idx_rating_likes_user_activity');
        });

        // Indexes cho showcase_rating_reply_likes
        Schema::table('showcase_rating_reply_likes', function (Blueprint $table) {
            // Index cho user activity queries
            $table->index(['user_id', 'created_at'], 'idx_reply_likes_user_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showcase_ratings', function (Blueprint $table) {
            $table->dropIndex('idx_ratings_quality_media');
            $table->dropIndex('idx_ratings_recent_popular');
        });

        Schema::table('showcase_rating_replies', function (Blueprint $table) {
            $table->dropIndex('idx_replies_thread_popular');
        });

        Schema::table('showcase_rating_likes', function (Blueprint $table) {
            $table->dropIndex('idx_rating_likes_user_activity');
        });

        Schema::table('showcase_rating_reply_likes', function (Blueprint $table) {
            $table->dropIndex('idx_reply_likes_user_activity');
        });
    }
};
