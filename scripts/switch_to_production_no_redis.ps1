# MechaMap Production Deployment Script (No Redis) - PowerShell Version
# Chuyển đổi từ development sang production environment

Write-Host "🚀 MECHAMAP PRODUCTION DEPLOYMENT (NO REDIS)" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""

# Change to Laravel directory
$LaravelPath = "d:\xampp\htdocs\laravel\mechamap_backend"
Set-Location $LaravelPath

Write-Host "📁 Current directory: $(Get-Location)" -ForegroundColor Cyan
Write-Host ""

# 1. Backup current .env
Write-Host "1️⃣ BACKING UP CURRENT CONFIGURATION..." -ForegroundColor Yellow
Write-Host "────────────────────────────────────────" -ForegroundColor Gray

if (Test-Path ".env") {
    $BackupName = ".env.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    Copy-Item ".env" $BackupName
    Write-Host "✅ Current .env backed up as $BackupName" -ForegroundColor Green
} else {
    Write-Host "⚠️ No .env file found" -ForegroundColor Yellow
}

# 2. Copy production environment
Write-Host ""
Write-Host "2️⃣ APPLYING PRODUCTION CONFIGURATION..." -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

if (Test-Path ".env.production") {
    Copy-Item ".env.production" ".env"
    Write-Host "✅ Production .env applied" -ForegroundColor Green
} else {
    Write-Host "❌ .env.production file not found!" -ForegroundColor Red
    exit 1
}

# 3. Clear all caches
Write-Host ""
Write-Host "3️⃣ CLEARING CACHES..." -ForegroundColor Yellow
Write-Host "────────────────────" -ForegroundColor Gray

& php artisan config:clear
Write-Host "✅ Config cache cleared" -ForegroundColor Green

& php artisan route:clear
Write-Host "✅ Route cache cleared" -ForegroundColor Green

& php artisan view:clear
Write-Host "✅ View cache cleared" -ForegroundColor Green

& php artisan cache:clear
Write-Host "✅ Application cache cleared" -ForegroundColor Green

# 4. Optimize for production
Write-Host ""
Write-Host "4️⃣ OPTIMIZING FOR PRODUCTION..." -ForegroundColor Yellow
Write-Host "──────────────────────────────" -ForegroundColor Gray

& php artisan config:cache
Write-Host "✅ Config cached" -ForegroundColor Green

& php artisan route:cache
Write-Host "✅ Routes cached" -ForegroundColor Green

& php artisan view:cache
Write-Host "✅ Views cached" -ForegroundColor Green

# 5. Run database migrations (if needed)
Write-Host ""
Write-Host "5️⃣ CHECKING DATABASE..." -ForegroundColor Yellow
Write-Host "─────────────────────" -ForegroundColor Gray

& php artisan migrate --force
Write-Host "✅ Database migrations completed" -ForegroundColor Green

# 6. Verify production configuration
Write-Host ""
Write-Host "6️⃣ VERIFYING CONFIGURATION..." -ForegroundColor Yellow
Write-Host "────────────────────────────" -ForegroundColor Gray

& php scripts/verify_production_config_no_redis.php

Write-Host ""
Write-Host "🎉 PRODUCTION DEPLOYMENT COMPLETED!" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""
Write-Host "📋 NEXT STEPS:" -ForegroundColor Cyan
Write-Host "1. Restart your web server (Apache/Nginx)" -ForegroundColor White
Write-Host "2. Start queue worker: php artisan queue:work --daemon" -ForegroundColor White
Write-Host "3. Set up cron job for scheduled tasks" -ForegroundColor White
Write-Host "4. Monitor logs: Get-Content storage/logs/laravel.log -Wait" -ForegroundColor White
Write-Host ""
Write-Host "⚠️ IMPORTANT NOTES:" -ForegroundColor Yellow
Write-Host "• Redis is disabled - using file cache and database queue" -ForegroundColor White
Write-Host "• Make sure to backup database before going live" -ForegroundColor White
Write-Host "• Monitor performance and consider Redis if needed" -ForegroundColor White
Write-Host ""

# Optional: Ask if user wants to start queue worker
$StartQueue = Read-Host "Do you want to start the queue worker now? (y/N)"
if ($StartQueue -eq "y" -or $StartQueue -eq "Y") {
    Write-Host ""
    Write-Host "🔄 Starting queue worker..." -ForegroundColor Yellow
    Write-Host "Press Ctrl+C to stop the worker" -ForegroundColor Gray
    & php artisan queue:work --verbose --tries=3 --timeout=90
}
