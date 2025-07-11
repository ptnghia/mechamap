<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentSystemSetting;

class PaymentSystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Stripe Admin Account Configuration
            [
                'key' => 'admin_bank_account_stripe',
                'value' => json_encode([
                    'account_id' => env('STRIPE_ADMIN_ACCOUNT_ID', ''),
                    'account_name' => 'MechaMap Admin Account',
                    'currency' => 'VND',
                    'country' => 'VN',
                    'description' => 'Centralized Stripe account for receiving all marketplace payments'
                ]),
                'type' => 'json',
                'description' => 'Thông tin tài khoản Stripe Admin nhận tiền tập trung',
                'group' => 'payment_gateways',
                'sort_order' => 1,
                'is_system' => true,
                'is_active' => true,
            ],

            // SePay Admin Account Configuration
            [
                'key' => 'admin_bank_account_sepay',
                'value' => json_encode([
                    'bank_code' => env('SEPAY_ADMIN_BANK_CODE', 'MBBank'),
                    'account_number' => env('SEPAY_ADMIN_ACCOUNT_NUMBER', ''),
                    'account_name' => env('SEPAY_ADMIN_ACCOUNT_NAME', 'CONG TY CO PHAN CONG NGHE MECHAMAP'),
                    'currency' => 'VND',
                    'description' => 'Centralized SePay account for receiving all marketplace payments'
                ]),
                'type' => 'json',
                'description' => 'Thông tin tài khoản SePay Admin nhận tiền tập trung',
                'group' => 'payment_gateways',
                'sort_order' => 2,
                'is_system' => true,
                'is_active' => true,
            ],

            // Commission Rates Configuration
            [
                'key' => 'default_commission_rates',
                'value' => json_encode([
                    'manufacturer' => 5.0,
                    'supplier' => 3.0,
                    'brand' => 0.0,
                    'verified_partner' => 2.0
                ]),
                'type' => 'json',
                'description' => 'Tỷ lệ hoa hồng mặc định theo role của seller',
                'group' => 'commission',
                'sort_order' => 1,
                'is_system' => false,
                'is_active' => true,
            ],

            // Payout Settings
            [
                'key' => 'minimum_payout_amount',
                'value' => '100000',
                'type' => 'number',
                'description' => 'Số tiền tối thiểu để thực hiện payout (VNĐ)',
                'group' => 'payout',
                'sort_order' => 1,
                'is_system' => false,
                'is_active' => true,
            ],

            [
                'key' => 'auto_payout_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Tự động xử lý payout khi đủ điều kiện',
                'group' => 'payout',
                'sort_order' => 2,
                'is_system' => false,
                'is_active' => true,
            ],

            [
                'key' => 'payout_processing_days',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Số ngày xử lý payout (business days)',
                'group' => 'payout',
                'sort_order' => 3,
                'is_system' => false,
                'is_active' => true,
            ],

            // Gateway Fee Settings
            [
                'key' => 'stripe_fee_percentage',
                'value' => '3.4',
                'type' => 'number',
                'description' => 'Phí Stripe theo phần trăm (%)',
                'group' => 'gateway_fees',
                'sort_order' => 1,
                'is_system' => false,
                'is_active' => true,
            ],

            [
                'key' => 'stripe_fee_fixed',
                'value' => '10000',
                'type' => 'number',
                'description' => 'Phí cố định Stripe (VNĐ)',
                'group' => 'gateway_fees',
                'sort_order' => 2,
                'is_system' => false,
                'is_active' => true,
            ],

            [
                'key' => 'sepay_fee_percentage',
                'value' => '0',
                'type' => 'number',
                'description' => 'Phí SePay theo phần trăm (%)',
                'group' => 'gateway_fees',
                'sort_order' => 3,
                'is_system' => false,
                'is_active' => true,
            ],

            [
                'key' => 'sepay_fee_fixed',
                'value' => '0',
                'type' => 'number',
                'description' => 'Phí cố định SePay (VNĐ)',
                'group' => 'gateway_fees',
                'sort_order' => 4,
                'is_system' => false,
                'is_active' => true,
            ],

            // Security Settings
            [
                'key' => 'require_admin_review_threshold',
                'value' => '5000000',
                'type' => 'number',
                'description' => 'Ngưỡng đơn hàng cần admin review (VNĐ)',
                'group' => 'security',
                'sort_order' => 1,
                'is_system' => false,
                'is_active' => true,
            ],

            [
                'key' => 'max_daily_payout_amount',
                'value' => '50000000',
                'type' => 'number',
                'description' => 'Số tiền payout tối đa mỗi ngày (VNĐ)',
                'group' => 'security',
                'sort_order' => 2,
                'is_system' => false,
                'is_active' => true,
            ],

            // Notification Settings
            [
                'key' => 'notify_admin_on_large_payments',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Thông báo admin khi có payment lớn',
                'group' => 'notifications',
                'sort_order' => 1,
                'is_system' => false,
                'is_active' => true,
            ],

            [
                'key' => 'notify_sellers_on_payout',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Thông báo sellers khi có payout',
                'group' => 'notifications',
                'sort_order' => 2,
                'is_system' => false,
                'is_active' => true,
            ],

            // System Configuration
            [
                'key' => 'centralized_payment_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Bật/tắt hệ thống thanh toán tập trung',
                'group' => 'general',
                'sort_order' => 1,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'key' => 'payment_audit_retention_days',
                'value' => '2555',
                'type' => 'integer',
                'description' => 'Số ngày lưu trữ audit logs (7 năm)',
                'group' => 'general',
                'sort_order' => 2,
                'is_system' => true,
                'is_active' => true,
            ],
        ];

        foreach ($settings as $setting) {
            PaymentSystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Payment system settings seeded successfully!');
    }
}
