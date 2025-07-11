<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\SellerPayoutRequest;
use Carbon\Carbon;

class SimplePayoutTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating simple payout test data...');

        // Create test sellers if they don't exist
        $sellers = $this->createTestSellers();

        // Create payout requests
        foreach ($sellers as $seller) {
            $this->createPayoutRequestsForSeller($seller);
        }

        $this->command->info('Simple payout test data created successfully!');
    }

    /**
     * Create test sellers
     */
    protected function createTestSellers(): array
    {
        $sellers = [];

        for ($i = 1; $i <= 5; $i++) {
            // Create user
            $user = User::firstOrCreate(
                ['email' => "seller{$i}@mechamap.vn"],
                [
                    'name' => "Test Seller {$i}",
                    'username' => "testseller{$i}",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Create seller account
            $sellerAccount = MarketplaceSeller::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'seller_type' => 'manufacturer',
                    'business_name' => "Business Seller {$i}",
                    'business_type' => 'individual',
                    'contact_person_name' => "Contact Person {$i}",
                    'contact_email' => "seller{$i}@mechamap.vn",
                    'contact_phone' => "090325242700{$i}",
                    'business_address' => "Test Business Address {$i}",
                    'status' => 'active',
                    'commission_rate' => rand(3, 7),
                    'bank_information' => json_encode([
                        'bank_name' => 'MBBank',
                        'account_number' => '090325242700' . $i,
                        'account_name' => "TEST SELLER {$i}",
                    ]),
                    'payout_frequency' => ['weekly', 'biweekly', 'monthly'][rand(0, 2)],
                    'minimum_payout_amount' => 100000,
                    'total_earnings' => 0,
                    'pending_payout' => 0,
                    'total_commission_paid' => 0,
                ]
            );

            $sellers[] = [
                'user' => $user,
                'account' => $sellerAccount
            ];
        }

        return $sellers;
    }

    /**
     * Create payout requests for seller
     */
    protected function createPayoutRequestsForSeller($seller): void
    {
        $user = $seller['user'];
        $sellerAccount = $seller['account'];

        // Create some mock payout requests for different months
        for ($i = 1; $i <= 3; $i++) {
            $monthsAgo = $i;
            $monthStart = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $totalSales = rand(500000, 5000000); // 500k - 5M VND
            $commissionRate = $sellerAccount->commission_rate;
            $commissionAmount = ($totalSales * $commissionRate) / 100;
            $netPayout = $totalSales - $commissionAmount;

            // Random status for variety
            $statuses = ['pending', 'approved', 'completed', 'rejected'];
            $weights = [40, 30, 25, 5]; // 40% pending, 30% approved, 25% completed, 5% rejected
            $status = $this->weightedRandom($statuses, $weights);

            $payoutRequest = SellerPayoutRequest::create([
                'payout_reference' => SellerPayoutRequest::generatePayoutReference(),
                'seller_id' => $user->id,
                'seller_account_id' => $sellerAccount->id,
                'total_sales' => $totalSales,
                'commission_amount' => $commissionAmount,
                'net_payout' => $netPayout,
                'order_count' => rand(5, 20),
                'period_from' => $monthStart,
                'period_to' => $monthEnd,
                'bank_details' => json_decode($sellerAccount->bank_information, true),
                'status' => $status,
                'created_at' => $monthEnd->addDays(rand(1, 5)), // Created few days after month end
            ]);

            // Update timestamps and notes based on status
            if (in_array($status, ['approved', 'completed'])) {
                $payoutRequest->update([
                    'approved_at' => $payoutRequest->created_at->addDays(rand(1, 3)),
                    'processed_by' => 1, // Assuming admin user ID 1
                    'admin_notes' => 'Test approval for demo purposes',
                ]);
            }

            if ($status === 'completed') {
                $payoutRequest->update([
                    'completed_at' => $payoutRequest->approved_at->addDays(rand(1, 5)),
                    'admin_notes' => $payoutRequest->admin_notes . "\nCompleted: Payment sent via bank transfer",
                ]);

                // Update seller account
                $sellerAccount->increment('total_earnings', $netPayout);
            } elseif ($status === 'pending') {
                $sellerAccount->increment('pending_payout', $netPayout);
            } elseif ($status === 'rejected') {
                $payoutRequest->update([
                    'processed_by' => 1,
                    'processed_at' => $payoutRequest->created_at->addDays(rand(1, 2)),
                    'rejection_reason' => 'Test rejection: Incomplete bank information',
                ]);
            }

            $this->command->info("Created payout request: {$payoutRequest->payout_reference} for {$user->name} - Status: {$status}");
        }
    }

    /**
     * Weighted random selection
     */
    protected function weightedRandom($values, $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        for ($i = 0; $i < count($values); $i++) {
            $currentWeight += $weights[$i];
            if ($random <= $currentWeight) {
                return $values[$i];
            }
        }
        
        return $values[0]; // Fallback
    }
}
