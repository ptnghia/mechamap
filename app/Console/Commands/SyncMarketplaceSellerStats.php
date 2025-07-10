<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;

class SyncMarketplaceSellerStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketplace:sync-seller-stats {--seller-id= : Sync specific seller by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync cached product statistics for marketplace sellers with real data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting marketplace seller stats synchronization...');

        $query = MarketplaceSeller::query();

        // If specific seller ID provided
        if ($sellerId = $this->option('seller-id')) {
            $query->where('id', $sellerId);
        }

        $sellers = $query->get();
        $progressBar = $this->output->createProgressBar($sellers->count());
        $progressBar->start();

        $updated = 0;
        $errors = 0;

        foreach ($sellers as $seller) {
            try {
                // Get real product counts
                $totalProducts = $seller->products()->count();
                $activeProducts = $seller->products()
                                         ->where('status', 'approved')
                                         ->where('is_active', true)
                                         ->count();

                // Update cached fields
                $seller->update([
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                ]);

                $updated++;

                if ($this->option('verbose')) {
                    $this->line("\nUpdated seller ID {$seller->id}: {$totalProducts} total, {$activeProducts} active products");
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("\nError updating seller ID {$seller->id}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->newLine(2);
        $this->info("Synchronization completed!");
        $this->info("Updated: {$updated} sellers");

        if ($errors > 0) {
            $this->error("Errors: {$errors} sellers");
        }

        return Command::SUCCESS;
    }
}
