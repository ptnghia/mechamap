<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tạo lại bảng forums với cấu trúc đúng cho MechaMap
     * Cấu trúc: Categories → Forums → Threads → Comments
     */
    public function up(): void
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Hierarchy relationships
            $table->foreignId('category_id')->nullable()
                ->comment('Danh mục mà forum thuộc về')
                ->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->nullable()
                ->comment('Forum cha (cho phân cấp forum con)');

            // Forum properties
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->boolean('is_private')->default(false)->comment('Forum riêng tư');

            // Statistics (cached for performance)
            $table->integer('thread_count')->default(0)->comment('Số lượng threads');
            $table->integer('post_count')->default(0)->comment('Tổng số posts');
            $table->timestamp('last_activity_at')->nullable()->comment('Hoạt động cuối cùng');
            $table->unsignedBigInteger('last_thread_id')->nullable()->comment('Thread mới nhất');
            $table->unsignedBigInteger('last_post_user_id')->nullable()->comment('User post cuối');

            // Forum moderation settings
            $table->boolean('requires_approval')->default(false)
                ->comment('Yêu cầu phê duyệt trước khi post');
            $table->json('allowed_thread_types')->nullable()
                ->comment('Loại thread được phép: ["discussion","question","tutorial"]');

            $table->timestamps();

            // Indexes for performance
            $table->index(['category_id', 'order', 'is_private'], 'forums_category_display');
            $table->index(['last_activity_at', 'thread_count'], 'forums_activity_stats');
            $table->index(['parent_id', 'order'], 'forums_hierarchy');

            // Foreign key constraints
            $table->foreign('parent_id')->references('id')->on('forums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forums');
    }
};
