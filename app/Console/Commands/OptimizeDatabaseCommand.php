<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseOptimizationService;
use Illuminate\Support\Facades\Log;

/**
 * Database Optimization Command
 * Runs comprehensive database optimization tasks
 */
class OptimizeDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:optimize 
                            {--indexes : Create performance indexes}
                            {--fulltext : Create full-text search indexes}
                            {--analyze : Analyze slow queries}
                            {--cleanup : Clean up and optimize tables}
                            {--metrics : Show database performance metrics}
                            {--all : Run all optimization tasks}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize database performance with indexes, caching, and analysis';

    protected $optimizationService;

    /**
     * Create a new command instance.
     */
    public function __construct(DatabaseOptimizationService $optimizationService)
    {
        parent::__construct();
        $this->optimizationService = $optimizationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting Database Optimization...');
        $this->newLine();

        $startTime = microtime(true);

        try {
            if ($this->option('all')) {
                $this->runAllOptimizations();
            } else {
                $this->runSelectedOptimizations();
            }

            $executionTime = round((microtime(true) - $startTime), 2);
            $this->newLine();
            $this->info("✅ Database optimization completed in {$executionTime} seconds");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Database optimization failed: ' . $e->getMessage());
            Log::error('Database optimization command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Run all optimization tasks
     */
    private function runAllOptimizations(): void
    {
        $this->createPerformanceIndexes();
        $this->createFullTextIndexes();
        $this->analyzeSlowQueries();
        $this->cleanupTables();
        $this->showPerformanceMetrics();
    }

    /**
     * Run selected optimization tasks
     */
    private function runSelectedOptimizations(): void
    {
        if ($this->option('indexes')) {
            $this->createPerformanceIndexes();
        }

        if ($this->option('fulltext')) {
            $this->createFullTextIndexes();
        }

        if ($this->option('analyze')) {
            $this->analyzeSlowQueries();
        }

        if ($this->option('cleanup')) {
            $this->cleanupTables();
        }

        if ($this->option('metrics')) {
            $this->showPerformanceMetrics();
        }

        // If no specific options, show help
        if (!$this->option('indexes') && !$this->option('fulltext') && 
            !$this->option('analyze') && !$this->option('cleanup') && 
            !$this->option('metrics')) {
            $this->showHelp();
        }
    }

    /**
     * Create performance indexes
     */
    private function createPerformanceIndexes(): void
    {
        $this->info('📊 Creating performance indexes...');
        
        $progressBar = $this->output->createProgressBar(3);
        $progressBar->start();

        // Create optimized indexes
        $progressBar->advance();
        $this->optimizationService->createOptimizedIndexes();

        // Create composite indexes
        $progressBar->advance();
        $this->optimizationService->createCompositeIndexes();

        // Run migration for additional indexes
        $progressBar->advance();
        $this->call('migrate', ['--path' => 'database/migrations/2024_01_15_000000_add_performance_indexes.php']);

        $progressBar->finish();
        $this->newLine();
        $this->info('✅ Performance indexes created successfully');
    }

    /**
     * Create full-text search indexes
     */
    private function createFullTextIndexes(): void
    {
        $this->info('🔍 Creating full-text search indexes...');

        try {
            $indexes = $this->optimizationService->createFullTextIndexes();
            $successCount = count(array_filter($indexes));
            
            $this->info("✅ Created {$successCount} full-text search indexes");

        } catch (\Exception $e) {
            $this->warn('⚠️  Some full-text indexes could not be created: ' . $e->getMessage());
        }
    }

    /**
     * Analyze slow queries
     */
    private function analyzeSlowQueries(): void
    {
        $this->info('🐌 Analyzing slow queries...');

        try {
            $analysis = $this->optimizationService->analyzeSlowQueriesAdvanced(20);

            if (empty($analysis)) {
                $this->info('✅ No slow queries found');
                return;
            }

            $this->warn("⚠️  Found " . count($analysis) . " slow queries");
            $this->newLine();

            $headers = ['Query', 'Avg Time (s)', 'Exec Count', 'Efficiency', 'Suggestions'];
            $rows = [];

            foreach (array_slice($analysis, 0, 10) as $query) {
                $rows[] = [
                    substr($query['query'], 0, 50) . '...',
                    $query['avg_time_seconds'],
                    $query['execution_count'],
                    $query['efficiency_ratio'],
                    implode(', ', array_slice($query['suggestions'], 0, 2)),
                ];
            }

            $this->table($headers, $rows);

        } catch (\Exception $e) {
            $this->warn('⚠️  Could not analyze slow queries: ' . $e->getMessage());
        }
    }

    /**
     * Clean up and optimize tables
     */
    private function cleanupTables(): void
    {
        $this->info('🧹 Cleaning up and optimizing tables...');

        $progressBar = $this->output->createProgressBar(2);
        $progressBar->start();

        // Clean up tables
        $progressBar->advance();
        $this->optimizationService->cleanupTables();

        // Optimize queries
        $progressBar->advance();
        $this->optimizationService->optimizeQueries();

        $progressBar->finish();
        $this->newLine();
        $this->info('✅ Tables cleaned up and optimized');
    }

    /**
     * Show performance metrics
     */
    private function showPerformanceMetrics(): void
    {
        $this->info('📈 Database Performance Metrics');
        $this->newLine();

        try {
            $metrics = $this->optimizationService->getPerformanceMetrics();

            if (isset($metrics['error'])) {
                $this->error($metrics['error']);
                return;
            }

            $this->displayMetricsTable($metrics);

        } catch (\Exception $e) {
            $this->error('❌ Could not retrieve performance metrics: ' . $e->getMessage());
        }
    }

    /**
     * Display metrics in a formatted table
     */
    private function displayMetricsTable(array $metrics): void
    {
        $rows = [
            ['Slow Queries', $metrics['slow_queries'] ?? 'N/A'],
            ['Total Connections', number_format($metrics['connections'] ?? 0)],
            ['Total Queries', number_format($metrics['queries'] ?? 0)],
            ['Uptime (hours)', round(($metrics['uptime'] ?? 0) / 3600, 2)],
            ['Buffer Pool Hit Ratio', ($metrics['buffer_pool_hit_ratio'] ?? 0) . '%'],
        ];

        $this->table(['Metric', 'Value'], $rows);

        // Performance assessment
        $this->assessPerformance($metrics);
    }

    /**
     * Assess database performance and provide recommendations
     */
    private function assessPerformance(array $metrics): void
    {
        $this->newLine();
        $this->info('🎯 Performance Assessment:');

        $issues = [];
        $recommendations = [];

        // Check slow queries
        $slowQueries = $metrics['slow_queries'] ?? 0;
        if ($slowQueries > 100) {
            $issues[] = "High number of slow queries ({$slowQueries})";
            $recommendations[] = 'Run query analysis and add missing indexes';
        }

        // Check buffer pool hit ratio
        $hitRatio = $metrics['buffer_pool_hit_ratio'] ?? 100;
        if ($hitRatio < 95) {
            $issues[] = "Low buffer pool hit ratio ({$hitRatio}%)";
            $recommendations[] = 'Consider increasing innodb_buffer_pool_size';
        }

        // Display results
        if (empty($issues)) {
            $this->info('✅ Database performance looks good!');
        } else {
            $this->warn('⚠️  Performance Issues Found:');
            foreach ($issues as $issue) {
                $this->line("  • {$issue}");
            }

            $this->newLine();
            $this->info('💡 Recommendations:');
            foreach ($recommendations as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    /**
     * Show command help
     */
    private function showHelp(): void
    {
        $this->info('🔧 Database Optimization Tool');
        $this->newLine();
        $this->line('Available options:');
        $this->line('  --indexes    Create performance indexes');
        $this->line('  --fulltext   Create full-text search indexes');
        $this->line('  --analyze    Analyze slow queries');
        $this->line('  --cleanup    Clean up and optimize tables');
        $this->line('  --metrics    Show database performance metrics');
        $this->line('  --all        Run all optimization tasks');
        $this->newLine();
        $this->line('Examples:');
        $this->line('  php artisan db:optimize --all');
        $this->line('  php artisan db:optimize --indexes --analyze');
        $this->line('  php artisan db:optimize --metrics');
    }
}
