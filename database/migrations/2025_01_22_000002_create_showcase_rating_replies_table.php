<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tạo bảng showcase_rating_replies để hỗ trợ:
     * - Trả lời đánh giá
     * - Nested replies (replies của replies)
     * - Hình ảnh trong replies
     * - Like system cho replies
     */
    public function up(): void
    {
        Schema::create('showcase_rating_replies', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('rating_id')
                ->constrained('showcase_ratings')
                ->onDelete('cascade')
                ->comment('ID của đánh giá được trả lời');
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ID của user trả lời');
            
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('showcase_rating_replies')
                ->onDelete('cascade')
                ->comment('ID của reply cha (cho nested replies)');
            
            // Content
            $table->text('content')
                ->comment('Nội dung trả lời');
            
            // Media support
            $table->boolean('has_media')->default(false)
                ->comment('Có hình ảnh đính kèm không');
            
            $table->json('images')->nullable()
                ->comment('Danh sách hình ảnh đính kèm (JSON array)');
            
            // Engagement
            $table->unsignedInteger('like_count')->default(0)
                ->comment('Số lượt thích reply này');
            
            $table->timestamps();
            
            // Indexes để tối ưu performance
            $table->index(['rating_id', 'created_at'], 'idx_replies_rating_time');
            $table->index(['user_id', 'created_at'], 'idx_replies_user_time');
            $table->index(['parent_id', 'created_at'], 'idx_replies_nested');
            $table->index(['like_count', 'created_at'], 'idx_replies_popular');
            $table->index(['has_media', 'created_at'], 'idx_replies_with_media');
            
            // Composite indexes cho queries phức tạp
            $table->index(['rating_id', 'parent_id', 'created_at'], 'idx_replies_hierarchy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_rating_replies');
    }
};
