<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Chạy tất cả seeders để khởi tạo dữ liệu mẫu cho MechaMap
     * - Nền tảng cộng đồng kỹ thuật cơ khí Việt Nam
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting MechaMap Database Seeding Process...');
        $this->command->info('🔧 Initializing Mechanical Engineering Community Platform');
        $this->command->newLine();

        // ====================================================================
        // CORE SETTINGS - Khởi tạo cấu hình cơ bản
        // ====================================================================
        $this->command->info('⚙️ Seeding Core Settings...');
        $this->call([
            SettingSeeder::class,        // General settings, company info, etc.
            SeoSettingSeeder::class,     // SEO configuration
            PageSeoSeeder::class,        // Page-specific SEO settings
        ]);
        $this->command->newLine();

        // ====================================================================
        // COMMUNITY DATA - Dữ liệu cộng đồng
        // ====================================================================
        $this->command->info('👥 Seeding Community Data...');
        $this->call([
            RolesAndPermissionsSeeder::class, // Roles & permissions system
            MechaMapCategorySeeder::class,    // Mechanical engineering categories
            MechaMapUserSeeder::class,        // Community users (Admin, Moderator, Senior, Member, Guest)
            BusinessUserSeeder::class,        // Business users (Supplier, Manufacturer, Brand)
            MechanicalEngineeringDataSeeder::class, // Realistic forum data
            MediaSeeder::class,               // Media files (images, avatars, etc.)
            // ShowcaseSeeder::class,         // Uncomment when showcase seeder is ready
        ]);
        $this->command->newLine();

        // ====================================================================
        // COMPLETION MESSAGE
        // ====================================================================
        $this->command->info('✅ MechaMap Database Seeding Completed Successfully!');
        $this->command->info('🎯 Platform ready for Vietnamese Mechanical Engineering Community');
        $this->command->info('👥 8 User Roles: Admin, Moderator, Senior, Member, Guest, Supplier, Manufacturer, Brand');
        $this->command->info('💼 Business Marketplace: Verified suppliers, manufacturers, and brands');
        $this->command->info('📊 SEO optimized for mechanical engineering keywords');
        $this->command->info('🔧 Forum configured for CAD/CAM, Automation, Robotics discussions');
        $this->command->info('⚡ Performance settings applied for technical content');
        $this->command->newLine();

        $this->command->warn('💡 Next Steps:');
        $this->command->line('   1. Configure SMTP settings in admin panel');
        $this->command->line('   2. Update Google Analytics ID in SEO settings');
        $this->command->line('   3. Upload brand images (logo, favicon, banners)');
        $this->command->line('   4. Configure social media API keys');
        $this->command->line('   5. Test email notifications');
        $this->command->newLine();

        $this->command->info('🌐 Access admin panel: /admin');
        $this->command->info('🏠 Visit homepage: /');
    }
}
