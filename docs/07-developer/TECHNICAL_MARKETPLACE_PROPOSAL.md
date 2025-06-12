# 🛒 **TECHNICAL MARKETPLACE - Phương Án Phát Triển**

> **Mở rộng Showcase thành Marketplace bán tài liệu kỹ thuật cho MechaMap**  
> **Tích hợp**: Showcase + E-commerce + Digital Asset Protection

---

## 📋 **TÓM TẮT EXECUTIVE**

### **🎯 Mục Tiêu**
Phát triển hệ thống showcase hiện tại thành một marketplace hoàn chỉnh cho việc mua bán tài liệu kỹ thuật, bản vẽ CAD, và digital assets trong lĩnh vực cơ khí.

### **🔑 Tính Năng Core**
- **Digital Asset Store**: Bán bản vẽ, tài liệu kỹ thuật, tutorials
- **Secure Downloads**: File được bảo mật, chỉ người mua mới truy cập được
- **Payment Integration**: Tích hợp gateway thanh toán
- **License Management**: Quản lý license và quyền sử dụng
- **Revenue Sharing**: Chia sẻ doanh thu với tác giả

---

## 🏗️ **KIẾN TRÚC HỆ THỐNG**

### **1. Database Schema Extension**

#### **Products Table** (Mở rộng từ Showcases)
```sql
CREATE TABLE technical_products (
    id BIGINT PRIMARY KEY,
    showcase_id BIGINT REFERENCES showcases(id),
    seller_id BIGINT REFERENCES users(id),
    
    -- Product Information
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    short_description TEXT,
    
    -- Pricing & Sales
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    sale_price DECIMAL(10,2) GENERATED ALWAYS AS (price * (1 - discount_percentage/100)),
    
    -- Product Categories
    category_id BIGINT REFERENCES product_categories(id),
    tags JSON, -- ["CAD", "SolidWorks", "Mechanical"]
    
    -- Technical Specifications
    software_compatibility JSON, -- {"solidworks": "2020+", "autocad": "2019+"}
    file_formats JSON, -- ["dwg", "step", "pdf", "docx"]
    complexity_level ENUM('beginner', 'intermediate', 'advanced'),
    industry_applications JSON, -- ["automotive", "aerospace", "manufacturing"]
    
    -- Digital Assets
    preview_images JSON, -- Array of preview image URLs
    sample_files JSON, -- Free sample files for preview
    protected_files JSON, -- Encrypted files for buyers only
    documentation_files JSON, -- Setup guides, tutorials
    
    -- Sales & Analytics
    download_count INT DEFAULT 0,
    sales_count INT DEFAULT 0,
    total_revenue DECIMAL(12,2) DEFAULT 0,
    rating_average DECIMAL(3,2) DEFAULT 0,
    rating_count INT DEFAULT 0,
    
    -- Status & Moderation
    status ENUM('draft', 'pending', 'approved', 'rejected', 'suspended') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    is_bestseller BOOLEAN DEFAULT FALSE,
    featured_until TIMESTAMP NULL,
    
    -- SEO & Marketing
    meta_title VARCHAR(255),
    meta_description TEXT,
    keywords TEXT,
    
    -- Timestamps
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW() ON UPDATE NOW(),
    
    -- Indexes
    INDEX idx_seller_status (seller_id, status),
    INDEX idx_category_featured (category_id, is_featured),
    INDEX idx_price_range (price, status),
    INDEX idx_ratings (rating_average, rating_count),
    FULLTEXT idx_search (title, description, keywords)
);
```

#### **Product Categories**
```sql
CREATE TABLE product_categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(255), -- Icon URL
    parent_id BIGINT REFERENCES product_categories(id),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    commission_rate DECIMAL(5,2) DEFAULT 10.00, -- Platform commission %
    
    -- Engineering specific categories
    engineering_discipline VARCHAR(50), -- "mechanical", "electrical", "civil"
    required_software JSON, -- Software tags for this category
    
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW() ON UPDATE NOW(),
    
    INDEX idx_parent_active (parent_id, is_active),
    INDEX idx_discipline (engineering_discipline)
);
```

