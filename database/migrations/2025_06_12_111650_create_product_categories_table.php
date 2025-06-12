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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon URL
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->decimal('commission_rate', 5, 2)->default(10.00); // Platform commission %

            // Engineering specific categories
            $table->string('engineering_discipline', 50)->nullable(); // "mechanical", "electrical", "civil"
            $table->json('required_software')->nullable(); // Software tags for this category

            // Statistics
            $table->unsignedInteger('product_count')->default(0);
            $table->unsignedInteger('total_sales')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['parent_id', 'is_active']);
            $table->index('engineering_discipline');
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
