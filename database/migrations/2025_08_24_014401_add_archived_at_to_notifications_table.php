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
        Schema::table('notifications', function (Blueprint $table) {
            // Add archived_at column for archive functionality
            $table->timestamp('archived_at')->nullable()->after('expires_at');

            // Add index for archived notifications queries
            $table->index(['user_id', 'status', 'archived_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex(['user_id', 'status', 'archived_at']);

            // Drop archived_at column
            $table->dropColumn('archived_at');
        });
    }
};
