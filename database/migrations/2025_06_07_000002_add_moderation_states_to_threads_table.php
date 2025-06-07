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
            // Moderation States
            $table->boolean('is_flagged')->default(false)->after('is_featured');
            $table->boolean('is_spam')->default(false)->after('is_flagged');
            $table->enum('moderation_status', ['clean', 'flagged', 'under_review', 'spam', 'approved'])
                ->default('clean')->after('is_spam');
            $table->integer('reports_count')->default(0)->after('moderation_status');
            $table->timestamp('flagged_at')->nullable()->after('reports_count');
            $table->unsignedBigInteger('flagged_by')->nullable()->after('flagged_at');
            $table->text('moderation_notes')->nullable()->after('flagged_by');

            // Foreign key cho moderator
            $table->foreign('flagged_by')->references('id')->on('users')->onDelete('set null');

            // Index cho performance
            $table->index(['is_flagged']);
            $table->index(['is_spam']);
            $table->index(['moderation_status']);
            $table->index(['flagged_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            // Drop foreign key trước
            $table->dropForeign(['flagged_by']);

            // Drop indexes
            $table->dropIndex(['is_flagged']);
            $table->dropIndex(['is_spam']);
            $table->dropIndex(['moderation_status']);
            $table->dropIndex(['flagged_at']);

            // Drop columns
            $table->dropColumn([
                'is_flagged',
                'is_spam',
                'moderation_status',
                'reports_count',
                'flagged_at',
                'flagged_by',
                'moderation_notes'
            ]);
        });
    }
};
