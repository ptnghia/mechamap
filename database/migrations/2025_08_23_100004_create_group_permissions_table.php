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
        Schema::create('group_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Specific permissions
            $table->boolean('can_invite_members')->default(false);
            $table->boolean('can_remove_members')->default(false);
            $table->boolean('can_moderate_content')->default(false);
            $table->boolean('can_change_settings')->default(false);
            $table->boolean('can_manage_roles')->default(false);
            $table->boolean('can_delete_group')->default(false);
            
            $table->timestamps();
            
            // Unique constraint - one permission set per user per conversation
            $table->unique(['conversation_id', 'user_id'], 'unique_group_permission');
            
            // Indexes
            $table->index('conversation_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_permissions');
    }
};
