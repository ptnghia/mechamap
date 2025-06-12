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
        // Add performance indexes for forum functionality
        // Only add indexes that don't already exist

        // Comments table - Add performance index for popular comments sorting
        if (!$this->indexExists('comments', 'comments_like_count_created_at_index')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->index(['like_count', 'created_at'], 'comments_like_count_created_at_index');
            });
        }

        // Categories table - Add order index for category sorting
        if (!$this->indexExists('categories', 'categories_order_index')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->index(['order'], 'categories_order_index');
            });
        }

        // Reports table - Add status + date index for admin filtering
        if (!$this->indexExists('reports', 'reports_status_created_at_index')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->index(['status', 'created_at'], 'reports_status_created_at_index');
            });
        }

        // Social interactions - Add polymorphic indexes for better performance
        if (!$this->indexExists('reactions', 'reactions_reactable_type_reactable_id_index')) {
            Schema::table('reactions', function (Blueprint $table) {
                $table->index(['reactable_type', 'reactable_id'], 'reactions_reactable_type_reactable_id_index');
            });
        }

        // Messaging system - Add conversation ordering
        if (!$this->indexExists('conversations', 'conversations_updated_at_index')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->index(['updated_at'], 'conversations_updated_at_index');
            });
        }

        // Add fulltext search index for threads if not exists
        if (!$this->indexExists('threads', 'threads_title_content_fulltext')) {
            try {
                DB::statement('ALTER TABLE threads ADD FULLTEXT threads_title_content_fulltext (title, content)');
            } catch (\Exception $e) {
                // Ignore if fulltext index already exists
            }
        }

        // Add foreign key constraint for solution_comment_id (after comments table is created)
        if (!$this->foreignKeyExists('threads', 'threads_solution_comment_id_foreign')) {
            try {
                Schema::table('threads', function (Blueprint $table) {
                    $table->foreign('solution_comment_id')->references('id')->on('comments')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Ignore if foreign key already exists
            }
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if a foreign key exists on a table
     */
    private function foreignKeyExists(string $table, string $foreignKeyName): bool
    {
        try {
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.REFERENTIAL_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND CONSTRAINT_NAME = ?
            ", [$table, $foreignKeyName]);
            return count($constraints) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint first
        if ($this->foreignKeyExists('threads', 'threads_solution_comment_id_foreign')) {
            try {
                Schema::table('threads', function (Blueprint $table) {
                    $table->dropForeign('threads_solution_comment_id_foreign');
                });
            } catch (\Exception $e) {
                // Ignore errors on drop
            }
        }

        // Drop the indexes we added
        $indexes = [
            'comments' => ['comments_like_count_created_at_index'],
            'categories' => ['categories_order_index'],
            'reports' => ['reports_status_created_at_index'],
            'reactions' => ['reactions_reactable_type_reactable_id_index'],
            'conversations' => ['conversations_updated_at_index'],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $indexName) {
                if ($this->indexExists($table, $indexName)) {
                    try {
                        Schema::table($table, function (Blueprint $table) use ($indexName) {
                            $table->dropIndex($indexName);
                        });
                    } catch (\Exception $e) {
                        // Ignore errors on drop
                    }
                }
            }
        }

        // Drop fulltext index
        if ($this->indexExists('threads', 'threads_title_content_fulltext')) {
            try {
                DB::statement('ALTER TABLE threads DROP INDEX threads_title_content_fulltext');
            } catch (\Exception $e) {
                // Ignore errors on drop
            }
        }
    }
};