#### **Purchase & License Management**
```sql
CREATE TABLE product_purchases (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT REFERENCES technical_products(id),
    buyer_id BIGINT REFERENCES users(id),
    seller_id BIGINT REFERENCES users(id),
    
    -- Transaction Details
    purchase_token VARCHAR(64) UNIQUE NOT NULL, -- Unique per purchase
    amount_paid DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    platform_fee DECIMAL(10,2) NOT NULL,
    seller_revenue DECIMAL(10,2) NOT NULL,
    
    -- Payment Information
    payment_method VARCHAR(50), -- "card", "paypal", "bank_transfer"
    payment_id VARCHAR(255), -- Gateway transaction ID
    payment_status ENUM('pending', 'completed', 'failed', 'refunded'),
    payment_gateway VARCHAR(50), -- "stripe", "paypal", "vnpay"
    
    -- License & Access
    license_type ENUM('single_use', 'commercial', 'educational', 'unlimited'),
    license_key VARCHAR(128) UNIQUE,
    download_limit INT DEFAULT 5, -- Number of allowed downloads
    download_count INT DEFAULT 0,
    expires_at TIMESTAMP NULL, -- License expiration
    
    -- Download Security
    download_token VARCHAR(128) UNIQUE, -- Changes after each download
    last_download_at TIMESTAMP NULL,
    download_ip_addresses JSON, -- Track download IPs
    
    -- Status & Tracking
    status ENUM('active', 'expired', 'revoked', 'refunded') DEFAULT 'active',
    refund_reason TEXT NULL,
    refunded_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW() ON UPDATE NOW(),
    
    -- Indexes
    INDEX idx_buyer_status (buyer_id, status),
    INDEX idx_product_sales (product_id, payment_status),
    INDEX idx_purchase_token (purchase_token),
    INDEX idx_download_token (download_token),
    
    -- Ensure one active purchase per user per product
    UNIQUE KEY unique_active_purchase (product_id, buyer_id, status)
);
```

#### **Protected File Management**
```sql
CREATE TABLE protected_files (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT REFERENCES technical_products(id),
    
    -- File Information
    original_filename VARCHAR(255) NOT NULL,
    encrypted_filename VARCHAR(255) NOT NULL, -- Hashed filename on disk
    file_path VARCHAR(500) NOT NULL, -- Encrypted storage path
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_hash VARCHAR(128) NOT NULL, -- SHA-256 for integrity
    
    -- File Categories
    file_type ENUM('cad_file', 'documentation', 'calculation', 'tutorial', 'sample'),
    software_required VARCHAR(100), -- "SolidWorks 2020+", "AutoCAD 2019+"
    description TEXT,
    
    -- Security
    encryption_key VARCHAR(128) NOT NULL, -- Unique per file
    encryption_method VARCHAR(50) DEFAULT 'AES-256-CBC',
    access_level ENUM('preview', 'sample', 'full_access') DEFAULT 'full_access',
    
    -- Access Control
    download_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW() ON UPDATE NOW(),
    
    INDEX idx_product_type (product_id, file_type),
    INDEX idx_encrypted_filename (encrypted_filename)
);
```

#### **Download Tracking & Security**
```sql
CREATE TABLE secure_downloads (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    purchase_id BIGINT REFERENCES product_purchases(id),
    protected_file_id BIGINT REFERENCES protected_files(id),
    user_id BIGINT REFERENCES users(id),
    
    -- Download Security
    download_token VARCHAR(128) UNIQUE NOT NULL,
    download_url VARCHAR(500) NOT NULL, -- Temporary signed URL
    expires_at TIMESTAMP NOT NULL, -- URL expiration (usually 24 hours)
    
    -- Tracking
    downloaded_at TIMESTAMP NULL,
    download_ip VARCHAR(45), -- Support IPv6
    user_agent TEXT,
    download_size BIGINT,
    download_duration_seconds INT,
    
    -- Security
    is_completed BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT FALSE, -- File integrity verified
    failure_reason VARCHAR(255) NULL,
    
    created_at TIMESTAMP DEFAULT NOW(),
    
    INDEX idx_token_expires (download_token, expires_at),
    INDEX idx_user_downloads (user_id, downloaded_at),
    INDEX idx_purchase_files (purchase_id, protected_file_id)
);
```

#### **Shopping Cart & Wishlist**
```sql
-- Giỏ hàng cho phép mua nhiều sản phẩm cùng lúc
CREATE TABLE shopping_carts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT REFERENCES users(id),
    product_id BIGINT REFERENCES technical_products(id),
    license_type ENUM('single_use', 'commercial', 'educational', 'unlimited'),
    added_at TIMESTAMP DEFAULT NOW(),
    
    INDEX idx_user_cart (user_id),
    UNIQUE KEY unique_cart_item (user_id, product_id)
);

-- Danh sách yêu thích
CREATE TABLE wishlists (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT REFERENCES users(id),
    product_id BIGINT REFERENCES technical_products(id),
    added_at TIMESTAMP DEFAULT NOW(),
    
    INDEX idx_user_wishlist (user_id),
    UNIQUE KEY unique_wishlist_item (user_id, product_id)
);
```

