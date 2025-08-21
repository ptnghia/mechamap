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
        Schema::create('technical_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showcase_id')->nullable()->constrained('showcases')->onDelete('set null');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');

            // Product Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();

            // Pricing & Sales
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->storedAs('price * (1 - discount_percentage/100)');

            // Product Categories
            $table->foreignId('category_id')->constrained('product_categories');
            $table->json('tags')->nullable(); // ["CAD", "SolidWorks", "Mechanical"]

            // Technical Specifications
            $table->json('software_compatibility')->nullable(); // {"solidworks": "2020+", "autocad": "2019+"}
            $table->json('file_formats')->nullable(); // ["dwg", "step", "pdf", "docx"]
            $table->enum('complexity_level', ['beginner', 'intermediate', 'advanced'])->default('intermediate');
            $table->json('industry_applications')->nullable(); // ["automotive", "aerospace", "manufacturing"]

            // Digital Assets
            $table->json('preview_images')->nullable(); // Array of preview image URLs
            $table->json('sample_files')->nullable(); // Free sample files for preview
            $table->json('protected_files')->nullable(); // Encrypted files for buyers only
            $table->json('documentation_files')->nullable(); // Setup guides, tutorials

            // Sales & Analytics
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);

            // Status & Moderation
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'suspended'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->timestamp('featured_until')->nullable();

            // SEO & Marketing
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();

            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes support

            // Indexes
            $table->index(['seller_id', 'status']);
            $table->index(['category_id', 'is_featured']);
            $table->index(['price', 'status']);
            $table->index(['rating_average', 'rating_count']);
            $table->fullText(['title', 'description', 'keywords']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_products');
    }
};
