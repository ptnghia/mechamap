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

        echo "🚀 Bắt đầu tạo performance indexes cho threads và comments...\n";

        // Performance indexes cho threads table
        Schema::table('threads', function (Blueprint $table) use ($indexExists) {
            echo "⚡ Tạo indexes cho bảng threads...\n";

            // Index cho tìm kiếm title
            if (!$indexExists('threads', 'threads_title_search_index')) {
                $table->index(['title'], 'threads_title_search_index');
                echo "  ✅ Tạo threads_title_search_index\n";
            } else {
                echo "  ⏭️ threads_title_search_index đã tồn tại\n";
            }

            // Index composite cho forum threads sắp xếp theo created_at
            if (!$indexExists('threads', 'threads_forum_created_index')) {
                $table->index(['forum_id', 'created_at'], 'threads_forum_created_index');
                echo "  ✅ Tạo threads_forum_created_index\n";
            } else {
                echo "  ⏭️ threads_forum_created_index đã tồn tại\n";
            }

            // Index cho trending threads (view_count, average_rating)
            if (!$indexExists('threads', 'threads_trending_index')) {
                $table->index(['view_count', 'average_rating'], 'threads_trending_index');
                echo "  ✅ Tạo threads_trending_index\n";
            } else {
                echo "  ⏭️ threads_trending_index đã tồn tại\n";
            }

            // Index cho sticky threads
            if (!$indexExists('threads', 'threads_sticky_index')) {
                $table->index(['is_sticky', 'created_at'], 'threads_sticky_index');
                echo "  ✅ Tạo threads_sticky_index\n";
            } else {
                echo "  ⏭️ threads_sticky_index đã tồn tại\n";
            }

            // Index cho featured threads
            if (!$indexExists('threads', 'threads_featured_index')) {
                $table->index(['is_featured', 'created_at'], 'threads_featured_index');
                echo "  ✅ Tạo threads_featured_index\n";
            } else {
                echo "  ⏭️ threads_featured_index đã tồn tại\n";
            }
        });

        // Performance indexes cho comments table
        Schema::table('comments', function (Blueprint $table) use ($indexExists) {
            echo "⚡ Tạo indexes cho bảng comments...\n";

            // Index cho comments của thread sắp xếp theo thời gian
            if (!$indexExists('comments', 'comments_thread_created_index')) {
                $table->index(['thread_id', 'created_at'], 'comments_thread_created_index');
                echo "  ✅ Tạo comments_thread_created_index\n";
            } else {
                echo "  ⏭️ comments_thread_created_index đã tồn tại\n";
            }

            // Index cho parent comments
            if (!$indexExists('comments', 'comments_parent_created_index')) {
                $table->index(['parent_id', 'created_at'], 'comments_parent_created_index');
                echo "  ✅ Tạo comments_parent_created_index\n";
            } else {
                echo "  ⏭️ comments_parent_created_index đã tồn tại\n";
            }

            // Index cho user comments
            if (!$indexExists('comments', 'comments_user_created_index')) {
                $table->index(['user_id', 'created_at'], 'comments_user_created_index');
                echo "  ✅ Tạo comments_user_created_index\n";
            } else {
                echo "  ⏭️ comments_user_created_index đã tồn tại\n";
            }
        });

        echo "🎉 Hoàn thành tạo indexes cho threads và comments!\n";
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

        echo "🔄 Bắt đầu xóa performance indexes cho threads và comments...\n";

        // Drop indexes từ comments table
        Schema::table('comments', function (Blueprint $table) use ($indexExists) {
            if ($indexExists('comments', 'comments_thread_created_index')) {
                $table->dropIndex('comments_thread_created_index');
                echo "  ❌ Xóa comments_thread_created_index\n";
            }
            if ($indexExists('comments', 'comments_parent_created_index')) {
                $table->dropIndex('comments_parent_created_index');
                echo "  ❌ Xóa comments_parent_created_index\n";
            }
            if ($indexExists('comments', 'comments_user_created_index')) {
                $table->dropIndex('comments_user_created_index');
                echo "  ❌ Xóa comments_user_created_index\n";
            }
        });

        // Drop indexes từ threads table
        Schema::table('threads', function (Blueprint $table) use ($indexExists) {
            if ($indexExists('threads', 'threads_title_search_index')) {
                $table->dropIndex('threads_title_search_index');
                echo "  ❌ Xóa threads_title_search_index\n";
            }
            if ($indexExists('threads', 'threads_forum_created_index')) {
                $table->dropIndex('threads_forum_created_index');
                echo "  ❌ Xóa threads_forum_created_index\n";
            }
            if ($indexExists('threads', 'threads_trending_index')) {
                $table->dropIndex('threads_trending_index');
                echo "  ❌ Xóa threads_trending_index\n";
            }
            if ($indexExists('threads', 'threads_sticky_index')) {
                $table->dropIndex('threads_sticky_index');
                echo "  ❌ Xóa threads_sticky_index\n";
            }
            if ($indexExists('threads', 'threads_featured_index')) {
                $table->dropIndex('threads_featured_index');
                echo "  ❌ Xóa threads_featured_index\n";
            }
        });

        echo "🎉 Hoàn thành xóa indexes cho threads và comments!\n";
    }
};
