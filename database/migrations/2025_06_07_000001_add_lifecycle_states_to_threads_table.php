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
            // Lifecycle Management States
            $table->timestamp('archived_at')->nullable()->after('updated_at');
            $table->string('archived_reason')->nullable()->after('archived_at');
            $table->timestamp('hidden_at')->nullable()->after('archived_reason');
            $table->string('hidden_reason')->nullable()->after('hidden_at');
            $table->softDeletes()->after('hidden_reason'); // Thêm deleted_at

            // Index cho performance
            $table->index(['archived_at']);
            $table->index(['hidden_at']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropIndex(['archived_at']);
            $table->dropIndex(['hidden_at']);
            $table->dropIndex(['deleted_at']);

            $table->dropColumn([
                'archived_at',
                'archived_reason',
                'hidden_at',
                'hidden_reason'
            ]);
            $table->dropSoftDeletes();
        });
    }
};
