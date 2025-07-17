# MechaMap - Remove Old WebSocket Implementation Script (PowerShell)
Write-Host "🗑️  MECHAMAP - REMOVE OLD WEBSOCKET IMPLEMENTATION" -ForegroundColor Green
Write-Host "=================================================" -ForegroundColor Green
Write-Host ""

# Function to print colored output
function Write-Status {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "⚠️  $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "❌ $Message" -ForegroundColor Red
}

function Write-Info {
    param([string]$Message)
    Write-Host "ℹ️  $Message" -ForegroundColor Blue
}

# Check if we're in the right directory
if (-not (Test-Path "composer.json")) {
    Write-Error "This script must be run from the Laravel project root directory"
    exit 1
}

Write-Host "🔍 ANALYZING CURRENT WEBSOCKET IMPLEMENTATION..." -ForegroundColor Yellow
Write-Host "──────────────────────────────────────────────" -ForegroundColor Gray

# 1. Check Laravel Reverb packages
Write-Host ""
Write-Host "1️⃣ CHECKING LARAVEL REVERB PACKAGES..." -ForegroundColor Yellow
Write-Host "────────────────────────────────────────" -ForegroundColor Gray

$composerContent = Get-Content "composer.json" -Raw
if ($composerContent -match "laravel/reverb") {
    Write-Warning "Found Laravel Reverb in composer.json"
}
else {
    Write-Info "No Laravel Reverb found in composer.json"
}

# 2. Check WebSocket Controllers
Write-Host ""
Write-Host "2️⃣ CHECKING WEBSOCKET CONTROLLERS..." -ForegroundColor Yellow
Write-Host "──────────────────────────────────────" -ForegroundColor Gray

$WebSocketControllers = @(
    "app\Http\Controllers\WebSocketController.php",
    "app\Http\Controllers\RealTimeController.php"
)

foreach ($controller in $WebSocketControllers) {
    if (Test-Path $controller) {
        Write-Warning "Found: $controller"
    }
}

# 3. Check Broadcasting Events
Write-Host ""
Write-Host "3️⃣ CHECKING BROADCASTING EVENTS..." -ForegroundColor Yellow
Write-Host "────────────────────────────────────" -ForegroundColor Gray

$BroadcastingEvents = @(
    "app\Events\ConnectionEvent.php",
    "app\Events\ChatMessageSent.php",
    "app\Events\UserOnlineStatusChanged.php",
    "app\Events\RealTimeNotification.php",
    "app\Events\UserActivityUpdate.php",
    "app\Events\DashboardMetricsUpdate.php",
    "app\Events\TypingStarted.php",
    "app\Events\SecurityIncidentDetected.php"
)

foreach ($eventFile in $BroadcastingEvents) {
    if (Test-Path $eventFile) {
        Write-Warning "Found: $eventFile"
    }
}

# 4. Check WebSocket Services
Write-Host ""
Write-Host "4️⃣ CHECKING WEBSOCKET SERVICES..." -ForegroundColor Yellow
Write-Host "───────────────────────────────────" -ForegroundColor Gray

$WebSocketServices = @(
    "app\Services\WebSocketService.php",
    "app\Services\WebSocketConnectionService.php",
    "app\Services\RealTimeNotificationService.php",
    "app\Services\TypingIndicatorService.php",
    "app\Services\ConnectionManagementService.php",
    "app\Services\ConnectionHealthMonitor.php"
)

foreach ($service in $WebSocketServices) {
    if (Test-Path $service) {
        Write-Warning "Found: $service"
    }
}

# 5. Check Frontend WebSocket files
Write-Host ""
Write-Host "5️⃣ CHECKING FRONTEND WEBSOCKET FILES..." -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

$FrontendWebSocketFiles = @(
    "public\js\realtime-client.js",
    "public\js\notification-service.js",
    "resources\js\realtime-client.js",
    "resources\js\notification-service.js"
)

foreach ($file in $FrontendWebSocketFiles) {
    if (Test-Path $file) {
        Write-Warning "Found: $file"
    }
}

# 6. Check Configuration files
Write-Host ""
Write-Host "6️⃣ CHECKING CONFIGURATION FILES..." -ForegroundColor Yellow
Write-Host "────────────────────────────────────" -ForegroundColor Gray

