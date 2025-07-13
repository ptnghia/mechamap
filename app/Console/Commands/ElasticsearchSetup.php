<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class ElasticsearchSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:setup {--force : Force recreate indices}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Elasticsearch indices for MechaMap';

    protected $client;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Setting up Elasticsearch for MechaMap...');

        // Check if Elasticsearch is enabled
        if (!config('elasticsearch.enabled', false)) {
            $this->error('❌ Elasticsearch is disabled in configuration. Enable it in .env file.');
            return 1;
        }

        // Initialize Elasticsearch client
        if (!$this->initializeClient()) {
            return 1;
        }

        // Test connection
        if (!$this->testConnection()) {
            return 1;
        }

        // Setup indices
        $this->setupIndices();

        $this->info('✅ Elasticsearch setup completed successfully!');
        return 0;
    }

    private function initializeClient(): bool
    {
        try {
            $hosts = config('elasticsearch.hosts', ['localhost:9200']);
            $retries = config('elasticsearch.retries', 2);

            $this->client = ClientBuilder::create()
                ->setHosts($hosts)
                ->setRetries($retries)
                ->build();

            return true;

        } catch (\Exception $e) {
            $this->error('❌ Failed to initialize Elasticsearch client: ' . $e->getMessage());
            return false;
        }
    }

    private function testConnection(): bool
    {
        try {
            $this->info('🔗 Testing Elasticsearch connection...');

            $response = $this->client->ping();

            if ($response) {
                $this->info('✅ Elasticsearch connection successful');

                // Get cluster info
                $info = $this->client->info();
                $this->info("📊 Elasticsearch version: {$info['version']['number']}");
                $this->info("🏷️  Cluster name: {$info['cluster_name']}");

                return true;
            } else {
                $this->error('❌ Elasticsearch ping failed');
                return false;
            }

        } catch (\Exception $e) {
            $this->error('❌ Elasticsearch connection failed: ' . $e->getMessage());
            $this->warn('💡 Make sure Elasticsearch is running on ' . implode(', ', config('elasticsearch.hosts')));
            return false;
        }
    }

    private function setupIndices(): void
    {
        $indices = config('elasticsearch.indices', []);
        $force = $this->option('force');

        foreach ($indices as $type => $config) {
            $indexName = $config['name'];

            $this->info("🔧 Setting up index: {$indexName}");

            try {
                // Check if index exists
                $exists = $this->client->indices()->exists(['index' => $indexName]);

                if ($exists && $force) {
                    $this->warn("🗑️  Deleting existing index: {$indexName}");
                    $this->client->indices()->delete(['index' => $indexName]);
                    $exists = false;
                }

                if (!$exists) {
                    // Create index with settings and mappings
                    $params = [
                        'index' => $indexName,
                        'body' => [
                            'settings' => $config['settings'],
                            'mappings' => $config['mappings']
                        ]
                    ];

                    $this->client->indices()->create($params);
                    $this->info("✅ Created index: {$indexName}");
                } else {
                    $this->warn("⚠️  Index already exists: {$indexName} (use --force to recreate)");
                }

            } catch (\Exception $e) {
                $this->error("❌ Failed to setup index {$indexName}: " . $e->getMessage());
                Log::error("Elasticsearch index setup failed for {$indexName}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }
}
