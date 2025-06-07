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
        Schema::table('threads', function (Blueprint $table) {
            // Quality & Solution States
            $table->boolean('is_solved')->default(false)->after('is_featured');
            $table->unsignedBigInteger('solution_comment_id')->nullable()->after('is_solved');
            $table->timestamp('solved_at')->nullable()->after('solution_comment_id');
            $table->unsignedBigInteger('solved_by')->nullable()->after('solved_at');
            $table->integer('quality_score')->default(0)->after('solved_by');
            $table->decimal('average_rating', 3, 2)->default(0)->after('quality_score'); // 0.00 - 5.00
            $table->integer('ratings_count')->default(0)->after('average_rating');

            // Thread Type Classification
            $table->enum('thread_type', [
                'discussion',
                'question',
                'announcement',
                'tutorial',
                'poll',
                'project',
                'showcase'
            ])->default('discussion')->after('ratings_count');

            // Foreign keys
            $table->foreign('solution_comment_id')->references('id')->on('comments')->onDelete('set null');
            $table->foreign('solved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['is_solved']);
            $table->index(['thread_type']);
            $table->index(['quality_score']);
            $table->index(['average_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['solution_comment_id']);
            $table->dropForeign(['solved_by']);

            // Drop indexes
            $table->dropIndex(['is_solved']);
            $table->dropIndex(['thread_type']);
            $table->dropIndex(['quality_score']);
            $table->dropIndex(['average_rating']);

            // Drop columns
            $table->dropColumn([
                'is_solved',
                'solution_comment_id',
                'solved_at',
                'solved_by',
                'quality_score',
                'average_rating',
                'ratings_count',
                'thread_type'
            ]);
        });
    }
};
