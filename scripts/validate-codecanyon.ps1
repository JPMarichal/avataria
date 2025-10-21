# Avatar Steward - CodeCanyon Validation Pre-Check (PowerShell)
# Validates the plugin package against CodeCanyon quality standards

param(
    [switch]$Verbose = $false
)

Write-Host "🎯 Avatar Steward - CodeCanyon Validation Pre-Check" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host ""

$PassCount = 0
$FailCount = 0
$WarnCount = 0

# Function to check and report
function Test-Item {
    param(
        [string]$ItemName,
        [scriptblock]$CheckCommand,
        [bool]$IsCritical = $true
    )
    
    Write-Host "Checking $ItemName... " -NoNewline
    
    try {
        $result = & $CheckCommand
        if ($result) {
            Write-Host "✓ PASS" -ForegroundColor Green
            $script:PassCount++
            return $true
        } else {
            if ($IsCritical) {
                Write-Host "✗ FAIL" -ForegroundColor Red
                $script:FailCount++
            } else {
                Write-Host "⚠ WARNING" -ForegroundColor Yellow
                $script:WarnCount++
            }
            return $false
        }
    } catch {
        if ($IsCritical) {
            Write-Host "✗ FAIL" -ForegroundColor Red
            $script:FailCount++
        } else {
            Write-Host "⚠ WARNING" -ForegroundColor Yellow
            $script:WarnCount++
        }
        return $false
    }
}

Write-Host "📋 Section 1: Required Documentation" -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow
Test-Item "README.md exists" { Test-Path "README.md" }
Test-Item "CHANGELOG.md exists" { Test-Path "CHANGELOG.md" }
Test-Item "LICENSE.txt exists" { Test-Path "LICENSE.txt" }
Test-Item "User manual exists" { Test-Path "docs/user-manual.md" }
Test-Item "FAQ exists" { Test-Path "docs/faq.md" }
Test-Item "Support documentation" { Test-Path "docs/support.md" }

Write-Host ""
Write-Host "📋 Section 2: Code Quality Configuration" -ForegroundColor Yellow
Write-Host "---------------------------------------" -ForegroundColor Yellow
Test-Item "phpcs.xml exists" { Test-Path "phpcs.xml" }
Test-Item "PHPUnit config exists" { Test-Path "phpunit.xml.dist" }
Test-Item "ESLint config exists" { Test-Path ".eslintrc.json" } -IsCritical $false

Write-Host ""
Write-Host "📋 Section 3: Assets and Screenshots" -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow
Test-Item "Assets directory exists" { Test-Path "assets" -PathType Container }
Test-Item "Screenshots exist" { 
    (Test-Path "assets/screenshots" -PathType Container) -and 
    ((Get-ChildItem "assets/screenshots" -ErrorAction SilentlyContinue).Count -gt 0)
}
Test-Item "README in screenshots" { Test-Path "assets/screenshots/README.md" } -IsCritical $false

Write-Host ""
Write-Host "📋 Section 4: License Documentation" -ForegroundColor Yellow
Write-Host "----------------------------------" -ForegroundColor Yellow
Test-Item "Licensing docs exist" { 
    (Test-Path "docs/licensing.md") -or (Test-Path "docs/licenses-pro.md")
}
Test-Item "GPL origin documented" { Test-Path "docs/legal/origen-gpl.md" }

Write-Host ""
Write-Host "📋 Section 5: Testing Infrastructure" -ForegroundColor Yellow
Write-Host "-----------------------------------" -ForegroundColor Yellow
Test-Item "Tests directory exists" { Test-Path "tests" -PathType Container }
Test-Item "PHPUnit tests exist" { 
    (Test-Path "tests/phpunit" -PathType Container) -and 
    ((Get-ChildItem "tests/phpunit" -Recurse -Filter "*Test.php" -ErrorAction SilentlyContinue).Count -gt 0)
}

Write-Host ""
Write-Host "📋 Section 6: Demo Environment" -ForegroundColor Yellow
Write-Host "-----------------------------" -ForegroundColor Yellow
Test-Item "Demo docker-compose.yml" { Test-Path "docker-compose.demo.yml" }
Test-Item "Demo documentation" { Test-Path "docs/demo/README.md" }

