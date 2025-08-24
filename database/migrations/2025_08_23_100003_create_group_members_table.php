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
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Role in group
            $table->enum('role', ['creator', 'admin', 'moderator', 'member'])->default('member');
            
            // Join info
            $table->timestamp('joined_at')->useCurrent();
            $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('invitation_accepted_at')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('left_at')->nullable();
            
            $table->timestamps();
            
            // Unique constraint - one user per conversation
            $table->unique(['conversation_id', 'user_id'], 'unique_group_member');
            
            // Indexes
            $table->index('conversation_id');
            $table->index('user_id');
            $table->index('role');
            $table->index('is_active');
            $table->index(['conversation_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
