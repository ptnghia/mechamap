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
        Schema::table('threads', function (Blueprint $table) {
            // Activity Tracking States
            $table->timestamp('last_activity_at')->nullable()->after('updated_at');
            $table->timestamp('last_comment_at')->nullable()->after('last_activity_at');
            $table->unsignedBigInteger('last_comment_by')->nullable()->after('last_comment_at');
            $table->integer('bump_count')->default(0)->after('last_comment_by');
            $table->timestamp('last_bump_at')->nullable()->after('bump_count');

            // Enhanced Counters
            $table->integer('dislikes_count')->default(0)->after('view_count');
            $table->integer('bookmark_count')->default(0)->after('dislikes_count');
            $table->integer('share_count')->default(0)->after('bookmark_count');
            $table->integer('cached_comments_count')->default(0)->after('share_count');
            $table->integer('cached_participants_count')->default(0)->after('cached_comments_count');

            // SEO & Content Enhancement
            $table->text('meta_description')->nullable()->after('content');
            $table->text('search_keywords')->nullable()->after('meta_description');
            $table->integer('read_time')->nullable()->after('search_keywords'); // minutes

            // Foreign key
            $table->foreign('last_comment_by')->references('id')->on('users')->onDelete('set null');

            // Indexes cho performance
            $table->index(['last_activity_at']);
            $table->index(['last_comment_at']);
            $table->index(['last_bump_at']);
            $table->index(['cached_comments_count']);
            $table->index(['cached_participants_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['last_comment_by']);

            // Drop indexes
            $table->dropIndex(['last_activity_at']);
            $table->dropIndex(['last_comment_at']);
            $table->dropIndex(['last_bump_at']);
            $table->dropIndex(['cached_comments_count']);
            $table->dropIndex(['cached_participants_count']);

            // Drop columns
            $table->dropColumn([
                'last_activity_at',
                'last_comment_at',
                'last_comment_by',
                'bump_count',
                'last_bump_at',
                'dislikes_count',
                'bookmark_count',
                'share_count',
                'cached_comments_count',
                'cached_participants_count',
                'meta_description',
                'search_keywords',
                'read_time'
            ]);
        });
    }
};
