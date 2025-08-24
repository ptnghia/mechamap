<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            // Check and add columns that don't exist
            if (!Schema::hasColumn('page_seos', 'meta_author')) {
                $table->string('meta_author')->nullable()->comment('Author meta tag')->after('breadcrumb_title_i18n');
            }
            if (!Schema::hasColumn('page_seos', 'og_type')) {
                $table->string('og_type')->default('website')->comment('Open Graph type')->after('twitter_description_i18n');
            }
            if (!Schema::hasColumn('page_seos', 'twitter_card_type')) {
                $table->string('twitter_card_type')->default('summary')->comment('Twitter card type')->after('og_type');
            }
        });

        // Add indexes for performance (check if they don't exist)
        Schema::table('page_seos', function (Blueprint $table) {
            if (!$this->indexExists('page_seos', 'idx_page_seos_priority')) {
                $table->index(['priority', 'is_active'], 'idx_page_seos_priority');
            }
            if (!$this->indexExists('page_seos', 'idx_page_seos_sitemap')) {
                $table->index(['sitemap_include', 'sitemap_priority'], 'idx_page_seos_sitemap');
            }
            if (!$this->indexExists('page_seos', 'idx_page_seos_article_type')) {
                $table->index(['article_type', 'is_active'], 'idx_page_seos_article_type');
            }
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $name)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            // Drop indexes first (check if they exist)
            if ($this->indexExists('page_seos', 'idx_page_seos_priority')) {
                $table->dropIndex('idx_page_seos_priority');
            }
            if ($this->indexExists('page_seos', 'idx_page_seos_sitemap')) {
                $table->dropIndex('idx_page_seos_sitemap');
            }
            if ($this->indexExists('page_seos', 'idx_page_seos_article_type')) {
                $table->dropIndex('idx_page_seos_article_type');
            }

            // Drop only columns that were added by this migration
            $columnsToCheck = ['meta_author', 'og_type', 'twitter_card_type'];
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('page_seos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
