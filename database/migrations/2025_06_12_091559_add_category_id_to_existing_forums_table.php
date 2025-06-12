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
        Schema::table('forums', function (Blueprint $table) {
            // Add missing columns for the new forum structure
            $table->foreignId('category_id')->nullable()->after('description')
                ->comment('Danh mục mà forum thuộc về')
                ->constrained()->onDelete('cascade');

            $table->integer('thread_count')->default(0)->after('is_private')
                ->comment('Số lượng threads');

            $table->integer('post_count')->default(0)->after('thread_count')
                ->comment('Tổng số posts');

            $table->timestamp('last_activity_at')->nullable()->after('post_count')
                ->comment('Hoạt động cuối cùng');

            $table->foreignId('last_thread_id')->nullable()->after('last_activity_at')
                ->comment('Thread mới nhất');

            $table->foreignId('last_post_user_id')->nullable()->after('last_thread_id')
                ->comment('User post cuối');

            $table->boolean('requires_approval')->default(false)->after('last_post_user_id')
                ->comment('Yêu cầu phê duyệt trước khi post');

            $table->json('allowed_thread_types')->nullable()->after('requires_approval')
                ->comment('Loại thread được phép: ["discussion","question","tutorial"]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
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
