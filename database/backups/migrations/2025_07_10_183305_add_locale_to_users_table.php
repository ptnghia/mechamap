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
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't exist
            if (!Schema::hasColumn('users', 'browser_notifications_enabled')) {
                $table->boolean('browser_notifications_enabled')->default(true)->after('email_notifications_enabled');
            }
            if (!Schema::hasColumn('users', 'marketing_emails_enabled')) {
                $table->boolean('marketing_emails_enabled')->default(true)->after('browser_notifications_enabled');
            }

            // Add indexes if they don't exist
            if (!Schema::hasIndex('users', 'users_locale_index')) {
                $table->index('locale');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes if they exist
            if (Schema::hasIndex('users', 'users_locale_index')) {
                $table->dropIndex(['locale']);
            }

            // Drop columns if they exist
            if (Schema::hasColumn('users', 'browser_notifications_enabled')) {
                $table->dropColumn('browser_notifications_enabled');
            }
            if (Schema::hasColumn('users', 'marketing_emails_enabled')) {
                $table->dropColumn('marketing_emails_enabled');
            }
        });
    }
};
