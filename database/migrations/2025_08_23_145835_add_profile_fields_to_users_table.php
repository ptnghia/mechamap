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
        Schema::table('users', function (Blueprint $table) {
            // Professional information
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable()->after('about_me')
                    ->comment('Chức danh công việc');
            }

            if (!Schema::hasColumn('users', 'company')) {
                $table->string('company')->nullable()->after('job_title')
                    ->comment('Tên công ty');
            }

            if (!Schema::hasColumn('users', 'experience_years')) {
                $table->string('experience_years')->nullable()->after('company')
                    ->comment('Số năm kinh nghiệm');
            }

            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('experience_years')
                    ->comment('Giới thiệu bản thân');
            }

            if (!Schema::hasColumn('users', 'skills')) {
                $table->text('skills')->nullable()->after('bio')
                    ->comment('Kỹ năng chuyên môn');
            }

            // Contact information
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('skills')
                    ->comment('Số điện thoại');
            }

            // Social links
            if (!Schema::hasColumn('users', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable()->after('website')
                    ->comment('LinkedIn profile URL');
            }

            if (!Schema::hasColumn('users', 'github_url')) {
                $table->string('github_url')->nullable()->after('linkedin_url')
                    ->comment('GitHub profile URL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'job_title',
                'company',
                'experience_years',
                'bio',
                'skills',
                'phone',
                'linkedin_url',
                'github_url'
            ]);
        });
    }
};
