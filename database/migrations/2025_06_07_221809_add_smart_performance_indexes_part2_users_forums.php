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

        echo "🚀 Bắt đầu tạo performance indexes cho users và forums...\n";

        // Performance indexes cho users table
        Schema::table('users', function (Blueprint $table) use ($indexExists) {
            echo "⚡ Tạo indexes cho bảng users...\n";

            // Index cho tìm kiếm user theo name
            if (!$indexExists('users', 'users_name_search_index')) {
                $table->index(['name'], 'users_name_search_index');
                echo "  ✅ Tạo users_name_search_index\n";
            } else {
                echo "  ⏭️ users_name_search_index đã tồn tại\n";
            }

            // Index cho tìm kiếm user theo username (nếu có)
            if (Schema::hasColumn('users', 'username') && !$indexExists('users', 'users_username_search_index')) {
                $table->index(['username'], 'users_username_search_index');
                echo "  ✅ Tạo users_username_search_index\n";
            } else {
                echo "  ⏭️ users_username_search_index đã tồn tại hoặc cột username không có\n";
            }

            // Index cho filter user theo status và role
            if (Schema::hasColumn('users', 'status') && !$indexExists('users', 'users_status_role_index')) {
                $table->index(['status', 'role'], 'users_status_role_index');
                echo "  ✅ Tạo users_status_role_index\n";
            } else {
                echo "  ⏭️ users_status_role_index đã tồn tại hoặc cột status không có\n";
            }

            // Index cho last seen tracking
            if (Schema::hasColumn('users', 'last_seen_at') && !$indexExists('users', 'users_last_seen_index')) {
                $table->index(['last_seen_at'], 'users_last_seen_index');
                echo "  ✅ Tạo users_last_seen_index\n";
            } else {
                echo "  ⏭️ users_last_seen_index đã tồn tại hoặc cột last_seen_at không có\n";
            }
        });

        // Performance indexes cho forums table (nếu tồn tại)
        if (Schema::hasTable('forums')) {
            Schema::table('forums', function (Blueprint $table) use ($indexExists) {
                echo "⚡ Tạo indexes cho bảng forums...\n";

                // Index cho forum hierarchy
                if (Schema::hasColumn('forums', 'parent_id') && Schema::hasColumn('forums', 'order') && !$indexExists('forums', 'forums_parent_order_index')) {
                    $table->index(['parent_id', 'order'], 'forums_parent_order_index');
                    echo "  ✅ Tạo forums_parent_order_index\n";
                } else {
                    echo "  ⏭️ forums_parent_order_index đã tồn tại hoặc cột không có\n";
                }

                // Index cho searching forums
                if (!$indexExists('forums', 'forums_name_search_index')) {
                    $table->index(['name'], 'forums_name_search_index');
                    echo "  ✅ Tạo forums_name_search_index\n";
                } else {
                    echo "  ⏭️ forums_name_search_index đã tồn tại\n";
                }
            });
        } else {
            echo "⏭️ Bảng forums không tồn tại, bỏ qua...\n";
        }

        echo "🎉 Hoàn thành tạo indexes cho users và forums!\n";
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

        echo "🔄 Bắt đầu xóa performance indexes cho users và forums...\n";

        // Drop indexes từ forums table
        if (Schema::hasTable('forums')) {
            Schema::table('forums', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('forums', 'forums_parent_order_index')) {
                    $table->dropIndex('forums_parent_order_index');
                    echo "  ❌ Xóa forums_parent_order_index\n";
                }
                if ($indexExists('forums', 'forums_name_search_index')) {
                    $table->dropIndex('forums_name_search_index');
                    echo "  ❌ Xóa forums_name_search_index\n";
                }
            });
        }

        // Drop indexes từ users table
        Schema::table('users', function (Blueprint $table) use ($indexExists) {
            if ($indexExists('users', 'users_name_search_index')) {
                $table->dropIndex('users_name_search_index');
                echo "  ❌ Xóa users_name_search_index\n";
            }
            if ($indexExists('users', 'users_username_search_index')) {
                $table->dropIndex('users_username_search_index');
                echo "  ❌ Xóa users_username_search_index\n";
            }
            if ($indexExists('users', 'users_status_role_index')) {
                $table->dropIndex('users_status_role_index');
                echo "  ❌ Xóa users_status_role_index\n";
            }
            if ($indexExists('users', 'users_last_seen_index')) {
                $table->dropIndex('users_last_seen_index');
                echo "  ❌ Xóa users_last_seen_index\n";
            }
        });

        echo "🎉 Hoàn thành xóa indexes cho users và forums!\n";
    }
};
