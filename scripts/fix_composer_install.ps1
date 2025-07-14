# MechaMap Composer Install Fix Script - PowerShell
Write-Host "🔧 MECHAMAP COMPOSER INSTALL FIX" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Green
Write-Host ""

# 1. Clear all caches first
Write-Host "1️⃣ CLEARING CACHES..." -ForegroundColor Yellow
Write-Host "────────────────────" -ForegroundColor Gray

if (Test-Path "vendor") {
    Remove-Item -Recurse -Force "vendor"
    Write-Host "✅ Vendor directory removed" -ForegroundColor Green
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

Write-Host ""

# 2. Update Composer
Write-Host "2️⃣ UPDATING COMPOSER..." -ForegroundColor Yellow
Write-Host "─────────────────────" -ForegroundColor Gray
& composer self-update
Write-Host "✅ Composer updated" -ForegroundColor Green
Write-Host ""

# 3. Clear Composer cache
Write-Host "3️⃣ CLEARING COMPOSER CACHE..." -ForegroundColor Yellow
Write-Host "────────────────────────────" -ForegroundColor Gray
& composer clear-cache
Write-Host "✅ Composer cache cleared" -ForegroundColor Green
Write-Host ""

# 4. Install dependencies with specific flags
Write-Host "4️⃣ INSTALLING DEPENDENCIES..." -ForegroundColor Yellow
Write-Host "────────────────────────────" -ForegroundColor Gray
& composer install --no-scripts --no-autoloader --no-dev
Write-Host "✅ Dependencies installed without scripts" -ForegroundColor Green
Write-Host ""

# 5. Generate autoloader
Write-Host "5️⃣ GENERATING AUTOLOADER..." -ForegroundColor Yellow
Write-Host "──────────────────────────" -ForegroundColor Gray
& composer dump-autoload --optimize --no-dev
Write-Host "✅ Autoloader generated" -ForegroundColor Green
Write-Host ""

# 6. Run post-install scripts manually
Write-Host "6️⃣ RUNNING POST-INSTALL SCRIPTS..." -ForegroundColor Yellow
Write-Host "─────────────────────────────────" -ForegroundColor Gray

# Check if .env exists and has APP_KEY
$envExists = Test-Path ".env"
$hasAppKey = $false

if ($envExists) {
    $envContent = Get-Content ".env" -Raw
    $hasAppKey = $envContent -match "APP_KEY=base64:"
}

if (-not $hasAppKey) {
    Write-Host "Generating APP_KEY..." -ForegroundColor Cyan
    & php artisan key:generate --force
}

# Create storage link if not exists
if (-not (Test-Path "public/storage")) {
    Write-Host "Creating storage link..." -ForegroundColor Cyan
    & php artisan storage:link
}

Write-Host "✅ Post-install scripts completed" -ForegroundColor Green
Write-Host ""

Write-Host "🎉 COMPOSER INSTALL FIX COMPLETED!" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Copy .env.production to .env" -ForegroundColor White
Write-Host "2. Update database credentials in .env" -ForegroundColor White
Write-Host "3. Run: php artisan migrate --force" -ForegroundColor White
Write-Host "4. Run: php artisan config:cache" -ForegroundColor White
Write-Host ""
