<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateRealisticTimestamps extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mechamap:update-timestamps
                            {--backup : Create backup before updating}
                            {--dry-run : Show what would be updated without making changes}
                            {--start-date= : Start date (default: start of current month)}
                            {--end-date= : End date (default: now)}';

    /**
     * The console command description.
     */
    protected $description = 'Update timestamps to create realistic test data with proper chronological order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 MechaMap Realistic Timestamps Update Tool');
        $this->info('================================================');

        // Parse options
        $dryRun = $this->option('dry-run');
        $backup = $this->option('backup');

        // Set date range
        $startDate = $this->option('start-date')
            ? Carbon::parse($this->option('start-date'))
            : Carbon::now()->startOfMonth();

        $endDate = $this->option('end-date')
            ? Carbon::parse($this->option('end-date'))
            : Carbon::now();

        $this->info("📅 Date Range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        // Show current statistics
        $this->showCurrentStats();

        // Confirm before proceeding
        if (!$dryRun && !$this->confirm('Do you want to proceed with updating timestamps?')) {
            $this->info('❌ Operation cancelled');
            return 0;
        }

        // Create backup if requested
        if ($backup && !$dryRun) {
            $this->createBackup();
        }

        // Start transaction
        if (!$dryRun) {
            DB::beginTransaction();
        }

        try {
            // Step 1: Update Users timestamps
            $this->updateUsersTimestamps($startDate, $endDate, $dryRun);

            // Step 2: Update Threads timestamps (after users)
            $this->updateThreadsTimestamps($dryRun);

            // Step 3: Update Comments timestamps (after threads)
            $this->updateCommentsTimestamps($dryRun);

            // Step 4: Update Showcases timestamps (after users)
            $this->updateShowcasesTimestamps($dryRun);

            // Step 5: Update Marketplace Products timestamps (after users)
            $this->updateMarketplaceProductsTimestamps($dryRun);

            if (!$dryRun) {
                DB::commit();
                $this->info('✅ All timestamps updated successfully!');
            } else {
                $this->info('✅ Dry run completed - no changes made');
            }

            // Show final statistics
            $this->showFinalStats($dryRun);

            // Validate constraints
            $this->validateConstraints();

        } catch (\Exception $e) {
            if (!$dryRun) {
                DB::rollBack();
            }
            $this->error('❌ Error occurred: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Show current database statistics
     */
    private function showCurrentStats()
    {
        $this->info('📊 Current Database Statistics:');
        $this->table(['Table', 'Count', 'Min Created', 'Max Created'], [
            ['Users', DB::table('users')->count(), DB::table('users')->min('created_at'), DB::table('users')->max('created_at')],
            ['Threads', DB::table('threads')->count(), DB::table('threads')->min('created_at'), DB::table('threads')->max('created_at')],
            ['Comments', DB::table('comments')->count(), DB::table('comments')->min('created_at'), DB::table('comments')->max('created_at')],
            ['Showcases', DB::table('showcases')->count(), DB::table('showcases')->min('created_at'), DB::table('showcases')->max('created_at')],
            ['Products', DB::table('marketplace_products')->count(), DB::table('marketplace_products')->min('created_at'), DB::table('marketplace_products')->max('created_at')],
        ]);
    }

    /**
     * Create database backup
     */
    private function createBackup()
    {
        $this->info('💾 Creating backup...');
        $backupFile = storage_path('backups/timestamps_backup_' . date('Y_m_d_H_i_s') . '.sql');

        // Create backups directory if not exists
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $command = "mysqldump -h{$host} -u{$username} -p{$password} {$database} users threads comments showcases marketplace_products > {$backupFile}";

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("✅ Backup created: {$backupFile}");
        } else {
            $this->error("❌ Backup failed");
            throw new \Exception('Backup creation failed');
        }
    }

    /**
     * Update users timestamps
     */
    private function updateUsersTimestamps($startDate, $endDate, $dryRun)
    {
        $this->info('👥 Updating Users timestamps...');

        $users = DB::table('users')->select('id', 'name', 'created_at')->get();
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            // Generate random timestamp between start and end date
            $randomTimestamp = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            if ($dryRun) {
                $this->line("Would update User {$user->id} ({$user->name}): {$user->created_at} → {$randomTimestamp}");
            } else {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'created_at' => $randomTimestamp,
                        'updated_at' => $randomTimestamp->copy()->addMinutes(rand(1, 60))
                    ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Users timestamps updated: {$users->count()} records");
    }

    /**
     * Update threads timestamps (must be after user creation)
     */
    private function updateThreadsTimestamps($dryRun)
    {
        $this->info('💬 Updating Threads timestamps...');

        $threads = DB::table('threads')
            ->join('users', 'threads.user_id', '=', 'users.id')
            ->select('threads.id', 'threads.title', 'threads.user_id', 'threads.created_at', 'users.created_at as user_created_at')
            ->get();

        $bar = $this->output->createProgressBar($threads->count());

        foreach ($threads as $thread) {
            $userCreatedAt = Carbon::parse($thread->user_created_at);

            // Thread created 1-30 days after user registration
            $minThreadDate = $userCreatedAt->copy()->addDays(rand(1, 30));
            $maxThreadDate = Carbon::now();

            if ($minThreadDate->gt($maxThreadDate)) {
                $minThreadDate = $userCreatedAt->copy()->addHours(rand(1, 24));
            }

            $randomTimestamp = Carbon::createFromTimestamp(
                rand($minThreadDate->timestamp, $maxThreadDate->timestamp)
            );

            if ($dryRun) {
                $this->line("Would update Thread {$thread->id}: {$thread->created_at} → {$randomTimestamp} (User created: {$userCreatedAt})");
            } else {
                DB::table('threads')
                    ->where('id', $thread->id)
                    ->update([
                        'created_at' => $randomTimestamp,
                        'updated_at' => $randomTimestamp->copy()->addMinutes(rand(1, 120))
                    ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Threads timestamps updated: {$threads->count()} records");
    }

    /**
     * Update comments timestamps (must be after thread creation)
     */
    private function updateCommentsTimestamps($dryRun)
    {
        $this->info('💭 Updating Comments timestamps...');

        $comments = DB::table('comments')
            ->join('threads', 'comments.thread_id', '=', 'threads.id')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->select('comments.id', 'comments.thread_id', 'comments.user_id', 'comments.created_at',
                    'threads.created_at as thread_created_at', 'users.created_at as user_created_at')
            ->get();

        $bar = $this->output->createProgressBar($comments->count());

        foreach ($comments as $comment) {
            $threadCreatedAt = Carbon::parse($comment->thread_created_at);
            $userCreatedAt = Carbon::parse($comment->user_created_at);

            // Comment created after both user and thread exist
            $minCommentDate = $threadCreatedAt->gt($userCreatedAt) ? $threadCreatedAt : $userCreatedAt;
            $minCommentDate = $minCommentDate->copy()->addMinutes(rand(30, 1440)); // 30 minutes to 24 hours after

            $maxCommentDate = Carbon::now();

            if ($minCommentDate->gt($maxCommentDate)) {
                $minCommentDate = $threadCreatedAt->copy()->addMinutes(rand(30, 120));
            }

            $randomTimestamp = Carbon::createFromTimestamp(
                rand($minCommentDate->timestamp, $maxCommentDate->timestamp)
            );

            if ($dryRun) {
                $this->line("Would update Comment {$comment->id}: {$comment->created_at} → {$randomTimestamp}");
            } else {
                DB::table('comments')
                    ->where('id', $comment->id)
                    ->update([
                        'created_at' => $randomTimestamp,
                        'updated_at' => $randomTimestamp->copy()->addMinutes(rand(1, 60))
                    ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Comments timestamps updated: {$comments->count()} records");
    }

    /**
     * Update showcases timestamps (must be after user creation)
     */
    private function updateShowcasesTimestamps($dryRun)
    {
        $this->info('🏆 Updating Showcases timestamps...');

        $showcases = DB::table('showcases')
            ->join('users', 'showcases.user_id', '=', 'users.id')
            ->select('showcases.id', 'showcases.title', 'showcases.user_id', 'showcases.created_at',
                    'users.created_at as user_created_at')
            ->get();

        $bar = $this->output->createProgressBar($showcases->count());

        foreach ($showcases as $showcase) {
            $userCreatedAt = Carbon::parse($showcase->user_created_at);

            // Showcase created 7-60 days after user registration
            $minShowcaseDate = $userCreatedAt->copy()->addDays(rand(7, 60));
            $maxShowcaseDate = Carbon::now();

            if ($minShowcaseDate->gt($maxShowcaseDate)) {
                $minShowcaseDate = $userCreatedAt->copy()->addDays(rand(1, 7));
            }

            $randomTimestamp = Carbon::createFromTimestamp(
                rand($minShowcaseDate->timestamp, $maxShowcaseDate->timestamp)
            );

            if ($dryRun) {
                $this->line("Would update Showcase {$showcase->id}: {$showcase->created_at} → {$randomTimestamp}");
            } else {
                DB::table('showcases')
                    ->where('id', $showcase->id)
                    ->update([
                        'created_at' => $randomTimestamp,
                        'updated_at' => $randomTimestamp->copy()->addMinutes(rand(1, 180))
                    ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Showcases timestamps updated: {$showcases->count()} records");
    }

    /**
     * Update marketplace products timestamps (must be after seller creation)
     */
    private function updateMarketplaceProductsTimestamps($dryRun)
    {
        $this->info('🛒 Updating Marketplace Products timestamps...');

        $products = DB::table('marketplace_products')
            ->join('users', 'marketplace_products.seller_id', '=', 'users.id')
            ->select('marketplace_products.id', 'marketplace_products.name', 'marketplace_products.seller_id',
                    'marketplace_products.created_at', 'users.created_at as seller_created_at')
            ->get();

        $bar = $this->output->createProgressBar($products->count());

        foreach ($products as $product) {
            $sellerCreatedAt = Carbon::parse($product->seller_created_at);

            // Product created 14-90 days after seller registration
            $minProductDate = $sellerCreatedAt->copy()->addDays(rand(14, 90));
            $maxProductDate = Carbon::now();

            if ($minProductDate->gt($maxProductDate)) {
                $minProductDate = $sellerCreatedAt->copy()->addDays(rand(1, 14));
            }

            $randomTimestamp = Carbon::createFromTimestamp(
                rand($minProductDate->timestamp, $maxProductDate->timestamp)
            );

            if ($dryRun) {
                $this->line("Would update Product {$product->id}: {$product->created_at} → {$randomTimestamp}");
            } else {
                DB::table('marketplace_products')
                    ->where('id', $product->id)
                    ->update([
                        'created_at' => $randomTimestamp,
                        'updated_at' => $randomTimestamp->copy()->addMinutes(rand(1, 240))
                    ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Marketplace Products timestamps updated: {$products->count()} records");
    }

    /**
     * Show final statistics after update
     */
    private function showFinalStats($dryRun)
    {
        if ($dryRun) {
            $this->info('📊 Dry Run Results - No actual changes made');
            return;
        }

        $this->info('📊 Updated Database Statistics:');
        $this->table(['Table', 'Count', 'Min Created', 'Max Created'], [
            ['Users', DB::table('users')->count(), DB::table('users')->min('created_at'), DB::table('users')->max('created_at')],
            ['Threads', DB::table('threads')->count(), DB::table('threads')->min('created_at'), DB::table('threads')->max('created_at')],
            ['Comments', DB::table('comments')->count(), DB::table('comments')->min('created_at'), DB::table('comments')->max('created_at')],
            ['Showcases', DB::table('showcases')->count(), DB::table('showcases')->min('created_at'), DB::table('showcases')->max('created_at')],
            ['Products', DB::table('marketplace_products')->count(), DB::table('marketplace_products')->min('created_at'), DB::table('marketplace_products')->max('created_at')],
        ]);
    }

    /**
     * Validate chronological constraints
     */
    private function validateConstraints()
    {
        $this->info('🔍 Validating chronological constraints...');

        $violations = [];

        // Check: users.created_at ≤ threads.created_at
        $threadViolations = DB::table('threads')
            ->join('users', 'threads.user_id', '=', 'users.id')
            ->whereRaw('users.created_at > threads.created_at')
            ->count();

        if ($threadViolations > 0) {
            $violations[] = "❌ Thread constraint violations: {$threadViolations}";
        } else {
            $this->info('✅ Thread constraints: OK');
        }

        // Check: threads.created_at ≤ comments.created_at
        $commentViolations = DB::table('comments')
            ->join('threads', 'comments.thread_id', '=', 'threads.id')
            ->whereRaw('threads.created_at > comments.created_at')
            ->count();

        if ($commentViolations > 0) {
            $violations[] = "❌ Comment constraint violations: {$commentViolations}";
        } else {
            $this->info('✅ Comment constraints: OK');
        }

        // Check: users.created_at ≤ showcases.created_at
        $showcaseViolations = DB::table('showcases')
            ->join('users', 'showcases.user_id', '=', 'users.id')
            ->whereRaw('users.created_at > showcases.created_at')
            ->count();

        if ($showcaseViolations > 0) {
            $violations[] = "❌ Showcase constraint violations: {$showcaseViolations}";
        } else {
            $this->info('✅ Showcase constraints: OK');
        }

        // Check: users.created_at ≤ marketplace_products.created_at
        $productViolations = DB::table('marketplace_products')
            ->join('users', 'marketplace_products.seller_id', '=', 'users.id')
            ->whereRaw('users.created_at > marketplace_products.created_at')
            ->count();

        if ($productViolations > 0) {
            $violations[] = "❌ Product constraint violations: {$productViolations}";
        } else {
            $this->info('✅ Product constraints: OK');
        }

        if (empty($violations)) {
            $this->info('🎉 All chronological constraints validated successfully!');
        } else {
            $this->error('⚠️  Constraint violations found:');
            foreach ($violations as $violation) {
                $this->error($violation);
            }
        }
    }
}