#### **Payment Transactions & History**
```sql
-- Lịch sử giao dịch thanh toán chi tiết
CREATE TABLE payment_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    purchase_id BIGINT REFERENCES product_purchases(id),
    user_id BIGINT REFERENCES users(id),
    
    -- Transaction Details
    transaction_id VARCHAR(255) UNIQUE NOT NULL, -- Gateway transaction ID
    gateway_type VARCHAR(50) NOT NULL, -- "stripe", "paypal", "vnpay", "momo"
    payment_method VARCHAR(50), -- "card", "bank_transfer", "ewallet"
    
    -- Amounts
    gross_amount DECIMAL(12,2) NOT NULL,
    net_amount DECIMAL(12,2) NOT NULL,
    platform_fee DECIMAL(12,2) NOT NULL,
    gateway_fee DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(12,2) DEFAULT 0,
    currency VARCHAR(3) NOT NULL,
    
    -- Status & Tracking
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'),
    gateway_status VARCHAR(100), -- Gateway-specific status
    failure_reason TEXT NULL,
    
    -- Metadata
    gateway_response JSON, -- Store full gateway response
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    -- Timestamps
    initiated_at TIMESTAMP DEFAULT NOW(),
    completed_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    
    INDEX idx_user_transactions (user_id, status),
    INDEX idx_gateway_tracking (gateway_type, transaction_id),
    INDEX idx_purchase_payments (purchase_id, status)
);
```

#### **Order Management System**
```sql
-- Hệ thống đơn hàng (có thể chứa nhiều sản phẩm)
CREATE TABLE orders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL, -- ORD-2024-001234
    user_id BIGINT REFERENCES users(id),
    
    -- Order Totals
    subtotal DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(12,2) DEFAULT 0,
    platform_fee DECIMAL(12,2) NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    
    -- Status & Processing
    status ENUM('pending', 'processing', 'completed', 'cancelled', 'refunded'),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded'),
    
    -- Billing Information
    billing_email VARCHAR(255),
    billing_name VARCHAR(255),
    billing_country VARCHAR(2),
    billing_details JSON, -- Store full billing address
    
    -- Order Processing
    processed_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    
    -- Notes
    customer_notes TEXT,
    admin_notes TEXT,
    
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW() ON UPDATE NOW(),
    
    INDEX idx_user_orders (user_id, status),
    INDEX idx_order_status (status, created_at),
    INDEX idx_payment_status (payment_status)
);

-- Chi tiết các item trong đơn hàng
CREATE TABLE order_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT REFERENCES orders(id),
    product_id BIGINT REFERENCES technical_products(id),
    
    -- Item Details
    product_title VARCHAR(255) NOT NULL, -- Snapshot at time of purchase
    product_price DECIMAL(10,2) NOT NULL,
    license_type ENUM('single_use', 'commercial', 'educational', 'unlimited'),
    quantity INT DEFAULT 1,
    
    -- Calculations
    item_total DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) NOT NULL,
    seller_revenue DECIMAL(10,2) NOT NULL,
    
    -- Product Snapshot (for historical record)
    product_snapshot JSON, -- Store product details at purchase time
    
    created_at TIMESTAMP DEFAULT NOW(),
    
    INDEX idx_order_products (order_id, product_id)
);
```

#### **Refunds & Returns Management**
```sql
-- Quản lý hoàn tiền và tranh chấp
CREATE TABLE refund_requests (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    purchase_id BIGINT REFERENCES product_purchases(id),
    order_id BIGINT REFERENCES orders(id) NULL,
    user_id BIGINT REFERENCES users(id),
    
    -- Refund Details
    refund_amount DECIMAL(10,2) NOT NULL,
    refund_reason ENUM('defective_file', 'wrong_product', 'duplicate_purchase', 'quality_issues', 'other'),
    reason_description TEXT NOT NULL,
    
    -- Status & Processing
    status ENUM('pending', 'approved', 'rejected', 'processed', 'completed'),
    admin_id BIGINT REFERENCES users(id) NULL, -- Admin who processed
    admin_notes TEXT,
    
    -- Evidence
    evidence_files JSON, -- Screenshots, documentation
    
    -- Refund Processing
    refund_transaction_id VARCHAR(255) NULL,
    refunded_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW() ON UPDATE NOW(),
    
    INDEX idx_user_refunds (user_id, status),
    INDEX idx_purchase_refunds (purchase_id)
);
```

#### **User Payment Methods**
```sql
-- Phương thức thanh toán đã lưu của user
CREATE TABLE user_payment_methods (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT REFERENCES users(id),
    
    -- Payment Method Details
    type ENUM('card', 'bank_account', 'paypal', 'ewallet'),
    gateway VARCHAR(50), -- "stripe", "paypal", "vnpay"
    gateway_method_id VARCHAR(255), -- Gateway's payment method ID
    
    -- Display Information
    display_name VARCHAR(100), -- "Visa ****1234", "PayPal (email@example.com)"
    is_default BOOLEAN DEFAULT FALSE,
    
    -- Card-specific (if applicable)
    card_brand VARCHAR(20) NULL, -- "visa", "mastercard", "amex"
    card_last4 VARCHAR(4) NULL,
    card_exp_month INT NULL,
    card_exp_year INT NULL,
    
    -- Status
    is_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW() ON UPDATE NOW(),
    
    INDEX idx_user_methods (user_id, is_active),
    INDEX idx_default_method (user_id, is_default)
);
```

