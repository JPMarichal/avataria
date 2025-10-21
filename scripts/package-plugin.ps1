# Avatar Steward - Plugin Package Generator (PowerShell)
# Creates a clean ZIP file for WordPress.org/CodeCanyon submission and testing

param(
    [string]$Version = "",
    [switch]$NoDev = $false,
    [switch]$Pro = $false
)

Write-Host "🎯 Avatar Steward - Plugin Package Generator" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Auto-detect version from plugin file if not provided
if ([string]::IsNullOrEmpty($Version)) {
    $pluginContent = Get-Content "avatar-steward.php" -Raw
    if ($pluginContent -match "Version:\s*([0-9.]+)") {
        $Version = $matches[1]
    } else {
        $Version = "0.1.0"
    }
}

# Configuration
$PluginName = "avatar-steward"
$OutputDir = "dist"
$TempDir = "$OutputDir\$PluginName"
$ZipName = "$PluginName-$Version.zip"

if ($Pro) {
    $ZipName = "$PluginName-pro-$Version.zip"
}

# Clean previous builds
Write-Host "🧹 Cleaning previous builds..." -ForegroundColor Yellow
if (Test-Path $OutputDir) {
    Remove-Item -Recurse -Force $OutputDir
}
if (Test-Path $ZipName) {
    Remove-Item -Force $ZipName
}

# Create output directory structure
New-Item -ItemType Directory -Force -Path $TempDir | Out-Null

Write-Host "📦 Copying plugin files..." -ForegroundColor Yellow

# Read .distignore patterns
$excludePatterns = @()
if (Test-Path ".distignore") {
    $excludePatterns = Get-Content ".distignore" | Where-Object { $_ -notmatch '^\s*#' -and $_ -notmatch '^\s*$' }
}

# Copy files excluding patterns from .distignore
Get-ChildItem -Path . -Recurse | ForEach-Object {
    $relativePath = $_.FullName.Substring((Get-Location).Path.Length + 1)
    $shouldExclude = $false
    
    foreach ($pattern in $excludePatterns) {
        $pattern = $pattern.Trim()
        if ($relativePath -like $pattern -or $relativePath -like "$pattern/*" -or $_.Name -eq $pattern) {
            $shouldExclude = $true
            break
        }
    }
    
    # Always exclude output directory
    if ($relativePath -like "$OutputDir*") {
        $shouldExclude = $true
    }
    
    if (-not $shouldExclude) {
        $targetPath = Join-Path $TempDir $relativePath
        $targetDir = Split-Path $targetPath -Parent
        
        if (-not (Test-Path $targetDir)) {
            New-Item -ItemType Directory -Force -Path $targetDir | Out-Null
        }
        
        if (-not $_.PSIsContainer) {
            Copy-Item $_.FullName -Destination $targetPath -Force
        }
    }
}

# Handle Composer dependencies
if ($NoDev) {
    Write-Host "📦 Installing production dependencies only..." -ForegroundColor Yellow
    Push-Location $TempDir
    if (Test-Path "composer.json") {
        $result = & composer install --no-dev --optimize-autoloader --no-interaction --quiet 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Production dependencies installed" -ForegroundColor Green
        } else {
            Write-Host "⚠️  Warning: composer install failed, but continuing..." -ForegroundColor Yellow
        }
    }
    Pop-Location
} else {
    Write-Host "ℹ️  Including vendor directory as-is (dev dependencies included)" -ForegroundColor Gray
}

# Copy essential user documentation
Write-Host "📄 Preparing user documentation..." -ForegroundColor Yellow
Push-Location $TempDir
New-Item -ItemType Directory -Force -Path "docs" | Out-Null
if (Test-Path "..\..\docs\user-manual.md") { Copy-Item "..\..\docs\user-manual.md" "docs\" -ErrorAction SilentlyContinue }
if (Test-Path "..\..\docs\faq.md") { Copy-Item "..\..\docs\faq.md" "docs\" -ErrorAction SilentlyContinue }
if (Test-Path "..\..\docs\support.md") { Copy-Item "..\..\docs\support.md" "docs\" -ErrorAction SilentlyContinue }
if ($Pro) {
    if (Test-Path "..\..\docs\licensing.md") { Copy-Item "..\..\docs\licensing.md" "docs\" -ErrorAction SilentlyContinue }
    if (Test-Path "..\..\docs\licenses-pro.md") { Copy-Item "..\..\docs\licenses-pro.md" "docs\" -ErrorAction SilentlyContinue }
}

# Clean up any remaining development artifacts
Get-ChildItem -Recurse -Force | Where-Object { $_.Name -in @(".DS_Store", "Thumbs.db") -or $_.Extension -eq ".log" } | Remove-Item -Force -ErrorAction SilentlyContinue

Pop-Location

# Create ZIP file
Write-Host "📦 Creating ZIP package..." -ForegroundColor Yellow
Compress-Archive -Path "$OutputDir\$PluginName" -DestinationPath $ZipName -Force

# Verify package
Write-Host "✅ Verifying package..." -ForegroundColor Green
if (Test-Path $ZipName) {
    $size = (Get-Item $ZipName).Length / 1MB
    Write-Host "📊 Package created: $ZipName ($("{0:N2}" -f $size) MB)" -ForegroundColor Green

    # List contents (first 30 files)
    Write-Host "📋 Package contents (first 30 files):" -ForegroundColor Yellow
    try {
        Add-Type -AssemblyName System.IO.Compression.FileSystem
        $zip = [System.IO.Compression.ZipFile]::OpenRead((Get-Item $ZipName).FullName)
        $fileCount = $zip.Entries.Count
        $zip.Entries | Select-Object -First 30 | ForEach-Object { Write-Host "  $($_.FullName)" -ForegroundColor Gray }
        Write-Host "📁 Total files: $fileCount" -ForegroundColor Cyan
        $zip.Dispose()
    } catch {
        Write-Host "  (Unable to list ZIP contents - package created successfully)" -ForegroundColor Gray
    }
} else {
    Write-Host "❌ Failed to create package" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🎉 Package generation complete!" -ForegroundColor Green
Write-Host "📁 Location: $(Get-Location)\$ZipName" -ForegroundColor Cyan
Write-Host ""

if ($Pro) {
    Write-Host "📝 Next steps for Pro version:" -ForegroundColor Yellow
    Write-Host "1. Test the ZIP in a clean WordPress installation" -ForegroundColor White
    Write-Host "2. Verify licensing system works correctly" -ForegroundColor White
    Write-Host "3. Run CodeCanyon validation: .\scripts\validate-codecanyon.ps1" -ForegroundColor White
    Write-Host "4. Upload to CodeCanyon when ready" -ForegroundColor White
} else {
    Write-Host "📝 Next steps:" -ForegroundColor Yellow
    Write-Host "1. Test the ZIP in WordPress Playground: https://playground.wordpress.net/" -ForegroundColor White
    Write-Host "2. Upload to WordPress.org when ready for submission" -ForegroundColor White
}

# Clean up prompt
Write-Host ""
$cleanup = Read-Host "🧹 Clean up temporary files? (y/N)"
if ($cleanup -eq "y" -or $cleanup -eq "Y") {
    Write-Host "🧹 Cleaning up..." -ForegroundColor Yellow
    Remove-Item -Recurse -Force $OutputDir
    Write-Host "✅ Cleanup complete" -ForegroundColor Green
}

Write-Host ""
Write-Host "🚀 Ready for testing!" -ForegroundColor Green
