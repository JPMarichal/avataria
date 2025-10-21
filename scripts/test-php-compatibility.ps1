# Avatar Steward - PHP Compatibility Analysis
Write-Host "🔍 Avatar Steward - PHP Compatibility Analysis" -ForegroundColor Cyan
Write-Host "==============================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "📦 Testing PHP 7.4+ Compatibility..." -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow
& vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 7.4- src/
$PHP74_EXIT = $LASTEXITCODE
Write-Host ""

Write-Host "📦 Testing PHP 8.0+ Compatibility..." -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow
& vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.0- src/
$PHP80_EXIT = $LASTEXITCODE
Write-Host ""

Write-Host "📦 Testing PHP 8.2+ Compatibility..." -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow
& vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.2- src/
$PHP82_EXIT = $LASTEXITCODE
Write-Host ""

Write-Host "==============================================" -ForegroundColor Cyan
Write-Host "📊 Compatibility Test Results Summary" -ForegroundColor Cyan
Write-Host "==============================================" -ForegroundColor Cyan

if ($PHP74_EXIT -eq 0) {
    Write-Host "✅ PHP 7.4+: PASS - No compatibility issues found" -ForegroundColor Green
} else {
    Write-Host "❌ PHP 7.4+: FAIL - Review errors above" -ForegroundColor Red
}

if ($PHP80_EXIT -eq 0) {
    Write-Host "✅ PHP 8.0+: PASS - No compatibility issues found" -ForegroundColor Green
} else {
    Write-Host "❌ PHP 8.0+: FAIL - Review errors above" -ForegroundColor Red
}

if ($PHP82_EXIT -eq 0) {
    Write-Host "✅ PHP 8.2+: PASS - No compatibility issues found" -ForegroundColor Green
} else {
    Write-Host "❌ PHP 8.2+: FAIL - Review errors above" -ForegroundColor Red
}

Write-Host ""
if ($PHP74_EXIT -eq 0 -and $PHP80_EXIT -eq 0 -and $PHP82_EXIT -eq 0) {
    Write-Host "🎉 All PHP compatibility tests PASSED!" -ForegroundColor Green
    Write-Host "Avatar Steward is compatible with PHP 7.4, 8.0, 8.1, and 8.2+" -ForegroundColor Green
    exit 0
} else {
    Write-Host "⚠️  Some compatibility tests FAILED." -ForegroundColor Yellow
    Write-Host "Please review the errors above and fix compatibility issues." -ForegroundColor Yellow
    exit 1
}