#### **Seller Analytics & Earnings**
```sql
-- Thống kê thu nhập của seller
CREATE TABLE seller_earnings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    seller_id BIGINT REFERENCES users(id),
    product_id BIGINT REFERENCES technical_products(id),
    purchase_id BIGINT REFERENCES product_purchases(id),
    
    -- Earning Details
    gross_amount DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) NOT NULL,
    net_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    
    -- Payout Status
    payout_status ENUM('pending', 'available', 'paid', 'on_hold'),
    payout_date DATE NULL,
    payout_reference VARCHAR(255) NULL,
    
    -- Period Tracking
    earning_date DATE NOT NULL,
    earning_month VARCHAR(7), -- "2024-01" for grouping
    
    created_at TIMESTAMP DEFAULT NOW(),
    
    INDEX idx_seller_earnings (seller_id, earning_date),
    INDEX idx_payout_status (payout_status, payout_date),
    INDEX idx_monthly_earnings (seller_id, earning_month)
);

-- Lịch sử chi trả cho seller
CREATE TABLE seller_payouts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    seller_id BIGINT REFERENCES users(id),
    
    -- Payout Details
    payout_amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    payout_method VARCHAR(50), -- "bank_transfer", "paypal", "stripe"
    
    -- Period Covered
    period_from DATE NOT NULL,
    period_to DATE NOT NULL,
    
    -- Status & Processing
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled'),
    gateway_payout_id VARCHAR(255) NULL,
    failure_reason TEXT NULL,
    
    -- Bank Details (encrypted)
    bank_details JSON NULL,
    
    -- Timestamps
    initiated_at TIMESTAMP DEFAULT NOW(),
    completed_at TIMESTAMP NULL,
    
    INDEX idx_seller_payouts (seller_id, status),
    INDEX idx_payout_period (period_from, period_to)
);
```

#### **Notifications & Communications**
```sql
-- Thông báo marketplace-specific
CREATE TABLE marketplace_notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT REFERENCES users(id),
    
    -- Notification Details
    type ENUM('purchase_confirmed', 'file_downloaded', 'sale_notification', 'payout_processed', 'refund_approved'),
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    
    -- Related Entities
    related_type VARCHAR(50), -- "purchase", "product", "payout"
    related_id BIGINT,
    
    -- Action
    action_url VARCHAR(500) NULL,
    action_text VARCHAR(100) NULL,
    
    -- Status
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT NOW(),
    
    INDEX idx_user_notifications (user_id, is_read),
    INDEX idx_notification_type (type, created_at)
);
```

#### **Anti-Fraud & Security**
```sql
-- Phát hiện và ngăn chặn gian lận
CREATE TABLE fraud_detection_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT REFERENCES users(id) NULL,
    
    -- Event Details
    event_type ENUM('suspicious_download', 'multiple_accounts', 'payment_fraud', 'file_sharing'),
    risk_score INT, -- 0-100
    details JSON,
    
    -- Detection Method
    detection_method VARCHAR(100), -- "ip_analysis", "device_fingerprint", "behavior_pattern"
    
    -- Action Taken
    action_taken ENUM('none', 'flag_review', 'suspend_user', 'block_ip', 'require_verification'),
    admin_reviewed BOOLEAN DEFAULT FALSE,
    admin_id BIGINT REFERENCES users(id) NULL,
    
    -- Context
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    created_at TIMESTAMP DEFAULT NOW(),
    
    INDEX idx_user_fraud (user_id, event_type),
    INDEX idx_risk_score (risk_score, created_at)
);

-- Tracking thiết bị và IP để phát hiện tài khoản clone
CREATE TABLE device_fingerprints (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT REFERENCES users(id),
    
    -- Device Information
    fingerprint_hash VARCHAR(128) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    browser_fingerprint JSON, -- Canvas, WebGL, fonts, etc.
    
    -- Usage Tracking
    first_seen_at TIMESTAMP DEFAULT NOW(),
    last_seen_at TIMESTAMP DEFAULT NOW(),
    access_count INT DEFAULT 1,
    
    -- Flags
    is_suspicious BOOLEAN DEFAULT FALSE,
    is_blocked BOOLEAN DEFAULT FALSE,
    
    INDEX idx_user_devices (user_id, last_seen_at),
    INDEX idx_fingerprint (fingerprint_hash),
    INDEX idx_suspicious (is_suspicious, is_blocked)
);
```

### **2. Security Architecture**