$ConfigFiles = @(
    "config\reverb.php",
    "config\broadcasting.php"
)

foreach ($config in $ConfigFiles) {
    if (Test-Path $config) {
        Write-Warning "Found: $config"
    }
}

# 7. Check Routes
Write-Host ""
Write-Host "7️⃣ CHECKING WEBSOCKET ROUTES..." -ForegroundColor Yellow
Write-Host "─────────────────────────────────" -ForegroundColor Gray

if (Test-Path "routes\api.php") {
    $apiContent = Get-Content "routes\api.php" -Raw
    if ($apiContent -match "websocket") {
        Write-Warning "Found WebSocket routes in routes\api.php"
    }
}

if (Test-Path "routes\web.php") {
    $webContent = Get-Content "routes\web.php" -Raw
    if ($webContent -match "realtime") {
        Write-Warning "Found realtime routes in routes\web.php"
    }
}

if (Test-Path "routes\channels.php") {
    Write-Warning "Found broadcasting channels in routes\channels.php"
}

Write-Host ""
Write-Host "🤔 READY TO REMOVE OLD WEBSOCKET IMPLEMENTATION?" -ForegroundColor Yellow
Write-Host "═══════════════════════════════════════════════" -ForegroundColor Yellow
Write-Host ""
Write-Host "This will remove:" -ForegroundColor White
Write-Host "  • Laravel Reverb package" -ForegroundColor White
Write-Host "  • WebSocket Controllers & Services" -ForegroundColor White
Write-Host "  • Broadcasting Events" -ForegroundColor White
Write-Host "  • Frontend WebSocket files" -ForegroundColor White
Write-Host "  • WebSocket routes" -ForegroundColor White
Write-Host "  • Configuration files" -ForegroundColor White
Write-Host ""

$confirmation = Read-Host "Continue? (y/N)"
if ($confirmation -ne "y" -and $confirmation -ne "Y") {
    Write-Info "Operation cancelled by user"
    exit 0
}

Write-Host ""
Write-Host "🗑️  REMOVING OLD WEBSOCKET IMPLEMENTATION..." -ForegroundColor Green
Write-Host "═══════════════════════════════════════════" -ForegroundColor Green

# Step 1: Remove Laravel Reverb package
Write-Host ""
Write-Host "1️⃣ REMOVING LARAVEL REVERB PACKAGE..." -ForegroundColor Yellow
Write-Host "────────────────────────────────────────" -ForegroundColor Gray

if ($composerContent -match "laravel/reverb") {
    Write-Info "Removing Laravel Reverb from composer.json..."
    try {
        & composer remove laravel/reverb --no-interaction
        Write-Status "Laravel Reverb package removed"
    }
    catch {
        Write-Error "Failed to remove Laravel Reverb package"
    }
}
else {
    Write-Info "Laravel Reverb not found in composer.json"
}

# Step 2: Remove WebSocket Controllers
Write-Host ""
Write-Host "2️⃣ REMOVING WEBSOCKET CONTROLLERS..." -ForegroundColor Yellow
Write-Host "──────────────────────────────────────" -ForegroundColor Gray

foreach ($controller in $WebSocketControllers) {
    if (Test-Path $controller) {
        Remove-Item $controller -Force
        Write-Status "Removed: $controller"
    }
}

# Step 3: Remove Broadcasting Events
Write-Host ""
Write-Host "3️⃣ REMOVING BROADCASTING EVENTS..." -ForegroundColor Yellow
Write-Host "────────────────────────────────────" -ForegroundColor Gray

foreach ($eventFile in $BroadcastingEvents) {
    if (Test-Path $eventFile) {
        Remove-Item $eventFile -Force
        Write-Status "Removed: $eventFile"
    }
}

# Step 4: Remove WebSocket Services
Write-Host ""
Write-Host "4️⃣ REMOVING WEBSOCKET SERVICES..." -ForegroundColor Yellow
Write-Host "───────────────────────────────────" -ForegroundColor Gray

foreach ($service in $WebSocketServices) {
    if (Test-Path $service) {
        Remove-Item $service -Force
        Write-Status "Removed: $service"
    }
}

