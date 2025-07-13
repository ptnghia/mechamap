<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Category;
use App\Models\Forum;
use App\Models\ProductCategory;
use App\Models\Setting;

class ProductionSeeder extends Seeder
{
    /**
     * 🚀 Production Seeder for MechaMap
     * 
     * Seeds essential data for production deployment:
     * - Admin user
     * - Roles and permissions
     * - Basic categories and forums
     * - Essential settings
     */
    public function run(): void
    {
        $this->command->info('🚀 Seeding production data for MechaMap...');
        
        // Seed in order of dependencies
        $this->seedRolesAndPermissions();
        $this->seedAdminUser();
        $this->seedCategories();
        $this->seedForums();
        $this->seedProductCategories();
        $this->seedEssentialSettings();
        
        $this->command->info('✅ Production seeding completed successfully!');
    }

    /**
     * Seed roles and permissions
     */
    private function seedRolesAndPermissions(): void
    {
        $this->command->info('👥 Seeding roles and permissions...');
        
        // Create permissions
        $permissions = [
            'view_admin_panel',
            'manage_users',
            'manage_content',
            'manage_marketplace',
            'manage_settings',
            'moderate_content',
            'create_threads',
            'create_showcases',
            'buy_products',
            'sell_products',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Create roles with permissions
        $roles = [
            'super_admin' => ['view_admin_panel', 'manage_users', 'manage_content', 'manage_marketplace', 'manage_settings'],
            'admin' => ['view_admin_panel', 'manage_users', 'manage_content', 'moderate_content'],
            'moderator' => ['view_admin_panel', 'moderate_content'],
            'senior_member' => ['create_threads', 'create_showcases', 'buy_products'],
            'member' => ['create_threads', 'buy_products'],
            'guest' => [],
            'supplier' => ['create_threads', 'create_showcases', 'buy_products', 'sell_products'],
            'manufacturer' => ['create_threads', 'create_showcases', 'buy_products', 'sell_products'],
        ];
        
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            if (!empty($rolePermissions)) {
                $permissions = Permission::whereIn('name', $rolePermissions)->get();
                $role->syncPermissions($permissions);
            }
        }
        
        $this->command->info('✅ Roles and permissions seeded');
    }

    /**
     * Seed admin user
     */
    private function seedAdminUser(): void
    {
        $this->command->info('👤 Seeding admin user...');
        
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@mechamap.vn'],
            [
                'username' => 'admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('admin123456'),
                'email_verified_at' => now(),
                'is_active' => true,
                'avatar' => '/images/avatars/admin-avatar.jpg',
                'bio' => 'System Administrator',
                'location' => 'Vietnam',
                'website' => 'https://mechamap.com',
                'phone' => '+84123456789',
            ]
        );
        