#### **File Encryption System**
```php
// app/Services/FileEncryptionService.php
class FileEncryptionService 
{
    private string $encryptionKey;
    private string $encryptionMethod = 'AES-256-CBC';
    
    public function encryptFile(UploadedFile $file, int $productId): array
    {
        // Generate unique encryption key for this file
        $fileKey = $this->generateFileKey($productId, $file->getClientOriginalName());
        
        // Encrypt file content
        $encryptedContent = openssl_encrypt(
            file_get_contents($file->getPathname()),
            $this->encryptionMethod,
            $fileKey,
            OPENSSL_RAW_DATA,
            $iv = openssl_random_pseudo_bytes(16)
        );
        
        // Generate secure filename
        $encryptedFilename = hash('sha256', $fileKey . time()) . '.enc';
        
        // Store encrypted file
        $storagePath = "protected/{$productId}/{$encryptedFilename}";
        Storage::disk('secure')->put($storagePath, $iv . $encryptedContent);
        
        return [
            'encrypted_filename' => $encryptedFilename,
            'file_path' => $storagePath,
            'encryption_key' => base64_encode($fileKey),
            'file_hash' => hash('sha256', $encryptedContent)
        ];
    }
    
    public function decryptFile(ProtectedFile $file, string $downloadToken): string
    {
        // Verify download permission
        $this->verifyDownloadPermission($file, $downloadToken);
        
        // Get encrypted content
        $encryptedData = Storage::disk('secure')->get($file->file_path);
        $iv = substr($encryptedData, 0, 16);
        $encryptedContent = substr($encryptedData, 16);
        
        // Decrypt with file-specific key
        $decryptedContent = openssl_decrypt(
            $encryptedContent,
            $this->encryptionMethod,
            base64_decode($file->encryption_key),
            OPENSSL_RAW_DATA,
            $iv
        );
        
        // Verify file integrity
        if (hash('sha256', $encryptedContent) !== $file->file_hash) {
            throw new FileCorruptedException('File integrity check failed');
        }
        
        return $decryptedContent;
    }
    
    private function generateFileKey(int $productId, string $filename): string
    {
        return hash('sha256', config('app.key') . $productId . $filename . time());
    }
}
```

#### **Download Link Security**
```php
// app/Services/SecureDownloadService.php
class SecureDownloadService
{
    public function generateDownloadLink(ProductPurchase $purchase, ProtectedFile $file): string
    {
        // Verify purchase is valid and not expired
        $this->verifyPurchaseAccess($purchase, $file);
        
        // Generate unique download token
        $downloadToken = $this->generateSecureToken($purchase, $file);
        
        // Create secure download record
        $secureDownload = SecureDownload::create([
            'purchase_id' => $purchase->id,
            'protected_file_id' => $file->id,
            'user_id' => $purchase->buyer_id,
            'download_token' => $downloadToken,
            'download_url' => route('secure.download', ['token' => $downloadToken]),
            'expires_at' => now()->addHours(24) // 24-hour expiration
        ]);
        
        return $secureDownload->download_url;
    }
    
    public function processSecureDownload(string $token): StreamedResponse
    {
        $download = SecureDownload::where('download_token', $token)
            ->where('expires_at', '>', now())
            ->where('is_completed', false)
            ->firstOrFail();
            
        // Verify user authentication
        if (auth()->id() !== $download->user_id) {
            abort(403, 'Unauthorized download attempt');
        }
        
        // Get protected file
        $protectedFile = $download->protectedFile;
        
        // Decrypt file content
        $fileContent = app(FileEncryptionService::class)
            ->decryptFile($protectedFile, $token);
        
        // Update download tracking
        $download->update([
            'downloaded_at' => now(),
            'download_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'download_size' => strlen($fileContent),
            'download_duration_seconds' => 0, // To be calculated
            'is_completed' => true
        ]);
        
        // Increment download counters
        $download->purchase->increment('download_count');
        $protectedFile->increment('download_count');
        
        // Stream file with proper headers
        return response()->streamDownload(
            function() use ($fileContent) {
                echo $fileContent;
            },
            $protectedFile->original_filename,
            [
                'Content-Type' => $protectedFile->mime_type,
                'Content-Length' => strlen($fileContent),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]
        );
    }
    
    private function generateSecureToken(ProductPurchase $purchase, ProtectedFile $file): string
    {
        return hash('sha256', implode('|', [
            $purchase->id,
            $file->id,
            $purchase->buyer_id,
            time(),
            Str::random(32)
        ]));
    }
}
```

### **3. Payment Integration**

