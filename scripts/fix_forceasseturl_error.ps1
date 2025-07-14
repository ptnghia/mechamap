# Fix forceAssetUrl Error - PowerShell Script
Write-Host "🔧 FIXING forceAssetUrl ERROR" -ForegroundColor Green
Write-Host "=============================" -ForegroundColor Green

Write-Host ""
Write-Host "1️⃣ IDENTIFYING THE ISSUE..." -ForegroundColor Yellow
Write-Host "──────────────────────────" -ForegroundColor Gray

# Check if the problematic code exists
if (Select-String -Path "app/Providers/AppServiceProvider.php" -Pattern "forceAssetUrl" -Quiet) {
    Write-Host "❌ Found forceAssetUrl usage in AppServiceProvider.php" -ForegroundColor Red
    Write-Host "This method doesn't exist in current Laravel version" -ForegroundColor Red
} else {
    Write-Host "✅ No forceAssetUrl usage found in AppServiceProvider.php" -ForegroundColor Green
}

Write-Host ""
Write-Host "2️⃣ CLEANING ENVIRONMENT..." -ForegroundColor Yellow
Write-Host "─────────────────────────" -ForegroundColor Gray

# Remove vendor and caches
if (Test-Path "vendor") {
    Remove-Item -Recurse -Force "vendor"
    Write-Host "✅ Vendor directory removed" -ForegroundColor Green
}

if (Test-Path "composer.lock") {
    Remove-Item -Force "composer.lock"
    Write-Host "✅ Composer lock removed" -ForegroundColor Green
}

if (Test-Path "bootstrap/cache") {
    Get-ChildItem "bootstrap/cache/*.php" | Remove-Item -Force
    Write-Host "✅ Bootstrap cache cleared" -ForegroundColor Green
}

if (Test-Path "storage/framework/cache/data") {
    Get-ChildItem "storage/framework/cache/data/*" | Remove-Item -Force -Recurse
    Write-Host "✅ Framework cache cleared" -ForegroundColor Green
}

if (Test-Path "storage/framework/sessions") {
    Get-ChildItem "storage/framework/sessions/*" | Remove-Item -Force
    Write-Host "✅ Sessions cleared" -ForegroundColor Green
}

if (Test-Path "storage/framework/views") {
    Get-ChildItem "storage/framework/views/*" | Remove-Item -Force
    Write-Host "✅ Views cache cleared" -ForegroundColor Green
}

# Clear Composer cache
& composer clear-cache
Write-Host "✅ Composer cache cleared" -ForegroundColor Green

Write-Host ""
Write-Host "3️⃣ FIXING COMPOSER AUTOLOAD..." -ForegroundColor Yellow
Write-Host "─────────────────────────────" -ForegroundColor Gray

# Try to install without running scripts
$installResult = & composer install --no-scripts --no-autoloader --no-dev
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Dependencies installed without scripts" -ForegroundColor Green
} else {
    Write-Host "❌ Failed to install dependencies" -ForegroundColor Red
    exit 1
}

# Generate autoloader manually
$autoloadResult = & composer dump-autoload --no-scripts --optimize --no-dev
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Autoloader generated successfully" -ForegroundColor Green
} else {
    Write-Host "❌ Failed to generate autoloader" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "4️⃣ SETTING UP ENVIRONMENT..." -ForegroundColor Yellow
Write-Host "───────────────────────────" -ForegroundColor Gray

# Copy environment file if needed
if (-not (Test-Path ".env")) {
    if (Test-Path ".env.production") {
        Copy-Item ".env.production" ".env"
        Write-Host "✅ Environment file created" -ForegroundColor Green
    } else {
        Write-Host "❌ .env.production not found" -ForegroundColor Red
        exit 1
    }
}

# Generate APP_KEY if needed
$envContent = Get-Content ".env" -Raw
if (-not ($envContent -match "APP_KEY=base64:")) {
    & php artisan key:generate --force
    Write-Host "✅ APP_KEY generated" -ForegroundColor Green
}

# Create storage link
if (-not (Test-Path "public/storage")) {
    & php artisan storage:link
    Write-Host "✅ Storage link created" -ForegroundColor Green
}

Write-Host ""
Write-Host "5️⃣ TESTING LARAVEL FUNCTIONALITY..." -ForegroundColor Yellow
Write-Host "──────────────────────────────────" -ForegroundColor Gray

# Test basic Laravel commands
$versionResult = & php artisan --version 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Laravel CLI working" -ForegroundColor Green
} else {
    Write-Host "❌ Laravel CLI not working" -ForegroundColor Red
    exit 1
}

# Test config loading
$configResult = & php artisan config:clear 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Config system working" -ForegroundColor Green
} else {
    Write-Host "❌ Config system not working" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "6️⃣ PRODUCTION OPTIMIZATION..." -ForegroundColor Yellow
Write-Host "────────────────────────────" -ForegroundColor Gray

# Cache configurations
& php artisan config:cache
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Config cached" -ForegroundColor Green
} else {
    Write-Host "⚠️ Config cache failed" -ForegroundColor Yellow
}

& php artisan route:cache
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Routes cached" -ForegroundColor Green
} else {
    Write-Host "⚠️ Route cache failed" -ForegroundColor Yellow
}

& php artisan view:cache
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Views cached" -ForegroundColor Green
} else {
    Write-Host "⚠️ View cache failed" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "7️⃣ FINAL VERIFICATION..." -ForegroundColor Yellow
Write-Host "───────────────────────" -ForegroundColor Gray

# Test that package discovery works now
$packageResult = & php artisan package:discover --ansi 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Package discovery working" -ForegroundColor Green
} else {
    Write-Host "❌ Package discovery still failing" -ForegroundColor Red
    Write-Host "Please check for other forceAssetUrl references in your code" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "🎉 forceAssetUrl error fixed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Update your database credentials in .env" -ForegroundColor White
Write-Host "2. Import your database: mysql -u user -p database < data_v2_fixed.sql" -ForegroundColor White
Write-Host "3. Configure your web server" -ForegroundColor White
Write-Host "4. Test your application" -ForegroundColor White
