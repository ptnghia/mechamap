<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Products table for MechaMap marketplace system
     * Supports both physical products and digital/technical files
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Basic Product Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('sku')->unique()->nullable();

            // Seller Information
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade')
                ->comment('User bán sản phẩm (supplier, manufacturer, brand)');

            // Product Classification
            $table->foreignId('product_category_id')->constrained()->onDelete('cascade');
            $table->enum('product_type', ['physical', 'digital', 'service', 'technical_file'])
                ->comment('Loại sản phẩm: vật lý, kỹ thuật số, dịch vụ, file kỹ thuật');

            // Business Role Specific
            $table->enum('seller_type', ['supplier', 'manufacturer', 'brand'])
                ->comment('Loại người bán theo business role');

            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->boolean('is_on_sale')->default(false);
            $table->timestamp('sale_starts_at')->nullable();
            $table->timestamp('sale_ends_at')->nullable();

            // Inventory (for physical products)
            $table->integer('stock_quantity')->default(0);
            $table->boolean('manage_stock')->default(true);
            $table->boolean('in_stock')->default(true);
            $table->integer('low_stock_threshold')->default(5);

            // Technical Specifications (for mechanical products)
            $table->json('technical_specs')->nullable()
                ->comment('Thông số kỹ thuật: material, dimensions, tolerance, etc.');
            $table->json('mechanical_properties')->nullable()
                ->comment('Tính chất cơ học: strength, hardness, elasticity');
            $table->string('material')->nullable();
            $table->string('manufacturing_process')->nullable();
            $table->json('standards_compliance')->nullable()
                ->comment('Tiêu chuẩn tuân thủ: ISO, ASME, ASTM, etc.');

            // Digital/File Products
            $table->json('file_formats')->nullable()
                ->comment('Định dạng file: DWG, STEP, PDF, etc.');
            $table->string('software_compatibility')->nullable()
                ->comment('Tương thích phần mềm: AutoCAD, SolidWorks, etc.');
            $table->decimal('file_size_mb', 8, 2)->nullable();
            $table->integer('download_limit')->nullable()
                ->comment('Giới hạn số lần download');

            // Media
            $table->json('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('attachments')->nullable();
            $table->string('video_url')->nullable();

            // SEO & Marketing
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('keywords')->nullable();

            // Status & Visibility
            $table->enum('status', ['draft', 'published', 'archived', 'out_of_stock'])
                ->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_digital_download')->default(false);
            $table->boolean('requires_shipping')->default(true);

            // Quality & Reviews
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('wishlist_count')->default(0);

            // Shipping (for physical products)
            $table->decimal('weight', 8, 2)->nullable()->comment('Weight in kg');
            $table->json('dimensions')->nullable()->comment('L x W x H in cm');
            $table->string('shipping_class')->nullable();

            // Business Logic
            $table->boolean('requires_approval')->default(false)
                ->comment('Cần phê duyệt trước khi bán');
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['seller_id', 'status', 'created_at']);
            $table->index(['product_category_id', 'product_type']);
            $table->index(['seller_type', 'is_featured', 'average_rating']);
            $table->index(['price', 'is_on_sale']);
            $table->index(['status', 'is_approved', 'created_at']);

            // Full-text search (excluding JSON columns)
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