# Step 5: Remove Frontend WebSocket files
Write-Host ""
Write-Host "5️⃣ REMOVING FRONTEND WEBSOCKET FILES..." -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

foreach ($file in $FrontendWebSocketFiles) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Status "Removed: $file"
    }
}

# Step 6: Remove Configuration files
Write-Host ""
Write-Host "6️⃣ REMOVING CONFIGURATION FILES..." -ForegroundColor Yellow
Write-Host "────────────────────────────────────" -ForegroundColor Gray

# Remove reverb.php config
if (Test-Path "config\reverb.php") {
    Remove-Item "config\reverb.php" -Force
    Write-Status "Removed: config\reverb.php"
}

# Update broadcasting.php to remove reverb driver
if (Test-Path "config\broadcasting.php") {
    Write-Info "Updating config\broadcasting.php to remove reverb driver..."
    # Create backup
    Copy-Item "config\broadcasting.php" "config\broadcasting.php.backup"

    # Read content and remove reverb configuration
    $broadcastingContent = Get-Content "config\broadcasting.php" -Raw
    $broadcastingContent = $broadcastingContent -replace "(?s)'reverb'.*?],", ""
    Set-Content "config\broadcasting.php" $broadcastingContent
    Write-Status "Updated: config\broadcasting.php (backup created)"
}

# Step 7: Clean WebSocket routes
Write-Host ""
Write-Host "7️⃣ CLEANING WEBSOCKET ROUTES..." -ForegroundColor Yellow
Write-Host "─────────────────────────────────" -ForegroundColor Gray

# Clean API routes
if (Test-Path "routes\api.php") {
    $apiContent = Get-Content "routes\api.php" -Raw
    if ($apiContent -match "websocket") {
        Write-Info "Cleaning WebSocket routes from routes\api.php..."
        Copy-Item "routes\api.php" "routes\api.php.backup"

        # Remove WebSocket API routes block
        $apiContent = $apiContent -replace "(?s)// WebSocket API routes.*?\}\);", ""
        Set-Content "routes\api.php" $apiContent
        Write-Status "Cleaned: routes\api.php (backup created)"
    }
}

# Clean web routes
if (Test-Path "routes\web.php") {
    $webContent = Get-Content "routes\web.php" -Raw
    if ($webContent -match "realtime") {
        Write-Info "Cleaning realtime routes from routes\web.php..."
        Copy-Item "routes\web.php" "routes\web.php.backup"

        # Remove realtime routes block
        $webContent = $webContent -replace "(?s)Route::prefix.*?realtime.*?\}\);", ""
        Set-Content "routes\web.php" $webContent
        Write-Status "Cleaned: routes\web.php (backup created)"
    }
}

# Remove broadcasting channels
if (Test-Path "routes\channels.php") {
    Write-Info "Cleaning broadcasting channels..."
    Copy-Item "routes\channels.php" "routes\channels.php.backup"

    # Create new minimal channels.php
    $channelsContent = @'
<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcasting channels will be managed by Node.js WebSocket server
// This file is kept for Laravel compatibility but channels are not used
'@
    Set-Content "routes\channels.php" $channelsContent
    Write-Status "Cleaned: routes\channels.php (backup created)"
}

# Step 8: Clean Frontend Layout
Write-Host ""
Write-Host "8️⃣ CLEANING FRONTEND LAYOUT..." -ForegroundColor Yellow
Write-Host "─────────────────────────────" -ForegroundColor Gray

# Remove Pusher.js initialization from app.blade.php
if (Test-Path "resources\views\layouts\app.blade.php") {
    Write-Info "Removing Pusher.js initialization from app.blade.php..."
    Copy-Item "resources\views\layouts\app.blade.php" "resources\views\layouts\app.blade.php.backup"

    # Remove Pusher.js script block
    $appContent = Get-Content "resources\views\layouts\app.blade.php" -Raw
    $appContent = $appContent -replace "(?s)<!-- Initialize Pusher\.js directly for Reverb -->.*?\}\);", ""
    Set-Content "resources\views\layouts\app.blade.php" $appContent
    Write-Status "Cleaned: resources\views\layouts\app.blade.php (backup created)"
}

