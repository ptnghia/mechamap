<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class MultilingualPageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌐 Starting Multilingual PageSeo seeding...');

        // Update existing records with multilingual data
        $this->updateHomePages();
        $this->updateForumPages();
        $this->updateShowcasePages();
        $this->updateMarketplacePages();
        $this->updateUserPages();
        $this->updateToolsPages();
        $this->updateSearchPages();

        $this->command->info('✅ Multilingual PageSeo seeding completed successfully!');
    }

    private function updateHomePages(): void
    {
        $homePages = [
            [
                'route_name' => 'home',
                'title_i18n' => [
                    'vi' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
                    'en' => 'MechaMap - Vietnam Mechanical Engineering Community'
                ],
                'description_i18n' => [
                    'vi' => 'Nền tảng forum hàng đầu cho cộng đồng kỹ sư cơ khí Việt Nam. Thảo luận CAD/CAM, chia sẻ kinh nghiệm thiết kế, công nghệ chế tạo và giải pháp kỹ thuật.',
                    'en' => 'Leading forum platform for Vietnam\'s mechanical engineering community. Discuss CAD/CAM, share design experience, manufacturing technology and technical solutions.'
                ],
                'keywords_i18n' => [
                    'vi' => 'mechanical engineering vietnam, CAD CAM, thiết kế cơ khí, forum kỹ thuật, cộng đồng kỹ sư',
                    'en' => 'mechanical engineering vietnam, CAD CAM, mechanical design, technical forum, engineering community'
                ],
                'og_title_i18n' => [
                    'vi' => 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam',
                    'en' => 'MechaMap - Vietnam Mechanical Engineering Community'
                ],
                'og_description_i18n' => [
                    'vi' => 'Tham gia cộng đồng kỹ sư cơ khí lớn nhất Việt Nam. Thảo luận về CAD/CAM, thiết kế máy móc, công nghệ chế tạo.',
                    'en' => 'Join Vietnam\'s largest mechanical engineering community. Discuss CAD/CAM, machine design, manufacturing technology.'
                ]
            ]
        ];

        foreach ($homePages as $pageData) {
            $this->updatePageSeo($pageData);
        }

        $this->command->info('✅ Updated home pages with multilingual data');
    }

    private function updateForumPages(): void
    {
        $forumPages = [
            [
                'route_name' => 'forums.index',
                'title_i18n' => [
                    'vi' => 'Diễn đàn Kỹ thuật Cơ khí',
                    'en' => 'Mechanical Engineering Forums'
                ],
                'description_i18n' => [
                    'vi' => 'Khám phá các diễn đàn chuyên ngành về kỹ thuật cơ khí, CAD/CAM, thiết kế máy móc và công nghệ chế tạo.',
                    'en' => 'Explore specialized forums on mechanical engineering, CAD/CAM, machine design and manufacturing technology.'
                ],
                'keywords_i18n' => [
                    'vi' => 'diễn đàn cơ khí, mechanical engineering forum, CAD CAM, thiết kế máy móc',
                    'en' => 'mechanical forum, mechanical engineering forum, CAD CAM, machine design'
                ]
            ],
            [
                'route_name' => 'categories.show',
                'title_i18n' => [
                    'vi' => 'Danh mục: {category_name}',
                    'en' => 'Category: {category_name}'
                ],
                'description_i18n' => [
                    'vi' => 'Khám phá tất cả diễn đàn trong danh mục {category_name}.',
                    'en' => 'Explore all forums in {category_name} category.'
                ]
            ],
            [
                'route_name' => 'forums.show',
                'title_i18n' => [
                    'vi' => '{forum_name} - Diễn đàn',
                    'en' => '{forum_name} - Forum'
                ],
                'description_i18n' => [
                    'vi' => 'Tham gia thảo luận trong diễn đàn {forum_name}.',
                    'en' => 'Join discussions in {forum_name} forum.'
                ]
            ]
        ];

        foreach ($forumPages as $pageData) {
            $this->updatePageSeo($pageData);
        }

        $this->command->info('✅ Updated forum pages with multilingual data');
    }

    private function updateShowcasePages(): void
    {
        $showcasePages = [
            [
                'route_name' => 'showcase.index',
                'title_i18n' => [
                    'vi' => 'Showcase Dự án Kỹ thuật',
                    'en' => 'Engineering Project Showcase'
                ],
                'description_i18n' => [
                    'vi' => 'Khám phá các dự án kỹ thuật xuất sắc từ cộng đồng kỹ sư cơ khí Việt Nam.',
                    'en' => 'Discover outstanding engineering projects from Vietnam\'s mechanical engineering community.'
                ],
                'keywords_i18n' => [
                    'vi' => 'showcase dự án, engineering projects, mechanical design, CAD projects',
                    'en' => 'project showcase, engineering projects, mechanical design, CAD projects'
                ]
            ],
            [
                'route_name' => 'showcase.show',
                'title_i18n' => [
                    'vi' => '{showcase_title} - Showcase',
                    'en' => '{showcase_title} - Showcase'
                ],
                'description_i18n' => [
                    'vi' => 'Chi tiết dự án showcase: {showcase_description}',
                    'en' => 'Showcase project details: {showcase_description}'
                ]
            ]
        ];

        foreach ($showcasePages as $pageData) {
            $this->updatePageSeo($pageData);
        }

        $this->command->info('✅ Updated showcase pages with multilingual data');
    }

    private function updateMarketplacePages(): void
    {
        $marketplacePages = [
            [
                'route_name' => 'marketplace.index',
                'title_i18n' => [
                    'vi' => 'Marketplace Sản phẩm Kỹ thuật',
                    'en' => 'Engineering Products Marketplace'
                ],
                'description_i18n' => [
                    'vi' => 'Thị trường mua bán sản phẩm kỹ thuật, phần mềm CAD/CAM, thiết bị cơ khí và dịch vụ kỹ thuật.',
                    'en' => 'Marketplace for engineering products, CAD/CAM software, mechanical equipment and technical services.'
                ],
                'keywords_i18n' => [
                    'vi' => 'marketplace cơ khí, sản phẩm kỹ thuật, CAD software, thiết bị cơ khí',
                    'en' => 'mechanical marketplace, engineering products, CAD software, mechanical equipment'
                ]
            ]
        ];

        foreach ($marketplacePages as $pageData) {
            $this->updatePageSeo($pageData);
        }

        $this->command->info('✅ Updated marketplace pages with multilingual data');
    }

    private function updateUserPages(): void
    {
        $userPages = [
            [
                'route_name' => 'users.index',
                'title_i18n' => [
                    'vi' => 'Thành viên Cộng đồng',
                    'en' => 'Community Members'
                ],
                'description_i18n' => [
                    'vi' => 'Khám phá cộng đồng kỹ sư cơ khí Việt Nam và kết nối với các chuyên gia trong ngành.',
                    'en' => 'Discover Vietnam\'s mechanical engineering community and connect with industry experts.'
                ]
            ],
            [
                'route_name' => 'users.show',
                'title_i18n' => [
                    'vi' => 'Hồ sơ {user_name}',
                    'en' => '{user_name} Profile'
                ],
                'description_i18n' => [
                    'vi' => 'Xem hồ sơ và hoạt động của {user_name} trong cộng đồng MechaMap.',
                    'en' => 'View {user_name}\'s profile and activities in MechaMap community.'
                ]
            ]
        ];

        foreach ($userPages as $pageData) {
            $this->updatePageSeo($pageData);
        }

        $this->command->info('✅ Updated user pages with multilingual data');
    }

    private function updateToolsPages(): void
    {
        $toolsPages = [
            [
                'route_name' => 'tools.index',
                'title_i18n' => [
                    'vi' => 'Công cụ Kỹ thuật',
                    'en' => 'Engineering Tools'
                ],
                'description_i18n' => [
                    'vi' => 'Bộ sưu tập công cụ hữu ích cho kỹ sư cơ khí: calculators, converters, reference tables.',
                    'en' => 'Collection of useful tools for mechanical engineers: calculators, converters, reference tables.'
                ]
            ]
        ];

        foreach ($toolsPages as $pageData) {
            $this->updatePageSeo($pageData);
        }

        $this->command->info('✅ Updated tools pages with multilingual data');
    }

    private function updateSearchPages(): void
    {
        $searchPages = [
            [
                'route_name' => 'search.index',
                'title_i18n' => [
                    'vi' => 'Tìm kiếm',
                    'en' => 'Search'
                ],
                'description_i18n' => [
                    'vi' => 'Tìm kiếm nội dung trong cộng đồng kỹ thuật cơ khí: bài viết, showcase, sản phẩm và thành viên.',
                    'en' => 'Search content in mechanical engineering community: threads, showcases, products and members.'
                ]
            ]
        ];

        foreach ($searchPages as $pageData) {
            $this->updatePageSeo($pageData);
        }

        $this->command->info('✅ Updated search pages with multilingual data');
    }

    private function updatePageSeo(array $data): void
    {
        $routeName = $data['route_name'];
        $pageSeo = PageSeo::where('route_name', $routeName)->first();

        if ($pageSeo) {
            $pageSeo->update($data);
            $this->command->info("  ↳ Updated: {$routeName}");
        } else {
            $this->command->warn("  ⚠️  Not found: {$routeName}");
        }
    }
}
