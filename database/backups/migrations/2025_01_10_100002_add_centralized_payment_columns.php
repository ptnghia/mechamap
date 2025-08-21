<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 🔄 Add Essential Columns for Centralized Payment System
     * Chỉ thêm những columns thực sự cần thiết và chưa tồn tại
     */
    public function up(): void
    {
        // 1. Thêm columns cho marketplace_orders nếu chưa có
        if (!Schema::hasColumn('marketplace_orders', 'requires_admin_review')) {
            Schema::table('marketplace_orders', function (Blueprint $table) {
                $table->boolean('requires_admin_review')->default(false)
                    ->after('payment_details')
                    ->comment('Đơn hàng cần admin review');
            });
        }

        if (!Schema::hasColumn('marketplace_orders', 'reviewed_by')) {
            Schema::table('marketplace_orders', function (Blueprint $table) {
                $table->foreignId('reviewed_by')->nullable()
                    ->after('requires_admin_review')
                    ->constrained('users')
                    ->onDelete('set null')
                    ->comment('Admin đã review đơn hàng');
            });
        }

        if (!Schema::hasColumn('marketplace_orders', 'reviewed_at')) {
            Schema::table('marketplace_orders', function (Blueprint $table) {
                $table->timestamp('reviewed_at')->nullable()
                    ->after('reviewed_by')
                    ->comment('Thời gian admin review');
            });
        }

        if (!Schema::hasColumn('marketplace_orders', 'seller_paid')) {
            Schema::table('marketplace_orders', function (Blueprint $table) {
                $table->boolean('seller_paid')->default(false)
                    ->after('reviewed_at')
                    ->comment('Đã trả tiền cho seller chưa');
            });
        }

        if (!Schema::hasColumn('marketplace_orders', 'seller_paid_at')) {
            Schema::table('marketplace_orders', function (Blueprint $table) {
                $table->timestamp('seller_paid_at')->nullable()
                    ->after('seller_paid')
                    ->comment('Thời gian trả tiền cho seller');
            });
        }

        // 2. Thêm columns cho marketplace_order_items nếu chưa có
        if (!Schema::hasColumn('marketplace_order_items', 'included_in_payout')) {
            Schema::table('marketplace_order_items', function (Blueprint $table) {
                $table->boolean('included_in_payout')->default(false)
                    ->after('commission_amount')
                    ->comment('Đã bao gồm trong payout chưa');
            });
        }

        if (!Schema::hasColumn('marketplace_order_items', 'payout_included_at')) {
            Schema::table('marketplace_order_items', function (Blueprint $table) {
                $table->timestamp('payout_included_at')->nullable()
                    ->after('included_in_payout')
                    ->comment('Thời gian include vào payout');
            });
        }

        if (!Schema::hasColumn('marketplace_order_items', 'admin_commission')) {
            Schema::table('marketplace_order_items', function (Blueprint $table) {
                $table->decimal('admin_commission', 12, 2)->default(0)
                    ->after('payout_included_at')
                    ->comment('Hoa hồng admin nhận được');
            });
        }

        if (!Schema::hasColumn('marketplace_order_items', 'gateway_fee_share')) {
            Schema::table('marketplace_order_items', function (Blueprint $table) {
                $table->decimal('gateway_fee_share', 12, 2)->default(0)
                    ->after('admin_commission')
                    ->comment('Phần phí gateway seller chịu');
            });
        }

        // 3. Thêm columns cho marketplace_sellers nếu chưa có
        if (!Schema::hasColumn('marketplace_sellers', 'bank_information')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->json('bank_information')->nullable()
                    ->after('commission_rate')
                    ->comment('Thông tin ngân hàng để nhận tiền');
            });
        }

        if (!Schema::hasColumn('marketplace_sellers', 'payout_frequency')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->enum('payout_frequency', ['weekly', 'biweekly', 'monthly'])
                    ->default('monthly')
                    ->after('bank_information')
                    ->comment('Tần suất nhận tiền');
            });
        }

        if (!Schema::hasColumn('marketplace_sellers', 'minimum_payout_amount')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->decimal('minimum_payout_amount', 12, 2)
                    ->default(100000)
                    ->after('payout_frequency')
                    ->comment('Số tiền tối thiểu để payout (VNĐ)');
            });
        }

        if (!Schema::hasColumn('marketplace_sellers', 'total_earnings')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->decimal('total_earnings', 12, 2)->default(0)
                    ->after('minimum_payout_amount')
                    ->comment('Tổng thu nhập từ trước đến nay');
            });
        }

        if (!Schema::hasColumn('marketplace_sellers', 'pending_payout')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->decimal('pending_payout', 12, 2)->default(0)
                    ->after('total_earnings')
                    ->comment('Số tiền chờ thanh toán');
            });
        }

        if (!Schema::hasColumn('marketplace_sellers', 'total_commission_paid')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->decimal('total_commission_paid', 12, 2)->default(0)
                    ->after('pending_payout')
                    ->comment('Tổng hoa hồng đã trả');
            });
        }

        if (!Schema::hasColumn('marketplace_sellers', 'last_payout_at')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->timestamp('last_payout_at')->nullable()
                    ->after('total_commission_paid')
                    ->comment('Lần payout cuối cùng');
            });
        }

        // 4. Tạo bảng cấu hình hệ thống payment nếu chưa có
        if (!Schema::hasTable('payment_system_settings')) {
            Schema::create('payment_system_settings', function (Blueprint $table) {
                $table->id();
                
                // Setting key-value
                $table->string('key')->unique()->comment('Khóa cấu hình');
                $table->text('value')->nullable()->comment('Giá trị cấu hình');
                $table->string('type')->default('string')->comment('Kiểu dữ liệu: string, number, boolean, json');
                $table->text('description')->nullable()->comment('Mô tả cấu hình');
                
                // Grouping
                $table->string('group')->default('general')->comment('Nhóm cấu hình');
                $table->integer('sort_order')->default(0)->comment('Thứ tự hiển thị');
                
                // Status
                $table->boolean('is_active')->default(true);
                $table->boolean('is_system')->default(false)->comment('Cấu hình hệ thống không được xóa');
                
                // Audit
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                // Index
                $table->index(['group', 'sort_order']);
                $table->index(['is_active', 'group']);
            });

            // Insert default settings
            DB::table('payment_system_settings')->insert([
                [
                    'key' => 'admin_bank_account_stripe',
                    'value' => json_encode([
                        'account_id' => '',
                        'account_name' => 'MechaMap Admin Account',
                        'currency' => 'VND'
                    ]),
                    'type' => 'json',
                    'description' => 'Thông tin tài khoản Stripe Admin nhận tiền',
                    'group' => 'payment_gateways',
                    'sort_order' => 1,
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'admin_bank_account_sepay',
                    'value' => json_encode([
                        'bank_code' => 'MBBank',
                        'account_number' => '',
                        'account_name' => 'CONG TY CO PHAN CONG NGHE MECHAMAP',
                        'currency' => 'VND'
                    ]),
                    'type' => 'json',
                    'description' => 'Thông tin tài khoản SePay Admin nhận tiền',
                    'group' => 'payment_gateways',
                    'sort_order' => 2,
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'default_commission_rates',
                    'value' => json_encode([
                        'manufacturer' => 5.0,
                        'supplier' => 3.0,
                        'brand' => 0.0,
                        'verified_partner' => 2.0
                    ]),
                    'type' => 'json',
                    'description' => 'Tỷ lệ hoa hồng mặc định theo role',
                    'group' => 'commission',
                    'sort_order' => 1,
                    'is_system' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'key' => 'minimum_payout_amount',
                    'value' => '100000',
                    'type' => 'number',
                    'description' => 'Số tiền tối thiểu để thực hiện payout (VNĐ)',
                    'group' => 'payout',
                    'sort_order' => 1,
                    'is_system' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new table
        Schema::dropIfExists('payment_system_settings');
        
        // Remove columns from existing tables
        $columnsToRemove = [
            'marketplace_sellers' => [
                'bank_information',
                'payout_frequency', 
                'minimum_payout_amount',
                'total_earnings',
                'pending_payout',
                'total_commission_paid',
                'last_payout_at'
            ],
            'marketplace_order_items' => [
                'included_in_payout',
                'payout_included_at',
                'admin_commission',
                'gateway_fee_share'
            ],
            'marketplace_orders' => [
                'requires_admin_review',
                'reviewed_by',
                'reviewed_at',
                'seller_paid',
                'seller_paid_at'
            ]
        ];

        foreach ($columnsToRemove as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    Schema::table($table, function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }
    }
};
