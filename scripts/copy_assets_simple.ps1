# Simple PowerShell script to copy Dason assets
Write-Host "Starting Dason Assets Copy..." -ForegroundColor Green

# Define paths
$sourcePath = ".\Dason-Laravel_v1.0.0\Admin\public\assets"
$destPath = ".\public\assets"

# Check if source exists
if (-not (Test-Path $sourcePath)) {
    Write-Host "Source path not found: $sourcePath" -ForegroundColor Red
    exit 1
}

# Create destination directories
Write-Host "Creating destination directories..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path "$destPath\css" -Force | Out-Null
New-Item -ItemType Directory -Path "$destPath\js" -Force | Out-Null
New-Item -ItemType Directory -Path "$destPath\images" -Force | Out-Null
New-Item -ItemType Directory -Path "$destPath\fonts" -Force | Out-Null
New-Item -ItemType Directory -Path "$destPath\libs" -Force | Out-Null

# Copy CSS
Write-Host "Copying CSS files..." -ForegroundColor Cyan
if (Test-Path "$sourcePath\css") {
    Copy-Item "$sourcePath\css\*" "$destPath\css\" -Recurse -Force
    Write-Host "CSS files copied successfully" -ForegroundColor Green
}

# Copy JS
Write-Host "Copying JS files..." -ForegroundColor Cyan
if (Test-Path "$sourcePath\js") {
    Copy-Item "$sourcePath\js\*" "$destPath\js\" -Recurse -Force
    Write-Host "JS files copied successfully" -ForegroundColor Green
}

# Copy Images
Write-Host "Copying Images..." -ForegroundColor Cyan
if (Test-Path "$sourcePath\images") {
    Copy-Item "$sourcePath\images\*" "$destPath\images\" -Recurse -Force
    Write-Host "Images copied successfully" -ForegroundColor Green
}

# Copy Fonts
Write-Host "Copying Fonts..." -ForegroundColor Cyan
if (Test-Path "$sourcePath\fonts") {
    Copy-Item "$sourcePath\fonts\*" "$destPath\fonts\" -Recurse -Force
    Write-Host "Fonts copied successfully" -ForegroundColor Green
}

# Copy Libraries
Write-Host "Copying Libraries (this may take a while)..." -ForegroundColor Cyan
if (Test-Path "$sourcePath\libs") {
    Copy-Item "$sourcePath\libs\*" "$destPath\libs\" -Recurse -Force
    Write-Host "Libraries copied successfully" -ForegroundColor Green
}

Write-Host "Dason Assets Copy Completed!" -ForegroundColor Green
Write-Host "Next: Update package.json and run npm install" -ForegroundColor Yellow
