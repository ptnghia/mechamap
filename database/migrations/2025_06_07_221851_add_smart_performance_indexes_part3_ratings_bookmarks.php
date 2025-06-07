<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper function để kiểm tra index tồn tại
        $indexExists = function (string $table, string $indexName): bool {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        };

        echo "🚀 Bắt đầu tạo performance indexes cho ratings và bookmarks...\n";

        // Performance indexes cho thread_ratings table
        if (Schema::hasTable('thread_ratings')) {
            Schema::table('thread_ratings', function (Blueprint $table) use ($indexExists) {
                echo "⚡ Tạo indexes cho bảng thread_ratings...\n";

                // Index cho thread rating aggregation
                if (!$indexExists('thread_ratings', 'thread_ratings_thread_rating_index')) {
                    $table->index(['thread_id', 'rating'], 'thread_ratings_thread_rating_index');
                    echo "  ✅ Tạo thread_ratings_thread_rating_index\n";
                } else {
                    echo "  ⏭️ thread_ratings_thread_rating_index đã tồn tại\n";
                }

                // Index cho user rating history
                if (!$indexExists('thread_ratings', 'thread_ratings_user_created_index')) {
                    $table->index(['user_id', 'created_at'], 'thread_ratings_user_created_index');
                    echo "  ✅ Tạo thread_ratings_user_created_index\n";
                } else {
                    echo "  ⏭️ thread_ratings_user_created_index đã tồn tại\n";
                }
            });
        } else {
            echo "⏭️ Bảng thread_ratings không tồn tại, bỏ qua...\n";
        }

        // Performance indexes cho thread_bookmarks table
        if (Schema::hasTable('thread_bookmarks')) {
            Schema::table('thread_bookmarks', function (Blueprint $table) use ($indexExists) {
                echo "⚡ Tạo indexes cho bảng thread_bookmarks...\n";

                // Index cho user bookmarks sorting
                if (!$indexExists('thread_bookmarks', 'thread_bookmarks_user_created_index')) {
                    $table->index(['user_id', 'created_at'], 'thread_bookmarks_user_created_index');
                    echo "  ✅ Tạo thread_bookmarks_user_created_index\n";
                } else {
                    echo "  ⏭️ thread_bookmarks_user_created_index đã tồn tại\n";
                }

                // Index cho thread bookmarks
                if (!$indexExists('thread_bookmarks', 'thread_bookmarks_thread_user_index')) {
                    $table->index(['thread_id', 'user_id'], 'thread_bookmarks_thread_user_index');
                    echo "  ✅ Tạo thread_bookmarks_thread_user_index\n";
                } else {
                    echo "  ⏭️ thread_bookmarks_thread_user_index đã tồn tại\n";
                }

                // Index cho folder bookmarks nếu có
                if (Schema::hasColumn('thread_bookmarks', 'folder') && !$indexExists('thread_bookmarks', 'thread_bookmarks_folder_index')) {
                    $table->index(['folder'], 'thread_bookmarks_folder_index');
                    echo "  ✅ Tạo thread_bookmarks_folder_index\n";
                } else {
                    echo "  ⏭️ thread_bookmarks_folder_index đã tồn tại hoặc cột folder không có\n";
                }
            });
        } else {
            echo "⏭️ Bảng thread_bookmarks không tồn tại, bỏ qua...\n";
        }

        echo "🎉 Hoàn thành tạo indexes cho ratings và bookmarks!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Helper function để kiểm tra index tồn tại trước khi drop
        $indexExists = function (string $table, string $indexName): bool {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        };

        echo "🔄 Bắt đầu xóa performance indexes cho ratings và bookmarks...\n";

        // Drop indexes từ thread_bookmarks table
        if (Schema::hasTable('thread_bookmarks')) {
            Schema::table('thread_bookmarks', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('thread_bookmarks', 'thread_bookmarks_user_created_index')) {
                    $table->dropIndex('thread_bookmarks_user_created_index');
                    echo "  ❌ Xóa thread_bookmarks_user_created_index\n";
                }
                if ($indexExists('thread_bookmarks', 'thread_bookmarks_thread_user_index')) {
                    $table->dropIndex('thread_bookmarks_thread_user_index');
                    echo "  ❌ Xóa thread_bookmarks_thread_user_index\n";
                }
                if ($indexExists('thread_bookmarks', 'thread_bookmarks_folder_index')) {
                    $table->dropIndex('thread_bookmarks_folder_index');
                    echo "  ❌ Xóa thread_bookmarks_folder_index\n";
                }
            });
        }

        // Drop indexes từ thread_ratings table
        if (Schema::hasTable('thread_ratings')) {
            Schema::table('thread_ratings', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('thread_ratings', 'thread_ratings_thread_rating_index')) {
                    $table->dropIndex('thread_ratings_thread_rating_index');
                    echo "  ❌ Xóa thread_ratings_thread_rating_index\n";
                }
                if ($indexExists('thread_ratings', 'thread_ratings_user_created_index')) {
                    $table->dropIndex('thread_ratings_user_created_index');
                    echo "  ❌ Xóa thread_ratings_user_created_index\n";
                }
            });
        }

        echo "🎉 Hoàn thành xóa indexes cho ratings và bookmarks!\n";
    }
};
