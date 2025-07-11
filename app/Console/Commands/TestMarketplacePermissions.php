<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Services\MarketplacePermissionService;

class TestMarketplacePermissions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'marketplace:test-permissions {--detailed : Show detailed test results}';

    /**
     * The console command description.
     */
    protected $description = 'Test marketplace permission matrix with real users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 Testing Marketplace Permission Matrix...');
        $this->newLine();

        // Test permission matrix
        $this->testPermissionMatrix();

        // Test with real users
        $this->testWithRealUsers();

        // Test product creation validation
        $this->testProductCreationValidation();

        $this->newLine();
        $this->info('✅ All marketplace permission tests completed!');

        return Command::SUCCESS;
    }

    /**
     * Test permission matrix
     */
    private function testPermissionMatrix(): void
    {
        $this->info('📊 Testing Permission Matrix...');

        $roles = ['guest', 'member', 'senior_member', 'supplier', 'manufacturer', 'brand'];
        $expectedMatrix = [
            'guest' => ['buy' => ['digital'], 'sell' => ['digital']],
            'member' => ['buy' => [], 'sell' => []],
            'senior_member' => ['buy' => [], 'sell' => []],
            'supplier' => ['buy' => ['digital'], 'sell' => ['digital', 'new_product']],
            'manufacturer' => ['buy' => ['digital', 'new_product'], 'sell' => ['digital', 'new_product']],
            'brand' => ['buy' => [], 'sell' => []],
        ];

        $table = [];
        foreach ($roles as $role) {
            $buyTypes = MarketplacePermissionService::getAllowedBuyTypes($role);
            $sellTypes = MarketplacePermissionService::getAllowedSellTypes($role);

            $expected = $expectedMatrix[$role];
            $buyMatch = $buyTypes === $expected['buy'] ? '✅' : '❌';
            $sellMatch = $sellTypes === $expected['sell'] ? '✅' : '❌';

            $table[] = [
                $role,
                implode(', ', $buyTypes) ?: 'None',
                implode(', ', $sellTypes) ?: 'None',
                $buyMatch . ' ' . $sellMatch
            ];
        }

        $this->table(['Role', 'Buy Permissions', 'Sell Permissions', 'Status'], $table);
    }

    /**
     * Test with real users
     */
    private function testWithRealUsers(): void
    {
        $this->info('👥 Testing with Real Users...');

        $testEmails = [
            'guest@mechamap.test',
            'member@mechamap.test',
            'supplier@mechamap.test',
            'manufacturer@mechamap.test',
            'brand@mechamap.test'
        ];

        $table = [];
        foreach ($testEmails as $email) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $table[] = [$email, 'Not Found', '❌', '❌', '❌'];
                continue;
            }

            $seller = MarketplaceSeller::where('user_id', $user->id)->first();
            $buyTypes = MarketplacePermissionService::getAllowedBuyTypes($user->role);
            $sellTypes = MarketplacePermissionService::getAllowedSellTypes($user->role);

            $hasMarketplaceAccess = !empty($buyTypes) || !empty($sellTypes);
            $hasSellerAccount = $seller ? '✅' : '❌';
            $marketplaceStatus = $hasMarketplaceAccess ? '✅' : '❌';

            $table[] = [
                $user->role,
                $user->email,
                $hasSellerAccount,
                $marketplaceStatus,
                implode(', ', array_merge($buyTypes, $sellTypes)) ?: 'None'
            ];
        }

        $this->table(['Role', 'Email', 'Has Seller', 'Marketplace Access', 'Permissions'], $table);
    }

    /**
     * Test product creation validation
     */
    private function testProductCreationValidation(): void
    {
        $this->info('🛡️ Testing Product Creation Validation...');

        $testCases = [
            ['role' => 'guest', 'product_type' => 'digital', 'should_pass' => true],
            ['role' => 'guest', 'product_type' => 'new_product', 'should_pass' => false],
            ['role' => 'member', 'product_type' => 'digital', 'should_pass' => false],
            ['role' => 'member', 'product_type' => 'new_product', 'should_pass' => false],
            ['role' => 'supplier', 'product_type' => 'digital', 'should_pass' => true],
            ['role' => 'supplier', 'product_type' => 'new_product', 'should_pass' => true],
            ['role' => 'supplier', 'product_type' => 'used_product', 'should_pass' => false],
            ['role' => 'manufacturer', 'product_type' => 'digital', 'should_pass' => true],
            ['role' => 'manufacturer', 'product_type' => 'new_product', 'should_pass' => true],
            ['role' => 'manufacturer', 'product_type' => 'used_product', 'should_pass' => false],
            ['role' => 'brand', 'product_type' => 'digital', 'should_pass' => false],
            ['role' => 'brand', 'product_type' => 'new_product', 'should_pass' => false],
        ];

        $table = [];
        foreach ($testCases as $testCase) {
            // Create a mock user for testing
            $user = new User(['role' => $testCase['role']]);
            $productData = ['product_type' => $testCase['product_type']];

            $errors = MarketplacePermissionService::validateProductCreation($user, $productData);
            $hasErrors = !empty($errors);

            $actualResult = !$hasErrors;
            $expectedResult = $testCase['should_pass'];
            $status = $actualResult === $expectedResult ? '✅' : '❌';

            $table[] = [
                $testCase['role'],
                $testCase['product_type'],
                $expectedResult ? 'Should Pass' : 'Should Fail',
                $actualResult ? 'Passed' : 'Failed',
                $status
            ];

            if ($this->option('detailed') && $hasErrors) {
                $this->warn("  Validation errors for {$testCase['role']} + {$testCase['product_type']}: " . implode(', ', $errors));
            }
        }

        $this->table(['Role', 'Product Type', 'Expected', 'Actual', 'Status'], $table);
    }
}
