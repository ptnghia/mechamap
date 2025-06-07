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
        // Index cho tìm kiếm và sắp xếp threads
        Schema::table('threads', function (Blueprint $table) {
            // Index cho tìm kiếm title
            $table->index(['title'], 'threads_title_search_index');

            // Index composite cho forum threads sắp xếp theo created_at
            $table->index(['forum_id', 'created_at'], 'threads_forum_created_index');

            // Index cho trending threads (view_count, average_rating)
            $table->index(['view_count', 'average_rating'], 'threads_trending_index');

            // Index cho sticky threads
            $table->index(['is_sticky', 'created_at'], 'threads_sticky_index');

            // Index cho featured threads
            $table->index(['is_featured', 'created_at'], 'threads_featured_index');
        });

        // Index cho comments performance
        Schema::table('comments', function (Blueprint $table) {
            // Index cho comments của thread sắp xếp theo thời gian
            $table->index(['thread_id', 'created_at'], 'comments_thread_created_index');

            // Index cho parent comments
            $table->index(['parent_id', 'created_at'], 'comments_parent_created_index');

            // Index cho user comments
            $table->index(['user_id', 'created_at'], 'comments_user_created_index');
        });

        // Index cho forums
        Schema::table('forums', function (Blueprint $table) {
            // Index cho forum hierarchy
            $table->index(['parent_id', 'order'], 'forums_parent_order_index');

            // Index cho searching forums
            $table->index(['name'], 'forums_name_search_index');
        });

        // Index cho users
        Schema::table('users', function (Blueprint $table) {
            // Index cho tìm kiếm user
            $table->index(['name'], 'users_name_search_index');
            $table->index(['username'], 'users_username_search_index');

            // Index cho user status và role
            $table->index(['status', 'role'], 'users_status_role_index');

            // Index cho last_seen_at (active users)
            $table->index(['last_seen_at'], 'users_last_seen_index');
        });

        // Index cho thread ratings
        Schema::table('thread_ratings', function (Blueprint $table) {
            // Index cho calculating average ratings
            $table->index(['thread_id', 'rating'], 'thread_ratings_thread_rating_index');

            // Index cho user ratings
            $table->index(['user_id', 'created_at'], 'thread_ratings_user_created_index');
        });

        // Index cho thread bookmarks
        Schema::table('thread_bookmarks', function (Blueprint $table) {
            // Index cho user bookmarks sắp xếp theo thời gian
            $table->index(['user_id', 'created_at'], 'thread_bookmarks_user_created_index');
        });

        // Index cho showcases
        Schema::table('showcases', function (Blueprint $table) {
            // Index cho polymorphic relationships
            $table->index(['showcaseable_type', 'showcaseable_id'], 'showcases_polymorphic_index');

            // Index cho user showcases sắp xếp theo thời gian
            $table->index(['user_id', 'created_at'], 'showcases_user_created_index');
        });

        // Index cho showcase comments
        Schema::table('showcase_comments', function (Blueprint $table) {
            // Index cho showcase comments sắp xếp theo thời gian
            $table->index(['showcase_id', 'created_at'], 'showcase_comments_showcase_created_index');

            // Index cho user showcase comments
            $table->index(['user_id', 'created_at'], 'showcase_comments_user_created_index');
        });

        // Index cho showcase likes
        Schema::table('showcase_likes', function (Blueprint $table) {
            // Index cho counting likes
            $table->index(['showcase_id'], 'showcase_likes_showcase_index');

            // Index cho user likes
            $table->index(['user_id', 'created_at'], 'showcase_likes_user_created_index');
        });

        // Index cho showcase follows
        Schema::table('showcase_follows', function (Blueprint $table) {
            // Index cho follower relationships
            $table->index(['user_id', 'followed_user_id'], 'showcase_follows_relationship_index');

            // Index cho following activity
            $table->index(['followed_user_id', 'created_at'], 'showcase_follows_followed_created_index');
        });

        // Index cho reactions
        Schema::table('reactions', function (Blueprint $table) {
            // Index cho polymorphic reactions
            $table->index(['reactable_type', 'reactable_id'], 'reactions_polymorphic_index');

            // Index cho user reactions
            $table->index(['user_id', 'created_at'], 'reactions_user_created_index');

            // Index cho reaction types
            $table->index(['type', 'created_at'], 'reactions_type_created_index');
        });

        // Index cho polls
        Schema::table('polls', function (Blueprint $table) {
            // Index cho thread polls
            $table->index(['thread_id'], 'polls_thread_index');

            // Index cho active polls
            $table->index(['close_at'], 'polls_close_at_index');
        });

        // Index cho alerts
        Schema::table('alerts', function (Blueprint $table) {
            // Index cho user alerts sắp xếp theo thời gian
            $table->index(['user_id', 'created_at'], 'alerts_user_created_index');

            // Index cho unread alerts
            $table->index(['user_id', 'read_at'], 'alerts_user_read_index');
        });

        // Index cho categories
        Schema::table('categories', function (Blueprint $table) {
            // Index cho category ordering
            $table->index(['order', 'name'], 'categories_order_name_index');

            // Index cho slug lookup
            $table->index(['slug'], 'categories_slug_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop threads indexes
        Schema::table('threads', function (Blueprint $table) {
            $table->dropIndex('threads_title_search_index');
            $table->dropIndex('threads_forum_created_index');
            $table->dropIndex('threads_trending_index');
            $table->dropIndex('threads_sticky_index');
            $table->dropIndex('threads_featured_index');
        });

        // Drop comments indexes
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_thread_created_index');
            $table->dropIndex('comments_parent_created_index');
            $table->dropIndex('comments_user_created_index');
        });

        // Drop forums indexes
        Schema::table('forums', function (Blueprint $table) {
            $table->dropIndex('forums_parent_order_index');
            $table->dropIndex('forums_name_search_index');
        });

        // Drop users indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_search_index');
            $table->dropIndex('users_username_search_index');
            $table->dropIndex('users_status_role_index');
            $table->dropIndex('users_last_seen_index');
        });

        // Drop thread_ratings indexes
        Schema::table('thread_ratings', function (Blueprint $table) {
            $table->dropIndex('thread_ratings_thread_rating_index');
            $table->dropIndex('thread_ratings_user_created_index');
        });

        // Drop thread_bookmarks indexes
        Schema::table('thread_bookmarks', function (Blueprint $table) {
            $table->dropIndex('thread_bookmarks_user_created_index');
        });

        // Drop showcases indexes
        Schema::table('showcases', function (Blueprint $table) {
            $table->dropIndex('showcases_polymorphic_index');
            $table->dropIndex('showcases_user_created_index');
        });

        // Drop showcase_comments indexes
        Schema::table('showcase_comments', function (Blueprint $table) {
            $table->dropIndex('showcase_comments_showcase_created_index');
            $table->dropIndex('showcase_comments_user_created_index');
        });

        // Drop showcase_likes indexes
        Schema::table('showcase_likes', function (Blueprint $table) {
            $table->dropIndex('showcase_likes_showcase_index');
            $table->dropIndex('showcase_likes_user_created_index');
        });

        // Drop showcase_follows indexes
        Schema::table('showcase_follows', function (Blueprint $table) {
            $table->dropIndex('showcase_follows_relationship_index');
            $table->dropIndex('showcase_follows_followed_created_index');
        });

        // Drop reactions indexes
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropIndex('reactions_polymorphic_index');
            $table->dropIndex('reactions_user_created_index');
            $table->dropIndex('reactions_type_created_index');
        });

        // Drop polls indexes
        Schema::table('polls', function (Blueprint $table) {
            $table->dropIndex('polls_thread_index');
            $table->dropIndex('polls_close_at_index');
        });

        // Drop alerts indexes
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropIndex('alerts_user_created_index');
            $table->dropIndex('alerts_user_read_index');
        });

        // Drop categories indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_order_name_index');
            $table->dropIndex('categories_slug_index');
        });
    }
};
