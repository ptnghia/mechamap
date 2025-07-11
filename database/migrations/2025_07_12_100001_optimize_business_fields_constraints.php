<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🎯 Task 1.3: Tối ưu hóa business fields constraints và validation
     * Đảm bảo data integrity cho registration wizard
     */
    public function up(): void
    {
        // Clean existing tax_code data trước khi thêm constraints
        $this->cleanTaxCodeData();

        // Thêm unique constraint cho tax_code (nếu chưa có)
        if (!$this->hasUniqueConstraint('users', 'tax_code')) {
            // Xóa duplicates trước khi thêm unique constraint
            DB::statement("
                UPDATE users u1
                JOIN (
                    SELECT tax_code, MIN(id) as min_id
                    FROM users
                    WHERE tax_code IS NOT NULL AND tax_code != ''
                    GROUP BY tax_code
                    HAVING COUNT(*) > 1
                ) u2 ON u1.tax_code = u2.tax_code AND u1.id > u2.min_id
                SET u1.tax_code = CONCAT(u1.tax_code, '_', u1.id)
                WHERE u1.tax_code IS NOT NULL
            ");

            Schema::table('users', function (Blueprint $table) {
                $table->unique('tax_code', 'users_tax_code_unique');
            });
        }

        // Thêm basic check constraints (compatible với MariaDB)
        if ($this->supportCheckConstraints()) {
            try {
                // Tax code format validation (10-13 digits only)
                DB::statement("
                    ALTER TABLE users
                    ADD CONSTRAINT chk_tax_code_format
                    CHECK (tax_code IS NULL OR (CHAR_LENGTH(tax_code) >= 10 AND CHAR_LENGTH(tax_code) <= 13))
                ");

                // Business rating range validation
                DB::statement("
                    ALTER TABLE users
                    ADD CONSTRAINT chk_business_rating_range
                    CHECK (business_rating IS NULL OR (business_rating >= 0 AND business_rating <= 5))
                ");

                echo "Added CHECK constraints successfully\n";
            } catch (Exception $e) {
                echo "Warning: Could not add CHECK constraints (MariaDB limitation): " . $e->getMessage() . "\n";
            }
        }

        // Thêm indexes cho performance optimization
        Schema::table('users', function (Blueprint $table) {
            // Index cho business search và filtering
            if (!Schema::hasIndex('users', 'users_business_search_index')) {
                $table->index(['role', 'is_verified_business', 'business_rating'], 'users_business_search_index');
            }

            // Index cho tax code lookups
            if (!Schema::hasIndex('users', 'users_tax_code_index')) {
                $table->index('tax_code', 'users_tax_code_index');
            }

            // Index cho business categories search
            if (!Schema::hasIndex('users', 'users_business_categories_index')) {
                $table->index('business_categories', 'users_business_categories_index');
            }

            // Composite index cho verification workflow
            if (!Schema::hasIndex('users', 'users_verification_workflow_index')) {
                $table->index(['role', 'is_verified_business', 'created_at'], 'users_verification_workflow_index');
            }
        });

        // Cập nhật business categories format cho existing data
        DB::statement("
            UPDATE users
            SET business_categories = JSON_ARRAY(business_categories)
            WHERE business_categories IS NOT NULL
            AND JSON_VALID(business_categories) = 0
            AND business_categories != ''
        ");

        // Đảm bảo role_group được set đúng cho business users
        DB::statement("
            UPDATE users
            SET role_group = 'business_partners'
            WHERE role IN ('manufacturer', 'supplier', 'brand')
            AND (role_group IS NULL OR role_group != 'business_partners')
        ");

        // Set default subscription level cho business users
        DB::statement("
            UPDATE users
            SET subscription_level = 'free'
            WHERE role IN ('manufacturer', 'supplier', 'brand')
            AND subscription_level IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop check constraints (nếu có)
        if ($this->supportCheckConstraints()) {
            try {
                DB::statement("ALTER TABLE users DROP CONSTRAINT chk_tax_code_format");
                DB::statement("ALTER TABLE users DROP CONSTRAINT chk_business_rating_range");
            } catch (Exception $e) {
                // Ignore if constraints don't exist
            }
        }

        Schema::table('users', function (Blueprint $table) {
            // Drop indexes
            $indexes = [
                'users_business_search_index',
                'users_tax_code_index',
                'users_business_categories_index',
                'users_verification_workflow_index'
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('users', $index)) {
                    $table->dropIndex($index);
                }
            }

            // Drop unique constraint
            if ($this->hasUniqueConstraint('users', 'tax_code')) {
                $table->dropUnique('users_tax_code_unique');
            }
        });
    }

    /**
     * Check if database supports CHECK constraints
     */
    private function supportCheckConstraints(): bool
    {
        $version = DB::select("SELECT VERSION() as version")[0]->version;
        return version_compare($version, '8.0.16', '>=');
    }

    /**
     * Clean existing tax_code data to match format requirements
     */
    private function cleanTaxCodeData(): void
    {
        // Remove hyphens and extra characters from tax_code
        DB::statement("
            UPDATE users
            SET tax_code = REGEXP_REPLACE(tax_code, '[^0-9]', '')
            WHERE tax_code IS NOT NULL
            AND tax_code != ''
            AND tax_code REGEXP '[^0-9]'
        ");

        // Set invalid tax_codes to NULL (too short or too long)
        DB::statement("
            UPDATE users
            SET tax_code = NULL
            WHERE tax_code IS NOT NULL
            AND (CHAR_LENGTH(tax_code) < 10 OR CHAR_LENGTH(tax_code) > 13)
        ");

        echo "Cleaned tax_code data: removed non-numeric characters and invalid lengths\n";
    }

    /**
     * Check if unique constraint exists
     */
    private function hasUniqueConstraint(string $table, string $column): bool
    {
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND CONSTRAINT_TYPE = 'UNIQUE'
            AND CONSTRAINT_NAME LIKE ?
        ", [$table, "%{$column}%"]);

        return count($constraints) > 0;
    }
};