#### **Payment Gateway Service**
```php
// app/Services/PaymentGatewayService.php
class PaymentGatewayService
{
    public function createPaymentIntent(TechnicalProduct $product, User $buyer): PaymentIntent
    {
        // Calculate fees
        $productPrice = $product->sale_price;
        $platformFee = $productPrice * (config('marketplace.commission_rate') / 100);
        $sellerRevenue = $productPrice - $platformFee;
        
        // Create payment intent with Stripe
        $paymentIntent = $this->stripeClient->paymentIntents->create([
            'amount' => $productPrice * 100, // Convert to cents
            'currency' => $product->currency,
            'metadata' => [
                'product_id' => $product->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $product->seller_id,
                'platform_fee' => $platformFee,
                'seller_revenue' => $sellerRevenue
            ],
            'description' => "Purchase: {$product->title}",
            'receipt_email' => $buyer->email
        ]);
        
        return $paymentIntent;
    }
    
    public function handlePaymentSuccess(string $paymentIntentId): ProductPurchase
    {
        $paymentIntent = $this->stripeClient->paymentIntents->retrieve($paymentIntentId);
        $metadata = $paymentIntent->metadata;
        
        DB::beginTransaction();
        try {
            // Create purchase record
            $purchase = ProductPurchase::create([
                'product_id' => $metadata['product_id'],
                'buyer_id' => $metadata['buyer_id'],
                'seller_id' => $metadata['seller_id'],
                'purchase_token' => $this->generatePurchaseToken(),
                'amount_paid' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'platform_fee' => $metadata['platform_fee'],
                'seller_revenue' => $metadata['seller_revenue'],
                'payment_id' => $paymentIntentId,
                'payment_status' => 'completed',
                'payment_gateway' => 'stripe',
                'license_key' => $this->generateLicenseKey(),
                'download_token' => $this->generateDownloadToken(),
                'status' => 'active'
            ]);
            
            // Update product statistics
            $product = TechnicalProduct::find($metadata['product_id']);
            $product->increment('sales_count');
            $product->increment('total_revenue', $metadata['seller_revenue']);
            
            // Update seller earnings
            User::find($metadata['seller_id'])
                ->increment('total_earnings', $metadata['seller_revenue']);
            
            // Send confirmation emails
            $this->sendPurchaseConfirmation($purchase);
            $this->sendSellerNotification($purchase);
            
            DB::commit();
            return $purchase;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    private function generatePurchaseToken(): string
    {
        return 'purchase_' . Str::random(32) . '_' . time();
    }
    
    private function generateLicenseKey(): string
    {
        return 'lic_' . Str::upper(Str::random(4)) . '-' . 
               Str::upper(Str::random(4)) . '-' . 
               Str::upper(Str::random(4)) . '-' . 
               Str::upper(Str::random(4));
    }
}
```

### **4. API Endpoints**

#### **Product Management API**
```php
// routes/api.php - Marketplace API Routes
Route::prefix('marketplace')->middleware('auth:sanctum')->group(function () {
    
    // Product browsing (public)
    Route::get('/products', [MarketplaceController::class, 'index']);
    Route::get('/products/{slug}', [MarketplaceController::class, 'show']);
    Route::get('/categories', [MarketplaceController::class, 'categories']);
    Route::get('/search', [MarketplaceController::class, 'search']);
    
    // Seller management
    Route::prefix('seller')->group(function () {
        Route::get('/products', [SellerController::class, 'myProducts']);
        Route::post('/products', [SellerController::class, 'createProduct']);
        Route::put('/products/{id}', [SellerController::class, 'updateProduct']);
        Route::delete('/products/{id}', [SellerController::class, 'deleteProduct']);
        Route::post('/products/{id}/files', [SellerController::class, 'uploadFiles']);
        Route::get('/earnings', [SellerController::class, 'earnings']);
        Route::get('/analytics', [SellerController::class, 'analytics']);
    });
    
    // Purchase flow
    Route::post('/purchase/intent', [PurchaseController::class, 'createIntent']);
    Route::post('/purchase/confirm', [PurchaseController::class, 'confirmPayment']);
    Route::get('/purchases', [PurchaseController::class, 'myPurchases']);
    Route::get('/purchases/{id}/downloads', [PurchaseController::class, 'downloadLinks']);
    
    // Secure downloads
    Route::get('/download/{token}', [SecureDownloadController::class, 'download'])
        ->name('secure.download');
});
```

#### **Marketplace Controller**
```php
// app/Http/Controllers/Api/MarketplaceController.php
class MarketplaceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TechnicalProduct::where('status', 'approved')
            ->with(['seller', 'category', 'previewImages']);
        
        // Filtering
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('price_min')) {
            $query->where('sale_price', '>=', $request->price_min);
        }
        
        if ($request->has('price_max')) {
            $query->where('sale_price', '<=', $request->price_max);
        }
        
        if ($request->has('software')) {
            $query->whereJsonContains('software_compatibility', $request->software);
        }
        
        if ($request->has('complexity')) {
            $query->where('complexity_level', $request->complexity);
        }
        
        // Sorting
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderBy('sale_price', $sortOrder);
                break;
            case 'popularity':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_average', 'desc');
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }
        
        $products = $query->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'filters' => $this->getAvailableFilters()
        ]);
    }
    
    public function show(string $slug): JsonResponse
    {
        $product = TechnicalProduct::where('slug', $slug)
            ->where('status', 'approved')
            ->with([
                'seller',
                'category',
                'previewImages',
                'sampleFiles',
                'reviews' => function($query) {
                    $query->where('is_approved', true)
                          ->with('buyer')
                          ->latest()
                          ->limit(10);
                }
            ])
            ->firstOrFail();
        
        // Increment view count
        $product->increment('view_count');
        
        // Check if current user already purchased
        $hasPurchased = false;
        if (auth()->check()) {
            $hasPurchased = ProductPurchase::where('product_id', $product->id)
                ->where('buyer_id', auth()->id())
                ->where('status', 'active')
                ->exists();
        }
        
        return response()->json([
            'success' => true,
            'data' => $product,
            'has_purchased' => $hasPurchased,
            'related_products' => $this->getRelatedProducts($product)
        ]);
    }
    
    private function getRelatedProducts(TechnicalProduct $product): Collection
    {
        return TechnicalProduct::where('status', 'approved')
            ->where('id', '!=', $product->id)
            ->where(function($query) use ($product) {
                $query->where('category_id', $product->category_id)
                      ->orWhereJsonOverlaps('tags', $product->tags)
                      ->orWhere('complexity_level', $product->complexity_level);
            })
            ->orderBy('rating_average', 'desc')
            ->limit(6)
            ->get();
    }
}
```

