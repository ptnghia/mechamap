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
        // Convert existing student users to member role
        DB::table('users')
            ->where('role', 'student')
            ->update([
                'role' => 'member',
                'updated_at' => now(),
            ]);

        // Remove student role from roles table if exists
        DB::table('roles')
            ->where('name', 'student')
            ->delete();

        // Remove student permissions if exists
        DB::table('permissions')
            ->where('name', 'LIKE', '%student%')
            ->delete();

        // Log the migration
        \Illuminate\Support\Facades\Log::info('Student role removed migration completed', [
            'converted_users' => DB::table('users')->where('role', 'member')->count(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration is not reversible as we don't know
        // which users were originally students vs members
        \Illuminate\Support\Facades\Log::warning('Student role removal migration cannot be reversed');
    }
};
