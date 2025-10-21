#!/bin/bash

# Avatar Steward - CodeCanyon Validation Pre-Check
# Validates the plugin package against CodeCanyon quality standards

echo "🎯 Avatar Steward - CodeCanyon Validation Pre-Check"
echo "===================================================="
echo ""

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

PASS_COUNT=0
FAIL_COUNT=0
WARN_COUNT=0

# Function to check and report
check_item() {
    local item_name="$1"
    local check_command="$2"
    local is_critical="${3:-true}"
    
    echo -n "Checking $item_name... "
    
    if eval "$check_command" &>/dev/null; then
        echo -e "${GREEN}✓ PASS${NC}"
        ((PASS_COUNT++))
        return 0
    else
        if [ "$is_critical" = "true" ]; then
            echo -e "${RED}✗ FAIL${NC}"
            ((FAIL_COUNT++))
        else
            echo -e "${YELLOW}⚠ WARNING${NC}"
            ((WARN_COUNT++))
        fi
        return 1
    fi
}

echo "📋 Section 1: Required Documentation"
echo "-----------------------------------"
check_item "README.md exists" "test -f README.md"
check_item "CHANGELOG.md exists" "test -f CHANGELOG.md"
check_item "LICENSE.txt exists" "test -f LICENSE.txt"
check_item "User manual exists" "test -f docs/user-manual.md"
check_item "FAQ exists" "test -f docs/faq.md"
check_item "Support documentation" "test -f docs/support.md"

echo ""
echo "📋 Section 2: Code Quality Configuration"
echo "---------------------------------------"
check_item "phpcs.xml exists" "test -f phpcs.xml"
check_item "PHPUnit config exists" "test -f phpunit.xml.dist"
check_item "ESLint config exists" "test -f .eslintrc.json" "false"

echo ""
echo "📋 Section 3: Assets and Screenshots"
echo "-----------------------------------"
check_item "Assets directory exists" "test -d assets"
check_item "Screenshots exist" "test -d assets/screenshots && [ -n \"\$(ls -A assets/screenshots 2>/dev/null)\" ]"
check_item "README in screenshots" "test -f assets/screenshots/README.md" "false"

echo ""
echo "📋 Section 4: License Documentation"
echo "----------------------------------"
check_item "Licensing docs exist" "test -f docs/licensing.md || test -f docs/licenses-pro.md"
check_item "GPL origin documented" "test -f docs/legal/origen-gpl.md"

echo ""
echo "📋 Section 5: Testing Infrastructure"
echo "-----------------------------------"
check_item "Tests directory exists" "test -d tests"
check_item "PHPUnit tests exist" "test -d tests/phpunit && [ -n \"\$(find tests/phpunit -name '*Test.php' 2>/dev/null)\" ]"

echo ""
echo "📋 Section 6: Demo Environment"
echo "-----------------------------"
check_item "Demo docker-compose.yml" "test -f docker-compose.demo.yml"
check_item "Demo documentation" "test -f docs/demo/README.md"

echo ""
echo "📋 Section 7: Package Structure"
echo "------------------------------"
check_item "Main plugin file exists" "test -f avatar-steward.php"
check_item "Source directory exists" "test -d src"
check_item ".distignore exists" "test -f .distignore"

echo ""
echo "📋 Section 8: WordPress Standards Compliance"
echo "-------------------------------------------"

# Check if composer is available
if command -v composer &> /dev/null; then
    if composer lint --dry-run &> /dev/null 2>&1; then
        check_item "PHPCS can run" "composer lint --help" "false"
    else
        check_item "PHPCS can run" "vendor/bin/phpcs --version" "false"
    fi
else
    echo -e "${YELLOW}⚠ Composer not available - skipping PHPCS check${NC}"
    ((WARN_COUNT++))
fi

# Check PHPUnit
if command -v phpunit &> /dev/null || [ -f vendor/bin/phpunit ]; then
    check_item "PHPUnit is available" "test -f vendor/bin/phpunit || command -v phpunit" "false"
else
    echo -e "${YELLOW}⚠ PHPUnit not available - run 'composer install' first${NC}"
    ((WARN_COUNT++))
fi

echo ""
echo "📋 Section 9: Plugin Metadata"
echo "----------------------------"

# Check plugin header
if grep -q "Plugin Name:" avatar-steward.php; then
    check_item "Plugin Name header" "grep -q 'Plugin Name:' avatar-steward.php"
fi

if grep -q "Version:" avatar-steward.php; then
    check_item "Version header" "grep -q 'Version:' avatar-steward.php"
fi

if grep -q "Text Domain:" avatar-steward.php; then
    check_item "Text Domain header" "grep -q 'Text Domain:' avatar-steward.php"
fi

echo ""
echo "📋 Section 10: Security Best Practices"
echo "-------------------------------------"
check_item "No hardcoded credentials in PHP" "! grep -r 'password.*=.*['\"].*['\"]' src/ --include='*.php' | grep -v '// ' | grep -v '/\*'" "false"
check_item "Nonce verification present" "grep -r 'wp_verify_nonce' src/ --include='*.php' -q" "false"
check_item "Capability checks present" "grep -r 'current_user_can' src/ --include='*.php' -q" "false"

echo ""
echo "================================================================"
echo "📊 Validation Summary"
echo "================================================================"
echo -e "${GREEN}Passed: $PASS_COUNT${NC}"
echo -e "${YELLOW}Warnings: $WARN_COUNT${NC}"
echo -e "${RED}Failed: $FAIL_COUNT${NC}"
echo ""

if [ $FAIL_COUNT -eq 0 ]; then
    echo -e "${GREEN}✅ Plugin meets CodeCanyon basic requirements!${NC}"
    echo ""
    echo "📝 Recommended Next Steps:"
    echo "1. Run full linting: composer lint"
    echo "2. Run all tests: composer test"
    echo "3. Test the demo environment: docker compose -f docker-compose.demo.yml up -d"
    echo "4. Generate the package: ./scripts/package-plugin.sh --no-dev --pro"
    echo "5. Manual testing in WordPress Playground"
    echo ""
    exit 0
else
    echo -e "${RED}❌ Plugin has critical issues that need to be addressed.${NC}"
    echo ""
    echo "Please review the failed checks above and address them before submission."
    echo ""
    exit 1
fi
