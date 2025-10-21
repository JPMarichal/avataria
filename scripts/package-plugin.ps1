# Avatar Steward - Plugin Package Generator (PowerShell)
# Creates a clean ZIP file for WordPress.org submission and testing

param(
    [string]$Version = "0.1.0"
)

Write-Host "🎯 Avatar Steward - Plugin Package Generator" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$PluginName = "avatar-steward"
$OutputDir = "dist"
$ZipName = "$PluginName-$Version.zip"

# Clean previous builds
Write-Host "🧹 Cleaning previous builds..." -ForegroundColor Yellow
if (Test-Path $OutputDir) {
    Remove-Item -Recurse -Force $OutputDir
}
if (Test-Path $ZipName) {
    Remove-Item -Force $ZipName
}

# Create output directory
New-Item -ItemType Directory -Force -Path $OutputDir | Out-Null

# Copy plugin files (excluding development files)
Write-Host "📦 Copying plugin files..." -ForegroundColor Yellow

# Core plugin files
Copy-Item -Recurse "avatar-steward.php" $OutputDir
if (Test-Path "src") { Copy-Item -Recurse "src" $OutputDir }
if (Test-Path "assets") { Copy-Item -Recurse "assets" $OutputDir }
if (Test-Path "languages") { Copy-Item -Recurse "languages" $OutputDir }
if (Test-Path "templates") { Copy-Item -Recurse "templates" $OutputDir }

# Documentation (only essential files)
Copy-Item "README.md" $OutputDir
Copy-Item "CHANGELOG.md" $OutputDir
Copy-Item "LICENSE.txt" $OutputDir

# Exclude development files
Write-Host "🚫 Excluding development files..." -ForegroundColor Yellow
Push-Location $OutputDir

# Remove development directories
$devDirs = @("vendor", "node_modules", "tests", ".git", ".github", "docker", "docker_logs", "design", "examples", "scripts", "simple-local-avatars", "wp-config.php")
foreach ($dir in $devDirs) {
    if (Test-Path $dir) {
        Remove-Item -Recurse -Force $dir
    }
}

# Remove development files
$devFiles = @("*.log", ".DS_Store", "Thumbs.db", "*.tmp", "*.bak", "composer.*", "package*.json", "phpunit*.xml*", "phpcs.xml*", "webpack.config.js", "test-generator.php")
foreach ($pattern in $devFiles) {
    Get-ChildItem -Recurse -File -Filter $pattern | Remove-Item -Force
}

# Remove docs directory (keep only essential docs)
if (Test-Path "docs") {
    Remove-Item -Recurse -Force "docs"
}

# Remove all .md files except README.md and CHANGELOG.md
Get-ChildItem -Recurse -File -Filter "*.md" | Where-Object { $_.Name -notin @("README.md", "CHANGELOG.md") } | Remove-Item -Force

Pop-Location

# Create ZIP file
Write-Host "📦 Creating ZIP package..." -ForegroundColor Yellow
Compress-Archive -Path "$OutputDir\*" -DestinationPath $ZipName -Force

# Verify package
Write-Host "✅ Verifying package..." -ForegroundColor Green
if (Test-Path $ZipName) {
    $size = (Get-Item $ZipName).Length / 1MB
    Write-Host "📊 Package created: $ZipName ($("{0:N2}" -f $size) MB)" -ForegroundColor Green

    # List contents (first 20 files)
    Write-Host "📋 Package contents (first 20 files):" -ForegroundColor Yellow
    $zipContents = Get-ChildItem -Path $ZipName | Select-Object -ExpandProperty FullName
    if ($zipContents) {
        try {
            $shell = New-Object -ComObject Shell.Application
            $zip = $shell.NameSpace($zipContents)
            $zip.Items() | Select-Object -First 20 | ForEach-Object { Write-Host "  $($_.Name)" }
        } catch {
            Write-Host "  (Unable to list ZIP contents - package created successfully)" -ForegroundColor Gray
        }
    }
} else {
    Write-Host "❌ Failed to create package" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🎉 Package generation complete!" -ForegroundColor Green
Write-Host "📁 Location: $(Get-Location)\$ZipName" -ForegroundColor Cyan
Write-Host ""
Write-Host "📝 Next steps:" -ForegroundColor Yellow
Write-Host "1. Test the ZIP in WordPress Playground: https://playground.wordpress.net/" -ForegroundColor White
Write-Host "2. Upload to WordPress.org when ready for submission" -ForegroundColor White
Write-Host "3. Keep this ZIP for CodeCanyon submission (Pro version)" -ForegroundColor White

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
