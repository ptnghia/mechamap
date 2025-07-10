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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Unique identifier for achievement
            $table->string('name'); // Display name
            $table->text('description'); // Achievement description
            $table->string('category'); // Category (social, content, marketplace, etc.)
            $table->enum('type', ['milestone', 'badge', 'streak', 'special']); // Achievement type
            $table->json('criteria'); // Criteria for unlocking
            $table->string('icon')->nullable(); // Icon/image for achievement
            $table->string('color', 7)->default('#3B82F6'); // Color for achievement badge
            $table->integer('points')->default(0); // Points awarded
            $table->enum('rarity', ['common', 'uncommon', 'rare', 'epic', 'legendary'])->default('common');
            $table->boolean('is_active')->default(true); // Can be earned
            $table->boolean('is_hidden')->default(false); // Hidden until unlocked
            $table->integer('sort_order')->default(0); // Display order
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['type', 'rarity']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
