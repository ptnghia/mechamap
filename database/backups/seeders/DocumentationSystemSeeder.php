<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\DocumentationCategory;
use App\Models\Documentation;

class DocumentationSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Documentation System Seeding...');
        $this->command->newLine();

        // Check if tables exist
        if (!$this->checkTables()) {
            $this->command->error('❌ Documentation tables not found! Please run migrations first.');
            $this->command->info('Run: php artisan migrate');
            return;
        }

        // Check if data already exists
        if ($this->hasExistingData()) {
            $this->command->warn('⚠️  Documentation data already exists!');
            
            if (!$this->confirm('Do you want to clear existing data and reseed?')) {
                $this->command->info('Seeding cancelled.');
                return;
            }
            
            $this->clearExistingData();
        }

        // Run seeders in order
        $this->seedCategories();
        $this->seedContent();
        
        // Show summary
        $this->showSummary();
        
        $this->command->newLine();
        $this->command->info('🎉 Documentation System seeding completed successfully!');
        $this->command->newLine();
        
        $this->showAccessInfo();
    }

    /**
     * Check if required tables exist
     */
    private function checkTables(): bool
    {
        $requiredTables = [
            'documentation_categories',
            'documentations',
            'documentation_versions',
            'documentation_views',
            'documentation_ratings',
            'documentation_comments',
            'documentation_downloads'
        ];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->command->error("❌ Table '{$table}' not found!");
                return false;
            }
        }

        $this->command->info('✅ All required tables found.');
        return true;
    }

    /**
     * Check if data already exists
     */
    private function hasExistingData(): bool
    {
        $categoryCount = DocumentationCategory::count();
        $documentCount = Documentation::count();
        
        return $categoryCount > 0 || $documentCount > 0;
    }

    /**
     * Clear existing data safely
     */
    private function clearExistingData(): void
    {
        $this->command->info('🧹 Clearing existing documentation data...');
        
        // Delete in correct order to avoid foreign key constraints
        \DB::table('documentation_downloads')->delete();
        \DB::table('documentation_comments')->delete();
        \DB::table('documentation_ratings')->delete();
        \DB::table('documentation_views')->delete();
        \DB::table('documentation_versions')->delete();
        \DB::table('documentations')->delete();
        \DB::table('documentation_categories')->delete();
        
        $this->command->info('✅ Existing data cleared.');
    }

    /**
     * Seed categories
     */
    private function seedCategories(): void
    {
        $this->command->info('📁 Seeding Documentation Categories...');
        $this->call(DocumentationCategorySeeder::class);
        $this->command->info('✅ Categories seeded successfully.');
    }

    /**
     * Seed content
     */
    private function seedContent(): void
    {
        $this->command->info('📝 Seeding Documentation Content...');
        $this->call(DocumentationContentSeeder::class);
        $this->command->info('✅ Content seeded successfully.');
    }

    /**
     * Show seeding summary
     */
    private function showSummary(): void
    {
        $this->command->newLine();
        $this->command->info('📊 Seeding Summary:');
        $this->command->table(
            ['Item', 'Count'],
            [
                ['Categories', DocumentationCategory::count()],
                ['Documents', Documentation::count()],
                ['Published Documents', Documentation::where('status', 'published')->count()],
                ['Public Documents', Documentation::where('is_public', true)->count()],
                ['Featured Documents', Documentation::where('is_featured', true)->count()],
            ]
        );
    }

    /**
     * Show access information
     */
    private function showAccessInfo(): void
    {
        $this->command->info('🌐 Access Information:');
        $this->command->newLine();
        
        $this->command->info('📋 Admin Panel:');
        $this->command->line('  • Documentation Management: http://localhost:8000/admin/documentation');
        $this->command->line('  • Category Management: http://localhost:8000/admin/documentation/categories');
        $this->command->line('  • Create New Document: http://localhost:8000/admin/documentation/create');
        
        $this->command->newLine();
        $this->command->info('🌍 Public Portal:');
        $this->command->line('  • Documentation Portal: http://localhost:8000/docs');
        $this->command->line('  • Search Documents: http://localhost:8000/docs/search');
        
        $this->command->newLine();
        $this->command->info('📚 Sample Documents Created:');
        
        $documents = Documentation::select('title', 'slug', 'is_public')->get();
        foreach ($documents as $doc) {
            $access = $doc->is_public ? '🌍 Public' : '🔒 Private';
            $this->command->line("  • {$doc->title} ({$access})");
            $this->command->line("    URL: http://localhost:8000/docs/{$doc->slug}");
        }
        
        $this->command->newLine();
        $this->command->info('💡 Next Steps:');
        $this->command->line('  1. Visit admin panel to manage documentation');
        $this->command->line('  2. Check public portal to see user experience');
        $this->command->line('  3. Create additional documents as needed');
        $this->command->line('  4. Configure permissions for different user roles');
    }

    /**
     * Ask for confirmation
     */
    private function confirm(string $question): bool
    {
        $answer = $this->command->ask($question . ' (yes/no)', 'no');
        return in_array(strtolower($answer), ['yes', 'y', '1', 'true']);
    }
}
