@echo off
REM =============================================================================
REM MechaMap Environment Switcher for Windows
REM Switch between development and production environments
REM =============================================================================

setlocal enabledelayedexpansion

REM Check arguments
if "%1"=="" (
    echo Usage: %0 [development^|production] [domain]
    echo.
    echo Examples:
    echo   %0 development                    # Switch to development ^(mechamap.test^)
    echo   %0 production                     # Switch to production ^(mechamap.com^)
    echo   %0 production yourdomain.com      # Switch to production with custom domain
    echo.
    exit /b 1
)

set ENVIRONMENT=%1
set CUSTOM_DOMAIN=%2

echo.
echo 🔄 MechaMap Environment Switcher
echo ================================
echo.

REM Set domain based on environment
if "%ENVIRONMENT%"=="development" (
    set DOMAIN=mechamap.test
    set WEBSOCKET_DOMAIN=localhost:3000
    set WEBSOCKET_URL=http://localhost:3000
    set WEBSOCKET_SECURE=false
) else if "%ENVIRONMENT%"=="production" (
    if not "%CUSTOM_DOMAIN%"=="" (
        set DOMAIN=%CUSTOM_DOMAIN%
        set WEBSOCKET_DOMAIN=realtime.%CUSTOM_DOMAIN%
    ) else (
        set DOMAIN=mechamap.com
        set WEBSOCKET_DOMAIN=realtime.mechamap.com
    )
    set WEBSOCKET_URL=https://!WEBSOCKET_DOMAIN!
    set WEBSOCKET_SECURE=true
) else (
    echo Error: Invalid environment. Use 'development' or 'production'
    exit /b 1
)

echo [%time%] Switching to %ENVIRONMENT% environment
echo [%time%] Domain: %DOMAIN%
echo [%time%] WebSocket: %WEBSOCKET_URL%
echo.

REM 1. Laravel Environment Setup
echo [%time%] 📝 Setting up Laravel environment...

set ENV_TEMPLATE=.env.%ENVIRONMENT%.template
set ENV_FILE=.env

if exist "%ENV_TEMPLATE%" (
    echo [%time%] Using template: %ENV_TEMPLATE%
    copy "%ENV_TEMPLATE%" "%ENV_FILE%" >nul
) else (
    echo [%time%] ⚠️  Template %ENV_TEMPLATE% not found, using current .env
)

REM Update .env file using PowerShell for better text replacement
if exist "%ENV_FILE%" (
    powershell -Command "(Get-Content '%ENV_FILE%') -replace 'APP_URL=.*', 'APP_URL=https://%DOMAIN%' | Set-Content '%ENV_FILE%'"
    powershell -Command "(Get-Content '%ENV_FILE%') -replace 'WEBSOCKET_SERVER_URL=.*', 'WEBSOCKET_SERVER_URL=%WEBSOCKET_URL%' | Set-Content '%ENV_FILE%'"
    powershell -Command "(Get-Content '%ENV_FILE%') -replace 'WEBSOCKET_SERVER_SECURE=.*', 'WEBSOCKET_SERVER_SECURE=%WEBSOCKET_SECURE%' | Set-Content '%ENV_FILE%'"
    powershell -Command "(Get-Content '%ENV_FILE%') -replace 'NODEJS_BROADCAST_URL=.*', 'NODEJS_BROADCAST_URL=%WEBSOCKET_URL%' | Set-Content '%ENV_FILE%'"
    
    echo [%time%] ✅ Laravel environment updated
) else (
    echo [%time%] ❌ .env file not found
    exit /b 1
)

REM 2. Node.js Environment Setup
echo [%time%] 📝 Setting up Node.js environment...

set NODEJS_DIR=realtime-server
if exist "%NODEJS_DIR%" (
    pushd "%NODEJS_DIR%"
    
    set NODEJS_ENV_TEMPLATE=.env.%ENVIRONMENT%.template
    set NODEJS_ENV_FILE=.env
    
    if exist "!NODEJS_ENV_TEMPLATE!" (
        echo [%time%] Using Node.js template: !NODEJS_ENV_TEMPLATE!
        copy "!NODEJS_ENV_TEMPLATE!" "!NODEJS_ENV_FILE!" >nul
    ) else (
        echo [%time%] ⚠️  Node.js template !NODEJS_ENV_TEMPLATE! not found
    )
    
    REM Update Node.js .env file
    if exist "!NODEJS_ENV_FILE!" (
        powershell -Command "(Get-Content '!NODEJS_ENV_FILE!') -replace 'LARAVEL_API_URL=.*', 'LARAVEL_API_URL=https://%DOMAIN%' | Set-Content '!NODEJS_ENV_FILE!'"
        
        if "%ENVIRONMENT%"=="development" (
            set CORS_ORIGINS=https://%DOMAIN%,http://localhost:8000,https://localhost:8000
        ) else (
            set CORS_ORIGINS=https://%DOMAIN%,https://www.%DOMAIN%
        )
        
        powershell -Command "(Get-Content '!NODEJS_ENV_FILE!') -replace 'CORS_ORIGIN=.*', 'CORS_ORIGIN=!CORS_ORIGINS!' | Set-Content '!NODEJS_ENV_FILE!'"
        
        echo [%time%] ✅ Node.js environment updated
    ) else (
        echo [%time%] ⚠️  Node.js .env file not found
    )
    
    popd
) else (
    echo [%time%] ⚠️  Node.js directory not found: %NODEJS_DIR%
)

REM 3. Clear Laravel caches
echo [%time%] 🧹 Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM 4. Validate configuration
echo [%time%] 🔍 Validating configuration...
php artisan websocket:validate-config

REM 5. Restart Node.js server if PM2 is available
echo [%time%] 🔄 Checking Node.js server...
where pm2 >nul 2>&1
if !errorlevel! equ 0 (
    pushd "%NODEJS_DIR%"
    pm2 restart mechamap-realtime 2>nul || echo [%time%] PM2 process not running
    popd
) else (
    echo [%time%] PM2 not found, please restart Node.js server manually
)

echo.
echo [%time%] ✅ Environment switched successfully!
echo.
echo 📋 Summary:
echo    Environment: %ENVIRONMENT%
echo    Laravel URL: https://%DOMAIN%
echo    WebSocket URL: %WEBSOCKET_URL%
echo.
echo 🔧 Next steps:
if "%ENVIRONMENT%"=="production" (
    echo    1. Update DNS records to point %DOMAIN% to your server IP
    echo    2. Configure SSL certificates for %DOMAIN% and %WEBSOCKET_DOMAIN%
    echo    3. Update Nginx configuration for both domains
    echo    4. Update database credentials in .env files
    echo    5. Generate new API keys: php artisan websocket:generate-api-key --env=production
) else (
    echo    1. Ensure %DOMAIN% points to 127.0.0.1 in your hosts file
    echo    2. Start Laravel development server: php artisan serve --host=%DOMAIN%
    echo    3. Start Node.js server: cd realtime-server ^&^& npm start
)
echo.

pause
