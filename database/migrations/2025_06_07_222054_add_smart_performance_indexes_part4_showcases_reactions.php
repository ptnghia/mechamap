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

        echo "🚀 Bắt đầu tạo performance indexes cho showcases và reactions...\n";

        // Performance indexes cho showcases table
        if (Schema::hasTable('showcases')) {
            Schema::table('showcases', function (Blueprint $table) use ($indexExists) {
                echo "⚡ Tạo indexes cho bảng showcases...\n";

                // Index cho polymorphic relationship
                if (!$indexExists('showcases', 'showcases_polymorphic_index')) {
                    $table->index(['showcaseable_type', 'showcaseable_id'], 'showcases_polymorphic_index');
                    echo "  ✅ Tạo showcases_polymorphic_index\n";
                } else {
                    echo "  ⏭️ showcases_polymorphic_index đã tồn tại\n";
                }

                // Index cho user showcases sorting
                if (!$indexExists('showcases', 'showcases_user_created_index')) {
                    $table->index(['user_id', 'created_at'], 'showcases_user_created_index');
                    echo "  ✅ Tạo showcases_user_created_index\n";
                } else {
                    echo "  ⏭️ showcases_user_created_index đã tồn tại\n";
                }

                // Index cho showcase order
                if (Schema::hasColumn('showcases', 'order') && !$indexExists('showcases', 'showcases_order_index')) {
                    $table->index(['order'], 'showcases_order_index');
                    echo "  ✅ Tạo showcases_order_index\n";
                } else {
                    echo "  ⏭️ showcases_order_index đã tồn tại hoặc cột order không có\n";
                }
            });
        } else {
            echo "⏭️ Bảng showcases không tồn tại, bỏ qua...\n";
        }

        // Performance indexes cho showcase_comments table (nếu tồn tại)
        if (Schema::hasTable('showcase_comments')) {
            Schema::table('showcase_comments', function (Blueprint $table) use ($indexExists) {
                echo "⚡ Tạo indexes cho bảng showcase_comments...\n";

                // Index cho showcase comments sorting
                if (!$indexExists('showcase_comments', 'showcase_comments_showcase_created_index')) {
                    $table->index(['showcase_id', 'created_at'], 'showcase_comments_showcase_created_index');
                    echo "  ✅ Tạo showcase_comments_showcase_created_index\n";
                } else {
                    echo "  ⏭️ showcase_comments_showcase_created_index đã tồn tại\n";
                }

                // Index cho user comments
                if (!$indexExists('showcase_comments', 'showcase_comments_user_created_index')) {
                    $table->index(['user_id', 'created_at'], 'showcase_comments_user_created_index');
                    echo "  ✅ Tạo showcase_comments_user_created_index\n";
                } else {
                    echo "  ⏭️ showcase_comments_user_created_index đã tồn tại\n";
                }
            });
        } else {
            echo "⏭️ Bảng showcase_comments không tồn tại, bỏ qua...\n";
        }

        // Performance indexes cho reactions table
        if (Schema::hasTable('reactions')) {
            Schema::table('reactions', function (Blueprint $table) use ($indexExists) {
                echo "⚡ Tạo indexes cho bảng reactions...\n";

                // Index cho polymorphic reactions
                if (!$indexExists('reactions', 'reactions_polymorphic_index')) {
                    $table->index(['reactable_type', 'reactable_id'], 'reactions_polymorphic_index');
                    echo "  ✅ Tạo reactions_polymorphic_index\n";
                } else {
                    echo "  ⏭️ reactions_polymorphic_index đã tồn tại\n";
                }

                // Index cho user reactions với type
                if (!$indexExists('reactions', 'reactions_user_type_index')) {
                    $table->index(['user_id', 'type'], 'reactions_user_type_index');
                    echo "  ✅ Tạo reactions_user_type_index\n";
                } else {
                    echo "  ⏭️ reactions_user_type_index đã tồn tại\n";
                }

                // Index cho reactions counting by type
                if (!$indexExists('reactions', 'reactions_polymorphic_type_index')) {
                    $table->index(['reactable_type', 'reactable_id', 'type'], 'reactions_polymorphic_type_index');
                    echo "  ✅ Tạo reactions_polymorphic_type_index\n";
                } else {
                    echo "  ⏭️ reactions_polymorphic_type_index đã tồn tại\n";
                }
            });
        } else {
            echo "⏭️ Bảng reactions không tồn tại, bỏ qua...\n";
        }

        echo "🎉 Hoàn thành tạo indexes cho showcases và reactions!\n";
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

        echo "🔄 Bắt đầu xóa performance indexes cho showcases và reactions...\n";

        // Drop indexes từ reactions table
        if (Schema::hasTable('reactions')) {
            Schema::table('reactions', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('reactions', 'reactions_polymorphic_index')) {
                    $table->dropIndex('reactions_polymorphic_index');
                    echo "  ❌ Xóa reactions_polymorphic_index\n";
                }
                if ($indexExists('reactions', 'reactions_user_type_index')) {
                    $table->dropIndex('reactions_user_type_index');
                    echo "  ❌ Xóa reactions_user_type_index\n";
                }
                if ($indexExists('reactions', 'reactions_polymorphic_type_index')) {
                    $table->dropIndex('reactions_polymorphic_type_index');
                    echo "  ❌ Xóa reactions_polymorphic_type_index\n";
                }
            });
        }

        // Drop indexes từ showcase_comments table
        if (Schema::hasTable('showcase_comments')) {
            Schema::table('showcase_comments', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('showcase_comments', 'showcase_comments_showcase_created_index')) {
                    $table->dropIndex('showcase_comments_showcase_created_index');
                    echo "  ❌ Xóa showcase_comments_showcase_created_index\n";
                }
                if ($indexExists('showcase_comments', 'showcase_comments_user_created_index')) {
                    $table->dropIndex('showcase_comments_user_created_index');
                    echo "  ❌ Xóa showcase_comments_user_created_index\n";
                }
            });
        }

        // Drop indexes từ showcases table
        if (Schema::hasTable('showcases')) {
            Schema::table('showcases', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('showcases', 'showcases_polymorphic_index')) {
                    $table->dropIndex('showcases_polymorphic_index');
                    echo "  ❌ Xóa showcases_polymorphic_index\n";
                }
                if ($indexExists('showcases', 'showcases_user_created_index')) {
                    $table->dropIndex('showcases_user_created_index');
                    echo "  ❌ Xóa showcases_user_created_index\n";
                }
                if ($indexExists('showcases', 'showcases_order_index')) {
                    $table->dropIndex('showcases_order_index');
                    echo "  ❌ Xóa showcases_order_index\n";
                }
            });
        }

        echo "🎉 Hoàn thành xóa indexes cho showcases và reactions!\n";
    }
};
