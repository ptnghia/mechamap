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
        Schema::create('group_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_type_id')->constrained('conversation_types')->onDelete('cascade');
            $table->string('title', 200);
            $table->text('description');
            $table->text('justification')->nullable(); // Lý do tạo group
            $table->integer('expected_members')->default(10);
            
            // Creator info
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            
            // Status tracking
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'needs_revision'])
                  ->default('pending');
            
            // Admin review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Timestamps
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('creator_id');
            $table->index('conversation_type_id');
            $table->index('requested_at');
            $table->index(['status', 'requested_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_requests');
    }
};
