<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Nâng cấp hệ thống user roles để hỗ trợ business marketplace
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Thêm các fields cho business users
            $table->string('company_name')->nullable()->after('name')
                ->comment('Tên công ty (cho supplier, manufacturer, brand)');

            $table->string('business_license')->nullable()->after('company_name')
                ->comment('Số giấy phép kinh doanh');

            $table->string('tax_code')->nullable()->after('business_license')
                ->comment('Mã số thuế');

            $table->text('business_description')->nullable()->after('about_me')
                ->comment('Mô tả doanh nghiệp');

            $table->json('business_categories')->nullable()->after('business_description')
                ->comment('Danh mục kinh doanh chính');

            $table->string('business_phone')->nullable()->after('business_categories')
                ->comment('Số điện thoại doanh nghiệp');

            $table->string('business_email')->nullable()->after('business_phone')
                ->comment('Email doanh nghiệp');

            $table->text('business_address')->nullable()->after('business_email')
                ->comment('Địa chỉ doanh nghiệp');

            $table->boolean('is_verified_business')->default(false)->after('business_address')
                ->comment('Đã xác thực doanh nghiệp');

            $table->timestamp('business_verified_at')->nullable()->after('is_verified_business')
                ->comment('Thời gian xác thực doanh nghiệp');

            $table->foreignId('verified_by')->nullable()->after('business_verified_at')
                ->constrained('users')->onDelete('set null')
                ->comment('Admin xác thực');

            // Thêm subscription level cho business
            $table->enum('subscription_level', ['free', 'basic', 'premium', 'enterprise'])
                ->default('free')->after('verified_by')
                ->comment('Gói dịch vụ');

            // Thêm rating system
            $table->decimal('business_rating', 3, 2)->default(0)->after('subscription_level')
                ->comment('Đánh giá doanh nghiệp (0-5.0)');

            $table->integer('total_reviews')->default(0)->after('business_rating')
                ->comment('Tổng số đánh giá');
        });

        // Cập nhật ENUM cho role column (nếu cần)
        // Note: MySQL không hỗ trợ ALTER ENUM trực tiếp, cần workaround
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) DEFAULT 'member'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'company_name',
                'business_license',
                'tax_code',
                'business_description',
                'business_categories',
                'business_phone',
                'business_email',
                'business_address',
                'is_verified_business',
                'business_verified_at',
                'verified_by',
                'subscription_level',
                'business_rating',
                'total_reviews'
            ]);
        });

        // Khôi phục ENUM cũ
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','moderator','senior','member','guest') DEFAULT 'member'");
    }
};
