<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Services\UnifiedMarketplacePermissionService;

class UpdateMarketplacePermissions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'marketplace:update-permissions {--dry-run : Show what would be changed without making changes} {--force : Force update without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Update user permissions according to new marketplace permission matrix';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Updating Marketplace Permissions...');
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Check current permission matrix
        $this->displayCurrentMatrix();

        // Find users that need updates
        $usersToUpdate = $this->findUsersNeedingUpdates();

        if (empty($usersToUpdate)) {
            $this->info('✅ All users already have correct permissions!');
            return Command::SUCCESS;
        }

        // Display proposed changes
        $this->displayProposedChanges($usersToUpdate);

        // Confirm changes unless forced
        if (!$isDryRun && !$isForced) {
            if (!$this->confirm('Do you want to proceed with these changes?')) {
                $this->info('❌ Operation cancelled.');
                return Command::FAILURE;
            }
        }

        // Apply changes
        if (!$isDryRun) {
            $this->applyChanges($usersToUpdate);
        }

        $this->newLine();
        $this->info('✅ Permission update completed!');

        return Command::SUCCESS;
    }

    /**
     * Display current permission matrix
     */
    private function displayCurrentMatrix(): void
    {
        $this->info('📊 Current Permission Matrix:');

        $roles = ['guest', 'member', 'senior_member', 'supplier', 'manufacturer', 'brand'];
        $table = [];

        foreach ($roles as $role) {
            // Create mock user for testing
            $mockUser = new User(['role' => $role]);
            $buyTypes = UnifiedMarketplacePermissionService::getAllowedBuyTypes($mockUser);
            $sellTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($mockUser);

            $table[] = [
                $role,
                empty($buyTypes) ? 'None' : implode(', ', $buyTypes),
                empty($sellTypes) ? 'None' : implode(', ', $sellTypes)
            ];
        }

        $this->table(['Role', 'Buy Permissions', 'Sell Permissions'], $table);
        $this->newLine();
    }

    /**
     * Find users that need permission updates
     */
    private function findUsersNeedingUpdates(): array
    {
        $usersToUpdate = [];

        // Check members and senior_members with seller accounts
        $problematicRoles = ['member', 'senior_member'];

        foreach ($problematicRoles as $role) {
            $users = User::where('role', $role)->get();

            foreach ($users as $user) {
                $seller = MarketplaceSeller::where('user_id', $user->id)->first();

                if ($seller) {
                    // Check if they have products
                    $productCount = MarketplaceProduct::where('seller_id', $seller->id)->count();

                    $usersToUpdate[] = [
                        'user' => $user,
                        'seller' => $seller,
                        'issue' => 'has_seller_account',
                        'current_role' => $role,
                        'suggested_action' => $productCount > 0 ? 'upgrade_to_supplier' : 'remove_seller_account',
                        'product_count' => $productCount,
                        'reason' => $role . ' should not have marketplace access according to new matrix'
                    ];
                }
            }
        }

        return $usersToUpdate;
    }

    /**
     * Display proposed changes
     */
    private function displayProposedChanges(array $usersToUpdate): void
    {
        $this->warn('⚠️  Users requiring permission updates:');
        $this->newLine();

        $table = [];
        foreach ($usersToUpdate as $update) {
            $user = $update['user'];
            $action = $update['suggested_action'] === 'upgrade_to_supplier' ?
                'Upgrade to Supplier' : 'Remove Seller Account';

            $table[] = [
                $user->email,
                $update['current_role'],
                $update['product_count'],
                $action,
                $update['reason']
            ];
        }

        $this->table(['Email', 'Current Role', 'Products', 'Suggested Action', 'Reason'], $table);
        $this->newLine();
    }

    /**
     * Apply the changes
     */
    private function applyChanges(array $usersToUpdate): void
    {
        $this->info('🔧 Applying changes...');

        $upgraded = 0;
        $removed = 0;

        foreach ($usersToUpdate as $update) {
            $user = $update['user'];
            $seller = $update['seller'];

            if ($update['suggested_action'] === 'upgrade_to_supplier') {
                // Upgrade user to supplier
                $user->update(['role' => 'supplier']);
                $seller->update(['seller_type' => 'supplier']);

                $this->line("✅ Upgraded {$user->email} to supplier role");
                $upgraded++;

            } else {
                // Remove seller account (soft delete)
                $seller->delete();

                $this->line("✅ Removed seller account for {$user->email}");
                $removed++;
            }
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("- Users upgraded to supplier: {$upgraded}");
        $this->info("- Seller accounts removed: {$removed}");
    }
}
