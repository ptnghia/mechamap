<?php

namespace App\Console\Commands;

use App\Models\NotificationAbTest;
use App\Services\NotificationAbTestService;
use Illuminate\Console\Command;

class NotificationAbTestManager extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ab-test:manage 
                            {--check : Check and auto-conclude tests}
                            {--list : List all tests}
                            {--show= : Show specific test details}
                            {--start= : Start a test by ID}
                            {--conclude= : Conclude a test by ID}
                            {--status= : Filter by status (active, concluded, etc.)}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Manage notification A/B tests';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 Notification A/B Test Manager');
        $this->newLine();

        if ($this->option('check')) {
            return $this->checkAndAutoConclude();
        }

        if ($this->option('list')) {
            return $this->listTests();
        }

        if ($this->option('show')) {
            return $this->showTest($this->option('show'));
        }

        if ($this->option('start')) {
            return $this->startTest($this->option('start'));
        }

        if ($this->option('conclude')) {
            return $this->concludeTest($this->option('conclude'));
        }

        // Default: show overview
        return $this->showOverview();
    }

    /**
     * Check and auto-conclude tests
     */
    private function checkAndAutoConclude(): int
    {
        try {
            $this->info('🔍 Checking tests for auto-conclusion...');
            
            $concluded = NotificationAbTestService::checkAutoConcludeTests();
            
            if ($concluded > 0) {
                $this->info("✅ Auto-concluded {$concluded} test(s)");
            } else {
                $this->info("ℹ️ No tests ready for auto-conclusion");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Auto-conclusion check failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * List all tests
     */
    private function listTests(): int
    {
        try {
            $query = NotificationAbTest::query();
            
            if ($this->option('status')) {
                $query->where('status', $this->option('status'));
            }

            $tests = $query->orderBy('created_at', 'desc')->get();

            if ($this->option('json')) {
                $this->line(json_encode($tests->toArray(), JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            if ($tests->isEmpty()) {
                $this->warn('No tests found');
                return Command::SUCCESS;
            }

            $this->info("📋 A/B Tests ({$tests->count()} found)");
            $this->newLine();

            $headers = ['ID', 'Name', 'Type', 'Notification Type', 'Status', 'Start Date', 'Participants'];
            $rows = [];

            foreach ($tests as $test) {
                $participantCount = $test->participants()->count();
                
                $rows[] = [
                    $test->id,
                    $this->truncate($test->name, 30),
                    ucfirst($test->test_type),
                    $test->notification_type,
                    $this->getStatusDisplay($test->status),
                    $test->start_date->format('Y-m-d'),
                    $participantCount,
                ];
            }

            $this->table($headers, $rows);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to list tests: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Show specific test details
     */
    private function showTest(int $testId): int
    {
        try {
            $test = NotificationAbTest::findOrFail($testId);
            
            if ($this->option('json')) {
                $summary = NotificationAbTestService::getTestSummary($test);
                $this->line(json_encode($summary, JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            $this->displayTestDetails($test);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to show test: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display test details
     */
    private function displayTestDetails(NotificationAbTest $test): void
    {
        $this->info("🧪 Test Details: {$test->name}");
        $this->newLine();

        // Basic info
        $this->line("📊 <fg=blue>ID:</> {$test->id}");
        $this->line("📝 <fg=blue>Description:</> " . ($test->description ?: 'No description'));
        $this->line("🔧 <fg=blue>Type:</> " . ucfirst($test->test_type));
        $this->line("📧 <fg=blue>Notification Type:</> {$test->notification_type}");
        $this->line("🚦 <fg=blue>Status:</> " . $this->getStatusDisplay($test->status));
        $this->line("📅 <fg=blue>Duration:</> {$test->start_date->format('Y-m-d')} to {$test->end_date->format('Y-m-d')}");
        
        // Variants
        $this->newLine();
        $this->line("🎯 <fg=blue>Variants:</>");
        foreach ($test->variants as $variant => $content) {
            $split = $test->traffic_split[$variant] ?? 0;
            $this->line("  • <fg=cyan>{$variant}</> ({$split}%): " . $this->truncate($content, 50));
        }

        // Participants
        $participantCount = $test->participants()->count();
        $this->newLine();
        $this->line("👥 <fg=blue>Participants:</> {$participantCount}");

        if ($participantCount > 0) {
            $participantsByVariant = $test->participants()
                ->selectRaw('variant, COUNT(*) as count')
                ->groupBy('variant')
                ->pluck('count', 'variant');

            foreach ($participantsByVariant as $variant => $count) {
                $percentage = $participantCount > 0 ? round(($count / $participantCount) * 100, 1) : 0;
                $this->line("  • <fg=cyan>{$variant}:</> {$count} ({$percentage}%)");
            }
        }

        // Metrics
        $this->newLine();
        $this->line("📈 <fg=blue>Target Metrics:</> " . implode(', ', $test->target_metrics));

        // Results (if concluded)
        if ($test->isConcluded() && $test->results) {
            $this->newLine();
            $this->line("🏆 <fg=blue>Results:</>");
            
            if ($test->winner_variant) {
                $this->line("  Winner: <fg=green>{$test->winner_variant}</>");
            } else {
                $this->line("  Winner: <fg=yellow>No clear winner</>");
            }
            
            if ($test->statistical_confidence) {
                $this->line("  Confidence: {$test->statistical_confidence}%");
            }
            
            if ($test->conclusion_reason) {
                $this->line("  Conclusion Reason: {$test->conclusion_reason}");
            }
        }
    }

    /**
     * Start a test
     */
    private function startTest(int $testId): int
    {
        try {
            $test = NotificationAbTest::findOrFail($testId);
            
            if ($test->status !== NotificationAbTest::STATUS_DRAFT) {
                $this->error("❌ Test can only be started from draft status (current: {$test->status})");
                return Command::FAILURE;
            }

            $success = NotificationAbTestService::startTest($test);
            
            if ($success) {
                $this->info("✅ Test '{$test->name}' started successfully");
                return Command::SUCCESS;
            } else {
                $this->error("❌ Failed to start test");
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('❌ Failed to start test: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Conclude a test
     */
    private function concludeTest(int $testId): int
    {
        try {
            $test = NotificationAbTest::findOrFail($testId);
            
            if ($test->isConcluded()) {
                $this->error("❌ Test is already concluded");
                return Command::FAILURE;
            }

            $success = NotificationAbTestService::concludeTest($test, NotificationAbTest::CONCLUSION_MANUAL);
            
            if ($success) {
                $this->info("✅ Test '{$test->name}' concluded successfully");
                
                if ($test->winner_variant) {
                    $this->info("🏆 Winner: {$test->winner_variant}");
                } else {
                    $this->warn("⚠️ No clear winner determined");
                }
                
                return Command::SUCCESS;
            } else {
                $this->error("❌ Failed to conclude test");
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('❌ Failed to conclude test: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Show overview
     */
    private function showOverview(): int
    {
        $this->info('📊 A/B Testing Overview');
        $this->newLine();

        // Check if A/B testing is enabled
        $enabled = NotificationAbTestService::isEnabled();
        $this->line("🔧 <fg=blue>A/B Testing:</> " . ($enabled ? '<fg=green>Enabled</>' : '<fg=yellow>Disabled</>'));

        if (!$enabled) {
            $this->warn('A/B testing is disabled. Enable it in config/notification-ab-testing.php');
        }

        // Test counts by status
        $statusCounts = NotificationAbTest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->newLine();
        $this->line("📈 <fg=blue>Test Statistics:</>");
        
        foreach (['draft', 'active', 'paused', 'concluded', 'cancelled'] as $status) {
            $count = $statusCounts[$status] ?? 0;
            $display = $this->getStatusDisplay($status);
            $this->line("  • {$display}: {$count}");
        }

        // Active tests
        $activeTests = NotificationAbTest::where('status', NotificationAbTest::STATUS_ACTIVE)->get();
        
        if ($activeTests->isNotEmpty()) {
            $this->newLine();
            $this->line("🔥 <fg=blue>Active Tests:</>");
            
            foreach ($activeTests as $test) {
                $participantCount = $test->participants()->count();
                $this->line("  • {$test->name} ({$participantCount} participants)");
            }
        }

        $this->newLine();
        $this->line("💡 <fg=blue>Available Commands:</>");
        $this->line("  • <fg=cyan>--check</> : Check and auto-conclude tests");
        $this->line("  • <fg=cyan>--list</> : List all tests");
        $this->line("  • <fg=cyan>--show=ID</> : Show test details");
        $this->line("  • <fg=cyan>--start=ID</> : Start a test");
        $this->line("  • <fg=cyan>--conclude=ID</> : Conclude a test");
        $this->line("  • <fg=cyan>--status=STATUS</> : Filter by status");
        $this->line("  • <fg=cyan>--json</> : Output in JSON format");

        return Command::SUCCESS;
    }

    /**
     * Get status display with color
     */
    private function getStatusDisplay(string $status): string
    {
        return match($status) {
            'draft' => '<fg=gray>Draft</>',
            'active' => '<fg=green>Active</>',
            'paused' => '<fg=yellow>Paused</>',
            'concluded' => '<fg=blue>Concluded</>',
            'cancelled' => '<fg=red>Cancelled</>',
            default => ucfirst($status),
        };
    }

    /**
     * Truncate text
     */
    private function truncate(string $text, int $length): string
    {
        return strlen($text) > $length ? substr($text, 0, $length - 3) . '...' : $text;
    }
}
