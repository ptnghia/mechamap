<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 🏦 MechaMap Centralized Payment Management System
     * Tạo hệ thống quản lý thanh toán tập trung cho Admin
     * Tất cả payments từ customers sẽ đi về Admin account trước
     */
    public function up(): void
    {
        // 1. Bảng cấu hình hoa hồng theo role và product type
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            
            // Commission Configuration
            $table->enum('seller_role', ['manufacturer', 'supplier', 'brand', 'verified_partner']);
            $table->enum('product_type', ['digital', 'new_product', 'used_product', 'service'])->nullable();
            $table->decimal('commission_rate', 5, 2)->comment('Tỷ lệ hoa hồng (%)');
            $table->decimal('fixed_fee', 10, 2)->default(0)->comment('Phí cố định (VNĐ)');
            $table->decimal('min_commission', 10, 2)->default(0)->comment('Hoa hồng tối thiểu (VNĐ)');
            $table->decimal('max_commission', 10, 2)->nullable()->comment('Hoa hồng tối đa (VNĐ)');
            
            // Rules & Conditions
            $table->decimal('min_order_value', 12, 2)->default(0)->comment('Giá trị đơn hàng tối thiểu');
            $table->json('special_conditions')->nullable()->comment('Điều kiện đặc biệt');
            
            // Status & Metadata
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamp('effective_from')->default(now());
            $table->timestamp('effective_until')->nullable();
            
            // Audit
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['seller_role', 'product_type', 'is_active']);
            $table->index(['effective_from', 'effective_until']);
        });

        // 2. Bảng quản lý thanh toán tập trung (Admin receives all payments)
        Schema::create('centralized_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference')->unique()->comment('Mã tham chiếu thanh toán');
            
            // Order & Customer Information
            $table->foreignId('order_id')->constrained('marketplace_orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_email');
            
            // Payment Gateway Information
            $table->enum('payment_method', ['stripe', 'sepay']);
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_payment_intent_id')->nullable();
            $table->json('gateway_response')->nullable();
            
            // Payment Amounts (All in VNĐ)
            $table->decimal('gross_amount', 12, 2)->comment('Tổng tiền khách hàng trả');
            $table->decimal('gateway_fee', 12, 2)->default(0)->comment('Phí payment gateway');
            $table->decimal('net_received', 12, 2)->comment('Tiền thực nhận vào Admin account');
            
            // Payment Status
            $table->enum('status', [
                'pending',      // Chờ thanh toán
                'processing',   // Đang xử lý
                'completed',    // Đã hoàn thành
                'failed',       // Thất bại
                'cancelled',    // Đã hủy
                'refunded'      // Đã hoàn tiền
            ])->default('pending');
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['order_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['gateway_transaction_id']);
            $table->index(['paid_at', 'status']);
        });

        // 3. Bảng yêu cầu thanh toán cho Seller (Payout Requests)
        Schema::create('seller_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->string('payout_reference')->unique()->comment('Mã tham chiếu payout');
            
            // Seller Information
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_account_id')->constrained('marketplace_sellers')->onDelete('cascade');
            
            // Payout Details
            $table->decimal('total_sales', 12, 2)->comment('Tổng doanh thu');
            $table->decimal('commission_amount', 12, 2)->comment('Tổng hoa hồng');
            $table->decimal('net_payout', 12, 2)->comment('Số tiền thực trả cho seller');
            $table->integer('order_count')->comment('Số đơn hàng');
            
            // Period Information
            $table->date('period_from')->comment('Từ ngày');
            $table->date('period_to')->comment('Đến ngày');
            
            // Bank Information for Payout
            $table->json('bank_details')->nullable()->comment('Thông tin ngân hàng nhận tiền');
            
            // Status & Processing
            $table->enum('status', [
                'pending',      // Chờ xử lý
                'approved',     // Đã phê duyệt
                'processing',   // Đang chuyển tiền
                'completed',    // Đã hoàn thành
                'rejected',     // Bị từ chối
                'cancelled'     // Đã hủy
            ])->default('pending');
            
            // Admin Processing
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['seller_id', 'status']);
            $table->index(['status', 'period_from', 'period_to']);
            $table->index(['processed_by', 'status']);
        });

        // 4. Bảng chi tiết payout cho từng order item
        Schema::create('seller_payout_items', function (Blueprint $table) {
            $table->id();
            
            // References
            $table->foreignId('payout_request_id')->constrained('seller_payout_requests')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('marketplace_orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('marketplace_order_items')->onDelete('cascade');
            $table->foreignId('centralized_payment_id')->constrained('centralized_payments')->onDelete('cascade');
            
            // Product & Seller Info
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            
            // Financial Breakdown
            $table->decimal('item_price', 12, 2)->comment('Giá sản phẩm');
            $table->integer('quantity')->comment('Số lượng');
            $table->decimal('item_total', 12, 2)->comment('Tổng tiền item');
            $table->decimal('commission_rate', 5, 2)->comment('Tỷ lệ hoa hồng (%)');
            $table->decimal('commission_amount', 12, 2)->comment('Số tiền hoa hồng');
            $table->decimal('seller_earnings', 12, 2)->comment('Tiền seller nhận được');
            
            // Status
            $table->enum('status', ['pending', 'included', 'paid', 'disputed'])->default('pending');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['payout_request_id', 'seller_id']);
            $table->index(['order_id', 'seller_id']);
            $table->index(['seller_id', 'status']);
        });

        // 5. Bảng audit log cho tất cả financial transactions
        Schema::create('payment_audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Event Information
            $table->string('event_type')->comment('Loại sự kiện: payment, payout, refund, etc.');
            $table->string('entity_type')->comment('Loại entity: order, payment, payout, etc.');
            $table->unsignedBigInteger('entity_id')->comment('ID của entity');
            
            // User & Admin Information
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            // Change Details
            $table->json('old_values')->nullable()->comment('Giá trị cũ');
            $table->json('new_values')->nullable()->comment('Giá trị mới');
            $table->json('metadata')->nullable()->comment('Thông tin bổ sung');
            
            // Financial Impact
            $table->decimal('amount_impact', 12, 2)->nullable()->comment('Tác động tài chính');
            $table->string('currency', 3)->default('VND');
            
            // Description & Notes
            $table->text('description')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['entity_type', 'entity_id']);
            $table->index(['event_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['admin_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_audit_logs');
        Schema::dropIfExists('seller_payout_items');
        Schema::dropIfExists('seller_payout_requests');
        Schema::dropIfExists('centralized_payments');
        Schema::dropIfExists('commission_settings');
    }
};
