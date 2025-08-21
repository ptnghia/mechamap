<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backup existing data
        $existingData = DB::table('page_seos')->get();

        Schema::table('page_seos', function (Blueprint $table) {
            // Add JSON columns for multilingual support
            $table->json('title_i18n')->nullable()->after('title');
            $table->json('description_i18n')->nullable()->after('description');
            $table->json('keywords_i18n')->nullable()->after('keywords');
            $table->json('og_title_i18n')->nullable()->after('og_title');
            $table->json('og_description_i18n')->nullable()->after('og_description');
            $table->json('twitter_title_i18n')->nullable()->after('twitter_title');
            $table->json('twitter_description_i18n')->nullable()->after('twitter_description');
        });

        // Migrate existing data to JSON format
        foreach ($existingData as $row) {
            $updateData = [];
            
            if ($row->title) {
                $updateData['title_i18n'] = json_encode([
                    'vi' => $row->title,
                    'en' => $this->translateToEnglish($row->title)
                ]);
            }
            
            if ($row->description) {
                $updateData['description_i18n'] = json_encode([
                    'vi' => $row->description,
                    'en' => $this->translateToEnglish($row->description)
                ]);
            }
            
            if ($row->keywords) {
                $updateData['keywords_i18n'] = json_encode([
                    'vi' => $row->keywords,
                    'en' => $this->translateKeywords($row->keywords)
                ]);
            }

            if (!empty($updateData)) {
                DB::table('page_seos')
                    ->where('id', $row->id)
                    ->update($updateData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            $table->dropColumn([
                'title_i18n',
                'description_i18n', 
                'keywords_i18n',
                'og_title_i18n',
                'og_description_i18n',
                'twitter_title_i18n',
                'twitter_description_i18n'
            ]);
        });
    }

    /**
     * Basic translation helper for migration
     */
    private function translateToEnglish(string $vietnamese): string
    {
        $translations = [
            'Trang chủ' => 'Home',
            'Diễn đàn' => 'Forums',
            'Showcase' => 'Showcase',
            'Marketplace' => 'Marketplace',
            'Thành viên' => 'Members',
            'Công cụ' => 'Tools',
            'Cộng đồng Kỹ thuật Cơ khí Việt Nam' => 'Vietnam Mechanical Engineering Community',
            'Dự án Kỹ thuật' => 'Engineering Projects',
            'Sản phẩm Kỹ thuật' => 'Engineering Products',
            'Hồ sơ' => 'Profile',
            'Tìm kiếm' => 'Search',
            'Duyệt' => 'Browse',
            'Bài viết' => 'Threads',
            'Có gì Mới' => 'What\'s New',
        ];

        foreach ($translations as $vi => $en) {
            if (str_contains($vietnamese, $vi)) {
                return str_replace($vi, $en, $vietnamese);
            }
        }

        return $vietnamese; // Fallback to original if no translation found
    }

    /**
     * Translate keywords
     */
    private function translateKeywords(string $keywords): string
    {
        $keywordTranslations = [
            'cơ khí' => 'mechanical',
            'kỹ thuật' => 'engineering',
            'thiết kế' => 'design',
            'cộng đồng' => 'community',
            'diễn đàn' => 'forum',
            'showcase' => 'showcase',
            'dự án' => 'projects',
            'sản phẩm' => 'products',
            'thành viên' => 'members',
            'công cụ' => 'tools',
            'tìm kiếm' => 'search',
            'bài viết' => 'threads',
        ];

        $result = $keywords;
        foreach ($keywordTranslations as $vi => $en) {
            $result = str_replace($vi, $en, $result);
        }

        return $result;
    }
};