---

## 💰 **REVENUE MODEL**

### **Commission Structure**
```php
// config/marketplace.php
return [
    'commission' => [
        'default_rate' => 15.0, // 15% platform fee
        'new_seller_rate' => 10.0, // Reduced rate for first 30 days
        'premium_seller_rate' => 12.0, // Reduced rate for verified sellers
        'volume_discounts' => [
            'tier_1' => ['min_sales' => 50, 'rate' => 13.0],
            'tier_2' => ['min_sales' => 100, 'rate' => 11.0],
            'tier_3' => ['min_sales' => 250, 'rate' => 9.0],
        ]
    ],
    
    'pricing' => [
        'minimum_price' => 5.00,
        'maximum_price' => 10000.00,
        'currency' => 'USD',
        'supported_currencies' => ['USD', 'EUR', 'VND']
    ],
    
    'payouts' => [
        'minimum_threshold' => 50.00,
        'payout_schedule' => 'weekly', // weekly, monthly
        'payout_day' => 'friday'
    ]
];
```

### **Seller Dashboard Analytics**
```php
// app/Http/Controllers/Api/SellerController.php
public function analytics(Request $request): JsonResponse
{
    $sellerId = auth()->id();
    $period = $request->input('period', '30days'); // 7days, 30days, 3months, 1year
    
    $startDate = match($period) {
        '7days' => now()->subWeek(),
        '30days' => now()->subMonth(),
        '3months' => now()->subMonths(3),
        '1year' => now()->subYear(),
        default => now()->subMonth()
    };
    
    // Sales analytics
    $salesData = ProductPurchase::where('seller_id', $sellerId)
        ->where('payment_status', 'completed')
        ->where('created_at', '>=', $startDate)
        ->selectRaw('
            DATE(created_at) as date,
            COUNT(*) as sales_count,
            SUM(seller_revenue) as revenue,
            COUNT(DISTINCT buyer_id) as unique_buyers
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    // Product performance
    $productPerformance = TechnicalProduct::where('seller_id', $sellerId)
        ->withCount(['purchases' => function($query) use ($startDate) {
            $query->where('payment_status', 'completed')
                  ->where('created_at', '>=', $startDate);
        }])
        ->withSum(['purchases' => function($query) use ($startDate) {
            $query->where('payment_status', 'completed')
                  ->where('created_at', '>=', $startDate);
        }], 'seller_revenue')
        ->get();
    
    // Download analytics
    $downloadData = SecureDownload::whereHas('purchase', function($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })
        ->where('downloaded_at', '>=', $startDate)
        ->selectRaw('
            DATE(downloaded_at) as date,
            COUNT(*) as download_count,
            COUNT(DISTINCT user_id) as unique_downloaders
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => [
            'period' => $period,
            'sales' => $salesData,
            'products' => $productPerformance,
            'downloads' => $downloadData,
            'summary' => [
                'total_revenue' => $salesData->sum('revenue'),
                'total_sales' => $salesData->sum('sales_count'),
                'total_downloads' => $downloadData->sum('download_count'),
                'conversion_rate' => $this->calculateConversionRate($sellerId, $startDate)
            ]
        ]
    ]);
}
```

---

## 🔐 **SECURITY MEASURES**

### **Multi-Layer Security**
1. **File Encryption**: AES-256-CBC encryption for all protected files
2. **Access Tokens**: Time-limited, user-specific download tokens
3. **IP Tracking**: Monitor download patterns for abuse detection
4. **License Verification**: Validate purchase before each download
5. **DRM Protection**: Optional watermarking for sensitive documents

