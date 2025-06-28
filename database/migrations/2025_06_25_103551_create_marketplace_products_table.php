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
        Schema::create('marketplace_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('sku')->unique();

            // Seller Information
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_category_id')->nullable()->constrained()->onDelete('set null');

            // Product Type & Classification
            $table->enum('product_type', ['physical', 'digital', 'service'])->default('physical');
            $table->enum('seller_type', ['supplier', 'manufacturer', 'brand'])->default('supplier');
            $table->string('industry_category')->nullable(); // automotive, aerospace, manufacturing, etc.

            // Pricing
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->boolean('is_on_sale')->default(false);
            $table->timestamp('sale_starts_at')->nullable();
            $table->timestamp('sale_ends_at')->nullable();

            // Inventory Management
            $table->integer('stock_quantity')->default(0);
            $table->boolean('manage_stock')->default(true);
            $table->boolean('in_stock')->default(true);
            $table->integer('low_stock_threshold')->default(5);

            // Technical Specifications (for mechanical products)
            $table->json('technical_specs')->nullable(); // dimensions, weight, material, etc.
            $table->json('mechanical_properties')->nullable(); // strength, hardness, elasticity
            $table->string('material')->nullable(); // Steel, Aluminum, Composite, etc.
            $table->string('manufacturing_process')->nullable(); // CNC, 3D Printing, Casting, etc.
            $table->json('standards_compliance')->nullable(); // ISO, ASME, DIN standards

            // Digital Product Specific
            $table->json('file_formats')->nullable(); // CAD formats: .dwg, .step, .iges
            $table->json('software_compatibility')->nullable(); // SolidWorks, AutoCAD, CATIA
            $table->decimal('file_size_mb', 8, 2)->nullable();
            $table->integer('download_limit')->nullable();

            // Media & Attachments
            $table->json('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('attachments')->nullable(); // technical docs, manuals, certificates

            // SEO & Marketing
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();

            // Status & Moderation
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'suspended'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();

            // Analytics
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->integer('purchase_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);

            // Display & Sorting
            $table->integer('display_order')->default(0);
            $table->timestamp('featured_at')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['seller_id', 'status']);
            $table->index(['product_category_id', 'status']);
            $table->index(['product_type', 'seller_type']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['created_at', 'status']);
            $table->index('slug');
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_products');
    }
};
