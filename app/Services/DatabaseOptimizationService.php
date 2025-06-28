<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseOptimizationService
{
    /**
     * Create optimized indexes for better performance
     */
    public function createOptimizedIndexes()
    {
        $indexes = [
            // Users table indexes
            'users' => [
                ['email', 'email_verified_at'],
                ['last_login_at'],
                ['created_at', 'status'],
                ['country', 'city'],
            ],

            // Marketplace tables indexes
            'marketplace_products' => [
                ['status', 'is_active'],
                ['seller_id', 'status'],
                ['product_category_id', 'status'],
                ['created_at', 'status'],
                ['price', 'sale_price'],
                ['view_count', 'download_count'],
                ['is_featured', 'status'],
            ],

            'marketplace_orders' => [
                ['customer_id', 'status'],
                ['payment_status', 'created_at'],
                ['status', 'created_at'],
                ['order_number'],
                ['total_amount', 'payment_status'],
            ],

            'marketplace_order_items' => [
                ['order_id', 'seller_id'],
                ['product_id', 'fulfillment_status'],
                ['seller_id', 'fulfillment_status'],
            ],

            'marketplace_sellers' => [
                ['user_id'],
                ['seller_type', 'status'],
                ['verification_status', 'status'],
                ['is_featured', 'status'],
                ['total_revenue', 'status'],
            ],

            // Technical tables indexes
            'technical_drawings' => [
                ['created_by', 'status'],
                ['company_id', 'visibility'],
                ['drawing_type', 'status'],
                ['industry_category', 'application_area'],
                ['is_featured', 'is_active'],
                ['download_count', 'view_count'],
                ['created_at', 'status'],
            ],

            'cad_files' => [
                ['created_by', 'status'],
                ['company_id', 'visibility'],
                ['cad_software', 'model_type'],
                ['processing_status', 'virus_scanned'],
                ['file_extension', 'status'],
            ],

            'materials' => [
                ['category', 'subcategory'],
                ['material_type', 'status'],
                ['is_active', 'is_featured'],
                ['density', 'yield_strength'],
                ['cost_per_kg', 'availability'],
            ],

            // Forum tables indexes
            'threads' => [
                ['forum_id', 'status'],
                ['user_id', 'created_at'],
                ['is_pinned', 'is_locked'],
                ['view_count', 'comment_count'],
                ['created_at', 'updated_at'],
            ],

            'comments' => [
                ['thread_id', 'created_at'],
                ['user_id', 'created_at'],
                ['parent_id', 'created_at'],
            ],

            'forums' => [
                ['category_id', 'is_active'],
                ['slug'],
                ['sort_order', 'is_active'],
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (Schema::hasTable($table)) {
                foreach ($tableIndexes as $columns) {
                    $this->createIndexIfNotExists($table, $columns);
                }
            }
        }
    }

    /**
     * Optimize database queries with proper indexing
     */
    public function optimizeQueries()
    {
        // Analyze slow queries and create indexes
        $this->analyzeSlowQueries();
        
        // Update table statistics
        $this->updateTableStatistics();
        
        // Optimize table structures
        $this->optimizeTableStructures();
    }

    /**
     * Create composite indexes for complex queries
     */
    public function createCompositeIndexes()
    {
        $compositeIndexes = [
            // Analytics optimized indexes
            'marketplace_orders' => [
                'idx_orders_analytics' => ['payment_status', 'created_at', 'total_amount'],
                'idx_orders_customer' => ['customer_id', 'status', 'created_at'],
            ],

            'marketplace_order_items' => [
                'idx_items_seller_analytics' => ['seller_id', 'fulfillment_status', 'created_at'],
                'idx_items_product_analytics' => ['product_id', 'created_at', 'total_amount'],
            ],

            'marketplace_products' => [
                'idx_products_search' => ['status', 'is_active', 'seller_type'],
                'idx_products_featured' => ['is_featured', 'status', 'created_at'],
                'idx_products_category' => ['product_category_id', 'status', 'price'],
            ],

            'technical_drawings' => [
                'idx_drawings_search' => ['status', 'visibility', 'drawing_type'],
                'idx_drawings_analytics' => ['created_at', 'download_count', 'view_count'],
            ],

            'users' => [
                'idx_users_activity' => ['last_login_at', 'created_at', 'status'],
            ],
        ];

        foreach ($compositeIndexes as $table => $indexes) {
            if (Schema::hasTable($table)) {
                foreach ($indexes as $indexName => $columns) {
                    $this->createNamedIndexIfNotExists($table, $indexName, $columns);
                }
            }
        }
    }

    /**
     * Optimize database configuration
     */
    public function optimizeDatabaseConfig()
    {
        // Set optimal MySQL configuration for Laravel
        $optimizations = [
            "SET SESSION query_cache_type = ON",
            "SET SESSION query_cache_size = 268435456", // 256MB
            "SET SESSION innodb_buffer_pool_size = 1073741824", // 1GB
            "SET SESSION max_connections = 200",
            "SET SESSION innodb_log_file_size = 268435456", // 256MB
        ];

        foreach ($optimizations as $query) {
            try {
                DB::statement($query);
            } catch (\Exception $e) {
                // Log error but continue with other optimizations
                \Log::warning("Database optimization failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Clean up and optimize tables
     */
    public function cleanupTables()
    {
        $tables = [
            'marketplace_orders',
            'marketplace_order_items', 
            'marketplace_products',
            'technical_drawings',
            'cad_files',
            'materials',
            'threads',
            'comments',
            'users',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement("OPTIMIZE TABLE {$table}");
                    DB::statement("ANALYZE TABLE {$table}");
                } catch (\Exception $e) {
                    \Log::warning("Table optimization failed for {$table}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Get database performance metrics
     */
    public function getPerformanceMetrics()
    {
        try {
            $metrics = [
                'slow_queries' => DB::select("SHOW STATUS LIKE 'Slow_queries'")[0]->Value ?? 0,
                'connections' => DB::select("SHOW STATUS LIKE 'Connections'")[0]->Value ?? 0,
                'uptime' => DB::select("SHOW STATUS LIKE 'Uptime'")[0]->Value ?? 0,
                'queries' => DB::select("SHOW STATUS LIKE 'Queries'")[0]->Value ?? 0,
                'innodb_buffer_pool_reads' => DB::select("SHOW STATUS LIKE 'Innodb_buffer_pool_reads'")[0]->Value ?? 0,
                'innodb_buffer_pool_read_requests' => DB::select("SHOW STATUS LIKE 'Innodb_buffer_pool_read_requests'")[0]->Value ?? 0,
            ];

            // Calculate buffer pool hit ratio
            if ($metrics['innodb_buffer_pool_read_requests'] > 0) {
                $metrics['buffer_pool_hit_ratio'] = round(
                    (1 - ($metrics['innodb_buffer_pool_reads'] / $metrics['innodb_buffer_pool_read_requests'])) * 100, 
                    2
                );
            } else {
                $metrics['buffer_pool_hit_ratio'] = 100;
            }

            return $metrics;
        } catch (\Exception $e) {
            return [
                'error' => 'Could not retrieve performance metrics: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Private helper methods
     */
    private function createIndexIfNotExists($table, $columns)
    {
        $indexName = 'idx_' . $table . '_' . implode('_', $columns);
        
        try {
            if (!$this->indexExists($table, $indexName)) {
                Schema::table($table, function ($table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to create index {$indexName} on table {$table}: " . $e->getMessage());
        }
    }

    private function createNamedIndexIfNotExists($table, $indexName, $columns)
    {
        try {
            if (!$this->indexExists($table, $indexName)) {
                Schema::table($table, function ($table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to create named index {$indexName} on table {$table}: " . $e->getMessage());
        }
    }

    private function indexExists($table, $indexName)
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function analyzeSlowQueries()
    {
        try {
            // Enable slow query log analysis
            DB::statement("SET SESSION slow_query_log = 'ON'");
            DB::statement("SET SESSION long_query_time = 1"); // Log queries taking more than 1 second
        } catch (\Exception $e) {
            \Log::warning("Could not configure slow query logging: " . $e->getMessage());
        }
    }

    private function updateTableStatistics()
    {
        $tables = Schema::getAllTables();
        
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            try {
                DB::statement("ANALYZE TABLE {$tableName}");
            } catch (\Exception $e) {
                \Log::warning("Failed to analyze table {$tableName}: " . $e->getMessage());
            }
        }
    }

    private function optimizeTableStructures()
    {
        // Add any table structure optimizations here
        // For example, converting MyISAM tables to InnoDB, optimizing column types, etc.
        
        try {
            // Ensure all tables are using InnoDB engine
            $tables = Schema::getAllTables();
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                DB::statement("ALTER TABLE {$tableName} ENGINE=InnoDB");
            }
        } catch (\Exception $e) {
            \Log::warning("Table structure optimization failed: " . $e->getMessage());
        }
    }
}
