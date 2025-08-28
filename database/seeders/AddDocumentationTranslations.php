<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class AddDocumentationTranslations extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $translations = [
            // Documentation Core
            'docs.title' => [
                'vi' => 'Cổng thông tin Tài liệu',
                'en' => 'Documentation Portal'
            ],
            'docs.portal_description' => [
                'vi' => 'Trung tâm tài liệu kỹ thuật và hướng dẫn sử dụng MechaMap',
                'en' => 'Technical documentation and user guides for MechaMap'
            ],
            'docs.documentation_portal' => [
                'vi' => 'Cổng thông tin Tài liệu',
                'en' => 'Documentation Portal'
            ],
            
            // Statistics
            'docs.documents' => [
                'vi' => 'Tài liệu',
                'en' => 'Documents'
            ],
            'docs.categories' => [
                'vi' => 'Danh mục',
                'en' => 'Categories'
            ],
            'docs.total_views' => [
                'vi' => 'Lượt xem',
                'en' => 'Total Views'
            ],
            'docs.total_downloads' => [
                'vi' => 'Lượt tải',
                'en' => 'Total Downloads'
            ],
            
            // Search & Filters
            'docs.search_documentation' => [
                'vi' => 'Tìm kiếm tài liệu',
                'en' => 'Search Documentation'
            ],
            'docs.search_placeholder' => [
                'vi' => 'Tìm kiếm tài liệu...',
                'en' => 'Search documentation...'
            ],
            'docs.search' => [
                'vi' => 'Tìm kiếm',
                'en' => 'Search'
            ],
            'docs.category' => [
                'vi' => 'Danh mục',
                'en' => 'Category'
            ],
            'docs.all_categories' => [
                'vi' => 'Tất cả danh mục',
                'en' => 'All Categories'
            ],
            'docs.content_type' => [
                'vi' => 'Loại nội dung',
                'en' => 'Content Type'
            ],
            'docs.all_types' => [
                'vi' => 'Tất cả loại',
                'en' => 'All Types'
            ],
            'docs.difficulty' => [
                'vi' => 'Độ khó',
                'en' => 'Difficulty'
            ],
            'docs.all_levels' => [
                'vi' => 'Tất cả cấp độ',
                'en' => 'All Levels'
            ],
            'docs.sort_by' => [
                'vi' => 'Sắp xếp theo',
                'en' => 'Sort By'
            ],
            'docs.clear_filters' => [
                'vi' => 'Xóa bộ lọc',
                'en' => 'Clear Filters'
            ],
            
            // Content Types
            'docs.guide' => [
                'vi' => 'Hướng dẫn',
                'en' => 'Guide'
            ],
            'docs.api' => [
                'vi' => 'API',
                'en' => 'API'
            ],
            'docs.tutorial' => [
                'vi' => 'Hướng dẫn thực hành',
                'en' => 'Tutorial'
            ],
            'docs.reference' => [
                'vi' => 'Tài liệu tham khảo',
                'en' => 'Reference'
            ],
            'docs.faq' => [
                'vi' => 'Câu hỏi thường gặp',
                'en' => 'FAQ'
            ],
            
            // Difficulty Levels
            'docs.beginner' => [
                'vi' => 'Cơ bản',
                'en' => 'Beginner'
            ],
            'docs.intermediate' => [
                'vi' => 'Trung cấp',
                'en' => 'Intermediate'
            ],
            'docs.advanced' => [
                'vi' => 'Nâng cao',
                'en' => 'Advanced'
            ],
            'docs.expert' => [
                'vi' => 'Chuyên gia',
                'en' => 'Expert'
            ],
            
            // Sort Options
            'docs.newest' => [
                'vi' => 'Mới nhất',
                'en' => 'Newest'
            ],
            'docs.most_viewed' => [
                'vi' => 'Xem nhiều nhất',
                'en' => 'Most Viewed'
            ],
            'docs.most_downloaded' => [
                'vi' => 'Tải nhiều nhất',
                'en' => 'Most Downloaded'
            ],
            'docs.highest_rated' => [
                'vi' => 'Đánh giá cao nhất',
                'en' => 'Highest Rated'
            ],
            'docs.title_az' => [
                'vi' => 'Tiêu đề A-Z',
                'en' => 'Title A-Z'
            ],
            
            // Content Sections
            'docs.recent_documentation' => [
                'vi' => 'Tài liệu gần đây',
                'en' => 'Recent Documentation'
            ],
            'docs.featured_documentation' => [
                'vi' => 'Tài liệu nổi bật',
                'en' => 'Featured Documentation'
            ],
            'docs.quick_links' => [
                'vi' => 'Liên kết nhanh',
                'en' => 'Quick Links'
            ],
            
            // Quick Links
            'docs.user_guides' => [
                'vi' => 'Hướng dẫn người dùng',
                'en' => 'User Guides'
            ],
            'docs.tutorials' => [
                'vi' => 'Hướng dẫn thực hành',
                'en' => 'Tutorials'
            ],
            'docs.api_documentation' => [
                'vi' => 'Tài liệu API',
                'en' => 'API Documentation'
            ],
            'docs.beginner_guides' => [
                'vi' => 'Hướng dẫn cơ bản',
                'en' => 'Beginner Guides'
            ],
            'docs.advanced_topics' => [
                'vi' => 'Chủ đề nâng cao',
                'en' => 'Advanced Topics'
            ],
            'docs.need_help' => [
                'vi' => 'Cần trợ giúp?',
                'en' => 'Need Help?'
            ]
        ];

        foreach ($translations as $key => $values) {
            foreach ($values as $locale => $content) {
                // Parse group from key (e.g., 'docs.title' -> 'docs')
                $keyParts = explode('.', $key);
                $groupName = $keyParts[0] ?? 'general';
                
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'locale' => $locale
                    ],
                    [
                        'content' => $content,
                        'group_name' => $groupName,
                        'is_active' => true,
                        'created_by' => 1, // Default to admin user
                        'updated_by' => 1
                    ]
                );
            }
        }

        $this->command->info('Documentation translations added successfully!');
    }
}
