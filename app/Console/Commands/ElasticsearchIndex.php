<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SearchService;
use App\Models\Thread;
use App\Models\Showcase;
use App\Models\User;
use App\Models\MarketplaceProduct;
use Illuminate\Support\Facades\Log;

class ElasticsearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index {type?} {--batch=100 : Batch size for indexing} {--force : Force reindex all data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index data into Elasticsearch (threads, showcases, users, products)';

    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        parent::__construct();
        $this->searchService = $searchService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 Starting Elasticsearch indexing...');

        // Check if Elasticsearch is enabled
        if (!config('elasticsearch.enabled', false)) {
            $this->error('❌ Elasticsearch is disabled in configuration.');
            return 1;
        }

        $type = $this->argument('type');
        $batchSize = (int) $this->option('batch');
        $force = $this->option('force');

        if ($type) {
            $this->indexType($type, $batchSize, $force);
        } else {
            $this->indexAll($batchSize, $force);
        }

        $this->info('✅ Elasticsearch indexing completed!');
        return 0;
    }

    private function indexAll(int $batchSize, bool $force): void
    {
        $types = ['threads', 'showcases', 'users', 'products'];

        foreach ($types as $type) {
            $this->indexType($type, $batchSize, $force);
        }
    }

    private function indexType(string $type, int $batchSize, bool $force): void
    {
        $this->info("🔄 Indexing {$type}...");

        try {
            switch ($type) {
                case 'threads':
                    $this->indexThreads($batchSize, $force);
                    break;
                case 'showcases':
                    $this->indexShowcases($batchSize, $force);
                    break;
                case 'users':
                    $this->indexUsers($batchSize, $force);
                    break;
                case 'products':
                    $this->indexProducts($batchSize, $force);
                    break;
                default:
                    $this->error("❌ Unknown type: {$type}");
                    return;
            }

            $this->info("✅ Completed indexing {$type}");

        } catch (\Exception $e) {
            $this->error("❌ Failed to index {$type}: " . $e->getMessage());
            Log::error("Elasticsearch indexing failed for {$type}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function indexThreads(int $batchSize, bool $force): void
    {
        $query = Thread::with(['user', 'category', 'forum']);

        if (!$force) {
            $query->where('updated_at', '>', now()->subHours(24));
        }

        $total = $query->count();
        $this->info("📝 Found {$total} threads to index");

        if ($total === 0) {
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk($batchSize, function ($threads) use ($bar) {
            foreach ($threads as $thread) {
                try {
                    $this->searchService->indexThread($thread);
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("\n❌ Failed to index thread {$thread->id}: " . $e->getMessage());
                }
            }
        });

        $bar->finish();
        $this->newLine();
    }

    private function indexShowcases(int $batchSize, bool $force): void
    {
        $query = Showcase::with(['user']);

        if (!$force) {
            $query->where('updated_at', '>', now()->subHours(24));
        }

        $total = $query->count();
        $this->info("🎨 Found {$total} showcases to index");

        if ($total === 0) {
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk($batchSize, function ($showcases) use ($bar) {
            foreach ($showcases as $showcase) {
                try {
                    $this->searchService->indexShowcase($showcase);
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("\n❌ Failed to index showcase {$showcase->id}: " . $e->getMessage());
                }
            }
        });

        $bar->finish();
        $this->newLine();
    }

    private function indexUsers(int $batchSize, bool $force): void
    {
        $query = User::query();

        if (!$force) {
            $query->where('updated_at', '>', now()->subHours(24));
        }

        $total = $query->count();
        $this->info("👥 Found {$total} users to index");

        if ($total === 0) {
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk($batchSize, function ($users) use ($bar) {
            foreach ($users as $user) {
                try {
                    $this->searchService->indexUser($user);
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("\n❌ Failed to index user {$user->id}: " . $e->getMessage());
                }
            }
        });

        $bar->finish();
        $this->newLine();
    }

    private function indexProducts(int $batchSize, bool $force): void
    {
        if (!class_exists(MarketplaceProduct::class)) {
            $this->warn("⚠️  MarketplaceProduct model not found, skipping products indexing");
            return;
        }

        $query = MarketplaceProduct::with(['seller']);

        if (!$force) {
            $query->where('updated_at', '>', now()->subHours(24));
        }

        $total = $query->count();
        $this->info("🛒 Found {$total} products to index");

        if ($total === 0) {
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk($batchSize, function ($products) use ($bar) {
            foreach ($products as $product) {
                try {
                    $this->searchService->indexProduct($product);
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("\n❌ Failed to index product {$product->id}: " . $e->getMessage());
                }
            }
        });

        $bar->finish();
        $this->newLine();
    }
}
