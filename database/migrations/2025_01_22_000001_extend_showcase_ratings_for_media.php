<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Mở rộng bảng showcase_ratings để hỗ trợ:
     * - Hình ảnh đính kèm
     * - Hệ thống like
     * - Tối ưu indexes
     */
    public function up(): void
    {
        Schema::table('showcase_ratings', function (Blueprint $table) {
            // Thêm hỗ trợ media
            $table->boolean('has_media')->default(false)->after('review')
                ->comment('Có hình ảnh đính kèm không');
            
            $table->json('images')->nullable()->after('has_media')
                ->comment('Danh sách hình ảnh đính kèm (JSON array)');
            
            // Thêm hệ thống like
            $table->unsignedInteger('like_count')->default(0)->after('images')
                ->comment('Số lượt thích đánh giá này');
            
            // Thêm indexes để tối ưu performance
            $table->index(['showcase_id', 'like_count'], 'idx_ratings_showcase_likes');
            $table->index(['like_count', 'created_at'], 'idx_ratings_popular');
            $table->index(['has_media', 'created_at'], 'idx_ratings_with_media');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showcase_ratings', function (Blueprint $table) {
            // Xóa indexes trước
            $table->dropIndex('idx_ratings_showcase_likes');
            $table->dropIndex('idx_ratings_popular');
            $table->dropIndex('idx_ratings_with_media');
            
            // Xóa columns
            $table->dropColumn(['has_media', 'images', 'like_count']);
        });
    }
};
