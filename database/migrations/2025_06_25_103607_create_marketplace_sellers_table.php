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
        Schema::create('marketplace_sellers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // User Relationship
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Seller Type & Classification
            $table->enum('seller_type', ['supplier', 'manufacturer', 'brand'])->default('supplier');
            $table->enum('business_type', ['individual', 'company', 'corporation'])->default('company');

            // Business Information
            $table->string('business_name');
            $table->string('business_registration_number')->nullable();
            $table->string('tax_identification_number')->nullable();
            $table->text('business_description')->nullable();

            // Contact Information
            $table->string('contact_person_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->json('business_address')->nullable();
            $table->string('website_url')->nullable();

            // Industry & Specialization
            $table->json('industry_categories')->nullable(); // automotive, aerospace, manufacturing
            $table->json('specializations')->nullable(); // CNC machining, 3D printing, casting
            $table->json('certifications')->nullable(); // ISO 9001, AS9100, etc.
            $table->json('capabilities')->nullable(); // manufacturing capabilities

            // Business Verification
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('verification_documents')->nullable();
            $table->text('verification_notes')->nullable();

            // Seller Performance
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->integer('total_sales')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0.00);
            $table->integer('total_products')->default(0);
            $table->integer('active_products')->default(0);

            // Commission & Payments
            $table->decimal('commission_rate', 5, 2)->default(5.00); // percentage
            $table->decimal('pending_earnings', 12, 2)->default(0.00);
            $table->decimal('available_earnings', 12, 2)->default(0.00);
            $table->decimal('total_earnings', 15, 2)->default(0.00);
            $table->json('payment_methods')->nullable(); // bank account, e-wallet info

            // Seller Settings
            $table->boolean('auto_approve_orders')->default(false);
            $table->integer('processing_time_days')->default(3);
            $table->json('shipping_methods')->nullable();
            $table->json('return_policy')->nullable();
            $table->json('terms_conditions')->nullable();

            // Status & Activity
            $table->enum('status', ['active', 'inactive', 'suspended', 'banned'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();

            // Store Customization
            $table->string('store_name')->nullable();
            $table->string('store_slug')->unique()->nullable();
            $table->text('store_description')->nullable();
            $table->string('store_logo')->nullable();
            $table->string('store_banner')->nullable();
            $table->json('store_settings')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id']);
            $table->index(['seller_type', 'status']);
            $table->index(['verification_status']);
            $table->index(['is_featured', 'status']);
            $table->index(['store_slug']);
            $table->index(['rating_average', 'rating_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_sellers');
    }
};
