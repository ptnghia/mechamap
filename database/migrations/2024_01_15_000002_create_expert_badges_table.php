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
        Schema::create('expert_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->enum('category', ['expertise', 'contribution', 'leadership', 'achievement', 'skill', 'recognition', 'milestone', 'special']);
            $table->enum('type', ['bronze', 'silver', 'gold', 'platinum', 'diamond', 'legendary']);
            $table->string('icon')->nullable();
            $table->string('color', 7)->default('#6c757d'); // hex color
            $table->json('requirements')->nullable(); // requirements to earn badge
            $table->integer('points_required')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->enum('rarity', ['common', 'uncommon', 'rare', 'epic', 'legendary', 'mythic'])->default('common');
            $table->boolean('verification_required')->default(false); // requires admin verification
            $table->boolean('auto_award')->default(true); // automatically awarded when requirements met
            $table->integer('display_order')->default(0);
            $table->json('metadata')->nullable(); // additional data
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['category', 'is_active']);
            $table->index(['type', 'is_active']);
            $table->index(['rarity', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index('display_order');
        });

        Schema::create('user_expert_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expert_badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('awarded_at');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['user_id', 'expert_badge_id']);
            
            // Indexes
            $table->index(['user_id', 'awarded_at']);
            $table->index(['expert_badge_id', 'awarded_at']);
            $table->index('verified_at');
        });

        Schema::create('badge_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expert_badge_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('evidence')->nullable(); // evidence provided by user
            $table->json('evidence_files')->nullable(); // uploaded files as evidence
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['expert_badge_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_verifications');
        Schema::dropIfExists('user_expert_badges');
        Schema::dropIfExists('expert_badges');
    }
};
