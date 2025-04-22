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
            if (!Schema::hasColumn('users', 'banned_at')) {
                $table->timestamp('banned_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'banned_reason')) {
                $table->string('banned_reason')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('users', 'banned_at')) {
                $columns[] = 'banned_at';
            }
            if (Schema::hasColumn('users', 'banned_reason')) {
                $columns[] = 'banned_reason';
            }
            if (Schema::hasColumn('users', 'last_seen_at')) {
                $columns[] = 'last_seen_at';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
