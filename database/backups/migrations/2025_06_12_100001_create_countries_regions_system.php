<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tạo bảng countries và regions để hỗ trợ đa quốc gia
     * cho diễn đàn cơ khí MechaMap
     */
    public function up(): void
    {
        // ====================================================================
        // COUNTRIES TABLE - Bảng quốc gia
        // ====================================================================
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            // Thông tin cơ bản quốc gia
            $table->string('name'); // Tên quốc gia
            $table->string('name_local')->nullable(); // Tên bằng tiếng địa phương
            $table->string('code', 2)->unique(); // Mã ISO 2 ký tự (VN, US, JP)
            $table->string('code_alpha3', 3)->unique(); // Mã ISO 3 ký tự (VNM, USA, JPN)
            $table->string('phone_code', 10)->nullable(); // Mã điện thoại (+84, +1)
            $table->string('currency_code', 3)->nullable(); // Mã tiền tệ (VND, USD)
            $table->string('currency_symbol', 10)->nullable(); // Ký hiệu tiền tệ (₫, $)

            // Cấu hình địa lý
            $table->string('continent')->nullable(); // Châu lục
            $table->string('timezone')->nullable(); // Múi giờ chính
            $table->json('timezones')->nullable(); // Danh sách múi giờ

            // Cấu hình ngôn ngữ
            $table->string('language_code', 5)->default('en'); // Ngôn ngữ chính (vi, en, ja)
            $table->json('languages')->nullable(); // Danh sách ngôn ngữ hỗ trợ

            // Cấu hình mechanical engineering
            $table->enum('measurement_system', ['metric', 'imperial', 'mixed'])->default('metric');
            $table->json('standard_organizations')->nullable(); // ["TCVN", "JIS", "ANSI"]
            $table->json('common_cad_software')->nullable(); // ["AutoCAD", "SolidWorks"]

            // Hiển thị và trạng thái
            $table->string('flag_emoji', 10)->nullable(); // 🇻🇳, 🇺🇸, 🇯🇵
            $table->string('flag_icon')->nullable(); // Path to flag image
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_user_registration')->default(true);

            // Forum configuration
            $table->json('mechanical_specialties')->nullable(); // Chuyên ngành cơ khí phổ biến
            $table->json('industrial_sectors')->nullable(); // Ngành công nghiệp chính

            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'sort_order']);
            $table->index(['continent', 'is_active']);
            $table->index(['allow_user_registration']);
        });

        // ====================================================================
        // REGIONS TABLE - Bảng khu vực/vùng miền
        // ====================================================================
        Schema::create('regions', function (Blueprint $table) {
            $table->id();

            // Quan hệ với quốc gia
            $table->foreignId('country_id')->constrained()->onDelete('cascade');

            // Thông tin cơ bản khu vực
            $table->string('name'); // Tên khu vực
            $table->string('name_local')->nullable(); // Tên địa phương
            $table->string('code', 10)->nullable(); // Mã khu vực (HCM, HN, NY, TK)
            $table->enum('type', [
                'province',     // Tỉnh/thành phố
                'state',        // Bang (US)
                'prefecture',   // Tỉnh (Japan)
                'region',       // Vùng
                'city',         // Thành phố lớn
                'zone'          // Khu vực đặc biệt
            ])->default('province');

            // Thông tin địa lý
            $table->string('timezone')->nullable(); // Múi giờ riêng (nếu khác quốc gia)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Cấu hình mechanical engineering
            $table->json('industrial_zones')->nullable(); // Khu công nghiệp
            $table->json('universities')->nullable(); // Trường đại học kỹ thuật
            $table->json('major_companies')->nullable(); // Công ty lớn
            $table->json('specialization_areas')->nullable(); // Lĩnh vực chuyên môn

            // Cấu hình forum
            $table->string('forum_moderator_timezone')->nullable();
            $table->json('local_standards')->nullable(); // Tiêu chuẩn địa phương
            $table->json('common_materials')->nullable(); // Vật liệu phổ biến

            // Hiển thị
            $table->string('icon')->nullable(); // Icon khu vực
            $table->string('color', 7)->nullable(); // Màu đại diện (#FF0000)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Khu vực nổi bật

            // Thống kê
            $table->integer('forum_count')->default(0);
            $table->integer('user_count')->default(0);
            $table->integer('thread_count')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['country_id', 'is_active', 'sort_order']);
            $table->index(['type', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['latitude', 'longitude']); // Geo queries
            $table->unique(['country_id', 'code']); // Unique code per country
        });

        // ====================================================================
        // CẬP NHẬT BẢNG FORUMS - Thêm region support
        // ====================================================================
        Schema::table('forums', function (Blueprint $table) {
            // Thêm category_id nếu chưa có
            if (!Schema::hasColumn('forums', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('description')->constrained()->onDelete('set null');
            }

            // Thêm relationship với region
            $table->foreignId('region_id')->nullable()->after('parent_id')->constrained()->onDelete('set null');

            // Cấu hình đa quốc gia
            $table->json('allowed_countries')->nullable()->after('region_id'); // Quốc gia được phép tham gia
            $table->json('primary_languages')->nullable()->after('allowed_countries'); // Ngôn ngữ chính
            $table->enum('scope', ['global', 'regional', 'country', 'local'])->default('global')->after('primary_languages');

            // Forum specialization theo khu vực
            $table->json('regional_standards')->nullable(); // Tiêu chuẩn kỹ thuật theo vùng
            $table->json('local_regulations')->nullable(); // Quy định địa phương

            // Thêm các field thống kê nếu chưa có
            if (!Schema::hasColumn('forums', 'thread_count')) {
                $table->integer('thread_count')->default(0);
            }
            if (!Schema::hasColumn('forums', 'post_count')) {
                $table->integer('post_count')->default(0);
            }
            if (!Schema::hasColumn('forums', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable();
            }
            if (!Schema::hasColumn('forums', 'last_thread_id')) {
                $table->foreignId('last_thread_id')->nullable()->constrained('threads')->onDelete('set null');
            }
            if (!Schema::hasColumn('forums', 'last_post_user_id')) {
                $table->foreignId('last_post_user_id')->nullable()->constrained('users')->onDelete('set null');
            }

            // Index cho multi-region queries
            $table->index(['region_id', 'is_private']);
            $table->index(['scope', 'is_private']);
        });

        // ====================================================================
        // CẬP NHẬT BẢNG USERS - Thêm location support
        // ====================================================================
        Schema::table('users', function (Blueprint $table) {
            // Geographic location - thêm sau existing location field nếu có
            $table->foreignId('country_id')->nullable()->after('location')->constrained()->onDelete('set null');
            $table->foreignId('region_id')->nullable()->after('country_id')->constrained()->onDelete('set null');

            // Professional location context
            $table->json('work_locations')->nullable()->after('region_id'); // Nơi làm việc
            $table->json('expertise_regions')->nullable()->after('work_locations'); // Khu vực chuyên môn

            // Indexes
            $table->index(['country_id', 'region_id']);
            $table->index(['country_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys từ dependent tables trước
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['region_id']);
            $table->dropIndex(['country_id', 'region_id']);
            $table->dropIndex(['country_id', 'is_active']);
            $table->dropColumn(['country_id', 'region_id', 'work_locations', 'expertise_regions']);
        });

        Schema::table('forums', function (Blueprint $table) {
            if (Schema::hasColumn('forums', 'region_id')) {
                $table->dropForeign(['region_id']);
                $table->dropIndex(['region_id', 'is_private']);
                $table->dropIndex(['scope', 'is_private']);
                $table->dropColumn([
                    'region_id', 'allowed_countries', 'primary_languages',
                    'scope', 'regional_standards', 'local_regulations'
                ]);
            }
        });

        // Drop main tables
        Schema::dropIfExists('regions');
        Schema::dropIfExists('countries');
    }
};
