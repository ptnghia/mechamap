<?php
/**
 * Phase 3A Payment Gateway Configuration Setup
 * MechaMap Marketplace - Stripe & VNPay Integration
 */

echo "🚀 PHASE 3A: PAYMENT GATEWAY INTEGRATION\n";
echo "==========================================\n";
echo "Setting up Stripe and VNPay for MechaMap Marketplace\n\n";

// Check current .env configuration
echo "📝 Step 1: Checking current payment configuration...\n";

$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Check for existing payment configuration
$hasStripe = strpos($envContent, 'STRIPE_KEY=') !== false;
$hasVNPay = strpos($envContent, 'VNPAY_TMN_CODE=') !== false;

echo "Current payment gateway status:\n";
echo "- Stripe: " . ($hasStripe ? "✅ Configured" : "❌ Not configured") . "\n";
echo "- VNPay: " . ($hasVNPay ? "✅ Configured" : "❌ Not configured") . "\n\n";

// Backup current .env
$backupFile = __DIR__ . '/.env.backup.' . date('Y-m-d-H-i-s');
if (copy($envFile, $backupFile)) {
    echo "✅ Created .env backup: " . basename($backupFile) . "\n";
} else {
    echo "⚠️  Could not create .env backup\n";
}

echo "\n📝 Step 2: Adding payment gateway configuration...\n";

// Payment gateway configuration template
$paymentConfig = "

# ====================================
# PAYMENT GATEWAY CONFIGURATION
# ====================================

# Stripe Configuration (Test Mode)
# Get your keys from: https://dashboard.stripe.com/test/apikeys
STRIPE_KEY=pk_test_your_stripe_publishable_key_here
STRIPE_SECRET=sk_test_your_stripe_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here

# VNPay Configuration (Sandbox)
# Get your credentials from: https://sandbox.vnpayment.vn/
VNPAY_TMN_CODE=your_tmn_code_here
VNPAY_HASH_SECRET=your_hash_secret_here
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html

# Payment Settings
PAYMENT_PROCESSING_FEE_PERCENTAGE=2.9
PAYMENT_PLATFORM_FEE_PERCENTAGE=15.0
PAYMENT_SUPPORTED_CURRENCIES=USD,VND
PAYMENT_DEFAULT_CURRENCY=USD

# Order Settings
ORDER_AUTO_EXPIRE_MINUTES=60
ORDER_CONFIRMATION_EMAIL=true
ORDER_INVOICE_GENERATION=true

# Download Settings
DOWNLOAD_LINK_EXPIRY_HOURS=24
DOWNLOAD_MAX_ATTEMPTS=5
DOWNLOAD_SECURE_TOKEN_LENGTH=64

";

// Check if payment config already exists
if (!$hasStripe && !$hasVNPay) {
    // Add payment configuration to .env
    if (file_put_contents($envFile, $paymentConfig, FILE_APPEND | LOCK_EX)) {
        echo "✅ Added payment gateway configuration to .env\n";
    } else {
        echo "❌ Failed to update .env file\n";
        exit(1);
    }
} else {
    echo "⚠️  Payment configuration already exists in .env\n";
}

echo "\n📝 Step 3: Creating payment gateway configuration files...\n";

// Create config/payment.php
$paymentConfigFile = __DIR__ . '/config/payment.php';
$paymentConfigContent = '<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */

    \'stripe\' => [
        \'key\' => env(\'STRIPE_KEY\'),
        \'secret\' => env(\'STRIPE_SECRET\'),
        \'webhook_secret\' => env(\'STRIPE_WEBHOOK_SECRET\'),
        \'currency\' => \'usd\',
        \'enabled\' => !empty(env(\'STRIPE_KEY\')) && !empty(env(\'STRIPE_SECRET\')),
    ],

    \'vnpay\' => [
        \'tmn_code\' => env(\'VNPAY_TMN_CODE\'),
        \'hash_secret\' => env(\'VNPAY_HASH_SECRET\'),
        \'url\' => env(\'VNPAY_URL\', \'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html\'),
        \'return_url\' => env(\'APP_URL\') . \'/api/v1/payments/vnpay/return\',
        \'currency\' => \'VND\',
        \'enabled\' => !empty(env(\'VNPAY_TMN_CODE\')) && !empty(env(\'VNPAY_HASH_SECRET\')),
    ],

    \'settings\' => [
        \'processing_fee_percentage\' => env(\'PAYMENT_PROCESSING_FEE_PERCENTAGE\', 2.9),
        \'platform_fee_percentage\' => env(\'PAYMENT_PLATFORM_FEE_PERCENTAGE\', 15.0),
        \'supported_currencies\' => explode(\',\', env(\'PAYMENT_SUPPORTED_CURRENCIES\', \'USD,VND\')),
        \'default_currency\' => env(\'PAYMENT_DEFAULT_CURRENCY\', \'USD\'),
    ],

    \'order\' => [
        \'auto_expire_minutes\' => env(\'ORDER_AUTO_EXPIRE_MINUTES\', 60),
        \'confirmation_email\' => env(\'ORDER_CONFIRMATION_EMAIL\', true),
        \'invoice_generation\' => env(\'ORDER_INVOICE_GENERATION\', true),
    ],

    \'download\' => [
        \'link_expiry_hours\' => env(\'DOWNLOAD_LINK_EXPIRY_HOURS\', 24),
        \'max_attempts\' => env(\'DOWNLOAD_MAX_ATTEMPTS\', 5),
        \'secure_token_length\' => env(\'DOWNLOAD_SECURE_TOKEN_LENGTH\', 64),
    ],
];
';

if (file_put_contents($paymentConfigFile, $paymentConfigContent)) {
    echo "✅ Created config/payment.php\n";
} else {
    echo "❌ Failed to create config/payment.php\n";
}

echo "\n📝 Step 4: Next steps for manual configuration...\n";
echo "===============================================\n\n";

echo "🔑 STRIPE CONFIGURATION:\n";
echo "1. Visit: https://dashboard.stripe.com/test/apikeys\n";
echo "2. Copy your Publishable Key (starts with pk_test_)\n";
echo "3. Copy your Secret Key (starts with sk_test_)\n";
echo "4. Replace STRIPE_KEY and STRIPE_SECRET in .env\n\n";

echo "🔑 VNPAY CONFIGURATION:\n";
echo "1. Visit: https://sandbox.vnpayment.vn/\n";
echo "2. Register for sandbox account\n";
echo "3. Get your TMN_CODE and HASH_SECRET\n";
echo "4. Replace VNPAY_TMN_CODE and VNPAY_HASH_SECRET in .env\n\n";

echo "🧪 TESTING CONFIGURATION:\n";
echo "Run: php test_payment_gateways.php\n\n";

echo "📋 WHAT\'S NEXT:\n";
echo "1. Configure payment gateway credentials\n";
echo "2. Implement payment processing endpoints\n";
echo "3. Add webhook handlers\n";
echo "4. Test payment flows\n";
echo "5. Add download system integration\n\n";

echo "✅ Phase 3A setup completed!\n";
echo "Ready for payment gateway integration...\n";
