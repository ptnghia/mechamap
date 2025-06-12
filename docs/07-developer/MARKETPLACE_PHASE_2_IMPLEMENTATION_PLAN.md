# 💰 **MARKETPLACE PHASE 2: PAYMENT INTEGRATION**

> **Phase**: 2 of 4  
> **Focus**: Payment Systems & Purchase Flow  
> **Timeline**: June 13-20, 2025 (1 week)  
> **Prerequisites**: ✅ Phase 1 Complete

---

## 🎯 **PHASE 2 OBJECTIVES**

Transform the marketplace from a catalog system into a fully functional e-commerce platform with secure payment processing and purchase management.

### **Key Goals**
- 💳 **Payment Gateway Integration** (Stripe + VNPay)
- 🛒 **Shopping Cart System** 
- 💰 **Purchase Flow Implementation**
- 🔐 **License Management System**
- 📊 **Seller Revenue Tracking**

---

## 🗓️ **IMPLEMENTATION SCHEDULE**

### **Day 1: Payment Gateway Setup**
#### Morning (2-3 hours)
- [ ] Install Stripe PHP SDK
- [ ] Configure Stripe API keys in environment
- [ ] Create Stripe payment service class
- [ ] Test basic payment intent creation

#### Afternoon (3-4 hours)  
- [ ] VNPay integration setup
- [ ] Vietnamese payment gateway configuration
- [ ] Currency conversion utilities (USD ↔ VND)
- [ ] Test payment gateway switching

#### Files to Create:
```
app/Services/PaymentGateway/
├── StripeService.php
├── VNPayService.php
├── PaymentGatewayInterface.php
└── CurrencyConverter.php

config/payment.php
```

### **Day 2: Database Extensions**
#### Morning (2-3 hours)
- [ ] Create shopping cart migrations
- [ ] Create order system migrations  
- [ ] Payment transaction tracking
- [ ] User payment methods storage

#### Afternoon (3-4 hours)
- [ ] Cart and Order models
- [ ] Payment transaction models
- [ ] Model relationships setup
- [ ] Database seeding for test data

#### Files to Create:
```
database/migrations/
├── create_shopping_carts_table.php
├── create_orders_table.php
├── create_order_items_table.php
├── create_payment_transactions_table.php
└── create_user_payment_methods_table.php

app/Models/
├── ShoppingCart.php
├── Order.php
├── OrderItem.php
├── PaymentTransaction.php
└── UserPaymentMethod.php
```

### **Day 3: Purchase Flow API**
#### Morning (2-3 hours)
- [ ] Cart management endpoints
- [ ] Add/remove items from cart
- [ ] Cart total calculations
- [ ] Cart persistence and validation

#### Afternoon (3-4 hours)
- [ ] Checkout process API
- [ ] Payment intent creation
- [ ] Order creation from cart
- [ ] Purchase confirmation handling

#### Files to Create:
```
app/Http/Controllers/Api/
├── CartController.php
├── CheckoutController.php
└── OrderController.php

app/Http/Requests/
├── AddToCartRequest.php
├── CheckoutRequest.php
└── ProcessPaymentRequest.php
```

### **Day 4: License & Download System**
#### Morning (2-3 hours)
- [ ] License key generation system
- [ ] Download token creation
- [ ] Access validation middleware
- [ ] Download limits enforcement

#### Afternoon (3-4 hours)
- [ ] Secure file streaming
- [ ] Download tracking and analytics
- [ ] License expiration handling
- [ ] Anti-abuse measures

#### Files to Create:
```
app/Services/
├── LicenseManager.php
├── DownloadManager.php
└── FileStreamingService.php

app/Http/Middleware/
├── ValidatePurchaseAccess.php
└── TrackDownload.php

routes/downloads.php
```

### **Day 5: Seller Dashboard Backend**
#### Morning (2-3 hours)
- [ ] Seller earnings calculations
- [ ] Sales analytics aggregation
- [ ] Revenue tracking system
- [ ] Payout management preparation

#### Afternoon (3-4 hours)
- [ ] Seller dashboard APIs
- [ ] Product performance metrics
- [ ] Sales reporting endpoints
- [ ] Seller profile management

#### Files to Create:
```
app/Http/Controllers/Api/Seller/
├── DashboardController.php
├── AnalyticsController.php
├── EarningsController.php
└── ProductManagementController.php

app/Services/
├── SellerAnalyticsService.php
└── EarningsCalculator.php
```

### **Day 6: Payment Webhooks & Security**
#### Morning (2-3 hours)
- [ ] Stripe webhook handling
- [ ] VNPay callback processing
- [ ] Payment status updates
- [ ] Failed payment handling

#### Afternoon (3-4 hours)
- [ ] Fraud detection basics
- [ ] Security hardening
- [ ] Rate limiting implementation
- [ ] Payment logging and auditing

#### Files to Create:
```
app/Http/Controllers/Webhooks/
├── StripeWebhookController.php
└── VNPayCallbackController.php

app/Services/
├── FraudDetectionService.php
└── PaymentAuditService.php

app/Http/Middleware/
├── VerifyStripeWebhook.php
└── PaymentRateLimit.php
```

### **Day 7: Testing & Integration**
#### Morning (2-3 hours)
- [ ] End-to-end purchase testing
- [ ] Payment flow verification
- [ ] Error scenario testing
- [ ] Performance optimization

#### Afternoon (3-4 hours)
- [ ] API documentation updates
- [ ] Integration with existing frontend
- [ ] Load testing preparations
- [ ] Phase 2 completion report

