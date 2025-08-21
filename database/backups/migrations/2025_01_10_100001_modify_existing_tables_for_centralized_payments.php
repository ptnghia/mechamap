<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🔄 Modify Existing Tables for Centralized Payment System
     * Cập nhật các bảng hiện tại để support centralized payment flow
     */
    public function up(): void
    {
        // 1. Cập nhật marketplace_orders table
        Schema::table('marketplace_orders', function (Blueprint $table) {
            // Thêm reference đến centralized payment (check if not exists)
            if (!Schema::hasColumn('marketplace_orders', 'centralized_payment_id')) {
                $table->foreignId('centralized_payment_id')->nullable()
                    ->after('payment_details')
                    ->constrained('centralized_payments')
                    ->onDelete('set null')
                    ->comment('Link đến centralized payment record');
            }

            // Thêm admin processing fields
            if (!Schema::hasColumn('marketplace_orders', 'requires_admin_review')) {
                $table->boolean('requires_admin_review')->default(false)
                    ->after('centralized_payment_id')
                    ->comment('Đơn hàng cần admin review');
            }

            if (!Schema::hasColumn('marketplace_orders', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()
                    ->after('requires_admin_review')
                    ->constrained('users')
                    ->onDelete('set null')
                    ->comment('Admin đã review đơn hàng');
            }

            if (!Schema::hasColumn('marketplace_orders', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()
                    ->after('reviewed_by')
                    ->comment('Thời gian admin review');
            }

            // Thêm payout tracking
            if (!Schema::hasColumn('marketplace_orders', 'seller_paid')) {
                $table->boolean('seller_paid')->default(false)
                    ->after('reviewed_at')
                    ->comment('Đã trả tiền cho seller chưa');
            }

            if (!Schema::hasColumn('marketplace_orders', 'seller_paid_at')) {
                $table->timestamp('seller_paid_at')->nullable()
                    ->after('seller_paid')
                    ->comment('Thời gian trả tiền cho seller');
            }

            // Index cho performance - sẽ skip nếu đã tồn tại
            try {
                $table->index(['centralized_payment_id']);
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            try {
                $table->index(['requires_admin_review', 'status']);
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            try {
                $table->index(['seller_paid', 'created_at']);
            } catch (\Exception $e) {
                // Index already exists, skip
            }
        });

        // 2. Cập nhật marketplace_order_items table
        Schema::table('marketplace_order_items', function (Blueprint $table) {
            // Thêm reference đến payout system
            if (!Schema::hasColumn('marketplace_order_items', 'payout_request_id')) {
                $table->foreignId('payout_request_id')->nullable()
                    ->after('commission_amount')
                    ->constrained('seller_payout_requests')
                    ->onDelete('set null')
                    ->comment('Link đến payout request');
            }

            if (!Schema::hasColumn('marketplace_order_items', 'included_in_payout')) {
                $table->boolean('included_in_payout')->default(false)
                    ->after('payout_request_id')
                    ->comment('Đã bao gồm trong payout chưa');
            }

            if (!Schema::hasColumn('marketplace_order_items', 'payout_included_at')) {
                $table->timestamp('payout_included_at')->nullable()
                    ->after('included_in_payout')
                    ->comment('Thời gian include vào payout');
            }

            // Thêm commission calculation details
            if (!Schema::hasColumn('marketplace_order_items', 'admin_commission')) {
                $table->decimal('admin_commission', 12, 2)->default(0)
                    ->after('payout_included_at')
                    ->comment('Hoa hồng admin nhận được');
            }

            if (!Schema::hasColumn('marketplace_order_items', 'gateway_fee_share')) {
                $table->decimal('gateway_fee_share', 12, 2)->default(0)
                    ->after('admin_commission')
                    ->comment('Phần phí gateway seller chịu');
            }

            // Index
            try {
                $table->index(['payout_request_id']);
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            try {
                $table->index(['included_in_payout', 'seller_id']);
            } catch (\Exception $e) {
                // Index already exists, skip
            }
        });

        // 3. Cập nhật marketplace_sellers table
        Schema::table('marketplace_sellers', function (Blueprint $table) {
            // Thêm bank information cho payout
            if (!Schema::hasColumn('marketplace_sellers', 'bank_information')) {
                $table->json('bank_information')->nullable()
                    ->after('commission_rate')
                    ->comment('Thông tin ngân hàng để nhận tiền');
            }

            // Thêm payout preferences
            if (!Schema::hasColumn('marketplace_sellers', 'payout_frequency')) {
                $table->enum('payout_frequency', ['weekly', 'biweekly', 'monthly'])
                    ->default('monthly')
                    ->after('bank_information')
                    ->comment('Tần suất nhận tiền');
            }

            if (!Schema::hasColumn('marketplace_sellers', 'minimum_payout_amount')) {
                $table->decimal('minimum_payout_amount', 12, 2)
                    ->default(100000)
                    ->after('payout_frequency')
                    ->comment('Số tiền tối thiểu để payout (VNĐ)');
            }

            // Thêm tracking fields
            if (!Schema::hasColumn('marketplace_sellers', 'total_earnings')) {
                $table->decimal('total_earnings', 12, 2)->default(0)
                    ->after('minimum_payout_amount')
                    ->comment('Tổng thu nhập từ trước đến nay');
            }

            if (!Schema::hasColumn('marketplace_sellers', 'pending_payout')) {
                $table->decimal('pending_payout', 12, 2)->default(0)
                    ->after('total_earnings')
                    ->comment('Số tiền chờ thanh toán');
            }

            if (!Schema::hasColumn('marketplace_sellers', 'total_commission_paid')) {
                $table->decimal('total_commission_paid', 12, 2)->default(0)
                    ->after('pending_payout')
                    ->comment('Tổng hoa hồng đã trả');
            }

            if (!Schema::hasColumn('marketplace_sellers', 'last_payout_at')) {
                $table->timestamp('last_payout_at')->nullable()
                    ->after('total_commission_paid')
                    ->comment('Lần payout cuối cùng');
            }

            // Index
            try {
                $table->index(['payout_frequency', 'status']);
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            try {
                $table->index(['pending_payout']);
            } catch (\Exception $e) {
                // Index already exists, skip
            }
        });

        // 4. Cập nhật payment_transactions table (nếu cần)
        if (Schema::hasTable('payment_transactions')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                // Link đến centralized payment
                if (!Schema::hasColumn('payment_transactions', 'centralized_payment_id')) {
                    $table->foreignId('centralized_payment_id')->nullable()
                        ->after('user_id')
                        ->constrained('centralized_payments')
                        ->onDelete('set null')
                        ->comment('Link đến centralized payment');
                }

                // Thêm admin processing flag
                if (!Schema::hasColumn('payment_transactions', 'is_admin_transaction')) {
                    $table->boolean('is_admin_transaction')->default(false)
                        ->after('centralized_payment_id')
                        ->comment('Giao dịch do admin thực hiện');
                }

                // Index
                $table->index(['centralized_payment_id']);
                $table->index(['is_admin_transaction', 'type']);
            });
        }

        // 5. Tạo bảng cấu hình hệ thống payment
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

        // 6. Insert default payment system settings
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
            ],
            [
                'key' => 'auto_payout_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Tự động payout cho sellers',
                'group' => 'payout',
                'sort_order' => 2,
                'is_system' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'payout_processing_days',
                'value' => '7',
                'type' => 'number',
                'description' => 'Số ngày xử lý payout (business days)',
                'group' => 'payout',
                'sort_order' => 3,
                'is_system' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new table
        Schema::dropIfExists('payment_system_settings');

        // Remove columns from existing tables
        Schema::table('marketplace_sellers', function (Blueprint $table) {
            $table->dropColumn([
                'bank_information',
                'payout_frequency',
                'minimum_payout_amount',
                'total_earnings',
                'pending_payout',
                'total_commission_paid',
                'last_payout_at'
            ]);
        });

        Schema::table('marketplace_order_items', function (Blueprint $table) {
            $table->dropForeign(['payout_request_id']);
            $table->dropColumn([
                'payout_request_id',
                'included_in_payout',
                'payout_included_at',
                'admin_commission',
                'gateway_fee_share'
            ]);
        });

        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->dropForeign(['centralized_payment_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'centralized_payment_id',
                'requires_admin_review',
                'reviewed_by',
                'reviewed_at',
                'seller_paid',
                'seller_paid_at'
            ]);
        });

        if (Schema::hasTable('payment_transactions')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('payment_transactions', 'centralized_payment_id')) {
                    $table->dropForeign(['centralized_payment_id']);
                    $table->dropColumn('centralized_payment_id');
                }
                if (Schema::hasColumn('payment_transactions', 'is_admin_transaction')) {
                    $table->dropColumn('is_admin_transaction');
                }
            });
        }
    }
};