### **Anti-Piracy Features**
```php
// app/Services/AntiPiracyService.php
class AntiPiracyService
{
    public function addWatermark(string $fileContent, ProductPurchase $purchase): string
    {
        $watermarkData = [
            'buyer' => $purchase->buyer->email,
            'license' => $purchase->license_key,
            'purchase_date' => $purchase->created_at->format('Y-m-d'),
            'unique_id' => $purchase->purchase_token
        ];
        
        // Add invisible watermark to PDF files
        if (Str::endsWith($purchase->protectedFile->original_filename, '.pdf')) {
            return $this->addPdfWatermark($fileContent, $watermarkData);
        }
        
        // Add metadata to CAD files
        if (in_array($purchase->protectedFile->mime_type, ['application/dwg', 'application/step'])) {
            return $this->addCadMetadata($fileContent, $watermarkData);
        }
        
        return $fileContent;
    }
    
    public function detectSuspiciousActivity(User $user): bool
    {
        // Check for unusual download patterns
        $recentDownloads = SecureDownload::where('user_id', $user->id)
            ->where('downloaded_at', '>', now()->subHours(24))
            ->count();
            
        // Flag if too many downloads in short time
        if ($recentDownloads > 50) {
            $this->flagUser($user, 'excessive_downloads');
            return true;
        }
        
        // Check IP consistency
        $uniqueIps = SecureDownload::where('user_id', $user->id)
            ->where('downloaded_at', '>', now()->subDays(7))
            ->distinct('download_ip')
            ->count();
            
        if ($uniqueIps > 10) {
            $this->flagUser($user, 'multiple_ip_addresses');
            return true;
        }
        
        return false;
    }
}
```

---

## 🎨 **USER EXPERIENCE FLOW**

### **Buyer Journey**
```
1. Browse Products → 2. View Details → 3. Add to Cart → 4. Checkout
   ↓                   ↓                ↓               ↓
5. Payment → 6. Confirmation → 7. Access Downloads → 8. Download Files
```

### **Seller Journey**
```
1. Create Product → 2. Upload Files → 3. Set Pricing → 4. Submit for Review
   ↓                 ↓                ↓                ↓
5. Approval → 6. Go Live → 7. Monitor Sales → 8. Receive Payouts
```

### **Frontend Integration**
```typescript
// marketplace/types.ts
interface TechnicalProduct {
  id: number;
  title: string;
  slug: string;
  description: string;
  price: number;
  salePrice: number;
  currency: string;
  category: ProductCategory;
  seller: User;
  previewImages: string[];
  sampleFiles: File[];
  softwareCompatibility: Record<string, string>;
  complexityLevel: 'beginner' | 'intermediate' | 'advanced';
  rating: {
    average: number;
    count: number;
  };
  salesCount: number;
  tags: string[];
}

interface ProductPurchase {
  id: number;
  product: TechnicalProduct;
  purchaseToken: string;
  licenseKey: string;
  downloadLimit: number;
  downloadCount: number;
  status: 'active' | 'expired' | 'revoked';
  expiresAt: string | null;
  purchasedAt: string;
}
```

---

## 📈 **IMPLEMENTATION ROADMAP**

### **Phase 1: Foundation (4 weeks)**
- [ ] Database schema implementation
- [ ] Basic product CRUD operations
- [ ] File encryption system
- [ ] User authentication extension

### **Phase 2: Core Features (6 weeks)**
- [ ] Payment gateway integration
- [ ] Secure download system
- [ ] Purchase management
- [ ] Basic seller dashboard

### **Phase 3: Advanced Features (4 weeks)**
- [ ] Analytics and reporting
- [ ] Review and rating system
- [ ] Anti-piracy measures
- [ ] Admin moderation tools

### **Phase 4: Polish & Launch (2 weeks)**
- [ ] Frontend integration
- [ ] Testing and optimization
- [ ] Documentation
- [ ] Production deployment

---

## 🎯 **SUCCESS METRICS**

### **Business KPIs**
- Monthly Recurring Revenue (MRR)
- Average Order Value (AOV)
- Seller retention rate
- Buyer conversion rate
- Platform commission earnings

### **Technical KPIs**
- Download success rate (>99%)
- Payment processing time (<3 seconds)
- File security incidents (0)
- System uptime (99.9%)
- API response time (<200ms)

---

## 📚 **CONCLUSION**

Hệ thống Technical Marketplace này sẽ biến MechaMap thành một nền tảng toàn diện cho cộng đồng kỹ sư cơ khí, không chỉ để thảo luận mà còn để kinh doanh tài liệu kỹ thuật một cách chuyên nghiệp và bảo mật.

**Lợi ích chính:**
- Tạo thu nhập cho cộng đồng kỹ sư
- Khuyến khích chia sẻ kiến thức chất lượng cao
- Xây dựng hệ sinh thái technical content phong phú
- Tăng giá trị và tính bền vững của platform

**Competitive Advantages:**
- Chuyên biệt cho engineering content
- Bảo mật cao với encryption
- Revenue sharing hấp dẫn
- Integration sâu với forum community

---

*Tài liệu này sẽ được cập nhật theo tiến độ phát triển.*
