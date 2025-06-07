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
        Schema::table('comments', function (Blueprint $table) {
            // Edit Tracking
            $table->timestamp('edited_at')->nullable()->after('updated_at');
            $table->integer('edit_count')->default(0)->after('edited_at');
            $table->unsignedBigInteger('edited_by')->nullable()->after('edit_count');
            $table->text('edit_reason')->nullable()->after('edited_by');

            // Moderation States
            $table->boolean('is_flagged')->default(false)->after('like_count');
            $table->boolean('is_spam')->default(false)->after('is_flagged');
            $table->boolean('is_solution')->default(false)->after('is_spam');
            $table->integer('reports_count')->default(0)->after('is_solution');

            // Enhanced Interactions
            $table->integer('dislikes_count')->default(0)->after('like_count');
            $table->decimal('quality_score', 5, 2)->default(0)->after('dislikes_count'); // -999.99 to 999.99

            // Soft Delete
            $table->softDeletes()->after('updated_at');

            // Foreign key
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['edited_at']);
            $table->index(['is_flagged']);
            $table->index(['is_spam']);
            $table->index(['is_solution']);
            $table->index(['quality_score']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['edited_by']);

            // Drop indexes
            $table->dropIndex(['edited_at']);
            $table->dropIndex(['is_flagged']);
            $table->dropIndex(['is_spam']);
            $table->dropIndex(['is_solution']);
            $table->dropIndex(['quality_score']);
            $table->dropIndex(['deleted_at']);

            // Drop columns
            $table->dropColumn([
                'edited_at',
                'edit_count',
                'edited_by',
                'edit_reason',
                'is_flagged',
                'is_spam',
                'is_solution',
                'reports_count',
                'dislikes_count',
                'quality_score'
            ]);

            $table->dropSoftDeletes();
        });
    }
};