Write-Host ""
Write-Host "📋 Section 7: Package Structure" -ForegroundColor Yellow
Write-Host "------------------------------" -ForegroundColor Yellow
Test-Item "Main plugin file exists" { Test-Path "avatar-steward.php" }
Test-Item "Source directory exists" { Test-Path "src" -PathType Container }
Test-Item ".distignore exists" { Test-Path ".distignore" }

Write-Host ""
Write-Host "📋 Section 8: WordPress Standards Compliance" -ForegroundColor Yellow
Write-Host "-------------------------------------------" -ForegroundColor Yellow

# Check if composer is available
if (Get-Command composer -ErrorAction SilentlyContinue) {
    Test-Item "Composer is available" { $true } -IsCritical $false
} else {
    Write-Host "⚠ Composer not available - skipping PHPCS check" -ForegroundColor Yellow
    $script:WarnCount++
}

# Check PHPUnit
if ((Get-Command phpunit -ErrorAction SilentlyContinue) -or (Test-Path "vendor/bin/phpunit")) {
    Test-Item "PHPUnit is available" { $true } -IsCritical $false
} else {
    Write-Host "⚠ PHPUnit not available - run 'composer install' first" -ForegroundColor Yellow
    $script:WarnCount++
}

Write-Host ""
Write-Host "📋 Section 9: Plugin Metadata" -ForegroundColor Yellow
Write-Host "----------------------------" -ForegroundColor Yellow

# Check plugin header
if (Test-Path "avatar-steward.php") {
    $pluginContent = Get-Content "avatar-steward.php" -Raw
    Test-Item "Plugin Name header" { $pluginContent -match "Plugin Name:" }
    Test-Item "Version header" { $pluginContent -match "Version:" }
    Test-Item "Text Domain header" { $pluginContent -match "Text Domain:" }
}

Write-Host ""
Write-Host "📋 Section 10: Security Best Practices" -ForegroundColor Yellow
Write-Host "-------------------------------------" -ForegroundColor Yellow

Test-Item "No hardcoded credentials in PHP" { 
    $files = Get-ChildItem -Path "src" -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
    $found = $false
    foreach ($file in $files) {
        $content = Get-Content $file.FullName -Raw
        if ($content -match 'password\s*=\s*[''"].*[''"]' -and $content -notmatch '//.*password') {
            $found = $true
            break
        }
    }
    -not $found
} -IsCritical $false

Test-Item "Nonce verification present" { 
    $files = Get-ChildItem -Path "src" -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
    ($files | Select-String "wp_verify_nonce" -ErrorAction SilentlyContinue).Count -gt 0
} -IsCritical $false

Test-Item "Capability checks present" { 
    $files = Get-ChildItem -Path "src" -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
    ($files | Select-String "current_user_can" -ErrorAction SilentlyContinue).Count -gt 0
} -IsCritical $false

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "📊 Validation Summary" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "Passed: $PassCount" -ForegroundColor Green
Write-Host "Warnings: $WarnCount" -ForegroundColor Yellow
Write-Host "Failed: $FailCount" -ForegroundColor Red
Write-Host ""

if ($FailCount -eq 0) {
    Write-Host "✅ Plugin meets CodeCanyon basic requirements!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📝 Recommended Next Steps:" -ForegroundColor Yellow
    Write-Host "1. Run full linting: composer lint" -ForegroundColor White
    Write-Host "2. Run all tests: composer test" -ForegroundColor White
    Write-Host "3. Test the demo environment: docker compose -f docker-compose.demo.yml up -d" -ForegroundColor White
    Write-Host "4. Generate the package: .\scripts\package-plugin.ps1 -NoDev -Pro" -ForegroundColor White
    Write-Host "5. Manual testing in WordPress Playground" -ForegroundColor White
    Write-Host ""
    exit 0
} else {
    Write-Host "❌ Plugin has critical issues that need to be addressed." -ForegroundColor Red
    Write-Host ""
    Write-Host "Please review the failed checks above and address them before submission." -ForegroundColor Yellow
    Write-Host ""
    exit 1
}
