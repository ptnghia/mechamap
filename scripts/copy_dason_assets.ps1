# PowerShell script to copy Dason assets to MechaMap
# Run this script from the MechaMap root directory

Write-Host "🔧 Starting Dason Assets Copy Process..." -ForegroundColor Green

# Define source and destination paths
$DasonPath = ".\Dason-Laravel_v1.0.0\Admin\public\assets"
$DestPath = ".\public\assets"

# Check if Dason source exists
if (-Not (Test-Path $DasonPath)) {
    Write-Host "❌ Error: Dason source path not found: $DasonPath" -ForegroundColor Red
    Write-Host "Please ensure you're running this script from the MechaMap root directory" -ForegroundColor Yellow
    exit 1
}

# Create destination directories if they don't exist
$Directories = @("css", "js", "images", "fonts", "libs")
foreach ($dir in $Directories) {
    $destDir = Join-Path $DestPath $dir
    if (-Not (Test-Path $destDir)) {
        New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        Write-Host "✅ Created directory: $destDir" -ForegroundColor Green
    }
}

# Copy CSS files
Write-Host "📄 Copying CSS files..." -ForegroundColor Cyan
$cssSource = Join-Path $DasonPath "css\*"
$cssDest = Join-Path $DestPath "css"
if (Test-Path (Join-Path $DasonPath "css")) {
    Copy-Item $cssSource $cssDest -Recurse -Force
    Write-Host "✅ CSS files copied successfully" -ForegroundColor Green
} else {
    Write-Host "⚠️ CSS source directory not found" -ForegroundColor Yellow
}

# Copy JS files
Write-Host "📄 Copying JS files..." -ForegroundColor Cyan
$jsSource = Join-Path $DasonPath "js\*"
$jsDest = Join-Path $DestPath "js"
if (Test-Path (Join-Path $DasonPath "js")) {
    Copy-Item $jsSource $jsDest -Recurse -Force
    Write-Host "✅ JS files copied successfully" -ForegroundColor Green
} else {
    Write-Host "⚠️ JS source directory not found" -ForegroundColor Yellow
}

# Copy Images
Write-Host "🖼️ Copying Images..." -ForegroundColor Cyan
$imagesSource = Join-Path $DasonPath "images\*"
$imagesDest = Join-Path $DestPath "images"
if (Test-Path (Join-Path $DasonPath "images")) {
    Copy-Item $imagesSource $imagesDest -Recurse -Force
    Write-Host "✅ Images copied successfully" -ForegroundColor Green
} else {
    Write-Host "⚠️ Images source directory not found" -ForegroundColor Yellow
}

# Copy Fonts
Write-Host "🔤 Copying Fonts..." -ForegroundColor Cyan
$fontsSource = Join-Path $DasonPath "fonts\*"
$fontsDest = Join-Path $DestPath "fonts"
if (Test-Path (Join-Path $DasonPath "fonts")) {
    Copy-Item $fontsSource $fontsDest -Recurse -Force
    Write-Host "✅ Fonts copied successfully" -ForegroundColor Green
} else {
    Write-Host "⚠️ Fonts source directory not found" -ForegroundColor Yellow
}

# Copy Libraries
Write-Host "📚 Copying Libraries..." -ForegroundColor Cyan
$libsSource = Join-Path $DasonPath "libs\*"
$libsDest = Join-Path $DestPath "libs"
if (Test-Path (Join-Path $DasonPath "libs")) {
    Copy-Item $libsSource $libsDest -Recurse -Force
    Write-Host "✅ Libraries copied successfully" -ForegroundColor Green
} else {
    Write-Host "⚠️ Libraries source directory not found" -ForegroundColor Yellow
}

# Verify copy results
Write-Host "`n🔍 Verifying copy results..." -ForegroundColor Cyan

$Directories = @("css", "js", "images", "fonts", "libs")
foreach ($dir in $Directories) {
    $destDir = Join-Path $DestPath $dir
    if (Test-Path $destDir) {
        $fileCount = (Get-ChildItem $destDir -Recurse -File).Count
        Write-Host "✅ $dir directory: $fileCount files" -ForegroundColor Green
    } else {
        Write-Host "❌ $dir directory: Not found" -ForegroundColor Red
    }
}

Write-Host "`n🎉 Dason Assets Copy Process Completed!" -ForegroundColor Green
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Update package.json with Dason dependencies" -ForegroundColor White
Write-Host "2. Run: npm install" -ForegroundColor White
Write-Host "3. Run: npm run dev" -ForegroundColor White
Write-Host "4. Test admin dashboard at /admin/dason" -ForegroundColor White
