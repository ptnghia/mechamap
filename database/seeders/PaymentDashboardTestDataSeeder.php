<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MarketplaceOrder;
use App\Models\CentralizedPayment;
use App\Models\PaymentAuditLog;
use App\Services\CentralizedPaymentService;
use Carbon\Carbon;

class PaymentDashboardTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test data for Payment Management Dashboard...');

        // Find or create test user
        $user = User::where('email', 'test@mechamap.vn')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test Customer',
                'email' => 'test@mechamap.vn',
                'username' => 'testcustomer',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $service = new CentralizedPaymentService();

        // Create test orders and payments for the last 30 days
        for ($i = 0; $i < 20; $i++) {
            $createdAt = Carbon::now()->subDays(rand(0, 30));

            // Create test order
            $order = MarketplaceOrder::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'order_number' => 'TEST-DASH-' . time() . '-' . $i,
                'customer_id' => $user->id,
                'customer_email' => $user->email,
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => rand(100000, 2000000), // 100k - 2M VND
                'tax_amount' => 0,
                'shipping_amount' => rand(0, 50000),
                'discount_amount' => 0,
                'total_amount' => 0, // Will be calculated after creation
                'currency' => 'VND',
                'shipping_address' => [
                    'name' => 'Test Customer ' . $i,
                    'address' => 'Test Address ' . $i,
                    'city' => 'Ho Chi Minh City',
                    'country' => 'Vietnam'
                ],
                'billing_address' => [
                    'name' => 'Test Customer ' . $i,
                    'address' => 'Test Address ' . $i,
                    'city' => 'Ho Chi Minh City',
                    'country' => 'Vietnam'
                ],
                // metadata column doesn't exist in marketplace_orders
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Calculate total_amount properly
            $order->update([
                'total_amount' => $order->subtotal + $order->shipping_amount
            ]);

            // Create centralized payment
            $paymentMethod = rand(0, 1) ? 'stripe' : 'sepay';
            $centralizedPayment = $service->createPayment($order, $paymentMethod);

            // Update created_at for centralized payment
            $centralizedPayment->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Randomly set payment status
            $statusRand = rand(1, 100);
            if ($statusRand <= 80) {
                // 80% success rate
                $centralizedPayment->markAsCompleted([
                    'payment_intent_id' => 'pi_test_dashboard_' . time() . '_' . $i,
                    'amount' => $order->total_amount,
                    'status' => 'succeeded'
                ]);

                $order->update([
                    'status' => 'completed',
                    'payment_status' => 'paid'
                ]);
            } elseif ($statusRand <= 90) {
                // 10% failed
                $centralizedPayment->markAsFailed('Test payment failure', [
                    'payment_intent_id' => 'pi_test_dashboard_' . time() . '_' . $i,
                    'error' => 'Test failure'
                ]);

                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed'
                ]);
            }
            // 10% remain pending

            $orderNum = $i + 1;
            $this->command->info("Created test order #$orderNum: {$order->order_number}");
        }

        // Create some test audit logs
        $this->createTestAuditLogs();

        $this->command->info('Payment Dashboard test data created successfully!');
    }

    /**
     * Create test audit logs
     */
    protected function createTestAuditLogs(): void
    {
        $events = [
            'payment_completed',
            'payment_failed',
            'payout_approved',
            'payout_completed',
            'commission_updated',
            'settings_changed'
        ];

        for ($i = 0; $i < 50; $i++) {
            PaymentAuditLog::create([
                'event_type' => $events[array_rand($events)],
                'entity_type' => 'centralized_payment',
                'entity_id' => rand(1, 20),
                'user_id' => User::inRandomOrder()->first()->id ?? 1,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test User Agent',
                'old_values' => null,
                'new_values' => [
                    'status' => 'completed',
                    'test_data' => true
                ],
                'metadata' => [
                    'test_log' => true,
                    'created_for' => 'dashboard_demo'
                ],
                'amount_impact' => rand(-1000000, 1000000),
                'currency' => 'VND',
                'description' => 'Test audit log entry #' . ($i + 1),
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
