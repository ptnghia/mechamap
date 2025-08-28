<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class AddCADLibraryTranslations extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $translations = [
            // CAD Library Core
            'cad.library.title' => [
                'vi' => 'Thư viện CAD',
                'en' => 'CAD Library'
            ],
            'cad.library.description' => [
                'vi' => 'Tải xuống và chia sẻ file CAD, mô hình 3D và bản vẽ kỹ thuật',
                'en' => 'Download and share CAD files, 3D models and technical drawings'
            ],

            // Navigation & Actions
            'cad.library.my_files' => [
                'vi' => 'File của tôi',
                'en' => 'My Files'
            ],
            'cad.library.upload_file' => [
                'vi' => 'Tải lên file',
                'en' => 'Upload File'
            ],
            'cad.library.export' => [
                'vi' => 'Xuất dữ liệu',
                'en' => 'Export Data'
            ],

            // Statistics
            'cad.library.cad_files' => [
                'vi' => 'File CAD',
                'en' => 'CAD Files'
            ],
            'cad.library.downloads' => [
                'vi' => 'Lượt tải',
                'en' => 'Downloads'
            ],
            'cad.library.file_types' => [
                'vi' => 'Loại file',
                'en' => 'File Types'
            ],
            'cad.library.contributors' => [
                'vi' => 'Người đóng góp',
                'en' => 'Contributors'
            ],

            // Search & Filters
            'cad.library.search_cad_files' => [
                'vi' => 'Tìm kiếm file CAD',
                'en' => 'Search CAD Files'
            ],
            'cad.library.search_placeholder' => [
                'vi' => 'Tìm theo tên file, mô tả hoặc từ khóa...',
                'en' => 'Search by filename, description or keywords...'
            ],
            'cad.library.category' => [
                'vi' => 'Danh mục',
                'en' => 'Category'
            ],
            'cad.library.all_categories' => [
                'vi' => 'Tất cả danh mục',
                'en' => 'All Categories'
            ],
            'cad.library.file_type' => [
                'vi' => 'Loại file',
                'en' => 'File Type'
            ],
            'cad.library.all_types' => [
                'vi' => 'Tất cả loại',
                'en' => 'All Types'
            ],
            'cad.library.software' => [
                'vi' => 'Phần mềm',
                'en' => 'Software'
            ],
            'cad.library.all_software' => [
                'vi' => 'Tất cả phần mềm',
                'en' => 'All Software'
            ],
            'cad.library.sort_by' => [
                'vi' => 'Sắp xếp theo',
                'en' => 'Sort By'
            ],
            'cad.library.newest' => [
                'vi' => 'Mới nhất',
                'en' => 'Newest'
            ],
            'cad.library.most_downloaded' => [
                'vi' => 'Tải nhiều nhất',
                'en' => 'Most Downloaded'
            ],
            'cad.library.highest_rated' => [
                'vi' => 'Đánh giá cao nhất',
                'en' => 'Highest Rated'
            ],
            'cad.library.name_az' => [
                'vi' => 'Tên A-Z',
                'en' => 'Name A-Z'
            ],

            // File Details
            'cad.library.views' => [
                'vi' => 'Lượt xem',
                'en' => 'Views'
            ],
            'cad.library.rating' => [
                'vi' => 'Đánh giá',
                'en' => 'Rating'
            ],
            'cad.library.files' => [
                'vi' => 'File đính kèm',
                'en' => 'Attached Files'
            ],
            'cad.library.by' => [
                'vi' => 'bởi',
                'en' => 'by'
            ],
            'cad.library.view' => [
                'vi' => 'Xem',
                'en' => 'View'
            ],
            'cad.library.download' => [
                'vi' => 'Tải xuống',
                'en' => 'Download'
            ],
            'cad.library.login' => [
                'vi' => 'Đăng nhập',
                'en' => 'Login'
            ],

            // No Results
            'cad.library.no_results' => [
                'vi' => 'Không tìm thấy kết quả',
                'en' => 'No Results Found'
            ],
            'cad.library.no_results_description' => [
                'vi' => 'Thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm để tìm thấy file CAD phù hợp.',
                'en' => 'Try adjusting your filters or search terms to find suitable CAD files.'
            ],
            'cad.library.clear_filters' => [
                'vi' => 'Xóa bộ lọc',
                'en' => 'Clear Filters'
            ],
            'cad.library.browse_showcases' => [
                'vi' => 'Duyệt Showcase',
                'en' => 'Browse Showcases'
            ],

            // Popular Software
            'cad.library.popular_cad_software' => [
                'vi' => 'Phần mềm CAD phổ biến',
                'en' => 'Popular CAD Software'
            ],
            'cad.library.files_available' => [
                'vi' => 'file có sẵn',
                'en' => 'files available'
            ]
        ];

        foreach ($translations as $key => $values) {
            foreach ($values as $locale => $content) {
                // Parse group from key (e.g., 'cad.library.title' -> 'cad')
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

        $this->command->info('CAD Library translations added successfully!');
    }
}
