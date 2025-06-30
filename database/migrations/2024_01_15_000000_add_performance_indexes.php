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
        // Users table indexes for performance
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['email'], 'users_email_performance_idx');
                $table->index(['username'], 'users_username_performance_idx');
                $table->index(['role'], 'users_role_performance_idx');
                $table->index(['created_at'], 'users_created_at_performance_idx');
                $table->index(['last_activity_at'], 'users_last_activity_performance_idx');
                $table->index(['email_verified_at'], 'users_email_verified_performance_idx');
                $table->index(['role', 'created_at'], 'users_role_created_composite_idx');
            });
        }

        // Threads table indexes
        if (Schema::hasTable('threads')) {
            Schema::table('threads', function (Blueprint $table) {
                $table->index(['user_id'], 'threads_user_id_performance_idx');
                $table->index(['category_id'], 'threads_category_id_performance_idx');
                $table->index(['forum_id'], 'threads_forum_id_performance_idx');
                $table->index(['created_at'], 'threads_created_at_performance_idx');
                $table->index(['updated_at'], 'threads_updated_at_performance_idx');
                $table->index(['view_count'], 'threads_view_count_performance_idx');
                $table->index(['is_pinned', 'created_at'], 'threads_pinned_created_composite_idx');
                $table->index(['status', 'created_at'], 'threads_status_created_composite_idx');
            });
        }

        // Posts table indexes
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->index(['thread_id'], 'posts_thread_id_performance_idx');
                $table->index(['user_id'], 'posts_user_id_performance_idx');
                $table->index(['created_at'], 'posts_created_at_performance_idx');
                $table->index(['thread_id', 'created_at'], 'posts_thread_created_composite_idx');
            });
        }

        // Marketplace products indexes
        if (Schema::hasTable('marketplace_products')) {
            Schema::table('marketplace_products', function (Blueprint $table) {
                $table->index(['user_id'], 'marketplace_products_user_id_performance_idx');
                $table->index(['category_id'], 'marketplace_products_category_id_performance_idx');
                $table->index(['status'], 'marketplace_products_status_performance_idx');
                $table->index(['price'], 'marketplace_products_price_performance_idx');
                $table->index(['created_at'], 'marketplace_products_created_at_performance_idx');
                $table->index(['view_count'], 'marketplace_products_view_count_performance_idx');
                $table->index(['status', 'created_at'], 'marketplace_products_status_created_composite_idx');
                $table->index(['category_id', 'status'], 'marketplace_products_category_status_composite_idx');
                $table->index(['price', 'status'], 'marketplace_products_price_status_composite_idx');
            });
        }

        // Marketplace orders indexes
        if (Schema::hasTable('marketplace_orders')) {
            Schema::table('marketplace_orders', function (Blueprint $table) {
                $table->index(['user_id'], 'marketplace_orders_user_id_performance_idx');
                $table->index(['seller_id'], 'marketplace_orders_seller_id_performance_idx');
                $table->index(['status'], 'marketplace_orders_status_performance_idx');
                $table->index(['created_at'], 'marketplace_orders_created_at_performance_idx');
                $table->index(['total_amount'], 'marketplace_orders_total_amount_performance_idx');
                $table->index(['user_id', 'status'], 'marketplace_orders_user_status_composite_idx');
                $table->index(['seller_id', 'status'], 'marketplace_orders_seller_status_composite_idx');
                $table->index(['status', 'created_at'], 'marketplace_orders_status_created_composite_idx');
            });
        }

        // Notifications indexes
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['user_id'], 'notifications_user_id_performance_idx');
                $table->index(['read_at'], 'notifications_read_at_performance_idx');
                $table->index(['created_at'], 'notifications_created_at_performance_idx');
                $table->index(['type'], 'notifications_type_performance_idx');
                $table->index(['user_id', 'read_at'], 'notifications_user_read_composite_idx');
                $table->index(['user_id', 'created_at'], 'notifications_user_created_composite_idx');
            });
        }

        // Categories indexes
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->index(['parent_id'], 'categories_parent_id_performance_idx');
                $table->index(['slug'], 'categories_slug_performance_idx');
                $table->index(['sort_order'], 'categories_sort_order_performance_idx');
                $table->index(['is_active'], 'categories_is_active_performance_idx');
            });
        }

        // Forums indexes
        if (Schema::hasTable('forums')) {
            Schema::table('forums', function (Blueprint $table) {
                $table->index(['category_id'], 'forums_category_id_performance_idx');
                $table->index(['slug'], 'forums_slug_performance_idx');
                $table->index(['sort_order'], 'forums_sort_order_performance_idx');
                $table->index(['is_active'], 'forums_is_active_performance_idx');
            });
        }

        // Search logs indexes
        if (Schema::hasTable('search_logs')) {
            Schema::table('search_logs', function (Blueprint $table) {
                $table->index(['user_id'], 'search_logs_user_id_performance_idx');
                $table->index(['created_at'], 'search_logs_created_at_performance_idx');
                $table->index(['query'], 'search_logs_query_performance_idx');
                $table->index(['results_count'], 'search_logs_results_count_performance_idx');
            });
        }

        // User activities indexes
        if (Schema::hasTable('user_activities')) {
            Schema::table('user_activities', function (Blueprint $table) {
                $table->index(['user_id'], 'user_activities_user_id_performance_idx');
                $table->index(['created_at'], 'user_activities_created_at_performance_idx');
                $table->index(['activity_type'], 'user_activities_type_performance_idx');
                $table->index(['user_id', 'created_at'], 'user_activities_user_created_composite_idx');
            });
        }

        // Technical drawings indexes
        if (Schema::hasTable('technical_drawings')) {
            Schema::table('technical_drawings', function (Blueprint $table) {
                $table->index(['user_id'], 'technical_drawings_user_id_performance_idx');
                $table->index(['category_id'], 'technical_drawings_category_id_performance_idx');
                $table->index(['status'], 'technical_drawings_status_performance_idx');
                $table->index(['created_at'], 'technical_drawings_created_at_performance_idx');
                $table->index(['download_count'], 'technical_drawings_download_count_performance_idx');
                $table->index(['view_count'], 'technical_drawings_view_count_performance_idx');
            });
        }

        // CAD files indexes
        if (Schema::hasTable('cad_files')) {
            Schema::table('cad_files', function (Blueprint $table) {
                $table->index(['user_id'], 'cad_files_user_id_performance_idx');
                $table->index(['technical_drawing_id'], 'cad_files_technical_drawing_id_performance_idx');
                $table->index(['file_type'], 'cad_files_file_type_performance_idx');
                $table->index(['status'], 'cad_files_status_performance_idx');
                $table->index(['created_at'], 'cad_files_created_at_performance_idx');
            });
        }

        // Create full-text search indexes
        $this->createFullTextIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop performance indexes
        $tables = [
            'users' => [
                'users_email_performance_idx',
                'users_username_performance_idx',
                'users_role_performance_idx',
                'users_created_at_performance_idx',
                'users_last_activity_performance_idx',
                'users_email_verified_performance_idx',
                'users_role_created_composite_idx',
            ],
            'threads' => [
                'threads_user_id_performance_idx',
                'threads_category_id_performance_idx',
                'threads_forum_id_performance_idx',
                'threads_created_at_performance_idx',
                'threads_updated_at_performance_idx',
                'threads_view_count_performance_idx',
                'threads_pinned_created_composite_idx',
                'threads_status_created_composite_idx',
            ],
            'posts' => [
                'posts_thread_id_performance_idx',
                'posts_user_id_performance_idx',
                'posts_created_at_performance_idx',
                'posts_thread_created_composite_idx',
            ],
            'marketplace_products' => [
                'marketplace_products_user_id_performance_idx',
                'marketplace_products_category_id_performance_idx',
                'marketplace_products_status_performance_idx',
                'marketplace_products_price_performance_idx',
                'marketplace_products_created_at_performance_idx',
                'marketplace_products_view_count_performance_idx',
                'marketplace_products_status_created_composite_idx',
                'marketplace_products_category_status_composite_idx',
                'marketplace_products_price_status_composite_idx',
            ],
            'marketplace_orders' => [
                'marketplace_orders_user_id_performance_idx',
                'marketplace_orders_seller_id_performance_idx',
                'marketplace_orders_status_performance_idx',
                'marketplace_orders_created_at_performance_idx',
                'marketplace_orders_total_amount_performance_idx',
                'marketplace_orders_user_status_composite_idx',
                'marketplace_orders_seller_status_composite_idx',
                'marketplace_orders_status_created_composite_idx',
            ],
            'notifications' => [
                'notifications_user_id_performance_idx',
                'notifications_read_at_performance_idx',
                'notifications_created_at_performance_idx',
                'notifications_type_performance_idx',
                'notifications_user_read_composite_idx',
                'notifications_user_created_composite_idx',
            ],
        ];

        foreach ($tables as $table => $indexes) {
            if (Schema::hasTable($table)) {
                foreach ($indexes as $index) {
                    try {
                        DB::statement("DROP INDEX {$index} ON {$table}");
                    } catch (\Exception $e) {
                        // Index might not exist, continue
                    }
                }
            }
        }

        // Drop full-text indexes
        $this->dropFullTextIndexes();
    }

    /**
     * Create full-text search indexes
     */
    private function createFullTextIndexes(): void
    {
        try {
            // Threads full-text search
            if (Schema::hasTable('threads')) {
                DB::statement('CREATE FULLTEXT INDEX threads_fulltext_search_idx ON threads (title, content)');
            }

            // Products full-text search
            if (Schema::hasTable('marketplace_products')) {
                DB::statement('CREATE FULLTEXT INDEX products_fulltext_search_idx ON marketplace_products (name, description)');
            }

            // Users full-text search
            if (Schema::hasTable('users')) {
                DB::statement('CREATE FULLTEXT INDEX users_fulltext_search_idx ON users (name, username)');
            }

        } catch (\Exception $e) {
            // Full-text indexes might already exist or not supported
        }
    }

    /**
     * Drop full-text search indexes
     */
    private function dropFullTextIndexes(): void
    {
        try {
            if (Schema::hasTable('threads')) {
                DB::statement('DROP INDEX threads_fulltext_search_idx ON threads');
            }

            if (Schema::hasTable('marketplace_products')) {
                DB::statement('DROP INDEX products_fulltext_search_idx ON marketplace_products');
            }

            if (Schema::hasTable('users')) {
                DB::statement('DROP INDEX users_fulltext_search_idx ON users');
            }

        } catch (\Exception $e) {
            // Indexes might not exist
        }
    }
};
