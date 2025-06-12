<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Thêm relationship Categories → Forums để tạo phân cấp đúng:
     * Categories (Danh mục lớn) → Forums (Diễn đàn con) → Threads (Bài đăng) → Comments
     */
    public function up(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            // Thêm category_id để forums thuộc về categories
            $table->foreignId('category_id')->nullable()->after('parent_id')
                ->comment('Danh mục mà forum thuộc về')
                ->constrained()->onDelete('cascade');

            // Thêm các trường tối ưu cho forum management
            $table->integer('thread_count')->default(0)->after('is_private')
                ->comment('Số lượng threads trong forum');
            $table->integer('post_count')->default(0)->after('thread_count')
                ->comment('Tổng số posts trong forum');
            $table->timestamp('last_activity_at')->nullable()->after('post_count')
                ->comment('Thời gian hoạt động cuối cùng');
            $table->unsignedBigInteger('last_thread_id')->nullable()->after('last_activity_at')
                ->comment('Thread mới nhất');
            $table->unsignedBigInteger('last_post_user_id')->nullable()->after('last_thread_id')
                ->comment('User post cuối cùng');

            // Thêm trường cho forum moderation
            $table->boolean('requires_approval')->default(false)->after('last_post_user_id')
                ->comment('Yêu cầu phê duyệt trước khi post');
            $table->json('allowed_thread_types')->nullable()->after('requires_approval')
                ->comment('Loại thread được phép: ["discussion","question","tutorial"]');

            // Indexes cho performance
            $table->index(['category_id', 'order', 'is_private'], 'forums_category_display');
            $table->index(['last_activity_at', 'thread_count'], 'forums_activity_stats');
        });

        // Add foreign key constraints
        Schema::table('forums', function (Blueprint $table) {
            $table->foreign('last_thread_id')->references('id')->on('threads')->onDelete('set null');
            $table->foreign('last_post_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['category_id']);
            $table->dropForeign(['last_thread_id']);
            $table->dropForeign(['last_post_user_id']);

            // Drop indexes
            $table->dropIndex('forums_category_display');
            $table->dropIndex('forums_activity_stats');

            // Drop columns
            $table->dropColumn([
                'category_id',
                'thread_count',
                'post_count',
                'last_activity_at',
                'last_thread_id',
                'last_post_user_id',
                'requires_approval',
                'allowed_thread_types'
            ]);
        });
    }
};