        // Assign super admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $adminUser->assignRole($superAdminRole);
        }
        
        $this->command->info('✅ Admin user seeded');
    }

    /**
     * Seed basic categories
     */
    private function seedCategories(): void
    {
        $this->command->info('📂 Seeding categories...');
        
        $categories = [
            [
                'name' => 'Cơ Khí Chế Tạo',
                'slug' => 'co-khi-che-tao',
                'description' => 'Thảo luận về cơ khí chế tạo máy, gia công, sản xuất',
                'icon' => 'fas fa-cogs',
                'color' => '#3498db',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Tự Động Hóa',
                'slug' => 'tu-dong-hoa',
                'description' => 'Hệ thống tự động hóa, PLC, SCADA, IoT',
                'icon' => 'fas fa-robot',
                'color' => '#e74c3c',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'CAD/CAM/CAE',
                'slug' => 'cad-cam-cae',
                'description' => 'Thiết kế hỗ trợ máy tính, mô phỏng, phân tích',
                'icon' => 'fas fa-drafting-compass',
                'color' => '#f39c12',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Vật Liệu Kỹ Thuật',
                'slug' => 'vat-lieu-ky-thuat',
                'description' => 'Vật liệu cơ khí, kim loại, composite, polymer',
                'icon' => 'fas fa-cube',
                'color' => '#9b59b6',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Marketplace',
                'slug' => 'marketplace',
                'description' => 'Mua bán thiết bị, linh kiện, dịch vụ kỹ thuật',
                'icon' => 'fas fa-shopping-cart',
                'color' => '#27ae60',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];
        
        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
        
        $this->command->info('✅ Categories seeded');
    }

    /**
     * Seed basic forums
     */
    private function seedForums(): void
    {
        $this->command->info('💬 Seeding forums...');
        
        $categories = Category::all();
        
        foreach ($categories as $category) {
            $forums = $this->getForumsForCategory($category->slug);
            
            foreach ($forums as $forumData) {
                Forum::firstOrCreate(
                    [
                        'slug' => $forumData['slug'],
                        'category_id' => $category->id,
                    ],
                    array_merge($forumData, ['category_id' => $category->id])
                );
            }
        }
        
        $this->command->info('✅ Forums seeded');
    }

    /**
     * Get forums for specific category
     */
    private function getForumsForCategory(string $categorySlug): array
    {
        $forumsByCategory = [
            'co-khi-che-tao' => [
                [
                    'name' => 'Thiết Kế Máy',
                    'slug' => 'thiet-ke-may',
                    'description' => 'Thiết kế máy móc, cơ cấu, hệ thống cơ khí',
                    'icon' => 'fas fa-drafting-compass',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Gia Công Cơ Khí',
                    'slug' => 'gia-cong-co-khi',
                    'description' => 'Công nghệ gia công, máy công cụ, dụng cụ cắt',
                    'icon' => 'fas fa-tools',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            ],
            'tu-dong-hoa' => [
                [
                    'name' => 'PLC & SCADA',
                    'slug' => 'plc-scada',
                    'description' => 'Lập trình PLC, hệ thống SCADA, HMI',
                    'icon' => 'fas fa-microchip',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'name' => 'IoT & Industry 4.0',
                    'slug' => 'iot-industry-4-0',
                    'description' => 'Internet of Things, Industry 4.0, Smart Manufacturing',
                    'icon' => 'fas fa-wifi',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            ],
            'cad-cam-cae' => [
                [
                    'name' => 'SolidWorks',
                    'slug' => 'solidworks',
                    'description' => 'Thiết kế 3D với SolidWorks, mô phỏng, rendering',
                    'icon' => 'fas fa-cube',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'name' => 'AutoCAD',
                    'slug' => 'autocad',
                    'description' => 'Vẽ kỹ thuật 2D/3D với AutoCAD',
                    'icon' => 'fas fa-pencil-ruler',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            ],
            'vat-lieu-ky-thuat' => [
                [
                    'name' => 'Thép & Kim Loại',
                    'slug' => 'thep-kim-loai',
                    'description' => 'Vật liệu thép, hợp kim, xử lý nhiệt',
                    'icon' => 'fas fa-hammer',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            ],
            'marketplace' => [
                [
                    'name' => 'Mua Bán Thiết Bị',
                    'slug' => 'mua-ban-thiet-bi',
                    'description' => 'Mua bán máy móc, thiết bị công nghiệp',
                    'icon' => 'fas fa-handshake',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            ],
        ];
        
        return $forumsByCategory[$categorySlug] ?? [];
    }

    /**
     * Seed product categories
     */
    private function seedProductCategories(): void
    {
        $this->command->info('🛒 Seeding product categories...');
        
        $productCategories = [
            [
                'name' => 'File CAD/CAM',
                'slug' => 'file-cad-cam',
                'description' => 'File thiết kế 3D, bản vẽ kỹ thuật, template',
                'icon' => 'fas fa-file-alt',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Linh Kiện Điện Tử',
                'slug' => 'linh-kien-dien-tu',
                'description' => 'IC, sensor, module điện tử, board mạch',
                'icon' => 'fas fa-microchip',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Thiết Bị Cơ Khí',
                'slug' => 'thiet-bi-co-khi',
                'description' => 'Máy móc, dụng cụ, thiết bị gia công',
                'icon' => 'fas fa-cogs',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Vật Liệu Kỹ Thuật',
                'slug' => 'vat-lieu-ky-thuat',
                'description' => 'Thép, nhôm, nhựa kỹ thuật, composite',
                'icon' => 'fas fa-cube',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];
        
        foreach ($productCategories as $categoryData) {
            ProductCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
        
        $this->command->info('✅ Product categories seeded');
    }

    /**
     * Seed essential settings
     */
    private function seedEssentialSettings(): void
    {
        $this->command->info('⚙️ Seeding essential settings...');
        
        $settings = [
            // General settings
            'site_name' => 'MechaMap',
            'site_description' => 'Cộng đồng cơ khí và tự động hóa hàng đầu Việt Nam',
            'site_keywords' => 'cơ khí, tự động hóa, CAD, CAM, PLC, SCADA, IoT, Industry 4.0',
            'site_logo' => '/images/logo.png',
            'site_favicon' => '/images/favicon.ico',
            'admin_email' => 'admin@mechamap.com',
            'contact_email' => 'contact@mechamap.com',
            'support_email' => 'support@mechamap.com',
            
            // Forum settings
            'forum_enabled' => true,
            'forum_posts_per_page' => 15,
            'forum_threads_per_page' => 20,
            'forum_allow_guest_viewing' => true,
            'forum_require_email_verification' => true,
            
            // Marketplace settings
            'marketplace_enabled' => true,
            'marketplace_products_per_page' => 24,
            'marketplace_commission_rate' => 5,
            'marketplace_currency' => 'VND',
            'marketplace_allow_guest_viewing' => true,
            'marketplace_require_seller_verification' => true,
            
            // Security settings
            'registration_enabled' => true,
            'email_verification_required' => true,
            'two_factor_enabled' => false,
            'captcha_enabled' => false,
            
            // SEO settings
            'seo_enabled' => true,
            'sitemap_enabled' => true,
            'robots_txt_enabled' => true,
            'meta_title' => 'MechaMap - Cộng đồng cơ khí Việt Nam',
            'meta_description' => 'Nền tảng diễn đàn kỹ thuật hàng đầu dành cho cộng đồng cơ khí, tự động hóa và công nghệ Việt Nam',
            
            // Social settings
            'facebook_url' => 'https://facebook.com/mechamap.vn',
            'twitter_url' => '',
            'linkedin_url' => '',
            'youtube_url' => '',
            
            // Contact settings
            'company_name' => 'MechaMap Vietnam',
            'company_address' => 'Việt Nam',
            'company_phone' => '+84 123 456 789',
            'company_email' => 'info@mechamap.com',
        ];
        
        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => is_bool($value) ? 'boolean' : (is_numeric($value) ? 'number' : 'text'),
                    'group' => $this->getSettingGroup($key),
                ]
            );
        }
        
        $this->command->info('✅ Essential settings seeded');
    }

    /**
     * Get setting group for a key
     */
    private function getSettingGroup(string $key): string
    {
        if (str_starts_with($key, 'site_') || str_starts_with($key, 'admin_')) {
            return 'general';
        } elseif (str_starts_with($key, 'forum_')) {
            return 'forum';
        } elseif (str_starts_with($key, 'marketplace_')) {
            return 'marketplace';
        } elseif (str_starts_with($key, 'seo_') || str_starts_with($key, 'meta_')) {
            return 'seo';
        } elseif (str_contains($key, 'email') || str_starts_with($key, 'contact_') || str_starts_with($key, 'company_')) {
            return 'contact';
        } elseif (str_contains($key, '_url')) {
            return 'social';
        } else {
            return 'security';
        }
    }
}
