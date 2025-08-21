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
        Schema::create('business_categories', function (Blueprint $table) {
            $table->char('id', 36)->primary(); // UUID compatible with hosting
            $table->string('key', 100)->unique(); // Unique identifier key
            $table->string('name_vi', 255); // Vietnamese name
            $table->string('name_en', 255); // English name
            $table->text('description_vi')->nullable(); // Vietnamese description
            $table->text('description_en')->nullable(); // English description
            $table->string('icon', 50)->nullable(); // FontAwesome icon class
            $table->string('color', 7)->default('#007bff'); // Hex color code
            $table->integer('sort_order')->default(0); // Display order
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps();

            // Indexes for performance
            $table->index(['is_active', 'sort_order']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_categories');
    }
};
