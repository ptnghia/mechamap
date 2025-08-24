<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Optimize notification performance với advanced indexes và caching
     */
    public function up(): void
    {
        echo "🚀 Starting notification performance optimization...\n";

        try {
            // Step 1: Add missing critical indexes
            echo "📋 Adding critical performance indexes...\n";
            $this->addCriticalIndexes();

            // Step 2: Add search optimization indexes
            echo "📋 Adding search optimization indexes...\n";
            $this->addSearchIndexes();

            // Step 3: Add filtering indexes
            echo "📋 Adding filtering indexes...\n";
            $this->addFilteringIndexes();

            // Step 4: Add statistics optimization indexes
            echo "📋 Adding statistics optimization indexes...\n";
            $this->addStatisticsIndexes();

            // Step 5: Optimize existing data
            echo "📋 Optimizing existing data...\n";
            $this->optimizeExistingData();

            echo "✅ Notification performance optimization completed!\n";

        } catch (\Exception $e) {
            echo "❌ Error during optimization: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Add critical performance indexes
     */
    private function addCriticalIndexes(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Critical index for main notification queries
            if (!$this->indexExists('notifications', 'idx_user_read_status_created')) {
                $table->index(['user_id', 'is_read', 'status', 'created_at'], 'idx_user_read_status_created');
                echo "  ✅ Added critical index: idx_user_read_status_created\n";
            }

            // Index for unread count queries (most frequent)
            if (!$this->indexExists('notifications', 'idx_user_unread_active')) {
                $table->index(['user_id', 'is_read', 'status'], 'idx_user_unread_active');
                echo "  ✅ Added unread count index: idx_user_unread_active\n";
            }

            // Index for archive queries
            if (!$this->indexExists('notifications', 'idx_user_archived_at')) {
                $table->index(['user_id', 'archived_at', 'status'], 'idx_user_archived_at');
                echo "  ✅ Added archive index: idx_user_archived_at\n";
            }

            // Index for requires_action queries
            if (!$this->indexExists('notifications', 'idx_user_requires_action')) {
                $table->index(['user_id', 'requires_action', 'status'], 'idx_user_requires_action');
                echo "  ✅ Added requires_action index: idx_user_requires_action\n";
            }
        });
    }

    /**
     * Add search optimization indexes
     */
    private function addSearchIndexes(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Full-text search index for title and message
            if (!$this->indexExists('notifications', 'idx_fulltext_search')) {
                DB::statement('ALTER TABLE notifications ADD FULLTEXT idx_fulltext_search (title, message)');
                echo "  ✅ Added fulltext search index: idx_fulltext_search\n";
            }
        });
    }

    /**
     * Add filtering indexes
     */
    private function addFilteringIndexes(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Category filtering index
            if (!$this->indexExists('notifications', 'idx_user_category_status')) {
                $table->index(['user_id', 'category', 'status', 'created_at'], 'idx_user_category_status');
                echo "  ✅ Added category filtering index: idx_user_category_status\n";
            }

            // Type filtering index
            if (!$this->indexExists('notifications', 'idx_user_type_status')) {
                $table->index(['user_id', 'type', 'status', 'created_at'], 'idx_user_type_status');
                echo "  ✅ Added type filtering index: idx_user_type_status\n";
            }

            // Priority filtering index
            if (!$this->indexExists('notifications', 'idx_user_priority_status')) {
                $table->index(['user_id', 'priority', 'status', 'created_at'], 'idx_user_priority_status');
                echo "  ✅ Added priority filtering index: idx_user_priority_status\n";
            }

            // Date range filtering index
            if (!$this->indexExists('notifications', 'idx_user_created_status')) {
                $table->index(['user_id', 'created_at', 'status'], 'idx_user_created_status');
                echo "  ✅ Added date range filtering index: idx_user_created_status\n";
            }
        });
    }

    /**
     * Add statistics optimization indexes
     */
    private function addStatisticsIndexes(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Statistics aggregation index
            if (!$this->indexExists('notifications', 'idx_stats_aggregation')) {
                $table->index(['user_id', 'category', 'priority', 'is_read'], 'idx_stats_aggregation');
                echo "  ✅ Added statistics aggregation index: idx_stats_aggregation\n";
            }

            // View count optimization index
            if (!$this->indexExists('notifications', 'idx_view_count_optimization')) {
                $table->index(['user_id', 'view_count', 'created_at'], 'idx_view_count_optimization');
                echo "  ✅ Added view count optimization index: idx_view_count_optimization\n";
            }
        });
    }

    /**
     * Optimize existing data
     */
    private function optimizeExistingData(): void
    {
        // Update null view_count to 0
        $updated = DB::table('notifications')
            ->whereNull('view_count')
            ->update(['view_count' => 0]);
        
        if ($updated > 0) {
            echo "  ✅ Updated {$updated} null view_count values to 0\n";
        }

        // Optimize table
        DB::statement('OPTIMIZE TABLE notifications');
        echo "  ✅ Optimized notifications table\n";

        // Analyze table for better query planning
        DB::statement('ANALYZE TABLE notifications');
        echo "  ✅ Analyzed notifications table for query optimization\n";
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table}");
            foreach ($indexes as $index) {
                if ($index->Key_name === $indexName) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Rolling back notification performance optimization...\n";

        try {
            Schema::table('notifications', function (Blueprint $table) {
                // Drop performance indexes
                $indexes = [
                    'idx_user_read_status_created',
                    'idx_user_unread_active',
                    'idx_user_archived_at',
                    'idx_user_requires_action',
                    'idx_user_category_status',
                    'idx_user_type_status',
                    'idx_user_priority_status',
                    'idx_user_created_status',
                    'idx_stats_aggregation',
                    'idx_view_count_optimization'
                ];

                foreach ($indexes as $index) {
                    if ($this->indexExists('notifications', $index)) {
                        $table->dropIndex($index);
                        echo "  ✅ Dropped index: {$index}\n";
                    }
                }
            });

            // Drop fulltext index
            if ($this->indexExists('notifications', 'idx_fulltext_search')) {
                DB::statement('ALTER TABLE notifications DROP INDEX idx_fulltext_search');
                echo "  ✅ Dropped fulltext index: idx_fulltext_search\n";
            }

            echo "✅ Performance optimization rollback completed!\n";

        } catch (\Exception $e) {
            echo "❌ Error during rollback: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};
