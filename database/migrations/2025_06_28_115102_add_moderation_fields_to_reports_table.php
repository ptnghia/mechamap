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
        Schema::table('reports', function (Blueprint $table) {
            // Add moderation fields
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->timestamp('resolved_at')->nullable()->after('resolved_by');
            $table->text('resolution_note')->nullable()->after('resolved_at');

            // Update status enum to include 'dismissed'
            $table->dropColumn('status');
        });

        // Re-add status column with updated enum
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending')->after('description');

            // Add indexes for better performance
            $table->index(['resolved_by', 'resolved_at']);
            $table->index(['status', 'priority', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['resolved_by', 'resolved_at']);
            $table->dropIndex(['status', 'priority', 'created_at']);

            // Drop foreign key and columns
            $table->dropForeign(['resolved_by']);
            $table->dropColumn(['resolved_by', 'resolved_at', 'resolution_note']);

            // Revert status enum
            $table->dropColumn('status');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->enum('status', ['pending', 'resolved', 'rejected'])->default('pending')->after('description');
        });
    }
};