#### Files to Create:
```
tests/Feature/
├── PurchaseFlowTest.php
├── PaymentIntegrationTest.php
└── SellerDashboardTest.php

docs/api/
├── payment-endpoints.md
├── seller-dashboard-api.md
└── purchase-flow-guide.md
```

---

## 🛠️ **TECHNICAL SPECIFICATIONS**

### **Payment Gateway Integration**

#### Stripe Configuration
```php
// config/payment.php
'stripe' => [
    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'currencies' => ['USD', 'VND'],
    'minimum_amount' => 500, // 5 USD cents
],
```

#### VNPay Configuration
```php
'vnpay' => [
    'tmn_code' => env('VNPAY_TMN_CODE'),
    'hash_secret' => env('VNPAY_HASH_SECRET'),
    'url' => env('VNPAY_URL'),
    'return_url' => env('VNPAY_RETURN_URL'),
    'currency' => 'VND',
    'minimum_amount' => 10000, // 10,000 VND
],
```

### **Database Schema Extensions**

#### Shopping Carts
```sql
CREATE TABLE shopping_carts (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    quantity INT DEFAULT 1,
    price_at_add DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'VND',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES technical_products(id),
    UNIQUE KEY unique_user_product (user_id, product_id)
);
```

#### Orders System
```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    order_number VARCHAR(32) UNIQUE,
    status ENUM('pending', 'processing', 'completed', 'failed', 'refunded'),
    subtotal DECIMAL(12,2),
    tax_amount DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(12,2),
    currency VARCHAR(3) DEFAULT 'VND',
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'processing', 'completed', 'failed'),
    payment_gateway VARCHAR(20),
    gateway_transaction_id VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE order_items (
    id BIGINT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2),
    total_price DECIMAL(10,2),
    license_key VARCHAR(128),
    download_limit INT DEFAULT 5,
    created_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES technical_products(id)
);
```

### **API Endpoint Structure**

#### Cart Management
```http
POST   /api/v1/cart/add
GET    /api/v1/cart
PUT    /api/v1/cart/{productId}
DELETE /api/v1/cart/{productId}
DELETE /api/v1/cart/clear
```

#### Checkout Process
```http
POST   /api/v1/checkout/validate
POST   /api/v1/checkout/payment-intent
POST   /api/v1/checkout/process
GET    /api/v1/checkout/status/{orderId}
```

#### Order Management
```http
GET    /api/v1/orders
GET    /api/v1/orders/{orderId}
GET    /api/v1/orders/{orderId}/downloads
POST   /api/v1/orders/{orderId}/download/{fileId}
```

#### Seller Dashboard
```http
GET    /api/v1/seller/dashboard
GET    /api/v1/seller/analytics
GET    /api/v1/seller/earnings
GET    /api/v1/seller/products
POST   /api/v1/seller/products
PUT    /api/v1/seller/products/{productId}
```

---

## 🔒 **SECURITY CONSIDERATIONS**

### **Payment Security**
- [ ] PCI DSS compliance preparation
- [ ] Secure payment token handling
- [ ] Webhook signature verification
- [ ] Rate limiting on payment endpoints
- [ ] Fraud detection integration

### **Download Security**
- [ ] Time-limited download URLs
- [ ] IP-based download restrictions
- [ ] Download attempt logging
- [ ] File integrity verification
- [ ] License violation detection

### **Anti-Fraud Measures**
- [ ] User behavior analysis
- [ ] Payment pattern detection
- [ ] Geographic restriction options
- [ ] Velocity checking
- [ ] Manual review triggers

---

## 📊 **SUCCESS METRICS**

### **Technical KPIs**
- [ ] Payment success rate > 99%
- [ ] Cart abandonment rate < 30%
- [ ] Download success rate > 99.5%
- [ ] API response time < 300ms
- [ ] Zero payment security incidents

### **Business KPIs**
- [ ] Complete purchase flow functional
- [ ] Multiple payment methods working
- [ ] Seller dashboard operational
- [ ] License management working
- [ ] Revenue tracking accurate

### **User Experience KPIs**
- [ ] Checkout completion rate > 80%
- [ ] Payment error rate < 1%
- [ ] Download completion rate > 95%
- [ ] Seller satisfaction > 4.5/5
- [ ] Customer support tickets < 5%

---

## 🎯 **DELIVERABLES**

### **Core Components**
1. ✅ **Payment Gateway Integration**
   - Stripe and VNPay working
   - Currency conversion
   - Payment processing

2. ✅ **Shopping & Checkout System**
   - Cart management
   - Checkout flow
   - Order processing

3. ✅ **License Management**
   - Key generation
   - Download controls
   - Access validation

4. ✅ **Seller Tools**
   - Dashboard APIs
   - Analytics system
   - Earnings tracking

5. ✅ **Security Framework**
   - Payment security
   - Download protection
   - Fraud detection

### **Documentation**
- [ ] Payment integration guide
- [ ] Seller dashboard documentation
- [ ] API endpoint specifications
- [ ] Security implementation notes
- [ ] Testing procedures

---

## 🚀 **PHASE 3 PREPARATION**

### **Phase 3 Preview: Advanced Features**
- **Review & Rating System**
- **Advanced Analytics & Reporting**
- **Anti-Piracy Measures**
- **Admin Moderation Tools**
- **Recommendation Engine**

### **Technical Dependencies**
- Complete payment flow testing
- Seller dashboard functionality
- License management verification
- Security audit completion
- Performance optimization

---

*Phase 2 Implementation Plan*  
*Target Completion: June 20, 2025*  
*Next Phase: Advanced Features & Admin Tools*
