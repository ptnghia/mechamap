# 🚀 **PHASE 2 QUICK START GUIDE**

> **Ready to begin Payment Integration**  
> **Prerequisites**: ✅ Phase 1 Complete  
> **Estimated Time**: 1 week

---

## 🎯 **IMMEDIATE NEXT STEPS**

### **1. Environment Setup (5 minutes)**
```bash
# Add payment gateway keys to .env
echo "" >> .env
echo "# Payment Gateway Configuration" >> .env
echo "STRIPE_PUBLIC_KEY=pk_test_..." >> .env
echo "STRIPE_SECRET_KEY=sk_test_..." >> .env
echo "STRIPE_WEBHOOK_SECRET=whsec_..." >> .env
echo "VNPAY_TMN_CODE=your_tmn_code" >> .env
echo "VNPAY_HASH_SECRET=your_hash_secret" >> .env
echo "VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html" >> .env
```

### **2. Install Payment Dependencies (2 minutes)**
```bash
# Install Stripe PHP SDK
composer require stripe/stripe-php

# Install VNPay package (if available) or create custom integration
composer require omnipay/omnipay vnpay/vnpay-php
```

### **3. Create Directory Structure (1 minute)**
```bash
# Create payment service directories
mkdir -p app/Services/PaymentGateway
mkdir -p app/Http/Controllers/Api/Payment
mkdir -p app/Http/Controllers/Webhooks
mkdir -p database/migrations/payment
```

### **4. Start with Payment Configuration (15 minutes)**
Create `config/payment.php`:
```php
<?php
return [
    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'stripe'),
    'currency' => env('PAYMENT_DEFAULT_CURRENCY', 'VND'),
    
    'gateways' => [
        'stripe' => [
            'public_key' => env('STRIPE_PUBLIC_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'currencies' => ['USD', 'VND'],
        ],
        'vnpay' => [
            'tmn_code' => env('VNPAY_TMN_CODE'),
            'hash_secret' => env('VNPAY_HASH_SECRET'),
            'url' => env('VNPAY_URL'),
            'return_url' => env('APP_URL') . '/payment/vnpay/return',
        ],
    ],
    
    'currency_rates' => [
        'USD_TO_VND' => 24000, // Update with real rates
        'VND_TO_USD' => 0.0000417,
    ],
];
```

---

## 📋 **DAY 1 CHECKLIST**

### **Morning Tasks (3 hours)**
- [ ] Create `PaymentGatewayInterface.php`
- [ ] Implement `StripeService.php` 
- [ ] Create basic payment intent functionality
- [ ] Test Stripe connection with test keys

### **Afternoon Tasks (3 hours)**
- [ ] Implement `VNPayService.php`
- [ ] Create `CurrencyConverter.php` utility
- [ ] Test VNPay sandbox integration
- [ ] Create payment configuration validation

### **Evening Verification (1 hour)**
- [ ] Verify Stripe test payment works
- [ ] Verify VNPay sandbox works
- [ ] Check currency conversion accuracy
- [ ] Commit and push changes

---

## 🛠️ **DEVELOPMENT COMMANDS**

### **Create Migration Files**
```bash
php artisan make:migration create_shopping_carts_table
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
php artisan make:migration create_payment_transactions_table
```

### **Create Model Files**
```bash
php artisan make:model ShoppingCart
php artisan make:model Order
php artisan make:model OrderItem
php artisan make:model PaymentTransaction
```

### **Create Controller Files**
```bash
php artisan make:controller Api/CartController --api
php artisan make:controller Api/CheckoutController
php artisan make:controller Api/OrderController --api
php artisan make:controller Webhooks/StripeWebhookController
```

### **Testing Commands**
```bash
# Test payment gateway connections
php artisan tinker --execute="
\$stripe = new \App\Services\PaymentGateway\StripeService();
echo 'Stripe connection: ' . (\$stripe->testConnection() ? 'OK' : 'FAIL');
"

# Test database migrations
php artisan migrate:status
php artisan migrate --pretend
```

---

## 📚 **REFERENCE DOCUMENTATION**

### **Stripe Integration**
- [Stripe PHP Documentation](https://stripe.com/docs/api/php)
- [Laravel Cashier](https://laravel.com/docs/10.x/billing) (optional)
- [Payment Intents API](https://stripe.com/docs/payments/payment-intents)

### **VNPay Integration**
- [VNPay Developer Documentation](https://sandbox.vnpayment.vn/apis/)
- [VNPay Integration Guide](https://vnpay.vn/huong-dan-tich-hop/)

### **Security Best Practices**
- [OWASP Payment Security](https://owasp.org/www-project-payment-security/)
- [PCI DSS Guidelines](https://www.pcisecuritystandards.org/)

---

## 🎯 **SUCCESS CRITERIA FOR DAY 1**

### **Functional Tests**
- [ ] ✅ Stripe test payment intent creates successfully
- [ ] ✅ VNPay payment URL generates correctly
- [ ] ✅ Currency conversion between USD/VND works
- [ ] ✅ Payment configuration loads without errors

### **Code Quality**
- [ ] ✅ All payment services follow interface contract
- [ ] ✅ Error handling implemented for payment failures
- [ ] ✅ Logging configured for payment events
- [ ] ✅ Environment variables secured

### **Documentation**
- [ ] ✅ Payment service methods documented
- [ ] ✅ Environment setup guide written
- [ ] ✅ Test payment scenarios documented
- [ ] ✅ Error handling scenarios listed

---

## 🔄 **DAILY PROGRESS TRACKING**

### **Day 1**: Payment Gateway Setup ⏳
- Morning: Stripe integration
- Afternoon: VNPay integration  
- Evening: Testing and validation

### **Day 2**: Database Extensions ⏳
- Morning: Cart/Order migrations
- Afternoon: Models and relationships
- Evening: Seeding test data

### **Day 3**: Purchase Flow API ⏳
- Morning: Cart management
- Afternoon: Checkout process
- Evening: Order creation

*Continue daily tracking through Phase 2...*

---

## 🆘 **QUICK TROUBLESHOOTING**

### **Common Issues**
1. **Stripe API Key Issues**
   ```bash
   # Verify keys in .env
   php artisan config:clear
   php artisan cache:clear
   ```

2. **VNPay Connection Failed**
   ```bash
   # Check network and URL configuration
   curl -I https://sandbox.vnpayment.vn/
   ```

3. **Currency Conversion Errors**
   ```bash
   # Verify exchange rates are current
   php artisan tinker --execute="
   echo 'USD to VND: ' . config('payment.currency_rates.USD_TO_VND');
   "
   ```

### **Getting Help**
- 📖 Check Phase 2 implementation plan
- 🔍 Review Stripe/VNPay documentation
- 💬 Laravel community forums
- 📧 Payment gateway support

---

**🎉 Ready to start Phase 2! Let's build the payment system!**

*Last updated: June 12, 2025*
