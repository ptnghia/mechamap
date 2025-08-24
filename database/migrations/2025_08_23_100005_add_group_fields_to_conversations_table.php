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
        Schema::table('conversations', function (Blueprint $table) {
            // Group conversation fields
            $table->foreignId('conversation_type_id')->nullable()->constrained('conversation_types')->onDelete('set null');
            $table->boolean('is_group')->default(false);
            $table->foreignId('group_request_id')->nullable()->constrained('group_requests')->onDelete('set null');
            $table->integer('max_members')->default(2);
            $table->boolean('is_public')->default(false);
            $table->text('group_description')->nullable();
            $table->text('group_rules')->nullable();
            
            // Indexes
            $table->index('is_group');
            $table->index('conversation_type_id');
            $table->index('is_public');
            $table->index(['is_group', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['conversation_type_id']);
            $table->dropForeign(['group_request_id']);
            $table->dropColumn([
                'conversation_type_id',
                'is_group',
                'group_request_id',
                'max_members',
                'is_public',
                'group_description',
                'group_rules'
            ]);
        });
    }
};