# Step 9: Update Environment Variables
Write-Host ""
Write-Host "9️⃣ UPDATING ENVIRONMENT VARIABLES..." -ForegroundColor Yellow
Write-Host "──────────────────────────────────────" -ForegroundColor Gray

if (Test-Path ".env") {
    Write-Info "Updating .env file..."
    Copy-Item ".env" ".env.backup"

    # Read .env content
    $envContent = Get-Content ".env"

    # Update BROADCAST_CONNECTION and comment out REVERB variables
    $envContent = $envContent -replace "^BROADCAST_CONNECTION=.*", "BROADCAST_CONNECTION=log"
    $envContent = $envContent -replace "^REVERB_", "#REVERB_"

    Set-Content ".env" $envContent
    Write-Status "Updated: .env (backup created)"
    Write-Info "Set BROADCAST_CONNECTION=log (disabled broadcasting)"
}

# Step 10: Clear Caches
Write-Host ""
Write-Host "🔟 CLEARING CACHES..." -ForegroundColor Yellow
Write-Host "───────────────────" -ForegroundColor Gray

Write-Info "Clearing Laravel caches..."
try {
    & php artisan config:clear
    & php artisan route:clear
    & php artisan view:clear
    & php artisan cache:clear
    Write-Status "Laravel caches cleared"
}
catch {
    Write-Warning "Some caches may not have been cleared properly"
}

# Step 11: Regenerate Autoloader
Write-Host ""
Write-Host "1️⃣1️⃣ REGENERATING AUTOLOADER..." -ForegroundColor Yellow
Write-Host "─────────────────────────────" -ForegroundColor Gray

Write-Info "Regenerating Composer autoloader..."
try {
    & composer dump-autoload --optimize
    Write-Status "Composer autoloader regenerated"
}
catch {
    Write-Error "Failed to regenerate autoloader"
}

# Final Summary
Write-Host ""
Write-Host "🎉 OLD WEBSOCKET IMPLEMENTATION REMOVED!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════" -ForegroundColor Green
Write-Host ""
Write-Host "✅ Removed Components:" -ForegroundColor Green
Write-Host "   • Laravel Reverb package" -ForegroundColor White
Write-Host "   • WebSocket Controllers ($($WebSocketControllers.Count) files)" -ForegroundColor White
Write-Host "   • Broadcasting Events ($($BroadcastingEvents.Count) files)" -ForegroundColor White
Write-Host "   • WebSocket Services ($($WebSocketServices.Count) files)" -ForegroundColor White
Write-Host "   • Frontend WebSocket files" -ForegroundColor White
Write-Host "   • WebSocket routes" -ForegroundColor White
Write-Host "   • Configuration files" -ForegroundColor White
Write-Host "   • Pusher.js initialization" -ForegroundColor White
Write-Host ""
Write-Host "📁 Backup Files Created:" -ForegroundColor Blue
Write-Host "   • .env.backup" -ForegroundColor White
Write-Host "   • config\broadcasting.php.backup" -ForegroundColor White
Write-Host "   • routes\api.php.backup" -ForegroundColor White
Write-Host "   • routes\web.php.backup" -ForegroundColor White
Write-Host "   • routes\channels.php.backup" -ForegroundColor White
Write-Host "   • resources\views\layouts\app.blade.php.backup" -ForegroundColor White
Write-Host ""
Write-Host "🔧 Next Steps:" -ForegroundColor Yellow
Write-Host "   1. Test the application to ensure no errors" -ForegroundColor White
Write-Host "   2. Integrate Node.js WebSocket server" -ForegroundColor White
Write-Host "   3. Update frontend to connect to Node.js server" -ForegroundColor White
Write-Host "   4. Remove backup files when satisfied" -ForegroundColor White
Write-Host ""
Write-Host "⚠️  Note: Broadcasting is now disabled (BROADCAST_CONNECTION=log)" -ForegroundColor Yellow
Write-Host "   This will be re-enabled when Node.js WebSocket server is integrated" -ForegroundColor White
Write-Host ""
Write-Status "Old WebSocket implementation removal completed successfully!"
