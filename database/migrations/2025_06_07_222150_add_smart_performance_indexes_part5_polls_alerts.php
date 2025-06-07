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

        echo "🚀 Bắt đầu tạo performance indexes cho polls và alerts...\n";

        // Performance indexes cho polls table (nếu tồn tại)
        if (Schema::hasTable('polls')) {
            Schema::table('polls', function (Blueprint $table) use ($indexExists) {
                echo "⚡ Tạo indexes cho bảng polls...\n";

                // Index cho active polls
                if (Schema::hasColumn('polls', 'is_active') && Schema::hasColumn('polls', 'expires_at') && !$indexExists('polls', 'polls_active_index')) {
                    $table->index(['is_active', 'expires_at'], 'polls_active_index');
                    echo "  ✅ Tạo polls_active_index\n";
                } else {
                    echo "  ⏭️ polls_active_index đã tồn tại hoặc cột không có\n";
                }

                // Index cho thread polls
                if (Schema::hasColumn('polls', 'thread_id') && !$indexExists('polls', 'polls_thread_index')) {
                    $table->index(['thread_id'], 'polls_thread_index');
                    echo "  ✅ Tạo polls_thread_index\n";
                } else {
                    echo "  ⏭️ polls_thread_index đã tồn tại hoặc cột thread_id không có\n";
                }

                // Index cho user polls
                if (Schema::hasColumn('polls', 'user_id') && !$indexExists('polls', 'polls_user_created_index')) {
                    $table->index(['user_id', 'created_at'], 'polls_user_created_index');
                    echo "  ✅ Tạo polls_user_created_index\n";
                } else {
                    echo "  ⏭️ polls_user_created_index đã tồn tại hoặc cột user_id không có\n";
                }
            });
        } else {
            echo "⏭️ Bảng polls không tồn tại, bỏ qua...\n";
        }

        // Performance indexes cho alerts table (nếu tồn tại)
        if (Schema::hasTable('alerts')) {
            Schema::table('alerts', function (Blueprint $table) use ($indexExists) {
                echo "⚡ Tạo indexes cho bảng alerts...\n";

                // Index cho user alerts với trạng thái đọc
                if (Schema::hasColumn('alerts', 'is_read') && !$indexExists('alerts', 'alerts_user_read_index')) {
                    $table->index(['user_id', 'is_read', 'created_at'], 'alerts_user_read_index');
                    echo "  ✅ Tạo alerts_user_read_index\n";
                } else {
                    echo "  ⏭️ alerts_user_read_index đã tồn tại hoặc cột is_read không có\n";
                }

                // Index cho alert type filtering
                if (Schema::hasColumn('alerts', 'type') && !$indexExists('alerts', 'alerts_type_created_index')) {
                    $table->index(['type', 'created_at'], 'alerts_type_created_index');
                    echo "  ✅ Tạo alerts_type_created_index\n";
                } else {
                    echo "  ⏭️ alerts_type_created_index đã tồn tại hoặc cột type không có\n";
                }

                // Index cho unread alerts count
                if (Schema::hasColumn('alerts', 'is_read') && !$indexExists('alerts', 'alerts_unread_index')) {
                    $table->index(['user_id', 'is_read'], 'alerts_unread_index');
                    echo "  ✅ Tạo alerts_unread_index\n";
                } else {
                    echo "  ⏭️ alerts_unread_index đã tồn tại hoặc cột is_read không có\n";
                }
            });
        } else {
            echo "⏭️ Bảng alerts không tồn tại, bỏ qua...\n";
        }

        echo "🎉 Hoàn thành tạo indexes cho polls và alerts!\n";
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

        echo "🔄 Bắt đầu xóa performance indexes cho polls và alerts...\n";

        // Drop indexes từ alerts table
        if (Schema::hasTable('alerts')) {
            Schema::table('alerts', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('alerts', 'alerts_user_read_index')) {
                    $table->dropIndex('alerts_user_read_index');
                    echo "  ❌ Xóa alerts_user_read_index\n";
                }
                if ($indexExists('alerts', 'alerts_type_created_index')) {
                    $table->dropIndex('alerts_type_created_index');
                    echo "  ❌ Xóa alerts_type_created_index\n";
                }
                if ($indexExists('alerts', 'alerts_unread_index')) {
                    $table->dropIndex('alerts_unread_index');
                    echo "  ❌ Xóa alerts_unread_index\n";
                }
            });
        }

        // Drop indexes từ polls table
        if (Schema::hasTable('polls')) {
            Schema::table('polls', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('polls', 'polls_active_index')) {
                    $table->dropIndex('polls_active_index');
                    echo "  ❌ Xóa polls_active_index\n";
                }
                if ($indexExists('polls', 'polls_thread_index')) {
                    $table->dropIndex('polls_thread_index');
                    echo "  ❌ Xóa polls_thread_index\n";
                }
                if ($indexExists('polls', 'polls_user_created_index')) {
                    $table->dropIndex('polls_user_created_index');
                    echo "  ❌ Xóa polls_user_created_index\n";
                }
            });
        }

        echo "🎉 Hoàn thành xóa indexes cho polls và alerts!\n";
    }
};
